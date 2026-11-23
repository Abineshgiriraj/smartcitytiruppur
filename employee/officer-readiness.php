<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/pdf-escalation-policy.php';
require_once __DIR__ . '/../includes/officer-authority.php';

$user = require_roles(['Super Admin', 'Admin']);
require_permission('masters.view');
$canManage = can_access_permission($user, 'masters.manage');
$pdo = db();
$pageTitle = 'Officer Mapping Readiness Dashboard';

// Handle POST actions (Individual Authorize, Bulk Authorize, Revoke)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$canManage) {
        flash('danger', 'You do not have permission to authorize officer stage mappings.');
        redirect('employee/officer-readiness.php');
    }
    verify_csrf();
    $action = trim((string)($_POST['action'] ?? 'authorize'));
    $reference = trim((string)($_POST['approval_reference'] ?? ''));
    $isSimulation = !empty($_POST['is_simulation']);

    // Simulation and official approvals must be physically separated.
    // This official registry is used for routing real citizen grievances.
    if ($isSimulation || str_starts_with(strtoupper($reference), 'SIMULATION-LOCAL-TEST-')) {
        flash('danger', 'Simulation approvals cannot be stored in the official routing registry. Use isolated synthetic fixtures instead.');
        redirect('employee/officer-readiness.php');
    }

    try {
        if ($action === 'authorize') {
            $categoryId = (int)($_POST['category_id'] ?? 0);
            $stageNo = (int)($_POST['stage_no'] ?? 0);
            $officerId = (int)($_POST['officer_id'] ?? 0);

            if ($categoryId < 1 || $stageNo < 1 || $stageNo > 6 || $officerId < 1
                || mb_strlen($reference) < 8 || empty($_POST['confirm_authority'])) {
                throw new RuntimeException('Category, stage (1-6), officer, approval reference (min 8 chars), and confirmation are required.');
            }

            // Verify policy
            $policy = pdf_policy_reviewed($pdo, $categoryId);
            if (!$policy) {
                throw new RuntimeException('This category PDF policy is not yet approved. Approve the policy before authorizing officers.');
            }
            $stageStep = pdf_policy_stage($policy, $stageNo);
            if (!$stageStep) {
                throw new RuntimeException("Stage {$stageNo} does not exist in this category's approved PDF policy.");
            }

            // Verify officer eligibility
            $stmt = $pdo->prepare(
                "SELECT o.id, o.officer_name, o.designation, o.is_active, cc.department_id,
                        EXISTS(SELECT 1 FROM officer_department_map dm WHERE dm.officer_id = o.id AND dm.department_id = cc.department_id) as dept_ok,
                        EXISTS(SELECT 1 FROM officer_category_map cm WHERE cm.officer_id = o.id AND cm.category_id = cc.id) as cat_ok
                 FROM officers o
                 CROSS JOIN complaint_categories cc ON cc.id = ?
                 WHERE o.id = ? LIMIT 1"
            );
            $stmt->execute([$categoryId, $officerId]);
            $cand = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$cand || !(int)$cand['is_active']) {
                throw new RuntimeException("Officer #{$officerId} is not active in the employee master.");
            }
            if (!pdf_policy_role_matches((string)$stageStep['role_label'], (string)$cand['designation'])) {
                throw new RuntimeException("Officer #{$officerId} designation '{$cand['designation']}' does not match PDF required role '{$stageStep['role_label']}'.");
            }
            if (!(int)$cand['dept_ok'] || !(int)$cand['cat_ok']) {
                throw new RuntimeException("Officer #{$officerId} is not mapped to this department and category.");
            }

            $forbidden = officer_stage_forbidden_title((string)$cand['designation'], $stageNo, $officerId);
            if ($forbidden && strtoupper(trim((string)$stageStep['role_label'])) === 'COMMR' && $stageNo > 1 && $officerId !== 1) {
                $forbidden = false;
            }
            if ($forbidden) {
                throw new RuntimeException("Officer #{$officerId} has a title forbidden at Stage {$stageNo}.");
            }

            $pdo->beginTransaction();
            $existing = $pdo->prepare('SELECT id, revoked_at FROM officer_stage_approvals WHERE category_id = ? AND stage_no = ? AND officer_id = ? FOR UPDATE');
            $existing->execute([$categoryId, $stageNo, $officerId]);
            $prev = $existing->fetch(PDO::FETCH_ASSOC);

            if ($prev && $prev['revoked_at'] === null) {
                throw new RuntimeException("Officer #{$officerId} is already authorized for Stage {$stageNo}.");
            }

            if ($prev) {
                $save = $pdo->prepare('UPDATE officer_stage_approvals
                    SET revoked_at = NULL, approval_reference = ?, approved_by = ?, approved_at = NOW()
                    WHERE id = ?');
                $save->execute([$reference, (int)$user['id'], (int)$prev['id']]);
            } else {
                $save = $pdo->prepare('INSERT INTO officer_stage_approvals
                    (category_id, stage_no, officer_id, approval_reference, approved_by, approved_at)
                    VALUES (?, ?, ?, ?, ?, NOW())');
                $save->execute([$categoryId, $stageNo, $officerId, $reference, (int)$user['id']]);
            }

            $pdo->commit();
            audit_log((int)$user['id'], 'officer_stage_approved', 'officer', $officerId, [
                'category_id' => $categoryId,
                'stage_no' => $stageNo,
                'reference' => $reference,
                'is_simulation' => $isSimulation,
            ]);

            flash('success', ($isSimulation ? '[SIMULATION MODE] ' : '') . "Officer #{$officerId} authorized for Category #{$categoryId} Stage {$stageNo}.");
            redirect('employee/officer-readiness.php?cat_id=' . $categoryId);

        } elseif ($action === 'bulk_authorize') {
            $rawItems = (array)($_POST['selected_items'] ?? []);
            if (!$rawItems || mb_strlen($reference) < 8 || empty($_POST['confirm_bulk_authority'])) {
                throw new RuntimeException('Select at least one candidate officer, enter an approval reference (min 8 chars), and confirm verified authority.');
            }

            $pdo->beginTransaction();
            $authorizedCount = 0;
            $skipped = 0;

            foreach ($rawItems as $itemStr) {
                // Format: "categoryId:stageNo:officerId"
                $parts = explode(':', (string)$itemStr);
                if (count($parts) !== 3) continue;
                $catId = (int)$parts[0];
                $stNo = (int)$parts[1];
                $offId = (int)$parts[2];

                $policy = pdf_policy_reviewed($pdo, $catId);
                if (!$policy) { $skipped++; continue; }
                $stageStep = pdf_policy_stage($policy, $stNo);
                if (!$stageStep) { $skipped++; continue; }

                $stmt = $pdo->prepare(
                    "SELECT o.id, o.officer_name, o.designation, o.is_active, cc.department_id,
                            EXISTS(SELECT 1 FROM officer_department_map dm WHERE dm.officer_id = o.id AND dm.department_id = cc.department_id) as dept_ok,
                            EXISTS(SELECT 1 FROM officer_category_map cm WHERE cm.officer_id = o.id AND cm.category_id = cc.id) as cat_ok
                     FROM officers o
                     CROSS JOIN complaint_categories cc ON cc.id = ?
                     WHERE o.id = ? LIMIT 1"
                );
                $stmt->execute([$catId, $offId]);
                $cand = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$cand || !(int)$cand['is_active'] || !(int)$cand['dept_ok'] || !(int)$cand['cat_ok']) {
                    $skipped++; continue;
                }
                if (!pdf_policy_role_matches((string)$stageStep['role_label'], (string)$cand['designation'])) {
                    $skipped++; continue;
                }

                $forbidden = officer_stage_forbidden_title((string)$cand['designation'], $stNo, $offId);
                if ($forbidden && strtoupper(trim((string)$stageStep['role_label'])) === 'COMMR' && $stNo > 1 && $offId !== 1) {
                    $forbidden = false;
                }
                if ($forbidden) { $skipped++; continue; }

                // Check existing
                $existing = $pdo->prepare('SELECT id, revoked_at FROM officer_stage_approvals WHERE category_id = ? AND stage_no = ? AND officer_id = ? FOR UPDATE');
                $existing->execute([$catId, $stNo, $offId]);
                $prev = $existing->fetch(PDO::FETCH_ASSOC);

                if ($prev && $prev['revoked_at'] === null) {
                    $skipped++; continue;
                }

                if ($prev) {
                    $pdo->prepare('UPDATE officer_stage_approvals SET revoked_at = NULL, approval_reference = ?, approved_by = ?, approved_at = NOW() WHERE id = ?')
                        ->execute([$reference, (int)$user['id'], (int)$prev['id']]);
                } else {
                    $pdo->prepare('INSERT INTO officer_stage_approvals (category_id, stage_no, officer_id, approval_reference, approved_by, approved_at) VALUES (?, ?, ?, ?, ?, NOW())')
                        ->execute([$catId, $stNo, $offId, $reference, (int)$user['id']]);
                }

                $authorizedCount++;
                audit_log((int)$user['id'], 'officer_stage_approved', 'officer', $offId, [
                    'category_id' => $catId,
                    'stage_no' => $stNo,
                    'reference' => $reference,
                    'is_simulation' => $isSimulation,
                    'batch' => true,
                ]);
            }

            $pdo->commit();
            $msg = ($isSimulation ? '[SIMULATION MODE] ' : '') . "Bulk authorization complete: {$authorizedCount} officer mappings authorized.";
            if ($skipped > 0) $msg .= " ({$skipped} items skipped due to policy or eligibility constraints).";
            flash('success', $msg);
            redirect('employee/officer-readiness.php');

        } elseif ($action === 'revoke') {
            $approvalId = (int)($_POST['approval_id'] ?? 0);
            if ($approvalId < 1 || mb_strlen($reference) < 8) {
                throw new RuntimeException('Select an approval and provide a revocation reference (min 8 chars).');
            }

            $pdo->beginTransaction();
            $revoke = $pdo->prepare('UPDATE officer_stage_approvals SET revoked_at = NOW() WHERE id = ? AND revoked_at IS NULL');
            $revoke->execute([$approvalId]);

            if ($revoke->rowCount() !== 1) throw new RuntimeException('Approval record is not active or already revoked.');
            $pdo->commit();

            audit_log((int)$user['id'], 'officer_stage_revoked', 'officer_approval', $approvalId, ['reference' => $reference]);
            flash('success', 'Officer authorization revoked.');
            redirect('employee/officer-readiness.php');
        }
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        flash('danger', APP_ENV === 'local' ? $e->getMessage() : 'Authorization action failed.');
        redirect('employee/officer-readiness.php');
    }
}

