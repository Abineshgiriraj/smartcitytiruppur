<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/complaint-routing-engine.php';

require_login();
require_admin();

$pageTitle = 'Complaint Routing Analysis & Reassignment';
$activePage = 'routing-analysis';

$pdo = db();
$engine = new ComplaintRoutingEngine($pdo);

// Filters
$departmentId = (int)($_GET['department_id'] ?? 0);
$categoryId = (int)($_GET['category_id'] ?? 0);
$wardId = (int)($_GET['ward_id'] ?? 0);
$statusFilter = trim($_GET['status'] ?? '');
$typeFilter = trim($_GET['type'] ?? 'all'); // 'all', 'unassigned', 'inactive_officer', 'active_assigned'
$evidenceFilter = trim($_GET['evidence'] ?? '');
$search = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 25;
$offset = ($page - 1) * $perPage;

// Action handling: Dry Run / Generate Plans / Batch Apply (Safe Transactional Execution)
$flashMessage = null;
$flashType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'generate_proposals') {
        // Generate proposals for all non-closed complaints that are unassigned or assigned to inactive officers
        $openComplaintsStmt = $pdo->query("
            SELECT c.* FROM complaints c
            LEFT JOIN officers o ON o.id = c.assigned_officer_id
            WHERE c.is_deleted = 0 
              AND c.status NOT IN ('Closed', 'Resolved', 'Rejected')
              AND (c.assigned_officer_id IS NULL OR c.assigned_officer_id = 0 OR o.is_active = 0)
        ");
        $targets = $openComplaintsStmt->fetchAll(PDO::FETCH_ASSOC);

        $planInsertSql = "
            INSERT INTO complaint_reassignment_plans
            (complaint_id, current_status, current_officer_id, current_officer_active, proposed_officer_id, proposed_stage_no, calculated_due_at, reassignment_reason, evidence_source, plan_status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'proposed')
            ON DUPLICATE KEY UPDATE
                proposed_officer_id = VALUES(proposed_officer_id),
                reassignment_reason = VALUES(reassignment_reason),
                evidence_source = VALUES(evidence_source),
                plan_status = IF(plan_status = 'applied', 'applied', 'proposed')
        ";
        $insertStmt = $pdo->prepare($planInsertSql);

        $generated = 0;
        foreach ($targets as $c) {
            $plan = $engine->proposeReassignmentForOpenComplaint($c);
            if ($plan) {
                $insertStmt->execute([
                    $plan['complaint_id'],
                    $plan['current_status'],
                    $plan['current_officer_id'],
                    $plan['current_officer_active'],
                    $plan['proposed_officer_id'],
                    $plan['proposed_stage_no'],
                    $plan['calculated_due_at'],
                    $plan['reassignment_reason'],
                    $plan['evidence_source']
                ]);
                $generated++;
            }
        }
        $flashMessage = "Generated $generated reassignment proposals successfully.";
    } elseif ($action === 'reconstruct_history_batch') {
        $count = $engine->runReconstructionBatch(1000, 0);
        $flashMessage = "Reconstructed theoretical routing for $count complaints.";
    }
}

// Global Counts
$totalComplaints = (int)$pdo->query("SELECT COUNT(*) FROM complaints WHERE is_deleted = 0")->fetchColumn();
$closedComplaints = (int)$pdo->query("SELECT COUNT(*) FROM complaints WHERE is_deleted = 0 AND status IN ('Closed', 'Resolved')")->fetchColumn();
$openComplaints = (int)$pdo->query("SELECT COUNT(*) FROM complaints WHERE is_deleted = 0 AND status NOT IN ('Closed', 'Resolved')")->fetchColumn();
$unassignedOpen = (int)$pdo->query("SELECT COUNT(*) FROM complaints WHERE is_deleted = 0 AND status NOT IN ('Closed', 'Resolved') AND (assigned_officer_id IS NULL OR assigned_officer_id = 0)")->fetchColumn();
$inactiveAssignedOpen = (int)$pdo->query("SELECT COUNT(*) FROM complaints c JOIN officers o ON o.id = c.assigned_officer_id WHERE c.is_deleted = 0 AND c.status NOT IN ('Closed', 'Resolved') AND o.is_active = 0")->fetchColumn();
$activeAssignedOpen = (int)$pdo->query("SELECT COUNT(*) FROM complaints c JOIN officers o ON o.id = c.assigned_officer_id WHERE c.is_deleted = 0 AND c.status NOT IN ('Closed', 'Resolved') AND o.is_active = 1")->fetchColumn();

