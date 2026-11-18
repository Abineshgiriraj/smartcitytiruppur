<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

$count = (int)db()->query('SELECT COUNT(*) FROM users')->fetchColumn();

if ($count > 0) {
    redirect('employee/login.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim($_POST['full_name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $mobile = normalize_mobile($_POST['mobile_no'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['password_confirmation'] ?? '';

    if ($name === '') $errors[] = 'Full name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email.';
    if ($mobile !== '' && !preg_match('/^[6-9]\d{9}$/', $mobile)) $errors[] = 'Enter a valid mobile number.';
    if (strlen($password) < 8) $errors[] = 'Password must contain at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'Password confirmation does not match.';

    if (!$errors) {
        $stmt = db()->prepare(
            "INSERT INTO users
             (full_name, email, mobile_no, password_hash, role, is_active, created_at, updated_at)
             VALUES (?, ?, ?, ?, 'Super Admin', 1, NOW(), NOW())"
        );
        $stmt->execute([
            $name,
            $email,
            $mobile !== '' ? $mobile : null,
            password_hash($password, PASSWORD_DEFAULT),
        ]);

        flash('success', 'Super Admin account created. You can now log in.');
        redirect('employee/login.php');
    }
}

$pageTitle = 'Create First Administrator';
include __DIR__ . '/../includes/header.php';
?>
<section class="page-banner"><div class="container"><h1 class="fw-bold">Initial Administrator Setup</h1><p class="mb-0">This page works only while the users table is empty.</p></div></section>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?php echo e($error); ?></li><?php endforeach; ?></ul></div><?php endif; ?>
            <form method="post" class="card content-card p-4">
                <?php echo csrf_field(); ?>
                <div class="mb-3"><label class="form-label required">Full Name</label><input class="form-control" name="full_name" required></div>
                <div class="mb-3"><label class="form-label required">Email</label><input class="form-control" type="email" name="email" required></div>
                <div class="mb-3"><label class="form-label">Mobile Number</label><input class="form-control" name="mobile_no" maxlength="10"></div>
                <div class="mb-3"><label class="form-label required">Password</label><input class="form-control" type="password" name="password" required></div>
                <div class="mb-3"><label class="form-label required">Confirm Password</label><input class="form-control" type="password" name="password_confirmation" required></div>
                <button class="btn btn-primary btn-lg" type="submit">Create Super Admin</button>
            </form>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
