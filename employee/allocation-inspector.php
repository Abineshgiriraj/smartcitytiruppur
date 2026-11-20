<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/escalation-quality.php';

// Read-only allocation inspector. Only admins authorized to inspect master data.
$user = require_roles(['Super Admin', 'Admin']);
require_permission('masters.view');
$pageTitle = 'Allocation Inspector';

$categoryId = max(0, (int)($_GET['category_id'] ?? 0));
$wardId = max(0, (int)($_GET['ward_id'] ?? 0));
$search = trim((string)($_GET['q'] ?? ''));
$search = substr($search, 0, 100);

$pdo = db();
$totals = $pdo->query(
    "SELECT
        (SELECT COUNT(*) FROM officers) AS officers,
        (SELECT COUNT(*) FROM officers WHERE TRIM(COALESCE(officer_name,''))='') AS unnamed_officers,
        (SELECT COUNT(*) FROM officers WHERE user_id IS NOT NULL) AS linked_officers,
        (SELECT COUNT(*) FROM officer_ward_map) AS ward_mappings,
        (SELECT COUNT(*) FROM department_allocations WHERE is_active=1) AS allocation_rules,
        (SELECT COUNT(*) FROM escalation_officer_levels WHERE is_active=1) AS stage_rules,
        (SELECT COUNT(*) FROM escalation_officer_levels el JOIN officers o ON o.id=el.officer_id
            WHERE el.is_active=1 AND o.is_active=0) AS stage_inactive_officers"
)->fetch();

$categories = $pdo->query(
    'SELECT cc.id,cc.category_name,d.department_name
     FROM complaint_categories cc
     JOIN departments d ON d.id=cc.department_id
     WHERE cc.is_active=1 AND d.is_active=1
     ORDER BY d.department_name,cc.category_name'
)->fetchAll();
$wards = $pdo->query(
    'SELECT w.id,w.ward_no,z.zone_no FROM wards w
     JOIN zones z ON z.id=w.zone_id
     WHERE w.is_active=1 AND z.is_active=1
     ORDER BY z.zone_no,w.ward_no'
)->fetchAll();

$selectedCategory = null;
$selectedWard = null;
$stageRules = [];
$directRules = [];
$preview = null;
$previewSteps = [];
$warning = '';
if ($categoryId > 0) {
    $s = $pdo->prepare(
        'SELECT cc.id,cc.category_name,cc.department_id,d.department_name,cc.sla_days
         FROM complaint_categories cc
         JOIN departments d ON d.id=cc.department_id
         WHERE cc.id=? AND cc.is_active=1 AND d.is_active=1 LIMIT 1'
    );
    $s->execute([$categoryId]);
    $selectedCategory = $s->fetch() ?: null;
    if (!$selectedCategory) $warning = 'The requested category is missing or inactive.';

    if ($selectedCategory) {
        $s = $pdo->prepare(
            'SELECT el.id,el.level_no,el.designation AS configured_title,el.escalation_period_days,
                    o.id AS officer_id,o.officer_name,o.designation AS actual_title,
                    o.is_active AS officer_active,
                    EXISTS(SELECT 1 FROM officer_department_map m WHERE m.officer_id=o.id AND m.department_id=?) AS department_match,
                    EXISTS(SELECT 1 FROM officer_category_map m WHERE m.officer_id=o.id AND m.category_id=?) AS category_match
             FROM escalation_officer_levels el
             JOIN officers o ON o.id=el.officer_id
             WHERE el.category_id=? AND el.is_active=1
             ORDER BY el.level_no'
        );
        $s->execute([(int)$selectedCategory['department_id'],$categoryId,$categoryId]);
        $stageRules = $s->fetchAll();
    }
}
if ($wardId > 0) {
    $s = $pdo->prepare(
        'SELECT w.id,w.ward_no,w.zone_id,z.zone_no FROM wards w
         JOIN zones z ON z.id=w.zone_id
         WHERE w.id=? AND w.is_active=1 AND z.is_active=1 LIMIT 1'
    );
    $s->execute([$wardId]);
    $selectedWard = $s->fetch() ?: null;
    if (!$selectedWard) $warning = 'The requested ward is missing or inactive.';
}

