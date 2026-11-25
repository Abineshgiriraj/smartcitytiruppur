<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

$user = require_permission('complaints.view');
$pageTitle = 'Complaints';

$fromDate = trim((string)($_GET['from_date'] ?? ''));
$toDate = trim((string)($_GET['to_date'] ?? ''));
// Default to all dates. Restrict only when user explicitly enters a range.
if (($fromDate !== '' && !valid_date($fromDate))
    || ($toDate !== '' && !valid_date($toDate))
    || ($fromDate !== '' && $toDate !== '' && $fromDate > $toDate)) {
    http_response_code(400);
    exit('Invalid date range.');
}
$status = trim($_GET['status'] ?? '');
$departmentId = (int)($_GET['department_id'] ?? 0);
$zoneId = (int)($_GET['zone_id'] ?? 0);
$wardId = (int)($_GET['ward_id'] ?? 0);
$search = trim($_GET['q'] ?? '');

$where = ['c.is_deleted = 0'];
$params = [];

if ($fromDate !== '') {
    $where[] = 'c.created_at >= ?';
    $params[] = $fromDate;
}
if ($toDate !== '') {
    $where[] = 'c.created_at < DATE_ADD(?, INTERVAL 1 DAY)';
    $params[] = $toDate;
}
if ($status !== '') {
    $where[] = 'c.status = ?';
    $params[] = $status;
}
if ($departmentId > 0) {
    $where[] = 'c.department_id = ?';
    $params[] = $departmentId;
}
if ($zoneId > 0) {
    $where[] = 'c.zone_id = ?';
    $params[] = $zoneId;
}
if ($wardId > 0) {
    $where[] = 'c.ward_id = ?';
    $params[] = $wardId;
}
if ($search !== '') {
    $where[] = '(c.reference_no LIKE ? OR c.citizen_name LIKE ? OR c.mobile_no LIKE ? OR c.description_english LIKE ?)';
    $like = '%' . $search . '%';
    array_push($params, $like, $like, $like, $like);
}

if (!can_manage_all_complaints($user)) {
    if ($user['role'] === 'Department Head') {
        $where[] = 'c.department_id = ?';
        $params[] = (int)$user['department_id'];
    } elseif ($user['role'] === 'Zone Officer') {
        $where[] = 'c.zone_id = ?';
        $params[] = (int)$user['zone_id'];
    } elseif ($user['role'] === 'Ward Officer') {
        $where[] = 'c.ward_id = ?';
        $params[] = (int)$user['ward_id'];
    } elseif ($user['role'] === 'Field Officer') {
        $officerStmt = db()->prepare('SELECT id FROM officers WHERE user_id = ? LIMIT 1');
        $officerStmt->execute([(int)$user['id']]);
        $where[] = 'c.assigned_officer_id = ?';
        $params[] = (int)$officerStmt->fetchColumn();
    } elseif ($user['role'] !== 'Call Centre' && $user['role'] !== 'Report User') {
        $where[] = '1=0'; // Fail closed for unknown roles.
    }
}