// Filters
$filterDept = (int)($_GET['dept_id'] ?? 0);
$filterCat = (int)($_GET['cat_id'] ?? 0);
$filterStage = (int)($_GET['stage'] ?? 0);
$filterStatus = trim((string)($_GET['status'] ?? ''));
$filterWard = (int)($_GET['ward_id'] ?? 0);

// Load filter options
$departments = $pdo->query('SELECT id, department_name FROM departments ORDER BY department_name')->fetchAll(PDO::FETCH_ASSOC);
$categoriesQuery = 'SELECT cc.id, cc.category_name, cc.department_id, d.department_name
                    FROM complaint_categories cc
                    JOIN departments d ON d.id = cc.department_id
                    WHERE cc.is_active = 1';
if ($filterDept > 0) {
    $categoriesQuery .= ' AND cc.department_id = ' . $filterDept;
}
$categoriesQuery .= ' ORDER BY d.department_name, cc.category_name LIMIT 400';
$categories = $pdo->query($categoriesQuery)->fetchAll(PDO::FETCH_ASSOC);

// Build readiness query
// To keep performance high, default to first department if none selected, or allow viewing specific category
if ($filterDept === 0 && $filterCat === 0 && empty($_GET['view_all'])) {
    // Default to Department 3 (Engineering - Street Light) to inspect complaint 576866's department directly!
    $filterDept = 3;
}

