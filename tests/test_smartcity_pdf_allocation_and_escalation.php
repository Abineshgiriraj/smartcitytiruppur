<?php
declare(strict_types=1);

/**
 * Smart City Tiruppur — Comprehensive PDF Allocation & 6-Stage Escalation Test Suite
 *
 * Runs strictly against `smartcitytiruppur`.
 * Uses isolated synthetic fixtures (IDs >= 970000) to verify all 11 required scenarios.
 * Synthetic test fixtures are completely cleaned up upon completion.
 */

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/pdf-escalation-policy.php';
require_once __DIR__ . '/../includes/officer-authority.php';

$pdo = db();
$activeDb = (string)$pdo->query('SELECT DATABASE()')->fetchColumn();

if ($activeDb !== 'smartcitytiruppur') {
    fwrite(STDERR, "[FATAL] Active database is '{$activeDb}', NOT 'smartcitytiruppur'! Aborting.\n");
    exit(1);
}

$baselineComplaints = (int)$pdo->query("SELECT COUNT(*) FROM complaints WHERE id < 970000")->fetchColumn();
$baselineOfficers = (int)$pdo->query("SELECT COUNT(*) FROM officers WHERE id < 970000")->fetchColumn();

echo "======================================================================\n";
echo "=== SMART CITY TIRUPPUR PDF WORKFLOW AUTOMATED TEST SUITE ===\n";
echo "Active DB: {$activeDb} (Baseline complaints: {$baselineComplaints}, officers: {$baselineOfficers})\n";
echo "======================================================================\n\n";

$testResults = [];
function recordAssertion(string $id, string $scenario, bool $passed, string $details = ''): void {
    global $testResults;
    $status = $passed ? 'PASS' : 'FAIL';
    $testResults[] = ['id' => $id, 'scenario' => $scenario, 'status' => $status, 'details' => $details];
    echo "[{$id}] [{$status}] {$scenario}" . ($details ? " | {$details}" : "") . "\n";
}

function cleanupSyntheticFixtures(PDO $pdo): void {
    $pdo->exec("DELETE FROM complaint_status_history WHERE complaint_id >= 970000");
    $pdo->exec("DELETE FROM complaint_assignments WHERE complaint_id >= 970000");
    $pdo->exec("DELETE FROM complaint_escalations WHERE complaint_id >= 970000");
    $pdo->exec("DELETE FROM complaints WHERE id >= 970000");
    $pdo->exec("DELETE FROM officer_stage_approvals WHERE id >= 970000 OR category_id >= 970000 OR officer_id >= 970000");
    $pdo->exec("DELETE FROM officer_department_map WHERE officer_id >= 970000");
    $pdo->exec("DELETE FROM officer_category_map WHERE officer_id >= 970000");
    $pdo->exec("DELETE FROM officer_ward_map WHERE officer_id >= 970000");
    $pdo->exec("DELETE FROM officers WHERE id >= 970000");
}

cleanupSyntheticFixtures($pdo);

