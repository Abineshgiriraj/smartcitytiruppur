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

            $isOfficerRole = in_array($role, ['Field Officer', 'Officer'], true);
            if ($name === '' || !in_array($role, $roles, true)
                || !preg_match('/^[6-9][0-9]{9}$/D', $mobile)
                || ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL))
                || (!$isOfficerRole && strlen($password) < 8)
                || ($isOfficerRole && $password !== '' && strlen($password) < 8)) {
                throw new RuntimeException('Enter a name, a unique valid 10-digit mobile and an allowed role. Email is optional. Password, when supplied, needs 8+ characters.');
            }
            if ($isOfficerRole && $officerId <= 0) {
                throw new RuntimeException('Field Officers must be linked to an existing active officer profile.');
            }
            if ($email === '' && !user_email_nullable(db())) {
                throw new RuntimeException('Optional email requires the reviewed database migration: database/migrations/2026_11_phone_only_officer_login.sql');
            }
            $usedPhone = db()->prepare('SELECT id FROM users WHERE mobile_no=? LIMIT 1');
            $usedPhone->execute([$mobile]);
            if ($usedPhone->fetchColumn()) throw new RuntimeException('This phone number already has a login account.');
            if ($email !== '') {
                $usedEmail = db()->prepare('SELECT id FROM users WHERE email=? LIMIT 1');
                $usedEmail->execute([$email]);
                if ($usedEmail->fetchColumn()) throw new RuntimeException('This email already has a login account.');
            }

            db()->beginTransaction();
            if ($isOfficerRole) {
                $check = db()->prepare('SELECT id, user_id, mobile_no FROM officers WHERE id=? AND is_active=1 FOR UPDATE');
                $check->execute([$officerId]);
                $masterOfficer = $check->fetch(PDO::FETCH_ASSOC);
                if (!$masterOfficer || (int)$masterOfficer['user_id'] > 0) {
                    throw new RuntimeException('The officer is inactive, missing, or already linked to a login.');
                }
                $recordedPhone = normalize_mobile((string)$masterOfficer['mobile_no']);
                if ($recordedPhone !== $mobile) {
                    throw new RuntimeException('Mobile number must match the officer directory. Correct and verify the master contact before creating access.');
                }
            } elseif ($officerId > 0) {
                throw new RuntimeException('Officer profiles can only be linked to Officer / Field Officer roles.');
            }
            // OTP-only accounts get an unguessable, undisclosed password hash.
            // Never generate a shared password, fictitious email or phone number.
            $passwordHash = password_hash($password !== '' ? $password : bin2hex(random_bytes(32)), PASSWORD_DEFAULT);

            $insertStmt = db()->prepare(
                'INSERT INTO users
                 (full_name, email, mobile_no, password_hash, role, department_id, zone_id, ward_id, is_active, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())'
            );
            $insertStmt->execute([
                $name,
                $email !== '' ? $email : null,
                $mobile,
                $passwordHash,
                $role,
                $departmentId ?: null,
                $zoneId ?: null,
                $wardId ?: null,
            ]);
            $newUserId = (int)db()->lastInsertId();

            if ($officerId > 0) {
                // Keep the municipality's master contact values unchanged.
                $link = db()->prepare('UPDATE officers SET user_id=? WHERE id=? AND is_active=1 AND user_id IS NULL');
                $link->execute([$newUserId, $officerId]);
                if ($link->rowCount() !== 1) throw new RuntimeException('Officer link changed; retry account creation.');
            }

            db()->commit();
            flash('success', 'Employee user created successfully.');
        } elseif ($action === 'provision_officer_batch') {
            // Explicit opt-in, small reviewed batches. No database changes on GET.
            if (!user_email_nullable(db())) {
                throw new RuntimeException('Apply the reviewed nullable-email migration before phone-only provisioning.');
            }
            $afterId = max(0, (int)($_POST['after_officer_id'] ?? 0));
            $batch = db()->prepare(
                "SELECT id,officer_name,mobile_no FROM officers
                 WHERE id > ? AND is_active=1 AND user_id IS NULL
                 ORDER BY id LIMIT 100"
            );
            $batch->execute([$afterId]);
            $rows = $batch->fetchAll(PDO::FETCH_ASSOC);
            $allPhones = db()->query(
                "SELECT mobile_no,COUNT(*) AS n FROM officers
                 WHERE is_active=1 AND mobile_no REGEXP '^[6-9][0-9]{9}$'
                 GROUP BY mobile_no HAVING COUNT(*)>1"
            )->fetchAll(PDO::FETCH_KEY_PAIR);
            $existingPhones = db()->query(
                "SELECT mobile_no FROM users WHERE mobile_no IS NOT NULL AND mobile_no<>''"
            )->fetchAll(PDO::FETCH_COLUMN);
            $claimed = array_fill_keys($existingPhones, true);
            $created=0; $skipped=0; $last=$afterId;
            foreach($rows as $row) {
                $last=(int)$row['id'];
                $mobile=normalize_mobile((string)$row['mobile_no']);
                if(!preg_match('/^[6-9][0-9]{9}$/D',$mobile)
                    || (string)$row['mobile_no'] !== $mobile
                    || isset($allPhones[$mobile]) || isset($claimed[$mobile])) {
                    $skipped++;
                    continue;
                }
                try {
                    db()->beginTransaction();
                    $lock=db()->prepare('SELECT user_id FROM officers WHERE id=? AND is_active=1 FOR UPDATE');
                    $lock->execute([$last]);
                    $linked=$lock->fetchColumn();
                    if($linked===false || $linked!==null)throw new RuntimeException('Officer already linked.');
                    $check=db()->prepare('SELECT 1 FROM users WHERE mobile_no=? LIMIT 1');
                    $check->execute([$mobile]);
                    if($check->fetchColumn())throw new RuntimeException('Mobile already used.');
                    $newPassword=password_hash(bin2hex(random_bytes(32)),PASSWORD_DEFAULT);
                    $insert=db()->prepare(
                        "INSERT INTO users (full_name,email,mobile_no,password_hash,role,is_active,created_at,updated_at)
                         VALUES (?,?,?,?, 'Field Officer',1,NOW(),NOW())"
                    );
                    $insert->execute([(string)$row['officer_name'],null,$mobile,$newPassword]);
                    $newId=(int)db()->lastInsertId();
                    $up=db()->prepare('UPDATE officers SET user_id=? WHERE id=? AND user_id IS NULL');
                    $up->execute([$newId,$last]);
                    if($up->rowCount()!==1)throw new RuntimeException('Concurrent officer link.');
                    db()->commit();
                    $claimed[$mobile]=true;
                    $created++;
                } catch (Throwable $batchError) {
                    if(db()->inTransaction())db()->rollBack();
                    $skipped++;
                    log_message('Officer account provisioning skipped officer '.$last.': '.$batchError->getMessage());
                }
            }
            audit_log((int)$currentUser['id'],'officer_batch_provision','officer',null,
                ['created'=>$created,'skipped'=>$skipped,'last_officer_id'=>$last]);
            flash('success',"Created {$created} OTP-only officer logins; skipped {$skipped}. Last reviewed officer ID: {$last}. Continue with the next batch when ready.");
            redirect('employee/users.php?after_officer_id='.$last);
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

            db()->beginTransaction();
            $u=db()->prepare('SELECT role,mobile_no FROM users WHERE id=? AND is_active=1 FOR UPDATE');
            $u->execute([$userId]);
            $existingUser=$u->fetch(PDO::FETCH_ASSOC);
            $o=db()->prepare('SELECT user_id,mobile_no FROM officers WHERE id=? AND is_active=1 FOR UPDATE');
            $o->execute([$officerId]);
            $targetOfficer=$o->fetch(PDO::FETCH_ASSOC);
            if(!$existingUser || !in_array($existingUser['role'],['Field Officer','Officer'],true)
                || !$targetOfficer || $targetOfficer['user_id'] !== null
                || !preg_match('/^[6-9][0-9]{9}$/D',(string)$existingUser['mobile_no'])
                || normalize_mobile((string)$targetOfficer['mobile_no']) !== (string)$existingUser['mobile_no']) {
                throw new RuntimeException('Officer link denied: account must be a field officer, and both mobile numbers must match with no existing link.');
            }
            db()->prepare('UPDATE officers SET user_id=NULL WHERE user_id=?')->execute([$userId]);
            $link=db()->prepare('UPDATE officers SET user_id=? WHERE id=? AND user_id IS NULL');
            $link->execute([$userId,$officerId]);
            if($link->rowCount()!==1)throw new RuntimeException('Could not reserve officer link.');
            db()->commit();
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

$afterBatchId=max(0,(int)($_GET['after_officer_id']??0));
$officerAccessCounts=db()->query(
    "SELECT COUNT(*) AS total,
       COALESCE(SUM(user_id IS NOT NULL),0) AS linked
     FROM officers WHERE is_active=1"
)->fetch(PDO::FETCH_ASSOC);
$emailOptionalReady=user_email_nullable(db());

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
<h1 class="h3 fw-bold mb-3">Employee Access</h1>
<div class="card content-card border-0 p-4 mb-4">
    <div class="d-flex flex-wrap justify-content-between gap-3 align-items-center">
        <div>
            <h2 class="h5 fw-bold mb-1"><i class="bi bi-phone me-2"></i>Give officers mobile login access</h2>
            <p class="mb-1 text-muted">Active officers: <strong><?php echo (int)$officerAccessCounts['total']; ?></strong>.
               Linked accounts: <strong><?php echo (int)$officerAccessCounts['linked']; ?></strong>.
               Missing links: <strong><?php echo (int)$officerAccessCounts['total']-(int)$officerAccessCounts['linked']; ?></strong>.</p>
            <p class="small text-muted mb-0">Create up to 100 verified, unique-number accounts per batch. Duplicate or missing mobile numbers are skipped. No email or shared password is invented.</p>
        </div>
        <form method="post">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="provision_officer_batch">
            <input type="hidden" name="after_officer_id" value="<?php echo $afterBatchId; ?>">
            <button class="btn btn-primary" <?php echo !$emailOptionalReady ? 'disabled' : ''; ?> onclick="return confirm('Create OTP-only logins for the next 100 eligible officer records? Verify the phone directory first.');">
                <i class="bi bi-person-check me-1"></i> Create Next 100 Officer Logins
            </button>
        </form>
    </div>
    <?php if (!$emailOptionalReady): ?>
      <div class="alert alert-warning mt-3 mb-0">The database still requires email. Review and apply <code>database/migrations/2026_11_phone_only_officer_login.sql</code> before creating phone-only accounts.</div>
    <?php endif; ?>
    <?php if ($afterBatchId > 0): ?>
      <div class="mt-2 small">Batch cursor: officer #<?php echo $afterBatchId; ?>.
       <a href="<?php echo e(url('employee/users.php')); ?>">Start from first eligible officer</a></div>
    <?php endif; ?>
</div>
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
                <label class="form-label">Email (optional)</label>
                <input class="form-control" type="email" name="email" placeholder="Only when the officer has a real email">
                <div class="form-text">No fake email addresses. Mobile OTP works without an email.</div>
            </div>
            <div class="mb-3">
                <label class="form-label required">Mobile Number</label>
                <input class="form-control" type="tel" name="mobile_no" maxlength="10" pattern="[6-9][0-9]{9}" required inputmode="numeric" placeholder="Verified 10-digit mobile number">
                <div class="form-text small">Used for mobile OTP login.</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Password (optional for officers)</label>
                <input class="form-control" type="password" name="password" minlength="8" placeholder="Officers can use OTP only">
                <div class="form-text">Password is required for non-officer roles. Leave blank for secure OTP-only officer access.</div>
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
                <label class="form-label fw-semibold">Link to Officer Profile (required for officers)</label>
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
            <h2 class="h5 fw-bold mb-3"><i class="bi bi-people text-primary me-2"></i>Recent Employee Accounts (up to 100)</h2>
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
                                <td><?php echo e($row['email'] ?: '— Mobile login only'); ?></td>
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