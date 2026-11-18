<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Contact Us';
$activePage = 'contacts';
include __DIR__ . '/includes/header.php';
?>
<section class="page-banner"><div class="container-xxl"><h1 class="fw-bold">Contact Us</h1></div></section>
<div class="container-xxl py-5">
    <div class="card content-card p-4 p-lg-5">
        <div class="row g-4">
            <div class="col-md-6"><h2 class="h5 fw-bold">Tiruppur City Municipal Corporation</h2><p><?php echo nl2br(e(setting('office_address', 'Tiruppur City Municipal Corporation, Tiruppur, Tamil Nadu.'))); ?></p></div>
            <div class="col-md-6"><p><strong>Phone:</strong> <?php echo e(setting('support_phone', '+91 421 2240153')); ?></p><p><strong>Email:</strong> <?php echo e(setting('support_email', 'commr.tiruppur@tn.gov.in')); ?></p></div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
