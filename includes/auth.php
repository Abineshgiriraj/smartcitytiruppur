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
