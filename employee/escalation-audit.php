<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/escalation-quality.php';

$user = require_roles(['Super Admin', 'Admin']);
require_permission('masters.view');
$pageTitle = 'Escalation Configuration Audit';

$departmentFilter = max(0, (int)($_GET['department_id'] ?? 0));
$stageFilter = max(0, (int)($_GET['stage'] ?? 0));
if ($stageFilter > 4) $stageFilter = 0;
$issueFilter = (string)($_GET['issue'] ?? 'needs_review');
if (!in_array($issueFilter, ['needs_review','all','designation','mapping','inactive'], true)) {
    $issueFilter = 'needs_review';
}
$search = trim((string)($_GET['q'] ?? ''));
if (strlen($search) > 100) $search = substr($search, 0, 100);
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 50;
$maxScan = 5000;

$departments = db()->query(
    'SELECT id,department_name FROM departments WHERE is_active=1 ORDER BY department_name'
)->fetchAll(PDO::FETCH_ASSOC);
$where = ['el.is_active=1'];
$params = [];
if ($departmentFilter) {
    $where[] = 'd.id=?';
    $params[] = $departmentFilter;
}
if ($stageFilter) {
    $where[] = 'el.level_no=?';
    $params[] = $stageFilter;
}
if ($search !== '') {
    $where[] = '(cc.category_name LIKE ? OR o.officer_name LIKE ? OR CAST(cc.id AS CHAR)=?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
    $params[] = $search;
}
$sql = "SELECT
           el.id AS rule_id,el.category_id,el.level_no,el.designation AS configured_role,
           el.escalation_period_days,cc.category_name,
           d.id AS department_id,d.department_name,
           o.id AS officer_id,o.officer_name,o.designation AS actual_designation,
           o.is_active AS officer_active,o.user_id,
           EXISTS(SELECT 1 FROM officer_department_map dm
                  WHERE dm.officer_id=o.id AND dm.department_id=d.id) AS department_match,
           EXISTS(SELECT 1 FROM officer_category_map cm
                  WHERE cm.officer_id=o.id AND cm.category_id=cc.id) AS category_match
        FROM escalation_officer_levels el
        JOIN complaint_categories cc ON cc.id=el.category_id
        JOIN departments d ON d.id=cc.department_id
        JOIN officers o ON o.id=el.officer_id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY d.department_name,cc.category_name,el.level_no,el.id
        LIMIT " . ($maxScan + 1);
$stmt = db()->prepare($sql);
$stmt->execute($params);
$scanned = $stmt->fetchAll(PDO::FETCH_ASSOC);
$truncated = count($scanned) > $maxScan;
if ($truncated) $scanned = array_slice($scanned, 0, $maxScan);

$totals = [
    'scanned' => count($scanned),
    'issues' => 0,
    'designation' => 0,
    'missing_title' => 0,
    'mapping' => 0,
    'inactive' => 0,
    'not_linked' => 0,
];
$matched = [];
foreach ($scanned as $rule) {
    $issues = escalation_rule_issues($rule);
    $status = escalation_designation_check($rule['configured_role'], $rule['actual_designation']);
    $mapBad = (int)$rule['department_match'] !== 1 || (int)$rule['category_match'] !== 1;
    $inactive = (int)$rule['officer_active'] !== 1;
    $titleBad = $status !== 'text_match';
    $totals['issues'] += $issues ? 1 : 0;
    $totals['designation'] += $titleBad ? 1 : 0;
    $totals['missing_title'] += $status === 'missing' ? 1 : 0;
    $totals['mapping'] += $mapBad ? 1 : 0;
    $totals['inactive'] += $inactive ? 1 : 0;
    $totals['not_linked'] += empty($rule['user_id']) ? 1 : 0;
    $show = match ($issueFilter) {
        'all' => true,
        'designation' => $titleBad,
        'mapping' => $mapBad,
        'inactive' => $inactive,
        default => count($issues) > 0,
    };
    if ($show) {
        $rule['review_issues'] = $issues;
        $rule['title_status'] = $status;
        $matched[] = $rule;
    }
}
$totalMatched = count($matched);
$pages = max(1, (int)ceil($totalMatched / $perPage));
$page = min($page, $pages);
$rows = array_slice($matched, ($page - 1) * $perPage, $perPage);

