<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/officer-reconciliation.php';

$currentUser = require_roles(['Super Admin', 'Admin']);
require_permission('users.view');
$pageTitle = 'Users & Roles Management';
$errors = [];

$isSuperAdmin = ($currentUser['role'] === 'Super Admin');
$emailOptionalReady = user_email_nullable(db());

// -------------------------------------------------------------
// Handle Single User Creation & Actions
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_permission('users.manage');
    verify_csrf();
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create_user') {
            $name = trim($_POST['full_name'] ?? '');
            $rawEmail = strtolower(trim($_POST['email'] ?? ''));
            $mobile = normalize_mobile($_POST['mobile_no'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = trim($_POST['role'] ?? '');
            $departmentId = (int)($_POST['department_id'] ?? 0);
            $zoneId = (int)($_POST['zone_id'] ?? 0);
            $wardId = (int)($_POST['ward_id'] ?? 0);
            $officerId = (int)($_POST['officer_id'] ?? 0);

            $roles = known_user_roles();
            $isOfficerRole = in_array($role, ['Field Officer', 'Officer'], true);

            // 1. Validation rules
            if ($name === '') {
                throw new RuntimeException('Full Name is required.');
            }
            if (!in_array($role, $roles, true)) {
                throw new RuntimeException('Please select a valid user role.');
            }
            if (!preg_match('/^[6-9][0-9]{9}$/D', $mobile)) {
                throw new RuntimeException('A valid 10-digit mobile number starting with 6, 7, 8, or 9 is mandatory.');
            }

            // 2. Email validation (optional)
            $email = null;
            if ($rawEmail !== '') {
                if (!filter_var($rawEmail, FILTER_VALIDATE_EMAIL)) {
                    throw new RuntimeException('The entered email address is invalid.');
                }
                $emailStmt = db()->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
                $emailStmt->execute([$rawEmail]);
                if ($emailStmt->fetchColumn()) {
                    throw new RuntimeException('This email address is already registered to another user account.');
                }
                $email = $rawEmail;
            }

            // 3. Mobile uniqueness check
            $phoneStmt = db()->prepare('SELECT id FROM users WHERE mobile_no = ? LIMIT 1');
            $phoneStmt->execute([$mobile]);
            if ($phoneStmt->fetchColumn()) {
                throw new RuntimeException('This mobile number is already registered to an existing login account.');
            }

            // 4. Password validation: Required for administrative roles; optional for officers (defaults to secure OTP hash)
            if (!$isOfficerRole && strlen($password) < 8) {
                throw new RuntimeException('A password of at least 8 characters is required for administrative/staff accounts.');
            }
            if ($isOfficerRole && $password !== '' && strlen($password) < 8) {
                throw new RuntimeException('If supplying a password for an officer, it must contain at least 8 characters.');
            }

            // 5. Officer profile link requirement
            if ($isOfficerRole) {
                if ($officerId <= 0) {
                    throw new RuntimeException('Please select an active municipal officer profile to link to this Field Officer account.');
                }
                $checkOfficer = db()->prepare('
                    SELECT o.id, o.user_id, dm.department_id 
                    FROM officers o 
                    LEFT JOIN officer_department_map dm ON dm.officer_id = o.id 
                    WHERE o.id = ? AND o.is_active = 1 
                    LIMIT 1
                ');
                $checkOfficer->execute([$officerId]);
                $masterOfficer = $checkOfficer->fetch(PDO::FETCH_ASSOC);

                if (!$masterOfficer) {
                    throw new RuntimeException('Selected officer profile is inactive or could not be found.');
                }
                if ($masterOfficer['user_id'] !== null) {
                    throw new RuntimeException('Selected officer profile is already linked to another user account.');
                }
                if (!$departmentId && $masterOfficer['department_id']) {
                    $departmentId = (int)$masterOfficer['department_id'];
                }
            }

            $passwordHash = password_hash($password !== '' ? $password : bin2hex(random_bytes(32)), PASSWORD_DEFAULT);

            db()->beginTransaction();

            $insertStmt = db()->prepare(
                'INSERT INTO users
                 (full_name, email, mobile_no, password_hash, role, department_id, zone_id, ward_id, is_active, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())'
            );
            $insertStmt->execute([
                $name,
                $email,
                $mobile,
                $passwordHash,
                $role,
                $departmentId ?: null,
                $zoneId ?: null,
                $wardId ?: null,
            ]);
            $newUserId = (int)db()->lastInsertId();

            if ($isOfficerRole && $officerId > 0) {
                $link = db()->prepare('UPDATE officers SET user_id = ? WHERE id = ? AND user_id IS NULL');
                $link->execute([$newUserId, $officerId]);
                if ($link->rowCount() !== 1) {
                    throw new RuntimeException('Failed to reserve officer profile link; please try again.');
                }
            }

            db()->commit();
            flash('success', "Account for '{$name}' created successfully." . ($officerId > 0 ? " Linked to Officer #{$officerId}." : ""));
        } elseif ($action === 'toggle_user') {
            $userId = (int)($_POST['user_id'] ?? 0);

            if ($userId <= 0 || $userId === (int)$currentUser['id']) {
                throw new RuntimeException('You cannot disable the current active administrator account.');
            }

            db()->exec('UPDATE users SET is_active = IF(is_active = 1, 0, 1) WHERE id = ' . $userId);
            flash('success', 'User account status updated.');
        } elseif ($action === 'unlink_officer') {
            $officerId = (int)($_POST['officer_id'] ?? 0);
            if ($officerId > 0) {
                db()->prepare('UPDATE officers SET user_id = NULL WHERE id = ?')->execute([$officerId]);
                flash('success', 'Officer profile unlinked from login account.');
            }
        } elseif ($action === 'update_officer_phone') {
            $officerId = (int)($_POST['officer_id'] ?? 0);
            $newMobile = trim((string)($_POST['new_mobile'] ?? ''));
            $res = update_officer_phone_verified(db(), $officerId, $newMobile, (int)$currentUser['id']);
            if ($res['ok']) {
                flash('success', $res['message']);
            } else {
                flash('danger', $res['message']);
            }
        }
    } catch (Throwable $exception) {
        if (db()->inTransaction()) {
            db()->rollBack();
        }
        flash('danger', $exception->getMessage());
    }

    redirect('employee/users.php');
}