$condition = implode(' AND ', $where);
$countStmt = db()->prepare("SELECT COUNT(*) FROM complaints c WHERE $condition");
$countStmt->execute($params);
$matchedTotal = (int)$countStmt->fetchColumn();
$pageSize = 50;
$pageCount = max(1, (int)ceil($matchedTotal / $pageSize));
$requestedPage = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT);
$page = min($pageCount, max(1, $requestedPage === false ? 1 : (int)$requestedPage));
$offset = ($page - 1) * $pageSize;
$paginationQuery = $_GET;
unset($paginationQuery['page']);
$pageUrl = static function (int $number) use ($paginationQuery): string {
    return url('employee/complaints.php') . '?' . http_build_query(array_merge($paginationQuery, ['page' => $number]));
};
$sql = "SELECT c.id,c.reference_no,c.citizen_name,c.mobile_no,c.status,c.created_at,c.due_at,
               COALESCE(d.department_name,'Unmapped') AS department_name,
               COALESCE(cc.category_name,'Unmapped') AS category_name,
               z.zone_no,w.ward_no,o.officer_name,
               (SELECT previous.officer_name FROM complaint_assignments former
                JOIN officers previous ON previous.id=former.officer_id
                WHERE former.complaint_id=c.id AND former.is_current=0
                ORDER BY former.unassigned_at DESC,former.id DESC LIMIT 1) previous_officer_name,
               (SELECT MAX(ce.level_no) FROM complaint_escalations ce
                WHERE ce.complaint_id=c.id AND ce.officer_id=c.assigned_officer_id) current_transfer_level,
               (SELECT MAX(ce.escalated_at) FROM complaint_escalations ce
                WHERE ce.complaint_id=c.id AND ce.officer_id=c.assigned_officer_id) last_escalated_at
        FROM complaints c
        LEFT JOIN departments d ON d.id=c.department_id
        LEFT JOIN complaint_categories cc ON cc.id=c.category_id
        LEFT JOIN zones z ON z.id=c.zone_id
        LEFT JOIN wards w ON w.id=c.ward_id
        LEFT JOIN officers o ON o.id=c.assigned_officer_id
        WHERE $condition
        ORDER BY c.created_at DESC,c.id DESC
        LIMIT $pageSize OFFSET $offset";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$departments = db()->query('SELECT id, department_name FROM departments WHERE is_active = 1 ORDER BY department_name')->fetchAll();
$zones = db()->query('SELECT id, zone_no FROM zones WHERE is_active = 1 ORDER BY zone_no')->fetchAll();
$wards = db()->query('SELECT id, ward_no FROM wards WHERE is_active = 1 ORDER BY ward_no')->fetchAll();

include __DIR__ . '/../includes/employee-header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div><h1 class="h3 fw-bold mb-1">Complaint Inbox</h1>
    <p class="text-muted mb-0"><?php echo number_format($matchedTotal); ?> matching non-deleted complaint(s) across <?php echo $fromDate !== '' || $toDate !== '' ? 'selected dates' : 'all dates'; ?> · showing <?php echo count($rows); ?> on page <?php echo $page; ?> of <?php echo $pageCount; ?>.</p></div>
</div>

<div class="card content-card p-4 mb-4 no-print">
    <form class="row g-3 align-items-end" method="get">
        <div class="col-md-2"><label class="form-label">From Date</label><input class="form-control" type="date" name="from_date" value="<?php echo e($fromDate); ?>"></div>
        <div class="col-md-2"><label class="form-label">To Date</label><input class="form-control" type="date" name="to_date" value="<?php echo e($toDate); ?>"></div>
        <div class="col-md-2"><label class="form-label">Status</label><select class="form-select" name="status"><option value="">All</option><?php foreach (complaint_statuses() as $item): ?><option <?php echo $status === $item ? 'selected' : ''; ?>><?php echo e($item); ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><label class="form-label">Department</label><select class="form-select" name="department_id"><option value="">All</option><?php foreach ($departments as $item): ?><option value="<?php echo (int)$item['id']; ?>" <?php echo $departmentId === (int)$item['id'] ? 'selected' : ''; ?>><?php echo e($item['department_name']); ?></option><?php endforeach; ?></select></div>
        <div class="col-md-1"><label class="form-label">Zone</label><select class="form-select" name="zone_id"><option value="">All</option><?php foreach ($zones as $item): ?><option value="<?php echo (int)$item['id']; ?>" <?php echo $zoneId === (int)$item['id'] ? 'selected' : ''; ?>><?php echo e($item['zone_no']); ?></option><?php endforeach; ?></select></div>
        <div class="col-md-1"><label class="form-label">Ward</label><select class="form-select" name="ward_id"><option value="">All</option><?php foreach ($wards as $item): ?><option value="<?php echo (int)$item['id']; ?>" <?php echo $wardId === (int)$item['id'] ? 'selected' : ''; ?>><?php echo e($item['ward_no']); ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><label class="form-label">Search</label><input class="form-control" name="q" value="<?php echo e($search); ?>" placeholder="Reference, name, mobile"></div>
        <div class="col-12 d-flex gap-2"><button class="btn btn-primary">Apply Filters</button><a class="btn btn-outline-secondary" href="<?php echo e(url('employee/complaints.php')); ?>">Reset</a></div>
    </form>
