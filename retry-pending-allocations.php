<?php
declare(strict_types=1);
/**
 * Retry Registered/unassigned complaints using the existing PDF-approved
 * Stage 1 allocator. Default is report-only. Local main DB opt-in only.
 *
 * php retry-pending-allocations.php --dry-run --expect-db=smartcitytiruppur
 * php retry-pending-allocations.php --execute --expect-db=smartcitytiruppur
 *
 * IMPORTANT: This does not approve policies, staff or mappings.
 * Local execution requires reviewed PDF and officer approvals; never run on live hosting.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/local-pdf-workflow-guard.php';
$expected = null;
$execute = false;
$complaintId = null;
$limit = 100;
foreach (array_slice($argv, 1) as $arg) {
    if ($arg === '--dry-run') continue;
    if ($arg === '--execute') { $execute = true; continue; }
    if (preg_match('/^--expect-db=([A-Za-z0-9_]+)$/D', $arg, $m)) {
        $expected = $m[1]; continue;
    }
    if (preg_match('/^--complaint-id=([1-9][0-9]*)$/D', $arg, $m)) {
        $complaintId = (int)$m[1]; continue;
    }
    if (preg_match('/^--limit=([1-9][0-9]{0,2})$/D', $arg, $m) && (int)$m[1] <= 500) {
        $limit = (int)$m[1]; continue;
    }
    fwrite(STDERR, "Unknown argument: {$arg}\n"); exit(2);
}
if ($expected === null) {
    fwrite(STDERR, "Required --expect-db=DATABASE\n"); exit(2);
}
$pdo = db();
$actual = (string)$pdo->query('SELECT DATABASE()')->fetchColumn();
enforce_local_pdf_workflow($pdo, $expected, $execute);
$pdo->exec("SET SESSION time_zone = '+05:30'");
$sql = "SELECT id,reference_no,department_id,category_id,ward_id,zone_id
        FROM complaints WHERE is_deleted=0 AND status='Registered'
          AND assigned_officer_id IS NULL";
$params = [];
if ($complaintId !== null) {
    $sql .= ' AND id=?';
    $params[] = $complaintId;
}
$sql .= ' ORDER BY id ASC LIMIT ' . $limit;
$query = $pdo->prepare($sql);
$query->execute($params);
$pending = $query->fetchAll(PDO::FETCH_ASSOC);
echo ($execute ? '[LOCAL EXECUTE]' : '[DRY RUN]') . " DB={$actual}; pending examined=" . count($pending) . "\n";
$lockHeld = false;
$assigned = 0;
$skipped = 0;
try {
    if ($execute) {
        $lockHeld = (int)$pdo->query("SELECT GET_LOCK('smartcity_pending_allocation', 0)")->fetchColumn() === 1;
        if (!$lockHeld) {
            fwrite(STDERR, "Retry worker already running.\n"); exit(2);
        }
    }
    foreach ($pending as $c) {
        $id = (int)$c['id'];
        $ref = (string)$c['reference_no'];
        if (!$execute) {
            // Read-only preview: context and authorization readiness. This is
            // NOT a prediction of the final officer or a guarantee of assignment.
            $context = valid_complaint_routing_context(
                $pdo, (int)$c['department_id'], (int)$c['category_id'],
                (int)$c['zone_id'], (int)$c['ward_id']
            );
            $policy = $context ? pdf_policy_reviewed($pdo, (int)$c['category_id']) : null;
            if (!$context) $reason = 'INVALID_ROUTING_CONTEXT';
            elseif (!$policy) $reason = 'PDF_POLICY_MISSING_OR_UNAPPROVED';
            else $reason = 'REVIEW_APPROVED_OFFICERS_AND_EXACT_RULE; NO WRITES';
            echo "[CHECK] {$ref}: {$reason}\n";
            continue;
        }
        try {
            $pdo->beginTransaction();
            $lock = $pdo->prepare(
                "SELECT id,department_id,category_id,ward_id,zone_id
                 FROM complaints
                 WHERE id=? AND is_deleted=0 AND status='Registered'
                   AND assigned_officer_id IS NULL FOR UPDATE"
            );
            $lock->execute([$id]);
            $fresh = $lock->fetch(PDO::FETCH_ASSOC);
            if (!$fresh) {
                $pdo->rollBack(); $skipped++;
                echo "[SKIP] {$ref} no longer pending\n";
                continue;
            }
            $officer = assign_officer_for_complaint(
                $id, (int)$fresh['department_id'], (int)$fresh['category_id'],
                (int)$fresh['ward_id'], (int)$fresh['zone_id']
            );
            if ($officer === null) {
                $pdo->rollBack(); $skipped++;
                echo "[SKIP] {$ref} policy, approval, rule or history not eligible; inspect logs\n";
                continue;
            }
            $pdo->commit(); $assigned++;
            echo "[ALLOCATED] {$ref} officer #{$officer}\n";
        } catch (Throwable $error) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $skipped++;
            fwrite(STDERR, "[ERROR] {$ref}: {$error->getMessage()}\n");
        }
    }
} finally {
    if ($lockHeld) $pdo->query("SELECT RELEASE_LOCK('smartcity_pending_allocation')");
}
echo "Finished. Allocated={$assigned}; skipped={$skipped}. No schedule installed.\n";
