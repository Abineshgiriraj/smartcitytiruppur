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
            $officerId = (int)($_POST['officer_id'] ?? 0);

            $roles = known_user_roles();

            if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8 || !in_array($role, $roles, true)) {
                throw new RuntimeException('Complete all required user fields. Password must contain at least 8 characters.');
            }

            db()->beginTransaction();

            $insertStmt = db()->prepare(
                'INSERT INTO users
                 (full_name, email, mobile_no, password_hash, role, department_id, zone_id, ward_id, is_active, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())'
            );
            $insertStmt->execute([
                $name,
                $email,
                $mobile !== '' ? $mobile : null,
                password_hash($password, PASSWORD_DEFAULT),
                $role,
                $departmentId ?: null,
                $zoneId ?: null,
                $wardId ?: null,
            ]);
            $newUserId = (int)db()->lastInsertId();

            if ($officerId > 0) {
                // Link to selected officer
                db()->prepare(
                    'UPDATE officers
                     SET user_id = ?,
                         email = CASE WHEN email IS NULL OR email = "" THEN ? ELSE email END,
                         mobile_no = CASE WHEN mobile_no IS NULL OR mobile_no = "" THEN ? ELSE mobile_no END
                     WHERE id = ?'
                )->execute([$newUserId, $email, $mobile !== '' ? $mobile : null, $officerId]);
            }

            db()->commit();
            flash('success', 'Employee user created successfully.');
        } elseif ($action === 'toggle_user') {
            $userId = (int)($_POST['user_id'] ?? 0);

            if ($userId <= 0 || $userId === (int)$currentUser['id']) {
                throw new RuntimeException('You cannot disable the current account.');
            }

            db()->exec('UPDATE users SET is_active = IF(is_active = 1, 0, 1) WHERE id = ' . $userId);
            flash('success', 'User status changed.');
        } elseif ($action === 'link_officer') {
            $userId = (int)($_POST['user_id'] ?? 0);
            $officerId = (int)($_POST['officer_id'] ?? 0);

            if ($userId <= 0 || $officerId <= 0) {
                throw new RuntimeException('Invalid user or officer selected.');
            }

            // Unlink any existing officer for this user
            db()->prepare('UPDATE officers SET user_id = NULL WHERE user_id = ?')->execute([$userId]);
            // Link selected officer
            db()->prepare('UPDATE officers SET user_id = ? WHERE id = ?')->execute([$userId, $officerId]);

            flash('success', 'Officer profile successfully linked to user.');
        }
    } catch (Throwable $exception) {
        if (db()->inTransaction()) {
            db()->rollBack();
        }
        flash('danger', APP_ENV === 'local' ? $exception->getMessage() : 'Unable to update user.');
    }

    redirect('employee/users.php');
}

$departments = db()->query('SELECT id, department_name FROM departments WHERE is_active = 1 ORDER BY department_name')->fetchAll();
$zones = db()->query('SELECT id, zone_no FROM zones WHERE is_active = 1 ORDER BY zone_no')->fetchAll();
$wards = db()->query('SELECT id, ward_no FROM wards WHERE is_active = 1 ORDER BY ward_no')->fetchAll();