</div>

<div class="card content-card p-4">
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-dark"><tr><th>Reference</th><th>Date</th><th>Citizen</th><th>Mobile</th><th>Department</th><th>Category</th><th>Zone</th><th>Ward</th><th>Current officer / Handover</th><th>Status</th><th>Due</th><th>Action</th></tr></thead>
            <tbody>
                <?php if (!$rows): ?><tr><td colspan="12" class="text-center text-muted py-4">No complaints found.</td></tr><?php endif; ?>
                <?php foreach ($rows as $row): ?>
                    <?php $overdue = $row['due_at'] && strtotime($row['due_at']) < time() && !in_array($row['status'], ['Resolved','Closed','Rejected'], true); ?>
                    <tr class="<?php echo $overdue ? 'table-danger' : ''; ?>">
                        <td><?php echo e($row['reference_no']); ?></td>
                        <td><?php echo e(date('d-m-Y', strtotime($row['created_at']))); ?></td>
                        <td><?php echo e($row['citizen_name']); ?></td>
                        <td><?php echo e($row['mobile_no']); ?></td>
                        <td><?php echo e($row['department_name']); ?></td>
                        <td><?php echo e($row['category_name']); ?></td>
                        <td><?php echo e($row['zone_no']); ?></td>
                        <td><?php echo e($row['ward_no']); ?></td>
                        <td>
                          <strong><?php echo e($row['officer_name'] ?: 'Unassigned'); ?></strong>
                          <?php if((int)($row['current_transfer_level']??0)>1): ?>
                            <div><span class="badge bg-info text-dark">Escalated · Stage <?php echo (int)$row['current_transfer_level']; ?></span></div>
                          <?php endif; ?>
                          <?php if(!empty($row['previous_officer_name'])): ?>
                            <small class="d-block text-muted">From: <?php echo e($row['previous_officer_name']); ?></small>
                          <?php endif; ?>
                          <?php if(!empty($row['last_escalated_at'])): ?>
                            <small class="d-block text-muted">Transferred: <?php echo e(date('d M Y, h:i A',strtotime($row['last_escalated_at']))); ?></small>
                          <?php endif; ?>
                        </td>
                        <td><span class="badge text-bg-<?php echo e(status_badge_class($row['status'])); ?>"><?php echo e($row['status']); ?></span></td>
                        <td><?php echo e($row['due_at'] ? date('d-m-Y', strtotime($row['due_at'])) : '-'); ?></td>
                        <td><a class="btn btn-sm btn-outline-primary" href="<?php echo e(url('employee/complaint-view.php?id=' . (int)$row['id'])); ?>">Open</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if ($pageCount > 1): ?>
      <nav class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3" aria-label="Complaint pagination">
        <span class="small text-muted">Page <?php echo $page; ?> of <?php echo $pageCount; ?> (<?php echo number_format($matchedTotal); ?> total matching)</span>
        <div class="d-flex gap-2">
          <?php if ($page > 1): ?><a class="btn btn-outline-primary btn-sm" href="<?php echo e($pageUrl($page - 1)); ?>">Previous</a><?php endif; ?>
          <?php if ($page < $pageCount): ?><a class="btn btn-outline-primary btn-sm" href="<?php echo e($pageUrl($page + 1)); ?>">Next</a><?php endif; ?>
        </div>
      </nav>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../includes/employee-footer.php'; ?>
