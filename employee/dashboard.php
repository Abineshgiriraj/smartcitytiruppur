<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

$user = require_login();
$pageTitle = 'Employee Dashboard';

$where = ['c.is_deleted = 0'];
$params = [];

if (!can_manage_all_complaints($user)) {
    if ($user['role'] === 'Department Head' && $user['department_id']) {
        $where[] = 'c.department_id = ?';
        $params[] = (int)$user['department_id'];
    } elseif ($user['role'] === 'Zone Officer' && $user['zone_id']) {
        $where[] = 'c.zone_id = ?';
        $params[] = (int)$user['zone_id'];
    } elseif ($user['role'] === 'Ward Officer' && $user['ward_id']) {
        $where[] = 'c.ward_id = ?';
        $params[] = (int)$user['ward_id'];
    } elseif ($user['role'] === 'Field Officer') {
        $officerStmt = db()->prepare('SELECT id FROM officers WHERE user_id = ? LIMIT 1');
        $officerStmt->execute([(int)$user['id']]);
        $officerId = (int)$officerStmt->fetchColumn();
        $where[] = 'c.assigned_officer_id = ?';
        $params[] = $officerId;
    }
}

$stmt = db()->prepare(
    "SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN c.status IN ('Registered','Assigned','Acknowledged','In Progress','Reopened') THEN 1 ELSE 0 END) AS pending,
        SUM(CASE WHEN c.status IN ('Resolved','Closed') THEN 1 ELSE 0 END) AS resolved,
        SUM(CASE WHEN c.due_at < NOW() AND c.status NOT IN ('Resolved','Closed','Rejected') THEN 1 ELSE 0 END) AS overdue
     FROM complaints c WHERE " . implode(' AND ', $where)
);
$stmt->execute($params);
$summary = $stmt->fetch() ?: [];

$recentStmt = db()->prepare(
    "SELECT c.id, c.reference_no, c.citizen_name, c.status, c.created_at,
            d.department_name, cc.category_name
     FROM complaints c
     JOIN departments d ON d.id = c.department_id
     JOIN complaint_categories cc ON cc.id = c.category_id
     WHERE " . implode(' AND ', $where) . "
     ORDER BY c.created_at DESC LIMIT 10"
);
$recentStmt->execute($params);
$recent = $recentStmt->fetchAll();

include __DIR__ . '/../includes/employee-header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div><h1 class="h3 fw-bold mb-1">Employee Dashboard</h1><p class="text-muted mb-0">Complaint workload and recent activity.</p></div>
    <a class="btn btn-primary" href="<?php echo e(url('employee/complaints.php')); ?>">Open Complaint Inbox</a>
</div>

<div class="row g-4 mb-4">
    <?php foreach ([
        ['Total', (int)($summary['total'] ?? 0), 'primary'],
        ['Pending', (int)($summary['pending'] ?? 0), 'warning'],
        ['Resolved', (int)($summary['resolved'] ?? 0), 'success'],
        ['SLA Overdue', (int)($summary['overdue'] ?? 0), 'danger'],
    ] as [$label, $value, $colour]): ?>
        <div class="col-sm-6 col-xl-3"><div class="stat-card border-start border-4 border-<?php echo e($colour); ?>"><div class="stat-number"><?php echo number_format($value); ?></div><div class="text-muted"><?php echo e($label); ?></div></div></div>
    <?php endforeach; ?>
</div>

<div class="card content-card p-4">
    <h2 class="h5 fw-bold mb-3">Recent Complaints</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-dark"><tr><th>Reference</th><th>Date</th><th>Citizen</th><th>Department</th><th>Category</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
                <?php if (!$recent): ?><tr><td colspan="7" class="text-center text-muted py-4">No complaints found.</td></tr><?php endif; ?>
                <?php foreach ($recent as $row): ?>
                    <tr>
                        <td><?php echo e($row['reference_no']); ?></td>
                        <td><?php echo e(date('d-m-Y', strtotime($row['created_at']))); ?></td>
                        <td><?php echo e($row['citizen_name']); ?></td>
                        <td><?php echo e($row['department_name']); ?></td>
                        <td><?php echo e($row['category_name']); ?></td>
                        <td><span class="badge text-bg-<?php echo e(status_badge_class($row['status'])); ?>"><?php echo e($row['status']); ?></span></td>
                        <td><a class="btn btn-sm btn-outline-primary" href="<?php echo e(url('employee/complaint-view.php?id=' . (int)$row['id'])); ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../includes/employee-footer.php'; ?>
