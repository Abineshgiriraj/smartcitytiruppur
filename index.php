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
<section class="original-hero">
    <div class="container-xxl">
        <div class="home-layout">
            <div>
                <h1 class="hero-title">HomePage</h1>
                <div class="stats-grid">
                    <div class="stat-block">
                        <div><div class="stat-number"><?php echo number_format($stats['resolved']); ?></div><div class="stat-label">Resolved Complaints</div></div>
                        <div class="stat-icon"><i class="bi bi-person-check"></i></div>
                    </div>
                    <div class="stat-block">
                        <div><div class="stat-number"><?php echo number_format($stats['pending']); ?></div><div class="stat-label">Pending Complaints</div></div>
                        <div class="stat-icon"><i class="bi bi-three-dots"></i></div>
                    </div>
                    <div class="stat-block total-stat">
                        <div><div class="stat-number"><?php echo number_format($stats['total']); ?></div><div class="stat-label">Total Complaints</div></div>
                        <div class="stat-icon"><i class="bi bi-diagram-3"></i></div>
                    </div>
                </div>
            </div>

            <aside class="side-service-panel" aria-label="Quick Services">
                <div class="side-service-title">Quick Services</div>
                <a class="side-service-item" href="<?php echo e(url('new-complaint.php')); ?>"><i class="bi bi-pencil-square"></i><span><strong>New Complaint</strong><small>What is troubling you?</small></span></a>
                <a class="side-service-item" href="<?php echo e(url('complaint-status.php')); ?>"><i class="bi bi-search"></i><span><strong>Track Complaint</strong><small>What happened so far?</small></span></a>
                <a class="side-service-item" href="<?php echo e(url('contact.php')); ?>"><i class="bi bi-chat-dots"></i><span><strong>Contact Us</strong><small>Get in touch with us</small></span></a>
                <a class="side-service-item" href="<?php echo e(url('feedback.php')); ?>"><i class="bi bi-megaphone"></i><span><strong>Feedback</strong><small>Tell us what you think.</small></span></a>
            </aside>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