if ($selectedCategory && $selectedWard) {
    $dept = (int)$selectedCategory['department_id'];
    $zone = (int)$selectedWard['zone_id'];

    $s = $pdo->prepare(
        'SELECT da.id,da.stage_no,da.officer_id,da.escalation_days,
                da.is_active,o.officer_name,o.designation,o.is_active AS officer_active,
                EXISTS(SELECT 1 FROM officer_department_map dm WHERE dm.officer_id=o.id AND dm.department_id=?) AS dept_match,
                EXISTS(SELECT 1 FROM officer_category_map cm WHERE cm.officer_id=o.id AND cm.category_id=?) AS category_match,
                EXISTS(SELECT 1 FROM officer_ward_map wm WHERE wm.officer_id=o.id AND wm.ward_id=?) AS ward_match
         FROM department_allocations da
         JOIN officers o ON o.id=da.officer_id
         WHERE da.department_id=? AND da.category_id=? AND da.zone_id=? AND da.ward_id=?
         ORDER BY da.stage_no'
    );
    $s->execute([$dept,$categoryId,$wardId,$dept,$categoryId,$zone,$wardId]);
    $directRules = $s->fetchAll();

    // Preview the exact *read* order of assign_officer_for_complaint().
    // No INSERT/UPDATE, complaint creation or automatic reassignment occurs.
    $tiers = [
        ['Exact active department allocation (stage 1)',
         'SELECT o.id,o.officer_name FROM department_allocations da
          JOIN officers o ON o.id=da.officer_id AND o.is_active=1
          WHERE da.department_id=? AND da.category_id=? AND da.zone_id=? AND da.ward_id=?
            AND da.stage_no=1 AND da.is_active=1 LIMIT 1',
         [$dept,$categoryId,$zone,$wardId]],
        ['Ward + department match',
         'SELECT o.id,o.officer_name FROM officers o
          JOIN officer_department_map dm ON dm.officer_id=o.id
          JOIN officer_ward_map wm ON wm.officer_id=o.id
          LEFT JOIN officer_category_map cm ON cm.officer_id=o.id AND cm.category_id=?
          WHERE dm.department_id=? AND wm.ward_id=? AND o.is_active=1
          ORDER BY (SELECT COUNT(*) FROM officer_ward_map wm2 WHERE wm2.officer_id=o.id) ASC,
                   (cm.category_id IS NOT NULL) DESC,o.id ASC LIMIT 1',
         [$categoryId,$dept,$wardId]],
        ['Category + ward match',
         'SELECT o.id,o.officer_name FROM officers o
          JOIN officer_category_map cm ON cm.officer_id=o.id
          JOIN officer_ward_map wm ON wm.officer_id=o.id
          WHERE cm.category_id=? AND wm.ward_id=? AND o.is_active=1
          ORDER BY (SELECT COUNT(*) FROM officer_ward_map wm2 WHERE wm2.officer_id=o.id) ASC,
                   o.id ASC LIMIT 1',
         [$categoryId,$wardId]],
        ['Escalation stage 1 fallback',
         'SELECT o.id,o.officer_name FROM escalation_officer_levels el
          JOIN officers o ON o.id=el.officer_id AND o.is_active=1
          WHERE el.category_id=? AND el.level_no=1 AND el.is_active=1 LIMIT 1',
         [$categoryId]],
        ['Category match',
         'SELECT o.id,o.officer_name FROM officers o
          JOIN officer_category_map cm ON cm.officer_id=o.id
          WHERE cm.category_id=? AND o.is_active=1
          ORDER BY (SELECT COUNT(*) FROM officer_ward_map wm2 WHERE wm2.officer_id=o.id) ASC,
                   o.id ASC LIMIT 1',
         [$categoryId]],
        ['Department match',
         'SELECT o.id,o.officer_name FROM officers o
          JOIN officer_department_map dm ON dm.officer_id=o.id
          WHERE dm.department_id=? AND o.is_active=1
          ORDER BY (SELECT COUNT(*) FROM officer_ward_map wm2 WHERE wm2.officer_id=o.id) ASC,
                   o.id ASC LIMIT 1',
         [$dept]],
    ];
    foreach ($tiers as [$source, $sql, $parameters]) {
        $s = $pdo->prepare($sql);
        $s->execute($parameters);
        $match = $s->fetch() ?: null;
        $previewSteps[] = [
            'source'=>$source,'match'=>$match,
            'selected'=>$preview===null && $match!==null
        ];
        if ($preview===null && $match!==null) {
            $preview = ['source'=>$source,'officer'=>$match];
        }
    }
}

