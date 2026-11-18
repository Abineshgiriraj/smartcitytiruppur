<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

if (current_user()) {
    redirect('employee/dashboard.php');
}

$errors = [];
$userCount = 0;

try {
    $userCount = (int)db()->query('SELECT COUNT(*) FROM users')->fetchColumn();
} catch (Throwable $exception) {
    $errors[] = 'Database tables are not installed. Import database/schema.sql first.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!login_user($email, $password)) {
        $errors[] = 'Invalid email or password.';
    } else {
        redirect('employee/dashboard.php');
    }
}

$pageTitle = 'Employee Login';
include __DIR__ . '/../includes/header.php';
?>
<section class="page-banner"><div class="container"><h1 class="fw-bold">Employee Login</h1><p class="mb-0">Authorized Tiruppur City Municipal Corporation employees only.</p></div></section>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?php echo e($error); ?></li><?php endforeach; ?></ul></div><?php endif; ?>

            <form method="post" class="card content-card p-4 p-lg-5">
                <?php echo csrf_field(); ?>
                <div class="mb-3"><label class="form-label required">Email</label><input class="form-control form-control-lg" type="email" name="email" required></div>
                <div class="mb-3"><label class="form-label required">Password</label><input class="form-control form-control-lg" type="password" name="password" required></div>
                <button class="btn btn-primary btn-lg w-100" type="submit">Login</button><div class="text-center mt-3"><a href="<?php echo e(url('employee/forgot-password.php')); ?>">Forgot password?</a></div>
            </form>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
