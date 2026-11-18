<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

$pageTitle = 'Forgot Password';
$errors = [];
$localResetUrl = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email = strtolower(trim($_POST['email'] ?? ''));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    } else {
        $stmt = db()->prepare('SELECT id FROM users WHERE email = ? AND is_active = 1 LIMIT 1');
        $stmt->execute([$email]);
        $userId = $stmt->fetchColumn();

        if ($userId !== false) {
            $token = bin2hex(random_bytes(32));
            $hash = hash('sha256', $token);
            $expiresAt = date('Y-m-d H:i:s', time() + 1800);

            db()->prepare(
                'INSERT INTO password_reset_tokens (user_id, token_hash, expires_at)
                 VALUES (?, ?, ?)'
            )->execute([(int)$userId, $hash, $expiresAt]);

            $resetUrl = url('employee/reset-password.php?token=' . urlencode($token));

            // Connect an approved email service here in production.
            if (APP_ENV === 'local') {
                $localResetUrl = $resetUrl;
            }
        }

        flash('success', 'If an active account exists for that email, a reset instruction has been created.');
        if (!$localResetUrl) {
            redirect('employee/forgot-password.php');
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>
<section class="page-banner"><div class="container"><h1 class="fw-bold">Forgot Password</h1></div></section>
<div class="container py-5">
    <div class="row justify-content-center"><div class="col-lg-5">
        <?php if ($errors): ?><div class="alert alert-danger"><?php echo e(implode(' ', $errors)); ?></div><?php endif; ?>
        <?php if ($localResetUrl): ?><div class="alert alert-info">Local development reset link:<br><a href="<?php echo e($localResetUrl); ?>"><?php echo e($localResetUrl); ?></a></div><?php endif; ?>
        <form method="post" class="card content-card p-4">
            <?php echo csrf_field(); ?>
            <label class="form-label required">Registered Email</label>
            <input class="form-control mb-3" type="email" name="email" required>
            <button class="btn btn-primary">Create Reset Request</button>
        </form>
    </div></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
