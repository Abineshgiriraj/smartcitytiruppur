<?php
declare(strict_types=1);
/**
 * escalate-complaints.php
 *
 * Run on a schedule (e.g. hourly via cron):
 *   php /path/to/smartcitytiruppur/escalate-complaints.php
 *
 * Additive only — does not change existing page behaviour.
 * Uses complaints.assigned_officer_id, complaint_assignments,
 * and complaint_status_history exactly as new-complaint.php /
 * complaint-view.php already do for normal (re)assignment, so
 * every existing page keeps working without modification.
 */
require_once __DIR__ . '/includes/bootstrap.php';

$pdo = db();

// Every open complaint that has escalation rules defined either in
// department_allocations (ward-level multi-stage) or escalation_officer_levels.
$openComplaints = $pdo->query(
    "SELECT c.id, c.department_id, c.category_id, c.zone_id, c.ward_id, c.status, c.created_at
     FROM complaints c
     WHERE c.is_deleted = 0
       AND c.status NOT IN ('Resolved','Closed','Rejected')
       AND (
           EXISTS (
               SELECT 1 FROM department_allocations da
               WHERE da.department_id = c.department_id
                 AND da.category_id = c.category_id
                 AND da.zone_id = c.zone_id
                 AND da.ward_id = c.ward_id
                 AND da.is_active = 1
           )
           OR EXISTS (
               SELECT 1 FROM escalation_officer_levels el
               WHERE el.category_id = c.category_id AND el.is_active = 1
           )
       )"
)->fetchAll();

$escalatedCount = 0;

