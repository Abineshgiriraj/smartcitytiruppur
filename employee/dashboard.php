<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

$user = require_login();
$pageTitle = can_manage_all_complaints($user) ? 'Corporation Overview' : 'My Dashboard';
$where = ['c.is_deleted = 0'];
$params = [];

// All dashboard queries use the same role-scoped WHERE clause.
if (!can_manage_all_complaints($user)) {
    if ($user['role'] === 'Department Head') {
        $where[] = 'c.department_id = ?';
        $params[] = (int)($user['department_id'] ?? 0);
    } elseif ($user['role'] === 'Zone Officer') {
        $where[] = 'c.zone_id = ?';
        $params[] = (int)($user['zone_id'] ?? 0);
    } elseif ($user['role'] === 'Ward Officer') {
        $where[] = 'c.ward_id = ?';
        $params[] = (int)($user['ward_id'] ?? 0);
    } elseif ($user['role'] === 'Field Officer') {
        $stmt = db()->prepare('SELECT id FROM officers WHERE user_id = ? LIMIT 1');
        $stmt->execute([(int)$user['id']]);
        $where[] = 'c.assigned_officer_id = ?';
        $params[] = (int)$stmt->fetchColumn();
    } elseif ($user['role'] !== 'Call Centre' && $user['role'] !== 'Report User') {
        // Unknown roles get no complaint access, rather than a global view.
        $where[] = '1 = 0';
    }
}
$condition = implode(' AND ', $where);
$openStates = "('Registered','Assigned','Acknowledged','In Progress','Reopened')";
$closedStates = "('Resolved','Closed','Rejected')";

$summaryStmt = db()->prepare(
    "SELECT COUNT(*) AS total,
            COALESCE(SUM(c.status IN $openStates),0) AS pending,
            COALESCE(SUM(c.status IN ('Resolved','Closed')),0) AS resolved,
            COALESCE(SUM(c.status NOT IN $closedStates AND c.due_at < NOW()),0) AS overdue,
            COALESCE(SUM(c.status NOT IN $closedStates AND c.assigned_officer_id IS NULL),0) AS unassigned,
            COALESCE(SUM(c.status = 'In Progress'),0) AS in_progress
     FROM complaints c WHERE $condition"
);
$summaryStmt->execute($params);
$summary = $summaryStmt->fetch() ?: [];

$recentStmt = db()->prepare(
    "SELECT c.id,c.reference_no,c.citizen_name,c.status,c.created_at,c.due_at,
            d.department_name,cc.category_name,o.officer_name
     FROM complaints c
     JOIN departments d ON d.id=c.department_id
     JOIN complaint_categories cc ON cc.id=c.category_id
     LEFT JOIN officers o ON o.id=c.assigned_officer_id
     WHERE $condition ORDER BY c.created_at DESC,c.id DESC LIMIT 8"
);
$recentStmt->execute($params);
$recent = $recentStmt->fetchAll();

$monthStmt = db()->prepare(
    "SELECT DATE_FORMAT(c.created_at,'%Y-%m') AS month_key,
            DATE_FORMAT(c.created_at,'%b') AS month_label,COUNT(*) AS total
     FROM complaints c WHERE $condition
       AND c.created_at >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 5 MONTH),'%Y-%m-01')
     GROUP BY month_key,month_label ORDER BY month_key"
);
$monthStmt->execute($params);
$monthly = $monthStmt->fetchAll();
$monthMax = $monthly ? max(array_map(static fn($row) => (int)$row['total'], $monthly)) : 0;

$departmentStmt = db()->prepare(
    "SELECT d.department_name,COUNT(*) AS total,
            COALESCE(SUM(c.status NOT IN $closedStates),0) AS open_count
     FROM complaints c JOIN departments d ON d.id=c.department_id
     WHERE $condition GROUP BY c.department_id,d.department_name
     ORDER BY total DESC LIMIT 6"
);
$departmentStmt->execute($params);
$departments = $departmentStmt->fetchAll();
$departmentMax = $departments ? max(array_map(static fn($row) => (int)$row['total'], $departments)) : 0;