// Fetch stages for analysis
$stageQuery = "SELECT s.category_id, s.stage_no, s.role_label, s.period_days,
                      cc.category_name, cc.department_id, d.department_name,
                      p.is_approved as policy_approved, p.approval_reference as policy_ref
               FROM pdf_escalation_policy_stages s
               JOIN pdf_escalation_policies p ON p.category_id = s.category_id
               JOIN complaint_categories cc ON cc.id = s.category_id
               JOIN departments d ON d.id = cc.department_id
               WHERE 1=1";
$stageParams = [];

if ($filterDept > 0) {
    $stageQuery .= " AND cc.department_id = ?";
    $stageParams[] = $filterDept;
}
if ($filterCat > 0) {
    $stageQuery .= " AND s.category_id = ?";
    $stageParams[] = $filterCat;
}
if ($filterStage > 0) {
    $stageQuery .= " AND s.stage_no = ?";
    $stageParams[] = $filterStage;
}
$stageQuery .= " ORDER BY d.department_name, cc.category_name, s.stage_no LIMIT 250";

$stageStmt = $pdo->prepare($stageQuery);
$stageStmt->execute($stageParams);
$stageRows = $stageStmt->fetchAll(PDO::FETCH_ASSOC);

// Pre-fetch all active officers mapped to the selected department(s)
$officerQuery = "SELECT o.id, o.officer_name, o.designation,
                        dm.department_id, cm.category_id,
                        (SELECT GROUP_CONCAT(wm.ward_id ORDER BY wm.ward_id SEPARATOR ',') FROM officer_ward_map wm WHERE wm.officer_id = o.id) as wards
                 FROM officers o
                 JOIN officer_department_map dm ON dm.officer_id = o.id
                 LEFT JOIN officer_category_map cm ON cm.officer_id = o.id
                 WHERE o.is_active = 1";
