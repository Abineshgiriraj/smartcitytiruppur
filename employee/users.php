<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

$currentUser = require_roles(['Super Admin', 'Admin']);
require_permission('users.view');
$pageTitle = 'User Management';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_permission('users.manage');
    verify_csrf();
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create_user') {
            $name = trim($_POST['full_name'] ?? '');
            $email = strtolower(trim($_POST['email'] ?? ''));
            $mobile = normalize_mobile($_POST['mobile_no'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = trim($_POST['role'] ?? '');
            $departmentId = (int)($_POST['department_id'] ?? 0);
            $zoneId = (int)($_POST['zone_id'] ?? 0);
            $wardId = (int)($_POST['ward_id'] ?? 0);

            $roles = ['Super Admin','Commissioner','Admin','Department Head','Zone Officer','Ward Officer','Call Centre','Field Officer','Report User'];

            if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8 || !in_array($role, $roles, true)) {
                throw new RuntimeException('Complete all required user fields. Password must contain at least 8 characters.');
            }

            db()->prepare(
                'INSERT INTO users
                 (full_name, email, mobile_no, password_hash, role, department_id, zone_id, ward_id, is_active, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())'
            )->execute([
                $name,
                $email,
                $mobile !== '' ? $mobile : null,
                password_hash($password, PASSWORD_DEFAULT),
                $role,
                $departmentId ?: null,
                $zoneId ?: null,
                $wardId ?: null,
            ]);

            flash('success', 'Employee user created.');
        } elseif ($action === 'toggle_user') {
            $userId = (int)($_POST['user_id'] ?? 0);

            if ($userId <= 0 || $userId === (int)$currentUser['id']) {
                throw new RuntimeException('You cannot disable the current account.');
            }

            db()->exec('UPDATE users SET is_active = IF(is_active = 1, 0, 1) WHERE id = ' . $userId);
            flash('success', 'User status changed.');
        }
    } catch (Throwable $exception) {
        flash('danger', APP_ENV === 'local' ? $exception->getMessage() : 'Unable to update user.');
    }

    redirect('employee/users.php');
}

$departments = db()->query('SELECT id, department_name FROM departments WHERE is_active = 1 ORDER BY department_name')->fetchAll();
$zones = db()->query('SELECT id, zone_no FROM zones WHERE is_active = 1 ORDER BY zone_no')->fetchAll();
$wards = db()->query('SELECT id, ward_no FROM wards WHERE is_active = 1 ORDER BY ward_no')->fetchAll();
$users = db()->query(
    'SELECT u.*, d.department_name, z.zone_no, w.ward_no
     FROM users u
     LEFT JOIN departments d ON d.id = u.department_id
     LEFT JOIN zones z ON z.id = u.zone_id
     LEFT JOIN wards w ON w.id = u.ward_id
     ORDER BY u.full_name'
)->fetchAll();

include __DIR__ . '/../includes/employee-header.php';
?>
<h1 class="h3 fw-bold mb-4">User Management</h1>
<div class="row g-4">
    <div class="col-xl-4">
        <form method="post" class="card content-card p-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="create_user">
            <h2 class="h5 fw-bold mb-3">Create Employee User</h2>
            <div class="mb-3"><label class="form-label required">Full Name</label><input class="form-control" name="full_name" required></div>
            <div class="mb-3"><label class="form-label required">Email</label><input class="form-control" type="email" name="email" required></div>
            <div class="mb-3"><label class="form-label">Mobile</label><input class="form-control" name="mobile_no" maxlength="10"></div>
            <div class="mb-3"><label class="form-label required">Password</label><input class="form-control" type="password" name="password" required></div>
            <div class="mb-3"><label class="form-label required">Role</label><select class="form-select" name="role" required><option value="">Select</option><?php foreach (['Super Admin','Commissioner','Admin','Department Head','Zone Officer','Ward Officer','Call Centre','Field Officer','Report User'] as $role): ?><option><?php echo e($role); ?></option><?php endforeach; ?></select></div>
            <div class="mb-3"><label class="form-label">Department Scope</label><select class="form-select" name="department_id"><option value="">None</option><?php foreach ($departments as $item): ?><option value="<?php echo (int)$item['id']; ?>"><?php echo e($item['department_name']); ?></option><?php endforeach; ?></select></div>
            <div class="mb-3"><label class="form-label">Zone Scope</label><select class="form-select" name="zone_id"><option value="">None</option><?php foreach ($zones as $item): ?><option value="<?php echo (int)$item['id']; ?>">Zone <?php echo e($item['zone_no']); ?></option><?php endforeach; ?></select></div>
            <div class="mb-3"><label class="form-label">Ward Scope</label><select class="form-select" name="ward_id"><option value="">None</option><?php foreach ($wards as $item): ?><option value="<?php echo (int)$item['id']; ?>">Ward <?php echo e($item['ward_no']); ?></option><?php endforeach; ?></select></div>
            <button class="btn btn-primary">Create User</button>
        </form>
    </div>
    <div class="col-xl-8">
        <div class="card content-card p-4">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark"><tr><th>Name</th><th>Email</th><th>Role</th><th>Scope</th><th>Last Login</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php foreach ($users as $row): ?>
                            <tr>
                                <td><?php echo e($row['full_name']); ?></td>
                                <td><?php echo e($row['email']); ?></td>
                                <td><?php echo e($row['role']); ?></td>
                                <td><?php echo e($row['department_name'] ?: ''); ?> <?php echo $row['zone_no'] ? 'Zone ' . e($row['zone_no']) : ''; ?> <?php echo $row['ward_no'] ? 'Ward ' . e($row['ward_no']) : ''; ?></td>
                                <td><?php echo e($row['last_login_at'] ?: '-'); ?></td>
                                <td><?php echo (int)$row['is_active'] ? 'Active' : 'Inactive'; ?></td>
                                <td>
                                    <?php if ((int)$row['id'] !== (int)$currentUser['id']): ?>
                                        <form method="post"><?php echo csrf_field(); ?><input type="hidden" name="action" value="toggle_user"><input type="hidden" name="user_id" value="<?php echo (int)$row['id']; ?>"><button class="btn btn-sm btn-outline-secondary">Toggle</button></form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/employee-footer.php'; ?>
