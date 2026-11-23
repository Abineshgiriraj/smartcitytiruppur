<?php
declare(strict_types=1);

/**
 * Retry Registered/unassigned complaints using the PDF-approved Stage 1 allocator.
 * Default is read-only preview. Local smartcitytiruppur DB opt-in only.
 *
 * Usage:
 *   php retry-pending-allocations.php --dry-run --expect-db=smartcitytiruppur
 *   php retry-pending-allocations.php --dry-run --expect-db=smartcitytiruppur --complaint-id=576866
 *   php retry-pending-allocations.php --dry-run --expect-db=smartcitytiruppur --reference=TCMC-2026-576866
 *   php retry-pending-allocations.php --execute --expect-db=smartcitytiruppur --complaint-id=576866
 *   php retry-pending-allocations.php --execute --expect-db=smartcitytiruppur --limit=10
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only\n");
}

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/local-pdf-workflow-guard.php';
require_once __DIR__ . '/includes/pdf-escalation-policy.php';
require_once __DIR__ . '/includes/officer-authority.php';
require_once __DIR__ . '/includes/functions.php';

$expected = null;
$execute = false;
$complaintId = null;
$complaintRef = null;
$limit = 100;
$filterDept = null;
$filterCat = null;

foreach (array_slice($argv, 1) as $arg) {
    if ($arg === '--dry-run') continue;
    if ($arg === '--execute') { $execute = true; continue; }
    if (preg_match('/^--expect-db=([A-Za-z0-9_]+)$/D', $arg, $m)) {
        $expected = $m[1]; continue;
    }
    if (preg_match('/^--complaint-id=([1-9][0-9]*)$/D', $arg, $m)) {
        $complaintId = (int)$m[1]; continue;
    }
    if (preg_match('/^--(?:reference|complaint-ref)=([A-Za-z0-9_-]+)$/D', $arg, $m)) {
        $complaintRef = $m[1]; continue;
    }
    if (preg_match('/^--limit=([1-9][0-9]{0,2})$/D', $arg, $m) && (int)$m[1] <= 500) {
        $limit = (int)$m[1]; continue;
    }
    if (preg_match('/^--department-id=([1-9][0-9]*)$/D', $arg, $m)) {
        $filterDept = (int)$m[1]; continue;
    }
    if (preg_match('/^--category-id=([1-9][0-9]*)$/D', $arg, $m)) {
        $filterCat = (int)$m[1]; continue;
    }
    fwrite(STDERR, "Unknown argument: {$arg}\n");
    exit(2);
}

if ($expected === null) {
    fwrite(STDERR, "Required: --expect-db=smartcitytiruppur\n");
    exit(2);
}

$pdo = db();
$actual = (string)$pdo->query('SELECT DATABASE()')->fetchColumn();
enforce_local_pdf_workflow($pdo, $expected, $execute);
$pdo->exec("SET SESSION time_zone = '+05:30'");

// Build complaint selection query
$sql = "SELECT id, reference_no, department_id, category_id, ward_id, zone_id, status, created_at, due_at
        FROM complaints
        WHERE is_deleted = 0 AND status = 'Registered' AND assigned_officer_id IS NULL";
$params = [];

if ($complaintId !== null) {
    $sql .= ' AND id = ?';
    $params[] = $complaintId;
} elseif ($complaintRef !== null) {
    $sql .= ' AND reference_no = ?';
    $params[] = $complaintRef;
}
if ($filterDept !== null) {
    $sql .= ' AND department_id = ?';
    $params[] = $filterDept;
}
if ($filterCat !== null) {
    $sql .= ' AND category_id = ?';
    $params[] = $filterCat;
}

$sql .= ' ORDER BY id ASC LIMIT ' . $limit;
$query = $pdo->prepare($sql);
$query->execute($params);
$pending = $query->fetchAll(PDO::FETCH_ASSOC);

echo ($execute ? '[LOCAL EXECUTE]' : '[DRY RUN PREVIEW]') . " DB={$actual}; pending examined=" . count($pending) . "\n";

$lockHeld = false;
$assigned = 0;
$skipped = 0;

try {
    if ($execute) {
        $lockHeld = ((int)$pdo->query("SELECT GET_LOCK('smartcity_pending_allocation', 0)")->fetchColumn() === 1);
        if (!$lockHeld) {
            fwrite(STDERR, "Retry worker is already running (MySQL named lock busy).\n");
            exit(2);
        }
    }

    foreach ($pending as $c) {
        $id = (int)$c['id'];
        $ref = (string)$c['reference_no'];
        $deptId = (int)$c['department_id'];
        $catId = (int)$c['category_id'];
        $wardId = (int)$c['ward_id'];
        $zoneId = (int)$c['zone_id'];

        if (!$execute) {
            // Read-Only Preview Analysis
            $context = valid_complaint_routing_context($pdo, $deptId, $catId, $zoneId, $wardId);
            if (!$context) {
                echo "[CHECK] {$ref}: INVALID_ROUTING_CONTEXT (Mismatched department, category, or ward/zone)\n";
                continue;
            }

            $policy = pdf_policy_routing_ready($pdo, $catId);
            if (!$policy) {
                echo "[CHECK] {$ref}: PDF_POLICY_MISSING_OR_INVALID\n";
                continue;
            }

            $firstStage = pdf_policy_stage($policy, 1);
            if (!$firstStage || (int)$firstStage['period_days'] <= 0) {
                echo "[CHECK] {$ref}: PDF_STAGE_1_INVALID_OR_ZERO_DAYS\n";
                continue;
            }

            // Check exact department_allocations rule
            $exactStmt = $pdo->prepare(
                'SELECT da.officer_id, o.officer_name, o.designation, da.escalation_days
                 FROM department_allocations da
                 JOIN officers o ON o.id = da.officer_id
                 WHERE da.department_id = ? AND da.category_id = ? AND da.zone_id = ? AND da.ward_id = ?
                   AND da.stage_no = 1 AND da.is_active = 1 LIMIT 1'
            );
            $exactStmt->execute([$deptId, $catId, $zoneId, $wardId]);
            $exact = $exactStmt->fetch(PDO::FETCH_ASSOC);

            if ($exact) {
                $eId = (int)$exact['officer_id'];
                if (officer_stage_authorized($pdo, $catId, 1, $eId, $deptId, $wardId)
                    && pdf_policy_role_matches((string)$firstStage['role_label'], (string)$exact['designation'])) {
                    echo "[PREVIEW READY] {$ref}: Would allocate to Officer #{$eId} ({$exact['officer_name']}, {$exact['designation']}) via Exact Rule (Stage 1 SLA: {$firstStage['period_days']}d)\n";
                } else {
                    echo "[CHECK] {$ref}: EXACT_ALLOCATION_OFFICER_INELIGIBLE (Rule points to Officer #{$eId})\n";
                }
                continue;
            }

            // The same eligibility source is used by registration and escalation.
            $candidates=pdf_routing_candidates($pdo,$catId,1,$deptId,$wardId);
            $bestRank=$candidates ? (int)$candidates[0]['ward_rank'] : null;
            $best=array_values(array_filter($candidates,
                static fn(array $item):bool => $bestRank !== null && (int)$item['ward_rank'] === $bestRank));
            if(count($best)===1){
                $sel=$best[0];
                echo "[PREVIEW READY] {$ref}: Would allocate to Officer #{$sel['id']} ({$sel['officer_name']}, {$sel['designation']}) with Stage 1 SLA: {$firstStage['period_days']}d\n";
            } elseif(count($best)>1){
                echo "[CHECK] {$ref}: AMBIGUOUS_MAPPED_OFFICERS (".count($best)." equally ranked officers; exact ward rule required)\n";
            } else {
                echo "[CHECK] {$ref}: NO_ELIGIBLE_MAPPED_STAGE_1_OFFICER (verify active officer, designation, department, category and ward maps)\n";
            }
            continue;
        }

        // Execution Mode
        try {
            $pdo->beginTransaction();
            $lock = $pdo->prepare(
                "SELECT id, department_id, category_id, ward_id, zone_id
                 FROM complaints
                 WHERE id = ? AND is_deleted = 0 AND status = 'Registered'
                   AND assigned_officer_id IS NULL FOR UPDATE"
            );
            $lock->execute([$id]);
            $fresh = $lock->fetch(PDO::FETCH_ASSOC);

            if (!$fresh) {
                $pdo->rollBack();
                $skipped++;
                echo "[SKIP] {$ref} no longer pending or already assigned\n";
                continue;
            }

            $officerId = assign_officer_for_complaint(
                $id, (int)$fresh['department_id'], (int)$fresh['category_id'],
                (int)$fresh['ward_id'], (int)$fresh['zone_id']
            );

            if ($officerId === null) {
                $pdo->rollBack();
                $skipped++;
                // Detailed skip diagnostics
                $p = pdf_policy_routing_ready($pdo, (int)$fresh['category_id']);
                if (!$p) {
                    echo "[SKIP] {$ref} PDF_POLICY_MISSING_OR_UNAPPROVED\n";
                } else {
                    echo "[SKIP] {$ref} NO_ELIGIBLE_OR_AMBIGUOUS_STAGE_1_OFFICER\n";
                }
                continue;
            }

            $pdo->commit();
            $assigned++;
            audit_log(null, 'complaint_auto_allocated', 'complaint', $id, [
                'reference_no' => $ref,
                'officer_id' => $officerId,
                'source' => 'retry-pending-allocations CLI',
            ]);
            echo "[ALLOCATED] {$ref} allocated to Officer #{$officerId}\n";
        } catch (Throwable $error) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $skipped++;
            fwrite(STDERR, "[ERROR] {$ref}: {$error->getMessage()}\n");
        }
    }
} finally {
    if ($lockHeld) {
        $pdo->query("SELECT RELEASE_LOCK('smartcity_pending_allocation')");
    }
}

echo "Finished. Allocated={$assigned}; skipped={$skipped}. No schedule installed.\n";
