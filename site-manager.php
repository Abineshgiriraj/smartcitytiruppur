<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Site Manager';
$activePage = 'contacts';

$manager = [
    'name' => setting('site_manager_name', 'Not configured'),
    'designation' => setting('site_manager_designation', 'Site Manager'),
    'email' => setting('site_manager_email', ''),
    'mobile' => setting('site_manager_mobile', ''),
];

include __DIR__ . '/includes/header.php';
?>
<section class="page-banner"><div class="container-xxl"><h1 class="fw-bold">Site Manager</h1><p class="mb-0">Official contact for website administration.</p></div></section>

<div class="container-xxl py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card content-card p-5 text-center">
                <div class="service-icon mx-auto mb-3">SM</div>
                <h2 class="h3 fw-bold"><?php echo e($manager['name']); ?></h2>
                <p class="text-muted"><?php echo e($manager['designation']); ?></p>
                <?php if ($manager['email']): ?><p><strong>Email:</strong> <?php echo e($manager['email']); ?></p><?php endif; ?>
                <?php if ($manager['mobile']): ?><p><strong>Mobile:</strong> <?php echo e($manager['mobile']); ?></p><?php endif; ?>
                <?php if (!$manager['email'] && !$manager['mobile']): ?><p class="text-muted">Contact information has not been configured.</p><?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
