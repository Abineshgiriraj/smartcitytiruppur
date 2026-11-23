<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/pdf-escalation-policy.php';
require_once __DIR__ . '/../includes/officer-authority.php';

$pdo = db();
$dbName = (string)$pdo->query('SELECT DATABASE()')->fetchColumn();
echo "Testing on database: {$dbName}\n";

if ($dbName !== 'smartcitytiruppur') {
    die("Expected smartcitytiruppur, got {$dbName}\n");
}

// 1. Verify Complaint 576866 initial state
$c = $pdo->query("SELECT id, reference_no, status, assigned_officer_id, due_at, category_id, ward_id, zone_id FROM complaints WHERE id = 576866")->fetch(PDO::FETCH_ASSOC);
echo "1. Initial Complaint State:\n";
print_r($c);

// 2. Approve Policy 294 (Simulation reference)
echo "\n2. Approving Category 294 PDF Policy with SIMULATION reference...\n";
$pdo->prepare("UPDATE pdf_escalation_policies SET is_approved = 1, approval_reference = 'SIMULATION-LOCAL-TEST-CAT294-REF', approved_by = 1, approved_at = NOW() WHERE category_id = 294")->execute();

// Check dry-run output via CLI
echo "Running dry-run before officer approval:\n";
passthru('"D:\\XAMPP\\php\\php.exe" retry-pending-allocations.php --dry-run --expect-db=smartcitytiruppur --complaint-id=576866');

// 3. Authorize Officer #16 for Category 294 Stage 1
echo "\n3. Authorizing Officer #16 for Category 294 Stage 1 with SIMULATION reference...\n";
$pdo->prepare("INSERT INTO officer_stage_approvals (category_id, stage_no, officer_id, approval_reference, approved_by, approved_at)
               VALUES (294, 1, 16, 'SIMULATION-LOCAL-TEST-OFF16-REF', 1, NOW())
               ON DUPLICATE KEY UPDATE revoked_at = NULL, approval_reference = VALUES(approval_reference)")->execute();

// Check dry-run output after officer approval
echo "Running dry-run after officer approval:\n";
passthru('"D:\\XAMPP\\php\\php.exe" retry-pending-allocations.php --dry-run --expect-db=smartcitytiruppur --complaint-id=576866');

// 4. Execute allocation for Complaint 576866
echo "\n4. Executing allocation for Complaint 576866...\n";
passthru('"D:\\XAMPP\\php\\php.exe" retry-pending-allocations.php --execute --expect-db=smartcitytiruppur --complaint-id=576866');

// 5. Verify Complaint 576866 fresh state in database
$fresh = $pdo->query("SELECT id, reference_no, status, assigned_officer_id, due_at, created_at FROM complaints WHERE id = 576866")->fetch(PDO::FETCH_ASSOC);
echo "\n5. Fresh Complaint State after Allocation:\n";
print_r($fresh);

// Check assignments
$assignments = $pdo->query("SELECT * FROM complaint_assignments WHERE complaint_id = 576866")->fetchAll(PDO::FETCH_ASSOC);
echo "Assignment records:\n";
print_r($assignments);

// Check status history
$history = $pdo->query("SELECT * FROM complaint_status_history WHERE complaint_id = 576866 ORDER BY id DESC LIMIT 2")->fetchAll(PDO::FETCH_ASSOC);
echo "Status history:\n";
print_r($history);

// 6. Test escalation evaluation on Complaint 576866 (within SLA)
echo "\n6. Testing PDF Escalation Evaluation on Complaint 576866:\n";
passthru('"D:\\XAMPP\\php\\php.exe" pdf-escalate-complaints.php --dry-run --expect-db=smartcitytiruppur --complaint-id=576866');

echo "\nVerification of Complaint 576866 flow completed successfully.\n";
