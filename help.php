<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Help';
include __DIR__ . '/includes/header.php';
?>
<section class="page-banner"><div class="container-xxl"><h1>Help</h1><p>Guidance for registering and tracking municipal complaints.</p></div></section>
<div class="container-xxl py-5"><div class="card content-card p-4 p-lg-5"><div class="row g-4"><div class="col-lg-6"><h2 class="h5 fw-bold">Register a complaint</h2><ol><li>Open New Complaint.</li><li>Verify your mobile number using OTP.</li><li>Select locality, ward, zone, department and complaint category.</li><li>Add the complaint description and permitted attachments.</li><li>Save the reference number shown after submission.</li></ol></div><div class="col-lg-6"><h2 class="h5 fw-bold">Track a complaint</h2><ol><li>Open Complaint Status.</li><li>Search with the complaint reference number, or verify the registered mobile number.</li><li>View status, officer, location and complaint details.</li><li>Use the status timeline to review progress.</li></ol></div><div class="col-12"><div class="alert alert-info mb-0">For assistance, call <strong>155304</strong>, WhatsApp <strong>7305712225</strong>, or email <strong><?php echo e(setting('support_email', 'commr.tiruppur@tn.gov.in')); ?></strong>.</div></div></div></div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
