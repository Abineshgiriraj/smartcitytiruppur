<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Feedback';
$activePage = 'feedback';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim($_POST['name'] ?? '');
    $mobile = normalize_mobile($_POST['mobile_no'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['feedback'] ?? '');

    if ($name === '') $errors[] = 'Name is required.';
    if (!preg_match('/^[6-9]\d{9}$/', $mobile)) $errors[] = 'Enter a valid mobile number.';
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if ($message === '') $errors[] = 'Feedback is required.';

    if (!$errors) {
        $stmt = db()->prepare(
            'INSERT INTO feedback (name, mobile_no, email, feedback_text, created_at)
             VALUES (?, ?, ?, ?, NOW())'
        );
        $stmt->execute([$name, $mobile, $email !== '' ? $email : null, $message]);
        flash('success', 'Thank you. Your feedback has been submitted.');
        redirect('feedback.php');
    }
}

include __DIR__ . '/includes/header.php';
?>
<section class="page-banner"><div class="container-xxl"><h1 class="fw-bold">Feedback</h1><p class="mb-0">Tell us about your experience with municipal grievance services.</p></div></section>

<div class="container-xxl py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?php echo e($error); ?></li><?php endforeach; ?></ul></div><?php endif; ?>
            <form method="post" class="card content-card p-4 p-lg-5">
                <?php echo csrf_field(); ?>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label required">Name</label><input class="form-control" name="name" required></div>
                    <div class="col-md-6"><label class="form-label required">Mobile Number</label><input class="form-control" name="mobile_no" maxlength="10" required></div>
                    <div class="col-12"><label class="form-label">Email</label><input class="form-control" type="email" name="email"></div>
                    <div class="col-12"><label class="form-label required">Feedback</label><textarea class="form-control" name="feedback" rows="6" required></textarea></div>
                    <div class="col-12"><button class="btn btn-primary btn-lg" type="submit">Submit Feedback</button></div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
