<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

$pdo = db();
$dbName = (string)$pdo->query('SELECT DATABASE()')->fetchColumn();
echo "Executing migrations on database: {$dbName}\n";

if ($dbName !== 'smartcitytiruppur') {
    die("Expected database smartcitytiruppur, got: {$dbName}\n");
}

// 1. Make complaint_escalations.escalation_level_id nullable
$col = $pdo->query("SHOW COLUMNS FROM complaint_escalations LIKE 'escalation_level_id'")->fetch(PDO::FETCH_ASSOC);
if ($col && strtoupper((string)$col['Null']) !== 'YES') {
    echo "Modifying complaint_escalations.escalation_level_id to NULL...\n";
    $pdo->exec("ALTER TABLE complaint_escalations MODIFY escalation_level_id INT UNSIGNED NULL");
    echo "Successfully modified escalation_level_id to NULL.\n";
} else {
    echo "complaint_escalations.escalation_level_id is already nullable.\n";
}

// 2. Verify officer_stage_approvals constraint allows stages 1..6
$ddl = $pdo->query("SHOW CREATE TABLE officer_stage_approvals")->fetch(PDO::FETCH_ASSOC);
if (strpos($ddl['Create Table'], 'chk_stage_authorized_range') !== false) {
    echo "officer_stage_approvals has chk_stage_authorized_range constraint.\n";
} else {
    echo "Adding chk_stage_authorized_range constraint...\n";
    $pdo->exec("ALTER TABLE officer_stage_approvals ADD CONSTRAINT chk_stage_authorized_range CHECK (stage_no BETWEEN 1 AND 6)");
}

// 3. Verify triggers
$trigs = $pdo->query("SELECT TRIGGER_NAME FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA = '{$dbName}' AND EVENT_OBJECT_TABLE = 'complaints' AND TRIGGER_NAME = 'trg_complaints_auto_assign'")->fetchAll(PDO::FETCH_COLUMN);
if ($trigs) {
    echo "Dropping unsafe trigger trg_complaints_auto_assign...\n";
    $pdo->exec("DROP TRIGGER IF EXISTS trg_complaints_auto_assign");
} else {
    echo "No unsafe triggers found on complaints table.\n";
}

echo "Migration complete and verified.\n";