if ($filterDept > 0) {
    $officerQuery .= " AND dm.department_id = {$filterDept}";
}
$officerStmt = $pdo->query($officerQuery);
$allOfficers = $officerStmt->fetchAll(PDO::FETCH_ASSOC);

// Pre-fetch existing active approvals
$approvalsStmt = $pdo->query(
    "SELECT a.id as approval_id, a.category_id, a.stage_no, a.officer_id, a.approval_reference, a.approved_at,
            o.officer_name, o.designation, u.full_name as approved_by_name
     FROM officer_stage_approvals a
     JOIN officers o ON o.id = a.officer_id
     LEFT JOIN users u ON u.id = a.approved_by
     WHERE a.revoked_at IS NULL"
);
$liveApprovals = [];
foreach ($approvalsStmt->fetchAll(PDO::FETCH_ASSOC) as $appr) {
    $liveApprovals[$appr['category_id'] . ':' . $appr['stage_no']][] = $appr;
}

// Compute Readiness Matrix
$readinessRows = [];
$totalRows = 0;
$approvedRows = 0;
$readyUniqueRows = 0;
$ambiguousRows = 0;
$missingRows = 0;

foreach ($stageRows as $st) {
    $cId = (int)$st['category_id'];
    $sNo = (int)$st['stage_no'];
    $dId = (int)$st['department_id'];
    $role = (string)$st['role_label'];
    $key = $cId . ':' . $sNo;

    // Check if already approved
    $existingApprs = $liveApprovals[$key] ?? [];
    $isApproved = !empty($existingApprs);

    // Find candidate officers matching role, department, and category
    $candidates = [];
    foreach ($allOfficers as $o) {
        if ((int)$o['department_id'] !== $dId) continue;
        if ($o['category_id'] !== null && (int)$o['category_id'] !== $cId) continue;
        if (pdf_policy_role_matches($role, (string)$o['designation'])) {
            $candidates[] = $o;
        }
    }

    // Determine status
    if ($isApproved) {
        $status = 'APPROVED';
        $statusBadge = '<span class="badge bg-success"><i class="bi bi-check2-circle me-1"></i> Authorized</span>';
        $approvedRows++;
    } elseif (!(int)$st['policy_approved']) {
        $status = 'POLICY_DRAFT';
        $statusBadge = '<span class="badge bg-secondary"><i class="bi bi-clock me-1"></i> Policy Draft</span>';
    } elseif (count($candidates) === 1) {
        $status = 'READY_UNIQUE';
        $statusBadge = '<span class="badge bg-primary"><i class="bi bi-person-check me-1"></i> Ready (Unique)</span>';
        $readyUniqueRows++;
    } elseif (count($candidates) > 1) {
        $status = 'AMBIGUOUS';
        $statusBadge = '<span class="badge bg-warning text-dark"><i class="bi bi-people me-1"></i> Ambiguous (' . count($candidates) . ')</span>';
        $ambiguousRows++;
    } else {
        $status = 'MISSING';
        $statusBadge = '<span class="badge bg-danger"><i class="bi bi-x-octagon me-1"></i> Missing Officer</span>';
        $missingRows++;
    }

    // Apply status filter if set
    if ($filterStatus !== '' && $filterStatus !== 'all' && $status !== $filterStatus) {
        continue;
    }

    $readinessRows[] = [
        'category_id' => $cId,
        'stage_no' => $sNo,
        'department_name' => $st['department_name'],
        'category_name' => $st['category_name'],
        'required_role' => $role,
        'period_days' => (int)$st['period_days'],
        'policy_approved' => (int)$st['policy_approved'] === 1,
        'policy_ref' => $st['policy_ref'],
        'status' => $status,
        'status_badge' => $statusBadge,
        'approvals' => $existingApprs,
        'candidates' => $candidates,
    ];
}

include __DIR__ . '/../includes/employee-header.php';
?>

<div class="admin-heading mb-3">
  <div>
    <span class="admin-eyebrow">Officer Authority Governance</span>
    <h1>Officer Mapping &amp; Escalation Readiness Dashboard</h1>
    <p class="text-muted mb-0">Inspect candidate officers matching PDF required designations, ward scope, and authorized escalation stages.</p>
  </div>
  <div class="d-flex gap-2 align-items-center">
    <a href="<?php echo e(url('employee/pdf-policy-review.php')); ?>" class="btn btn-outline-primary">
      <i class="bi bi-file-earmark-check me-1"></i> PDF Policy Review
    </a>
    <a href="<?php echo e(url('employee/officer-approvals.php')); ?>" class="btn btn-outline-secondary">
      <i class="bi bi-shield-check me-1"></i> Approvals Registry
    </a>
  </div>
