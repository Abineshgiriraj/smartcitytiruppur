<?php
declare(strict_types=1);

function current_user(): ?array
{
    static $cachedId = null;
    static $user = null;

    $userId = (int)($_SESSION['user_id'] ?? 0);
    if ($userId <= 0) {
        $cachedId = null;
        $user = null;
        return null;
    }

    if ($cachedId === $userId && $user !== null) {
        return $user;
    }

    $stmt = db()->prepare(
        'SELECT id, full_name, email, mobile_no, role, department_id, zone_id, ward_id, is_active
         FROM users WHERE id = ? LIMIT 1'
    );
    $stmt->execute([(int) $userId]);
    $record = $stmt->fetch();

    if (!$record || !(int) $record['is_active']) {
        unset($_SESSION['user_id']);
        $cachedId = null;
        $user = null;
        return null;
    }

    $user = $record;
    $cachedId = $userId;
    return $user;
}

/**
 * Returns the currently authenticated active officer mapped via officers.user_id = users.id.
 */
function current_officer(): ?array
{
    static $cachedUserId = null;
    static $officer = null;

    $user = current_user();
    if (!$user) {
        $cachedUserId = null;
        $officer = null;
        return null;
    }

    if ($cachedUserId === (int)$user['id'] && $officer !== null) {
        return $officer;
    }

    $stmt = db()->prepare(
        'SELECT o.*, d.department_name
         FROM officers o
         LEFT JOIN officer_department_map dm ON dm.officer_id = o.id
         LEFT JOIN departments d ON d.id = dm.department_id
         WHERE o.user_id = ? AND o.is_active = 1
         LIMIT 1'
    );
    $stmt->execute([(int)$user['id']]);
    $record = $stmt->fetch();

    $officer = $record ?: null;
    $cachedUserId = (int)$user['id'];
    return $officer;
}

/**
 * Enforces that the logged-in user is an authorized officer linked to an active officer profile.
 */
function require_officer(): array
{
    $user = require_login();

    if (!in_array($user['role'], ['Field Officer', 'Officer', 'Super Admin', 'Admin'], true)) {
        http_response_code(403);
        $errorMessage = 'Access denied. This section is restricted to municipal officers.';
        include __DIR__ . '/../employee/officer-access-error.php';
        exit;
    }

    $officer = current_officer();
    if (!$officer) {
        http_response_code(403);
        $errorMessage = 'Your account (' . htmlspecialchars((string)($user['mobile_no'] ?: $user['email'] ?: 'unknown')) . ') is not linked to any active municipal officer profile. Please contact the administrator.';
        include __DIR__ . '/../employee/officer-access-error.php';
        exit;
    }

    return ['user' => $user, 'officer' => $officer];
}

/**
 * Phone-only accounts require users.email to allow NULL (not placeholder emails).
 * Cache only for this request; do not perform schema changes during login.
 */
function user_email_nullable(PDO $pdo): bool
{
    static $allowed = null;
    if ($allowed !== null) return $allowed;
    $stmt=$pdo->prepare(
        "SELECT IS_NULLABLE FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='users' AND COLUMN_NAME='email'"
    );
    $stmt->execute();
    return $allowed=$stmt->fetchColumn()==='YES';
}

function login_user(string $identifier, string $password): bool
{
    $identifier = trim($identifier);
    if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
        $stmt = db()->prepare(
            'SELECT id, password_hash, is_active FROM users WHERE email = ? LIMIT 1'
        );
        $stmt->execute([strtolower($identifier)]);
    } else {
        $cleanMobile = normalize_mobile($identifier);
        if (!preg_match('/^[6-9][0-9]{9}$/D', $cleanMobile)) return false;
        $stmt = db()->prepare(
            'SELECT id, password_hash, is_active FROM users WHERE mobile_no = ? LIMIT 2'
        );
        $stmt->execute([$cleanMobile]);
        $matches=$stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($matches) !== 1) return false;
        $user=$matches[0];
    }
    if (!isset($user)) $user = $stmt->fetch();

    if (!$user || !(int) $user['is_active']) {
        return false;
    }

    if (!password_verify($password, $user['password_hash'])) {
        return false;
    }

    if (session_status() === PHP_SESSION_ACTIVE && !headers_sent()) {
        session_regenerate_id(true);
    }
    $_SESSION['user_id'] = (int) $user['id'];

    db()->prepare('UPDATE users SET last_login_at = NOW() WHERE id = ?')
        ->execute([(int) $user['id']]);

    audit_log((int) $user['id'], 'login', 'user', (int) $user['id']);

    return true;
}

