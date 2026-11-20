<?php
declare(strict_types=1);

function current_user(): ?array
{
    static $loaded = false;
    static $user = null;

    if ($loaded) {
        return $user;
    }

    $loaded = true;
    $userId = $_SESSION['user_id'] ?? null;

    if (!$userId) {
        return null;
    }

    $stmt = db()->prepare(
        'SELECT id, full_name, email, mobile_no, role, department_id, zone_id, ward_id, is_active
         FROM users WHERE id = ? LIMIT 1'
    );
    $stmt->execute([(int) $userId]);
    $record = $stmt->fetch();

    if (!$record || !(int) $record['is_active']) {
        unset($_SESSION['user_id']);
        return null;
    }

    $user = $record;
    return $user;
}

function login_user(string $email, string $password): bool
{
    $stmt = db()->prepare(
        'SELECT id, password_hash, is_active FROM users WHERE email = ? LIMIT 1'
    );
    $stmt->execute([strtolower(trim($email))]);
    $user = $stmt->fetch();

    if (!$user || !(int) $user['is_active']) {
        return false;
    }

    if (!password_verify($password, $user['password_hash'])) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];

    db()->prepare('UPDATE users SET last_login_at = NOW() WHERE id = ?')
        ->execute([(int) $user['id']]);

    audit_log((int) $user['id'], 'login', 'user', (int) $user['id']);

    return true;
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
        'Zone Officer', 'Ward Officer', 'Call Centre', 'Field Officer', 'Report User',
    ];
}

function baseline_permission(string $role, string $permission): bool
{
    if (!array_key_exists($permission, permission_catalog())
        || !in_array($role, known_user_roles(), true)) return false;
    if ($role === 'Super Admin') return true;
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
