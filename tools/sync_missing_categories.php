<?php
declare(strict_types=1);

/**
 * One-time, guarded, additive category-master repair for a LOCAL XAMPP copy.
 *
 * READ ONLY:
 *   php tools/sync_missing_categories.php --expect-db=smartcitytiruppur
 *
 * EXPLICIT ADD-ONLY (LOCAL database, creates JSON backup first):
 *   php tools/sync_missing_categories.php --expect-db=smartcitytiruppur --apply-local --confirm=ADD_MISSING_CATEGORY_ROWS
 *
 * Never updates, deletes, renumbers or reactivates existing category rows.
 * Does not change complaint/officer tables, SLA values of existing categories,
 * or automation configuration.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only.\n");
}
$options = ['expect-db' => null, 'apply-local' => false, 'confirm' => null];
foreach (array_slice($argv, 1) as $arg) {
    if ($arg === '--apply-local') {
        $options['apply-local'] = true;
    } elseif (str_starts_with($arg, '--expect-db=')) {
        $options['expect-db'] = substr($arg, strlen('--expect-db='));
    } elseif (str_starts_with($arg, '--confirm=')) {
        $options['confirm'] = substr($arg, strlen('--confirm='));
    } else {
        fwrite(STDERR, "Unknown option: {$arg}\n");
        exit(2);
    }
}
if (!$options['expect-db'] ||
    !preg_match('/^[A-Za-z0-9_]+$/D', (string)$options['expect-db'])) {
    fwrite(STDERR, "Required: --expect-db=<exact_database_name>\n");
    exit(2);
}
if ($options['apply-local'] && (
    $options['expect-db'] !== 'smartcitytiruppur' ||
    $options['confirm'] !== 'ADD_MISSING_CATEGORY_ROWS'
)) {
    fwrite(STDERR, "SAFETY STOP: local write requires --expect-db=smartcitytiruppur --apply-local --confirm=ADD_MISSING_CATEGORY_ROWS\n");
    exit(2);
}
require_once dirname(__DIR__) . '/includes/bootstrap.php';
if ($options['apply-local'] && (
    APP_ENV !== 'local' ||
    !in_array(strtolower(DB_HOST), ['127.0.0.1','localhost','::1'], true)
)) {
    fwrite(STDERR, "SAFETY STOP: database writes require local XAMPP configuration.\n");
    exit(2);
}
$pdo = db();
$actual = (string)$pdo->query('SELECT DATABASE()')->fetchColumn();
if ($actual !== $options['expect-db']) {
    fwrite(STDERR, "SAFETY STOP: connected to {$actual} instead of {$options['expect-db']}. No changes.\n");
    exit(2);
}
$sourceFile = dirname(__DIR__) . '/database/category_catalog_reference.json';
try {
    $source = json_decode((string)file_get_contents($sourceFile), true, 512, JSON_THROW_ON_ERROR);
} catch (Throwable $error) {
    fwrite(STDERR, "Cannot read source category catalog: {$error->getMessage()}\n");
    exit(2);
}
$catalog = $source['records'] ?? [];
if (!is_array($catalog) || count($catalog) !== 302) {
    fwrite(STDERR, "SAFETY STOP: expected 302 entries in versioned category reference.\n");
    exit(2);
}
$norm = static function (string $value): string {
    $value = trim(preg_replace('/\s+/u', ' ', $value) ?? $value);
    return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
};
$deptRows = $pdo->query(
    'SELECT id,department_name,is_active FROM departments ORDER BY id'
)->fetchAll(PDO::FETCH_ASSOC);
$categoryRows = $pdo->query(
    'SELECT id,department_id,category_name,sla_days,is_active
     FROM complaint_categories ORDER BY id'
)->fetchAll(PDO::FETCH_ASSOC);
$depts = [];
$deptNames = [];
foreach ($deptRows as $row) {
    $depts[$norm((string)$row['department_name'])] = $row;
    $deptNames[(int)$row['id']] = (string)$row['department_name'];
}
$existing = [];
foreach ($categoryRows as $row) {
    $dept = $deptNames[(int)$row['department_id']] ?? '';
    $existing[$norm($dept) . "\0" . $norm((string)$row['category_name'])] = $row;
}
$missing = [];
$inactive = [];
$changedSlas = [];
$unknownDepts = [];
$summary = [];
foreach ($catalog as $record) {
    if (!is_array($record) || !isset($record['department'],$record['category'],$record['source_sla_days'])) {
        fwrite(STDERR, "SAFETY STOP: malformed catalog entry.\n");
        exit(2);
    }
    $department = trim((string)$record['department']);
    $category = trim((string)$record['category']);
    $sla = (int)$record['source_sla_days'];
    if ($department === '' || $category === '' || $category === '-' || $sla < 1 || $sla > 3650) {
        fwrite(STDERR, "SAFETY STOP: invalid source row.\n");
        exit(2);
    }
    $summary[$department] ??= ['source'=>0,'present'=>0,'missing'=>0,'inactive'=>0,'sla_differences'=>0];
    $summary[$department]['source']++;
    $d = $depts[$norm($department)] ?? null;
    if (!$d || (int)$d['is_active'] !== 1) {
        $unknownDepts[] = $department;
        continue;
    }
    $key = $norm($department) . "\0" . $norm($category);
    $old = $existing[$key] ?? null;
    if (!$old) {
        $missing[] = ['department_id'=>(int)$d['id'],'department'=>$department,
                      'category'=>$category,'sla_days'=>$sla];
        $summary[$department]['missing']++;
        continue;
    }
    $summary[$department]['present']++;
    if ((int)$old['is_active'] !== 1) {
        $inactive[] = ['department'=>$department,'category'=>$category,'id'=>(int)$old['id']];
        $summary[$department]['inactive']++;
    }
    if ((int)$old['sla_days'] !== $sla) {
        $changedSlas[] = ['department'=>$department,'category'=>$category,
                           'existing'=>(int)$old['sla_days'],'historical'=>$sla];
        $summary[$department]['sla_differences']++;
    }
}
echo "Category master sync | DB: {$actual}\n";
echo "Reference records: ".count($catalog)." | Existing DB categories: ".count($categoryRows)."\n";
foreach ($summary as $name => $counts) {
    printf("%s: reference=%d present=%d missing=%d inactive=%d historical_SLA_difference=%d\n",
        $name,$counts['source'],$counts['present'],$counts['missing'],
        $counts['inactive'],$counts['sla_differences']);
}
printf("TOTAL: missing=%d inactive=%d SLA differences=%d unavailable departments=%d\n",
    count($missing), count($inactive), count($changedSlas), count(array_unique($unknownDepts)));
foreach (array_slice($missing, 0, 10) as $item) {
    echo "MISSING: {$item['department']} / {$item['category']}\n";
}
if (!$options['apply-local']) {
    echo "READ ONLY: No changes made. To insert missing rows, use explicit --apply-local with confirmation.\n";
    exit(0);
}
if ($unknownDepts) {
    fwrite(STDERR, "SAFETY STOP: missing/inactive departments: ".implode(', ',array_unique($unknownDepts)).". No changes.\n");
    exit(3);
}
if (!$missing) {
    echo "Nothing to insert. No existing categories were changed.\n";
    exit(0);
}
// Mandatory backup OUTSIDE the XAMPP web document root; no backup = no writes.
$backupDir = rtrim(sys_get_temp_dir(), '\\/') . DIRECTORY_SEPARATOR . 'smartcity_category_backups';
if (!is_dir($backupDir) && !mkdir($backupDir,0700,true) && !is_dir($backupDir)) {
    fwrite(STDERR, "SAFETY STOP: cannot create backup directory. No changes.\n");
    exit(3);
}
$backupPath = $backupDir . DIRECTORY_SEPARATOR
    . 'categories-before-add-only-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.json';
$backup = [
    'database'=>$actual,
    'timestamp'=>date('c'),
    'purpose'=>'Complete pre-migration snapshot of department and category master rows',
    'departments'=>$deptRows,
    'complaint_categories'=>$categoryRows,
    'missing_to_add'=>$missing,
    'inactive_preserved'=>$inactive,
    'sla_differences_preserved'=>$changedSlas
];
$json = json_encode($backup,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
if (!is_string($json) || file_put_contents($backupPath,$json,LOCK_EX)!==strlen($json)) {
    fwrite(STDERR,"SAFETY STOP: could not write verified category backup. No DB changes.\n");
    exit(3);
}
echo "Category-master backup written: {$backupPath}\n";
echo "Historical source SLA values apply ONLY to newly inserted rows; existing SLA remains untouched.\n";
$lockName = 'smartcity_category_master_add_only';
$locked = (int)$pdo->query("SELECT GET_LOCK('smartcity_category_master_add_only', 5)")->fetchColumn() === 1;
if (!$locked) {
    fwrite(STDERR, "SAFETY STOP: could not obtain maintenance lock. No changes.\n");
    exit(3);
}
$added = 0;
try {
    $pdo->beginTransaction();
    $insert = $pdo->prepare(
        "INSERT INTO complaint_categories (department_id,category_name,sla_days,is_active)
         SELECT ?,?,?,1
         WHERE NOT EXISTS (
           SELECT 1 FROM complaint_categories
           WHERE department_id=? AND category_name=?
         )"
    );
    foreach ($missing as $record) {
        $id = (int)$record['department_id'];
        $insert->execute([$id,$record['category'],(int)$record['sla_days'],$id,$record['category']]);
        $added += $insert->rowCount();
    }
    $pdo->commit();
    echo "COMPLETED: {$added} missing categories inserted. Existing category rows not updated.\n";
} catch (Throwable $error) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    fwrite(STDERR,"FAILED: transaction rolled back: ".$error->getMessage()."\n");
    exit(3);
} finally {
    $pdo->query("SELECT RELEASE_LOCK('smartcity_category_master_add_only')");
}
echo "Refresh New Complaint; category choices remain filtered by selected department.\n";
echo "Check inactive categories and unresolved SLA differences separately; automatic escalation remains disabled.\n";
