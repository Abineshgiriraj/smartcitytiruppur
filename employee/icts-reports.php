<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

$user = require_login();
$pageTitle = 'ICTS Reports';
$reportOptions = [
    'department' => 'Department Wise Report',
    'department-category' => 'Department Category Wise Report',
    'zone-source' => 'Zone and Complaint Source Wise Report',
    'zone-category' => 'Zonal Wise Category Report',
    'periodical' => 'Periodical Report',
    'ward' => 'Ward Wise Report',
    'employee-grievance' => 'Employee Wise Grievance Report',
    'rating' => 'Rating Abstract Report',
    'department-performance' => 'Department Performance Report',
    'employee-escalation' => 'Employee Wise Escalation Report',
    'employee-performance' => 'Employee Performance Report',
];
$type = (string)($_GET['type'] ?? 'department');
if (!isset($reportOptions[$type])) $type = 'department';

$fromDate = (string)($_GET['from_date'] ?? date('Y-m-01'));
$toDate = (string)($_GET['to_date'] ?? date('Y-m-d'));
if (!valid_date($fromDate)) $fromDate = date('Y-m-01');
if (!valid_date($toDate)) $toDate = date('Y-m-d');
if ($fromDate > $toDate) {
    http_response_code(400);
    exit('From date must be on or before to date.');
}
$departmentId = max(0, (int)($_GET['department_id'] ?? 0));
$zoneId = max(0, (int)($_GET['zone_id'] ?? 0));
$export = (string)($_GET['export'] ?? '');

$where = ['c.is_deleted = 0'];
$params = [];
$dateColumn = match ($type) {
    'rating' => 'f.created_at',
    'employee-escalation' => 'ce.escalated_at',
    default => 'c.created_at',
};
$where[] = "$dateColumn >= ? AND $dateColumn < DATE_ADD(?, INTERVAL 1 DAY)";
$params[] = $fromDate;
$params[] = $toDate;
if ($departmentId) { $where[] = 'c.department_id = ?'; $params[] = $departmentId; }
if ($zoneId) { $where[] = 'c.zone_id = ?'; $params[] = $zoneId; }

// Use the same complaint visibility rules as the employee portal.
if (!can_manage_all_complaints($user)) {
    switch ($user['role']) {
        case 'Department Head':
            $where[] = 'c.department_id = ?';
            $params[] = (int)($user['department_id'] ?? 0);
            break;
        case 'Zone Officer':
            $where[] = 'c.zone_id = ?';
            $params[] = (int)($user['zone_id'] ?? 0);
            break;
        case 'Ward Officer':
            $where[] = 'c.ward_id = ?';
            $params[] = (int)($user['ward_id'] ?? 0);
            break;
        case 'Field Officer':
            $officerQuery = db()->prepare('SELECT id FROM officers WHERE user_id = ? LIMIT 1');
            $officerQuery->execute([(int)$user['id']]);
            $where[] = 'c.assigned_officer_id = ?';
            $params[] = (int)$officerQuery->fetchColumn();
            break;
        case 'Call Centre':
        case 'Report User':
            break;
        default:
            $where[] = '1 = 0';
    }
}
$whereSql = implode(' AND ', $where);

$dimensionDefinitions = [
    'department' => ['d.department_name', 'd.id, d.department_name', 'Department'],
    'department-category' => [
        "CONCAT(d.department_name, ' / ', cc.category_name)",
        'd.id, d.department_name, cc.id, cc.category_name',
        'Department / Category',
    ],
    'zone-source' => [
        "CONCAT('Zone ', z.zone_no, ' / ', COALESCE(NULLIF(c.source,''), 'Unknown'))",
        'z.id, z.zone_no, c.source',
        'Zone / Source',
    ],
    'zone-category' => [
        "CONCAT('Zone ', z.zone_no, ' / ', cc.category_name)",
        'z.id, z.zone_no, cc.id, cc.category_name',
        'Zone / Category',
    ],
    'periodical' => [
        "DATE_FORMAT(c.created_at, '%Y-%m')",
        "DATE_FORMAT(c.created_at, '%Y-%m')",
        'Month',
    ],
    'ward' => [
        "CONCAT('Zone ', z.zone_no, ' / Ward ', w.ward_no)",
        'z.id, z.zone_no, w.id, w.ward_no',
        'Ward',
    ],
    'employee-grievance' => [
        "COALESCE(o.officer_name, 'Unassigned')",
        'c.assigned_officer_id, o.officer_name',
        'Current Assigned Officer',
    ],
    'department-performance' => ['d.department_name', 'd.id, d.department_name', 'Department'],
    'employee-performance' => [
        "COALESCE(o.officer_name, 'Unassigned')",
        'c.assigned_officer_id, o.officer_name',
        'Current Assigned Officer',
    ],
];