</div>

<!-- Readiness Scorecards -->
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card p-3 shadow-sm border-0 border-start border-4 border-success">
      <small class="text-muted text-uppercase fw-semibold">Authorized Stages</small>
      <h3 class="mb-0 fw-bold text-success"><?php echo $approvedRows; ?></h3>
      <small class="text-muted mt-1 d-block">Approved in registry</small>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3 shadow-sm border-0 border-start border-4 border-primary">
      <small class="text-muted text-uppercase fw-semibold">Ready for Authorization</small>
      <h3 class="mb-0 fw-bold text-primary"><?php echo $readyUniqueRows; ?></h3>
      <small class="text-muted mt-1 d-block">Unique candidate matched</small>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3 shadow-sm border-0 border-start border-4 border-warning">
      <small class="text-muted text-uppercase fw-semibold">Ambiguous Candidates</small>
      <h3 class="mb-0 fw-bold text-warning"><?php echo $ambiguousRows; ?></h3>
      <small class="text-muted mt-1 d-block">Multiple officers match</small>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3 shadow-sm border-0 border-start border-4 border-danger">
      <small class="text-muted text-uppercase fw-semibold">Missing Candidates</small>
      <h3 class="mb-0 fw-bold text-danger"><?php echo $missingRows; ?></h3>
      <small class="text-muted mt-1 d-block">No matching designation</small>
    </div>
  </div>
</div>

