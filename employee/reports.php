<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

$user = require_permission('reports.view');
$pageTitle = 'Complaint Reports';

$fromDate = trim($_GET['from_date'] ?? date('Y-m-01'));
$toDate = trim($_GET['to_date'] ?? date('Y-m-d'));
$status = trim($_GET['status'] ?? '');
$departmentId = (int)($_GET['department_id'] ?? 0);
$zoneId = (int)($_GET['zone_id'] ?? 0);
$wardId = (int)($_GET['ward_id'] ?? 0);
$export = trim($_GET['export'] ?? '');

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

$sql = "SELECT c.reference_no, c.created_at, c.citizen_name, c.mobile_no,
               d.department_name, cc.category_name, z.zone_no, w.ward_no,
               c.status, c.source, c.due_at, c.resolved_at, o.officer_name,
               c.description_english
        FROM complaints c
        JOIN departments d ON d.id = c.department_id
        JOIN complaint_categories cc ON cc.id = c.category_id
        JOIN zones z ON z.id = c.zone_id
        JOIN wards w ON w.id = c.ward_id
        LEFT JOIN officers o ON o.id = c.assigned_officer_id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY c.created_at DESC";

$stmt = db()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

if ($export === 'excel') {
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="complaint_report_' . date('Ymd_His') . '.xls"');

    echo '<table border="1"><tr>';
    foreach (['Reference','Created Date','Citizen','Mobile','Department','Category','Zone','Ward','Status','Source','Due Date','Resolved Date','Officer','Complaint'] as $heading) {
        echo '<th>' . e($heading) . '</th>';
    }
    echo '</tr>';

    foreach ($rows as $row) {
        echo '<tr>';
        $values = [
            $row['reference_no'],
            $row['created_at'],
            $row['citizen_name'],
            "'" . $row['mobile_no'],
            $row['department_name'],
            $row['category_name'],
            $row['zone_no'],
            $row['ward_no'],
            $row['status'],
            $row['source'],
            $row['due_at'],
            $row['resolved_at'],
            $row['officer_name'],
            $row['description_english'],
        ];

        foreach ($values as $value) {
            $safe = (string)$value;
            if (preg_match('/^[=+\-@]/', $safe)) {
                $safe = "'" . $safe;
            }
            echo '<td>' . e($safe) . '</td>';
        }
        echo '</tr>';
    }

    echo '</table>';
    exit;
}

$departments = db()->query('SELECT id, department_name FROM departments WHERE is_active = 1 ORDER BY department_name')->fetchAll();
$zones = db()->query('SELECT id, zone_no FROM zones WHERE is_active = 1 ORDER BY zone_no')->fetchAll();
$wards = db()->query('SELECT id, ward_no FROM wards WHERE is_active = 1 ORDER BY ward_no')->fetchAll();

include __DIR__ . '/../includes/employee-header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div><h1 class="h3 fw-bold mb-1">Complaint Report</h1><p class="text-muted mb-0"><?php echo count($rows); ?> record(s)</p></div>
    <div class="d-flex gap-2 no-print">
        <?php
        $query = $_GET;
        $query['export'] = 'excel';
        ?>
        <a class="btn btn-success" href="?<?php echo e(http_build_query($query)); ?>">Excel</a>
        <button class="btn btn-danger" onclick="window.print()">Print / PDF</button>
    </div>
</div>

<div class="card content-card p-4 mb-4 no-print">
    <form class="row g-3 align-items-end" method="get">
        <div class="col-md-2"><label class="form-label">From Date</label><input class="form-control" type="date" name="from_date" value="<?php echo e($fromDate); ?>"></div>
        <div class="col-md-2"><label class="form-label">To Date</label><input class="form-control" type="date" name="to_date" value="<?php echo e($toDate); ?>"></div>
        <div class="col-md-2"><label class="form-label">Status</label><select class="form-select" name="status"><option value="">All</option><?php foreach (complaint_statuses() as $item): ?><option <?php echo $status === $item ? 'selected' : ''; ?>><?php echo e($item); ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><label class="form-label">Department</label><select class="form-select" name="department_id"><option value="">All</option><?php foreach ($departments as $item): ?><option value="<?php echo (int)$item['id']; ?>" <?php echo $departmentId === (int)$item['id'] ? 'selected' : ''; ?>><?php echo e($item['department_name']); ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><label class="form-label">Zone</label><select class="form-select" name="zone_id"><option value="">All</option><?php foreach ($zones as $item): ?><option value="<?php echo (int)$item['id']; ?>" <?php echo $zoneId === (int)$item['id'] ? 'selected' : ''; ?>><?php echo e($item['zone_no']); ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><label class="form-label">Ward</label><select class="form-select" name="ward_id"><option value="">All</option><?php foreach ($wards as $item): ?><option value="<?php echo (int)$item['id']; ?>" <?php echo $wardId === (int)$item['id'] ? 'selected' : ''; ?>><?php echo e($item['ward_no']); ?></option><?php endforeach; ?></select></div>
        <div class="col-12 d-flex gap-2"><button class="btn btn-primary">Apply Filters</button><a class="btn btn-outline-secondary" href="<?php echo e(url('employee/reports.php')); ?>">Reset</a></div>
    </form>
</div>

<div class="print-only text-center mb-3">
    <h1 class="h4">Smart City Tiruppur Complaint Report</h1>
    <p><?php echo e($fromDate); ?> to <?php echo e($toDate); ?></p>
</div>

<div class="card content-card p-3">
    <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle mb-0">
            <thead class="table-dark"><tr><th>Reference</th><th>Date</th><th>Citizen</th><th>Mobile</th><th>Department</th><th>Category</th><th>Zone</th><th>Ward</th><th>Status</th><th>Officer</th><th>Due</th></tr></thead>
            <tbody>
                <?php if (!$rows): ?><tr><td colspan="11" class="text-center text-muted py-4">No report data found.</td></tr><?php endif; ?>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?php echo e($row['reference_no']); ?></td>
                        <td><?php echo e(date('d-m-Y', strtotime($row['created_at']))); ?></td>
                        <td><?php echo e($row['citizen_name']); ?></td>
                        <td><?php echo e($row['mobile_no']); ?></td>
                        <td><?php echo e($row['department_name']); ?></td>
                        <td><?php echo e($row['category_name']); ?></td>
                        <td><?php echo e($row['zone_no']); ?></td>
                        <td><?php echo e($row['ward_no']); ?></td>
                        <td><?php echo e($row['status']); ?></td>
                        <td><?php echo e($row['officer_name'] ?: '-'); ?></td>
                        <td><?php echo e($row['due_at'] ? date('d-m-Y', strtotime($row['due_at'])) : '-'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../includes/employee-footer.php'; ?>
