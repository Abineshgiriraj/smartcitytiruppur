<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/escalation-quality.php';

$user = require_roles(['Super Admin', 'Admin']);
require_permission('masters.view');
$pageTitle = 'Advanced Master Management';
$tabs = [
    'designations' => ['Designations', 'bi-person-vcard'],
    'officers' => ['Officer & Ward Mapping', 'bi-diagram-2'],
    'users' => ['Employee User Mapping', 'bi-person-badge'],
    'roles' => ['Roles & Page Access', 'bi-shield-lock'],
    'escalations' => ['DCC Escalation Mapping', 'bi-diagram-3'],
    'sources' => ['Complaint Sources', 'bi-broadcast'],
];
$tab = (string)($_GET['tab'] ?? 'designations');
if (!isset($tabs[$tab])) $tab = 'designations';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_permission('designations.manage');
    verify_csrf();
    try {
        if (($_POST['action'] ?? '') !== 'update_designation') {
            throw new RuntimeException('Unsupported master action.');
        }
        $officerId = (int)($_POST['officer_id'] ?? 0);
        $designation = trim((string)($_POST['designation'] ?? ''));
        if ($officerId <= 0 || $designation === '' || strlen($designation) > 100) {
            throw new RuntimeException('Select a valid officer and enter an official designation (maximum 100 bytes).');
        }
        $pdo = db();
        $find = $pdo->prepare('SELECT officer_name, designation FROM officers WHERE id=? LIMIT 1');
        $find->execute([$officerId]);
        $before = $find->fetch(PDO::FETCH_ASSOC);
        if (!$before) throw new RuntimeException('Officer does not exist.');

        // Only the human-reviewed officer title changes. No escalation rule or assignment is updated.
        if ((string)($before['designation'] ?? '') !== $designation) {
            $update = $pdo->prepare('UPDATE officers SET designation=?, updated_at=NOW() WHERE id=?');
            $update->execute([$designation, $officerId]);
            audit_log((int)$user['id'], 'officer_designation_updated', 'officer', $officerId, [
                'officer_name' => $before['officer_name'],
                'old_designation' => $before['designation'],
                'new_designation' => $designation,
            ]);
        }
        flash('success', 'Officer designation saved. Escalation rules were not changed.');
    } catch (Throwable $error) {
        flash('danger', APP_ENV === 'local' ? $error->getMessage() : 'Could not save designation.');
    }
    redirect('employee/master-control.php?tab=designations');
}

$totals = db()->query(
    "SELECT
        (SELECT COUNT(*) FROM officers) AS officer_total,
        (SELECT COUNT(*) FROM officers WHERE designation IS NULL OR TRIM(designation)='') AS missing_titles,
        (SELECT COUNT(*) FROM users) AS users_total,
        (SELECT COUNT(*) FROM officers WHERE user_id IS NOT NULL) AS linked_officers,
        (SELECT COUNT(*) FROM escalation_officer_levels WHERE is_active=1) AS escalation_rules,
        (SELECT COUNT(*) FROM officer_ward_map) AS ward_mappings"
)->fetch(PDO::FETCH_ASSOC);

$rows = [];
$search = trim((string)($_GET['q'] ?? ''));
if (strlen($search) > 100) $search = substr($search, 0, 100);
$categoryId = max(0, (int)($_GET['category_id'] ?? 0));
$groupCounts = [];