$officerRows = [];
if (mb_strlen($search) >= 2) {
    $like='%' . $search . '%';
    $s = $pdo->prepare(
        'SELECT o.id,o.officer_name,o.designation,o.is_active,u.full_name AS employee_account,
          (SELECT COUNT(*) FROM officer_department_map m WHERE m.officer_id=o.id) AS depts,
          (SELECT COUNT(*) FROM officer_category_map m WHERE m.officer_id=o.id) AS categories,
          (SELECT COUNT(*) FROM officer_ward_map m WHERE m.officer_id=o.id) AS wards,
          (SELECT COUNT(*) FROM escalation_officer_levels el WHERE el.officer_id=o.id AND el.is_active=1) AS stages
         FROM officers o LEFT JOIN users u ON u.id=o.user_id
         WHERE o.officer_name LIKE ? OR CAST(o.id AS CHAR) = ?
         ORDER BY o.officer_name,o.id LIMIT 60'
    );
    $s->execute([$like,$search]);
    $officerRows = $s->fetchAll();
}
include __DIR__ . '/../includes/employee-header.php';
?>
<div class="admin-heading">
  <div>
    <span class="admin-eyebrow">ICTS / Allocation Administration</span>
    <h1 class="fw-bold mb-1"><i class="bi bi-diagram-3 text-primary me-2"></i>Officer Allocation Inspector</h1>
    <p>Inspect exact allocation rules, fallback officer selection and escalation responsibility before making changes.</p>
  </div>
  <div class="d-flex gap-2 flex-wrap"><a class="btn btn-outline-warning" href="<?php echo e(url('employee/escalation-audit.php')); ?>">Escalation Audit</a><a class="btn btn-outline-secondary" href="<?php echo e(url('employee/master-control.php?tab=officers')); ?>">Advanced Masters</a></div>
</div>
<div class="alert alert-info border-0">
  <i class="bi bi-shield-check me-1"></i><strong>Read-only simulation:</strong>
  This tool never creates a complaint, changes an officer, saves a mapping, or runs escalation.
  It shows the historical fallback order, but final assignment now also requires
  an explicit officer-stage approval. An unapproved preview candidate will remain unassigned.
