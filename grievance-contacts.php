<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Grievance Redressal Contacts';
$activePage = 'contacts';
$departmentId = (int)($_GET['department_id'] ?? 0);
$categoryId = (int)($_GET['category_id'] ?? 0);

$departments = db()->query('SELECT id, department_name FROM departments WHERE is_active = 1 ORDER BY department_name')->fetchAll();

$categories = [];
if ($departmentId > 0) {
    $stmt = db()->prepare('SELECT id, category_name FROM complaint_categories WHERE department_id = ? AND is_active = 1 ORDER BY category_name');
    $stmt->execute([$departmentId]);
    $categories = $stmt->fetchAll();
}

$where = ['o.is_active = 1'];
$params = [];
if ($departmentId > 0) {
    $where[] = 'd.id = ?';
    $params[] = $departmentId;
}
if ($categoryId > 0) {
    $where[] = 'cc.id = ?';
    $params[] = $categoryId;
}

$sql = "SELECT d.department_name, cc.category_name, o.officer_name, o.mobile_no,
               GROUP_CONCAT(DISTINCT w.ward_no ORDER BY CAST(w.ward_no AS UNSIGNED) SEPARATOR ', ') AS wards
        FROM officers o
        JOIN officer_category_map ocm ON ocm.officer_id = o.id
        JOIN complaint_categories cc ON cc.id = ocm.category_id
        JOIN departments d ON d.id = cc.department_id
        LEFT JOIN officer_ward_map owm ON owm.officer_id = o.id
        LEFT JOIN wards w ON w.id = owm.ward_id
        WHERE " . implode(' AND ', $where) . "
        GROUP BY d.department_name, cc.category_name, o.id
        ORDER BY d.department_name, cc.category_name, o.officer_name";

$stmt = db()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<section class="page-banner"><div class="container-xxl"><h1 class="fw-bold">Grievance Redressal Contacts</h1><p class="mb-0">Find officers responsible for a complaint category.</p></div></section>

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
                <label class="form-label">Complaint Category</label>
                <select class="form-select" name="category_id">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo (int)$category['id']; ?>" <?php echo $categoryId === (int)$category['id'] ? 'selected' : ''; ?>>
                            <?php echo e($category['category_name']); ?>
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
                <thead class="table-dark"><tr><th>Department</th><th>Category</th><th>Ward</th><th>Officer</th><th>Mobile</th></tr></thead>
                <tbody>
                    <?php if (!$rows): ?><tr><td colspan="5" class="text-center text-muted py-4">No grievance contact mapping found.</td></tr><?php endif; ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?php echo e($row['department_name']); ?></td>
                            <td><?php echo e($row['category_name']); ?></td>
                            <td><?php echo e($row['wards'] ?: '-'); ?></td>
                            <td><?php echo e($row['officer_name']); ?></td>
                            <td><?php echo e($row['mobile_no'] ?: '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
