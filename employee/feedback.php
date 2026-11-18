<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

$user = require_login();
$pageTitle = 'Citizen Feedback';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $feedbackId = (int)($_POST['feedback_id'] ?? 0);

    if ($feedbackId > 0) {
        db()->prepare('UPDATE feedback SET is_read = 1 WHERE id = ?')->execute([$feedbackId]);
        flash('success', 'Feedback marked as read.');
    }

    redirect('employee/feedback.php');
}

$rows = db()->query(
    'SELECT * FROM feedback ORDER BY is_read ASC, created_at DESC LIMIT 1000'
)->fetchAll();

include __DIR__ . '/../includes/employee-header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div><h1 class="h3 fw-bold mb-1">Citizen Feedback</h1><p class="text-muted mb-0"><?php echo count($rows); ?> record(s)</p></div>
</div>

<div class="card content-card p-4">
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-dark"><tr><th>Date</th><th>Name</th><th>Mobile</th><th>Email</th><th>Feedback</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
                <?php if (!$rows): ?><tr><td colspan="7" class="text-center text-muted py-4">No feedback found.</td></tr><?php endif; ?>
                <?php foreach ($rows as $row): ?>
                    <tr class="<?php echo (int)$row['is_read'] ? '' : 'table-warning'; ?>">
                        <td><?php echo e(date('d-m-Y h:i A', strtotime($row['created_at']))); ?></td>
                        <td><?php echo e($row['name']); ?></td>
                        <td><?php echo e($row['mobile_no']); ?></td>
                        <td><?php echo e($row['email'] ?: '-'); ?></td>
                        <td style="min-width:300px"><?php echo nl2br(e($row['feedback_text'])); ?></td>
                        <td><?php echo (int)$row['is_read'] ? 'Read' : 'Unread'; ?></td>
                        <td>
                            <?php if (!(int)$row['is_read']): ?>
                                <form method="post"><?php echo csrf_field(); ?><input type="hidden" name="feedback_id" value="<?php echo (int)$row['id']; ?>"><button class="btn btn-sm btn-outline-success">Mark Read</button></form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../includes/employee-footer.php'; ?>