// -------------------------------------------------------------
// Load Overview Data & Options (Strictly scoped to Verified Department Contacts)
// -------------------------------------------------------------
$officerStats = db()->query("
    SELECT 
        COUNT(DISTINCT o.id) AS total_officers,
        COALESCE(SUM(o.user_id IS NOT NULL), 0) AS linked_officers,
        COALESCE(SUM(o.user_id IS NULL), 0) AS unlinked_officers
    FROM officers o 
    JOIN officer_department_map odm ON odm.officer_id = o.id
    WHERE o.is_active = 1
")->fetch(PDO::FETCH_ASSOC);

$totalUsersCount = (int)db()->query("SELECT COUNT(*) FROM users WHERE is_active = 1")->fetchColumn();

// Phone Number Reconciliation & Audit Data
$phoneAuditData = get_officer_phone_audit_data(db());

// Scopes
$departments = db()->query('SELECT id, department_name FROM departments WHERE is_active = 1 ORDER BY department_name')->fetchAll();
$zones = db()->query('SELECT id, zone_no FROM zones WHERE is_active = 1 ORDER BY zone_no')->fetchAll();
$wards = db()->query('SELECT id, ward_no FROM wards WHERE is_active = 1 ORDER BY ward_no')->fetchAll();

// Available unlinked officers for the unified form (searchable, only verified department contacts)
$officerSearch = trim((string)($_GET['officer_q'] ?? ''));
$officerQuery = db()->prepare('
    SELECT o.id, o.officer_name, o.designation, o.mobile_no, d.department_name, dm.department_id
    FROM officers o
    JOIN officer_department_map dm ON dm.officer_id = o.id
    JOIN departments d ON d.id = dm.department_id
    WHERE o.is_active = 1 AND o.user_id IS NULL
      AND (? = "" OR o.officer_name LIKE ? OR o.mobile_no LIKE ? OR CAST(o.id AS CHAR) = ?)
    GROUP BY o.id
    ORDER BY o.id ASC
    LIMIT 300
');
$likeSearch = '%' . $officerSearch . '%';
$officerQuery->execute([$officerSearch, $likeSearch, $likeSearch, $officerSearch]);
$availableOfficers = $officerQuery->fetchAll(PDO::FETCH_ASSOC);

// Filter users list
$userFilterRole = trim((string)($_GET['role_filter'] ?? ''));
$userSearch = trim((string)($_GET['user_search'] ?? ''));

$userConditions = ['1=1'];
$userParams = [];

if ($userFilterRole === 'field_officer') {
    $userConditions[] = "u.role IN ('Field Officer', 'Officer') AND u.is_active = 1";
} elseif ($userFilterRole === 'admin') {
    $userConditions[] = "u.role IN ('Super Admin', 'Admin', 'Commissioner', 'Department Head') AND u.is_active = 1";
} elseif ($userFilterRole === 'inactive') {
    $userConditions[] = "u.is_active = 0";
} elseif ($userFilterRole === 'all') {
    // Show all including inactive
} else {
    // Default view: active accounts only
    $userConditions[] = "u.is_active = 1";
}

if ($userSearch !== '') {
    $userConditions[] = "(u.full_name LIKE ? OR u.mobile_no LIKE ? OR u.email LIKE ?)";
    $likeUser = '%' . $userSearch . '%';
    $userParams[] = $likeUser;
    $userParams[] = $likeUser;
    $userParams[] = $likeUser;
}

$userWhere = implode(' AND ', $userConditions);
$usersQuery = db()->prepare("
    SELECT u.*, d.department_name, z.zone_no, w.ward_no,
           o.id as linked_officer_id, o.officer_name as linked_officer_name, o.designation as linked_designation
    FROM users u
    LEFT JOIN departments d ON d.id = u.department_id
    LEFT JOIN zones z ON z.id = u.zone_id
    LEFT JOIN wards w ON w.id = u.ward_id
    LEFT JOIN officers o ON o.user_id = u.id
    WHERE {$userWhere}
    ORDER BY u.id DESC
    LIMIT 100
");
$usersQuery->execute($userParams);
$users = $usersQuery->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/employee-header.php';
?>

<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Users &amp; Roles Management</h1>
        <p class="text-muted mb-0">Create employee logins, bulk-provision mobile access for field officers, and manage roles</p>
    </div>
</div>

<!-- 1. Top KPI Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card content-card border-0 shadow-sm p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">Active Officers</span>
                <span class="badge bg-primary-subtle text-primary p-2 rounded-circle"><i class="bi bi-person-badge fs-5"></i></span>
            </div>
            <div class="h3 fw-bold mb-0 text-dark"><?php echo number_format((int)$officerStats['total_officers']); ?></div>
            <div class="text-muted small mt-1">Officers in municipal directory</div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card content-card border-0 shadow-sm p-3 border-start border-4 border-success">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">Officers with Login</span>
                <span class="badge bg-success-subtle text-success p-2 rounded-circle"><i class="bi bi-shield-check fs-5"></i></span>
            </div>
            <div class="h3 fw-bold mb-0 text-success" id="kpiLinkedCount"><?php echo number_format((int)$officerStats['linked_officers']); ?></div>
            <div class="text-muted small mt-1">Accounts ready for Mobile OTP login</div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card content-card border-0 shadow-sm p-3 border-start border-4 border-warning">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">Awaiting Logins</span>
                <span class="badge bg-warning-subtle text-warning p-2 rounded-circle"><i class="bi bi-hourglass-split fs-5"></i></span>
            </div>
            <div class="h3 fw-bold mb-0 text-warning" id="kpiUnlinkedCount"><?php echo number_format((int)$officerStats['unlinked_officers']); ?></div>
            <div class="text-muted small mt-1">Eligible for one-click bulk provisioning</div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card content-card border-0 shadow-sm p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">Total User Accounts</span>
                <span class="badge bg-info-subtle text-info p-2 rounded-circle"><i class="bi bi-people fs-5"></i></span>
            </div>
            <div class="h3 fw-bold mb-0 text-dark" id="kpiTotalUsersCount"><?php echo number_format($totalUsersCount); ?></div>
            <div class="text-muted small mt-1">Staff, admins &amp; field officers</div>
        </div>
    </div>
</div>

<!-- 2. One-Click Bulk Provisioning Banner Card -->
<div class="card content-card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #0e2a47 0%, #17426e 100%); color: #fff;">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-success px-2 py-1"><i class="bi bi-phone-fill me-1"></i>One-Click Bulk Provisioning</span>
                    <span class="badge bg-light text-primary px-2 py-1">Email Optional &bull; Real Phone Numbers Only</span>
                </div>
                <h4 class="fw-bold mb-1 text-white">Create All Eligible Officer Logins</h4>
                <p class="text-white-50 mb-3 small">
                    Processes every eligible active officer in safe, resumable batches of 150. Skips missing, invalid, or duplicate phone numbers automatically without creating duplicate accounts or fake emails.
                </p>
                <?php
                    $totalOff = (int)$officerStats['total_officers'];
                    $linkedOff = (int)$officerStats['linked_officers'];
                    $pct = $totalOff > 0 ? round(($linkedOff / $totalOff) * 100, 1) : 0;
                ?>
                <div class="d-flex align-items-center gap-3">
                    <div class="progress flex-grow-1" style="height: 10px; background: rgba(255,255,255,0.2);">
                        <div class="progress-bar bg-success" id="provisionProgressBar" role="progressbar" style="width: <?php echo $pct; ?>%"></div>
                    </div>
                    <span class="small text-white fw-bold" id="provisionProgressLabel"><?php echo $pct; ?>% Linked</span>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <?php if ($isSuperAdmin): ?>
                    <button class="btn btn-warning btn-lg fw-bold shadow px-4 py-3 text-dark" id="btnStartBulkProvision" onclick="openBulkProvisionModal()">
                        <i class="bi bi-lightning-charge-fill me-1"></i> Create All Eligible Officer Logins
                    </button>
                    <div class="mt-2 d-flex flex-wrap gap-2 justify-content-lg-end">
                        <button class="btn btn-sm btn-outline-light" onclick="openPhoneAuditModal()">
                            <i class="bi bi-shield-check me-1"></i> Phone Number Audit
                        </button>
                        <button class="btn btn-sm btn-outline-light" onclick="openSkippedReportModal()">
                            <i class="bi bi-file-earmark-text me-1"></i> View Unprovisioned Report
                        </button>
                    </div>
                <?php else: ?>
                    <div class="alert alert-secondary py-2 px-3 small mb-0 text-start text-dark bg-white">
                        <i class="bi bi-shield-lock-fill text-warning me-1"></i> <strong>Super Admin Only:</strong> One-click bulk provisioning is restricted to Super Administrators.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- 3. Left Column: Unified Single Account Creation Form -->
    <div class="col-xl-4">
        <div class="card content-card border-0 shadow-sm p-4">
            <h2 class="h5 fw-bold mb-2"><i class="bi bi-person-plus text-primary me-2"></i>Create Employee Account</h2>
            <p class="text-muted small mb-3">One unified form for both Field Officers and Administrative staff.</p>

            <form method="post" id="unifiedUserForm">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="create_user">

                <!-- 1. Role Selection -->
                <div class="mb-3">
                    <label class="form-label fw-bold required">Role</label>
                    <select class="form-select form-select-lg" name="role" id="userRoleSelect" required onchange="handleRoleChange()">
                        <option value="Field Officer" selected>Field Officer (Municipal Field Staff)</option>
                        <option value="Officer">Officer</option>
                        <option value="Super Admin">Super Admin</option>
                        <option value="Admin">Admin</option>
                        <option value="Commissioner">Commissioner</option>
                        <option value="Department Head">Department Head</option>
                        <option value="Zone Officer">Zone Officer</option>
                        <option value="Ward Officer">Ward Officer</option>
                        <option value="Call Centre">Call Centre</option>
                        <option value="Report User">Report User</option>
                    </select>
                    <div class="form-text small" id="roleHelpText">Field officers are mapped to their official profile and log in via Mobile OTP.</div>
                </div>

                <!-- 2. Officer Profile Picker (Shown when Field Officer / Officer selected) -->
                <div class="mb-3 p-3 bg-light rounded border" id="officerProfileGroup">
                    <label class="form-label fw-bold text-primary required mb-1">
                        <i class="bi bi-person-badge me-1"></i>Link Officer Profile
                    </label>
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" id="officerSearchFilter" placeholder="Filter dropdown by name, ID or mobile..." oninput="filterOfficerDropdown(this.value)">
                    </div>
                    <select class="form-select" name="officer_id" id="officerProfileSelect" onchange="onOfficerSelected(this)">
                        <option value="">-- Select active municipal officer --</option>
                        <?php foreach ($availableOfficers as $item): ?>
                            <option value="<?php echo (int)$item['id']; ?>"
                                    data-name="<?php echo e($item['officer_name']); ?>"
                                    data-mobile="<?php echo e($item['mobile_no']); ?>"
                                    data-dept="<?php echo (int)($item['department_id'] ?? 0); ?>">
                                #<?php echo (int)$item['id']; ?> - <?php echo e($item['officer_name']); ?> (<?php echo e($item['designation']); ?><?php echo $item['department_name'] ? ' &bull; ' . e($item['department_name']) : ''; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text small text-success mt-1">
                        <i class="bi bi-magic me-1"></i>Auto-fills Name, Mobile, and Department automatically.
                    </div>
                </div>

                <!-- 3. Full Name -->
                <div class="mb-3">
                    <label class="form-label fw-bold required">Full Name</label>
                    <input class="form-control" name="full_name" id="userFullNameInput" required placeholder="e.g. Officer Name">
                </div>

                <!-- 4. Mobile Number (Mandatory) -->
                <div class="mb-3">
                    <label class="form-label fw-bold required">Mobile Number</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">+91</span>
                        <input class="form-control" type="tel" name="mobile_no" id="userMobileInput" maxlength="10" pattern="[6-9][0-9]{9}" required placeholder="10-digit mobile number">
                    </div>
                    <div class="form-text small text-success"><i class="bi bi-check-circle me-1"></i><strong>Mandatory.</strong> Used for Mobile OTP authentication.</div>
                </div>

                <!-- 5. Email (Optional) -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email Address (Optional)</label>
                    <input class="form-control" type="email" name="email" id="userEmailInput" placeholder="official@smartcitytiruppur.gov.in">
                    <div class="form-text small">Optional. Leave blank for phone-only OTP login. No fake emails needed.</div>
                </div>

                <!-- 6. Password -->
                <div class="mb-3" id="passwordGroup">
                    <label class="form-label fw-bold" id="passwordLabel">Password</label>
                    <input class="form-control" type="password" name="password" id="userPasswordInput" minlength="8" placeholder="Leave blank for OTP-only login">
                    <div class="form-text small" id="passwordHelpText">Optional for officers &mdash; leave blank for secure Mobile OTP login.</div>
                </div>

                <!-- 7. Department Scope -->
                <div class="mb-3" id="deptScopeGroup">
                    <label class="form-label fw-semibold">Department Scope</label>
                    <select class="form-select" name="department_id" id="userDeptScopeSelect">
                        <option value="">None / Corporate Level</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo (int)$dept['id']; ?>"><?php echo e($dept['department_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- 8. Zone & Ward Scope (Shown for Zone/Ward/Admin roles) -->
                <div class="row g-2 mb-3" id="zoneWardScopeGroup">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Zone Scope</label>
                        <select class="form-select form-select-sm" name="zone_id">
                            <option value="">None</option>
                            <?php foreach ($zones as $zone): ?>
                                <option value="<?php echo (int)$zone['id']; ?>">Zone <?php echo e($zone['zone_no']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Ward Scope</label>
                        <select class="form-select form-select-sm" name="ward_id">
                            <option value="">None</option>
                            <?php foreach ($wards as $ward): ?>
                                <option value="<?php echo (int)$ward['id']; ?>">Ward <?php echo e($ward['ward_no']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <button class="btn btn-primary w-100 py-2 shadow-sm fw-bold">
                    <i class="bi bi-check-circle me-1"></i> Save Account
                </button>
            </form>
        </div>
    </div>

    <!-- 4. Right Column: Users List Table -->
    <div class="col-xl-8">
        <div class="card content-card border-0 shadow-sm p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3 pb-3 border-bottom">
                <div>
                    <h2 class="h5 fw-bold mb-0"><i class="bi bi-people text-primary me-2"></i>Active Login Accounts (<?php echo count($users); ?>)</h2>
                    <small class="text-muted">Showing registered employee accounts with contact and mapping details</small>
                </div>

                <!-- Search Users Form -->
                <form method="get" class="d-flex gap-2">
                    <?php if ($userFilterRole !== ''): ?>
                        <input type="hidden" name="role_filter" value="<?php echo e($userFilterRole); ?>">
                    <?php endif; ?>
                    <input type="search" name="user_search" class="form-control form-control-sm" placeholder="Search name, phone, email..." value="<?php echo e($userSearch); ?>">
                    <button class="btn btn-sm btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
                    <?php if ($userSearch !== '' || $userFilterRole !== ''): ?>
                        <a href="<?php echo e(url('employee/users.php')); ?>" class="btn btn-sm btn-outline-secondary" title="Reset"><i class="bi bi-x-lg"></i></a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Role Filter Pills -->
            <div class="d-flex flex-wrap gap-2 mb-3">
                <a href="<?php echo e(url('employee/users.php' . ($userSearch ? '?user_search=' . urlencode($userSearch) : ''))); ?>" 
                   class="btn btn-sm <?php echo $userFilterRole === '' ? 'btn-primary' : 'btn-outline-secondary'; ?>">
                    <i class="bi bi-person-check me-1"></i>Active Users (<?php echo number_format($totalUsersCount); ?>)
                </a>
                <a href="<?php echo e(url('employee/users.php?role_filter=field_officer' . ($userSearch ? '&user_search=' . urlencode($userSearch) : ''))); ?>" 
                   class="btn btn-sm <?php echo $userFilterRole === 'field_officer' ? 'btn-primary' : 'btn-outline-secondary'; ?>">
                    Field Officers
                </a>
                <a href="<?php echo e(url('employee/users.php?role_filter=admin' . ($userSearch ? '&user_search=' . urlencode($userSearch) : ''))); ?>" 
                   class="btn btn-sm <?php echo $userFilterRole === 'admin' ? 'btn-primary' : 'btn-outline-secondary'; ?>">
                    Administrators
                </a>
                <a href="<?php echo e(url('employee/users.php?role_filter=inactive' . ($userSearch ? '&user_search=' . urlencode($userSearch) : ''))); ?>" 
                   class="btn btn-sm <?php echo $userFilterRole === 'inactive' ? 'btn-danger' : 'btn-outline-secondary'; ?>">
                    <i class="bi bi-slash-circle me-1"></i>Inactive Accounts
                </a>
                <a href="<?php echo e(url('employee/users.php?role_filter=all' . ($userSearch ? '&user_search=' . urlencode($userSearch) : ''))); ?>" 
                   class="btn btn-sm <?php echo $userFilterRole === 'all' ? 'btn-dark' : 'btn-outline-secondary'; ?>">
                    All Accounts
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>User Name</th>
                            <th>Mobile &amp; Email</th>
                            <th>Role &amp; Officer Link</th>
                            <th>Scope</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-1"></i> No matching users found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $row): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo e($row['full_name']); ?></div>
                                        <small class="text-muted">User ID: #<?php echo (int)$row['id']; ?></small>
                                    </td>
                                    <td>
                                        <div><i class="bi bi-telephone text-primary me-1"></i><strong><?php echo e($row['mobile_no'] ?: '-'); ?></strong></div>
                                        <?php if ($row['email']): ?>
                                            <small class="text-muted"><i class="bi bi-envelope me-1"></i><?php echo e($row['email']); ?></small>
                                        <?php else: ?>
                                            <span class="badge bg-light text-secondary border small">Mobile OTP Login</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo in_array($row['role'], ['Field Officer', 'Officer'], true) ? 'bg-primary' : 'bg-dark'; ?> px-2 py-1">
                                            <?php echo e($row['role']); ?>
                                        </span>
                                        <?php if ($row['linked_officer_id']): ?>
                                            <div class="mt-1 small text-success fw-semibold">
                                                <i class="bi bi-link-45deg"></i> Officer #<?php echo (int)$row['linked_officer_id']; ?>: <?php echo e($row['linked_officer_name']); ?> 
                                                <span class="text-muted">(<?php echo e($row['linked_designation']); ?>)</span>
                                            </div>
                                        <?php elseif (in_array($row['role'], ['Field Officer', 'Officer'], true)): ?>
                                            <div class="mt-1 small text-danger fw-semibold">
                                                <i class="bi bi-exclamation-circle me-1"></i> Unlinked Officer Profile
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['department_name']): ?>
                                            <div><?php echo e($row['department_name']); ?></div>
                                        <?php endif; ?>
                                        <?php if ($row['zone_no'] || $row['ward_no']): ?>
                                            <small class="text-muted">
                                                <?php echo $row['zone_no'] ? 'Zone ' . e($row['zone_no']) : ''; ?> 
                                                <?php echo $row['ward_no'] ? 'Ward ' . e($row['ward_no']) : ''; ?>
                                            </small>
                                        <?php elseif (!$row['department_name']): ?>
                                            <span class="text-muted small">Global Scope</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo (int)$row['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo (int)$row['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <?php if ((int)$row['id'] !== (int)$currentUser['id']): ?>
                                            <form method="post" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="action" value="toggle_user">
                                                <input type="hidden" name="user_id" value="<?php echo (int)$row['id']; ?>">
                                                <button class="btn btn-sm <?php echo (int)$row['is_active'] ? 'btn-outline-secondary' : 'btn-outline-success'; ?>" title="Toggle status">
                                                    <?php echo (int)$row['is_active'] ? 'Disable' : 'Enable'; ?>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================= -->
<!-- Bulk Provision Modal with Live Progress & Batch Runner        -->
<!-- ============================================================= -->
<div class="modal fade" id="bulkProvisionModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="bulkProvisionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="bulkProvisionModalLabel">
                    <i class="bi bi-lightning-charge-fill me-2"></i>One-Click Bulk Officer Provisioning
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" id="bulkModalCloseBtn" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Phase 1: Preflight Preview -->
                <div id="bulkPreflightSection">
                    <h5 class="fw-bold mb-2">Preflight Provisioning Analysis</h5>
                    <p class="text-muted small mb-3">The system will process every eligible active municipal officer into secure, OTP-ready accounts in safe 150-record batches.</p>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-4">
                            <div class="p-3 bg-light rounded text-center border">
                                <div class="text-muted small fw-semibold">Total Active Officers</div>
                                <div class="h4 fw-bold text-dark mb-0" id="prevTotalActive">-</div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 bg-light rounded text-center border border-success">
                                <div class="text-success small fw-semibold">Eligible to Create</div>
                                <div class="h4 fw-bold text-success mb-0" id="prevEligible">-</div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 bg-light rounded text-center border border-warning">
                                <div class="text-warning small fw-semibold">Skipped (Invalid/Dupe)</div>
                                <div class="h4 fw-bold text-warning mb-0" id="prevSkipped">-</div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info py-2 px-3 small">
                        <i class="bi bi-shield-check me-1"></i><strong>Safety Guarantees:</strong>
                        <ul class="mb-0 ps-3 mt-1">
                            <li>Officers will authenticate via their registered 10-digit mobile number + OTP.</li>
                            <li>No fake email addresses or shared passwords will ever be generated.</li>
                            <li>Existing user accounts and officer mappings remain untouched.</li>
                            <li>You can pause or resume anytime.</li>
                        </ul>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success px-4 fw-bold" id="btnConfirmStartBulk" onclick="startBulkProcessing()">
                            <i class="bi bi-play-fill me-1"></i> Start Provisioning
                        </button>
                    </div>
                </div>

                <!-- Phase 2: Live Progress Execution -->
                <div id="bulkProgressSection" style="display: none;">
                    <div class="text-center mb-4">
                        <div class="spinner-border text-primary mb-2" id="bulkSpinner" role="status" style="width: 3rem; height: 3rem;"></div>
                        <h5 class="fw-bold mb-1" id="bulkStatusHeading">Provisioning Officer Accounts...</h5>
                        <p class="text-muted small" id="bulkStatusSub">Executing safe batch transactions</p>
                    </div>

                    <!-- Progress Bar -->
                    <div class="progress mb-2" style="height: 20px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" id="liveModalProgressBar" role="progressbar" style="width: 0%">0%</div>
                    </div>
                    <div class="d-flex justify-content-between small text-muted mb-4">
                        <span>Created: <strong class="text-success" id="liveCreatedCount">0</strong></span>
                        <span>Skipped: <strong class="text-warning" id="liveSkippedCount">0</strong></span>
                        <span>Remaining: <strong class="text-primary" id="liveRemainingCount">0</strong></span>
                    </div>

                    <!-- Live Log Box -->
                    <div class="p-3 bg-dark text-light rounded font-monospace small mb-3" style="max-height: 180px; overflow-y: auto;" id="liveLogConsole">
                        <div>[Init] Connecting to municipal directory...</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-danger" id="btnPauseBulk" onclick="pauseBulkProcessing()">
                            <i class="bi bi-pause-fill me-1"></i> Pause
                        </button>
                        <button type="button" class="btn btn-success" id="btnDoneBulk" style="display: none;" onclick="window.location.reload()">
                            <i class="bi bi-check2-circle me-1"></i> Done &amp; Refresh Page
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================= -->
<!-- Phone Number Audit & Reconciliation Modal                     -->
<!-- ============================================================= -->
<div class="modal fade" id="phoneAuditModal" tabindex="-1" aria-labelledby="phoneAuditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="phoneAuditModalLabel">
                    <i class="bi bi-shield-check me-2"></i>Officer Identity &amp; Phone Number Audit
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Summary KPI Row -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded text-center border">
                            <span class="text-muted small d-block">Verified Officers</span>
                            <span class="fs-4 fw-bold text-primary"><?php echo $phoneAuditData['total_active_officers']; ?></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded text-center border">
                            <span class="text-muted small d-block">Phone Synchronized</span>
                            <span class="fs-4 fw-bold text-success"><?php echo $phoneAuditData['matched_count']; ?></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded text-center border">
                            <span class="text-muted small d-block">Phone Mismatches</span>
                            <span class="fs-4 fw-bold <?php echo $phoneAuditData['phone_mismatches_count'] > 0 ? 'text-danger' : 'text-muted'; ?>">
                                <?php echo $phoneAuditData['phone_mismatches_count']; ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded text-center border">
                            <span class="text-muted small d-block">Duplicate Phones</span>
                            <span class="fs-4 fw-bold <?php echo $phoneAuditData['duplicate_phones_count'] > 0 ? 'text-warning' : 'text-muted'; ?>">
                                <?php echo $phoneAuditData['duplicate_phones_count']; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info py-2 px-3 small mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    <strong>Reconciliation Rule:</strong> When updating an officer's phone number, the system updates both <code>officers.mobile_no</code> and <code>users.mobile_no</code> in an atomic transaction, validates that the number is not already assigned to another officer or login, and preserves all complaint assignments.
                </div>

                <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Officer ID &amp; Name</th>
                                <th>Department &amp; Designation</th>
                                <th>Officer Phone</th>
                                <th>Login Account Phone</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($phoneAuditData['active_officers'] as $ao): ?>
                                <?php
                                    $isMatch = ($ao['officer_phone'] === $ao['user_phone']) && !empty($ao['matched_user_id']) && (int)$ao['user_active'] === 1;
                                    $hasMismatch = !empty($ao['matched_user_id']) && ($ao['officer_phone'] !== $ao['user_phone']);
                                ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold">Officer #<?php echo (int)$ao['officer_id']; ?>: <?php echo e($ao['officer_name']); ?></div>
                                        <?php if ($ao['matched_user_id']): ?>
                                            <small class="text-muted">Linked to User #<?php echo (int)$ao['matched_user_id']; ?> (<?php echo e($ao['user_name']); ?>)</small>
                                        <?php else: ?>
                                            <small class="text-danger">No Login Account</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div><?php echo e($ao['departments'] ?: 'Unassigned'); ?></div>
                                        <small class="text-muted"><?php echo e($ao['designation'] ?: 'Corporation Officer'); ?></small>
                                    </td>
                                    <td>
                                        <span class="font-monospace fw-bold"><?php echo e($ao['officer_phone']); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($ao['user_phone']): ?>
                                            <span class="font-monospace fw-bold"><?php echo e($ao['user_phone']); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">&mdash;</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($isMatch): ?>
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Synchronized</span>
                                        <?php elseif ($hasMismatch): ?>
                                            <span class="badge bg-danger"><i class="bi bi-exclamation-triangle me-1"></i>Mismatch</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Review</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-xs btn-outline-primary" onclick="openPhoneCorrectionModal(<?php echo (int)$ao['officer_id']; ?>, '<?php echo e(addslashes($ao['officer_name'])); ?>', '<?php echo e($ao['officer_phone']); ?>')">
                                            <i class="bi bi-pencil me-1"></i>Edit Phone
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Phone Correction Submodal -->
<div class="modal fade" id="phoneCorrectionModal" tabindex="-1" aria-labelledby="phoneCorrectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="post">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="update_officer_phone">
                <input type="hidden" name="officer_id" id="correctOfficerId">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold" id="phoneCorrectionModalLabel">Update Officer Official Phone</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3" id="correctOfficerHelpText">Correcting phone number for officer.</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">New Verified 10-Digit Mobile Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">+91</span>
                            <input class="form-control" type="tel" name="new_mobile" id="correctMobileInput" maxlength="10" pattern="[6-9][0-9]{9}" required>
                        </div>
                        <div class="form-text small">Both officer profile and OTP login will be safely updated in one transaction.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Apply Correction</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================= -->
<!-- Unprovisioned / Skipped Officers Report Modal                 -->
<!-- ============================================================= -->
<div class="modal fade" id="skippedReportModal" tabindex="-1" aria-labelledby="skippedReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold" id="skippedReportModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Unprovisioned Officers Audit Report
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">
                    The following active officers could not be provisioned automatically because their contact records in the municipal directory contain missing, invalid, or duplicate phone numbers. Update their official contact in the directory to enable their logins.
                </p>
                <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                    <table class="table table-sm table-bordered table-hover align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Officer ID</th>
                                <th>Officer Name</th>
                                <th>Designation</th>
                                <th>Department</th>
                                <th>Recorded Mobile</th>
                                <th>Reason Skipped</th>
                            </tr>
                        </thead>
                        <tbody id="skippedReportTableBody">
                            <tr><td colspan="6" class="text-center py-3">Loading report...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// -------------------------------------------------------------
// Unified Form Dynamic Role Behavior
// -------------------------------------------------------------
function handleRoleChange() {
    const roleSelect = document.getElementById('userRoleSelect');
    const officerGroup = document.getElementById('officerProfileGroup');
    const officerSelect = document.getElementById('officerProfileSelect');
    const passwordInput = document.getElementById('userPasswordInput');
    const passwordLabel = document.getElementById('passwordLabel');
    const passwordHelp = document.getElementById('passwordHelpText');
    const zoneWardGroup = document.getElementById('zoneWardScopeGroup');
    const roleHelp = document.getElementById('roleHelpText');

    if (!roleSelect) return;
    const role = roleSelect.value;
    const isOfficer = (role === 'Field Officer' || role === 'Officer');

    if (isOfficer) {
        if (officerGroup) officerGroup.style.display = 'block';
        if (officerSelect) officerSelect.required = true;
        if (passwordInput) {
            passwordInput.required = false;
            passwordInput.placeholder = 'Leave blank for OTP-only login';
        }
        if (passwordLabel) passwordLabel.className = 'form-label fw-semibold';
        if (passwordHelp) passwordHelp.textContent = 'Optional for officers — leave blank for secure Mobile OTP login.';
        if (zoneWardGroup) zoneWardGroup.style.display = 'none';
        if (roleHelp) roleHelp.textContent = 'Field officers are mapped to their official profile and log in via Mobile OTP.';
    } else {
        if (officerGroup) officerGroup.style.display = 'none';
        if (officerSelect) {
            officerSelect.required = false;
            officerSelect.value = '';
        }
        if (passwordInput) {
            passwordInput.required = true;
            passwordInput.placeholder = 'Required (Minimum 8 characters)';
        }
        if (passwordLabel) passwordLabel.className = 'form-label fw-bold required';
        if (passwordHelp) passwordHelp.textContent = 'Mandatory for administrative access — minimum 8 characters.';
        if (zoneWardGroup) zoneWardGroup.style.display = 'flex';
        if (roleHelp) roleHelp.textContent = 'Administrative staff account with custom departmental or zonal scopes.';
    }
}

function onOfficerSelected(select) {
    const selectedOption = select.options[select.selectedIndex];
    if (!selectedOption || !selectedOption.value) return;

    const name = selectedOption.getAttribute('data-name') || '';
    const mobile = selectedOption.getAttribute('data-mobile') || '';
    const deptId = selectedOption.getAttribute('data-dept') || '';

    const nameInput = document.getElementById('userFullNameInput');
    const mobileInput = document.getElementById('userMobileInput');
    const deptSelect = document.getElementById('userDeptScopeSelect');

    if (nameInput) nameInput.value = name;
    if (mobileInput) {
        const clean = mobile.replace(/[^0-9]/g, '').slice(-10);
        mobileInput.value = clean;
    }
    if (deptSelect && deptId) {
        deptSelect.value = deptId;
    }
}

function filterOfficerDropdown(query) {
    const q = query.toLowerCase().trim();
    const select = document.getElementById('officerProfileSelect');
    if (!select) return;

    for (let i = 0; i < select.options.length; i++) {
        const opt = select.options[i];
        if (!opt.value) continue;
        const text = opt.textContent.toLowerCase();
        opt.hidden = (q !== '' && !text.includes(q));
    }
}

// Initial trigger
document.addEventListener('DOMContentLoaded', handleRoleChange);

// -------------------------------------------------------------
// One-Click Bulk Provisioning AJAX Runner
// -------------------------------------------------------------
let bulkState = {
    running: false,
    paused: false,
    totalEligible: 0,
    totalCreated: 0,
    totalSkipped: 0,
    batchSize: 150
};

function logMsg(msg) {
    const consoleBox = document.getElementById('liveLogConsole');
    if (!consoleBox) return;
    const d = new Date().toLocaleTimeString();
    const line = document.createElement('div');
    line.textContent = `[${d}] ${msg}`;
    consoleBox.appendChild(line);
    consoleBox.scrollTop = consoleBox.scrollHeight;
}

async function openBulkProvisionModal() {
    const modalEl = document.getElementById('bulkProvisionModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    document.getElementById('bulkPreflightSection').style.display = 'block';
    document.getElementById('bulkProgressSection').style.display = 'none';
    document.getElementById('btnConfirmStartBulk').disabled = true;

    try {
        const resp = await fetch('<?php echo e(url("ajax/bulk-provision-officers.php")); ?>?action=preview');
        const data = await resp.json();

        if (data.ok) {
            document.getElementById('prevTotalActive').textContent = Number(data.total_active).toLocaleString();
            document.getElementById('prevEligible').textContent = Number(data.eligible_count).toLocaleString();
            document.getElementById('prevSkipped').textContent = Number(data.skipped_count).toLocaleString();

            bulkState.totalEligible = data.eligible_count;
            bulkState.totalCreated = 0;
            bulkState.totalSkipped = 0;

            document.getElementById('btnConfirmStartBulk').disabled = (data.eligible_count <= 0);
            if (data.eligible_count <= 0) {
                document.getElementById('btnConfirmStartBulk').textContent = 'All Officers Already Provisioned';
            } else {
                document.getElementById('btnConfirmStartBulk').textContent = `Start Provisioning (${Number(data.eligible_count).toLocaleString()} Officers)`;
            }
        }
    } catch (e) {
        alert('Unable to load provisioning preview: ' + e.message);
    }
}

async function startBulkProcessing() {
    bulkState.running = true;
    bulkState.paused = false;

    document.getElementById('bulkPreflightSection').style.display = 'none';
    document.getElementById('bulkProgressSection').style.display = 'block';
    document.getElementById('bulkModalCloseBtn').style.display = 'none';

    logMsg(`Starting bulk provisioning for ${bulkState.totalEligible} eligible officers...`);

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
        || document.querySelector('input[name="csrf_token"]')?.value || '';

    let iteration = 1;

    while (bulkState.running && !bulkState.paused) {
        logMsg(`Processing batch #${iteration} (up to ${bulkState.batchSize} officers)...`);

        try {
            const formData = new FormData();
            formData.append('action', 'process_batch');
            formData.append('batch_size', bulkState.batchSize);
            formData.append('csrf_token', csrfToken);

            const resp = await fetch('<?php echo e(url("ajax/bulk-provision-officers.php")); ?>', {
                method: 'POST',
                body: formData
            });
            const res = await resp.json();

            if (!res.ok) {
                logMsg(`[Error] Batch #${iteration} failed: ` + (res.message || 'Unknown error'));
                break;
            }

            bulkState.totalCreated += res.created;
            bulkState.totalSkipped += res.skipped;

            document.getElementById('liveCreatedCount').textContent = bulkState.totalCreated.toLocaleString();
            document.getElementById('liveSkippedCount').textContent = bulkState.totalSkipped.toLocaleString();
            document.getElementById('liveRemainingCount').textContent = res.remaining_eligible.toLocaleString();

            // Progress bar
            const totalToProcess = bulkState.totalEligible > 0 ? bulkState.totalEligible : 1;
            const pct = Math.min(100, Math.round((bulkState.totalCreated / totalToProcess) * 100));
            const pBar = document.getElementById('liveModalProgressBar');
            pBar.style.width = pct + '%';
            pBar.textContent = pct + '%';

            logMsg(`Batch #${iteration} done: created ${res.created}, skipped ${res.skipped}. Remaining: ${res.remaining_eligible}.`);

            if (res.is_finished || res.created === 0) {
                logMsg('[Success] All eligible officers have been provisioned successfully!');
                finishBulkProcessing();
                break;
            }

            iteration++;
            // Brief 150ms throttle to yield UI thread
            await new Promise(r => setTimeout(r, 150));
        } catch (err) {
            logMsg(`[Network Error] ` + err.message);
            break;
        }
    }
}

function pauseBulkProcessing() {
    bulkState.paused = true;
    bulkState.running = false;
    logMsg('[Paused] Provisioning paused by user. Progress is preserved. Click to resume.');
    document.getElementById('btnPauseBulk').innerHTML = '<i class="bi bi-play-fill me-1"></i> Resume';
    document.getElementById('btnPauseBulk').onclick = resumeBulkProcessing;
    document.getElementById('bulkModalCloseBtn').style.display = 'inline-block';
}

function resumeBulkProcessing() {
    bulkState.paused = false;
    bulkState.running = true;
    document.getElementById('btnPauseBulk').innerHTML = '<i class="bi bi-pause-fill me-1"></i> Pause';
    document.getElementById('btnPauseBulk').onclick = pauseBulkProcessing;
    document.getElementById('bulkModalCloseBtn').style.display = 'none';
    startBulkProcessing();
}

function finishBulkProcessing() {
    bulkState.running = false;
    document.getElementById('bulkSpinner').style.display = 'none';
    document.getElementById('bulkStatusHeading').textContent = 'Bulk Provisioning Completed!';
    document.getElementById('bulkStatusHeading').className = 'fw-bold mb-1 text-success';
    document.getElementById('bulkStatusSub').textContent = `Successfully created ${bulkState.totalCreated} officer accounts.`;

    document.getElementById('btnPauseBulk').style.display = 'none';
    document.getElementById('btnDoneBulk').style.display = 'inline-block';
    document.getElementById('bulkModalCloseBtn').style.display = 'inline-block';
}

function openPhoneAuditModal() {
    const modalEl = document.getElementById('phoneAuditModal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
}

function openPhoneCorrectionModal(officerId, officerName, currentPhone) {
    document.getElementById('correctOfficerId').value = officerId;
    document.getElementById('correctMobileInput').value = currentPhone || '';
    document.getElementById('correctOfficerHelpText').textContent = `Correcting official mobile number for Officer #${officerId}: ${officerName}`;
    const modalEl = document.getElementById('phoneCorrectionModal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
}

async function openSkippedReportModal() {
    const modalEl = document.getElementById('skippedReportModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    const tbody = document.getElementById('skippedReportTableBody');
    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-3"><span class="spinner-border spinner-border-sm me-2"></span>Loading audit report...</td></tr>';

    try {
        const resp = await fetch('<?php echo e(url("ajax/bulk-provision-officers.php")); ?>?action=skipped_report');
        const data = await resp.json();

        if (data.ok && data.report) {
            if (data.report.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-success"><i class="bi bi-check-circle fs-3 d-block mb-1"></i>All active officers have valid mobile numbers and are provisioned!</td></tr>';
            } else {
                tbody.innerHTML = data.report.map(r => `
                    <tr>
                        <td class="fw-bold">#${r.id}</td>
                        <td>${escapeHtml(r.name)}</td>
                        <td><span class="badge bg-light text-dark border">${escapeHtml(r.designation)}</span></td>
                        <td>${escapeHtml(r.department)}</td>
                        <td class="font-monospace">${escapeHtml(r.mobile)}</td>
                        <td><span class="badge bg-warning text-dark">${escapeHtml(r.reason)}</span></td>
                    </tr>
                `).join('');
            }
        }
    } catch (e) {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-3 text-danger">Error loading report: ${e.message}</td></tr>`;
    }
}

function escapeHtml(str) {
    if (!str) return '-';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
</script>

<?php include __DIR__ . '/../includes/employee-footer.php'; ?>
