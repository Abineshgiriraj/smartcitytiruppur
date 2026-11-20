<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

$user = require_login();
$pageTitle = 'Complaint Management';
$views = [
    'repeated' => ['Potential Repeated Complaints', 'bi-files', 'Same mobile, category and ward within the selected dates. Review before classifying as a repeat.'],
    'completed' => ['Resolved & Closed Complaints', 'bi-check2-circle', 'Find complaints that reached Resolved or Closed status.'],
    'category' => ['Category Dashboard', 'bi-grid-3x3-gap', 'Complaint volume, pending work and SLA performance by department and category.'],
    'zone' => ['Zone Dashboard', 'bi-building', 'Compare registered grievances and completion rates by zone.'],
    'map' => ['Map / Area Coverage', 'bi-geo-alt', 'Ward-level location breakdown. Exact map pins require verified complaint coordinates.'],
];
$view = (string)($_GET['view'] ?? 'category');
if (!isset($views[$view])) $view = 'category';
[$heading, $icon, $description] = $views[$view];
$pageTitle = $heading;

$fromDate = (string)($_GET['from_date'] ?? date('Y-m-01'));
$toDate = (string)($_GET['to_date'] ?? date('Y-m-d'));
if (!valid_date($fromDate) || !valid_date($toDate) || $fromDate > $toDate) {
    http_response_code(400);
    exit('Invalid date range. Please select a valid from/to date.');
}
$departmentId = max(0, (int)($_GET['department_id'] ?? 0));
$zoneId = max(0, (int)($_GET['zone_id'] ?? 0));
$completedStatus = (string)($_GET['completed_status'] ?? 'Closed');
if (!in_array($completedStatus, ['Closed', 'Resolved', 'Both'], true)) $completedStatus = 'Closed';

$conditions = [
    'c.is_deleted = 0',
    'c.created_at >= ?',
    'c.created_at < DATE_ADD(?, INTERVAL 1 DAY)'
];
$params = [$fromDate, $toDate];
if ($departmentId > 0) {
    $conditions[] = 'c.department_id = ?';
    $params[] = $departmentId;
}
if ($zoneId > 0) {
    $conditions[] = 'c.zone_id = ?';
    $params[] = $zoneId;
}

// Scope the query to the same cases each employee can see in the inbox.
// An unrecognized role never receives an unrestricted global view.
if (!can_manage_all_complaints($user)) {
    switch ($user['role']) {
        case 'Department Head':
            $conditions[] = 'c.department_id = ?';
            $params[] = (int)($user['department_id'] ?? 0);
            break;
        case 'Zone Officer':
            $conditions[] = 'c.zone_id = ?';
            $params[] = (int)($user['zone_id'] ?? 0);
            break;
        case 'Ward Officer':
            $conditions[] = 'c.ward_id = ?';
            $params[] = (int)($user['ward_id'] ?? 0);
            break;
        case 'Field Officer':
            $officerStmt = db()->prepare('SELECT id FROM officers WHERE user_id = ? LIMIT 1');
            $officerStmt->execute([(int)$user['id']]);
            $conditions[] = 'c.assigned_officer_id = ?';
            $params[] = (int)$officerStmt->fetchColumn();
            break;
        case 'Call Centre':
        case 'Report User':
            // Both roles can read the all-complaints view in the existing portal.
            break;
        default:
            $conditions[] = '1 = 0';
    }
}
if ($view === 'completed' && $completedStatus !== 'Both') {
    $conditions[] = 'c.status = ?';
    $params[] = $completedStatus;
} elseif ($view === 'completed') {
    $conditions[] = "c.status IN ('Resolved','Closed')";
}
$where = implode(' AND ', $conditions);
$baseJoin = "FROM complaints c
JOIN departments d ON d.id=c.department_id
JOIN complaint_categories cc ON cc.id=c.category_id
JOIN zones z ON z.id=c.zone_id
JOIN wards w ON w.id=c.ward_id";

