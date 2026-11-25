<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

$auth = require_officer();
$user = $auth['user'];
$officer = $auth['officer'];
$officerId = (int)$officer['id'];

$pageTitle = 'My Assigned Complaints';

$currentStatus = trim((string)($_GET['status'] ?? ''));
$showPrevious = $currentStatus === 'PreviouslyHandled';
$showIncoming = $currentStatus === 'TransferredToMe';
$searchQuery = trim($_GET['q'] ?? '');
$fromDate = trim($_GET['from_date'] ?? '');
$toDate = trim($_GET['to_date'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

// Current assignments are controlled by complaints.assigned_officer_id.
// Historical rows are view-only and never part of an officer's active workload.
$conditions = ['c.is_deleted = 0'];
$params = [];
if ($showPrevious) {
    $conditions[] = "c.reference_no LIKE 'TCMC-2026-%'";
    $conditions[] = 'EXISTS (SELECT 1 FROM complaint_assignments old
        WHERE old.complaint_id=c.id AND old.officer_id=? AND old.is_current=0)';
    $conditions[] = '(c.assigned_officer_id IS NULL OR c.assigned_officer_id<>?)';
    array_push($params, $officerId, $officerId);
} else {
    $conditions[] = 'c.assigned_officer_id = ?';
    $params[] = $officerId;
    if ($showIncoming) {
        $conditions[] = "c.reference_no LIKE 'TCMC-2026-%'";
        $conditions[] = 'EXISTS (SELECT 1 FROM complaint_escalations ce
            WHERE ce.complaint_id=c.id AND ce.officer_id=? AND ce.level_no>1)';
        $params[] = $officerId;
    }
}

// Status filtering
if ($currentStatus === 'Overdue') {
    $conditions[] = 'c.status IN ("Assigned", "Acknowledged", "In Progress", "Reopened") AND c.due_at IS NOT NULL AND c.due_at < NOW()';
} elseif ($currentStatus === 'Assigned') {
    $conditions[] = 'c.status IN ("Assigned", "Acknowledged")';
} elseif ($currentStatus !== '' && in_array($currentStatus, ['Acknowledged', 'In Progress', 'Resolved', 'Closed', 'Reopened'], true)) {
    $conditions[] = 'c.status = ?';
    $params[] = $currentStatus;
}