// Available unlinked officers
$availableOfficers = db()->query('
    SELECT o.id, o.officer_name, o.designation, o.mobile_no, d.department_name
    FROM officers o
    LEFT JOIN officer_department_map dm ON dm.officer_id = o.id
    LEFT JOIN departments d ON d.id = dm.department_id
    WHERE o.is_active = 1 AND (o.user_id IS NULL OR o.user_id = 0)
    ORDER BY o.officer_name ASC
')->fetchAll();

$users = db()->query(
    'SELECT u.*, d.department_name, z.zone_no, w.ward_no,
            o.id as linked_officer_id, o.officer_name as linked_officer_name, o.designation as linked_designation
     FROM users u
     LEFT JOIN departments d ON d.id = u.department_id
     LEFT JOIN zones z ON z.id = u.zone_id
     LEFT JOIN wards w ON w.id = u.ward_id
     LEFT JOIN officers o ON o.user_id = u.id
     ORDER BY u.full_name'
)->fetchAll();

include __DIR__ . '/../includes/employee-header.php';
?>
<h1 class="h3 fw-bold mb-4">User Management</h1>
<div class="row g-4">
    <div class="col-xl-4">
        <form method="post" class="card content-card p-4 shadow-sm border-0">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="create_user">
            <h2 class="h5 fw-bold mb-3"><i class="bi bi-person-plus text-primary me-2"></i>Create Employee User</h2>
            
            <div class="mb-3">
                <label class="form-label required">Full Name</label>
                <input class="form-control" name="full_name" required placeholder="e.g. Officer Name">
            </div>
            <div class="mb-3">
                <label class="form-label required">Email</label>
                <input class="form-control" type="email" name="email" required placeholder="official@smartcitytiruppur.gov.in">
            </div>
            <div class="mb-3">
                <label class="form-label">Mobile Number</label>
                <input class="form-control" name="mobile_no" maxlength="10" placeholder="10-digit mobile number">
                <div class="form-text small">Used for mobile OTP login.</div>
            </div>
            <div class="mb-3">
                <label class="form-label required">Password</label>
                <input class="form-control" type="password" name="password" required minlength="8" placeholder="Minimum 8 characters">
            </div>
            <div class="mb-3">
                <label class="form-label required">Role</label>
                <select class="form-select" name="role" required id="userRoleSelect">
                    <option value="">Select Role</option>
                    <?php foreach (known_user_roles() as $role): ?>
                        <option value="<?php echo e($role); ?>" <?php echo $role === 'Field Officer' ? 'selected' : ''; ?>>
                            <?php echo e($role); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-semibold">Link to Officer Profile (Optional)</label>
                <select class="form-select" name="officer_id">
                    <option value="">None / Not a Field Officer</option>
                    <?php foreach ($availableOfficers as $item): ?>
                        <option value="<?php echo (int)$item['id']; ?>">
                            #<?php echo (int)$item['id']; ?> - <?php echo e($item['officer_name']); ?> (<?php echo e($item['designation']); ?><?php echo $item['department_name'] ? ' - ' . e($item['department_name']) : ''; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text small">Links this login account directly to their municipal officer profile.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Department Scope</label>
                <select class="form-select" name="department_id">
                    <option value="">None</option>
                    <?php foreach ($departments as $item): ?>
                        <option value="<?php echo (int)$item['id']; ?>"><?php echo e($item['department_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Zone Scope</label>
                <select class="form-select" name="zone_id">
                    <option value="">None</option>
                    <?php foreach ($zones as $item): ?>
                        <option value="<?php echo (int)$item['id']; ?>">Zone <?php echo e($item['zone_no']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Ward Scope</label>
                <select class="form-select" name="ward_id">
                    <option value="">None</option>
                    <?php foreach ($wards as $item): ?>
                        <option value="<?php echo (int)$item['id']; ?>">Ward <?php echo e($item['ward_no']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button class="btn btn-primary w-100"><i class="bi bi-check-circle me-1"></i> Create User</button>
        </form>
    </div>

    <div class="col-xl-8">
        <div class="card content-card p-4 shadow-sm border-0">
            <h2 class="h5 fw-bold mb-3"><i class="bi bi-people text-primary me-2"></i>Employee Accounts (<?php echo count($users); ?>)</h2>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Name &amp; Mobile</th>
                            <th>Email</th>
                            <th>Role &amp; Linked Officer</th>
                            <th>Scope</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $row): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold"><?php echo e($row['full_name']); ?></div>
                                    <small class="text-muted"><i class="bi bi-telephone me-1"></i><?php echo e($row['mobile_no'] ?: '-'); ?></small>
                                </td>
                                <td><?php echo e($row['email']); ?></td>
                                <td>
                                    <span class="badge bg-primary"><?php echo e($row['role']); ?></span>
                                    <?php if ($row['linked_officer_id']): ?>
                                        <div class="mt-1 small text-success fw-semibold">
                                            <i class="bi bi-link-45deg"></i> Officer #<?php echo (int)$row['linked_officer_id']; ?>: <?php echo e($row['linked_officer_name']); ?> (<?php echo e($row['linked_designation']); ?>)
                                        </div>
                                    <?php elseif (in_array($row['role'], ['Field Officer', 'Officer'], true)): ?>
                                        <div class="mt-1 small text-danger fw-semibold">
                                            <i class="bi bi-exclamation-circle me-1"></i> Not linked to officer profile
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo e($row['department_name'] ?: ''); ?> 
                                    <?php echo $row['zone_no'] ? 'Zone ' . e($row['zone_no']) : ''; ?> 
                                    <?php echo $row['ward_no'] ? 'Ward ' . e($row['ward_no']) : ''; ?>
                                </td>
                                <td>
                                    <span class="badge <?php echo (int)$row['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                                        <?php echo (int)$row['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ((int)$row['id'] !== (int)$currentUser['id']): ?>
                                        <form method="post" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="action" value="toggle_user">
                                            <input type="hidden" name="user_id" value="<?php echo (int)$row['id']; ?>">
                                            <button class="btn btn-sm btn-outline-secondary">Toggle</button>
                                        </form>
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
