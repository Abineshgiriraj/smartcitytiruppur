<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Home';
$activePage = 'home';
$showQuickServices = false;
$stats = ['total' => 0, 'pending' => 0, 'resolved' => 0];

try {
    $row = db()->query(
        "SELECT COUNT(*) AS total,
                SUM(CASE WHEN status IN ('Registered','Assigned','Acknowledged','In Progress','Reopened') THEN 1 ELSE 0 END) AS pending,
                SUM(CASE WHEN status IN ('Resolved','Closed') THEN 1 ELSE 0 END) AS resolved
         FROM complaints WHERE is_deleted = 0"
    )->fetch();
    if ($row) {
        $stats = ['total'=>(int)$row['total'],'pending'=>(int)$row['pending'],'resolved'=>(int)$row['resolved']];
    }
} catch (Throwable $exception) {
    log_message('Homepage statistics failed: ' . $exception->getMessage());
}

include __DIR__ . '/includes/header.php';
?>
<!-- Presentation-only home redesign: all links, counters and source queries retained. -->
<section class="city-landing" aria-labelledby="cityHomeTitle">
  <div class="city-hero">
    <div class="container-xxl city-hero-inner">
      <div class="city-hero-copy">
        <div class="city-kicker"><span class="city-kicker-dot"></span> HomePage <span class="city-kicker-rule"></span> Tiruppur City Municipal Corporation</div>
        <h1 id="cityHomeTitle">Your city.<br><span>Your voice.</span></h1>
        <p class="city-hero-intro">Register a complaint, track its progress and connect with your City Municipal Corporation — all in one place.</p>
        <div class="city-hero-actions">
          <a class="city-action-primary" href="<?php echo e(url('new-complaint.php')); ?>"><i class="bi bi-pencil-square" aria-hidden="true"></i> New Complaint <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
          <a class="city-action-secondary" href="<?php echo e(url('complaint-status.php')); ?>"><i class="bi bi-search" aria-hidden="true"></i> Track Complaint <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </div>
      </div>
      <div class="city-hero-visual" aria-hidden="true">
        <div class="city-hero-photo"></div>
        <div class="city-hero-photo-caption">
          <span class="city-hero-caption-icon"><i class="bi bi-buildings"></i></span>
          <span><strong>Tiruppur City</strong><small>Municipal Corporation</small></span>
        </div>
        <div class="city-hero-ornament city-hero-ornament-one"></div>
        <div class="city-hero-ornament city-hero-ornament-two"></div>
      </div>
    </div>
  </div>

  <div class="city-stats-section">
    <div class="container-xxl">
      <div class="city-stats-heading"><span>COMPLAINT OVERVIEW</span><strong>At a glance</strong></div>
      <div class="city-stats-grid" aria-label="Complaint statistics">
        <div class="city-stat city-stat-resolved">
          <div class="city-stat-top"><span class="city-stat-icon"><i class="bi bi-check2-circle" aria-hidden="true"></i></span><span class="city-stat-index">01</span></div>
          <strong class="city-stat-number"><?php echo number_format($stats['resolved']); ?></strong>
          <span class="city-stat-label">Resolved Complaints</span>
        </div>
        <div class="city-stat city-stat-pending">
          <div class="city-stat-top"><span class="city-stat-icon"><i class="bi bi-hourglass-split" aria-hidden="true"></i></span><span class="city-stat-index">02</span></div>
          <strong class="city-stat-number"><?php echo number_format($stats['pending']); ?></strong>
          <span class="city-stat-label">Pending Complaints</span>
        </div>
        <div class="city-stat city-stat-total">
          <div class="city-stat-top"><span class="city-stat-icon"><i class="bi bi-grid-3x3-gap" aria-hidden="true"></i></span><span class="city-stat-index">03</span></div>
          <strong class="city-stat-number"><?php echo number_format($stats['total']); ?></strong>
          <span class="city-stat-label">Total Complaints</span>
        </div>
      </div>
    </div>
  </div>

  <section class="city-services-section" aria-labelledby="cityServicesTitle">
    <div class="container-xxl">
      <div class="city-services-heading">
        <div><span class="city-section-eyebrow">PUBLIC SERVICES</span><h2 id="cityServicesTitle">Quick Services</h2></div>
        <p>What would you like to do today?</p>
      </div>
      <div class="city-services-grid">
        <a class="city-service-card" href="<?php echo e(url('new-complaint.php')); ?>">
          <span class="city-service-top"><span class="city-service-icon service-blue"><i class="bi bi-pencil-square" aria-hidden="true"></i></span><i class="bi bi-arrow-up-right city-service-arrow" aria-hidden="true"></i></span>
          <strong>New Complaint</strong><small>What is troubling you?</small>
          <span class="city-service-foot">Get started <i class="bi bi-arrow-right" aria-hidden="true"></i></span>
        </a>
        <a class="city-service-card" href="<?php echo e(url('complaint-status.php')); ?>">
          <span class="city-service-top"><span class="city-service-icon service-green"><i class="bi bi-search" aria-hidden="true"></i></span><i class="bi bi-arrow-up-right city-service-arrow" aria-hidden="true"></i></span>
          <strong>Track Complaint</strong><small>What happened so far?</small>
          <span class="city-service-foot">View status <i class="bi bi-arrow-right" aria-hidden="true"></i></span>
        </a>
        <a class="city-service-card" href="<?php echo e(url('contact.php')); ?>">
          <span class="city-service-top"><span class="city-service-icon service-orange"><i class="bi bi-chat-dots" aria-hidden="true"></i></span><i class="bi bi-arrow-up-right city-service-arrow" aria-hidden="true"></i></span>
          <strong>Contact Us</strong><small>Get in touch with us</small>
          <span class="city-service-foot">Contact details <i class="bi bi-arrow-right" aria-hidden="true"></i></span>
        </a>
        <a class="city-service-card" href="<?php echo e(url('feedback.php')); ?>">
          <span class="city-service-top"><span class="city-service-icon service-purple"><i class="bi bi-megaphone" aria-hidden="true"></i></span><i class="bi bi-arrow-up-right city-service-arrow" aria-hidden="true"></i></span>
          <strong>Feedback</strong><small>Tell us what you think.</small>
          <span class="city-service-foot">Share feedback <i class="bi bi-arrow-right" aria-hidden="true"></i></span>
        </a>
      </div>
    </div>
  </section>

  <div class="city-help-strip">
    <div class="container-xxl city-help-inner">
      <div class="city-help-message"><span class="city-help-icon"><i class="bi bi-headset" aria-hidden="true"></i></span><span><strong>Customer Support</strong><small>We're here to help with your complaint.</small></span></div>
      <div class="city-help-options">
        <a href="tel:155304"><i class="bi bi-telephone" aria-hidden="true"></i> 155304</a>
        <a href="mailto:<?php echo e(setting('support_email', 'commr.Tiruppur@tn.gov.in')); ?>"><i class="bi bi-envelope" aria-hidden="true"></i> <?php echo e(setting('support_email', 'commr.Tiruppur@tn.gov.in')); ?></a>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