// Search filtering
if ($searchQuery !== '') {
    $conditions[] = '(c.reference_no LIKE ? OR c.citizen_name LIKE ? OR c.mobile_no LIKE ? OR c.description_english LIKE ?)';
    $like = '%' . $searchQuery . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

// Date range
if ($fromDate !== '') {
    $conditions[] = 'c.created_at >= ?';
    $params[] = $fromDate . ' 00:00:00';
}
if ($toDate !== '') {
    $conditions[] = 'c.created_at <= ?';
    $params[] = $toDate . ' 23:59:59';
}

$whereClause = implode(' AND ', $conditions);

// 1. Total counts for tabs
$countsStmt = db()->prepare('
    SELECT 
        COUNT(*) as total_count,
        SUM(CASE WHEN status IN ("Assigned", "Acknowledged") THEN 1 ELSE 0 END) as assigned_count,
        SUM(CASE WHEN status = "In Progress" THEN 1 ELSE 0 END) as progress_count,
        SUM(CASE WHEN status = "Resolved" THEN 1 ELSE 0 END) as resolved_count,
        SUM(CASE WHEN status = "Closed" THEN 1 ELSE 0 END) as closed_count,
        SUM(CASE WHEN status = "Reopened" THEN 1 ELSE 0 END) as reopened_count,
        SUM(CASE WHEN status IN ("Assigned", "Acknowledged", "In Progress", "Reopened") AND due_at IS NOT NULL AND due_at < NOW() THEN 1 ELSE 0 END) as overdue_count,
        SUM(CASE WHEN c.reference_no LIKE 'TCMC-2026-%' AND EXISTS (
            SELECT 1 FROM complaint_escalations ce
            WHERE ce.complaint_id=c.id AND ce.officer_id=c.assigned_officer_id AND ce.level_no>1
        ) THEN 1 ELSE 0 END) AS transferred_to_me_count
    FROM complaints c
    WHERE c.assigned_officer_id = ? AND c.is_deleted = 0
');
$countsStmt->execute([$officerId]);
$tabCounts = $countsStmt->fetch() ?: [
    'total_count' => 0, 'assigned_count' => 0, 'progress_count' => 0,
    'resolved_count' => 0, 'closed_count' => 0, 'reopened_count' => 0, 'overdue_count' => 0
];
// Previous assignments count separately; historical complaints never inflate
// current-owner workload totals.
$previousStmt = db()->prepare("SELECT COUNT(DISTINCT c.id)
    FROM complaint_assignments h
    JOIN complaints c ON c.id=h.complaint_id
    WHERE h.officer_id=? AND h.is_current=0
      AND c.is_deleted=0 AND c.reference_no LIKE 'TCMC-2026-%'
      AND (c.assigned_officer_id IS NULL OR c.assigned_officer_id<>?)");
$previousStmt->execute([$officerId,$officerId]);
$previousCount=(int)$previousStmt->fetchColumn();

// 2. Count for current filter
$countStmt = db()->prepare("SELECT COUNT(*) FROM complaints c WHERE {$whereClause}");
$countStmt->execute($params);
$totalRecords = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalRecords / $perPage));

// 3. Fetch records
$dataSql = "
    SELECT c.*, d.department_name, cc.category_name, cc.sla_days, z.zone_no, w.ward_no, s.street_name,
      owner.officer_name AS current_owner_name,
      (SELECT prevOfficer.officer_name FROM complaint_assignments prev
       JOIN officers prevOfficer ON prevOfficer.id=prev.officer_id
       WHERE prev.complaint_id=c.id AND prev.is_current=0
       ORDER BY prev.unassigned_at DESC,prev.id DESC LIMIT 1) AS previous_owner_name,
      (SELECT MAX(ce.level_no) FROM complaint_escalations ce
       WHERE ce.complaint_id=c.id AND ce.officer_id=c.assigned_officer_id) AS incoming_level
    FROM complaints c
    JOIN departments d ON d.id = c.department_id
    JOIN complaint_categories cc ON cc.id = c.category_id
    JOIN zones z ON z.id = c.zone_id
    JOIN wards w ON w.id = c.ward_id
    LEFT JOIN streets s ON s.id = c.street_id
    LEFT JOIN officers owner ON owner.id=c.assigned_officer_id
    WHERE {$whereClause}
    ORDER BY 
        CASE WHEN c.status = 'Reopened' THEN 0 
             WHEN c.status IN ('Assigned', 'Acknowledged', 'In Progress') AND c.due_at IS NOT NULL AND c.due_at < NOW() THEN 1
             WHEN c.status IN ('Assigned', 'Acknowledged', 'In Progress') THEN 2
             ELSE 3 END ASC,
        c.created_at DESC
    LIMIT {$perPage} OFFSET {$offset}
";
$stmt = db()->prepare($dataSql);
$stmt->execute($params);
$complaints = $stmt->fetchAll();

include __DIR__ . '/../includes/employee-header.php';
?>

<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1"><?php echo $showPrevious?'Previously Handled Complaints':'My Assigned Complaints'; ?></h1>
        <p class="text-muted mb-0"><?php echo $showPrevious?'Previously assigned cases you can still open and review; updates belong to the current officer.':'Complaints currently assigned to '.e($officer['officer_name']).' ('.e($officer['designation']).')'; ?></p>
    </div>
    <div>
        <span class="badge bg-primary fs-6 px-3 py-2 shadow-sm">
            <i class="bi bi-person-badge me-1"></i> Officer ID: #<?php echo (int)$officer['id']; ?>
        </span>
    </div>
</div>

<!-- Status Tabs -->
<div class="card content-card border-0 shadow-sm mb-4">
    <div class="card-body p-2">
        <ul class="nav nav-pills flex-column flex-md-row gap-1">
            <li class="nav-item">
                <a class="nav-link <?php echo $currentStatus === '' ? 'active' : ''; ?> px-3 py-2 fw-semibold" 
                   href="<?php echo e(url('employee/my-complaints.php' . ($searchQuery ? '?q=' . urlencode($searchQuery) : ''))); ?>">
                    All <span class="badge bg-secondary ms-1"><?php echo (int)$tabCounts['total_count']; ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $currentStatus === 'Assigned' ? 'active' : ''; ?> px-3 py-2 fw-semibold" 
                   href="<?php echo e(url('employee/my-complaints.php?status=Assigned' . ($searchQuery ? '&q=' . urlencode($searchQuery) : ''))); ?>">
                    Pending <span class="badge bg-warning text-dark ms-1"><?php echo (int)$tabCounts['assigned_count']; ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $currentStatus === 'In Progress' ? 'active' : ''; ?> px-3 py-2 fw-semibold" 
                   href="<?php echo e(url('employee/my-complaints.php?status=In Progress' . ($searchQuery ? '&q=' . urlencode($searchQuery) : ''))); ?>">
                    In Progress <span class="badge bg-info text-dark ms-1"><?php echo (int)$tabCounts['progress_count']; ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $currentStatus === 'Resolved' ? 'active' : ''; ?> px-3 py-2 fw-semibold" 
                   href="<?php echo e(url('employee/my-complaints.php?status=Resolved' . ($searchQuery ? '&q=' . urlencode($searchQuery) : ''))); ?>">
                    Resolved <span class="badge bg-success ms-1"><?php echo (int)$tabCounts['resolved_count']; ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $currentStatus === 'Closed' ? 'active' : ''; ?> px-3 py-2 fw-semibold" 
                   href="<?php echo e(url('employee/my-complaints.php?status=Closed' . ($searchQuery ? '&q=' . urlencode($searchQuery) : ''))); ?>">
                    Closed <span class="badge bg-dark ms-1"><?php echo (int)$tabCounts['closed_count']; ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $currentStatus === 'Reopened' ? 'active bg-danger' : 'text-danger'; ?> px-3 py-2 fw-semibold" 
                   href="<?php echo e(url('employee/my-complaints.php?status=Reopened' . ($searchQuery ? '&q=' . urlencode($searchQuery) : ''))); ?>">
                    Reopened <span class="badge bg-danger ms-1"><?php echo (int)$tabCounts['reopened_count']; ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $showIncoming ? 'active' : ''; ?> px-3 py-2 fw-semibold" href="<?php echo e(url('employee/my-complaints.php?status=TransferredToMe')); ?>">
                    <i class="bi bi-arrow-left-right me-1"></i>Transferred to Me
                    <span class="badge bg-primary ms-1"><?php echo (int)($tabCounts['transferred_to_me_count']??0); ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $showPrevious ? 'active' : ''; ?> px-3 py-2 fw-semibold" href="<?php echo e(url('employee/my-complaints.php?status=PreviouslyHandled')); ?>">
                    <i class="bi bi-clock-history me-1"></i>Previously Handled
                    <span class="badge bg-secondary ms-1"><?php echo $previousCount; ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $currentStatus === 'Overdue' ? 'active bg-danger' : 'text-danger'; ?> px-3 py-2 fw-semibold" 
                   href="<?php echo e(url('employee/my-complaints.php?status=Overdue' . ($searchQuery ? '&q=' . urlencode($searchQuery) : ''))); ?>">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Overdue <span class="badge bg-danger ms-1"><?php echo (int)$tabCounts['overdue_count']; ?></span>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Filters Box -->
<div class="card content-card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="get" class="row g-2 align-items-center">
            <?php if ($currentStatus !== ''): ?>
                <input type="hidden" name="status" value="<?php echo e($currentStatus); ?>">
            <?php endif; ?>
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Search reference ID, citizen name, phone..." value="<?php echo e($searchQuery); ?>">
                </div>
            </div>
            <div class="col-md-3">
                <input type="date" name="from_date" class="form-control" placeholder="From Date" value="<?php echo e($fromDate); ?>" title="From registration date">
            </div>
            <div class="col-md-3">
                <input type="date" name="to_date" class="form-control" placeholder="To Date" value="<?php echo e($toDate); ?>" title="To registration date">
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary w-100" title="Apply filter"><i class="bi bi-funnel"></i></button>
                <a href="<?php echo e(url('employee/my-complaints.php' . ($currentStatus ? '?status=' . urlencode($currentStatus) : ''))); ?>" class="btn btn-outline-secondary" title="Reset filters"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Complaints Table -->
<div class="card content-card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom">
        <h5 class="fw-bold mb-0">Complaints List (<?php echo number_format($totalRecords); ?>)</h5>
        <span class="text-muted small">Showing page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
    </div>

    <?php if (empty($complaints)): ?>
        <div class="card-body text-center py-5">
            <div class="text-muted mb-2"><i class="bi bi-inbox fs-1"></i></div>
            <h5 class="fw-bold">No complaints found</h5>
            <p class="text-muted mb-0">No complaints match your current filter or search criteria.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Grievance ID</th>
                        <th><?php echo $showPrevious?'Assignment':'Citizen'; ?></th>
                        <th>Department / Category</th>
                        <th>Ward &amp; Street</th>
                        <th>Status</th>
                        <th><?php echo $showPrevious?'Current owner':'SLA Due Date'; ?></th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($complaints as $c): ?>
                        <?php
                            $isOverdue = false;
                            $dueText = '-';
                            if ($c['due_at']) {
                                $dueTime = strtotime($c['due_at']);
                                $isOverdue = time() > $dueTime && in_array($c['status'], ['Assigned', 'Acknowledged', 'In Progress', 'Reopened'], true);
                                $diffHours = round(($dueTime - time()) / 3600);
                                if ($isOverdue) {
                                    $dueText = '<span class="badge bg-danger"><i class="bi bi-clock-history me-1"></i>Overdue (' . abs($diffHours) . 'h ago)</span>';
                                } elseif (in_array($c['status'], ['Assigned', 'In Progress', 'Reopened'], true)) {
                                    $dueText = '<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>' . max(1, $diffHours) . 'h remaining</span>';
                                } else {
                                    $dueText = '<span class="badge bg-secondary">' . date('d-M-Y', $dueTime) . '</span>';
                                }
                            }
                            $statusBadgeClass = match($c['status']) {
                                'Assigned' => 'bg-warning text-dark',
                                 'Acknowledged' => 'bg-primary text-white',
                                'In Progress' => 'bg-info text-dark',
                                'Reopened' => 'bg-danger',
                                'Resolved' => 'bg-success',
                                'Closed' => 'bg-secondary',
                                default => 'bg-secondary'
                            };
                        ?>
                        <tr class="<?php echo $c['status'] === 'Reopened' ? 'table-danger' : ($isOverdue ? 'table-warning bg-opacity-25' : ''); ?>">
                            <td>
                                <a href="<?php echo e(url('employee/complaint-view.php?id=' . (int)$c['id'])); ?>" class="fw-bold text-decoration-none">
                                    <?php echo e($c['reference_no']); ?>
                                </a>
                                <?php if($showPrevious): ?>
                                  <span class="badge bg-light text-secondary border ms-1">Previously handled</span>
                                <?php elseif((int)($c['incoming_level']??0)>1): ?>
                                  <span class="badge bg-info text-dark ms-1" title="Recorded PDF escalation to your account">Escalated to me · L<?php echo (int)$c['incoming_level']; ?></span>
                                <?php endif; ?>
                                <?php if ($c['reopen_count'] > 0): ?>
                                    <span class="badge bg-danger ms-1" title="Reopened <?php echo (int)$c['reopen_count']; ?> times">Reopened &times;<?php echo (int)$c['reopen_count']; ?></span>
                                <?php endif; ?>
                                <div class="text-muted small"><?php echo date('d-M-Y H:i', strtotime($c['created_at'])); ?></div>
                            </td>
                            <td>
                                <?php if($showPrevious): ?>
                                  <span class="badge bg-light text-secondary border">Earlier assignment</span>
                                  <div class="small text-muted mt-1">View-only access retained</div>
                                <?php else: ?>
                                  <div class="fw-medium"><?php echo e($c['citizen_name']); ?></div>
                                  <div class="text-muted small"><?php echo e($c['mobile_no'] ? mask_mobile($c['mobile_no']) : '-'); ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-medium"><?php echo e($c['category_name']); ?></div>
                                <div class="text-muted small"><?php echo e($c['department_name']); ?></div>
                            </td>
                            <td>
                                <div>Zone <?php echo e($c['zone_no']); ?> &bull; Ward <?php echo e($c['ward_no']); ?></div>
                                <?php if ($c['street_name']): ?>
                                    <small class="text-muted"><?php echo e($c['street_name']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge <?php echo $statusBadgeClass; ?> px-2 py-1"><?php echo e($c['status']); ?></span>
                            </td>
                            <td>
                                <?php if($showPrevious): ?>
                                  <strong><?php echo e($c['current_owner_name'] ?: 'Unassigned'); ?></strong>
                                  <div class="text-muted small">Current responsible officer</div>
                                <?php else: ?>
                                  <?php echo $dueText; ?>
                                  <?php if ($c['due_at']): ?>
                                    <div class="text-muted small mt-1"><?php echo date('d-M-Y H:i', strtotime($c['due_at'])); ?></div>
                                  <?php endif; ?>
                                  <?php if($c['previous_owner_name']): ?>
                                    <div class="text-muted small mt-1">From: <?php echo e($c['previous_owner_name']); ?></div>
                                  <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if($showPrevious): ?>
                                  <a href="<?php echo e(url('employee/complaint-view.php?id=' . (int)$c['id'])); ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i> View
                                  </a>
                                <?php else: ?>
                                  <a href="<?php echo e(url('employee/complaint-view.php?id=' . (int)$c['id'])); ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil-square me-1"></i> Manage
                                  </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-center">
                <nav aria-label="Complaints pagination">
                    <ul class="pagination pagination-sm mb-0">
                        <?php
                            $queryParams = $_GET;
                        ?>
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <?php $queryParams['page'] = $page - 1; ?>
                            <a class="page-link" href="?<?php echo http_build_query($queryParams); ?>">&laquo; Previous</a>
                        </li>
                        <?php for ($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++): ?>
                            <?php $queryParams['page'] = $p; ?>
                            <li class="page-item <?php echo $p === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?<?php echo http_build_query($queryParams); ?>"><?php echo $p; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                            <?php $queryParams['page'] = $page + 1; ?>
                            <a class="page-link" href="?<?php echo http_build_query($queryParams); ?>">Next &raquo;</a>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/employee-footer.php'; ?>