if ($type === 'rating') {
    $sql = "SELECT CONCAT(f.rating,' star') AS label,
                   COUNT(*) AS responses
            FROM feedback f
            JOIN complaints c ON c.id = f.complaint_id
            WHERE $whereSql AND f.rating BETWEEN 1 AND 5
            GROUP BY f.rating ORDER BY f.rating DESC";
    $columns = ['label' => 'Rating', 'responses' => 'Feedback Responses'];
} elseif ($type === 'employee-escalation') {
    $sql = "SELECT COALESCE(eo.officer_name,'Unknown Officer') AS label,
                   COUNT(*) AS escalations,
                   SUM(CASE WHEN ce.level_no=2 THEN 1 ELSE 0 END) AS stage2,
                   SUM(CASE WHEN ce.level_no=3 THEN 1 ELSE 0 END) AS stage3,
                   SUM(CASE WHEN ce.level_no=4 THEN 1 ELSE 0 END) AS stage4
            FROM complaint_escalations ce
            JOIN complaints c ON c.id = ce.complaint_id
            LEFT JOIN officers eo ON eo.id = ce.officer_id
            WHERE $whereSql
            GROUP BY ce.officer_id, eo.officer_name
            ORDER BY escalations DESC, label LIMIT 500";
    $columns = [
        'label' => 'Escalated To Officer',
        'escalations' => 'Total Escalations',
        'stage2' => 'Stage 2',
        'stage3' => 'Stage 3',
        'stage4' => 'Stage 4',
    ];
} else {
    [$labelExpr, $groupExpr, $labelTitle] = $dimensionDefinitions[$type];
    $sql = "SELECT $labelExpr AS label,
                   COUNT(*) AS received,
                   SUM(CASE WHEN c.status IN ('Resolved','Closed') THEN 1 ELSE 0 END) AS resolved,
                   SUM(CASE WHEN c.status NOT IN ('Resolved','Closed','Rejected') THEN 1 ELSE 0 END) AS pending,
                   SUM(CASE WHEN c.due_at < NOW() AND c.status NOT IN ('Resolved','Closed','Rejected') THEN 1 ELSE 0 END) AS overdue,
                   SUM(CASE WHEN EXISTS(SELECT 1 FROM complaint_escalations ce2 WHERE ce2.complaint_id=c.id) THEN 1 ELSE 0 END) AS escalated,
                   SUM(CASE WHEN EXISTS(SELECT 1 FROM complaint_reopen_history rh WHERE rh.complaint_id=c.id) THEN 1 ELSE 0 END) AS reopened
            FROM complaints c
            JOIN departments d ON d.id = c.department_id
            JOIN complaint_categories cc ON cc.id = c.category_id
            JOIN zones z ON z.id = c.zone_id
            JOIN wards w ON w.id = c.ward_id
            LEFT JOIN officers o ON o.id = c.assigned_officer_id
            WHERE $whereSql
            GROUP BY $groupExpr
            ORDER BY received DESC, label
            LIMIT 500";
    $columns = [
        'label' => $labelTitle,
        'received' => 'Received',
        'resolved' => 'Resolved',
        'pending' => 'Pending',
        'overdue' => 'SLA Overdue',
        'escalated' => 'Ever Escalated',
        'reopened' => 'Reopened',
    ];
}
$statement = db()->prepare($sql);
$statement->execute($params);
$rows = $statement->fetchAll(PDO::FETCH_ASSOC);

if ($export === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="smartcity_icts_' . $type . '_' . date('Ymd') . '.csv"');
    header('X-Content-Type-Options: nosniff');
    $out = fopen('php://output', 'wb');
    if ($out === false) exit;
    fwrite($out, "\xEF\xBB\xBF");
    fputcsv($out, array_values($columns));
    foreach ($rows as $row) {
        $safeValues = [];
        foreach (array_keys($columns) as $field) {
            $value = (string)($row[$field] ?? '');
            // Prevent spreadsheet formula injection when opened in Excel.
            if (preg_match('/^[\s]*[=+\-@]/u', $value)) $value = "'" . $value;
            $safeValues[] = $value;
        }
        fputcsv($out, $safeValues);
    }
    fclose($out);
    exit;
}

$departments = db()->query('SELECT id, department_name FROM departments WHERE is_active=1 ORDER BY department_name')->fetchAll(PDO::FETCH_ASSOC);
$zones = db()->query('SELECT id, zone_no FROM zones WHERE is_active=1 ORDER BY zone_no')->fetchAll(PDO::FETCH_ASSOC);
$exportQuery = $_GET;
$exportQuery['type'] = $type;
$exportQuery['from_date'] = $fromDate;
$exportQuery['to_date'] = $toDate;
$exportQuery['export'] = 'csv';