$total = (int)($summary['total'] ?? 0);
$resolved = (int)($summary['resolved'] ?? 0);
$resolutionRate = $total > 0 ? (int)round($resolved * 100 / $total) : 0;

include __DIR__ . '/../includes/employee-header.php';
?>
<div class="admin-heading">
  <div>
    <span class="admin-eyebrow">Grievance Control Centre</span>
    <h1 class="fw-bold mb-1"><?php echo e($pageTitle); ?></h1>
    <p>Monitor complaints, service deadlines and departmental performance.</p>
  </div>
  <div class="d-flex flex-wrap gap-2 align-items-center">
    <span class="admin-pill"><i class="bi bi-circle-fill" style="font-size:6px"></i> Live from Smart City database</span>
    <a class="btn btn-primary" href="<?php echo e(url('employee/reports.php')); ?>"><i class="bi bi-file-earmark-bar-graph me-1"></i> View Reports</a>
  </div>
</div>
<div class="row g-3 mb-4">
  <?php foreach ([
    ['Total Complaints','total','bi-collection',''],
    ['Pending','pending','bi-hourglass-split','warning'],
    ['Resolved','resolved','bi-check2-circle','success'],
    ['SLA Overdue','overdue','bi-exclamation-triangle','danger'],
  ] as [$label,$key,$icon,$theme]): ?>
    <div class="col-sm-6 col-xl-3">
      <div class="card admin-metric h-100">
        <div class="admin-metric-top"><span><?php echo e($label); ?></span><span class="admin-metric-icon <?php echo e($theme); ?>"><i class="bi <?php echo e($icon); ?>"></i></span></div>
        <div class="admin-metric-value"><?php echo number_format((int)($summary[$key] ?? 0)); ?></div>
        <div class="admin-metric-caption"><?php echo $key === 'overdue' ? 'Open cases past deadline' : 'Based on current complaint records'; ?></div>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<div class="row g-3 mb-4">
  <div class="col-xl-8">
    <div class="card p-4 h-100">
      <div class="d-flex justify-content-between gap-2 align-items-start">
        <div><h2 class="admin-panel-heading">Complaint Registrations</h2><p class="admin-panel-subtitle">Monthly new complaints · last 6 months</p></div>
        <span class="admin-pill">Monthly</span>
      </div>
      <?php if (!$monthly): ?><div class="admin-empty">No registrations recorded in this period.</div>
      <?php else: ?>
        <div class="admin-chart" role="img" aria-label="Monthly complaint registrations">
          <?php foreach ($monthly as $row): ?>
            <?php $height = $monthMax > 0 ? max(3, (int)round(((int)$row['total'] / $monthMax) * 145)) : 3; ?>
            <div class="admin-chart-col">
              <span class="admin-chart-value"><?php echo (int)$row['total']; ?></span>
              <div class="admin-chart-bar-space"><div class="admin-chart-bar" style="height:<?php echo $height; ?>px"></div></div>
              <span class="admin-chart-label"><?php echo e($row['month_label']); ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <div class="col-xl-4">
    <div class="card p-4 h-100">
      <h2 class="admin-panel-heading">Resolution Performance</h2>
      <p class="admin-panel-subtitle">Resolved or closed complaints / total</p>
      <div class="d-flex align-items-end gap-2 my-3">
        <strong style="font-size:43px;color:#1b3553;line-height:1"><?php echo $resolutionRate; ?>%</strong>
        <span class="text-muted small pb-1">resolution rate</span>
      </div>
      <div class="admin-progress-track" style="height:10px"><div class="admin-progress-fill" style="width:<?php echo $resolutionRate; ?>%;background:#28a579"></div></div>
      <div class="d-flex justify-content-between text-muted small mt-2"><span><?php echo number_format($resolved); ?> resolved</span><span><?php echo number_format($total); ?> total</span></div>
      <hr style="border-color:#e9eef5">
      <div class="d-flex justify-content-between align-items-center py-1"><span><i class="bi bi-person-x me-2 text-danger"></i>Unassigned open</span><strong><?php echo (int)($summary['unassigned'] ?? 0); ?></strong></div>
      <div class="d-flex justify-content-between align-items-center py-1"><span><i class="bi bi-arrow-repeat me-2 text-primary"></i>In progress</span><strong><?php echo (int)($summary['in_progress'] ?? 0); ?></strong></div>
      <a class="btn btn-outline-primary btn-sm mt-auto" href="<?php echo e(url('employee/complaints.php')); ?>">Review complaint inbox <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
  </div>
