<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

$auth = require_officer();
$user = $auth['user'];
$officer = $auth['officer'];
$officerId = (int)$officer['id'];

$pageTitle = 'Officer Dashboard';

// 1. KPI Counts
$kpiStmt = db()->prepare('
    SELECT 
        COUNT(*) as total_count,
        SUM(CASE WHEN status IN ("Assigned", "Acknowledged") THEN 1 ELSE 0 END) as assigned_count,
        SUM(CASE WHEN status = "In Progress" THEN 1 ELSE 0 END) as progress_count,
        SUM(CASE WHEN status = "Resolved" THEN 1 ELSE 0 END) as resolved_count,
        SUM(CASE WHEN status = "Closed" THEN 1 ELSE 0 END) as closed_count,
        SUM(CASE WHEN status = "Reopened" THEN 1 ELSE 0 END) as reopened_count,
        SUM(CASE WHEN status IN ("Assigned", "Acknowledged", "In Progress", "Reopened") AND due_at IS NOT NULL AND due_at < NOW() THEN 1 ELSE 0 END) as overdue_count
    FROM complaints
    WHERE assigned_officer_id = ? AND is_deleted = 0
');
$kpiStmt->execute([$officerId]);
$kpis = $kpiStmt->fetch() ?: [
    'total_count' => 0, 'assigned_count' => 0, 'progress_count' => 0,
    'resolved_count' => 0, 'closed_count' => 0, 'reopened_count' => 0, 'overdue_count' => 0
];

// 2. Feedback stats
$feedbackStmt = db()->prepare('
    SELECT COUNT(f.id) as review_count, AVG(f.rating) as avg_rating
    FROM feedback f
    JOIN complaints c ON c.id = f.complaint_id
    WHERE c.assigned_officer_id = ?
');
$feedbackStmt->execute([$officerId]);
$feedbackStats = $feedbackStmt->fetch() ?: ['review_count' => 0, 'avg_rating' => null];

// 3. Urgent & Pending Action Complaints
$urgentStmt = db()->prepare('
    SELECT c.*, d.department_name, cc.category_name, cc.sla_days, z.zone_no, w.ward_no
    FROM complaints c
    JOIN departments d ON d.id = c.department_id
    JOIN complaint_categories cc ON cc.id = c.category_id
    JOIN zones z ON z.id = c.zone_id
    JOIN wards w ON w.id = c.ward_id
    WHERE c.assigned_officer_id = ? AND c.is_deleted = 0
      AND c.status IN ("Assigned", "Acknowledged", "In Progress", "Reopened")
    ORDER BY 
        CASE WHEN c.due_at IS NOT NULL AND c.due_at < NOW() THEN 0 ELSE 1 END ASC,
        c.due_at ASC,
        c.id DESC
    LIMIT 10
');
$urgentStmt->execute([$officerId]);
$urgentComplaints = $urgentStmt->fetchAll();

// 4. Reopened Complaints (special attention)
$reopenedStmt = db()->prepare('
    SELECT c.*, d.department_name, cc.category_name, z.zone_no, w.ward_no,
           (SELECT reason FROM complaint_reopen_history WHERE complaint_id = c.id ORDER BY id DESC LIMIT 1) as reopen_reason
    FROM complaints c
    JOIN departments d ON d.id = c.department_id
    JOIN complaint_categories cc ON cc.id = c.category_id
    JOIN zones z ON z.id = c.zone_id
    JOIN wards w ON w.id = c.ward_id
    WHERE c.assigned_officer_id = ? AND c.is_deleted = 0 AND c.status = "Reopened"
    ORDER BY c.updated_at DESC
');
$reopenedStmt->execute([$officerId]);
$reopenedComplaints = $reopenedStmt->fetchAll();

include __DIR__ . '/../includes/employee-header.php';
?>

<!-- Officer Profile Welcome Banner -->
<div class="card content-card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #0e2a47 0%, #17426e 100%); color: #fff;">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-white text-primary fw-bold" style="width: 60px; height: 60px; font-size: 24px;">
                        <?php echo e(strtoupper(substr(trim((string)$officer['officer_name']), 0, 1))); ?>
                    </div>
                    <div>
                        <div class="badge bg-warning text-dark mb-1 px-2 py-1"><i class="bi bi-shield-check me-1"></i><?php echo e($officer['designation'] ?: 'Field Officer'); ?></div>
                        <h2 class="h4 fw-bold mb-0 text-white"><?php echo e($officer['officer_name']); ?></h2>
                        <div class="text-white-50 small mt-1">
                            <span><i class="bi bi-building me-1"></i><?php echo e($officer['department_name'] ?: 'Municipal Administration'); ?></span>
                            <span class="mx-2">&bull;</span>
                            <span><i class="bi bi-telephone me-1"></i><?php echo e($officer['mobile_no'] ?: '-'); ?></span>
                            <span class="mx-2">&bull;</span>
                            <span><i class="bi bi-envelope me-1"></i><?php echo e($officer['email'] ?: $user['email'] ?: 'Mobile login'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="<?php echo e(url('employee/my-complaints.php')); ?>" class="btn btn-light fw-bold text-primary px-3 shadow-sm me-2">
                    <i class="bi bi-inbox me-1"></i> View My Tasks
                </a>
                <a href="<?php echo e(url('employee/officer-profile.php')); ?>" class="btn btn-outline-light px-3">
                    <i class="bi bi-gear me-1"></i> Settings
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__.'/../includes/legacy-complaint-history.php'; $legacyCounts=legacy_history_counts(db(),$officerId,(int)$user['id']); ?>
<?php if ($legacyCounts!==null): ?>
<div class="card content-card p-3 mb-4"><div class="d-flex flex-wrap justify-content-between align-items-center gap-2"><div>
<div class="text-muted">Current + approved historical complaints (unique)</div>
<div class="h3 mb-1"><?=number_format((int)$legacyCounts['lifetime_total'])?></div>
<div class="small text-muted"><?=number_format((int)$legacyCounts['additional_history'])?> additional historical complaints, not active tasks</div></div>
<a class="btn btn-outline-primary" href="<?=e(url('employee/legacy-complaint-history.php'))?>">Historical complaints</a></div></div>
<?php endif; ?>
<!-- KPI Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <a href="<?php echo e(url('employee/my-complaints.php')); ?>" class="text-decoration-none">
            <div class="card content-card border-0 shadow-sm h-100 p-3 hover-lift">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Total Assigned</span>
                    <span class="badge bg-primary-subtle text-primary p-2 rounded-circle"><i class="bi bi-folder2-open fs-5"></i></span>
                </div>
                <div class="h3 fw-bold mb-0 text-dark"><?php echo number_format((int)$kpis['total_count']); ?></div>
                <div class="text-muted small mt-1">All your complaints</div>
            </div>
        </a>
    </div>

    <div class="col-6 col-lg-3">
        <a href="<?php echo e(url('employee/my-complaints.php?status=Assigned')); ?>" class="text-decoration-none">
            <div class="card content-card border-0 shadow-sm h-100 p-3 hover-lift border-start border-4 border-warning">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">New / Pending</span>
                    <span class="badge bg-warning-subtle text-warning p-2 rounded-circle"><i class="bi bi-hourglass-split fs-5"></i></span>
                </div>
                <div class="h3 fw-bold mb-0 text-warning"><?php echo number_format((int)$kpis['assigned_count']); ?></div>
                <div class="text-muted small mt-1">Awaiting action / start</div>
            </div>
        </a>
    </div>

    <div class="col-6 col-lg-3">
        <a href="<?php echo e(url('employee/my-complaints.php?status=In Progress')); ?>" class="text-decoration-none">
            <div class="card content-card border-0 shadow-sm h-100 p-3 hover-lift border-start border-4 border-info">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">In Progress</span>
                    <span class="badge bg-info-subtle text-info p-2 rounded-circle"><i class="bi bi-tools fs-5"></i></span>
                </div>
                <div class="h3 fw-bold mb-0 text-info"><?php echo number_format((int)$kpis['progress_count']); ?></div>
                <div class="text-muted small mt-1">Currently under work</div>
            </div>
        </a>
    </div>

    <div class="col-6 col-lg-3">
        <a href="<?php echo e(url('employee/my-complaints.php?status=Closed')); ?>" class="text-decoration-none">
            <div class="card content-card border-0 shadow-sm h-100 p-3 hover-lift border-start border-4 border-success">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Closed</span>
                    <span class="badge bg-success-subtle text-success p-2 rounded-circle"><i class="bi bi-check-circle fs-5"></i></span>
                </div>
                <div class="h3 fw-bold mb-0 text-success"><?php echo number_format((int)$kpis['closed_count']); ?></div>
                <div class="text-muted small mt-1">Completed and closed</div>
            </div>
        </a>
    </div>

</div>
<div class="card content-card border-0 shadow-sm p-3 mb-4">
  <div class="d-flex flex-wrap gap-2 align-items-center">
    <span class="fw-semibold me-2">Quick filters:</span>
    <a class="btn btn-sm btn-outline-danger" href="<?php echo e(url('employee/my-complaints.php?status=Reopened')); ?>">
      <i class="bi bi-arrow-repeat me-1"></i>Reopened (<?php echo (int)$kpis['reopened_count']; ?>)</a>
    <a class="btn btn-sm btn-outline-danger" href="<?php echo e(url('employee/my-complaints.php?status=Overdue')); ?>">
      <i class="bi bi-exclamation-triangle me-1"></i>Overdue (<?php echo (int)$kpis['overdue_count']; ?>)</a>
    <a class="btn btn-sm btn-outline-success" href="<?php echo e(url('employee/my-complaints.php?status=Resolved')); ?>">
      <i class="bi bi-check-circle me-1"></i>Resolved (<?php echo (int)$kpis['resolved_count']; ?>)</a>
    <a class="btn btn-sm btn-outline-primary" href="<?php echo e(url('employee/officer-feedback.php')); ?>">
      <i class="bi bi-star me-1"></i>Feedback (<?php echo (int)$feedbackStats['review_count']; ?>)</a>
  </div>
</div>

<!-- Reopened Section (if any) -->
<?php if (!empty($reopenedComplaints)): ?>
<div class="card content-card border-danger shadow-sm mb-4">
    <div class="card-header bg-danger text-white d-flex align-items-center justify-content-between py-3">
        <span class="fw-bold"><i class="bi bi-arrow-repeat me-2"></i>Reopened Complaints Requiring Attention (<?php echo count($reopenedComplaints); ?>)</span>
        <span class="badge bg-white text-danger fw-bold">Priority Action</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Grievance ID</th>
                    <th>Citizen</th>
                    <th>Category</th>
                    <th>Ward</th>
                    <th>Citizen's Reopen Reason</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reopenedComplaints as $c): ?>
                    <tr>
                        <td>
                            <a href="<?php echo e(url('employee/complaint-view.php?id=' . $c['id'])); ?>" class="fw-bold text-decoration-none">
                                <?php echo e($c['reference_no']); ?>
                            </a>
                        </td>
                        <td>
                            <div><?php echo e($c['citizen_name']); ?></div>
                            <small class="text-muted"><?php echo e($c['mobile_no'] ? mask_mobile($c['mobile_no']) : '-'); ?></small>
                        </td>
                        <td><?php echo e($c['category_name']); ?></td>
                        <td>Zone <?php echo e($c['zone_no']); ?> &bull; Ward <?php echo e($c['ward_no']); ?></td>
                        <td>
                            <span class="text-danger fw-medium"><?php echo e($c['reopen_reason'] ?: 'Citizen was not satisfied with the resolution.'); ?></span>
                        </td>
                        <td class="text-end">
                            <a href="<?php echo e(url('employee/complaint-view.php?id=' . $c['id'])); ?>" class="btn btn-sm btn-danger">
                                <i class="bi bi-eye me-1"></i> Resolve Now
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Urgent / Active Complaints List -->
<div class="card content-card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
        <div>
            <h5 class="fw-bold mb-0"><i class="bi bi-list-task text-primary me-2"></i>Active Assigned Tasks</h5>
            <small class="text-muted">Complaints currently pending your action or resolution</small>
        </div>
        <a href="<?php echo e(url('employee/my-complaints.php')); ?>" class="btn btn-sm btn-outline-primary">
            View All My Complaints <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <?php if (empty($urgentComplaints)): ?>
        <div class="card-body text-center py-5">
            <div class="text-muted mb-2"><i class="bi bi-check2-circle fs-1 text-success"></i></div>
            <h5 class="fw-bold">No Pending Complaints</h5>
            <p class="text-muted">Great work! You have no pending or overdue complaints assigned at this time.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Grievance ID</th>
                        <th>Citizen</th>
                        <th>Department / Category</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>SLA Due Date</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($urgentComplaints as $c): ?>
                        <?php
                            $isOverdue = false;
                            $dueText = '-';
                            if ($c['due_at']) {
                                $dueTime = strtotime($c['due_at']);
                                $isOverdue = time() > $dueTime;
                                $diffHours = round(($dueTime - time()) / 3600);
                                if ($isOverdue) {
                                    $dueText = '<span class="badge bg-danger">Overdue (' . abs($diffHours) . 'h ago)</span>';
                                } else {
                                    $dueText = '<span class="badge bg-warning text-dark">Due in ' . max(1, $diffHours) . 'h</span>';
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
                        <tr>
                            <td>
                                <a href="<?php echo e(url('employee/complaint-view.php?id=' . $c['id'])); ?>" class="fw-bold text-decoration-none">
                                    <?php echo e($c['reference_no']); ?>
                                </a>
                                <div class="text-muted small"><?php echo date('d-M-Y H:i', strtotime($c['created_at'])); ?></div>
                            </td>
                            <td>
                                <div class="fw-medium"><?php echo e($c['citizen_name']); ?></div>
                                <small class="text-muted"><?php echo e($c['mobile_no'] ? mask_mobile($c['mobile_no']) : '-'); ?></small>
                            </td>
                            <td>
                                <div class="fw-medium"><?php echo e($c['category_name']); ?></div>
                                <small class="text-muted"><?php echo e($c['department_name']); ?></small>
                            </td>
                            <td>
                                <div>Zone <?php echo e($c['zone_no']); ?> &bull; Ward <?php echo e($c['ward_no']); ?></div>
                            </td>
                            <td>
                                <span class="badge <?php echo $statusBadgeClass; ?> px-2 py-1"><?php echo e($c['status']); ?></span>
                            </td>
                            <td>
                                <?php echo $dueText; ?>
                                <?php if ($c['due_at']): ?>
                                    <div class="text-muted small"><?php echo date('d-M-Y', strtotime($c['due_at'])); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="<?php echo e(url('employee/complaint-view.php?id=' . $c['id'])); ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil-square me-1"></i> Manage
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/employee-footer.php'; ?>
