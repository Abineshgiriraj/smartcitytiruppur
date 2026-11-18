<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Department Contacts';
$activePage = 'contacts';

$departmentId = (int)($_GET['department_id'] ?? 0);
$officerId = (int)($_GET['officer_id'] ?? 0);

$departments = db()->query('SELECT id, department_name FROM departments WHERE is_active = 1 ORDER BY department_name')->fetchAll();

$officersQuery = 'SELECT DISTINCT o.id, o.officer_name FROM officers o JOIN officer_department_map odm ON odm.officer_id = o.id WHERE o.is_active = 1';
$officerParams = [];
if ($departmentId > 0) {
    $officersQuery .= ' AND odm.department_id = ?';
    $officerParams[] = $departmentId;
}
$officersQuery .= ' ORDER BY o.officer_name';
$stmt = db()->prepare($officersQuery);
$stmt->execute($officerParams);
$officers = $stmt->fetchAll();

$where = ['o.is_active = 1', 'd.is_active = 1'];
$params = [];
if ($departmentId > 0) {
    $where[] = 'd.id = ?';
    $params[] = $departmentId;
}
if ($officerId > 0) {
    $where[] = 'o.id = ?';
    $params[] = $officerId;
}

$sql = "SELECT d.department_name, o.officer_name, o.mobile_no,
            GROUP_CONCAT(DISTINCT z.zone_no ORDER BY CAST(z.zone_no AS UNSIGNED) SEPARATOR ', ') AS zones,
            GROUP_CONCAT(DISTINCT w.ward_no ORDER BY CAST(w.ward_no AS UNSIGNED) SEPARATOR ', ') AS wards
     FROM officers o
     JOIN officer_department_map odm ON odm.officer_id = o.id
     JOIN departments d ON d.id = odm.department_id
     LEFT JOIN officer_ward_map owm ON owm.officer_id = o.id
     LEFT JOIN wards w ON w.id = owm.ward_id
     LEFT JOIN zones z ON z.id = w.zone_id
     WHERE " . implode(' AND ', $where) . "
     GROUP BY d.department_name, o.id
     ORDER BY d.department_name, o.officer_name";

$stmt = db()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<section class="page-banner"><div class="container-xxl"><h1 class="fw-bold">Department Contacts</h1><p class="mb-0">Municipal department officers and service areas.</p></div></section>

<div class="container-xxl py-5">
    <div class="card content-card p-4 mb-4">
        <form class="row g-3 align-items-end" method="get">
            <div class="col-md-5">
                <label class="form-label">Department</label>
                <select class="form-select" name="department_id" onchange="this.form.submit()">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $department): ?>
                        <option value="<?php echo (int)$department['id']; ?>" <?php echo $departmentId === (int)$department['id'] ? 'selected' : ''; ?>>
                            <?php echo e($department['department_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">Officer</label>
                <select class="form-select" name="officer_id">
                    <option value="">All Officers</option>
                    <?php foreach ($officers as $officer): ?>
                        <option value="<?php echo (int)$officer['id']; ?>" <?php echo $officerId === (int)$officer['id'] ? 'selected' : ''; ?>>
                            <?php echo e($officer['officer_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-primary w-100">Search</button></div>
        </form>
    </div>

    <div class="card content-card p-4">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-dark"><tr><th>Department</th><th>Officer</th><th>Zone</th><th>Ward</th><th>Mobile</th></tr></thead>
                <tbody>
                    <?php if (!$rows): ?><tr><td colspan="5" class="text-center text-muted py-4">No officer records found.</td></tr><?php endif; ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?php echo e($row['department_name']); ?></td>
                            <td><?php echo e($row['officer_name']); ?></td>
                            <td><?php echo e($row['zones'] ?: '-'); ?></td>
                            <td><?php echo e($row['wards'] ?: '-'); ?></td>
                            <td><?php echo e($row['mobile_no'] ?: '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
