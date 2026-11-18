<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Site Map';
include __DIR__ . '/includes/header.php';
$links = [
    'Home' => 'index.php',
    'New Complaint' => 'new-complaint.php',
    'Complaint Status' => 'complaint-status.php',
    'Escalation Period' => 'escalation-period.php',
    'Department Contacts' => 'department-contacts.php',
    'Grievance Redressal Contacts' => 'grievance-contacts.php',
    'Site Manager' => 'site-manager.php',
    'Dashboard' => 'dashboard.php',
    'Feedback' => 'feedback.php',
    'Contact Us' => 'contact.php',
    'FAQ' => 'faq.php',
    'Help' => 'help.php',
    'Privacy Policy' => 'privacy-policy.php',
    'Employee Login' => 'employee/login.php',
];
?>
<section class="page-banner"><div class="container-xxl"><h1 class="fw-bold">Site Map</h1></div></section>
<div class="container-xxl py-5"><div class="card content-card p-4"><div class="row g-3"><?php foreach ($links as $label => $link): ?><div class="col-md-6 col-lg-4"><a class="btn btn-outline-primary w-100 text-start" href="<?php echo e(url($link)); ?>"><?php echo e($label); ?></a></div><?php endforeach; ?></div></div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
