<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/pdf-escalation-policy.php';

$user = require_roles(['Super Admin', 'Admin']);
require_permission('masters.view');
$canManage = can_access_permission($user, 'masters.manage');
$pdo = db();
$pageTitle = 'PDF Escalation Policy Review';

// Handle POST actions (Individual Approve, Bulk Approve, Revoke)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$canManage) {
        flash('danger', 'You do not have permission to approve or modify policies.');
        redirect('employee/pdf-policy-review.php');
    }
    verify_csrf();
    $action = trim((string)($_POST['action'] ?? 'approve'));
    $reference = trim((string)($_POST['approval_reference'] ?? ''));
    $isSimulation = !empty($_POST['is_simulation']);

    if ($isSimulation && !str_starts_with($reference, 'SIMULATION-LOCAL-TEST-')) {
        $reference = 'SIMULATION-LOCAL-TEST-' . ($reference !== '' ? $reference : bin2hex(random_bytes(4)));
    }

    try {
        if ($action === 'approve') {
            $categoryId = (int)($_POST['category_id'] ?? 0);
            if ($categoryId < 1 || mb_strlen($reference) < 8 || empty($_POST['confirm_policy'])) {
                throw new RuntimeException('Category ID, approval reference (min 8 chars), and confirmation are required.');
            }

            $pdo->beginTransaction();
            $lock = $pdo->prepare('SELECT * FROM pdf_escalation_policies WHERE category_id = ? FOR UPDATE');
            $lock->execute([$categoryId]);
            $policy = $lock->fetch(PDO::FETCH_ASSOC);

            if (!$policy) throw new RuntimeException('PDF policy does not exist for this category.');
            if ((int)$policy['is_approved'] === 1) throw new RuntimeException('Policy is already approved.');
            if ($policy['pdf_sha256'] !== '03a496809403a2de6a597a844a36ffc979de47657541342924628c8d33d70c4b') {
                throw new RuntimeException('Source PDF fingerprint differs from verified provenance.');
            }

            $stages = $pdo->prepare('SELECT stage_no, role_label, period_days FROM pdf_escalation_policy_stages WHERE category_id = ? ORDER BY stage_no');
            $stages->execute([$categoryId]);
            $rows = $stages->fetchAll(PDO::FETCH_ASSOC);

            if (count($rows) !== (int)$policy['stage_count']) throw new RuntimeException('Incomplete PDF stages.');
            $sum = 0;
            foreach ($rows as $offset => $stage) {
                if ((int)$stage['stage_no'] !== $offset + 1 || (int)$stage['period_days'] <= 0) {
                    throw new RuntimeException('Category contains a 0-day or invalid stage in the PDF gazette. One-click approval is disabled.');
                }
                $sum += (int)$stage['period_days'];
            }
            if ($sum !== (int)$policy['total_days']) throw new RuntimeException('PDF SLA totals do not reconcile with sum of stage days.');

            $save = $pdo->prepare('UPDATE pdf_escalation_policies
                SET is_approved = 1, approval_reference = ?, approved_by = ?, approved_at = NOW()
                WHERE category_id = ? AND is_approved = 0');
            $save->execute([$reference, (int)$user['id'], $categoryId]);

            if ($save->rowCount() !== 1) throw new RuntimeException('Policy status was modified concurrently.');
            $pdo->commit();

            audit_log((int)$user['id'], 'pdf_escalation_policy_approved', 'complaint_category', $categoryId, [
                'reference' => $reference,
                'is_simulation' => $isSimulation,
                'source_row' => $policy['source_row'],
            ]);

            flash('success', ($isSimulation ? '[SIMULATION MODE] ' : '') . 'Reviewed PDF stage policy approved. Officer assignments still require separate verified approvals.');
            redirect('employee/pdf-policy-review.php?category_id=' . $categoryId);

        } elseif ($action === 'bulk_approve') {
            $selectedCats = array_filter(array_map('intval', (array)($_POST['category_ids'] ?? [])), fn($id) => $id > 0);
            if (!$selectedCats || mb_strlen($reference) < 8 || empty($_POST['confirm_bulk_policy'])) {
                throw new RuntimeException('Select at least one category, enter an approval reference (min 8 chars), and check the verification confirmation.');
            }

            $pdo->beginTransaction();
            $approvedCount = 0;
            $skippedCats = [];

            foreach ($selectedCats as $catId) {
                $lock = $pdo->prepare('SELECT * FROM pdf_escalation_policies WHERE category_id = ? FOR UPDATE');
                $lock->execute([$catId]);
                $policy = $lock->fetch(PDO::FETCH_ASSOC);

                if (!$policy || (int)$policy['is_approved'] === 1) {
                    $skippedCats[] = $catId;
                    continue;
                }
                if ($policy['pdf_sha256'] !== '03a496809403a2de6a597a844a36ffc979de47657541342924628c8d33d70c4b') {
                    $skippedCats[] = $catId;
                    continue;
                }

                $stages = $pdo->prepare('SELECT stage_no, role_label, period_days FROM pdf_escalation_policy_stages WHERE category_id = ? ORDER BY stage_no');
                $stages->execute([$catId]);
                $rows = $stages->fetchAll(PDO::FETCH_ASSOC);

                if (count($rows) !== (int)$policy['stage_count']) {
                    $skippedCats[] = $catId;
                    continue;
                }

                $hasZeroDay = false;
                $sum = 0;
                foreach ($rows as $offset => $stage) {
                    if ((int)$stage['stage_no'] !== $offset + 1 || (int)$stage['period_days'] <= 0) {
                        $hasZeroDay = true;
                        break;
                    }
                    $sum += (int)$stage['period_days'];
                }
                if ($hasZeroDay || $sum !== (int)$policy['total_days']) {
                    $skippedCats[] = $catId;
                    continue;
                }

                $save = $pdo->prepare('UPDATE pdf_escalation_policies
                    SET is_approved = 1, approval_reference = ?, approved_by = ?, approved_at = NOW()
                    WHERE category_id = ? AND is_approved = 0');
                $save->execute([$reference, (int)$user['id'], $catId]);
                if ($save->rowCount() === 1) {
                    $approvedCount++;
                    audit_log((int)$user['id'], 'pdf_escalation_policy_approved', 'complaint_category', $catId, [
                        'reference' => $reference,
                        'is_simulation' => $isSimulation,
                        'batch' => true,
                    ]);
                }
            }

            $pdo->commit();
            $msg = ($isSimulation ? '[SIMULATION MODE] ' : '') . "Batch review complete: {$approvedCount} policies approved.";
            if ($skippedCats) {
                $msg .= ' (' . count($skippedCats) . ' categories were skipped due to zero-day stages or prior approval).';
            }
            flash('success', $msg);
            redirect('employee/pdf-policy-review.php');

        } elseif ($action === 'revoke') {
            $categoryId = (int)($_POST['category_id'] ?? 0);
            if ($categoryId < 1 || mb_strlen($reference) < 8) {
                throw new RuntimeException('Category ID and revocation document reference are required.');
            }

            $pdo->beginTransaction();
            $revoke = $pdo->prepare('UPDATE pdf_escalation_policies
                SET is_approved = 0, approval_reference = NULL, approved_by = NULL, approved_at = NULL
                WHERE category_id = ? AND is_approved = 1');
            $revoke->execute([$categoryId]);

            if ($revoke->rowCount() !== 1) throw new RuntimeException('Policy is not currently approved.');
            $pdo->commit();

            audit_log((int)$user['id'], 'pdf_escalation_policy_revoked', 'complaint_category', $categoryId, [
                'reference' => $reference,
            ]);

            flash('success', 'Policy approval revoked. Category returned to draft state.');
            redirect('employee/pdf-policy-review.php?category_id=' . $categoryId);
        } else {
            throw new RuntimeException('Unsupported action.');
        }
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        flash('danger', APP_ENV === 'local' ? $error->getMessage() : 'Policy action could not be completed.');
        redirect('employee/pdf-policy-review.php' . ($categoryId > 0 ? '?category_id=' . $categoryId : ''));
    }
}

// Read queries
$filterDept = (int)($_GET['dept_id'] ?? 0);
$filterStatus = trim((string)($_GET['status'] ?? ''));
$filterSearch = trim((string)($_GET['q'] ?? ''));
$selectedCatId = (int)($_GET['category_id'] ?? 0);

// Load all departments for filter
$departments = $pdo->query('SELECT id, department_name FROM departments ORDER BY department_name')->fetchAll(PDO::FETCH_ASSOC);

// Policy statistics
$stats = $pdo->query(
    "SELECT
        COUNT(*) as total_count,
        SUM(is_approved = 1) as approved_count,
        SUM(is_approved = 0) as draft_count,
        (SELECT COUNT(DISTINCT category_id) FROM pdf_escalation_policy_stages WHERE period_days = 0) as zero_day_count
     FROM pdf_escalation_policies"
)->fetch(PDO::FETCH_ASSOC);

// Query filtered policies
$where = ['1=1'];
$params = [];

if ($filterDept > 0) {
    $where[] = 'cc.department_id = ?';
    $params[] = $filterDept;
}
if ($filterStatus === 'approved') {
    $where[] = 'p.is_approved = 1';
} elseif ($filterStatus === 'draft') {
    $where[] = 'p.is_approved = 0';
} elseif ($filterStatus === 'zeroday') {
    $where[] = 'EXISTS(SELECT 1 FROM pdf_escalation_policy_stages z WHERE z.category_id = p.category_id AND z.period_days = 0)';
}
if ($filterSearch !== '') {
    $where[] = '(cc.category_name LIKE ? OR d.department_name LIKE ?)';
    $params[] = '%' . $filterSearch . '%';
    $params[] = '%' . $filterSearch . '%';
}

$whereSql = implode(' AND ', $where);
$stmt = $pdo->prepare(
    "SELECT p.category_id, p.source_row, p.total_days, p.stage_count, p.is_approved,
            p.approval_reference, p.approved_at, u.full_name as approver_name,
            cc.category_name, cc.department_id, d.department_name,
            EXISTS(SELECT 1 FROM pdf_escalation_policy_stages z WHERE z.category_id = p.category_id AND z.period_days = 0) as has_zero_day
     FROM pdf_escalation_policies p
     JOIN complaint_categories cc ON cc.id = p.category_id
     JOIN departments d ON d.id = cc.department_id
     LEFT JOIN users u ON u.id = p.approved_by
     WHERE {$whereSql}
     ORDER BY d.department_name, cc.category_name"
);
$stmt->execute($params);
$policies = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Selected category details
$selected = null;
$selectedStages = [];
if ($selectedCatId > 0) {
    foreach ($policies as $p) {
        if ((int)$p['category_id'] === $selectedCatId) {
            $selected = $p;
            break;
        }
    }
    if (!$selected) {
        $singleStmt = $pdo->prepare(
            "SELECT p.category_id, p.source_row, p.total_days, p.stage_count, p.is_approved,
                    p.approval_reference, p.approved_at, u.full_name as approver_name,
                    cc.category_name, cc.department_id, d.department_name,
                    EXISTS(SELECT 1 FROM pdf_escalation_policy_stages z WHERE z.category_id = p.category_id AND z.period_days = 0) as has_zero_day
             FROM pdf_escalation_policies p
             JOIN complaint_categories cc ON cc.id = p.category_id
             JOIN departments d ON d.id = cc.department_id
             LEFT JOIN users u ON u.id = p.approved_by
             WHERE p.category_id = ?"
        );
        $singleStmt->execute([$selectedCatId]);
        $selected = $singleStmt->fetch(PDO::FETCH_ASSOC);
    }
    if ($selected) {
        $stageStmt = $pdo->prepare(
            "SELECT stage_no, role_label, period_days
             FROM pdf_escalation_policy_stages
             WHERE category_id = ?
             ORDER BY stage_no"
        );
        $stageStmt->execute([$selectedCatId]);
        $selectedStages = $stageStmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

include __DIR__ . '/../includes/employee-header.php';
?>

<div class="admin-heading mb-3">
  <div>
    <span class="admin-eyebrow">PDF Escalation Governance</span>
    <h1>PDF Escalation Policy Review & Bulk Verification</h1>
    <p class="text-muted mb-0">Review category-specific escalation hierarchies and SLA durations derived from <code>21.11.26.pdf</code>.</p>
  </div>
  <div class="d-flex gap-2 align-items-center">
    <a href="<?php echo e(url('employee/officer-readiness.php')); ?>" class="btn btn-outline-primary">
      <i class="bi bi-diagram-2 me-1"></i> Officer Readiness Dashboard
    </a>
    <a href="<?php echo e(url('employee/officer-approvals.php')); ?>" class="btn btn-outline-secondary">
      <i class="bi bi-person-check me-1"></i> Officer Approvals
    </a>
  </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card p-3 shadow-sm border-0">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <small class="text-muted text-uppercase fw-semibold">Total Policies</small>
          <h3 class="mb-0 fw-bold"><?php echo (int)($stats['total_count'] ?? 0); ?></h3>
        </div>
        <div class="bg-primary-subtle text-primary p-3 rounded-circle"><i class="bi bi-files fs-4"></i></div>
      </div>
      <small class="text-muted mt-2 d-block">100% matched to categories</small>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3 shadow-sm border-0">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <small class="text-muted text-uppercase fw-semibold">Approved</small>
          <h3 class="mb-0 fw-bold text-success"><?php echo (int)($stats['approved_count'] ?? 0); ?></h3>
        </div>
        <div class="bg-success-subtle text-success p-3 rounded-circle"><i class="bi bi-check2-circle fs-4"></i></div>
      </div>
      <small class="text-muted mt-2 d-block">Active for allocation</small>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3 shadow-sm border-0">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <small class="text-muted text-uppercase fw-semibold">Awaiting Review</small>
          <h3 class="mb-0 fw-bold text-warning"><?php echo (int)($stats['draft_count'] ?? 0); ?></h3>
        </div>
        <div class="bg-warning-subtle text-warning p-3 rounded-circle"><i class="bi bi-clock-history fs-4"></i></div>
      </div>
      <small class="text-muted mt-2 d-block">Draft policies pending sign-off</small>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3 shadow-sm border-0">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <small class="text-muted text-uppercase fw-semibold">Zero-Day Anomalies</small>
          <h3 class="mb-0 fw-bold text-danger"><?php echo (int)($stats['zero_day_count'] ?? 0); ?></h3>
        </div>
        <div class="bg-danger-subtle text-danger p-3 rounded-circle"><i class="bi bi-exclamation-triangle fs-4"></i></div>
      </div>
      <small class="text-muted mt-2 d-block">Quarantined (Cats #105, #137)</small>
    </div>
  </div>
</div>

<?php if ($selected): ?>
<!-- Selected Category Detail Drawer / Card -->
<div class="card shadow-sm border-primary mb-4">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <div>
      <span class="badge bg-light text-primary me-2">Category #<?php echo (int)$selected['category_id']; ?></span>
      <strong><?php echo e($selected['department_name']); ?> &mdash; <?php echo e($selected['category_name']); ?></strong>
    </div>
    <a href="<?php echo e(url('employee/pdf-policy-review.php')); ?>" class="btn btn-sm btn-outline-light">Close Detail</a>
  </div>
  <div class="card-body">
    <div class="row mb-3">
      <div class="col-md-3"><strong>PDF Source Row:</strong> #<?php echo (int)$selected['source_row']; ?></div>
      <div class="col-md-3"><strong>Total SLA Days:</strong> <?php echo (int)$selected['total_days']; ?> days</div>
      <div class="col-md-3"><strong>Escalation Levels:</strong> <?php echo (int)$selected['stage_count']; ?> stages</div>
      <div class="col-md-3">
        <strong>Status:</strong>
        <?php if ($selected['is_approved']): ?>
          <span class="badge bg-success">Approved</span>
          <small class="d-block text-muted">Ref: <?php echo e($selected['approval_reference']); ?> (<?php echo e($selected['approved_at']); ?>)</small>
        <?php elseif ($selected['has_zero_day']): ?>
          <span class="badge bg-danger">Quarantined (Zero-Day Stage)</span>
        <?php else: ?>
          <span class="badge bg-warning text-dark">Awaiting Review</span>
        <?php endif; ?>
      </div>
    </div>

    <h6 class="fw-bold mb-2">Escalation Stages Hierarchy:</h6>
    <div class="table-responsive mb-3">
      <table class="table table-bordered table-sm align-middle">
        <thead class="table-light">
          <tr>
            <th style="width: 80px;">Stage</th>
            <th>Required Role Label</th>
            <th style="width: 120px;">Stage SLA</th>
            <th>Stage Role Description & Match Rules</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($selectedStages as $st): ?>
            <tr class="<?php echo (int)$st['period_days'] === 0 ? 'table-danger' : ''; ?>">
              <td class="fw-bold text-center">Stage <?php echo (int)$st['stage_no']; ?></td>
              <td><code><?php echo e($st['role_label']); ?></code></td>
              <td>
                <?php if ((int)$st['period_days'] === 0): ?>
                  <strong class="text-danger">0 days (Anomaly)</strong>
                <?php else: ?>
                  <span class="badge bg-info text-dark"><?php echo (int)$st['period_days']; ?> day(s)</span>
                <?php endif; ?>
              </td>
              <td class="text-muted small">
                <?php
                  $sampleMatches = [];
                  if (strpos($st['role_label'], 'COMMR') !== false && strpos($st['role_label'], 'DEPUTY') === false) {
                      $sampleMatches[] = 'Matches: Commissioner';
                  } elseif (strpos($st['role_label'], 'DEPUTY') !== false) {
                      $sampleMatches[] = 'Matches: Deputy Commissioner';
                  } elseif (strpos($st['role_label'], 'AE') !== false) {
                      $sampleMatches[] = 'Matches: Assistant Engineer aliases';
                  } elseif (strpos($st['role_label'], 'AEE') !== false) {
                      $sampleMatches[] = 'Matches: Assistant Executive Engineer';
                  } elseif (strpos($st['role_label'], 'EE') !== false) {
                      $sampleMatches[] = 'Matches: Executive Engineer';
                  } elseif (strpos($st['role_label'], 'CE') !== false) {
                      $sampleMatches[] = 'Matches: City Engineer';
                  } else {
                      $sampleMatches[] = 'Matches exact or normalized municipal designation';
                  }
                  echo implode('; ', $sampleMatches);
                ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if ($canManage): ?>
      <?php if (!$selected['is_approved']): ?>
        <?php if ($selected['has_zero_day']): ?>
          <div class="alert alert-danger mb-0">
            <i class="bi bi-slash-circle me-1"></i>
            <strong>Policy Quarantined:</strong> This workflow defines a stage with 0 SLA days in <code>21.11.26.pdf</code>.
            Automatic one-click approval is disabled by system policy to avoid infinite escalation loops. An official municipal gazette amendment is required before activating this category.
          </div>
        <?php else: ?>
          <form method="post" class="border p-3 rounded bg-light">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="approve">
            <input type="hidden" name="category_id" value="<?php echo (int)$selected['category_id']; ?>">

            <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-shield-check me-1"></i> Approve Policy for Category #<?php echo (int)$selected['category_id']; ?></h6>

            <div class="mb-3">
              <label for="approval_reference" class="form-label fw-semibold">Official Policy Approval Document Reference <span class="text-danger">*</span></label>
              <input type="text" id="approval_reference" name="approval_reference" class="form-control" required minlength="8" maxlength="255" placeholder="e.g., MUNICIPAL-COUNCIL-RES-2026-001 or GO-TCMC-2026-45">
              <div class="form-text">Must reflect genuine municipal authorization or official gazette order.</div>
            </div>

            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="is_simulation_single" name="is_simulation" value="1">
              <label class="form-check-label text-muted" for="is_simulation_single">
                <strong>Local Functional Testing Simulation Mode:</strong> Check only for development testing without live municipal order. Automatically prefixes reference with <code>SIMULATION-LOCAL-TEST-</code>.
              </label>
            </div>

            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" id="confirm_policy_single" name="confirm_policy" value="1" required>
              <label class="form-check-label fw-semibold" for="confirm_policy_single">
                I have verified these specific stages, designations, and durations against municipal council regulations.
              </label>
            </div>

            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Approve Category Policy</button>
          </form>
        <?php endif; ?>
      <?php else: ?>
        <form method="post" class="border p-3 rounded bg-light" onsubmit="return confirm('Revoking this policy will stop automatic escalation for complaints in this category until re-approved. Continue?');">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="action" value="revoke">
          <input type="hidden" name="category_id" value="<?php echo (int)$selected['category_id']; ?>">

          <h6 class="fw-bold mb-2 text-danger"><i class="bi bi-x-circle me-1"></i> Revoke Policy Approval</h6>
          <div class="mb-2">
            <label for="revoke_reference" class="form-label fw-semibold">Revocation Document Reference <span class="text-danger">*</span></label>
            <input type="text" id="revoke_reference" name="approval_reference" class="form-control form-control-sm" required minlength="8" maxlength="255" placeholder="e.g., MUNICIPAL-REVOKE-ORDER-2026-001">
          </div>
          <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-arrow-counterclockwise me-1"></i> Revoke Approval to Draft</button>
        </form>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>

<!-- Filter & Search Controls -->
<div class="card shadow-sm border-0 mb-3">
  <div class="card-body">
    <form method="get" class="row g-2 align-items-center">
      <div class="col-md-4">
        <label for="dept_id" class="form-label small fw-semibold text-muted mb-1">Department</label>
        <select id="dept_id" name="dept_id" class="form-select form-select-sm" onchange="this.form.submit()">
          <option value="0">All Departments (<?php echo count($departments); ?>)</option>
          <?php foreach ($departments as $d): ?>
            <option value="<?php echo (int)$d['id']; ?>" <?php echo $filterDept === (int)$d['id'] ? 'selected' : ''; ?>>
              <?php echo e($d['department_name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-3">
        <label for="status" class="form-label small fw-semibold text-muted mb-1">Policy Status</label>
        <select id="status" name="status" class="form-select form-select-sm" onchange="this.form.submit()">
          <option value="">All Statuses</option>
          <option value="draft" <?php echo $filterStatus === 'draft' ? 'selected' : ''; ?>>Awaiting Review (Draft)</option>
          <option value="approved" <?php echo $filterStatus === 'approved' ? 'selected' : ''; ?>>Approved</option>
          <option value="zeroday" <?php echo $filterStatus === 'zeroday' ? 'selected' : ''; ?>>Zero-Day Quarantined</option>
        </select>
      </div>

      <div class="col-md-3">
        <label for="q" class="form-label small fw-semibold text-muted mb-1">Search Category</label>
        <input type="text" id="q" name="q" class="form-control form-control-sm" value="<?php echo e($filterSearch); ?>" placeholder="Search category...">
      </div>

      <div class="col-md-2 d-flex align-items-end gap-1 mt-auto">
        <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-search me-1"></i> Filter</button>
        <a href="<?php echo e(url('employee/pdf-policy-review.php')); ?>" class="btn btn-sm btn-outline-secondary" title="Reset Filters"><i class="bi bi-arrow-repeat"></i></a>
      </div>
    </form>
  </div>
</div>

<!-- Bulk Review & Policies Table Form -->
<form method="post" id="bulkPolicyForm">
  <?php echo csrf_field(); ?>
  <input type="hidden" name="action" value="bulk_approve">

  <div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
      <div>
        <h5 class="mb-0 fw-bold">Municipal Complaint Categories (<?php echo count($policies); ?> matching)</h5>
        <small class="text-muted">Select categories to approve in batch or click Inspect to view detailed stages.</small>
      </div>
      <?php if ($canManage): ?>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleSelectAll(true)">Select All</button>
          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleSelectAll(false)">Deselect All</button>
          <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#bulkApproveModal">
            <i class="bi bi-check-all me-1"></i> Bulk Approve Selected
          </button>
        </div>
      <?php endif; ?>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <?php if ($canManage): ?><th style="width: 40px;" class="text-center">#</th><?php endif; ?>
            <th style="width: 70px;">Cat ID</th>
            <th>Department</th>
            <th>Complaint Category</th>
            <th class="text-center">Stages</th>
            <th class="text-center">Total SLA</th>
            <th>Status</th>
            <th>Approval Reference</th>
            <th class="text-end" style="width: 120px;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$policies): ?>
            <tr><td colspan="<?php echo $canManage ? 9 : 8; ?>" class="text-center py-4 text-muted">No policies match your filter.</td></tr>
          <?php endif; ?>
          <?php foreach ($policies as $p): ?>
            <tr class="<?php echo $selectedCatId === (int)$p['category_id'] ? 'table-active' : ''; ?>">
              <?php if ($canManage): ?>
                <td class="text-center">
                  <?php if (!$p['is_approved'] && !$p['has_zero_day']): ?>
                    <input type="checkbox" name="category_ids[]" value="<?php echo (int)$p['category_id']; ?>" class="form-check-input cat-checkbox">
                  <?php else: ?>
                    <input type="checkbox" disabled class="form-check-input opacity-25">
                  <?php endif; ?>
                </td>
              <?php endif; ?>
              <td class="fw-bold">#<?php echo (int)$p['category_id']; ?></td>
              <td><span class="badge bg-light text-dark border"><?php echo e($p['department_name']); ?></span></td>
              <td class="fw-semibold"><?php echo e($p['category_name']); ?></td>
              <td class="text-center"><span class="badge bg-secondary"><?php echo (int)$p['stage_count']; ?> Levels</span></td>
              <td class="text-center"><span class="badge bg-info text-dark"><?php echo (int)$p['total_days']; ?> Days</span></td>
              <td>
                <?php if ($p['is_approved']): ?>
                  <span class="badge bg-success"><i class="bi bi-check2 me-1"></i> Approved</span>
                <?php elseif ($p['has_zero_day']): ?>
                  <span class="badge bg-danger" title="Contains 0-day stage"><i class="bi bi-exclamation-octagon me-1"></i> Quarantined</span>
                <?php else: ?>
                  <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i> Draft</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($p['is_approved']): ?>
                  <small class="text-muted d-block text-truncate" style="max-width: 180px;" title="<?php echo e($p['approval_reference']); ?>">
                    <?php echo e($p['approval_reference']); ?>
                  </small>
                <?php else: ?>
                  <span class="text-muted small">&mdash;</span>
                <?php endif; ?>
              </td>
              <td class="text-end">
                <a href="<?php echo e(url('employee/pdf-policy-review.php?category_id=' . (int)$p['category_id'])); ?>" class="btn btn-sm btn-outline-primary py-0 px-2">
                  Inspect
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Bulk Approve Modal -->
  <div class="modal fade" id="bulkApproveModal" tabindex="-1" aria-labelledby="bulkApproveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="bulkApproveModalLabel"><i class="bi bi-check-all me-1"></i> Batch Authorize Selected Policies</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="mb-3">You are about to authorize the selected category workflows derived from <code>21.11.26.pdf</code>.</p>
          <div class="alert alert-info py-2 small">
            <i class="bi bi-info-circle me-1"></i> Zero-day quarantined categories (Cats #105, #137) will be automatically skipped.
          </div>

          <div class="mb-3">
            <label for="bulk_approval_reference" class="form-label fw-semibold">Official Approval Document Reference <span class="text-danger">*</span></label>
            <input type="text" id="bulk_approval_reference" name="approval_reference" class="form-control" required minlength="8" maxlength="255" placeholder="e.g., MUNICIPAL-COUNCIL-RES-2026-BATCH-01">
            <div class="form-text">Reference number from the official gazette or municipal council resolution.</div>
          </div>

          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="bulk_is_simulation" name="is_simulation" value="1">
            <label class="form-check-label text-muted small" for="bulk_is_simulation">
              <strong>Local Functional Testing Simulation Mode:</strong> Check for local test runs without live council gazette. Prefixes reference with <code>SIMULATION-LOCAL-TEST-</code>.
            </label>
          </div>

          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="confirm_bulk_policy" name="confirm_bulk_policy" value="1" required>
            <label class="form-check-label fw-semibold small" for="confirm_bulk_policy">
              I confirm that these category hierarchies, SLA durations, and terminal stages are verified against municipal regulations.
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Commit Batch Authorization</button>
        </div>
      </div>
    </div>
  </div>
</form>

<script>
function toggleSelectAll(select) {
  document.querySelectorAll('.cat-checkbox').forEach(cb => cb.checked = select);
}
</script>

<?php include __DIR__ . '/../includes/employee-footer.php'; ?>
