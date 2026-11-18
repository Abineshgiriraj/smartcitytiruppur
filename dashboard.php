<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Public Dashboard';
$activePage = 'dashboard';

$summary = ['total' => 0, 'pending' => 0, 'resolved' => 0, 'overdue' => 0];
$frequent = [];
$monthly = [];

try {
    $summaryRow = db()->query(
        "SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN status IN ('Registered','Assigned','Acknowledged','In Progress','Reopened') THEN 1 ELSE 0 END) AS pending,
            SUM(CASE WHEN status IN ('Resolved','Closed') THEN 1 ELSE 0 END) AS resolved,
            SUM(CASE WHEN due_at < NOW() AND status NOT IN ('Resolved','Closed','Rejected') THEN 1 ELSE 0 END) AS overdue
         FROM complaints WHERE is_deleted = 0"
    )->fetch();

    if ($summaryRow) {
        foreach ($summary as $key => $value) {
            $summary[$key] = (int)($summaryRow[$key] ?? 0);
        }
    }

    $frequent = db()->query(
        "SELECT cc.category_name,
                COUNT(*) AS total,
                SUM(CASE WHEN c.status IN ('Registered','Assigned','Acknowledged','In Progress','Reopened') THEN 1 ELSE 0 END) AS pending,
                SUM(CASE WHEN c.status IN ('Resolved','Closed') THEN 1 ELSE 0 END) AS resolved
         FROM complaints c
         JOIN complaint_categories cc ON cc.id = c.category_id
         WHERE c.is_deleted = 0
         GROUP BY cc.id, cc.category_name
         ORDER BY total DESC
         LIMIT 10"
    )->fetchAll();

    $monthly = db()->query(
        "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month_key,
                DATE_FORMAT(created_at, '%b %Y') AS month_label,
                COUNT(*) AS total
         FROM complaints
         WHERE is_deleted = 0 AND created_at >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH)
         GROUP BY month_key, month_label
         ORDER BY month_key"
    )->fetchAll();
} catch (Throwable $exception) {
    log_message('Public dashboard failed: ' . $exception->getMessage());
}

$showQuickServices = false;
include __DIR__ . '/includes/header.php';
?>
<section class="original-hero">
    <div class="container-xxl">
        <h1 class="hero-title">Dashboard</h1>
        <div class="dashboard-stat-grid">
            <div class="stat-block"><div><div class="stat-number"><?php echo number_format($summary['resolved']); ?></div><div class="stat-label">Resolved Complaints</div></div><div class="stat-icon"><i class="bi bi-check2-circle"></i></div></div>
            <div class="stat-block"><div><div class="stat-number"><?php echo number_format($summary['pending']); ?></div><div class="stat-label">Pending Complaints</div></div><div class="stat-icon"><i class="bi bi-hourglass-split"></i></div></div>
            <div class="stat-block"><div><div class="stat-number"><?php echo number_format($summary['total']); ?></div><div class="stat-label">Total Complaints</div></div><div class="stat-icon"><i class="bi bi-grid-3x3-gap"></i></div></div>
        </div>

        <h2 class="frequent-heading">Most Frequent</h2>
        <div class="frequent-grid">
            <?php if (!$frequent): ?>
                <div class="frequent-card"><div class="frequent-icon"><i class="bi bi-inbox"></i></div><div><h3>No complaint data available</h3></div></div>
            <?php endif; ?>
            <?php foreach (array_slice($frequent, 0, 8) as $index => $row): ?>
                <article class="frequent-card">
                    <div class="frequent-icon"><i class="bi <?php echo e(['bi-droplet','bi-receipt','bi-lightbulb','bi-bug','bi-trash3','bi-water','bi-tools','bi-buildings'][$index % 8]); ?>"></i></div>
                    <div>
                        <h3><?php echo e($row['category_name']); ?></h3>
                        <div class="frequent-numbers">
                            <span>Total<b><?php echo (int)$row['total']; ?></b></span>
                            <span>Pending<b><?php echo (int)$row['pending']; ?></b></span>
                            <span>Closed<b><?php echo (int)$row['resolved']; ?></b></span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ($monthly): ?>
        <div class="monthly-panel">
            <h2 class="h5 mb-4">Monthly Complaint Trend</h2>
            <?php $maximum = max(array_map(fn($row) => (int)$row['total'], $monthly)); ?>
            <?php foreach ($monthly as $row): ?>
                <?php $percent = $maximum > 0 ? round(((int)$row['total'] / $maximum) * 100) : 0; ?>
                <div class="mb-3"><div class="d-flex justify-content-between small mb-1"><span><?php echo e($row['month_label']); ?></span><strong><?php echo (int)$row['total']; ?></strong></div><div class="progress" style="height:8px"><div class="progress-bar" style="width:<?php echo $percent; ?>%"></div></div></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