if ($view === 'repeated') {
    // Candidate matching only: identical phone/category/ward, not proof of duplicate.
    $sql = "SELECT c.mobile_no, cc.category_name, d.department_name, z.zone_no, w.ward_no,
                   COUNT(*) AS occurrences, MIN(c.created_at) AS first_seen,
                   MAX(c.created_at) AS last_seen, MAX(c.id) AS latest_id
            $baseJoin
            WHERE $where AND c.mobile_no <> ''
            GROUP BY c.mobile_no, c.category_id, c.ward_id,
                     cc.category_name, d.department_name, z.zone_no, w.ward_no
            HAVING COUNT(*) > 1
            ORDER BY last_seen DESC
            LIMIT 200";
} elseif ($view === 'completed') {
    $sql = "SELECT c.id, c.reference_no, c.citizen_name, c.status, c.created_at,
                   c.resolved_at, c.closed_at, d.department_name, cc.category_name,
                   z.zone_no, w.ward_no, o.officer_name
            $baseJoin
            LEFT JOIN officers o ON o.id=c.assigned_officer_id
            WHERE $where
            ORDER BY COALESCE(c.closed_at,c.resolved_at,c.updated_at) DESC, c.id DESC
            LIMIT 200";
} else {
    [$label, $group] = match ($view) {
        'category' => ["CONCAT(d.department_name, ' / ', cc.category_name)", "d.id,d.department_name,cc.id,cc.category_name"],
        'zone' => ["CONCAT('Zone ', z.zone_no)", "z.id,z.zone_no"],
        default => ["CONCAT('Zone ', z.zone_no, ' / Ward ', w.ward_no)", "z.id,z.zone_no,w.id,w.ward_no"],
    };
    $sql = "SELECT $label AS group_name, COUNT(*) AS received,
                   COALESCE(SUM(c.status IN ('Resolved','Closed')),0) AS resolved,
                   COALESCE(SUM(c.status NOT IN ('Resolved','Closed','Rejected')),0) AS pending,
                   COALESCE(SUM(c.status NOT IN ('Resolved','Closed','Rejected') AND c.due_at < NOW()),0) AS overdue,
                   COALESCE(SUM(c.reopen_count > 0),0) AS reopened
            $baseJoin
            WHERE $where
            GROUP BY $group
            ORDER BY received DESC, group_name
            LIMIT 250";
}
$stmt = db()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (($_GET['export'] ?? '') === 'csv') {
    $csvHeaders = match ($view) {
        'repeated' => ['Mobile (masked)', 'Department', 'Category', 'Zone', 'Ward', 'Occurrences', 'First Seen', 'Last Seen'],
        'completed' => ['Reference', 'Citizen', 'Department', 'Category', 'Zone', 'Ward', 'Status', 'Registered', 'Resolved At', 'Closed At', 'Current Officer'],
        default => ['Area', 'Received', 'Resolved', 'Pending', 'Overdue', 'Reopened'],
    };
    header('Content-Type: text/csv; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    header('Content-Disposition: attachment; filename="smartcity_complaints_' . $view . '_' . date('Ymd') . '.csv"');
    $output = fopen('php://output', 'wb');
    if ($output === false) exit;
    fwrite($output, "\xEF\xBB\xBF");
    fputcsv($output, $csvHeaders);
    foreach ($rows as $item) {
        $values = match ($view) {
            'repeated' => [
                mask_mobile((string)$item['mobile_no']), $item['department_name'], $item['category_name'],
                $item['zone_no'], $item['ward_no'], $item['occurrences'], $item['first_seen'], $item['last_seen']
            ],
            'completed' => [
                $item['reference_no'], $item['citizen_name'], $item['department_name'], $item['category_name'],
                $item['zone_no'], $item['ward_no'], $item['status'], $item['created_at'], $item['resolved_at'],
                $item['closed_at'], $item['officer_name']
            ],
            default => [
                $item['group_name'], $item['received'], $item['resolved'], $item['pending'],
                $item['overdue'], $item['reopened']
            ],
        };
        $safe = array_map(static function ($value): string {
            $value = (string)($value ?? '');
            return preg_match('/^\s*[=+\-@]/u', $value) ? "'" . $value : $value;
        }, $values);
        fputcsv($output, $safe);
    }
    fclose($output);
    exit;
}

$departments = db()->query('SELECT id, department_name FROM departments WHERE is_active=1 ORDER BY department_name')->fetchAll(PDO::FETCH_ASSOC);
$zones = db()->query('SELECT id, zone_no FROM zones WHERE is_active=1 ORDER BY zone_no')->fetchAll(PDO::FETCH_ASSOC);
$exportParams = $_GET;
$exportParams['view'] = $view;
$exportParams['from_date'] = $fromDate;
$exportParams['to_date'] = $toDate;
$exportParams['export'] = 'csv';
$totalCases = in_array($view, ['category','zone','map'], true)
    ? array_sum(array_map(static fn(array $row): int => (int)$row['received'], $rows))
    : count($rows);
include __DIR__ . '/../includes/employee-header.php';
?>
<div class="admin-heading">
  <div>
    <span class="admin-eyebrow">Complaint Management</span>
    <h1 class="fw-bold mb-1"><i class="bi <?php echo e($icon); ?> me-2 text-primary"></i><?php echo e($heading); ?></h1>
    <p><?php echo e($description); ?></p>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    <a href="<?php echo e(url('employee/complaints.php')); ?>" class="btn btn-outline-primary"><i class="bi bi-inboxes me-1"></i> Complaint Inbox</a>
    <a href="<?php echo e(url('employee/icts-reports.php')); ?>" class="btn btn-outline-secondary">ICTS Reports</a>
  </div>
</div>
<div class="row g-2 mb-3">
  <?php foreach ($views as $key => $item): ?>
    <?php $tab = $_GET; $tab['view'] = $key; unset($tab['export'], $tab['completed_status']); ?>
    <div class="col-auto">
      <a class="btn btn-sm <?php echo $view === $key ? 'btn-primary' : 'btn-outline-secondary'; ?>"
         href="<?php echo e(url('employee/complaint-centre.php?' . http_build_query($tab))); ?>"><?php echo e($item[0]); ?></a>
    </div>
  <?php endforeach; ?>
</div>
<div class="card content-card p-4 mb-3 no-print">
  <form method="get" class="row g-3 align-items-end">
    <input type="hidden" name="view" value="<?php echo e($view); ?>">
    <div class="col-sm-6 col-xl-2"><label class="form-label small">From date</label><input class="form-control" type="date" name="from_date" value="<?php echo e($fromDate); ?>" required></div>
    <div class="col-sm-6 col-xl-2"><label class="form-label small">To date</label><input class="form-control" type="date" name="to_date" value="<?php echo e($toDate); ?>" required></div>
    <div class="col-sm-6 col-xl-3">
      <label class="form-label small">Department</label>
      <select class="form-select" name="department_id"><option value="0">All departments</option>
        <?php foreach ($departments as $item): ?><option value="<?php echo (int)$item['id']; ?>"<?php echo $departmentId === (int)$item['id'] ? ' selected' : ''; ?>><?php echo e($item['department_name']); ?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="col-sm-6 col-xl-2">
      <label class="form-label small">Zone</label>
      <select class="form-select" name="zone_id"><option value="0">All zones</option>
        <?php foreach ($zones as $item): ?><option value="<?php echo (int)$item['id']; ?>"<?php echo $zoneId === (int)$item['id'] ? ' selected' : ''; ?>><?php echo e($item['zone_no']); ?></option><?php endforeach; ?>
      </select>
    </div>
    <?php if ($view === 'completed'): ?>
      <div class="col-sm-6 col-xl-2">
        <label class="form-label small">Status</label>
        <select class="form-select" name="completed_status">
          <?php foreach (['Closed','Resolved','Both'] as $value): ?><option value="<?php echo e($value); ?>"<?php echo $completedStatus === $value ? ' selected' : ''; ?>><?php echo e($value); ?></option><?php endforeach; ?>
        </select>
      </div>
    <?php endif; ?>
    <div class="col-auto"><button type="submit" class="btn btn-primary"><i class="bi bi-funnel me-1"></i> Apply</button></div>
    <div class="col-auto"><a class="btn btn-outline-secondary" href="<?php echo e(url('employee/complaint-centre.php?view=' . $view)); ?>">Reset</a></div>
  </form>
</div>
<?php if ($view === 'repeated'): ?>
  <div class="alert alert-warning border-0 small">
    <i class="bi bi-exclamation-triangle me-2"></i>
    <strong>Review candidates only.</strong> These groups have two or more complaints with the same mobile number, category and ward in the selected period.
    They are <strong>not</strong> automatically flagged, merged, deleted or confirmed as repeat complaints. The old ICTS repeat definition has not been verified.
  </div>
<?php elseif ($view === 'map'): ?>
  <div class="alert alert-info border-0 small">
    <i class="bi bi-geo-alt me-2"></i>
    <strong>Area coverage, not a point map.</strong> The current Smart City complaint schema does not store verified latitude/longitude.
    These zone/ward totals are accurate to stored ward assignments, but individual map pins cannot be plotted reliably yet.
  </div>
<?php endif; ?>
<div class="card content-card p-4">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
    <div>
      <h2 class="admin-panel-heading"><?php echo e($heading); ?></h2>
      <div class="admin-panel-subtitle"><?php echo e($fromDate); ?> to <?php echo e($toDate); ?> · <?php echo count($rows); ?> rows shown<?php echo in_array($view, ['category','zone','map'], true) ? ' · ' . number_format($totalCases) . ' complaints' : ''; ?></div>
    </div>
    <div class="d-flex gap-2 no-print">
      <a href="<?php echo e(url('employee/complaint-centre.php?' . http_build_query($exportParams))); ?>" class="btn btn-outline-success btn-sm"><i class="bi bi-filetype-csv me-1"></i> Export CSV</a>
      <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i> Print</button>
    </div>
  </div>
  <div class="table-responsive">
    <?php if ($view === 'repeated'): ?>
      <table class="table table-hover align-middle mb-0">
        <thead><tr><th>Mobile</th><th>Department / Category</th><th>Zone / Ward</th><th>Complaints</th><th>First Seen</th><th>Last Seen</th><th>Review</th></tr></thead>
        <tbody>
          <?php if (!$rows): ?><tr><td colspan="7" class="admin-empty">No potential repeat groups match the selected dates.</td></tr><?php endif; ?>
          <?php foreach ($rows as $row): ?>
          <tr>
            <td><?php echo e(mask_mobile((string)$row['mobile_no'])); ?></td>
            <td><strong><?php echo e($row['department_name']); ?></strong><div class="text-muted small"><?php echo e($row['category_name']); ?></div></td>
            <td><?php echo e('Zone ' . $row['zone_no'] . ' / Ward ' . $row['ward_no']); ?></td>
            <td><span class="badge text-bg-warning"><?php echo (int)$row['occurrences']; ?></span></td>
            <td><?php echo e($row['first_seen']); ?></td><td><?php echo e($row['last_seen']); ?></td>
            <td><a class="btn btn-sm btn-outline-primary" href="<?php echo e(url('employee/complaint-view.php?id=' . (int)$row['latest_id'])); ?>">Latest Complaint</a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php elseif ($view === 'completed'): ?>
      <table class="table table-hover align-middle mb-0">
        <thead><tr><th>Reference</th><th>Citizen</th><th>Department / Category</th><th>Zone / Ward</th><th>Status</th><th>Resolved At</th><th>Closed At</th><th>Action</th></tr></thead>
        <tbody>
          <?php if (!$rows): ?><tr><td colspan="8" class="admin-empty">No completed complaints match the selected dates.</td></tr><?php endif; ?>
          <?php foreach ($rows as $row): ?>
          <tr>
            <td class="fw-semibold"><?php echo e($row['reference_no']); ?></td>
            <td><?php echo e($row['citizen_name']); ?></td>
            <td><?php echo e($row['department_name']); ?><div class="text-muted small"><?php echo e($row['category_name']); ?></div></td>
            <td><?php echo e('Zone ' . $row['zone_no'] . ' / Ward ' . $row['ward_no']); ?></td>
            <td><span class="badge text-bg-success"><?php echo e($row['status']); ?></span></td>
            <td><?php echo e($row['resolved_at'] ?? '-'); ?></td><td><?php echo e($row['closed_at'] ?? '-'); ?></td>
            <td><a class="btn btn-sm btn-outline-primary" href="<?php echo e(url('employee/complaint-view.php?id=' . (int)$row['id'])); ?>">View</a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <?php $maxReceived = $rows ? max(array_map(static fn(array $row): int => (int)$row['received'], $rows)) : 0; ?>
      <table class="table table-hover align-middle mb-0">
        <thead><tr><th><?php echo $view === 'category' ? 'Department / Category' : ($view === 'zone' ? 'Zone' : 'Zone / Ward'); ?></th><th>Received</th><th>Resolved</th><th>Pending</th><th>SLA Overdue</th><th>Reopened</th><th>Resolution Rate</th></tr></thead>
        <tbody>
          <?php if (!$rows): ?><tr><td colspan="7" class="admin-empty">No complaints match the selected dates.</td></tr><?php endif; ?>
          <?php foreach ($rows as $row): ?>
          <?php $rate = (int)$row['received'] > 0 ? round((int)$row['resolved'] * 100 / (int)$row['received']) : 0; ?>
          <tr>
            <td style="min-width:220px">
              <strong><?php echo e($row['group_name']); ?></strong>
              <div class="admin-progress-track mt-2"><div class="admin-progress-fill" style="width:<?php echo $maxReceived > 0 ? (int)round((int)$row['received'] * 100 / $maxReceived) : 0; ?>%"></div></div>
            </td>
            <td class="fw-bold"><?php echo (int)$row['received']; ?></td>
            <td><?php echo (int)$row['resolved']; ?></td>
            <td><?php echo (int)$row['pending']; ?></td>
            <td class="<?php echo (int)$row['overdue'] > 0 ? 'text-danger fw-semibold' : ''; ?>"><?php echo (int)$row['overdue']; ?></td>
            <td><?php echo (int)$row['reopened']; ?></td>
            <td><?php echo $rate; ?>%</td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
  <p class="text-muted small mt-3 mb-0">
    Date filters apply to complaint registration dates. Status and SLA metrics reflect the complaint's current state.
    Results are capped at <?php echo $view === 'repeated' || $view === 'completed' ? 200 : 250; ?> rows/groups.
    No changes are made to complaint records when generating these views.
  </p>
</div>
<?php include __DIR__ . '/../includes/employee-footer.php'; ?>
