<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Your City, Your Voice';
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
    if ($row) $stats = [
        'total'=>(int)$row['total'],
        'pending'=>(int)$row['pending'],
        'resolved'=>(int)$row['resolved']
    ];
} catch (Throwable $error) {
    log_message('Homepage statistics failed: '.$error->getMessage());
}
include __DIR__ . '/includes/header.php';
?>
<div class="neo-home">
    <section class="neo-hero" aria-labelledby="neo-hero-title">
        <div class="neo-hero-noise" aria-hidden="true"></div>
        <div class="container-xxl neo-hero-grid">
            <div class="neo-hero-copy">
                <div class="neo-eyebrow"><span class="neo-status-dot"></span> HOMEPAGE · TIRUPPUR CITY MUNICIPAL CORPORATION</div>
                <h1 id="neo-hero-title">Your city.<br><em>Your voice.</em></h1>
                <p class="neo-hero-description">Register a complaint, track its progress and connect with your City Municipal Corporation — all in one place.</p>
                <div class="neo-hero-cta">
                    <a class="neo-button neo-button-primary neo-magnetic" href="<?php echo e(url('new-complaint.php')); ?>">
                        <span class="neo-button-icon"><i class="bi bi-plus-lg"></i></span>
                        New Complaint <i class="bi bi-arrow-up-right neo-arrow"></i>
                    </a>
                    <a class="neo-button neo-button-ghost" href="<?php echo e(url('complaint-status.php')); ?>">
                        Track Complaint <i class="bi bi-arrow-right neo-arrow"></i>
                    </a>
                </div>
                <div class="neo-hero-assurance"><i class="bi bi-shield-check"></i> Simple to report <span>·</span> Transparent progress <span>·</span> Built for everyone</div>
            </div>

            <div class="neo-hero-visual" aria-label="Tiruppur City Municipal Corporation building">
                <div class="neo-photo-frame">
                    <div class="neo-building-photo" role="img" aria-label="Tiruppur City Municipal Corporation"></div>
                    <div class="neo-photo-glow" aria-hidden="true"></div>
                    <div class="neo-photo-corner neo-corner-tl"></div>
                    <div class="neo-photo-corner neo-corner-br"></div>
                </div>
                <div class="neo-floating-note neo-note-top" aria-hidden="true">
                    <span class="neo-floating-icon"><i class="bi bi-geo-alt-fill"></i></span>
                    <span><strong>Locally focused</strong><small>Every street. Every ward.</small></span>
                </div>
                <div class="neo-floating-note neo-note-bottom" aria-hidden="true">
                    <span class="neo-pulse-icon"><i class="bi bi-check2-circle"></i></span>
                    <span><strong>Real progress</strong><small>From report to resolution</small></span>
                </div>
                <div class="neo-orbit neo-orbit-one" aria-hidden="true"></div>
                <div class="neo-orbit neo-orbit-two" aria-hidden="true"></div>
            </div>
        </div>
        <div class="container-xxl neo-hero-bottom">
            <span><span class="neo-small-line"></span> SCROLL TO EXPLORE</span>
            <a href="#neo-impact" aria-label="Explore complaint progress"><i class="bi bi-arrow-down"></i></a>
        </div>
    </section>

    <div class="neo-ticker" aria-hidden="true">
        <div class="neo-ticker-track">
            <span>YOUR VOICE MATTERS</span><i class="bi bi-asterisk"></i>
            <span>EVERY WARD COUNTS</span><i class="bi bi-asterisk"></i>
            <span>BETTER TOGETHER</span><i class="bi bi-asterisk"></i>
            <span>YOUR VOICE MATTERS</span><i class="bi bi-asterisk"></i>
            <span>EVERY WARD COUNTS</span><i class="bi bi-asterisk"></i>
            <span>BETTER TOGETHER</span><i class="bi bi-asterisk"></i>
        </div>
    </div>

    <section class="neo-impact neo-section" id="neo-impact" aria-labelledby="neo-impact-title">
        <div class="container-xxl">
            <div class="neo-section-heading">
                <div><div class="neo-label">COMPLAINT OVERVIEW <span></span> AT A GLANCE</div>
                    <h2 id="neo-impact-title">Complaint <em>Overview.</em></h2>
                </div>
                <p>Current complaint statistics from Tiruppur City Municipal Corporation.</p>
            </div>
            <div class="neo-impact-grid">
                <div class="neo-impact-card neo-impact-primary" data-civic-counter="<?php echo (int)$stats['total']; ?>">
                    <div class="neo-impact-head"><span>01 / ALL REPORTS</span><i class="bi bi-grid-3x3-gap"></i></div>
                    <strong class="neo-impact-value"><?php echo number_format($stats['total']); ?></strong>
                    <div class="neo-impact-foot"><h3>Total Complaints</h3><p>Registered complaints</p></div>
                    <span class="neo-impact-shape" aria-hidden="true"></span>
                </div>
                <div class="neo-impact-card" data-civic-counter="<?php echo (int)$stats['resolved']; ?>">
                    <div class="neo-impact-head"><span>02 / COMPLETED</span><i class="bi bi-check2-circle"></i></div>
                    <strong class="neo-impact-value"><?php echo number_format($stats['resolved']); ?></strong>
                    <div class="neo-impact-foot"><h3>Resolved Complaints</h3><p>Resolved or closed complaints</p></div>
                </div>
                <div class="neo-impact-card" data-civic-counter="<?php echo (int)$stats['pending']; ?>">
                    <div class="neo-impact-head"><span>03 / IN ACTION</span><i class="bi bi-arrow-repeat"></i></div>
                    <strong class="neo-impact-value"><?php echo number_format($stats['pending']); ?></strong>
                    <div class="neo-impact-foot"><h3>Pending Complaints</h3><p>Registered, assigned or in-progress complaints</p></div>
                </div>
            </div>
        </div>
    </section>

    <section class="neo-journey neo-section" aria-labelledby="neo-journey-title">
        <div class="container-xxl neo-journey-grid">
            <div class="neo-journey-intro">
                <div class="neo-label">COMPLAINT SERVICES <span></span> HOW IT WORKS</div>
                <h2 id="neo-journey-title">Register and<br><em>track complaints.</em></h2>
                <p>Submit a complaint, receive a reference number and follow its progress online.</p>
                <a class="neo-text-link" href="<?php echo e(url('new-complaint.php')); ?>">New Complaint <i class="bi bi-arrow-up-right"></i></a>
            </div>
            <div class="neo-journey-steps">
                <div class="neo-step"><span class="neo-step-num">01</span><span class="neo-step-icon"><i class="bi bi-chat-left-dots"></i></span><div><h3>Register a Complaint</h3><p>Enter complaint details, select your locality and attach supporting documents.</p></div></div>
                <div class="neo-step"><span class="neo-step-num">02</span><span class="neo-step-icon"><i class="bi bi-person-check"></i></span><div><h3>Automatic Allocation</h3><p>The complaint is routed to a responsible municipal officer.</p></div></div>
                <div class="neo-step"><span class="neo-step-num">03</span><span class="neo-step-icon"><i class="bi bi-activity"></i></span><div><h3>Track Complaint Status</h3><p>Use your reference number to check updates and resolution status.</p></div></div>
            </div>
        </div>
    </section>

    <section class="neo-services neo-section" aria-labelledby="neo-services-title">
        <div class="container-xxl">
            <div class="neo-section-heading">
                <div><div class="neo-label">PUBLIC SERVICES <span></span> QUICK ACCESS</div>
                    <h2 id="neo-services-title">Quick <em>Services.</em></h2>
                </div>
                <p>What would you like to do today?</p>
            </div>
            <div class="neo-services-grid">
                <a class="neo-service neo-service-feature" href="<?php echo e(url('new-complaint.php')); ?>">
                    <span class="neo-service-top"><i class="bi bi-pencil-square"></i><span class="neo-service-arrow"><i class="bi bi-arrow-up-right"></i></span></span>
                    <span class="neo-service-bottom"><small>01 · START HERE</small><strong>New<br>Complaint</strong><span>Register a new complaint online.</span></span>
                </a>
                <a class="neo-service" href="<?php echo e(url('complaint-status.php')); ?>">
                    <span class="neo-service-top"><i class="bi bi-search"></i><span class="neo-service-arrow"><i class="bi bi-arrow-up-right"></i></span></span>
                    <span class="neo-service-bottom"><small>02 · STAY INFORMED</small><strong>Track<br>Complaint</strong><span>Check your complaint status and progress.</span></span>
                </a>
                <a class="neo-service" href="<?php echo e(url('department-contacts.php')); ?>">
                    <span class="neo-service-top"><i class="bi bi-people"></i><span class="neo-service-arrow"><i class="bi bi-arrow-up-right"></i></span></span>
                    <span class="neo-service-bottom"><small>03 · FIND THE RIGHT PERSON</small><strong>Contact<br>Us</strong><span>Contact municipal offices and support.</span></span>
                </a>
                <a class="neo-service" href="<?php echo e(url('feedback.php')); ?>">
                    <span class="neo-service-top"><i class="bi bi-heart"></i><span class="neo-service-arrow"><i class="bi bi-arrow-up-right"></i></span></span>
                    <span class="neo-service-bottom"><small>04 · HAVE YOUR SAY</small><strong>Feedback</strong><span>Share feedback about municipal services.</span></span>
                </a>
            </div>
        </div>
    </section>

    <section class="neo-last-cta" aria-labelledby="neo-cta-title">
        <div class="container-xxl neo-last-cta-inner">
            <div class="neo-last-cta-copy">
                <div class="neo-label">TIRUPPUR CITY MUNICIPAL CORPORATION</div>
                <h2 id="neo-cta-title">Your city.<br><em>Your voice.</em></h2>
                <p>Register a complaint and track its progress online.</p>
                <a class="neo-button neo-button-primary neo-button-light" href="<?php echo e(url('new-complaint.php')); ?>">New Complaint <i class="bi bi-arrow-up-right"></i></a>
            </div>
            <div class="neo-last-cta-mark" aria-hidden="true">T<span>✦</span></div>
        </div>
    </section>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
