<?php
declare(strict_types=1);
/**
 * Local XAMPP only: prepare the three PDF policy/approval tables and import
 * PDF category definitions as UNAPPROVED drafts. Never assigns employees.
 *
 * Read-only preflight:
 *   php tools/install_pdf_schema_local.php --expect-db=smartcitytiruppur
 * Apply only after a VERIFIED backup exported outside the web root:
 *   php tools/install_pdf_schema_local.php --expect-db=smartcitytiruppur --apply --backup-file=C:\Backups\smartcitytiruppur.sql
 *
 * This does NOT deploy production workers or activate escalation.
 */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit('CLI only'); }
$expected = null; $apply = false; $backupFile = null;
foreach (array_slice($argv, 1) as $arg) {
    if (preg_match('/^--expect-db=([A-Za-z0-9_]+)$/D', $arg, $m)) {
        $expected = $m[1]; continue;
    }
    if ($arg === '--apply') { $apply = true; continue; }
    if (str_starts_with($arg, '--backup-file=')) {
        $backupFile = substr($arg, strlen('--backup-file=')); continue;
    }
    fwrite(STDERR, "Unknown argument: {$arg}\n"); exit(2);
}
if ($expected !== 'smartcitytiruppur') {
    fwrite(STDERR, "This installer is only for local main clone smartcitytiruppur. "
        . "Use the existing test migrations for smartcity_escalation_test.\n");
    exit(2);
}
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/pdf-escalation-policy.php';
if (APP_ENV !== 'local'
    || !in_array(strtolower((string)DB_HOST), ['127.0.0.1','localhost','::1'], true)) {
    fwrite(STDERR, "Refusing: requires a local-only XAMPP configuration.\n"); exit(2);
}
$pdo = db();
$actual = (string)$pdo->query('SELECT DATABASE()')->fetchColumn();
if ($actual !== $expected) {
    fwrite(STDERR, "DATABASE MISMATCH: connected {$actual}, expected {$expected}. No writes.\n");
    exit(2);
}
// The PDF, not the website mapping screen, is the canonical stage definition.
$json = dirname(__DIR__) . '/database/pdf_escalation_policy.json';
if (!is_file($json)) { fwrite(STDERR, "Missing PDF JSON.\n"); exit(2); }
$source = json_decode((string)file_get_contents($json), true, 512, JSON_THROW_ON_ERROR);
$fingerprint = '03a496809403a2de6a597a844a36ffc979de47657541342924628c8d33d70c4b';
if (($source['source_sha256'] ?? '') !== $fingerprint) {
    fwrite(STDERR, "PDF source fingerprint mismatch. No writes.\n"); exit(2);
}
$policies = pdf_policy_source_rows($source);
$needed = ['departments','complaint_categories','users','officers',
    'officer_department_map','officer_category_map','officer_ward_map'];
$target = ['pdf_escalation_policies','pdf_escalation_policy_stages','officer_stage_approvals'];
$existing = $pdo->query(
    'SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE()'
)->fetchAll(PDO::FETCH_COLUMN);
$present = array_fill_keys($existing, true);
foreach ($needed as $table) {
    if (!isset($present[$table])) {
        fwrite(STDERR, "Missing dependency {$table}. No writes.\n"); exit(3);
    }
}
$departments = [];
$categoryNames = [];
foreach ($pdo->query('SELECT id,department_name FROM departments')->fetchAll(PDO::FETCH_ASSOC) as $d) {
    $key = pdf_policy_key((string)$d['department_name']);
    if (isset($departments[$key])) { fwrite(STDERR, "Ambiguous department {$key}\n"); exit(3); }
    $departments[$key] = (int)$d['id'];
}
foreach ($pdo->query('SELECT id,department_id,category_name FROM complaint_categories')->fetchAll(PDO::FETCH_ASSOC) as $c) {
    $key = (int)$c['department_id'] . '|' . pdf_policy_key((string)$c['category_name']);
    if (isset($categoryNames[$key])) { fwrite(STDERR, "Ambiguous category {$key}\n"); exit(3); }
    $categoryNames[$key] = (int)$c['id'];
}
$mapped = []; $unmatched = [];
foreach ($policies as $policy) {
    $dep = $departments[pdf_policy_key((string)$policy['department'])] ?? 0;
    $key = $dep . '|' . pdf_policy_key((string)$policy['category']);
    $cat = $categoryNames[$key] ?? 0;
    if (!$dep || !$cat) {
        $unmatched[] = 'PDF row ' . $policy['s_no'] . ': '
            . $policy['department'] . ' / ' . $policy['category'];
    } else {
        $mapped[] = ['id'=>$cat, 'policy'=>$policy];
    }
}
echo "LOCAL PDF SCHEMA PRECHECK\n";
echo "Database={$actual}; PDF categories=" . count($policies)
    . "; mapped=" . count($mapped) . "; unmatched=" . count($unmatched) . "\n";