function login_user_by_otp(string $mobile, string $otp): array
{
    $mobile = normalize_mobile($mobile);
    $otp = trim($otp);

    if (!preg_match('/^[6-9]\d{9}$/', $mobile)) {
        return ['ok' => false, 'message' => 'Please enter a valid 10-digit mobile number.'];
    }

    if (!preg_match('/^\d{6}$/', $otp)) {
        return ['ok' => false, 'message' => 'Please enter a valid 6-digit OTP.'];
    }

    $stmt = db()->prepare(
        'SELECT id, full_name, email, is_active FROM users WHERE mobile_no = ? LIMIT 2'
    );
    $stmt->execute([$mobile]);
    $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($matches) > 1) {
        return ['ok'=>false,'message'=>'Duplicate login number detected. Ask an administrator to correct the accounts.'];
    }
    $user = $matches[0] ?? null;

    if (!$user) {
        return ['ok' => false, 'message' => 'No employee account found with this mobile number.'];
    }

    if (!(int)$user['is_active']) {
        return ['ok' => false, 'message' => 'Your account is currently disabled. Please contact your administrator.'];
    }

    $otpStmt = db()->prepare(
        'SELECT id, otp_hash, expires_at, attempt_count, verified_at
         FROM otp_requests
         WHERE mobile_no = ? AND purpose = ?
         ORDER BY id DESC LIMIT 1'
    );
    $otpStmt->execute([$mobile, 'login']);
    $record = $otpStmt->fetch();

    if (!$record) {
        return ['ok' => false, 'message' => 'No OTP request found for this mobile number. Please click Send OTP first.'];
    }

    if (!empty($record['verified_at'])) {
        return ['ok' => false, 'message' => 'This OTP has already been used. Please request a new OTP.'];
    }

    if ((int)$record['attempt_count'] >= 5) {
        return ['ok' => false, 'message' => 'Too many incorrect OTP attempts. Please request a new OTP.'];
    }

    if (strtotime((string)$record['expires_at']) < time()) {
        return ['ok' => false, 'message' => 'OTP has expired. Please request a new OTP.'];
    }

    // Increment attempt count
    db()->prepare('UPDATE otp_requests SET attempt_count = attempt_count + 1 WHERE id = ?')
        ->execute([(int)$record['id']]);

    if (!password_verify($otp, $record['otp_hash'])) {
        return ['ok' => false, 'message' => 'The entered OTP is incorrect. Please try again.'];
    }

    // Mark verified
    db()->prepare('UPDATE otp_requests SET verified_at = NOW() WHERE id = ?')
        ->execute([(int)$record['id']]);

    if (session_status() === PHP_SESSION_ACTIVE && !headers_sent()) {
        session_regenerate_id(true);
    }
    $_SESSION['user_id'] = (int)$user['id'];

    db()->prepare('UPDATE users SET last_login_at = NOW() WHERE id = ?')
        ->execute([(int)$user['id']]);

    audit_log((int)$user['id'], 'login_otp', 'user', (int)$user['id']);

    return ['ok' => true, 'message' => 'Logged in successfully.'];
}