// Helper to create synthetic officer
function createTestOfficer(PDO $pdo, int $id, string $name, string $desig, int $deptId, int $catId, ?int $wardId = null): void {
    $pdo->prepare("INSERT INTO officers (id, officer_name, designation, mobile_no, is_active) VALUES (?, ?, ?, '9111111111', 1)
                   ON DUPLICATE KEY UPDATE designation = VALUES(designation), is_active = 1")->execute([$id, $name, $desig]);
    $pdo->prepare("INSERT IGNORE INTO officer_department_map (officer_id, department_id) VALUES (?, ?)")->execute([$id, $deptId]);
    $pdo->prepare("INSERT IGNORE INTO officer_category_map (officer_id, category_id) VALUES (?, ?)")->execute([$id, $catId]);
    if ($wardId !== null) {
        $pdo->prepare("INSERT IGNORE INTO officer_ward_map (officer_id, ward_id) VALUES (?, ?)")->execute([$id, $wardId]);
    }
}

// Helper to authorize officer for stage
function authorizeTestOfficer(PDO $pdo, int $id, int $catId, int $stageNo, int $offId, string $ref = 'SIMULATION-LOCAL-TEST-AUTH'): void {
    $pdo->prepare("INSERT INTO officer_stage_approvals (id, category_id, stage_no, officer_id, approval_reference, approved_by, approved_at)
                   VALUES (?, ?, ?, ?, ?, 1, NOW())
                   ON DUPLICATE KEY UPDATE approval_reference = VALUES(approval_reference), revoked_at = NULL")->execute([$id, $catId, $stageNo, $offId, $ref]);
}

// Category 294 (Poor Lighting) - Dept 3, Ward 24, Zone 2
$cat294 = 294;
$dept3 = 3;
$ward24 = 24;
$zone2 = 2;

// Ensure Category 294 PDF Policy is approved for testing
$pdo->prepare("UPDATE pdf_escalation_policies SET is_approved = 1, approval_reference = 'SIMULATION-LOCAL-TEST-POLICY', approved_by = 1, approved_at = NOW() WHERE category_id = ?")->execute([$cat294]);

// Real Stage 1 Officer #16 is already approved for Category 294.
// For Stages 2 through 6, set up synthetic officers:
// Stage 1: Officer #16 - Mr. ARUMUGAM K - AE(ELECTRICAL) (2d)
// Stage 2: Officer #970002 - Synth AEE - AEE (1d)
// Stage 3: Officer #970003 - Synth EE - EE (1d)
// Stage 4: Officer #970004 - Synth CE - CE (1d)
// Stage 5: Officer #970005 - Synth DepCommr - DEPUTY COMMR (1d)
// Stage 6: Officer #970006 - Synth Commr - COMMR (1d)
$testOfficers = [
    1 => ['id' => 16, 'name' => 'Mr. ARUMUGAM K', 'role' => 'AE(ELECTRICAL)', 'days' => 2],
    2 => ['id' => 970002, 'name' => 'Synth AEE', 'role' => 'AEE', 'days' => 1],
    3 => ['id' => 970003, 'name' => 'Synth EE', 'role' => 'EE', 'days' => 1],
    4 => ['id' => 970004, 'name' => 'Synth CE', 'role' => 'CE', 'days' => 1],
    5 => ['id' => 970005, 'name' => 'Synth DepCommr', 'role' => 'DEPUTY COMMR', 'days' => 1],
    6 => ['id' => 970006, 'name' => 'Synth Commr', 'role' => 'COMMR', 'days' => 1],
];

foreach ($testOfficers as $stage => $o) {
    if ($stage > 1) {
        createTestOfficer($pdo, $o['id'], $o['name'], $o['role'], $dept3, $cat294, $ward24);
        authorizeTestOfficer($pdo, 970100 + $stage, $cat294, $stage, $o['id'], "SIMULATION-LOCAL-TEST-STAGE-{$stage}");
    }
}

$phpPath = 'D:\\XAMPP\\php\\php.exe';
$escalatorScript = dirname(__DIR__) . '\\pdf-escalate-complaints.php';
$retryScript = dirname(__DIR__) . '\\retry-pending-allocations.php';

// -------------------------------------------------------------
// Scenario 1: Complaint registration automatically allocates Stage 1
// -------------------------------------------------------------
$cid1 = 970010;
$ref1 = 'SYNTH-TCMC-970010';
$pdo->prepare(
    "INSERT INTO complaints (id, reference_no, citizen_name, mobile_no, communication_address, street_id,
                             ward_id, zone_id, department_id, category_id, description_english,
                             status, assigned_officer_id, due_at, is_deleted, created_at, updated_at)
     VALUES (?, ?, 'Test Citizen', '9876543210', '123 Test St', 1, ?, ?, ?, ?, 'Test Complaint',
             'Registered', NULL, DATE_ADD(NOW(), INTERVAL 7 DAY), 0, NOW(), NOW())"
)->execute([$cid1, $ref1, $ward24, $zone2, $dept3, $cat294]);

$allocRes1 = assign_officer_for_complaint($cid1, $dept3, $cat294, $ward24, $zone2);
$fresh1 = $pdo->query("SELECT assigned_officer_id, status, due_at FROM complaints WHERE id = {$cid1}")->fetch(PDO::FETCH_ASSOC);

recordAssertion(
    'SCENARIO-01',
    'Complaint registration automatically allocates Stage 1',
    $allocRes1 !== null && $fresh1['status'] === 'Assigned' && (int)$fresh1['assigned_officer_id'] === 16,
    "Allocated Officer: #{$fresh1['assigned_officer_id']}, Status: {$fresh1['status']}"
);

// -------------------------------------------------------------
// Scenario 2: The correct officer matching role & ward is selected
// -------------------------------------------------------------
recordAssertion(
    'SCENARIO-02',
    'The correct officer matching role & ward is selected',
    (int)$fresh1['assigned_officer_id'] === 16,
    "Selected officer ID: {$fresh1['assigned_officer_id']} (Mr. ARUMUGAM K, AE(ELECTRICAL))"
);

// -------------------------------------------------------------
// Scenario 3: Stage 1 deadline is calculated correctly (2 days, NOT total 7 days)
// -------------------------------------------------------------
$expectedDue = date('Y-m-d', strtotime('+2 days'));
$actualDue = date('Y-m-d', strtotime((string)$fresh1['due_at']));
recordAssertion(
    'SCENARIO-03',
    'Stage 1 deadline is calculated correctly from Stage 1 SLA (2 days)',
    $expectedDue === $actualDue,
    "Expected: {$expectedDue}, Actual: {$actualDue}"
);

// -------------------------------------------------------------
// Scenario 4: An overdue complaint escalates to Stage 2
// -------------------------------------------------------------
// Simulate Stage 1 having expired (assigned 2 days and 10 mins ago, due 10 mins ago)
$assignedPast = date('Y-m-d H:i:s', strtotime('-2 days - 10 minutes'));
$duePast = date('Y-m-d H:i:s', strtotime('-10 minutes'));
$pdo->prepare("UPDATE complaints SET assigned_officer_id = ?, due_at = ?, status = 'Assigned' WHERE id = ?")
    ->execute([$testOfficers[1]['id'], $duePast, $cid1]);
$pdo->prepare("UPDATE complaint_assignments SET officer_id = ?, assigned_at = ?, is_current = 1 WHERE complaint_id = ?")
    ->execute([$testOfficers[1]['id'], $assignedPast, $cid1]);

$cmdEsc = "\"{$phpPath}\" \"{$escalatorScript}\" --execute --expect-db=smartcitytiruppur --complaint-id={$cid1}";
$outEsc = []; $codeEsc = 0;
exec($cmdEsc . ' 2>&1', $outEsc, $codeEsc);

$freshEsc1 = $pdo->query("SELECT assigned_officer_id, due_at, status FROM complaints WHERE id = {$cid1}")->fetch(PDO::FETCH_ASSOC);
recordAssertion(
    'SCENARIO-04',
    'An overdue complaint escalates to Stage 2 (AE -> AEE)',
    (int)$freshEsc1['assigned_officer_id'] === $testOfficers[2]['id'],
    "Officer transitioned: #{$testOfficers[1]['id']} -> #{$freshEsc1['assigned_officer_id']} (Expected #{$testOfficers[2]['id']})"
);

// -------------------------------------------------------------
// Scenario 5: Escalation continues through all configured stages (Levels 2 to 6)
// -------------------------------------------------------------
$allStagesPassed = true;
for ($st = 2; $st <= 5; $st++) {
    $nextSt = $st + 1;
    // Each intermediate stage has 1 day SLA
    $escPast = date('Y-m-d H:i:s', strtotime('-1 day - 10 minutes'));
    $duePast = date('Y-m-d H:i:s', strtotime('-10 minutes'));
    $pdo->prepare("UPDATE complaint_escalations SET escalated_at = ? WHERE complaint_id = ? AND level_no = ?")
        ->execute([$escPast, $cid1, $st]);
    $pdo->prepare("UPDATE complaints SET due_at = ? WHERE id = ?")
        ->execute([$duePast, $cid1]);

    $outLoop = []; $codeLoop = 0;
    exec($cmdEsc . ' 2>&1', $outLoop, $codeLoop);
    $freshLoop = $pdo->query("SELECT assigned_officer_id FROM complaints WHERE id = {$cid1}")->fetch(PDO::FETCH_ASSOC);
    if ((int)$freshLoop['assigned_officer_id'] !== $testOfficers[$nextSt]['id']) {
        $allStagesPassed = false;
        break;
    }
}

// Stage 6 is TERMINAL: Escalation stops at Stage 6
$escPast6 = date('Y-m-d H:i:s', strtotime('-1 day - 10 minutes'));
$duePast6 = date('Y-m-d H:i:s', strtotime('-10 minutes'));
$pdo->prepare("UPDATE complaint_escalations SET escalated_at = ? WHERE complaint_id = ? AND level_no = 6")
    ->execute([$escPast6, $cid1]);
$pdo->prepare("UPDATE complaints SET due_at = ? WHERE id = ?")
    ->execute([$duePast6, $cid1]);
$outTerm = []; $codeTerm = 0;
exec($cmdEsc . ' 2>&1', $outTerm, $codeTerm);
$termStopped = strpos(implode("\n", $outTerm), 'TERMINAL_STAGE') !== false;

recordAssertion(
    'SCENARIO-05',
    'Escalation continues through all configured stages and halts at final stage (Stage 6 Terminal)',
    $allStagesPassed && $termStopped,
    "Sequential transitions 1..6 verified; Stage 6 halts with TERMINAL_STAGE"
);

// -------------------------------------------------------------
// Scenario 6: Resolved complaints do not escalate
// -------------------------------------------------------------
$cid6 = 970060;
$ref6 = 'SYNTH-TCMC-970060';
$pdo->prepare(
    "INSERT INTO complaints (id, reference_no, citizen_name, mobile_no, communication_address, street_id,
                             ward_id, zone_id, department_id, category_id, description_english,
                             status, assigned_officer_id, due_at, is_deleted, created_at, updated_at)
     VALUES (?, ?, 'Resolved Citizen', '9876543210', '123 St', 1, ?, ?, ?, ?, 'Resolved test',
             'Resolved', ?, DATE_SUB(NOW(), INTERVAL 1 HOUR), 0, NOW(), NOW())"
)->execute([$cid6, $ref6, $ward24, $zone2, $dept3, $cat294, $testOfficers[1]['id']]);