<!-- Filter Bar -->
<div class="card shadow-sm border-0 mb-3">
  <div class="card-body">
    <form method="get" class="row g-2 align-items-center">
      <div class="col-md-3">
        <label for="dept_id" class="form-label small fw-semibold text-muted mb-1">Department</label>
        <select id="dept_id" name="dept_id" class="form-select form-select-sm" onchange="this.form.submit()">
          <option value="0">All Departments</option>
          <?php foreach ($departments as $d): ?>
            <option value="<?php echo (int)$d['id']; ?>" <?php echo $filterDept === (int)$d['id'] ? 'selected' : ''; ?>>
              <?php echo e($d['department_name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-3">
        <label for="cat_id" class="form-label small fw-semibold text-muted mb-1">Category</label>
        <select id="cat_id" name="cat_id" class="form-select form-select-sm" onchange="this.form.submit()">
          <option value="0">All Categories in Dept</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?php echo (int)$c['id']; ?>" <?php echo $filterCat === (int)$c['id'] ? 'selected' : ''; ?>>
              <?php echo e($c['category_name']); ?> (#<?php echo (int)$c['id']; ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-2">
        <label for="stage" class="form-label small fw-semibold text-muted mb-1">Stage Level</label>
        <select id="stage" name="stage" class="form-select form-select-sm" onchange="this.form.submit()">
          <option value="0">All Stages (1-6)</option>
          <?php for ($s = 1; $s <= 6; $s++): ?>
            <option value="<?php echo $s; ?>" <?php echo $filterStage === $s ? 'selected' : ''; ?>>Stage <?php echo $s; ?></option>
          <?php endfor; ?>
        </select>
      </div>

      <div class="col-md-2">
        <label for="status" class="form-label small fw-semibold text-muted mb-1">Readiness Status</label>
        <select id="status" name="status" class="form-select form-select-sm" onchange="this.form.submit()">
          <option value="">All Statuses</option>
          <option value="READY_UNIQUE" <?php echo $filterStatus === 'READY_UNIQUE' ? 'selected' : ''; ?>>Ready (Unique Candidate)</option>
          <option value="APPROVED" <?php echo $filterStatus === 'APPROVED' ? 'selected' : ''; ?>>Authorized</option>
          <option value="AMBIGUOUS" <?php echo $filterStatus === 'AMBIGUOUS' ? 'selected' : ''; ?>>Ambiguous</option>
          <option value="MISSING" <?php echo $filterStatus === 'MISSING' ? 'selected' : ''; ?>>Missing Officer</option>
          <option value="POLICY_DRAFT" <?php echo $filterStatus === 'POLICY_DRAFT' ? 'selected' : ''; ?>>Policy Draft</option>
        </select>
      </div>

      <div class="col-md-2 d-flex align-items-end gap-1 mt-auto">
        <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
        <a href="<?php echo e(url('employee/officer-readiness.php')); ?>" class="btn btn-sm btn-outline-secondary" title="Reset"><i class="bi bi-arrow-repeat"></i></a>
      </div>
    </form>
  </div>
</div>

<!-- Bulk Authorization Form & Table -->
<form method="post" id="bulkOfficerForm">
  <?php echo csrf_field(); ?>
  <input type="hidden" name="action" value="bulk_authorize">

  <div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
      <div>
        <h5 class="mb-0 fw-bold">Officer Stage Mapping Matrix (<?php echo count($readinessRows); ?> stages examined)</h5>
        <small class="text-muted">Review candidates matching PDF role and ward scope. Controlled bulk authorization requires genuine approval reference.</small>
      </div>
      <?php if ($canManage): ?>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleSelectReady(true)">Select All Ready</button>
          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleSelectReady(false)">Deselect All</button>
          <button type="button" class="btn btn-sm btn-primary" onclick="prepareBulkModal()">
            <i class="bi bi-shield-check me-1"></i> Bulk Authorize Selected
          </button>
        </div>
      <?php endif; ?>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" id="readinessTable">
        <thead class="table-light">
          <tr>
            <?php if ($canManage): ?><th style="width: 40px;" class="text-center">#</th><?php endif; ?>
            <th>Department</th>
            <th>Category</th>
            <th class="text-center" style="width: 80px;">Stage</th>
            <th>Required Role</th>
            <th>Candidate Officer(s)</th>
            <th>Ward Scope</th>
            <th>Authorization Status</th>
            <th class="text-end" style="width: 140px;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$readinessRows): ?>
            <tr><td colspan="<?php echo $canManage ? 9 : 8; ?>" class="text-center py-4 text-muted">No stages found matching the selected filter.</td></tr>
          <?php endif; ?>
          <?php foreach ($readinessRows as $row): ?>
            <?php
              $firstCand = $row['candidates'][0] ?? null;
              $isReady = ($row['status'] === 'READY_UNIQUE' && $firstCand !== null && $row['policy_approved']);
              $itemValue = $row['category_id'] . ':' . $row['stage_no'] . ':' . ($firstCand ? (int)$firstCand['id'] : 0);
            ?>
            <tr>
              <?php if ($canManage): ?>
                <td class="text-center">
                  <?php if ($isReady): ?>
                    <input type="checkbox" name="selected_items[]" value="<?php echo e($itemValue); ?>" class="form-check-input ready-checkbox"
                           data-cat-name="<?php echo e($row['category_name']); ?>"
                           data-stage="<?php echo $row['stage_no']; ?>"
                           data-role="<?php echo e($row['required_role']); ?>"
                           data-officer="<?php echo e($firstCand['officer_name'] . ' (#' . $firstCand['id'] . ')'); ?>"
                           data-desig="<?php echo e($firstCand['designation']); ?>">
                  <?php else: ?>
                    <input type="checkbox" disabled class="form-check-input opacity-25">
                  <?php endif; ?>
                </td>
              <?php endif; ?>

              <td><span class="badge bg-light text-dark border"><?php echo e($row['department_name']); ?></span></td>
              <td class="fw-semibold">
                <?php echo e($row['category_name']); ?>
                <small class="text-muted d-block">Cat #<?php echo $row['category_id']; ?></small>
              </td>
              <td class="text-center">
                <span class="badge bg-dark">L<?php echo $row['stage_no']; ?></span>
                <small class="d-block text-muted"><?php echo $row['period_days']; ?>d</small>
              </td>
              <td><code><?php echo e($row['required_role']); ?></code></td>
              <td>
                <?php if ($row['approvals']): ?>
                  <?php foreach ($row['approvals'] as $a): ?>
                    <div>
                      <strong>#<?php echo (int)$a['officer_id']; ?> <?php echo e($a['officer_name']); ?></strong>
                      <span class="badge bg-secondary-subtle text-secondary small"><?php echo e($a['designation']); ?></span>
                    </div>
                  <?php endforeach; ?>
                <?php elseif ($row['candidates']): ?>
                  <?php foreach ($row['candidates'] as $c): ?>
                    <div class="mb-1">
                      <strong>#<?php echo (int)$c['id']; ?> <?php echo e($c['officer_name']); ?></strong>
                      <span class="badge bg-info-subtle text-dark small"><?php echo e($c['designation']); ?></span>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <span class="text-danger small"><i class="bi bi-exclamation-circle me-1"></i> No matching officer in master</span>
                <?php endif; ?>
              </td>
              <td class="small">
                <?php
                  if ($row['candidates']) {
                      $allWards = array_unique(array_filter(array_map('trim', explode(',', implode(',', array_column($row['candidates'], 'wards'))))));
                      if (count($allWards) >= 60 || !$allWards) {
                          echo '<span class="badge bg-light text-dark border">City-wide (1..60)</span>';
                      } else {
                          echo 'Wards: ' . implode(', ', array_slice($allWards, 0, 5)) . (count($allWards) > 5 ? '...' : '');
                      }
                  } else {
                      echo '&mdash;';
                  }
                ?>
              </td>
              <td>
                <?php echo $row['status_badge']; ?>
                <?php if ($row['approvals']): ?>
                  <small class="d-block text-muted text-truncate" style="max-width: 140px;" title="<?php echo e($row['approvals'][0]['approval_reference']); ?>">
                    <?php echo e($row['approvals'][0]['approval_reference']); ?>
                  </small>
                <?php endif; ?>
              </td>
              <td class="text-end">
                <?php if ($canManage): ?>
                  <?php if ($row['status'] === 'APPROVED'): ?>
                    <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2"
                            onclick="openRevokeModal(<?php echo (int)$row['approvals'][0]['approval_id']; ?>, '<?php echo e(addslashes($row['approvals'][0]['officer_name'])); ?>', <?php echo $row['category_id']; ?>, <?php echo $row['stage_no']; ?>)">
                      Revoke
                    </button>
                  <?php elseif ($row['status'] === 'READY_UNIQUE' && $firstCand): ?>
                    <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2"
                            onclick="openSingleAuthModal(<?php echo $row['category_id']; ?>, <?php echo $row['stage_no']; ?>, <?php echo (int)$firstCand['id']; ?>, '<?php echo e(addslashes($row['category_name'])); ?>', '<?php echo e($row['required_role']); ?>', '<?php echo e(addslashes($firstCand['officer_name'])); ?>', '<?php echo e($firstCand['designation']); ?>', <?php echo $row['policy_approved'] ? 'true' : 'false'; ?>)">
                      Authorize
                    </button>
                  <?php elseif (!$row['policy_approved']): ?>
                    <a href="<?php echo e(url('employee/pdf-policy-review.php?category_id=' . $row['category_id'])); ?>" class="btn btn-sm btn-outline-secondary py-0 px-2" title="Review policy first">
                      Approve Policy
                    </a>
                  <?php endif; ?>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Bulk Authorize Modal -->
  <div class="modal fade" id="bulkAuthModal" tabindex="-1" aria-labelledby="bulkAuthModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="bulkAuthModalLabel"><i class="bi bi-shield-check me-1"></i> Preview &amp; Authorize Officer Mappings</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="mb-2">Review the proposed officer-stage assignments below. Every assignment will be validated against active status, department, category, and designation.</p>

          <div class="table-responsive mb-3 border rounded" style="max-height: 250px;">
            <table class="table table-sm table-striped mb-0 small">
              <thead class="table-light sticky-top">
                <tr>
                  <th>Category</th>
                  <th>Stage</th>
                  <th>Required Role</th>
                  <th>Candidate Officer</th>
                  <th>Designation</th>
                </tr>
              </thead>
              <tbody id="bulkPreviewBody">
                <!-- Injected via JavaScript -->
              </tbody>
            </table>
          </div>

          <div class="mb-3">
            <label for="bulk_auth_reference" class="form-label fw-semibold">Official Authorization Document Reference <span class="text-danger">*</span></label>
            <input type="text" id="bulk_auth_reference" name="approval_reference" class="form-control" required minlength="8" maxlength="255" placeholder="e.g., MUNICIPAL-ORDER-ESTAB-2026-88">
            <div class="form-text">Must be a genuine administrative order or council approval reference.</div>
          </div>

          <div class="alert alert-warning small mb-2">Simulation approvals are disabled in this official registry. Use isolated synthetic test fixtures for simulation.</div>

          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="confirm_bulk_authority" name="confirm_bulk_authority" value="1" required>
            <label class="form-check-label fw-semibold small" for="confirm_bulk_authority">
              I have verified the administrative authorization, ward assignment, and designation for all listed officers.
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Authorize Selected Officers</button>
        </div>
      </div>
    </div>
  </div>
</form>

<!-- Single Authorize Modal -->
<div class="modal fade" id="singleAuthModal" tabindex="-1" aria-labelledby="singleAuthModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="action" value="authorize">
      <input type="hidden" name="category_id" id="single_cat_id">
      <input type="hidden" name="stage_no" id="single_stage_no">
      <input type="hidden" name="officer_id" id="single_officer_id">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="singleAuthModalLabel"><i class="bi bi-person-check me-1"></i> Authorize Officer for Stage</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3 bg-light p-3 rounded">
          <div class="mb-1"><strong>Category:</strong> <span id="modal_cat_name"></span></div>
          <div class="mb-1"><strong>Stage:</strong> Stage <span id="modal_stage_no"></span> &mdash; Required Role: <code id="modal_role"></code></div>
          <div><strong>Officer:</strong> <span id="modal_officer_name" class="fw-bold"></span> (<span id="modal_officer_desig" class="badge bg-secondary"></span>)</div>
        </div>

        <div id="policy_warning_alert" class="alert alert-warning d-none py-2 small">
          <i class="bi bi-exclamation-triangle me-1"></i> Warning: Category PDF policy is not yet approved. Approve policy first.
        </div>

        <div class="mb-3">
          <label for="single_approval_reference" class="form-label fw-semibold">Official Authorization Document Reference <span class="text-danger">*</span></label>
          <input type="text" id="single_approval_reference" name="approval_reference" class="form-control" required minlength="8" maxlength="255" placeholder="e.g., MUNICIPAL-ORDER-2026-001">
        </div>

        <div class="alert alert-warning small mb-2">Simulation approvals are disabled in this official registry. Use isolated synthetic test fixtures for simulation.</div>

        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" id="confirm_authority_single" name="confirm_authority" value="1" required>
          <label class="form-check-label fw-semibold small" for="confirm_authority_single">
            I confirm verified administrative authority and ward responsibility for this officer.
          </label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Save Authorization</button>
      </div>
    </form>
  </div>
</div>

<!-- Revoke Modal -->
<div class="modal fade" id="revokeModal" tabindex="-1" aria-labelledby="revokeModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="action" value="revoke">
      <input type="hidden" name="approval_id" id="revoke_approval_id">

      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="revokeModalLabel"><i class="bi bi-x-circle me-1"></i> Revoke Officer Authorization</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>You are revoking stage authorization for <strong id="revoke_officer_name"></strong>. This officer will no longer be selected for new allocations or escalations.</p>
        <div class="mb-3">
          <label for="revoke_ref_input" class="form-label fw-semibold">Revocation Document Reference <span class="text-danger">*</span></label>
          <input type="text" id="revoke_ref_input" name="approval_reference" class="form-control" required minlength="8" maxlength="255" placeholder="e.g., MUNICIPAL-REVOKE-ORDER-2026-001">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i> Revoke Authorization</button>
      </div>
    </form>
  </div>
</div>

<script>
function toggleSelectReady(select) {
  document.querySelectorAll('.ready-checkbox').forEach(cb => cb.checked = select);
}

function prepareBulkModal() {
  const selected = document.querySelectorAll('.ready-checkbox:checked');
  if (selected.length === 0) {
    alert('Please select at least one candidate officer to authorize.');
    return;
  }
  const tbody = document.getElementById('bulkPreviewBody');
  tbody.innerHTML = '';
  selected.forEach(cb => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${cb.dataset.catName}</td>
      <td class="text-center">Stage ${cb.dataset.stage}</td>
      <td><code>${cb.dataset.role}</code></td>
      <td class="fw-semibold">${cb.dataset.officer}</td>
      <td><span class="badge bg-secondary">${cb.dataset.desig}</span></td>
    `;
    tbody.appendChild(tr);
  });
  const modal = new bootstrap.Modal(document.getElementById('bulkAuthModal'));
  modal.show();
}

function openSingleAuthModal(catId, stageNo, officerId, catName, role, officerName, desig, policyApproved) {
  document.getElementById('single_cat_id').value = catId;
  document.getElementById('single_stage_no').value = stageNo;
  document.getElementById('single_officer_id').value = officerId;
  document.getElementById('modal_cat_name').innerText = catName + ' (Cat #' + catId + ')';
  document.getElementById('modal_stage_no').innerText = stageNo;
  document.getElementById('modal_role').innerText = role;
  document.getElementById('modal_officer_name').innerText = officerName + ' (#' + officerId + ')';
  document.getElementById('modal_officer_desig').innerText = desig;

  const alertEl = document.getElementById('policy_warning_alert');
  if (!policyApproved) {
    alertEl.classList.remove('d-none');
  } else {
    alertEl.classList.add('d-none');
  }

  const modal = new bootstrap.Modal(document.getElementById('singleAuthModal'));
  modal.show();
}

function openRevokeModal(approvalId, officerName, catId, stageNo) {
  document.getElementById('revoke_approval_id').value = approvalId;
  document.getElementById('revoke_officer_name').innerText = officerName;
  const modal = new bootstrap.Modal(document.getElementById('revokeModal'));
  modal.show();
}
</script>

<?php include __DIR__ . '/../includes/employee-footer.php'; ?>