if (($_GET['export'] ?? '') === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="smartcity_escalation_audit_' . date('Ymd') . '.csv"');
    header('X-Content-Type-Options: nosniff');
    $out = fopen('php://output', 'wb');
    if ($out === false) exit;
    fwrite($out, "\xEF\xBB\xBF");
    fputcsv($out, ['Department','Category','Category ID','Stage','Configured Role','Officer ID',
        'Officer Name','Recorded Designation','SLA Days','Department Map','Category Map','Officer Active','Review Reasons']);
    foreach ($matched as $row) {
        $values = [
            $row['department_name'],$row['category_name'],$row['category_id'],$row['level_no'],
            $row['configured_role'],$row['officer_id'],$row['officer_name'],$row['actual_designation'],
            $row['escalation_period_days'],$row['department_match'],$row['category_match'],
            $row['officer_active'],implode('; ', $row['review_issues'])
        ];
        $safe = array_map(static function ($value): string {
            $value = (string)($value ?? '');
            return preg_match('/^\s*[=+\-@]/u', $value) ? "'" . $value : $value;
        }, $values);
        fputcsv($out, $safe);
    }
    fclose($out);
    exit;
}
$csvQuery = $_GET;
$csvQuery['export'] = 'csv';
unset($csvQuery['page']);
include __DIR__ . '/../includes/employee-header.php';
?>
<div class="admin-heading">
  <div>
    <span class="admin-eyebrow">Advanced ICTS / Quality Assurance</span>
    <h1 class="fw-bold mb-1"><i class="bi bi-shield-exclamation me-2 text-warning"></i>Escalation Configuration Audit</h1>
    <p>Review department/category assignments, officer status and designation differences across configured stages.</p>
  </div>
  <a href="<?php echo e(url('employee/allocation-inspector.php')); ?>" class="btn btn-outline-primary">Allocation Inspector</a>
</div>
<div class="alert alert-warning border-0">
  <strong>Manual authorization is still required.</strong> A green text-match or mapping check is not proof of an official
  appointment. Configured titles may be outdated; abbreviations and authorized delegations can be flagged for review.
  Do not activate production escalation from this report.
</div>
<div class="row g-3 mb-4">
  <?php foreach ([
      ['Scanned Stage Rules','scanned','bi-diagram-3'],
      ['With Review Flags','issues','bi-exclamation-triangle'],
      ['Title Needs Review','designation','bi-person-vcard'],
      ['Missing Titles','missing_title','bi-person-x'],
      ['Missing Maps','mapping','bi-diagram-2'],
      ['Inactive Officers','inactive','bi-person-dash']
    ] as [$label,$key,$icon]): ?>
    <div class="col-6 col-xl-2">
      <div class="card content-card p-3 h-100">
        <div class="small text-muted mb-1"><i class="bi <?php echo e($icon); ?> me-1"></i><?php echo e($label); ?></div>
        <div class="h3 fw-bold mb-0"><?php echo number_format((int)$totals[$key]); ?></div>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<div class="card content-card p-4 mb-3">
  <form method="get" class="row g-2 align-items-end">
    <div class="col-lg-3 col-sm-6">
      <label class="form-label small">Department</label>
      <select class="form-select" name="department_id">
        <option value="0">All departments</option>
        <?php foreach ($departments as $department): ?>
          <option value="<?php echo (int)$department['id']; ?>"<?php echo $departmentFilter === (int)$department['id'] ? ' selected' : ''; ?>><?php echo e($department['department_name']); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-lg-2 col-sm-6">
      <label class="form-label small">Stage</label>
      <select class="form-select" name="stage">
        <option value="0">All stages</option>
        <?php for ($i=1;$i<=4;$i++): ?>
          <option value="<?php echo $i; ?>"<?php echo $stageFilter===$i ? ' selected' : ''; ?>>Stage <?php echo $i; ?></option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="col-lg-2 col-sm-6">
      <label class="form-label small">Check</label>
      <select class="form-select" name="issue">
        <?php foreach ([
          'needs_review'=>'Needs review','all'=>'All active rules','designation'=>'Title discrepancy',
          'mapping'=>'Missing mapping','inactive'=>'Inactive officer'
        ] as $value=>$label): ?>
          <option value="<?php echo e($value); ?>"<?php echo $issueFilter===$value ? ' selected' : ''; ?>><?php echo e($label); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-lg-3 col-sm-6">
      <label class="form-label small">Category ID, category or officer</label>
      <input class="form-control" type="search" name="q" maxlength="100" value="<?php echo e($search); ?>" placeholder="Search...">
    </div>
    <div class="col-lg-2 d-flex gap-2">
      <button class="btn btn-primary" type="submit">Apply</button>
      <a class="btn btn-outline-secondary" href="<?php echo e(url('employee/escalation-audit.php')); ?>">Reset</a>
    </div>
  </form>