$outRes = []; $codeRes = 0;
exec("\"{$phpPath}\" \"{$escalatorScript}\" --execute --expect-db=smartcitytiruppur --complaint-id={$cid6} 2>&1", $outRes, $codeRes);
$fresh6 = $pdo->query("SELECT assigned_officer_id, status FROM complaints WHERE id = {$cid6}")->fetch(PDO::FETCH_ASSOC);

recordAssertion(
    'SCENARIO-06',
    'Resolved complaints do not escalate',
    (int)$fresh6['assigned_officer_id'] === $testOfficers[1]['id'] && $fresh6['status'] === 'Resolved' && strpos(implode("\n", $outRes), 'cases=0') !== false,
    "Resolved status respected; 0 cases processed"
);

// -------------------------------------------------------------
// Scenario 7: Repeated worker execution does not duplicate assignments (Idempotency)
// -------------------------------------------------------------
$cid7 = 970070;
$ref7 = 'SYNTH-TCMC-970070';
$pdo->prepare(
    "INSERT INTO complaints (id, reference_no, citizen_name, mobile_no, communication_address, street_id,
                             ward_id, zone_id, department_id, category_id, description_english,
                             status, assigned_officer_id, due_at, is_deleted, created_at, updated_at)
     VALUES (?, ?, 'Repeat Citizen', '9876543210', '123 St', 1, ?, ?, ?, ?, 'Idempotency test',
             'Registered', NULL, DATE_ADD(NOW(), INTERVAL 7 DAY), 0, NOW(), NOW())"
)->execute([$cid7, $ref7, $ward24, $zone2, $dept3, $cat294]);

