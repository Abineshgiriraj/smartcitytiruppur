<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

$user = require_permission('complaints.view');
$pageTitle = 'Complaints';

$fromDate = trim($_GET['from_date'] ?? date('Y-m-01'));
$toDate = trim($_GET['to_date'] ?? date('Y-m-d'));
$status = trim($_GET['status'] ?? '');
$departmentId = (int)($_GET['department_id'] ?? 0);
$zoneId = (int)($_GET['zone_id'] ?? 0);
$wardId = (int)($_GET['ward_id'] ?? 0);
$search = trim($_GET['q'] ?? '');

$where = ['c.is_deleted = 0'];
$params = [];

if (valid_date($fromDate)) {
    $where[] = 'DATE(c.created_at) >= ?';
    $params[] = $fromDate;
}
if (valid_date($toDate)) {
    $where[] = 'DATE(c.created_at) <= ?';
    $params[] = $toDate;
}
if ($status !== '') {
    $where[] = 'c.status = ?';
    $params[] = $status;
}
if ($departmentId > 0) {
    $where[] = 'c.department_id = ?';
    $params[] = $departmentId;
}
if ($zoneId > 0) {
    $where[] = 'c.zone_id = ?';
    $params[] = $zoneId;
}
if ($wardId > 0) {
    $where[] = 'c.ward_id = ?';
    $params[] = $wardId;
}
if ($search !== '') {
    $where[] = '(c.reference_no LIKE ? OR c.citizen_name LIKE ? OR c.mobile_no LIKE ? OR c.description_english LIKE ?)';
    $like = '%' . $search . '%';
    array_push($params, $like, $like, $like, $like);
}

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
        $where[] = 'c.assigned_officer_id = ?';
        $params[] = (int)$officerStmt->fetchColumn();
    }
}

$sql = "SELECT c.id, c.reference_no, c.citizen_name, c.mobile_no, c.status, c.created_at, c.due_at,
               d.department_name, cc.category_name, z.zone_no, w.ward_no, o.officer_name
        FROM complaints c
        JOIN departments d ON d.id = c.department_id
        JOIN complaint_categories cc ON cc.id = c.category_id
        JOIN zones z ON z.id = c.zone_id
        JOIN wards w ON w.id = c.ward_id
        LEFT JOIN officers o ON o.id = c.assigned_officer_id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY c.created_at DESC
        LIMIT 1000";

$stmt = db()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$departments = db()->query('SELECT id, department_name FROM departments WHERE is_active = 1 ORDER BY department_name')->fetchAll();
$zones = db()->query('SELECT id, zone_no FROM zones WHERE is_active = 1 ORDER BY zone_no')->fetchAll();
$wards = db()->query('SELECT id, ward_no FROM wards WHERE is_active = 1 ORDER BY ward_no')->fetchAll();

include __DIR__ . '/../includes/employee-header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div><h1 class="h3 fw-bold mb-1">Complaint Inbox</h1><p class="text-muted mb-0"><?php echo count($rows); ?> record(s) shown</p></div>
</div>

<div class="card content-card p-4 mb-4 no-print">
    <form class="row g-3 align-items-end" method="get">
        <div class="col-md-2"><label class="form-label">From Date</label><input class="form-control" type="date" name="from_date" value="<?php echo e($fromDate); ?>"></div>
        <div class="col-md-2"><label class="form-label">To Date</label><input class="form-control" type="date" name="to_date" value="<?php echo e($toDate); ?>"></div>
        <div class="col-md-2"><label class="form-label">Status</label><select class="form-select" name="status"><option value="">All</option><?php foreach (complaint_statuses() as $item): ?><option <?php echo $status === $item ? 'selected' : ''; ?>><?php echo e($item); ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><label class="form-label">Department</label><select class="form-select" name="department_id"><option value="">All</option><?php foreach ($departments as $item): ?><option value="<?php echo (int)$item['id']; ?>" <?php echo $departmentId === (int)$item['id'] ? 'selected' : ''; ?>><?php echo e($item['department_name']); ?></option><?php endforeach; ?></select></div>
        <div class="col-md-1"><label class="form-label">Zone</label><select class="form-select" name="zone_id"><option value="">All</option><?php foreach ($zones as $item): ?><option value="<?php echo (int)$item['id']; ?>" <?php echo $zoneId === (int)$item['id'] ? 'selected' : ''; ?>><?php echo e($item['zone_no']); ?></option><?php endforeach; ?></select></div>
        <div class="col-md-1"><label class="form-label">Ward</label><select class="form-select" name="ward_id"><option value="">All</option><?php foreach ($wards as $item): ?><option value="<?php echo (int)$item['id']; ?>" <?php echo $wardId === (int)$item['id'] ? 'selected' : ''; ?>><?php echo e($item['ward_no']); ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><label class="form-label">Search</label><input class="form-control" name="q" value="<?php echo e($search); ?>" placeholder="Reference, name, mobile"></div>
        <div class="col-12 d-flex gap-2"><button class="btn btn-primary">Apply Filters</button><a class="btn btn-outline-secondary" href="<?php echo e(url('employee/complaints.php')); ?>">Reset</a></div>
    </form>
</div>

<div class="card content-card p-4">
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-dark"><tr><th>Reference</th><th>Date</th><th>Citizen</th><th>Mobile</th><th>Department</th><th>Category</th><th>Zone</th><th>Ward</th><th>Officer</th><th>Status</th><th>Due</th><th>Action</th></tr></thead>
            <tbody>
                <?php if (!$rows): ?><tr><td colspan="12" class="text-center text-muted py-4">No complaints found.</td></tr><?php endif; ?>
                <?php foreach ($rows as $row): ?>
                    <?php $overdue = $row['due_at'] && strtotime($row['due_at']) < time() && !in_array($row['status'], ['Resolved','Closed','Rejected'], true); ?>
                    <tr class="<?php echo $overdue ? 'table-danger' : ''; ?>">
                        <td><?php echo e($row['reference_no']); ?></td>
                        <td><?php echo e(date('d-m-Y', strtotime($row['created_at']))); ?></td>
                        <td><?php echo e($row['citizen_name']); ?></td>
                        <td><?php echo e($row['mobile_no']); ?></td>
                        <td><?php echo e($row['department_name']); ?></td>
                        <td><?php echo e($row['category_name']); ?></td>
                        <td><?php echo e($row['zone_no']); ?></td>
                        <td><?php echo e($row['ward_no']); ?></td>
                        <td><?php echo e($row['officer_name'] ?: '-'); ?></td>
                        <td><span class="badge text-bg-<?php echo e(status_badge_class($row['status'])); ?>"><?php echo e($row['status']); ?></span></td>
                        <td><?php echo e($row['due_at'] ? date('d-m-Y', strtotime($row['due_at'])) : '-'); ?></td>
                        <td><a class="btn btn-sm btn-outline-primary" href="<?php echo e(url('employee/complaint-view.php?id=' . (int)$row['id'])); ?>">Open</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../includes/employee-footer.php'; ?>