$plansProposedCount = (int)$pdo->query("SELECT COUNT(*) FROM complaint_reassignment_plans WHERE plan_status = 'proposed'")->fetchColumn();
$plansAppliedCount = (int)$pdo->query("SELECT COUNT(*) FROM complaint_reassignment_plans WHERE plan_status = 'applied'")->fetchColumn();
$calculatedRoutesCount = (int)$pdo->query("SELECT COUNT(*) FROM complaint_calculated_routing")->fetchColumn();

// Fetch filter options
$departments = $pdo->query("SELECT id, department_name FROM departments WHERE is_active = 1 ORDER BY department_name")->fetchAll(PDO::FETCH_ASSOC);
$wards = $pdo->query("SELECT id, ward_no FROM wards WHERE is_active = 1 ORDER BY ward_no ASC")->fetchAll(PDO::FETCH_ASSOC);

// Build search query for complaint plans / open complaints
$where = ["c.is_deleted = 0"];
$params = [];

if ($statusFilter !== '') {
    $where[] = "c.status = ?";
    $params[] = $statusFilter;
} else {
    // Default show open complaints
    $where[] = "c.status NOT IN ('Closed', 'Resolved', 'Rejected')";
}

if ($departmentId > 0) {
    $where[] = "c.department_id = ?";
    $params[] = $departmentId;
}
if ($wardId > 0) {
    $where[] = "c.ward_id = ?";
    $params[] = $wardId;
}
if ($search !== '') {
    $where[] = "(c.reference_no LIKE ? OR c.citizen_name LIKE ? OR c.location_address LIKE ?)";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if ($typeFilter === 'unassigned') {
    $where[] = "(c.assigned_officer_id IS NULL OR c.assigned_officer_id = 0)";
} elseif ($typeFilter === 'inactive_officer') {
    $where[] = "EXISTS (SELECT 1 FROM officers o WHERE o.id = c.assigned_officer_id AND o.is_active = 0)";
} elseif ($typeFilter === 'active_assigned') {
    $where[] = "EXISTS (SELECT 1 FROM officers o WHERE o.id = c.assigned_officer_id AND o.is_active = 1)";
}

$whereSql = implode(' AND ', $where);

// Total matching
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM complaints c WHERE $whereSql");
$countStmt->execute($params);
$totalMatching = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalMatching / $perPage));

// Main listing query
$listSql = "
    SELECT c.id, c.reference_no, c.citizen_name, c.mobile_no, c.created_at, c.status, c.due_at,
           c.department_id, d.department_name,
           c.category_id, cc.category_name,
           c.ward_id, w.ward_no, c.zone_id,
           c.assigned_officer_id,
           curr_o.officer_name AS current_officer_name,
           curr_o.designation AS current_officer_designation,
           curr_o.is_active AS current_officer_active,
           crp.proposed_officer_id,
           prop_o.officer_name AS proposed_officer_name,
           prop_o.designation AS proposed_officer_designation,
           crp.reassignment_reason,
           crp.evidence_source,
           crp.plan_status
    FROM complaints c
    LEFT JOIN departments d ON d.id = c.department_id
    LEFT JOIN complaint_categories cc ON cc.id = c.category_id
    LEFT JOIN wards w ON w.id = c.ward_id
    LEFT JOIN officers curr_o ON curr_o.id = c.assigned_officer_id
    LEFT JOIN complaint_reassignment_plans crp ON crp.complaint_id = c.id
    LEFT JOIN officers prop_o ON prop_o.id = crp.proposed_officer_id
    WHERE $whereSql
    ORDER BY c.created_at ASC, c.id ASC
    LIMIT $perPage OFFSET $offset
";
$listStmt = $pdo->prepare($listSql);
$listStmt->execute($params);
$complaintsList = $listStmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/employee-header.php';
?>

