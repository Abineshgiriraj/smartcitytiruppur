<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
$user = require_roles(['Super Admin','Admin','Commissioner','Department Head']);
require_permission('officers.view');
$pageTitle = 'Officer Directory';
$search = trim((string)($_GET['q'] ?? ''));
$search = mb_substr($search, 0, 100);
$params = [];
$where = ['1=1'];
if ($search !== '') {
    $where[] = '(o.officer_name LIKE ? OR o.designation LIKE ?)';
    $params = ['%' . $search . '%', '%' . $search . '%'];
}
if ($user['role'] === 'Department Head') {
    $where[] = 'EXISTS (SELECT 1 FROM officer_department_map m WHERE m.officer_id=o.id AND m.department_id=?)';
    $params[] = (int)($user['department_id'] ?? 0);
}
$statement = db()->prepare(
    'SELECT o.id,o.officer_name,o.designation,o.is_active,
      (SELECT COUNT(*) FROM officer_department_map m WHERE m.officer_id=o.id) AS departments,
      (SELECT COUNT(*) FROM officer_category_map m WHERE m.officer_id=o.id) AS categories,
      (SELECT COUNT(*) FROM officer_ward_map m WHERE m.officer_id=o.id) AS wards
     FROM officers o WHERE ' . implode(' AND ', $where) . ' ORDER BY o.officer_name LIMIT 200'
);
$statement->execute($params);
$officers = $statement->fetchAll();
include __DIR__ . '/../includes/employee-header.php';
?>
<div class="admin-heading">
 <div><span class="admin-eyebrow">Organization</span><h1 class="fw-bold mb-1">Officer Directory</h1><p>Current officer master records and assignment coverage from Smart City Tiruppur.</p></div>
 <?php if (in_array($user['role'],['Super Admin','Admin'],true)): ?>
 <a class="btn btn-primary" href="<?php echo e(url('employee/masters.php?section=officers')); ?>"><i class="bi bi-person-plus me-1"></i> Manage Officers</a>
 <?php endif; ?>
</div>
<div class="alert alert-info border-0"><i class="bi bi-info-circle me-2"></i>Officer designations may be blank. Escalation roles must be verified separately before automatic escalation is enabled.</div>
<div class="card content-card p-4">
  <form method="get" class="row g-2 mb-4">
    <div class="col-lg-6"><label for="officerSearch" class="form-label">Find officer</label><input class="form-control" id="officerSearch" name="q" value="<?php echo e($search); ?>" placeholder="Search by name or designation"></div>
    <div class="col-auto align-self-end"><button class="btn btn-primary" type="submit"><i class="bi bi-search me-1"></i> Search</button></div>
    <div class="col-auto align-self-end"><a class="btn btn-outline-secondary" href="<?php echo e(url('employee/officers.php')); ?>">Clear</a></div>
  </form>
  <div class="table-responsive">
  <table class="table table-hover align-middle mb-0"><thead><tr><th>ID</th><th>Officer</th><th>Designation</th><th>Departments</th><th>Categories</th><th>Wards</th><th>Status</th></tr></thead><tbody>
  <?php if (!$officers): ?><tr><td colspan="7" class="admin-empty">No matching officers.</td></tr><?php endif; ?>
  <?php foreach ($officers as $row): ?><tr>
    <td><?php echo (int)$row['id']; ?></td><td class="fw-semibold"><?php echo e($row['officer_name']); ?></td>
    <td><?php echo $row['designation'] ? e($row['designation']) : '<span class="text-warning">Not recorded</span>'; ?></td>
    <td><?php echo (int)$row['departments']; ?></td><td><?php echo (int)$row['categories']; ?></td><td><?php echo (int)$row['wards']; ?></td>
    <td><span class="badge <?php echo (int)$row['is_active'] === 1 ? 'text-bg-success' : 'text-bg-secondary'; ?>"><?php echo (int)$row['is_active'] === 1 ? 'Active' : 'Inactive'; ?></span></td>
  </tr><?php endforeach; ?>
  </tbody></table>
  </div><p class="text-muted small mb-0 mt-3">Showing up to 200 records. This page does not import staff from PGP.</p>
</div>
<?php include __DIR__ . '/../includes/employee-footer.php'; ?>
