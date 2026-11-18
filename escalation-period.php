<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Escalation Period';
$activePage = 'complaints';
$departmentId = (int)($_GET['department_id'] ?? 0);
$search = trim($_GET['q'] ?? '');

$departments = db()->query(
    'SELECT id, department_name FROM departments WHERE is_active = 1 ORDER BY department_name'
)->fetchAll();

$where = ['cc.is_active = 1', 'd.is_active = 1'];
$params = [];

if ($departmentId > 0) {
    $where[] = 'd.id = ?';
    $params[] = $departmentId;
}
if ($search !== '') {
    $where[] = '(d.department_name LIKE ? OR cc.category_name LIKE ?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

$sql = 'SELECT d.department_name, cc.category_name, cc.sla_days
        FROM complaint_categories cc
        JOIN departments d ON d.id = cc.department_id
        WHERE ' . implode(' AND ', $where) . '
        ORDER BY d.department_name, cc.category_name';

$stmt = db()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<section class="page-banner"><div class="container-xxl"><h1 class="fw-bold">Escalation Period</h1><p class="mb-0">Expected resolution time for each complaint category.</p></div></section>

<div class="container-xxl py-5">
    <div class="card content-card p-4 mb-4">
        <form class="row g-3 align-items-end" method="get">
            <div class="col-md-5">
                <label class="form-label">Department</label>
                <select class="form-select" name="department_id">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $department): ?>
                        <option value="<?php echo (int)$department['id']; ?>" <?php echo $departmentId === (int)$department['id'] ? 'selected' : ''; ?>>
                            <?php echo e($department['department_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">Search</label>
                <input class="form-control" name="q" value="<?php echo e($search); ?>" placeholder="Department or category">
            </div>
            <div class="col-md-2"><button class="btn btn-primary w-100">Apply</button></div>
        </form>
    </div>

    <div class="card content-card p-4">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-dark"><tr><th>S.No</th><th>Department</th><th>Complaint Category</th><th>Resolution Days</th></tr></thead>
                <tbody>
                    <?php if (!$rows): ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">No escalation master data found.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($rows as $index => $row): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo e($row['department_name']); ?></td>
                            <td><?php echo e($row['category_name']); ?></td>
                            <td><?php echo (int)$row['sla_days']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
