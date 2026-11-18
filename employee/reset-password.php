<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

$pageTitle = 'Reset Password';
$token = trim($_GET['token'] ?? $_POST['token'] ?? '');
$errors = [];

$tokenRecord = null;
if ($token !== '') {
    $hash = hash('sha256', $token);
    $stmt = db()->prepare(
        'SELECT id, user_id, expires_at, used_at
         FROM password_reset_tokens
         WHERE token_hash = ?
         ORDER BY id DESC LIMIT 1'
    );
    $stmt->execute([$hash]);
    $tokenRecord = $stmt->fetch();
}

if (!$tokenRecord || $tokenRecord['used_at'] || strtotime($tokenRecord['expires_at']) < time()) {
    $errors[] = 'The password reset link is invalid or expired.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$errors) {
    verify_csrf();

    $password = $_POST['password'] ?? '';
    $confirm = $_POST['password_confirmation'] ?? '';

    if (strlen($password) < 8) $errors[] = 'Password must contain at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'Password confirmation does not match.';

    if (!$errors) {
        $pdo = db();
        $pdo->beginTransaction();
        try {
            $pdo->prepare('UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?')
                ->execute([password_hash($password, PASSWORD_DEFAULT), (int)$tokenRecord['user_id']]);
            $pdo->prepare('UPDATE password_reset_tokens SET used_at = NOW() WHERE id = ?')
                ->execute([(int)$tokenRecord['id']]);
            $pdo->commit();
            flash('success', 'Password reset successfully. You can now log in.');
            redirect('employee/login.php');
        } catch (Throwable $exception) {
            $pdo->rollBack();
            $errors[] = 'Unable to reset the password.';
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>
<section class="page-banner"><div class="container"><h1 class="fw-bold">Reset Password</h1></div></section>
<div class="container py-5">
    <div class="row justify-content-center"><div class="col-lg-5">
        <?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?php echo e($error); ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <?php if ($tokenRecord && !$errors): ?>
        <form method="post" class="card content-card p-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="token" value="<?php echo e($token); ?>">
            <div class="mb-3"><label class="form-label required">New Password</label><input class="form-control" type="password" name="password" required></div>
            <div class="mb-3"><label class="form-label required">Confirm Password</label><input class="form-control" type="password" name="password_confirmation" required></div>
            <button class="btn btn-primary">Reset Password</button>
        </form>
        <?php endif; ?>
    </div></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
