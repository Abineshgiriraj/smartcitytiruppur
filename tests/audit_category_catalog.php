<?php
declare(strict_types=1);
/**
 * Category catalog comparison + missing-only TEST DB restore.
 *
 * Safe default:
 *   php tests/audit_category_catalog.php --expect-db=smartcitytiruppur
 *   php tests/audit_category_catalog.php --expect-db=smartcitytiruppur --department="Engineering -Town plan"
 *
 * Test-only restore after verifying a backup:
 *   php tests/audit_category_catalog.php --expect-db=smartcity_escalation_test --apply-test
 *
 * --apply-test is STRICTLY BLOCKED for any DB other than smartcity_escalation_test.
 * Existing categories (including inactive categories) are never overwritten.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only.\n");
}
$options = ['expect-db' => '', 'department' => ''];
$apply = false;
foreach (array_slice($argv, 1) as $arg) {
    if ($arg === '--apply-test') {
        $apply = true;
        continue;
    }
    if (str_starts_with($arg, '--expect-db=')) {
        $options['expect-db'] = substr($arg, strlen('--expect-db='));
        continue;
    }
    if (str_starts_with($arg, '--department=')) {
        $options['department'] = substr($arg, strlen('--department='));
        continue;
    }
    fwrite(STDERR, "Unknown argument: {$arg}\n");
    exit(2);
}
$expected = $options['expect-db'];
if (!preg_match('/^[A-Za-z0-9_]+$/D', $expected)) {
    fwrite(STDERR, "Required: --expect-db=<exact_database_name>\n");
    exit(2);
}
if ($apply && $expected !== 'smartcity_escalation_test') {
    fwrite(STDERR, "SAFETY BLOCK: --apply-test only allowed on smartcity_escalation_test.\n");
    exit(2);
}
require_once __DIR__ . '/../includes/bootstrap.php';
$pdo = db();
$actual = (string)$pdo->query('SELECT DATABASE()')->fetchColumn();
if ($actual !== $expected) {
    fwrite(STDERR, "Database mismatch: connected to {$actual}, expected {$expected}. No writes.\n");
    exit(2);
}
$file = __DIR__ . '/../database/category_catalog_reference.json';
$baseline = json_decode((string)file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
$records = $baseline['records'] ?? [];
if (count($records) !== 302) {
    fwrite(STDERR, "Catalog reference record count invalid; not proceeding.\n");
    exit(2);
}

$departments = $pdo->query(
    'SELECT id, department_name, is_active FROM departments ORDER BY id'
)->fetchAll(PDO::FETCH_ASSOC);
$departmentsByName = [];
$departmentsById = [];
foreach ($departments as $row) {
    $departmentsByName[$row['department_name']] = $row;
    $departmentsById[(int)$row['id']] = $row['department_name'];
}
$existing = $pdo->query(
    'SELECT id, department_id, category_name, sla_days, is_active
     FROM complaint_categories ORDER BY department_id,id'
)->fetchAll(PDO::FETCH_ASSOC);

$key = static function (string $dept, string $category): string {
    return mb_strtolower(trim($dept), 'UTF-8') . "\0" .
           mb_strtolower(trim($category), 'UTF-8');
};
$byDepartmentAndName = [];
$otherDepartmentNames = [];
foreach ($existing as $row) {
    $name = (string)$row['category_name'];
    $department = $departmentsById[(int)$row['department_id']] ?? '(unknown)';
    $byDepartmentAndName[$key($department,$name)] = $row;
    $otherDepartmentNames[mb_strtolower(trim($name), 'UTF-8')][] = $department;
}

$missing = [];
$inactive = [];
$wrongSla = [];
$unknownDept = [];
$perDepartment = [];
foreach ($records as $record) {
    $name = (string)$record['category'];
    $department = (string)$record['department'];
    $filter = $options['department'];
    if ($filter !== '' && strcasecmp($department,$filter) !== 0) continue;
    if (!isset($perDepartment[$department])) {
        $perDepartment[$department] = ['reference'=>0,'present'=>0,'missing'=>0,'inactive'=>0,'different_sla'=>0];
    }
    $perDepartment[$department]['reference']++;
    if (!isset($departmentsByName[$department])) {
        $unknownDept[] = $record;
        continue;
    }
    $existingRecord = $byDepartmentAndName[$key($department,$name)] ?? null;
    if ($existingRecord === null) {
        $missing[] = $record;
        $perDepartment[$department]['missing']++;
        continue;
    }
    $perDepartment[$department]['present']++;
    if ((int)$existingRecord['is_active'] !== 1) {
        $perDepartment[$department]['inactive']++;
        $inactive[] = ['ref'=>$record,'db'=>$existingRecord];
    }
    if ((int)$existingRecord['sla_days'] !== (int)$record['source_sla_days']) {
        $perDepartment[$department]['different_sla']++;
        $wrongSla[] = ['ref'=>$record,'db'=>$existingRecord];
    }
}
echo "=== Complaint-category catalog audit ===\n";
echo "Connected database: {$actual}\n";
echo "Baseline: database/data_import.sql ({$baseline['source']})\n";
echo "Current category rows: " . count($existing) . "\n";
foreach ($perDepartment as $name => $counts) {
    printf("%s: expected=%d found=%d missing=%d inactive=%d source_sla_diff=%d\n",
        $name,$counts['reference'],$counts['present'],$counts['missing'],
        $counts['inactive'],$counts['different_sla']);
}
printf("Totals in scope: MISSING=%d INACTIVE=%d SOURCE_SLA_DIFFERENT=%d UNKNOWN_DEPARTMENT=%d\n",
    count($missing),count($inactive),count($wrongSla),count($unknownDept));
foreach ($missing as $record) {
    $other = array_unique($otherDepartmentNames[mb_strtolower(trim($record['category']), 'UTF-8')] ?? []);
    printf("MISSING | %s | %s | historical_sla=%d | also_found_in=%s\n",
        $record['department'],$record['category'],$record['source_sla_days'],
        $other ? implode(',',$other) : 'none');
}
foreach (array_slice($inactive,0,30) as $record) {
    printf("INACTIVE (NOT MODIFIED) | %s | %s | id=%d\n",
        $record['ref']['department'],$record['ref']['category'],$record['db']['id']);
}
foreach (array_slice($wrongSla,0,30) as $record) {
    printf("SLA DIFF (NOT MODIFIED) | %s | %s | DB=%d baseline=%d\n",
        $record['ref']['department'],$record['ref']['category'],
        $record['db']['sla_days'],$record['ref']['source_sla_days']);
}
if (!$apply) {
    echo "READ-ONLY AUDIT. No database mutations executed.\n";
    exit(0);
}
if ($unknownDept) {
    fwrite(STDERR, "Cannot restore: reference department(s) missing from target DB.\n");
    exit(3);
}
// A CLI apply is deliberately restricted to the isolated test DB. Production
// data restoration needs a reviewed and backed-up migration with change control.
$stmt = $pdo->prepare(
    "INSERT INTO complaint_categories (department_id,category_name,sla_days,is_active)
     SELECT ?,?,?,1
     WHERE NOT EXISTS (
       SELECT 1 FROM complaint_categories
       WHERE department_id=? AND category_name=?
     )"
);
$inserted = 0;
try {
    $pdo->beginTransaction();
    foreach ($missing as $record) {
        $dept = $departmentsByName[$record['department']];
        if ((int)$dept['is_active'] !== 1) {
            throw new RuntimeException("Inactive department: ".$record['department']);
        }
        $departmentId = (int)$dept['id'];
        $stmt->execute([
            $departmentId,$record['category'],(int)$record['source_sla_days'],
            $departmentId,$record['category']
        ]);
        $inserted += $stmt->rowCount();
    }
    $pdo->commit();
    echo "SANDBOX RESTORE COMPLETE: inserted {$inserted} missing categories; existing records untouched.\n";
    echo "Historical baseline SLA used only for newly inserted test categories. NOT approved ICTS policy.\n";
} catch (Throwable $error) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    fwrite(STDERR, "Sandbox restore rolled back: ".$error->getMessage()."\n");
    exit(3);
}
