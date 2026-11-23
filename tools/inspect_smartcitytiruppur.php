<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

$pdo = db();
$dbName = (string)$pdo->query('SELECT DATABASE()')->fetchColumn();
echo "Active DB: {$dbName}\n";

// Table existence
$tables = ['pdf_escalation_policies', 'pdf_escalation_policy_stages', 'officer_stage_approvals', 'complaints', 'officers', 'department_allocations'];
foreach ($tables as $t) {
    $exists = (int)$pdo->query("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = '{$dbName}' AND TABLE_NAME = '{$t}'")->fetchColumn();
    $cnt = $exists ? (int)$pdo->query("SELECT COUNT(*) FROM `{$t}`")->fetchColumn() : -1;
    echo "Table `{$t}`: exists={$exists}, rows={$cnt}\n";
}

// PDF policies stats
if ((int)$pdo->query("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = '{$dbName}' AND TABLE_NAME = 'pdf_escalation_policies'")->fetchColumn() === 1) {
    $approved = (int)$pdo->query("SELECT COUNT(*) FROM pdf_escalation_policies WHERE is_approved = 1")->fetchColumn();
    echo "Approved PDF policies: {$approved}\n";
}

// Officer approvals
if ((int)$pdo->query("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = '{$dbName}' AND TABLE_NAME = 'officer_stage_approvals'")->fetchColumn() === 1) {
    $approvals = (int)$pdo->query("SELECT COUNT(*) FROM officer_stage_approvals WHERE revoked_at IS NULL")->fetchColumn();
    echo "Active Officer Stage Approvals: {$approvals}\n";
    // Check constraint on officer_stage_approvals
    $ddl = $pdo->query("SHOW CREATE TABLE officer_stage_approvals")->fetch(PDO::FETCH_ASSOC);
    echo "officer_stage_approvals DDL:\n" . ($ddl['Create Table'] ?? '') . "\n\n";
}

// Triggers on complaints
$trigs = $pdo->query("SELECT TRIGGER_NAME FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA = '{$dbName}' AND EVENT_OBJECT_TABLE = 'complaints'")->fetchAll(PDO::FETCH_COLUMN);
echo "Triggers on complaints: " . implode(', ', $trigs) . "\n";

// Unassigned complaints count
$unassigned = (int)$pdo->query("SELECT COUNT(*) FROM complaints WHERE is_deleted = 0 AND assigned_officer_id IS NULL AND status IN ('Registered', 'Pending')")->fetchColumn();
echo "Registered/Pending unassigned complaints: {$unassigned}\n";

// Inspect complaint TCMC-2026-576866
$c = $pdo->query("SELECT c.id, c.reference_no, c.department_id, c.category_id, c.ward_id, c.zone_id, c.status, c.assigned_officer_id, c.due_at, c.created_at, cat.category_name, d.department_name
                  FROM complaints c
                  LEFT JOIN complaint_categories cat ON cat.id = c.category_id
                  LEFT JOIN departments d ON d.id = c.department_id
                  WHERE c.reference_no = 'TCMC-2026-576866'")->fetch(PDO::FETCH_ASSOC);
echo "Complaint TCMC-2026-576866:\n";
print_r($c);

// Inspect Candidate Officer #16
$off16 = $pdo->query("SELECT o.id, o.officer_name, o.designation, o.mobile_no, o.is_active
                      FROM officers o WHERE o.id = 16")->fetch(PDO::FETCH_ASSOC);
echo "Officer #16:\n";
print_r($off16);

// Inspect mappings for Officer #16
if ($c) {
    $catId = (int)$c['category_id'];
    $deptId = (int)$c['department_id'];
    $wardId = (int)$c['ward_id'];

    $deptMap = $pdo->query("SELECT COUNT(*) FROM officer_department_map WHERE officer_id = 16 AND department_id = {$deptId}")->fetchColumn();
    $catMap = $pdo->query("SELECT COUNT(*) FROM officer_category_map WHERE officer_id = 16 AND category_id = {$catId}")->fetchColumn();
    $wardMap = $pdo->query("SELECT COUNT(*) FROM officer_ward_map WHERE officer_id = 16 AND ward_id = {$wardId}")->fetchColumn();

    echo "Officer #16 mappings for Complaint #{$c['id']}: Dept={$deptMap}, Cat={$catMap}, Ward={$wardMap}\n";

    // Check PDF policy for category
    $policy = $pdo->query("SELECT * FROM pdf_escalation_policies WHERE category_id = {$catId}")->fetch(PDO::FETCH_ASSOC);
    echo "PDF Policy for Cat #{$catId}:\n";
    print_r($policy);

    $stages = $pdo->query("SELECT * FROM pdf_escalation_policy_stages WHERE category_id = {$catId} ORDER BY stage_no")->fetchAll(PDO::FETCH_ASSOC);
    echo "PDF Stages for Cat #{$catId}:\n";
    print_r($stages);
}

$ceDDL = $pdo->query("SHOW CREATE TABLE complaint_escalations")->fetch(PDO::FETCH_ASSOC);
echo "\ncomplaint_escalations DDL:\n" . ($ceDDL['Create Table'] ?? '') . "\n";