switch ($tab) {
    case 'designations':
        $query = db()->prepare(
            "SELECT o.id,o.officer_name,o.designation,o.is_active, u.full_name AS linked_user
             FROM officers o LEFT JOIN users u ON u.id=o.user_id
             WHERE (?='' OR o.officer_name LIKE ? OR o.designation LIKE ?)
             ORDER BY CASE WHEN o.designation IS NULL OR TRIM(o.designation)='' THEN 0 ELSE 1 END,
                      o.officer_name LIMIT 200"
        );
        $like = '%' . $search . '%';
        $query->execute([$search, $like, $like]);
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 'officers':
        $query = db()->prepare(
            "SELECT o.id,o.officer_name,o.designation,o.is_active,
                 (SELECT COUNT(*) FROM officer_department_map m WHERE m.officer_id=o.id) AS departments,
                 (SELECT COUNT(*) FROM officer_category_map m WHERE m.officer_id=o.id) AS categories,
                 (SELECT COUNT(*) FROM officer_ward_map m WHERE m.officer_id=o.id) AS wards,
                 (SELECT COUNT(*) FROM escalation_officer_levels el WHERE el.officer_id=o.id AND el.is_active=1) AS stages
             FROM officers o
             WHERE (?='' OR o.officer_name LIKE ?)
             ORDER BY o.officer_name LIMIT 200"
        );
        $query->execute([$search, '%' . $search . '%']);
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 'users':
        $query = db()->prepare(
            "SELECT u.id,u.full_name,u.role,u.is_active,u.department_id,u.zone_id,u.ward_id,
                 d.department_name,z.zone_no,w.ward_no,o.id AS officer_id,o.officer_name
             FROM users u
             LEFT JOIN departments d ON d.id=u.department_id
             LEFT JOIN zones z ON z.id=u.zone_id
             LEFT JOIN wards w ON w.id=u.ward_id
             LEFT JOIN officers o ON o.user_id=u.id
             WHERE (?='' OR u.full_name LIKE ? OR o.officer_name LIKE ?)
             ORDER BY u.full_name LIMIT 200"
        );
        $query->execute([$search, '%' . $search . '%', '%' . $search . '%']);
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 'roles':
        $rows = db()->query(
            "SELECT role, COUNT(*) AS total, SUM(is_active=1) AS active_users
             FROM users GROUP BY role ORDER BY role"
        )->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 'escalations':
        $sql = "SELECT cc.id AS category_id,cc.category_name,d.department_name,el.id AS rule_id,
                       el.level_no,el.designation AS configured_role,el.escalation_period_days,
                       el.is_active,o.id AS officer_id,o.officer_name,
                       o.designation AS actual_designation,o.is_active AS officer_active,
                       EXISTS(SELECT 1 FROM officer_department_map dm
                              WHERE dm.officer_id=o.id AND dm.department_id=d.id) AS department_match,
                       EXISTS(SELECT 1 FROM officer_category_map cm
                              WHERE cm.officer_id=o.id AND cm.category_id=cc.id) AS category_match
                FROM escalation_officer_levels el
                JOIN complaint_categories cc ON cc.id=el.category_id
                JOIN departments d ON d.id=cc.department_id
                JOIN officers o ON o.id=el.officer_id
                WHERE (?=0 OR cc.id=?)
                ORDER BY d.department_name,cc.category_name,el.level_no LIMIT 300";
        $stmt = db()->prepare($sql);
        $stmt->execute([$categoryId, $categoryId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $groupCounts = db()->query(
            "SELECT el.level_no,COUNT(*) AS rules,
                    SUM(o.is_active=0) AS inactive_officers,
                    SUM(o.designation IS NULL OR TRIM(o.designation)='') AS missing_designations
             FROM escalation_officer_levels el JOIN officers o ON o.id=el.officer_id
             WHERE el.is_active=1 GROUP BY el.level_no ORDER BY el.level_no"
        )->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 'sources':
        $rows = db()->query(
            "SELECT COALESCE(NULLIF(TRIM(source),''),'Unknown') AS source_label,COUNT(*) AS complaints
             FROM complaints WHERE is_deleted=0
             GROUP BY COALESCE(NULLIF(TRIM(source),''),'Unknown')
             ORDER BY complaints DESC LIMIT 100"
        )->fetchAll(PDO::FETCH_ASSOC);
        break;
}

include __DIR__ . '/../includes/employee-header.php';
?>
<div class="admin-heading">
  <div>
    <span class="admin-eyebrow">ICTS Master Administration</span>
    <h1 class="fw-bold mb-1">Advanced Master Control</h1>
    <p>Inspect and validate current Smart City officers, employee mappings, roles and escalation rules.</p>
  </div>
  <div class="d-flex flex-wrap gap-2">
    <a href="<?php echo e(url('employee/masters.php')); ?>" class="btn btn-outline-secondary">
      <i class="bi bi-sliders me-1"></i> Existing Masters
    </a>
    <a href="<?php echo e(url('employee/users.php')); ?>" class="btn btn-outline-primary">
      <i class="bi bi-people me-1"></i> Manage Users
    </a>
  </div>
</div>
<div class="row g-3 mb-4">
  <?php foreach ([
      ['Officers','officer_total','bi-people'],
      ['Missing Designations','missing_titles','bi-exclamation-circle'],
      ['Linked Employee Accounts','linked_officers','bi-person-check'],
      ['Ward Mappings','ward_mappings','bi-geo'],
  ] as [$label,$key,$icon]): ?>
    <div class="col-6 col-xl-3">
      <div class="card admin-metric h-100">
        <div class="admin-metric-top"><span><?php echo e($label); ?></span><i class="bi <?php echo e($icon); ?> fs-4 text-primary"></i></div>
        <div class="admin-metric-value"><?php echo number_format((int)($totals[$key] ?? 0)); ?></div>
        <div class="admin-metric-caption">Current Smart City database</div>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<div class="card content-card p-3 mb-3">
  <div class="d-flex gap-2 flex-wrap">
    <?php foreach ($tabs as $key=>$item): ?>
      <a class="btn btn-sm <?php echo $tab===$key ? 'btn-primary' : 'btn-outline-secondary'; ?>"
         href="<?php echo e(url('employee/master-control.php?tab=' . rawurlencode($key))); ?>">
        <i class="bi <?php echo e($item[1]); ?> me-1"></i><?php echo e($item[0]); ?>
      </a>
    <?php endforeach; ?>
  </div>
</div>
<?php if ($tab==='designations'): ?>
  <div class="alert alert-warning border-0">
    <strong>Official role verification required.</strong> Escalation configuration labels are not verified officer titles.
    Only enter a designation after checking the authorized personnel record.
    Saving a designation will update that officer's <code>officers.designation</code> field only.
  </div>
<?php elseif ($tab==='roles'): ?>
  <div class="alert alert-info border-0">
    Current access is controlled through PHP role checks and scoped queries. A legacy-style editable Role Page Mapper
    and action-level permission matrix are <strong>not yet implemented</strong>; displaying roles here does not enable new permissions.
  </div>
<?php elseif ($tab==='escalations'): ?>
  <div class="alert alert-warning border-0">
    <a class="btn btn-outline-warning btn-sm mb-2" href="<?php echo e(url('employee/escalation-audit.php')); ?>">Open Escalation Audit</a>
    Escalation rules are for inspection only. Actual officer designations, category and department authorization must be verified
    before enabling automatic production scheduling.
  </div>
<?php elseif ($tab==='sources'): ?>
  <div class="alert alert-info border-0">
    The current schema stores complaint sources as text values. This list shows sources currently used; it is not a separate editable
    Complaint Source Master table.
  </div>
<?php endif; ?>
<div class="card content-card p-4">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
      <h2 class="admin-panel-heading"><?php echo e($tabs[$tab][0]); ?></h2>
      <p class="admin-panel-subtitle mb-0"><?php echo count($rows); ?> rows shown<?php echo $tab==='escalations' ? ' (up to 300; filter by category ID)' : ''; ?></p>
    </div>
    <?php if (in_array($tab,['designations','officers','users'],true)): ?>
      <form method="get" class="d-flex gap-2">
        <input type="hidden" name="tab" value="<?php echo e($tab); ?>">
        <input type="search" class="form-control form-control-sm" name="q" value="<?php echo e($search); ?>" placeholder="Search name..." maxlength="100">
        <button class="btn btn-sm btn-outline-primary" type="submit">Search</button>
      </form>
    <?php elseif ($tab==='escalations'): ?>
      <form method="get" class="d-flex gap-2 align-items-center">
        <input type="hidden" name="tab" value="escalations">
        <input class="form-control form-control-sm" type="number" min="0" name="category_id" aria-label="Category ID" placeholder="Category ID" value="<?php echo $categoryId ?: ''; ?>">
        <button class="btn btn-sm btn-outline-primary" type="submit">Filter</button>
      </form>
    <?php endif; ?>
  </div>
  <?php if ($tab==='escalations'): ?>
    <div class="row g-2 mb-4">
      <?php foreach ($groupCounts as $level): ?>
        <div class="col-md-3 col-6"><div class="border rounded p-3 h-100">
          <div class="text-muted small">Stage <?php echo (int)$level['level_no']; ?></div>
          <strong><?php echo (int)$level['rules']; ?> active rules</strong>
          <div class="small text-warning"><?php echo (int)$level['missing_designations']; ?> missing titles</div>
          <div class="small text-danger"><?php echo (int)$level['inactive_officers']; ?> inactive officers</div>
        </div></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <?php if ($tab==='designations'): ?>
        <thead><tr><th>Officer ID</th><th>Officer</th><th>Current Designation</th><th>Linked Account</th><th>Status</th><th>Save Official Designation</th></tr></thead>
        <tbody>
          <?php foreach ($rows as $row): ?>
            <tr>
              <td><?php echo (int)$row['id']; ?></td>
              <td class="fw-semibold"><?php echo e($row['officer_name']); ?></td>
              <td><?php echo $row['designation'] ? e($row['designation']) : '<span class="text-warning">Not verified / empty</span>'; ?></td>
              <td><?php echo e($row['linked_user'] ?: 'Not linked'); ?></td>
              <td><?php echo (int)$row['is_active']===1 ? 'Active' : 'Inactive'; ?></td>
              <td>
                <?php if (can_access_permission($user, 'designations.manage')): ?><form method="post" action="<?php echo e(url('employee/master-control.php?tab=designations')); ?>" class="d-flex gap-2 align-items-center">
                  <?php echo csrf_field(); ?>
                  <input type="hidden" name="action" value="update_designation">
                  <input type="hidden" name="officer_id" value="<?php echo (int)$row['id']; ?>">
                  <input class="form-control form-control-sm" type="text" name="designation" aria-label="Official designation" value="<?php echo e($row['designation'] ?? ''); ?>" maxlength="100" required>
                  <button class="btn btn-sm btn-outline-primary" type="submit">Save</button>
                </form><?php else: ?><span class="text-muted">Read only</span><?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      <?php elseif ($tab==='officers'): ?>
        <thead><tr><th>ID</th><th>Officer</th><th>Designation</th><th>Departments</th><th>Categories</th><th>Wards</th><th>Escalation Rules</th><th>Active</th></tr></thead>
        <tbody>
          <?php foreach ($rows as $row): ?><tr>
            <td><?php echo (int)$row['id']; ?></td><td class="fw-semibold"><?php echo e($row['officer_name']); ?></td>
            <td><?php echo e($row['designation'] ?: '—'); ?></td>
            <td><?php echo (int)$row['departments']; ?></td><td><?php echo (int)$row['categories']; ?></td>
            <td><?php echo (int)$row['wards']; ?></td><td><?php echo (int)$row['stages']; ?></td>
            <td><?php echo (int)$row['is_active']===1 ? 'Yes' : 'No'; ?></td>
          </tr><?php endforeach; ?>
        </tbody>
      <?php elseif ($tab==='users'): ?>
        <thead><tr><th>Employee User</th><th>Role</th><th>Department</th><th>Zone / Ward Scope</th><th>Officer Link</th><th>Status</th></tr></thead>
        <tbody>
          <?php foreach ($rows as $row): ?><tr>
            <td class="fw-semibold"><?php echo e($row['full_name']); ?></td>
            <td><?php echo e($row['role']); ?></td>
            <td><?php echo e($row['department_name'] ?: '—'); ?></td>
            <td><?php echo e(($row['zone_no'] ? 'Zone ' . $row['zone_no'] : '—') . ($row['ward_no'] ? ' / Ward ' . $row['ward_no'] : '')); ?></td>
            <td><?php echo e($row['officer_name'] ?: 'Not linked'); ?></td>
            <td><?php echo (int)$row['is_active']===1 ? 'Active' : 'Inactive'; ?></td>
          </tr><?php endforeach; ?>
        </tbody>
      <?php elseif ($tab==='roles'): ?>
        <thead><tr><th>Role</th><th>Users</th><th>Active</th><th>Role Management</th></tr></thead>
        <tbody>
          <?php foreach ($rows as $row): ?><tr>
            <td class="fw-semibold"><?php echo e($row['role']); ?></td>
            <td><?php echo (int)$row['total']; ?></td>
            <td><?php echo (int)$row['active_users']; ?></td>
            <td><a href="<?php echo e(url('employee/users.php')); ?>">View users / roles</a></td>
          </tr><?php endforeach; ?>
        </tbody>
      <?php elseif ($tab==='escalations'): ?>
        <thead><tr><th>Category / Department</th><th>Stage</th><th>Configured Role</th><th>Officer</th><th>Actual Designation</th><th>Title Check</th><th>Days</th><th>Mapping</th><th>Active</th></tr></thead>
        <tbody>
          <?php foreach ($rows as $row): ?>
            <?php $mapped=(int)$row['department_match']===1 && (int)$row['category_match']===1; ?>
            <tr>
              <td><strong><?php echo e($row['category_name']); ?></strong><div class="text-muted small"><?php echo e($row['department_name']); ?> · ID <?php echo (int)$row['category_id']; ?></div></td>
              <td><?php echo (int)$row['level_no']; ?></td>
              <td><?php echo e($row['configured_role']); ?></td>
              <td><?php echo e($row['officer_name']); ?> (#<?php echo (int)$row['officer_id']; ?>)</td>
              <td><?php echo $row['actual_designation'] ? e($row['actual_designation']) : '<span class="text-warning">Not recorded</span>'; ?></td>
              <?php $titleCheck=escalation_designation_check($row['configured_role'], $row['actual_designation']); ?>
              <td><span class="badge text-bg-<?php echo $titleCheck==='text_match' ? 'secondary' : 'warning'; ?>">
                <?php echo $titleCheck==='text_match' ? 'Text matches only' : 'Manual review'; ?>
              </span></td>
              <td><?php echo (int)$row['escalation_period_days']; ?></td>
              <td><span class="badge text-bg-<?php echo $mapped ? 'success' : 'warning'; ?>"><?php echo $mapped ? 'Dept & Category OK' : 'Review mapping'; ?></span></td>
              <td><?php echo ((int)$row['is_active']===1 && (int)$row['officer_active']===1) ? 'Yes' : 'No'; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      <?php else: ?>
        <thead><tr><th>Complaint Source</th><th>Complaints</th></tr></thead>
        <tbody>
          <?php foreach ($rows as $row): ?><tr><td class="fw-semibold"><?php echo e($row['source_label']); ?></td><td><?php echo number_format((int)$row['complaints']); ?></td></tr><?php endforeach; ?>
        </tbody>
      <?php endif; ?>
      <?php if (!$rows): ?><tbody><tr><td colspan="8" class="admin-empty">No matching records found.</td></tr></tbody><?php endif; ?>
    </table>
  </div>
  <div class="d-flex gap-2 flex-wrap mt-4">
    <?php if ($tab==='officers'): ?>
      <a class="btn btn-sm btn-outline-primary" href="<?php echo e(url('employee/masters.php?section=mappings')); ?>">Add officer mappings</a>
      <a class="btn btn-sm btn-outline-secondary" href="<?php echo e(url('employee/masters.php?section=officers')); ?>">Add new officer</a>
    <?php elseif ($tab==='users' || $tab==='roles'): ?>
      <a class="btn btn-sm btn-outline-primary" href="<?php echo e(url('employee/users.php')); ?>">Open user management</a>
    <?php elseif ($tab==='escalations'): ?>
      <a class="btn btn-sm btn-outline-primary" href="<?php echo e(url('employee/escalation.php')); ?>">Open escalation monitor</a>
      <a class="btn btn-sm btn-outline-secondary" href="<?php echo e(url('employee/masters.php?section=allocations')); ?>">Open allocation rules</a>
    <?php endif; ?>
  </div>
</div>
<p class="small text-muted mt-3">
  Views use current Smart City MySQL data. No ICTS/PGP officers or roles are imported. Browsing these screens never changes records.
  Only the explicit "Save" designation action performs a database update.
</p>
<?php include __DIR__ . '/../includes/employee-footer.php'; ?>
