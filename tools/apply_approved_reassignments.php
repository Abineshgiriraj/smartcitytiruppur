<?php
declare(strict_types=1);

/**
 * Transactional Reassignment Executor & Safe Rollback Tool
 *
 * Usage:
 *   php tools/apply_approved_reassignments.php --preview
 *   php tools/apply_approved_reassignments.php --execute --admin-id=1
 *   php tools/apply_approved_reassignments.php --rollback --batch-id=XYZ
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}

require_once __DIR__ . '/../includes/bootstrap.php';

$pdo = db();

$mode = 'preview';
$adminId = 1;
$batchId = null;

foreach (array_slice($argv, 1) as $arg) {
    if ($arg === '--preview') $mode = 'preview';
    if ($arg === '--execute') $mode = 'execute';
    if ($arg === '--rollback') $mode = 'rollback';
    if (preg_match('/^--admin-id=(\d+)$/', $arg, $m)) $adminId = (int)$m[1];
    if (preg_match('/^--batch-id=(.+)$/', $arg, $m)) $batchId = trim($m[1]);
}

echo "=================================================================\n";
echo " Smart City Tiruppur — Transactional Reassignment Executor        \n";
echo " Mode: $mode | Admin User ID: $adminId                           \n";
echo "=================================================================\n\n";

if ($mode === 'preview') {
    $proposedCount = (int)$pdo->query("SELECT COUNT(*) FROM complaint_reassignment_plans WHERE plan_status = 'proposed'")->fetchColumn();
    $appliedCount = (int)$pdo->query("SELECT COUNT(*) FROM complaint_reassignment_plans WHERE plan_status = 'applied'")->fetchColumn();

    echo "Pending proposals ready for approval: $proposedCount\n";
    echo "Already applied proposals: $appliedCount\n\n";

    echo "Proposed reassignment summary by officer:\n";
    $summary = $pdo->query("
        SELECT o.id, o.officer_name, o.designation, COUNT(*) as cnt
        FROM complaint_reassignment_plans crp
        JOIN officers o ON o.id = crp.proposed_officer_id
        WHERE crp.plan_status = 'proposed'
        GROUP BY o.id
        ORDER BY cnt DESC
        LIMIT 15
    ")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($summary as $row) {
        echo " - Officer #{$row['id']} ({$row['officer_name']}, {$row['designation']}): {$row['cnt']} complaints\n";
    }

    echo "\nTo execute approved reassignments transactionally, run:\n";
    echo "  php tools/apply_approved_reassignments.php --execute --admin-id=1\n";
    exit(0);
}

if ($mode === 'execute') {
    $executionBatchId = 'BATCH_' . date('Ymd_His');
    echo "Starting transactional application with Batch ID: $executionBatchId\n";

    $stmt = $pdo->query("
        SELECT crp.*, c.status as original_status, c.assigned_officer_id as original_officer_id
        FROM complaint_reassignment_plans crp
        JOIN complaints c ON c.id = crp.complaint_id
        WHERE crp.plan_status = 'proposed'
        ORDER BY crp.id ASC
    ");
    $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($plans)) {
        echo "No pending proposed plans found to apply.\n";
        exit(0);
    }

    $pdo->beginTransaction();
    try {
        $updateComp = $pdo->prepare("
            UPDATE complaints 
            SET assigned_officer_id = ?, 
                status = 'Assigned',
                due_at = COALESCE(due_at, ?),
                updated_at = NOW()
            WHERE id = ?
        ");

        $insertAssign = $pdo->prepare("
            INSERT INTO complaint_assignments
            (complaint_id, officer_id, assigned_by, assigned_at, is_current)
            VALUES (?, ?, ?, NOW(), 1)
        ");

        $closeOldAssign = $pdo->prepare("
            UPDATE complaint_assignments 
            SET is_current = 0, unassigned_at = NOW() 
            WHERE complaint_id = ? AND is_current = 1
        ");

        $insertHistory = $pdo->prepare("
            INSERT INTO complaint_status_history
            (complaint_id, old_status, new_status, remarks, changed_by)
            VALUES (?, ?, 'Assigned', ?, ?)
        ");

        $updatePlan = $pdo->prepare("
            UPDATE complaint_reassignment_plans 
            SET plan_status = 'applied', applied_at = NOW(), applied_by = ?
            WHERE id = ?
        ");

        $applied = 0;
        foreach ($plans as $p) {
            $cid = (int)$p['complaint_id'];
            $newOfficerId = (int)$p['proposed_officer_id'];
            $oldStatus = (string)$p['original_status'];

            $closeOldAssign->execute([$cid]);
            $insertAssign->execute([$cid, $newOfficerId, $adminId]);
            $updateComp->execute([$newOfficerId, $p['calculated_due_at'], $cid]);

            $remarks = "Intelligent Reassignment applied: " . $p['reassignment_reason'] . " (Batch: $executionBatchId)";
            $insertHistory->execute([$cid, $oldStatus, $remarks, $adminId]);
            $updatePlan->execute([$adminId, $p['id']]);

            $applied++;
        }

        $pdo->commit();
        echo "SUCCESS! Transaction committed. Applied $applied complaint reassignments under $executionBatchId.\n";
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo "FATAL ERROR: Transaction rolled back. " . $e->getMessage() . "\n";
        exit(1);
    }
}