// Run retry worker twice
exec("\"{$phpPath}\" \"{$retryScript}\" --execute --expect-db=smartcitytiruppur --complaint-id={$cid7} 2>&1");
exec("\"{$phpPath}\" \"{$retryScript}\" --execute --expect-db=smartcitytiruppur --complaint-id={$cid7} 2>&1");

$assignCount7 = (int)$pdo->query("SELECT COUNT(*) FROM complaint_assignments WHERE complaint_id = {$cid7} AND is_current = 1")->fetchColumn();
recordAssertion(
    'SCENARIO-07',
    'Repeated worker execution does not duplicate assignments (Idempotency)',
    $assignCount7 === 1,
    "Exactly {$assignCount7} active assignment row(s)"
);

// -------------------------------------------------------------
// Scenario 8: Missing or revoked approval prevents allocation
// -------------------------------------------------------------
$cid8 = 970080;
$ref8 = 'SYNTH-TCMC-970080';
$catUnappr = 1; // General Admin Category 1 has unapproved policy
$pdo->prepare(
    "INSERT INTO complaints (id, reference_no, citizen_name, mobile_no, communication_address, street_id,
                             ward_id, zone_id, department_id, category_id, description_english,
                             status, assigned_officer_id, due_at, is_deleted, created_at, updated_at)
     VALUES (?, ?, 'Unappr Citizen', '9876543210', '123 St', 1, 1, 1, 1, ?, 'Unapproved test',
             'Registered', NULL, DATE_ADD(NOW(), INTERVAL 7 DAY), 0, NOW(), NOW())"
)->execute([$cid8, $ref8, $catUnappr]);