foreach ($target as $table) {
    echo "{$table}: " . (isset($present[$table]) ? 'EXISTS' : 'MISSING') . "\n";
}
foreach ($unmatched as $item) echo "UNMATCHED {$item}\n";
if ($unmatched) {
    fwrite(STDERR, "STOP: reconcile category labels before installing policy tables.\n"); exit(3);
}
$installed = array_values(array_filter($target, static fn(string $t): bool => isset($present[$t])));
if ($installed) {
    echo "Existing target schema detected. This installer refuses to modify or overwrite partial/existing tables.\n";
    echo "Review current schema manually; the previous installer or a separate test migration may already have run.\n";
    exit(count($installed) === count($target) ? 0 : 3);
}
if (!$apply) {
    echo "DRY RUN ONLY. The three tables can be created; 301 PDF definitions/1,409 stages "
       . "will be inserted as DRAFT, with zero officer approvals.\n";
    echo "Before applying: export and verify a database backup OUTSIDE htdocs. "
       . "Review the generated plan with your DB administrator.\n";
    exit(0);
}
$backup = $backupFile !== null ? realpath($backupFile) : false;
if (!$backup || !is_file($backup) || !is_readable($backup)
    || !preg_match('/\.sql(?:\.gz)?$/i', $backup)
    || (int)filesize($backup) < 1024) {
    fwrite(STDERR, "Refusing --apply: supply --backup-file=PATH to a verified, nonempty .sql/.sql.gz backup.\n");
    exit(2);
}
$webRoot = realpath(dirname(__DIR__, 3));
if ($webRoot !== false && str_starts_with(strtolower($backup),
        strtolower($webRoot . DIRECTORY_SEPARATOR))) {
    fwrite(STDERR, "Backup must be stored OUTSIDE the web-accessible htdocs directory.\n");
    exit(2);
}
echo "Backup file exists: {$backup} (" . filesize($backup) . " bytes). "
    . "Existence does not prove it can be restored; operator must verify backup integrity.\n";
$schemaFiles = [
    dirname(__DIR__).'/database/pdf_escalation_policy_schema.sql',
    dirname(__DIR__).'/database/officer_stage_approvals.sql',
];
$ddl = [];
foreach ($schemaFiles as $file) {
    $sql = (string)file_get_contents($file);
    $sql = preg_replace('/^\s*--[^\r\n]*(?:\r?\n|$)/m', '', $sql) ?? $sql;
    foreach (preg_split('/;\s*(?:\r?\n|$)/', $sql) ?: [] as $statement) {
        $statement = trim($statement);
        if ($statement === '') continue;
        if (!preg_match('/^CREATE TABLE IF NOT EXISTS (pdf_escalation_policies|pdf_escalation_policy_stages|officer_stage_approvals)\s*\(/i', $statement, $m)) {
            fwrite(STDERR, "Unexpected DDL in schema files. No changes.\n"); exit(3);
        }
        $table = strtolower($m[1]);
        if (isset($ddl[$table])) { fwrite(STDERR, "Duplicate DDL for {$table}\n"); exit(3); }
        $ddl[$table] = $statement;
    }
}
if (count($ddl) !== 3) {
    fwrite(STDERR, "Expected exactly three CREATE TABLE statements; no changes.\n"); exit(3);
}
$locked = (int)$pdo->query("SELECT GET_LOCK('smartcity_pdf_local_schema_setup', 0)")->fetchColumn() === 1;
if (!$locked) { fwrite(STDERR, "Another schema installer is running.\n"); exit(3); }
try {
    foreach ($target as $table) {
        $pdo->exec($ddl[$table]);
        echo "Created table {$table}\n";
    }
    // MariaDB DDL implicitly commits; inserts are protected by a separate
    // transaction. A DDL failure may leave some empty tables for manual review.
    $header = $pdo->prepare(
        'INSERT INTO pdf_escalation_policies
         (category_id,source_row,total_days,stage_count,pdf_sha256,is_approved)
         VALUES (?,?,?,?,?,0)'
    );
    $stage = $pdo->prepare(
        'INSERT INTO pdf_escalation_policy_stages
         (category_id,stage_no,role_label,period_days) VALUES (?,?,?,?)'
    );
    $pdo->beginTransaction();
    foreach ($mapped as $item) {
        $cat = $item['id']; $policy = $item['policy'];
        $header->execute([$cat,$policy['s_no'],$policy['total_days'],
            count($policy['stages']),$fingerprint]);
        foreach ($policy['stages'] as $step) {
            $stage->execute([$cat,$step['stage'],$step['role'],$step['days']]);
        }
    }
    $pdo->commit();
    echo "SUCCESS: inserted 301 draft policy headers and 1409 stage definitions.\n";
    echo "OFFICER APPROVALS CREATED: ZERO. Policies remain UNAPPROVED.\n";
    echo "No existing complaints/officers/assignments edited. No scheduler enabled.\n";
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    fwrite(STDERR, "SETUP FAILED: ".$e->getMessage()."\n");
    fwrite(STDERR, "DDL may have left partial empty tables; check schema before retrying.\n");
    exit(3);
} finally {
    $pdo->query("SELECT RELEASE_LOCK('smartcity_pdf_local_schema_setup')");
}
