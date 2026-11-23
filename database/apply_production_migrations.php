<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') { http_response_code(403); exit('CLI only'); }
require_once __DIR__ . '/../includes/bootstrap.php';

$pdo = db();
$dbName = (string)$pdo->query('SELECT DATABASE()')->fetchColumn();
echo "Migration inspection on database: {$dbName}\n";
if (APP_ENV !== 'local' || !in_array(strtolower(DB_HOST), ['127.0.0.1','localhost','::1'], true)) {
    fwrite(STDERR, "Refusing nonlocal migration.\n"); exit(2);
}
$apply = in_array('--apply-local', array_slice($argv,1), true);
if (!$apply) {
    echo "READ-ONLY MODE. To apply migrations manually after a verified backup, use --apply-local.\n";
}

if ($dbName !== 'smartcitytiruppur') {
    die("Expected database smartcitytiruppur, got: {$dbName}\n");
}

// 1. Make complaint_escalations.escalation_level_id nullable
$col = $pdo->query("SHOW COLUMNS FROM complaint_escalations LIKE 'escalation_level_id'")->fetch(PDO::FETCH_ASSOC);
if ($col && strtoupper((string)$col['Null']) !== 'YES') {
    if (!$apply) { echo "WOULD MODIFY complaint_escalations.escalation_level_id.\n"; } else {
    echo "Modifying complaint_escalations.escalation_level_id to NULL...\n";
    $pdo->exec("ALTER TABLE complaint_escalations MODIFY escalation_level_id INT UNSIGNED NULL");
    echo "Successfully modified escalation_level_id to NULL.\n";
    }
} else {
    echo "complaint_escalations.escalation_level_id is already nullable.\n";
}

// 2. Verify officer_stage_approvals constraint allows stages 1..6
$ddl = $pdo->query("SHOW CREATE TABLE officer_stage_approvals")->fetch(PDO::FETCH_ASSOC);
if (strpos($ddl['Create Table'], 'chk_stage_authorized_range') !== false) {
    echo "officer_stage_approvals has chk_stage_authorized_range constraint.\n";
} else {
    if (!$apply) { echo "WOULD ADD chk_stage_authorized_range.\n"; }
    else {
        echo "Adding chk_stage_authorized_range constraint...\n";
        $pdo->exec("ALTER TABLE officer_stage_approvals ADD CONSTRAINT chk_stage_authorized_range CHECK (stage_no BETWEEN 1 AND 6)");
    }
}

// 3. Verify triggers
$trigs = $pdo->query("SELECT TRIGGER_NAME FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA = '{$dbName}' AND EVENT_OBJECT_TABLE = 'complaints' AND TRIGGER_NAME = 'trg_complaints_auto_assign'")->fetchAll(PDO::FETCH_COLUMN);
if ($trigs) {
    if (!$apply) { echo "WOULD DROP unsafe trigger trg_complaints_auto_assign.\n"; }
    else {
        echo "Dropping unsafe trigger trg_complaints_auto_assign...\n";
        $pdo->exec("DROP TRIGGER IF EXISTS trg_complaints_auto_assign");
    }
} else {
    echo "No unsafe triggers found on complaints table.\n";
}

echo $apply ? "Migration commands completed; verify schema independently.\n" : "Read-only inspection complete. No schema changes.\n";