<div class="container-fluid px-4 py-4">
    <!-- Header banner -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">
                <i class="bi bi-diagram-3-fill text-primary me-2"></i>Complaint Routing Analysis & Reassignment
            </h1>
            <p class="text-muted mb-0">Reconstruct historical routing, preview intelligent officer allocations, and manage 6-stage escalations.</p>
        </div>
        <div class="d-flex gap-2">
            <form method="post" class="d-inline">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="generate_proposals">
                <button type="submit" class="btn btn-outline-primary" onclick="return confirm('Generate reassignment plans for all pending unassigned & inactive officer cases?');">
                    <i class="bi bi-magic me-1"></i> Generate Reassignment Plans
                </button>
            </form>
            <form method="post" class="d-inline">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="reconstruct_history_batch">
                <button type="submit" class="btn btn-primary" onclick="return confirm('Run theoretical routing reconstruction batch?');">
                    <i class="bi bi-arrow-repeat me-1"></i> Reconstruct Historical Routing
                </button>
            </form>
        </div>
    </div>

    <?php if ($flashMessage): ?>
        <div class="alert alert-<?php echo e($flashType); ?> alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?php echo e($flashMessage); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="small text-muted fw-semibold">TOTAL COMPLAINTS</div>
                        <div class="fs-4 fw-bold text-dark"><?php echo number_format($totalComplaints); ?></div>
                    </div>
                    <div class="bg-light-primary p-3 rounded-circle text-primary"><i class="bi bi-collection fs-4"></i></div>
                </div>
                <div class="small text-muted mt-2">100% database complaints</div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="small text-muted fw-semibold">CLOSED / RESOLVED</div>
                        <div class="fs-4 fw-bold text-success"><?php echo number_format($closedComplaints); ?></div>
                    </div>
                    <div class="bg-light-success p-3 rounded-circle text-success"><i class="bi bi-check-all fs-4"></i></div>
                </div>
                <div class="small text-muted mt-2">Historical preserved</div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="small text-muted fw-semibold">OPEN / PENDING</div>
                        <div class="fs-4 fw-bold text-warning"><?php echo number_format($openComplaints); ?></div>
                    </div>
                    <div class="bg-light-warning p-3 rounded-circle text-warning"><i class="bi bi-hourglass-split fs-4"></i></div>
                </div>
                <div class="small text-muted mt-2">Actionable backlog</div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="small text-muted fw-semibold">UNASSIGNED OPEN</div>
                        <div class="fs-4 fw-bold text-danger"><?php echo number_format($unassignedOpen); ?></div>
                    </div>
                    <div class="bg-light-danger p-3 rounded-circle text-danger"><i class="bi bi-person-x fs-4"></i></div>
                </div>
                <div class="small text-muted mt-2">Needs allocation</div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="small text-muted fw-semibold">INACTIVE OFFICER OPEN</div>
                        <div class="fs-4 fw-bold text-danger"><?php echo number_format($inactiveAssignedOpen); ?></div>
                    </div>
                    <div class="bg-light-danger p-3 rounded-circle text-danger"><i class="bi bi-shield-slash fs-4"></i></div>
                </div>
                <div class="small text-muted mt-2">Needs active replacement</div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="small text-muted fw-semibold">PROPOSED PLANS</div>
                        <div class="fs-4 fw-bold text-info"><?php echo number_format($plansProposedCount); ?></div>
                    </div>
                    <div class="bg-light-info p-3 rounded-circle text-info"><i class="bi bi-clipboard-check fs-4"></i></div>
                </div>
                <div class="small text-muted mt-2"><?php echo $plansAppliedCount; ?> applied</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="get" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">View Scope</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="all" <?php echo $typeFilter === 'all' ? 'selected' : ''; ?>>All Open Complaints</option>
                        <option value="unassigned" <?php echo $typeFilter === 'unassigned' ? 'selected' : ''; ?>>Unassigned Only</option>
                        <option value="inactive_officer" <?php echo $typeFilter === 'inactive_officer' ? 'selected' : ''; ?>>Inactive Officers Only</option>
                        <option value="active_assigned" <?php echo $typeFilter === 'active_assigned' ? 'selected' : ''; ?>>Active Officers Only</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Department</label>
                    <select name="department_id" class="form-select form-select-sm">
                        <option value="0">All Departments</option>
                        <?php foreach ($departments as $d): ?>
                            <option value="<?php echo $d['id']; ?>" <?php echo $departmentId === (int)$d['id'] ? 'selected' : ''; ?>><?php echo e($d['department_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Ward</label>
                    <select name="ward_id" class="form-select form-select-sm">
                        <option value="0">All Wards</option>
                        <?php foreach ($wards as $w): ?>
                            <option value="<?php echo $w['id']; ?>" <?php echo $wardId === (int)$w['id'] ? 'selected' : ''; ?>>Ward <?php echo e($w['ward_no']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Search Reference / Citizen</label>
                    <input type="text" name="q" class="form-control form-control-sm" placeholder="e.g. 46685 or Citizen Name" value="<?php echo e($search); ?>">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm px-3"><i class="bi bi-filter me-1"></i> Filter</button>
                    <a href="routing-analysis.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Complaints Analysis Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold mb-0 text-dark">
                Complaints & Proposed Officer Allocations 
                <span class="badge bg-secondary ms-2"><?php echo number_format($totalMatching); ?> matching</span>
            </h5>
            <div class="small text-muted">Showing page <?php echo $page; ?> of <?php echo $totalPages; ?></div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Reference</th>
                        <th>Reg Date</th>
                        <th>Department & Category</th>
                        <th>Ward / Zone</th>
                        <th>Current Officer</th>
                        <th>Proposed Officer (Dry Run)</th>
                        <th>Source / Evidence</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($complaintsList)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">No complaints match the selected filter.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($complaintsList as $row): ?>
                            <tr>
                                <td>
                                    <span class="fw-bold text-primary"><?php echo e($row['reference_no']); ?></span>
                                    <div class="small text-muted">ID: #<?php echo $row['id']; ?></div>
                                </td>
                                <td>
                                    <div class="small text-dark"><?php echo date('d-m-Y', strtotime($row['created_at'])); ?></div>
                                    <div class="small text-muted"><?php echo date('H:i', strtotime($row['created_at'])); ?></div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?php echo e($row['department_name'] ?? 'N/A'); ?></div>
                                    <div class="small text-muted"><?php echo e($row['category_name'] ?? 'N/A'); ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">Ward <?php echo e($row['ward_no'] ?? 'N/A'); ?></span>
                                    <?php if ($row['zone_id']): ?>
                                        <span class="badge bg-light text-dark border">Zone <?php echo e($row['zone_id']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['assigned_officer_id']): ?>
                                        <div class="fw-semibold text-dark">
                                            <?php echo e($row['current_officer_name'] ?: 'Officer #' . $row['assigned_officer_id']); ?>
                                        </div>
                                        <div class="small">
                                            <?php if ($row['current_officer_active']): ?>
                                                <span class="badge bg-success-subtle text-success">Active Officer</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger">Inactive Profile</span>
                                            <?php endif; ?>
                                            <span class="text-muted ms-1"><?php echo e($row['current_officer_designation'] ?? ''); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning"><i class="bi bi-exclamation-triangle me-1"></i>Unassigned</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($row['proposed_officer_id'])): ?>
                                        <div class="fw-bold text-success">
                                            <i class="bi bi-check-circle-fill me-1"></i>
                                            <?php echo e($row['proposed_officer_name'] ?: 'Officer #' . $row['proposed_officer_id']); ?>
                                        </div>
                                        <div class="small text-muted"><?php echo e($row['proposed_officer_designation'] ?? ''); ?></div>
                                    <?php elseif ($row['current_officer_active']): ?>
                                        <span class="text-muted small"><i class="bi bi-check-lg me-1"></i>Retains current active officer</span>
                                    <?php else: ?>
                                        <span class="text-muted small">Pending calculation</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($row['evidence_source'])): ?>
                                        <span class="badge bg-info-subtle text-info fw-semibold"><?php echo e($row['evidence_source']); ?></span>
                                        <div class="small text-muted" style="max-width: 250px;"><?php echo e($row['reassignment_reason']); ?></div>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo status_badge_class($row['status']); ?>"><?php echo e($row['status']); ?></span>
                                </td>
                                <td>
                                    <a href="complaint-view.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="card-footer bg-white py-3">
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm justify-content-center mb-0">
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">Previous</a>
                        </li>
                        <li class="page-item active"><span class="page-link"><?php echo $page; ?></span></li>
                        <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/employee-footer.php'; ?>
