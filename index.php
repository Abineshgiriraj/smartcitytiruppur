<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle='Your City, Your Voice';
$activePage='home';
$showQuickServices=false;
$homepageDesignDemo=true;
$stats=['total'=>0,'pending'=>0,'resolved'=>0];
try {
    $row=db()->query(
        "SELECT COUNT(*) AS total,
          COALESCE(SUM(CASE WHEN status IN ('Registered','Assigned','Acknowledged','In Progress','Reopened') THEN 1 ELSE 0 END),0) AS pending,
          COALESCE(SUM(CASE WHEN status IN ('Resolved','Closed') THEN 1 ELSE 0 END),0) AS resolved
          FROM complaints WHERE is_deleted=0"
    )->fetch(PDO::FETCH_ASSOC);
    if ($row) foreach (array_keys($stats) as $name) $stats[$name]=(int)$row[$name];
} catch (Throwable $error) {
    log_message('Homepage statistics failed: '.$error->getMessage());
}
include __DIR__ . '/includes/header.php';
?>
<div class="demo-portal">
    <section class="dp-design-switch" aria-label="Homepage appearance">
        <div class="dp-wrap">
            <span class="dp-switch-label"><i class="bi bi-window-stack" aria-hidden="true"></i> Choose homepage view</span>
            <div class="dp-switch-actions" role="group" aria-label="Homepage layout options">
                <button type="button" data-home-layout="hub" aria-pressed="true">Civic Service Hub</button>
                <button type="button" data-home-layout="photo" aria-pressed="false">Photo-led Homepage</button>
            </div>
        </div>
    </section>

    <section class="dp-hero" aria-labelledby="dp-hero-heading">
        <div class="dp-wrap dp-split">
            <div class="dp-hero-copy">
                <p class="dp-kicker">Tiruppur City Municipal Corporation</p>
                <h1 id="dp-hero-heading">Your city.<br><span>Your voice.</span></h1>
                <p class="dp-subtitle">Register a local complaint and track its progress with your complaint reference number.</p>
                <div class="dp-actions">
                    <a class="dp-button dp-button-primary" href="<?php echo e(url('new-complaint.php')); ?>">
                        <i class="bi bi-pencil-square" aria-hidden="true"></i> New Complaint
                    </a>
                    <a class="dp-button dp-button-secondary dp-hero-secondary" href="<?php echo e(url('complaint-status.php')); ?>">
                        <i class="bi bi-search" aria-hidden="true"></i> Track Complaint
                    </a>
                </div>
            </div>
            <div class="dp-hero-image">
                <img class="dp-hero-photo" src="<?php echo e(url('assets/images/smart-bg.jpg')); ?>"
                    width="1280" height="720" alt="Tiruppur City Municipal Corporation building">
            </div>
        </div>
    </section>

    <section class="dp-utility" aria-labelledby="dp-tracking-title">
        <div class="dp-wrap">
            <div class="dp-track">
                <div>
                    <h2 id="dp-tracking-title">Track your complaint</h2>
                    <p>Enter your real grievance reference number to check the latest status.</p>
                </div>
                <form class="dp-track-form" method="get" action="<?php echo e(url('complaint-status.php')); ?>">
                    <div class="dp-field">
                        <label for="dp-reference">Complaint reference</label>
                        <input id="dp-reference" type="text" name="reference_no" maxlength="40"
                               placeholder="TCMC-2026-000001" required autocomplete="off">
                    </div>
                    <button class="dp-button dp-button-primary" type="submit">
                        Track <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </button>
                </form>
            </div>

            <div class="dp-services">
                <div class="dp-section-heading">
                    <h2>Municipal Services</h2>
                    <p>Choose a service to register your complaint.</p>
                </div>
                <div class="dp-service-grid">
                    <a class="dp-service-card" href="<?php echo e(url('new-complaint.php')); ?>">
                        <span class="dp-service-icon"><i class="bi bi-recycle" aria-hidden="true"></i></span>
                        <span><strong>Solid Waste Management</strong><br><small>Garbage and collection</small></span>
                        <i class="bi bi-arrow-up-right dp-service-arrow" aria-hidden="true"></i>
                    </a>
                    <a class="dp-service-card" href="<?php echo e(url('new-complaint.php')); ?>">
                        <span class="dp-service-icon"><i class="bi bi-signpost-split" aria-hidden="true"></i></span>
                        <span><strong>Roads &amp; Infrastructure</strong><br><small>Roads and footpaths</small></span>
                        <i class="bi bi-arrow-up-right dp-service-arrow" aria-hidden="true"></i>
                    </a>
                    <a class="dp-service-card" href="<?php echo e(url('new-complaint.php')); ?>">
                        <span class="dp-service-icon"><i class="bi bi-lightbulb" aria-hidden="true"></i></span>
                        <span><strong>Street Lighting</strong><br><small>Lights and repairs</small></span>
                        <i class="bi bi-arrow-up-right dp-service-arrow" aria-hidden="true"></i>
                    </a>
                    <a class="dp-service-card" href="<?php echo e(url('new-complaint.php')); ?>">
                        <span class="dp-service-icon"><i class="bi bi-droplet-half" aria-hidden="true"></i></span>
                        <span><strong>Water &amp; Drainage</strong><br><small>Supply and drainage</small></span>
                        <i class="bi bi-arrow-up-right dp-service-arrow" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <div class="dp-stats" aria-label="Current complaint overview">
                <div class="dp-stat"><strong><?php echo number_format($stats['total']); ?></strong><span>Total Complaints</span></div>
                <div class="dp-stat"><strong><?php echo number_format($stats['resolved']); ?></strong><span>Resolved or Closed</span></div>
                <div class="dp-stat"><strong><?php echo number_format($stats['pending']); ?></strong><span>Open Complaints</span></div>
            </div>
            <div class="dp-foot-note">Complaint statistics are retrieved from the municipal grievance database.</div>
        </div>
    </section>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