</div>
<div class="row g-3 mb-4">
  <?php foreach ([
      ['Officers','officers','bi-people'],
      ['Unnamed Officers','unnamed_officers','bi-exclamation-triangle'],
      ['Linked Accounts','linked_officers','bi-person-check'],
      ['Ward Mappings','ward_mappings','bi-geo-alt'],
      ['Active Exact Rules','allocation_rules','bi-diagram-2'],
      ['Inactive Stage Officers','stage_inactive_officers','bi-person-x'],
  ] as [$title,$key,$symbol]): ?>
  <div class="col-6 col-xl-2">
    <div class="card content-card p-3 h-100">
      <div class="text-muted small mb-1"><i class="bi <?php echo e($symbol); ?> me-1"></i><?php echo e($title); ?></div>
      <h3 class="fw-bold mb-0"><?php echo number_format((int)($totals[$key] ?? 0)); ?></h3>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<div class="card content-card p-4 mb-3">
  <h2 class="admin-panel-heading mb-2">Preview initial complaint assignment</h2>
  <p class="text-muted small">Choose a category and ward. The department and zone are derived from these authoritative master records.</p>
  <form method="get" class="row g-3 align-items-end">
    <div class="col-md-5">
      <label class="form-label">Department / Complaint Category</label>
      <select name="category_id" class="form-select" required>
        <option value="">Select Category</option>
        <?php foreach ($categories as $category): ?>
          <option value="<?php echo (int)$category['id']; ?>"<?php echo $categoryId === (int)$category['id'] ? ' selected' : ''; ?>>
            <?php echo e($category['department_name'] . ' / ' . $category['category_name'] . ' (#' . $category['id'] . ')'); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Zone / Ward</label>
      <select name="ward_id" class="form-select" required>
        <option value="">Select Ward</option>
        <?php foreach ($wards as $ward): ?>
          <option value="<?php echo (int)$ward['id']; ?>"<?php echo $wardId === (int)$ward['id'] ? ' selected' : ''; ?>>
            <?php echo e('Zone ' . $ward['zone_no'] . ' / Ward ' . $ward['ward_no']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3 d-flex gap-2">
      <button class="btn btn-primary" type="submit">Preview Allocation</button>
      <a class="btn btn-outline-secondary" href="<?php echo e(url('employee/allocation-inspector.php')); ?>">Reset</a>
    </div>
  </form>
</div>
<?php if ($warning !== ''): ?>
  <div class="alert alert-warning"><?php echo e($warning); ?></div>
<?php endif; ?>
<?php if ($selectedCategory): ?>
<div class="card content-card p-4 mb-3">
  <h2 class="admin-panel-heading mb-3">Configured category escalation chain</h2>
  <div class="text-muted small mb-3"><?php echo e($selectedCategory['department_name'] . ' / ' . $selectedCategory['category_name']); ?>
    · <?php echo (int)$selectedCategory['sla_days']; ?> category SLA days
  </div>
  <div class="alert alert-warning border-0 small">
    <strong>Personnel verification required:</strong> Officer department/category mapping is not proof that an officer's
    recorded designation matches the configured stage. "Text matches" is not an official authorization check.
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead><tr><th>Stage</th><th>Configured Role</th><th>Assigned Officer</th><th>Recorded Designation</th><th>Title Review</th><th>Stage SLA</th><th>Mapping Audit</th></tr></thead>
      <tbody>
        <?php if (!$stageRules): ?><tr><td colspan="7" class="text-muted">No active escalation rules for this category.</td></tr><?php endif; ?>
        <?php foreach ($stageRules as $stage): ?>
          <?php $ok = (int)$stage['officer_active']===1 && (int)$stage['department_match']===1 && (int)$stage['category_match']===1; ?>
          <tr>
            <td>Stage <?php echo (int)$stage['level_no']; ?></td>
            <td><?php echo e($stage['configured_title']); ?></td>
            <td><strong><?php echo e($stage['officer_name']); ?></strong> (#<?php echo (int)$stage['officer_id']; ?>)</td>
            <td><?php echo $stage['actual_title'] ? e($stage['actual_title']) : '<span class="text-warning">Not recorded</span>'; ?></td>
            <?php $titleCheck=escalation_designation_check($stage['configured_title'], $stage['actual_title']); ?>
            <td><span class="badge text-bg-<?php echo $titleCheck==='text_match' ? 'secondary' : 'warning'; ?>">
              <?php echo $titleCheck==='text_match' ? 'Text matches (unverified)' : ($titleCheck==='missing' ? 'No recorded title' : 'Review title difference'); ?>
            </span></td>
            <td><?php echo (int)$stage['escalation_period_days']; ?> days</td>
            <td><span class="badge text-bg-<?php echo $ok ? 'success' : 'warning'; ?>">
              <?php echo $ok ? 'Active; Dept + Category mapped' : 'Review officer/maps'; ?>
            </span></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <p class="text-muted small mb-0">Category-level escalation rules are not necessarily ward specific. Green mapping checks are independent of title reviews. Validate each officer's appointment and authority before production scheduling.</p>
</div>
<?php endif; ?>
<?php if ($selectedCategory && $selectedWard): ?>
<div class="card content-card p-4 mb-3">
  <h2 class="admin-panel-heading mb-3">Initial assignment preview</h2>
  <div class="mb-3 text-muted small">
    <?php echo e($selectedCategory['department_name'] . ' / ' . $selectedCategory['category_name']); ?> ·
    <?php echo e('Zone ' . $selectedWard['zone_no'] . ' / Ward ' . $selectedWard['ward_no']); ?>
  </div>
  <?php if ($preview): ?>
    <div class="alert alert-success">
      <strong>First matching officer:</strong> <?php echo e($preview['officer']['officer_name']); ?>
      (ID <?php echo (int)$preview['officer']['id']; ?>) ·
      <strong>Source:</strong> <?php echo e($preview['source']); ?>
    </div>
  <?php else: ?>
    <div class="alert alert-warning">No active officer matched any initial-assignment tier. A new complaint could remain unassigned.</div>
  <?php endif; ?>
  <div class="table-responsive">
    <table class="table table-sm align-middle table-hover">
      <thead><tr><th>Priority</th><th>Rule source</th><th>Candidate</th><th>Outcome</th></tr></thead>
      <tbody>
        <?php foreach ($previewSteps as $index=>$step): ?>
          <tr>
            <td><?php echo $index+1; ?></td>
            <td><?php echo e($step['source']); ?></td>
            <td><?php echo $step['match'] ? e($step['match']['officer_name']) . ' (#' . (int)$step['match']['id'] . ')' : 'No match'; ?></td>
            <td><?php if ($step['selected']): ?><span class="badge text-bg-success">Selected</span>
              <?php elseif ($step['match']): ?><span class="text-muted">Fallback, not selected</span>
              <?php else: ?><span class="text-muted">Not matched</span><?php endif; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <h3 class="h6 fw-bold mt-4 mb-2">Exact department/ward allocation rules</h3>
  <div class="table-responsive">
    <table class="table table-sm table-bordered align-middle">
      <thead><tr><th>Stage</th><th>Officer</th><th>SLA Days</th><th>Maps</th><th>Rule / Officer Status</th></tr></thead>
      <tbody>
        <?php if (!$directRules): ?><tr><td colspan="5" class="text-muted">No exact allocation rules for this combination. PHP will use fallback tiers.</td></tr><?php endif; ?>
        <?php foreach ($directRules as $rule): ?>
          <?php $mapsGood = (int)$rule['dept_match']===1 && (int)$rule['category_match']===1 && (int)$rule['ward_match']===1; ?>
          <tr>
            <td>Stage <?php echo (int)$rule['stage_no']; ?></td>
            <td><?php echo e($rule['officer_name']); ?> (#<?php echo (int)$rule['officer_id']; ?>)</td>
            <td><?php echo (int)$rule['escalation_days']; ?></td>
            <td><span class="badge text-bg-<?php echo $mapsGood ? 'success' : 'warning'; ?>">
              <?php echo $mapsGood ? 'Dept / Category / Ward OK' : 'Review mappings'; ?></span></td>
            <td><?php echo (int)$rule['is_active']===1 && (int)$rule['officer_active']===1 ? 'Active' : 'Inactive / Review'; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <p class="text-muted small mb-0">
    This is a read-only preview based on current rules, not a guarantee of which officer will be assigned if rules change later.
    It does not validate the actual staff's appointment or authority.
  </p>
</div>
<?php endif; ?>
<div class="card content-card p-4">
  <h2 class="admin-panel-heading mb-2">Officer mapping lookup</h2>
  <p class="text-muted small">Search by officer name (2+ characters) or exact numeric ID to avoid loading all officers at once.</p>
  <form method="get" class="row g-2 mb-3">
    <div class="col-md-6"><input type="search" class="form-control" name="q" value="<?php echo e($search); ?>" placeholder="Officer name or ID..." maxlength="100"></div>
    <div class="col-auto"><button class="btn btn-outline-primary" type="submit">Search Officers</button></div>
  </form>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead><tr><th>ID</th><th>Officer</th><th>Designation</th><th>Linked User</th><th>Departments</th><th>Categories</th><th>Wards</th><th>Stages</th><th>Active</th></tr></thead>
      <tbody>
        <?php if (!$officerRows): ?><tr><td colspan="9" class="text-muted"><?php echo $search === '' ? 'Search for an officer to inspect their mapping counts.' : 'No matching officers found.'; ?></td></tr><?php endif; ?>
        <?php foreach ($officerRows as $officer): ?>
          <tr><td><?php echo (int)$officer['id']; ?></td><td class="fw-semibold"><?php echo e($officer['officer_name']); ?></td>
            <td><?php echo e($officer['designation'] ?: '—'); ?></td>
            <td><?php echo e($officer['employee_account'] ?: 'Not linked'); ?></td>
            <td><?php echo (int)$officer['depts']; ?></td><td><?php echo (int)$officer['categories']; ?></td>
            <td><?php echo (int)$officer['wards']; ?></td><td><?php echo (int)$officer['stages']; ?></td>
            <td><?php echo (int)$officer['is_active'] ? 'Yes' : 'No'; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <p class="text-muted small mb-0">Showing at most 60 matching officers. This tool does not modify assignments, officer mappings or escalation levels.</p>
</div>
<?php include __DIR__ . '/../includes/employee-footer.php'; ?>