</div>
<div class="card content-card p-4">
  <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-3">
    <div>
      <h2 class="admin-panel-heading">Escalation Stage Audit</h2>
      <p class="admin-panel-subtitle mb-0">
        <?php echo number_format($totalMatched); ?> matching active rules ·
        page <?php echo $page; ?> of <?php echo $pages; ?>
        <?php if ($truncated): ?> · First <?php echo $maxScan; ?> rules scanned (incomplete)<?php endif; ?>
      </p>
    </div>
    <a class="btn btn-outline-success btn-sm"
       href="<?php echo e(url('employee/escalation-audit.php?' . http_build_query($csvQuery))); ?>">
       <i class="bi bi-filetype-csv me-1"></i> Export Filtered CSV
    </a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead><tr><th>Department / Category</th><th>Stage</th><th>Configured Role</th><th>Officer / Recorded Title</th><th>SLA</th><th>Maps</th><th>Review Reasons</th></tr></thead>
      <tbody>
        <?php if (!$rows): ?><tr><td colspan="7" class="admin-empty">No matching active stage rules.</td></tr><?php endif; ?>
        <?php foreach ($rows as $row): ?>
          <tr>
            <td>
              <strong><?php echo e($row['category_name']); ?></strong>
              <div class="text-muted small"><?php echo e($row['department_name']); ?> · Category ID <?php echo (int)$row['category_id']; ?></div>
            </td>
            <td>Stage <?php echo (int)$row['level_no']; ?></td>
            <td><?php echo e($row['configured_role']); ?></td>
            <td>
              <strong><?php echo e($row['officer_name']); ?></strong> (#<?php echo (int)$row['officer_id']; ?>)
              <div class="small text-muted"><?php echo e($row['actual_designation'] ?: 'No designation'); ?></div>
              <div class="small"><?php echo $row['title_status']==='text_match'
                ? '<span class="text-success">Title text matches (unverified)</span>'
                : '<span class="text-warning">Manual title review</span>'; ?></div>
            </td>
            <td><?php echo (int)$row['escalation_period_days']; ?> d</td>
            <td>
              <span class="badge text-bg-<?php echo (int)$row['department_match']===1 ? 'success':'warning'; ?>">Dept <?php echo (int)$row['department_match']===1 ? 'mapped':'missing'; ?></span>
              <span class="badge text-bg-<?php echo (int)$row['category_match']===1 ? 'success':'warning'; ?>">Category <?php echo (int)$row['category_match']===1 ? 'mapped':'missing'; ?></span>
            </td>
            <td>
              <?php if (!$row['review_issues']): ?>
                <span class="text-muted small">No automatic flags. Official authority not verified.</span>
              <?php else: ?>
                <?php foreach ($row['review_issues'] as $issue): ?>
                  <div class="small text-warning"><i class="bi bi-exclamation-circle me-1"></i><?php echo e($issue); ?></div>
                <?php endforeach; ?>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php if ($pages>1): ?>
    <nav class="d-flex flex-wrap gap-2 mt-3" aria-label="Audit pagination">
      <?php foreach ([
        'Previous'=>max(1,$page-1),'Next'=>min($pages,$page+1)
      ] as $label=>$target): ?>
        <?php $query=$_GET; $query['page']=$target;unset($query['export']); ?>
        <a class="btn btn-outline-secondary btn-sm<?php echo $target===$page ? ' disabled':''; ?>"
           href="<?php echo e(url('employee/escalation-audit.php?' . http_build_query($query))); ?>"><?php echo e($label); ?></a>
      <?php endforeach; ?>
    </nav>
  <?php endif; ?>
  <p class="text-muted small mt-3 mb-0">
    Inspects active category-level escalation rules only, not manual ward-specific department allocations.
    Missing user links do not necessarily mean a stage officer is unauthorized.
    <?php echo number_format((int)$totals['not_linked']); ?> scanned stage rules reference officers without linked portal accounts.
    No records are changed by this page.
  </p>
</div>
<?php include __DIR__ . '/../includes/employee-footer.php'; ?>
