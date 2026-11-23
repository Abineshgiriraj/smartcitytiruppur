<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/officer-authority.php';

$user = require_roles(['Super Admin', 'Admin']);
require_permission('masters.view');
$pageTitle = 'User Mapping Details';
$pdo = db();

$departmentId = max(0, (int)($_GET['department_id'] ?? 0));
$categoryId = max(0, (int)($_GET['category_id'] ?? 0));
$zoneId = max(0, (int)($_GET['zone_id'] ?? 0));
$wardId = max(0, (int)($_GET['ward_id'] ?? 0));
$searched = isset($_GET['search']);

$departments = $pdo->query(
    'SELECT id, department_name FROM departments WHERE is_active=1 ORDER BY department_name'
)->fetchAll(PDO::FETCH_ASSOC);
$categories = $pdo->query(
    'SELECT id, department_id, category_name FROM complaint_categories WHERE is_active=1 ORDER BY category_name'
)->fetchAll(PDO::FETCH_ASSOC);
$zones = $pdo->query(
    'SELECT id, zone_no FROM zones WHERE is_active=1 ORDER BY zone_no'
)->fetchAll(PDO::FETCH_ASSOC);
$wards = $pdo->query(
    'SELECT w.id, w.zone_id, w.ward_no, z.zone_no
     FROM wards w JOIN zones z ON z.id=w.zone_id
     WHERE w.is_active=1 AND z.is_active=1 ORDER BY z.zone_no,w.ward_no'
)->fetchAll(PDO::FETCH_ASSOC);

