<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
$user = require_roles(['Super Admin','Admin','Commissioner','Department Head']);
require_permission('escalation.view');
$pageTitle = 'Escalation Monitor';
$departmentScope = $user['role'] === 'Department Head' ? (int)($user['department_id'] ?? 0) : null;

$where = ['c.is_deleted=0', "c.status NOT IN ('Resolved','Closed','Rejected')"];
$params = [];
if ($departmentScope !== null) { $where[] = 'c.department_id=?'; $params[] = $departmentScope; }
$sql = 'SELECT c.id,c.reference_no,c.status,c.due_at,c.assigned_officer_id,
               d.department_name,o.officer_name
        FROM complaints c JOIN departments d ON d.id=c.department_id
        LEFT JOIN officers o ON o.id=c.assigned_officer_id
        WHERE ' . implode(' AND ', $where) . '
          AND c.due_at IS NOT NULL AND c.due_at < NOW()
        ORDER BY c.due_at ASC LIMIT 60';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$overdue = $stmt->fetchAll();

$rulesQuery = "SELECT el.level_no,el.designation,COUNT(*) AS total_rules,
                     SUM(o.is_active=1) AS active_mappings,
                     SUM(o.designation IS NULL OR o.designation='') AS missing_designations
              FROM escalation_officer_levels el
              JOIN officers o ON o.id=el.officer_id
              JOIN complaint_categories cc ON cc.id=el.category_id
              WHERE el.is_active=1";
$ruleParams = [];
if ($departmentScope !== null) {
    $rulesQuery .= ' AND cc.department_id=?';
    $ruleParams[] = $departmentScope;
}
$rulesQuery .= ' GROUP BY el.level_no,el.designation ORDER BY el.level_no';
$rulesStmt = db()->prepare($rulesQuery);
$rulesStmt->execute($ruleParams);
$rules = $rulesStmt->fetchAll();

$historyQuery = 'SELECT ce.level_no,ce.escalated_at,c.reference_no,o.officer_name
    FROM complaint_escalations ce JOIN complaints c ON c.id=ce.complaint_id
    JOIN officers o ON o.id=ce.officer_id WHERE c.is_deleted=0';
$historyParams = [];
if ($departmentScope !== null) { $historyQuery .= ' AND c.department_id=?'; $historyParams[] = $departmentScope; }
$historyQuery .= ' ORDER BY ce.escalated_at DESC LIMIT 12';
$historyStmt = db()->prepare($historyQuery);
$historyStmt->execute($historyParams);
$history = $historyStmt->fetchAll();
include __DIR__ . '/../includes/employee-header.php';
?>
<div class="admin-heading">
  <div><span class="admin-eyebrow">Workflow Monitoring</span><h1 class="fw-bold mb-1">Escalation Monitor</h1><p>Inspect overdue complaints, stage coverage and escalation events.</p></div>
  <?php if (in_array($user['role'],['Super Admin','Admin'],true)): ?><a class="btn btn-outline-primary" href="<?php echo e(url('employee/masters.php?section=allocations')); ?>">Allocation Rules <i class="bi bi-arrow-right ms-1"></i></a><?php endif; ?>
</div>
<div class="alert alert-warning border-0"><i class="bi bi-shield-exclamation me-2"></i><strong>Read-only monitor.</strong> Viewing this page does not execute escalation. Automatic scheduling remains disabled until officer responsibilities and later stages are verified.</div>
<div class="row g-3 mb-4">
  <div class="col-xl-7"><div class="card p-4 h-100">
    <h2 class="admin-panel-heading">Stage Configuration</h2><p class="admin-panel-subtitle">Active category-level escalation records</p>
    <div class="table-responsive"><table class="table mb-0"><thead><tr><th>Stage</th><th>Configured Role</th><th>Rules</th><th>Active</th><th>Missing designation</th></tr></thead><tbody>
    <?php if (!$rules): ?><tr><td colspan="5" class="admin-empty">No active stage rules.</td></tr><?php endif; ?>
    <?php foreach ($rules as $row): ?><tr>
      <td class="fw-bold"><?php echo (int)$row['level_no']; ?></td>
      <td><?php echo e($row['designation']); ?></td>
      <td><?php echo (int)$row['total_rules']; ?></td>
      <td><?php echo (int)$row['active_mappings']; ?></td>
      <td><?php echo (int)$row['missing_designations']; ?></td>
    </tr><?php endforeach; ?>
    </tbody></table></div>
  </div></div>
  <div class="col-xl-5"><div class="card p-4 h-100">
    <h2 class="admin-panel-heading">How escalation works</h2><p class="admin-panel-subtitle">Smart City PHP workflow</p>
    <ol class="small text-secondary ps-3 mt-3">
      <li class="mb-3">Complaint receives an initial officer and SLA deadline.</li>
      <li class="mb-3">The scheduled PHP processor checks expired deadlines.</li>
      <li class="mb-3">An eligible complaint is moved to a verified next-stage officer.</li>
      <li>Assignment, escalation and status histories are recorded together.</li>
    </ol>
    <p class="text-warning small mb-0"><i class="bi bi-info-circle me-1"></i>The configured designation label is not proof of the officer's actual role.</p>
  </div></div>
</div>
<div class="card content-card p-4 mb-4">
  <div class="d-flex justify-content-between align-items-start mb-3">
    <div><h2 class="admin-panel-heading">Overdue Open Complaints</h2><p class="admin-panel-subtitle mb-0">Past deadline; may include unassigned complaints that require manual action</p></div>
    <span class="admin-pill">Up to 60</span>
  </div>
  <div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Reference</th><th>Department</th><th>Assigned Officer</th><th>Status</th><th>Due At</th><th></th></tr></thead><tbody>
  <?php if (!$overdue): ?><tr><td colspan="6" class="admin-empty">No overdue open complaints.</td></tr><?php endif; ?>
  <?php foreach ($overdue as $row): ?><tr>
    <td class="fw-semibold"><?php echo e($row['reference_no']); ?></td>
    <td><?php echo e($row['department_name']); ?></td>
    <td><?php echo e($row['officer_name'] ?: 'Unassigned'); ?></td>
    <td><span class="badge text-bg-<?php echo e(status_badge_class($row['status'])); ?>"><?php echo e($row['status']); ?></span></td>
    <td class="text-danger"><?php echo e($row['due_at']); ?></td>
    <td><a href="<?php echo e(url('employee/complaint-view.php?id=' . (int)$row['id'])); ?>" class="btn btn-sm btn-outline-primary">Review</a></td>
  </tr><?php endforeach; ?>
  </tbody></table></div>
</div>
<div class="card content-card p-4">
  <h2 class="admin-panel-heading">Recent Escalation Events</h2><p class="admin-panel-subtitle">Escalation audit history from Smart City Tiruppur</p>
  <div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Complaint</th><th>Stage</th><th>Officer</th><th>Escalated At</th></tr></thead><tbody>
  <?php if (!$history): ?><tr><td colspan="4" class="admin-empty">No escalation events recorded yet.</td></tr><?php endif; ?>
  <?php foreach ($history as $row): ?><tr><td><?php echo e($row['reference_no']); ?></td><td><?php echo (int)$row['level_no']; ?></td><td><?php echo e($row['officer_name']); ?></td><td><?php echo e($row['escalated_at']); ?></td></tr><?php endforeach; ?>
  </tbody></table></div>
</div>
<?php include __DIR__ . '/../includes/employee-footer.php'; ?>