</div>
<div class="row g-3 mb-4">
  <div class="col-xl-7">
    <div class="card p-4 h-100">
      <h2 class="admin-panel-heading">Departments at a Glance</h2>
      <p class="admin-panel-subtitle">Leading departments by registered complaints</p>
      <?php if (!$departments): ?><div class="admin-empty">No department statistics available.</div><?php endif; ?>
      <?php foreach ($departments as $row): ?>
        <?php $percent = $departmentMax > 0 ? (int)round(((int)$row['total'] / $departmentMax)*100) : 0; ?>
        <div class="admin-progress-row">
          <div class="admin-progress-meta"><span title="<?php echo e($row['department_name']); ?>"><?php echo e($row['department_name']); ?></span><strong><?php echo (int)$row['total']; ?> <span class="text-muted fw-normal">· <?php echo (int)$row['open_count']; ?> open</span></strong></div>
          <div class="admin-progress-track"><div class="admin-progress-fill" style="width:<?php echo $percent; ?>%"></div></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="col-xl-5">
    <div class="card p-4 h-100">
      <h2 class="admin-panel-heading mb-1">Quick Actions</h2>
      <p class="admin-panel-subtitle mb-3">Go directly to your daily tasks</p>
      <div class="d-grid gap-2">
        <a class="card admin-quick-action" href="<?php echo e(url('employee/complaints.php')); ?>"><i class="bi bi-inboxes"></i><span><strong>Complaint Inbox</strong><small>Filter, review and update grievances</small></span><i class="bi bi-chevron-right ms-auto"></i></a>
        <a class="card admin-quick-action" href="<?php echo e(url('employee/reports.php')); ?>"><i class="bi bi-bar-chart"></i><span><strong>Analytics & Reports</strong><small>Export and review departmental metrics</small></span><i class="bi bi-chevron-right ms-auto"></i></a>
        <?php if (in_array($user['role'], ['Super Admin','Admin','Commissioner','Department Head'],true)): ?>
        <a class="card admin-quick-action" href="<?php echo e(url('employee/escalation.php')); ?>"><i class="bi bi-diagram-3"></i><span><strong>Escalation Monitor</strong><small>Review stage rules and overdue cases</small></span><i class="bi bi-chevron-right ms-auto"></i></a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<div class="card content-card p-4">
  <div class="d-flex flex-wrap justify-content-between gap-2 align-items-center mb-3">
    <div><h2 class="admin-panel-heading">Recent Complaints</h2><p class="admin-panel-subtitle mb-0">Latest registered grievances</p></div>
    <a href="<?php echo e(url('employee/complaints.php')); ?>" class="btn btn-sm btn-outline-primary">View All <i class="bi bi-arrow-right ms-1"></i></a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead><tr><th>Reference</th><th>Citizen</th><th>Department</th><th>Assigned To</th><th>Status</th><th>Registered</th><th></th></tr></thead>
      <tbody>
        <?php if (!$recent): ?><tr><td colspan="7" class="admin-empty">No complaints found.</td></tr><?php endif; ?>
        <?php foreach ($recent as $row): ?>
          <tr>
            <td class="fw-semibold"><?php echo e($row['reference_no']); ?></td>
            <td><?php echo e($row['citizen_name']); ?></td>
            <td><?php echo e($row['department_name']); ?></td>
            <td><?php echo e($row['officer_name'] ?: 'Unassigned'); ?></td>
            <td><span class="badge text-bg-<?php echo e(status_badge_class($row['status'])); ?>"><?php echo e($row['status']); ?></span></td>
            <td class="text-muted"><?php echo e(date('d M Y',strtotime($row['created_at']))); ?></td>
            <td><a class="btn btn-sm btn-outline-primary" href="<?php echo e(url('employee/complaint-view.php?id=' . (int)$row['id'])); ?>">Open</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../includes/employee-footer.php'; ?>