$chosenCategory = null;
foreach ($categories as $row) {
    if ((int)$row['id'] === $categoryId) { $chosenCategory = $row; break; }
}
$chosenWard = null;
foreach ($wards as $row) {
    if ((int)$row['id'] === $wardId) { $chosenWard = $row; break; }
}
$warning = '';
$policy = null;
$rows = [];
$stageRows = [];
$exact = [];
if ($searched) {
    if (!$departmentId || !$categoryId || !$zoneId || !$wardId
        || !$chosenCategory || !$chosenWard
        || (int)$chosenCategory['department_id'] !== $departmentId
        || (int)$chosenWard['zone_id'] !== $zoneId) {
        $warning = 'Select a matching department/category and zone/ward. No lookup was performed.';
    } else {
        $policy = pdf_policy_reviewed($pdo, $categoryId);
        if (!$policy) {
            $warning = 'Category PDF policy is missing, invalid, or not officially approved. No officer can be automatically allocated.';
        }
        try {
            // Department/category are both mandatory filters. The selected
            // ward remains visible even when a mapped officer covers another
            // ward; it is NEVER silently changed to match a source result.
            $stmt = $pdo->prepare(
                "SELECT o.id,o.officer_name,o.designation,o.mobile_no,o.is_active,
                    EXISTS(SELECT 1 FROM officer_ward_map wm
                           WHERE wm.officer_id=o.id) AS ward_scoped,
                    EXISTS(SELECT 1 FROM officer_ward_map wm
                           WHERE wm.officer_id=o.id AND wm.ward_id=?) AS ward_match,
                    (SELECT GROUP_CONCAT(w.ward_no ORDER BY w.ward_no SEPARATOR ', ')
                       FROM officer_ward_map wm JOIN wards w ON w.id=wm.ward_id
                      WHERE wm.officer_id=o.id) AS mapped_wards
                 FROM officers o
                 WHERE EXISTS (SELECT 1 FROM officer_department_map dm
                               WHERE dm.officer_id=o.id AND dm.department_id=?)
                   AND EXISTS (SELECT 1 FROM officer_category_map cm
                               WHERE cm.officer_id=o.id AND cm.category_id=?)
                 ORDER BY o.is_active DESC,o.officer_name,o.id LIMIT 200"
            );
            $stmt->execute([$wardId, $departmentId, $categoryId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $rulesStmt = $pdo->prepare(
                'SELECT stage_no, officer_id, escalation_days
                 FROM department_allocations
                 WHERE department_id=? AND category_id=? AND zone_id=?
                   AND ward_id=? AND is_active=1 ORDER BY stage_no'
            );
            $rulesStmt->execute([$departmentId,$categoryId,$zoneId,$wardId]);
            foreach ($rulesStmt->fetchAll(PDO::FETCH_ASSOC) as $rule) {
                $exact[(int)$rule['stage_no']] = $rule;
            }
            $approvalStmt = $pdo->prepare(
                'SELECT stage_no,officer_id FROM officer_stage_approvals
                 WHERE category_id=? AND revoked_at IS NULL
                   AND approved_by IS NOT NULL AND TRIM(approval_reference) <> \'\'
                 ORDER BY stage_no,officer_id'
            );
            $approvalStmt->execute([$categoryId]);
            $approvalPairs = [];
            foreach ($approvalStmt->fetchAll(PDO::FETCH_ASSOC) as $approval) {
                $approvalPairs[(int)$approval['stage_no']][(int)$approval['officer_id']] = true;
            }
            $byId = [];
            foreach ($rows as $i => $person) {
                $id = (int)$person['id'];
                $byId[$id] = $i;
                $rows[$i]['title_stages'] = [];
                $rows[$i]['authorized_stages'] = [];
                if (!$policy) continue;
                for ($level=1;$level<=(int)$policy['stage_count'];$level++) {
                    $step = pdf_policy_stage($policy,$level);
                    if (!$step || !pdf_policy_role_matches((string)$step['role_label'],(string)$person['designation'])) continue;
                    $rows[$i]['title_stages'][] = $level;
                    if (isset($approvalPairs[$level][$id])
                        && officer_stage_authorized($pdo,$categoryId,$level,$id,$departmentId,$wardId)) {
                        $rows[$i]['authorized_stages'][] = $level;
                    }
                }
            }
            if ($policy) {
                for ($level=1;$level<=(int)$policy['stage_count'];$level++) {
                    $step = pdf_policy_stage($policy,$level);
                    $authorized = [];
                    foreach ($rows as $person) {
                        if (in_array($level,$person['authorized_stages'],true)) {
                            $authorized[(int)$person['id']] = (string)$person['officer_name'];
                        }
                    }
                    $exactRule = $exact[$level] ?? null;
                    $chosenId = $exactRule ? (int)$exactRule['officer_id'] : null;
                    if ($exactRule) {
                        $ok = isset($authorized[$chosenId])
                            && (int)$exactRule['escalation_days'] === (int)$step['period_days'];
                        $outcome = $ok ? 'READY: approved exact officer'
                            : 'BLOCKED: exact rule has wrong officer, scope or SLA';
                    } elseif (!$authorized) {
                        $outcome = 'BLOCKED: no approved officer';
                    } elseif (count($authorized) === 1) {
                        $outcome = 'Eligible officer; no exact ward rule';
                    } else {
                        $outcome = 'BLOCKED: multiple approved officers need exact rule';
                    }
                    $stageRows[] = [
                        'level'=>$level, 'role'=>$step['role_label'],
                        'days'=>$step['period_days'],
                        'authorized'=>$authorized,
                        'exact_id'=>$chosenId,
                        'outcome'=>$outcome,
                    ];
                }
            }
        } catch (Throwable $error) {
            log_message('User Mapping Details could not load: '.$error->getMessage());
            $warning = 'Mapping or approval tables could not be read. Check database setup.';
            $rows = [];
            $stageRows = [];
        }
    }
}
include __DIR__ . '/../includes/employee-header.php';
?>
<div class="admin-heading">
  <div>
    <span class="admin-eyebrow">Complaint Administration / ICTS Comparison</span>
    <h1>User Mapping Details</h1>
    <p>Review the department, category, zone and ward mapping before enabling automatic allocation.</p>
  </div>
  <a class="btn btn-outline-secondary" href="<?php echo e(url('employee/allocation-inspector.php')); ?>">Allocation Inspector</a>
</div>
<div class="alert alert-info">
  <strong>Read-only review.</strong> This shows data from your own Smart City database, not a live connection to
  smarttiruppur.com. Employee names and mapped wards do not establish official authorization.
  The old site's priority values are not stored here; PDF stage numbers are shown separately.
</div>
<div class="card p-4 mb-3">
  <form method="get" class="row g-3 align-items-end">
    <div class="col-xl-3 col-md-6">
      <label class="form-label" for="department">Department</label>
      <select id="department" name="department_id" class="form-select" required>
        <option value="">Select department</option>
        <?php foreach ($departments as $d): ?>
          <option value="<?php echo (int)$d['id']; ?>" <?php echo $departmentId===(int)$d['id']?'selected':''; ?>>
            <?php echo e($d['department_name']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-xl-3 col-md-6">
      <label class="form-label" for="category">Complaint Category</label>
      <select id="category" name="category_id" class="form-select" required>
        <option value="">Select category</option>
        <?php foreach ($categories as $c): ?>
          <option data-department="<?php echo (int)$c['department_id']; ?>"
                  value="<?php echo (int)$c['id']; ?>" <?php echo $categoryId===(int)$c['id']?'selected':''; ?>>
            <?php echo e($c['category_name']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-xl-2 col-md-6">
      <label class="form-label" for="zone">Zone</label>
      <select id="zone" name="zone_id" class="form-select" required>
        <option value="">Select zone</option>
        <?php foreach ($zones as $z): ?>
          <option value="<?php echo (int)$z['id']; ?>" <?php echo $zoneId===(int)$z['id']?'selected':''; ?>>
            Zone <?php echo e($z['zone_no']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-xl-2 col-md-6">
      <label class="form-label" for="ward">Ward</label>
      <select id="ward" name="ward_id" class="form-select" required>
        <option value="">Select ward</option>
        <?php foreach ($wards as $w): ?>
          <option data-zone="<?php echo (int)$w['zone_id']; ?>" value="<?php echo (int)$w['id']; ?>"
            <?php echo $wardId===(int)$w['id']?'selected':''; ?>>
            Ward <?php echo e($w['ward_no']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-xl-2 col-md-6">
      <button type="submit" name="search" value="1" class="btn btn-primary w-100">Search</button>
    </div>
  </form>
</div>
<?php if ($warning): ?><div class="alert alert-warning"><?php echo e($warning); ?></div><?php endif; ?>
<?php if ($searched && $chosenCategory && $chosenWard
    && (int)$chosenCategory['department_id']===$departmentId
    && (int)$chosenWard['zone_id']===$zoneId): ?>
<div class="card p-4 mb-3">
  <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3">
    <div>
      <h2 class="admin-panel-heading">Employee mappings</h2>
      <div class="text-muted small">Category <?php echo e($chosenCategory['category_name']); ?>
        · Zone <?php echo e($chosenWard['zone_no']); ?> / Ward <?php echo e($chosenWard['ward_no']); ?>
        · <?php echo count($rows); ?> mapped employee(s) shown
      </div>
    </div>
    <a class="btn btn-outline-primary btn-sm" href="<?php echo e(url('employee/masters.php?section=mappings')); ?>">Review Master Mappings</a>
  </div>
  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead><tr>
        <th>#</th><th>Employee Name</th><th>Mobile No</th><th>Mapped Wards</th>
        <th>Recorded Designation</th><th>Legacy Priority</th>
        <th>PDF Role Match</th><th>Official Stage Approval</th><th>Ward Check</th>
      </tr></thead>
      <tbody>
        <?php if (!$rows): ?><tr><td colspan="9" class="text-muted py-4">
          No department/category-mapped officers found. No allocation was made.
        </td></tr><?php endif; ?>
        <?php foreach ($rows as $i=>$p): ?>
          <?php
            $wardOk=(int)$p['ward_match']===1;
            $scoped=(int)$p['ward_scoped']===1;
            $titleStages=$p['title_stages']??[];
            $approvedStages=$p['authorized_stages']??[];
          ?>
          <tr>
            <td><?php echo $i+1; ?></td>
            <td><strong><?php echo e($p['officer_name']); ?></strong>
              <span class="text-muted">(#<?php echo (int)$p['id']; ?>)</span>
              <?php if (!(int)$p['is_active']): ?><span class="badge text-bg-secondary">Inactive</span><?php endif; ?>
            </td>
            <td><?php echo e($p['mobile_no'] ?: '—'); ?></td>
            <td><?php echo e($p['mapped_wards'] ?: 'Not recorded'); ?></td>
            <td><?php echo e($p['designation'] ?: 'Not recorded'); ?></td>
            <td><span class="text-muted">Not stored</span></td>
            <td><?php echo $titleStages ? 'PDF stage '.e(implode(', ',$titleStages)) : 'No exact title match'; ?></td>
            <td>
              <?php if ($approvedStages): ?>
                <span class="badge text-bg-success">Approved: <?php echo e(implode(', ',$approvedStages)); ?></span>
              <?php else: ?>
                <span class="badge text-bg-warning">Not authorized</span>
              <?php endif; ?>
            </td>
            <td>
              <span class="badge text-bg-<?php echo $wardOk?'success':'warning'; ?>">
                <?php echo $wardOk?'Matches selected ward':($scoped?'OTHER WARD':'No ward scope recorded'); ?>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php if ($rows && !array_filter($rows,static fn(array $p):bool => (int)$p['ward_match']===1)): ?>
  <div class="alert alert-warning mt-3 mb-0">
    None of these employee records explicitly maps to the selected ward. Check the old ICTS
    page's Ward 53 / Ward 57 mismatch before any approval or assignment.
  </div>
  <?php endif; ?>
</div>
<div class="card p-4 mb-4">
  <h2 class="admin-panel-heading">Approved PDF escalation chain</h2>
  <p class="text-muted small">These are PDF stages, not the old site's priority numbers. No stage or employee is approved by viewing this page.</p>
  <div class="table-responsive">
    <table class="table align-middle">
      <thead><tr><th>PDF Stage</th><th>Required Designation</th><th>Days</th><th>Authorized candidates</th><th>Exact ward rule</th><th>Readiness</th></tr></thead>
      <tbody>
        <?php if (!$stageRows): ?><tr><td colspan="6" class="text-muted">No approved PDF policy or stage data available.</td></tr><?php endif; ?>
        <?php foreach ($stageRows as $s): ?>
        <tr>
          <td><?php echo (int)$s['level']; ?></td><td><?php echo e($s['role']); ?></td>
          <td><?php echo (int)$s['days']; ?></td>
          <td><?php echo $s['authorized'] ? e(implode(', ',array_values($s['authorized']))) : 'None'; ?></td>
          <td><?php echo $s['exact_id'] === null ? 'Not configured' : 'Officer #'.(int)$s['exact_id']; ?></td>
          <td><?php echo e($s['outcome']); ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <p class="small text-muted mb-0">To repair an approved mapping, verify the employee's official designation, identity,
    ward/category scope and signed delegation first. Then use Master Data and Officer Stage Approvals.
    Never create placeholder approvals for live municipal complaints.</p>
</div>
<?php endif; ?>
<script>
(function () {
  const department = document.getElementById('department');
  const category = document.getElementById('category');
  const zone = document.getElementById('zone');
  const ward = document.getElementById('ward');
  function constrain(child, attribute, parent) {
    const id = parent.value;
    Array.from(child.options).forEach(function (option) {
      if (!option.value) return;
      option.hidden = !id || option.getAttribute(attribute) !== id;
    });
    const selected = child.selectedOptions[0];
    if (selected && selected.hidden) child.value = '';
  }
  department.addEventListener('change', function () { constrain(category,'data-department',department); });
  zone.addEventListener('change', function () { constrain(ward,'data-zone',zone); });
  constrain(category,'data-department',department);
  constrain(ward,'data-zone',zone);
}());
</script>
<?php include __DIR__.'/../includes/employee-footer.php'; ?>