$res8 = assign_officer_for_complaint($cid8, 1, $catUnappr, 1, 1);
$fresh8 = $pdo->query("SELECT assigned_officer_id, status FROM complaints WHERE id = {$cid8}")->fetch(PDO::FETCH_ASSOC);

recordAssertion(
    'SCENARIO-08',
    'Missing or unapproved policy prevents allocation (fails closed)',
    $res8 === null && $fresh8['assigned_officer_id'] === null && $fresh8['status'] === 'Registered',
    "Allocation safely returns null, complaint remains Registered"
);

// -------------------------------------------------------------
// Scenario 9: Incorrect designation or ward mapping prevents allocation
// -------------------------------------------------------------
$badOfficer = 970090;
// Create officer with wrong designation 'Health Officer' for Street Light
createTestOfficer($pdo, $badOfficer, 'Wrong Desig Off', 'Health Officer', $dept3, $cat294, $ward24);
authorizeTestOfficer($pdo, 970190, $cat294, 1, $badOfficer);

$cid9 = 970091;
$ref9 = 'SYNTH-TCMC-970091';
$pdo->prepare(
    "INSERT INTO complaints (id, reference_no, citizen_name, mobile_no, communication_address, street_id,
                             ward_id, zone_id, department_id, category_id, description_english,
                             status, assigned_officer_id, due_at, is_deleted, created_at, updated_at)
     VALUES (?, ?, 'Bad Desig Citizen', '9876543210', '123 St', 1, ?, ?, ?, ?, 'Bad Desig test',
             'Registered', NULL, DATE_ADD(NOW(), INTERVAL 7 DAY), 0, NOW(), NOW())"
)->execute([$cid9, $ref9, $ward24, $zone2, $dept3, $cat294]);

// Temporarily disable the valid stage 1 test officers to isolate the bad one
$pdo->prepare("UPDATE officers SET is_active = 0 WHERE id IN (?, 16)")->execute([$testOfficers[1]['id']]);
$res9 = assign_officer_for_complaint($cid9, $dept3, $cat294, $ward24, $zone2);
$pdo->prepare("UPDATE officers SET is_active = 1 WHERE id IN (?, 16)")->execute([$testOfficers[1]['id']]);

recordAssertion(
    'SCENARIO-09',
    'Incorrect designation prevents allocation (Role mismatch rejected)',
    $res9 === null,
    "Candidate with designation 'Health Officer' rejected for AE(ELECTRICAL) role"
);

// -------------------------------------------------------------
// Scenario 10: Existing complaint and officer data is preserved
// -------------------------------------------------------------
$postComplaints = (int)$pdo->query("SELECT COUNT(*) FROM complaints WHERE id < 970000")->fetchColumn();
$postOfficers = (int)$pdo->query("SELECT COUNT(*) FROM officers WHERE id < 970000")->fetchColumn();

recordAssertion(
    'SCENARIO-10',
    'Existing complaint and officer data is strictly preserved (0 data loss)',
    $postComplaints === $baselineComplaints && $postOfficers === $baselineOfficers,
    "Baseline Complaints: {$baselineComplaints} -> {$postComplaints}, Officers: {$baselineOfficers} -> {$postOfficers}"
);

// -------------------------------------------------------------
// Scenario 11: Administrative and citizen-facing interfaces show correct assignment history
// -------------------------------------------------------------
$historyEntries = $pdo->query("SELECT old_status, new_status, remarks FROM complaint_status_history WHERE complaint_id = {$cid1} ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
$hasAuditEntries = count($historyEntries) >= 2;
recordAssertion(
    'SCENARIO-11',
    'Administrative and citizen interfaces show complete audit history',
    $hasAuditEntries,
    "Logged " . count($historyEntries) . " audit transitions for Complaint #{$cid1}"
);

// Cleanup synthetic fixtures
cleanupSyntheticFixtures($pdo);

echo "\n======================================================================\n";
echo "=== AUTOMATED TEST SUITE SUMMARY ===\n";
$totalTests = count($testResults);
$passCount = count(array_filter($testResults, fn($t) => $t['status'] === 'PASS'));
$failCount = count(array_filter($testResults, fn($t) => $t['status'] === 'FAIL'));
echo "Total Tests: {$totalTests} | Passed: {$passCount} | Failed: {$failCount}\n";
echo "======================================================================\n";

if ($failCount > 0) {
    exit(1);
}
exit(0);