foreach ($openComplaints as $complaint) {
    $complaintId  = (int)$complaint['id'];
    $departmentId = (int)$complaint['department_id'];
    $categoryId   = (int)$complaint['category_id'];
    $zoneId       = (int)$complaint['zone_id'];
    $wardId       = (int)$complaint['ward_id'];

    // Current level = highest level_no already escalated to, else level 1
    // (level 1 is the complaint's original assignment at creation time).
    $levelStmt = $pdo->prepare(
        'SELECT level_no, escalated_at FROM complaint_escalations
         WHERE complaint_id = ? ORDER BY level_no DESC LIMIT 1'
    );
    $levelStmt->execute([$complaintId]);
    $lastEscalation = $levelStmt->fetch();

    $currentLevel = $lastEscalation ? (int)$lastEscalation['level_no'] : 1;
    $sinceTime    = $lastEscalation ? $lastEscalation['escalated_at'] : $complaint['created_at'];

    // Check SLA days at current stage: department_allocations first, then escalation_officer_levels
    $currentDays = null;
    $allocCurStmt = $pdo->prepare(
        'SELECT escalation_days FROM department_allocations
         WHERE department_id = ? AND category_id = ? AND zone_id = ? AND ward_id = ?
           AND stage_no = ? AND is_active = 1
         LIMIT 1'
    );
    $allocCurStmt->execute([$departmentId, $categoryId, $zoneId, $wardId, $currentLevel]);
    $allocCurRow = $allocCurStmt->fetch();
    if ($allocCurRow) {
        $currentDays = (int)$allocCurRow['escalation_days'];
    } else {
        $currentLevelStmt = $pdo->prepare(
            'SELECT id, escalation_period_days FROM escalation_officer_levels
             WHERE category_id = ? AND level_no = ? AND is_active = 1'
        );
        $currentLevelStmt->execute([$categoryId, $currentLevel]);
        $currentLevelRow = $currentLevelStmt->fetch();
        if ($currentLevelRow) {
            $currentDays = (int)$currentLevelRow['escalation_period_days'];
        }
    }

    if ($currentDays === null || $currentDays <= 0) {
        continue; // no SLA configured for this level — skip
    }

    $deadline = strtotime($sinceTime) + ($currentDays * 86400);
    if (time() < $deadline) {
        continue; // still within SLA for this level
    }

    // Overdue — find the next stage officer: department_allocations first, then escalation_officer_levels
    $nextLevel = null;
    $nextEscalationLevelId = null;

    $allocNextStmt = $pdo->prepare(
        'SELECT da.stage_no AS level_no, da.officer_id, da.escalation_days,
                COALESCE(o.designation, o.officer_name) AS designation
         FROM department_allocations da
         JOIN officers o ON o.id = da.officer_id AND o.is_active = 1
         WHERE da.department_id = ? AND da.category_id = ? AND da.zone_id = ? AND da.ward_id = ?
           AND da.stage_no = ? AND da.is_active = 1
         LIMIT 1'
    );
    $allocNextStmt->execute([$departmentId, $categoryId, $zoneId, $wardId, $currentLevel + 1]);
    $allocNext = $allocNextStmt->fetch();

    if ($allocNext) {
        $nextLevel = $allocNext;
    } else {
        $nextLevelStmt = $pdo->prepare(
            'SELECT id, level_no, officer_id, designation, escalation_period_days AS escalation_days
             FROM escalation_officer_levels
             WHERE category_id = ? AND level_no = ? AND is_active = 1'
        );
        $nextLevelStmt->execute([$categoryId, $currentLevel + 1]);
        $eolNext = $nextLevelStmt->fetch();
        if ($eolNext) {
            $nextLevel = $eolNext;
            $nextEscalationLevelId = (int)$eolNext['id'];
        }
    }

    if (!$nextLevel) {
        continue; // already at the highest configured level — nothing further to do
    }

    $pdo->beginTransaction();
    try {
        // 1. Reassign the complaint (same column every other page already reads).
        $nextDueAt = date('Y-m-d H:i:s', time() + ((int)($nextLevel['escalation_days'] ?? 3) * 86400));
        $pdo->prepare('UPDATE complaints SET assigned_officer_id = ?, due_at = ? WHERE id = ?')
            ->execute([(int)$nextLevel['officer_id'], $nextDueAt, $complaintId]);

        // 2. Close out the previous assignment row, open a new one —
        //    exactly the pattern complaint_assignments already exists for.
        $pdo->prepare(
            'UPDATE complaint_assignments SET is_current = 0, unassigned_at = NOW()
             WHERE complaint_id = ? AND is_current = 1'
        )->execute([$complaintId]);

        $pdo->prepare(
            'INSERT INTO complaint_assignments (complaint_id, officer_id, assigned_by, assigned_at, is_current)
             VALUES (?, ?, NULL, NOW(), 1)'
        )->execute([$complaintId, (int)$nextLevel['officer_id']]);

        // 3. Log it in the existing status-history timeline citizens already see
        //    on complaint-view.php / complaint-status.php.
        $pdo->prepare(
            'INSERT INTO complaint_status_history (complaint_id, old_status, new_status, remarks, changed_by, changed_at)
             VALUES (?, ?, ?, ?, NULL, NOW())'
        )->execute([
            $complaintId,
            $complaint['status'],
            $complaint['status'],
            'Auto-escalated to Stage ' . $nextLevel['level_no'] . ' (' . $nextLevel['designation'] . ') — SLA exceeded at previous stage.',
        ]);

        // 4. Record the escalation itself.
        $pdo->prepare(
            'INSERT INTO complaint_escalations (complaint_id, escalation_level_id, level_no, officer_id, escalated_at)
             VALUES (?, ?, ?, ?, NOW())'
        )->execute([$complaintId, $nextEscalationLevelId, (int)$nextLevel['level_no'], (int)$nextLevel['officer_id']]);

        $pdo->commit();
        $escalatedCount++;
    } catch (Throwable $e) {
        $pdo->rollBack();
        log_message('Escalation failed for complaint ' . $complaintId . ': ' . $e->getMessage());
    }
}

log_message("Escalation run complete: {$escalatedCount} complaint(s) escalated.");
echo "{$escalatedCount} complaint(s) escalated." . PHP_EOL;