function logout_user(): void
{
    $userId = $_SESSION['user_id'] ?? null;

    if ($userId) {
        audit_log((int) $userId, 'logout', 'user', (int) $userId);
    }

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

function require_login(): array
{
    $user = current_user();

    if (!$user) {
        flash('warning', 'Please log in to continue.');
        redirect('employee/login.php');
    }

    return $user;
}

function require_roles(array $roles): array
{
    $user = require_login();

    if (!in_array($user['role'], $roles, true)) {
        http_response_code(403);
        exit('You do not have permission to access this page.');
    }

    return $user;
}

function can_manage_all_complaints(array $user): bool
{
    return in_array($user['role'], ['Super Admin', 'Commissioner', 'Admin'], true);
}


/**
 * Role Page Mapper: this is a restrictive layer over the existing role and
 * department/ward checks, never a grant beyond baseline permissions.
 */
function permission_catalog(): array
{
    return [
        'dashboard.view' => 'Overview dashboard',
        'complaints.view' => 'Complaint inbox, detail and dashboards',
        'complaints.update' => 'Update complaint status / assigned officer',
        'reports.view' => 'ICTS and complaint reports / exports',
        'feedback.view' => 'Read citizen feedback',
        'feedback.manage' => 'Mark feedback as read',
        'officers.view' => 'Officer directory',
        'escalation.view' => 'Escalation monitor',
        'masters.view' => 'Master and allocation administration (view)',
        'masters.manage' => 'Edit master data and allocations',
        'users.view' => 'View employee users',
        'users.manage' => 'Create and disable employee users',
        'designations.manage' => 'Edit verified officer designations',
        'roles.view' => 'View role-page permission matrix',
        'roles.manage' => 'Edit role-page permission matrix',
    ];
}

function known_user_roles(): array
{
    return [
        'Super Admin', 'Commissioner', 'Admin', 'Department Head',
        'Zone Officer', 'Ward Officer', 'Call Centre', 'Field Officer', 'Officer', 'Report User',
    ];
}

function baseline_permission(string $role, string $permission): bool
{
    if (!array_key_exists($permission, permission_catalog())
        || !in_array($role, known_user_roles(), true)) return false;
    if ($role === 'Super Admin') return true;

    // Field Officer and Officer roles operate strictly within their personal officer portal
    if (in_array($role, ['Field Officer', 'Officer'], true)) {
        return false;
    }

    if (in_array($permission, [
        'dashboard.view', 'complaints.view', 'reports.view',
        'feedback.view', 'feedback.manage'
    ], true)) return true;
    if ($permission === 'complaints.update') return $role !== 'Report User';
    if (in_array($permission, ['officers.view', 'escalation.view'], true)) {
        return in_array($role, ['Admin', 'Commissioner', 'Department Head'], true);
    }
    if (in_array($permission, [
        'masters.view', 'masters.manage', 'users.view',
        'users.manage', 'designations.manage', 'roles.view',
    ], true)) return $role === 'Admin';
    return false;
}

/**
 * null: no customized settings (baseline applies)
 * ['invalid'=>true]: malformed or unavailable configuration; deny non-Super-Admin.
 */
function permission_matrix_state(): ?array
{
    static $loaded = false;
    static $matrix = null;
    if ($loaded) return $matrix;
    $loaded = true;
    try {
        $statement = db()->prepare('SELECT setting_value FROM system_settings WHERE setting_key=? LIMIT 1');
        $statement->execute(['role_page_permissions_v1']);
        $raw = $statement->fetchColumn();
        if ($raw === false) return null;
        $data = json_decode((string)$raw, true);
        if (!is_array($data) || ($data['version'] ?? null) !== 1
            || !isset($data['roles']) || !is_array($data['roles'])) {
            $matrix = ['invalid' => true];
            return $matrix;
        }
        $matrix = $data;
    } catch (Throwable $error) {
        log_message('Permission settings could not be read: ' . $error->getMessage());
        $matrix = ['invalid' => true];
    }
    return $matrix;
}

function can_access_permission(array $user, string $permission): bool
{
    $role = (string)($user['role'] ?? '');
    if (!baseline_permission($role, $permission)) return false;
    if ($role === 'Super Admin') return true;
    $matrix = permission_matrix_state();
    if ($matrix === null) return true;
    if (!empty($matrix['invalid'])) return false;
    if (!array_key_exists($role, $matrix['roles'])) return true;
    $rules = $matrix['roles'][$role];
    if (!is_array($rules)) return false;
    return array_key_exists($permission, $rules) && $rules[$permission] === true;
}

function require_permission(string $permission): array
{
    $user = require_login();
    if (!can_access_permission($user, $permission)) {
        http_response_code(403);
        exit('Permission denied for this action.');
    }
    return $user;
}
