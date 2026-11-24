<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

$auth = require_officer();
$user = $auth['user'];
$officer = $auth['officer'];
$officerId = (int)$officer['id'];

$pageTitle = 'Citizen Feedback & Ratings';

// 1. Summary Statistics
$statsStmt = db()->prepare('
    SELECT 
        COUNT(f.id) as total_reviews,
        AVG(f.rating) as avg_rating,
        SUM(CASE WHEN f.rating = 5 THEN 1 ELSE 0 END) as star_5,
        SUM(CASE WHEN f.rating = 4 THEN 1 ELSE 0 END) as star_4,
        SUM(CASE WHEN f.rating = 3 THEN 1 ELSE 0 END) as star_3,
        SUM(CASE WHEN f.rating = 2 THEN 1 ELSE 0 END) as star_2,
        SUM(CASE WHEN f.rating = 1 THEN 1 ELSE 0 END) as star_1
    FROM feedback f
    JOIN complaints c ON c.id = f.complaint_id
    WHERE c.assigned_officer_id = ?
');
$statsStmt->execute([$officerId]);
$stats = $statsStmt->fetch() ?: [
    'total_reviews' => 0, 'avg_rating' => null,
    'star_5' => 0, 'star_4' => 0, 'star_3' => 0, 'star_2' => 0, 'star_1' => 0
];

$totalReviews = (int)$stats['total_reviews'];
$avgRating = $stats['avg_rating'] !== null ? round((float)$stats['avg_rating'], 1) : null;

// 2. Fetch all feedback for this officer
$feedbackStmt = db()->prepare('
    SELECT f.*, c.reference_no, c.status as complaint_status, cc.category_name, d.department_name
    FROM feedback f
    JOIN complaints c ON c.id = f.complaint_id
    JOIN complaint_categories cc ON cc.id = c.category_id
    JOIN departments d ON d.id = c.department_id
    WHERE c.assigned_officer_id = ?
    ORDER BY f.created_at DESC
');
$feedbackStmt->execute([$officerId]);
$feedbacks = $feedbackStmt->fetchAll();

include __DIR__ . '/../includes/employee-header.php';
?>

<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Citizen Feedback &amp; Ratings</h1>
        <p class="text-muted mb-0">Ratings and reviews submitted by citizens for complaints handled by <?php echo e($officer['officer_name']); ?></p>
    </div>
    <div>
        <a href="<?php echo e(url('employee/officer-dashboard.php')); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Dashboard
        </a>
    </div>
</div>

<!-- Rating Overview Card -->
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card content-card border-0 shadow-sm text-center p-4 h-100">
            <h5 class="fw-bold text-muted mb-3">Overall Performance Rating</h5>
            <div class="display-3 fw-bold text-warning mb-2">
                <?php echo $avgRating !== null ? $avgRating : '0.0'; ?>
            </div>
            <div class="fs-4 mb-2">
                <?php
                    $fullStars = $avgRating !== null ? (int)floor($avgRating) : 0;
                    $halfStar = ($avgRating !== null && ($avgRating - $fullStars) >= 0.5);
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $fullStars) {
                            echo '<i class="bi bi-star-fill text-warning me-1"></i>';
                        } elseif ($halfStar && $i === $fullStars + 1) {
                            echo '<i class="bi bi-star-half text-warning me-1"></i>';
                        } else {
                            echo '<i class="bi bi-star text-muted me-1"></i>';
                        }
                    }
                ?>
            </div>
            <div class="text-muted small">Based on <strong><?php echo $totalReviews; ?></strong> citizen ratings</div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card content-card border-0 shadow-sm p-4 h-100">
            <h5 class="fw-bold mb-3">Rating Breakdown</h5>
            <?php
                $ratingsBreakdown = [
                    5 => (int)$stats['star_5'],
                    4 => (int)$stats['star_4'],
                    3 => (int)$stats['star_3'],
                    2 => (int)$stats['star_2'],
                    1 => (int)$stats['star_1'],
                ];
                foreach ($ratingsBreakdown as $star => $count):
                    $pct = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
            ?>
                <div class="d-flex align-items-center gap-3 mb-2">
                    <span class="fw-semibold text-nowrap" style="width: 60px;">
                        <?php echo $star; ?> <i class="bi bi-star-fill text-warning small"></i>
                    </span>
                    <div class="progress flex-grow-1" style="height: 8px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $pct; ?>%"></div>
                    </div>
                    <span class="text-muted small text-nowrap" style="width: 80px; text-align: right;">
                        <?php echo $count; ?> (<?php echo $pct; ?>%)
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Feedback Reviews List -->
<div class="card content-card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h5 class="fw-bold mb-0">Citizen Reviews (<?php echo count($feedbacks); ?>)</h5>
    </div>

    <?php if (empty($feedbacks)): ?>
        <div class="card-body text-center py-5">
            <div class="text-muted mb-2"><i class="bi bi-chat-heart fs-1"></i></div>
            <h5 class="fw-bold">No Citizen Feedback Yet</h5>
            <p class="text-muted mb-0">Citizens will be invited to rate and comment when their complaints are resolved.</p>
        </div>
    <?php else: ?>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                <?php foreach ($feedbacks as $fb): ?>
                    <div class="list-group-item p-3 p-md-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-2">
                            <div>
                                <span class="fw-bold fs-6 text-dark"><?php echo e($fb['name']); ?></span>
                                <small class="text-muted ms-2">&bull; <?php echo e($fb['mobile_no'] ? mask_mobile($fb['mobile_no']) : '-'); ?></small>
                                <span class="badge bg-light text-primary border ms-2">
                                    <a href="<?php echo e(url('employee/officer-complaint.php?id=' . $fb['complaint_id'])); ?>" class="text-decoration-none">
                                        <?php echo e($fb['reference_no']); ?>
                                    </a>
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="bi <?php echo $i <= (int)$fb['rating'] ? 'bi-star-fill text-warning' : 'bi-star text-muted'; ?>"></i>
                                    <?php endfor; ?>
                                    <strong class="ms-1"><?php echo (int)$fb['rating']; ?> / 5</strong>
                                </div>
                                <small class="text-muted"><?php echo date('d-M-Y H:i', strtotime($fb['created_at'])); ?></small>
                            </div>
                        </div>

                        <div class="text-dark bg-light p-3 rounded mt-2 border">
                            <?php echo nl2br(e($fb['feedback_text'])); ?>
                        </div>

                        <div class="text-muted small mt-2">
                            <i class="bi bi-tag me-1"></i> Category: <strong><?php echo e($fb['category_name']); ?></strong> &bull; Department: <?php echo e($fb['department_name']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/employee-footer.php'; ?>