include __DIR__ . '/../includes/employee-header.php';
?>
<div class="admin-heading">
  <div>
    <span class="admin-eyebrow">ICTS Feature Parity</span>
    <h1 class="fw-bold mb-1">Reporting Centre</h1>
    <p>Department, ward, source, employee and escalation analytics using current Smart City data.</p>
  </div>
  <a class="btn btn-outline-secondary" href="<?php echo e(url('employee/reports.php')); ?>">Detailed Complaint Export</a>
</div>
<div class="row g-3 mb-3">
  <div class="col-lg-4 col-xl-3">
    <div class="card content-card p-3">
      <div class="text-uppercase small fw-bold text-muted mb-3">Report Library</div>
      <div class="list-group list-group-flush">
        <?php foreach ($reportOptions as $key => $label): ?>
          <?php $query = $_GET; $query['type'] = $key; unset($query['export']); ?>
          <a class="list-group-item list-group-item-action small py-3<?php echo $type === $key ? ' active' : ''; ?>"
             href="<?php echo e(url('employee/icts-reports.php?' . http_build_query($query))); ?>"><?php echo e($label); ?></a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div class="col-lg-8 col-xl-9">
    <div class="card content-card p-4 mb-3">
      <h2 class="admin-panel-heading mb-3"><?php echo e($reportOptions[$type]); ?></h2>
      <form class="row g-2 align-items-end" action="<?php echo e(url('employee/icts-reports.php')); ?>" method="get">
        <input type="hidden" name="type" value="<?php echo e($type); ?>">
        <div class="col-sm-6 col-xl-3"><label class="form-label small">From date</label><input class="form-control" name="from_date" type="date" value="<?php echo e($fromDate); ?>" required></div>
        <div class="col-sm-6 col-xl-3"><label class="form-label small">To date</label><input class="form-control" name="to_date" type="date" value="<?php echo e($toDate); ?>" required></div>
        <div class="col-sm-6 col-xl-3"><label class="form-label small">Department</label>
          <select class="form-select" name="department_id">
            <option value="0">All departments</option>
            <?php foreach ($departments as $department): ?>
              <option value="<?php echo (int)$department['id']; ?>"<?php echo $departmentId === (int)$department['id'] ? ' selected' : ''; ?>><?php echo e($department['department_name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-sm-6 col-xl-3"><label class="form-label small">Zone</label>
          <select class="form-select" name="zone_id">
            <option value="0">All zones</option>
            <?php foreach ($zones as $zone): ?>
              <option value="<?php echo (int)$zone['id']; ?>"<?php echo $zoneId === (int)$zone['id'] ? ' selected' : ''; ?>><?php echo e($zone['zone_no']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12 d-flex gap-2 flex-wrap mt-3">
          <button class="btn btn-primary" type="submit"><i class="bi bi-funnel me-1"></i> Generate Report</button>
          <a href="<?php echo e(url('employee/icts-reports.php?' . http_build_query($exportQuery))); ?>" class="btn btn-outline-success"><i class="bi bi-filetype-csv me-1"></i> Export CSV</a>
          <button type="button" onclick="window.print()" class="btn btn-outline-secondary"><i class="bi bi-printer me-1"></i> Print / PDF</button>
        </div>
      </form>
    </div>
    <div class="card content-card p-4">
      <div class="d-flex justify-content-between gap-3 flex-wrap mb-3">
        <div>
          <h2 class="admin-panel-heading"><?php echo e($reportOptions[$type]); ?></h2>
          <div class="admin-panel-subtitle"><?php echo e($fromDate); ?> to <?php echo e($toDate); ?> · <?php echo count($rows); ?> groups</div>
        </div>
        <span class="admin-pill">Read-only report</span>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle mb-0">
          <thead><tr><?php foreach ($columns as $heading): ?><th><?php echo e($heading); ?></th><?php endforeach; ?></tr></thead>
          <tbody>
            <?php if (!$rows): ?><tr><td colspan="<?php echo count($columns); ?>" class="admin-empty">No records match the selected filters.</td></tr><?php endif; ?>
            <?php foreach ($rows as $row): ?><tr>
              <?php foreach (array_keys($columns) as $field): ?>
                <td><?php echo e($row[$field] ?? ''); ?></td>
              <?php endforeach; ?>
            </tr><?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <p class="text-muted small mt-3 mb-0">
        Reports reflect current complaint status for records created in the selected period (except escalation and rating reports, which use event dates).
        "Reopened" means a recorded reopen event; it is not necessarily an ICTS "Repeated Complaint".
        Officer statistics use current assignments, not all historical disposals. Output is limited to 500 groups.
      </p>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/employee-footer.php'; ?>
