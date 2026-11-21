<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

$user = require_roles(['Super Admin', 'Admin']);
require_permission('roles.view');
$pageTitle = 'Role Page Mapper';
$roles = known_user_roles();
$catalog = permission_catalog();
$selectedRole = (string)($_GET['role'] ?? 'Department Head');
if (!in_array($selectedRole, $roles, true)) $selectedRole = 'Department Head';
$canEdit = can_access_permission($user, 'roles.manage') && $user['role'] === 'Super Admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_permission('roles.manage');
    verify_csrf();
    $formRole = (string)($_POST['role'] ?? '');
    $action = (string)($_POST['action'] ?? 'save');
    if (!in_array($formRole, $roles, true) || $formRole === 'Super Admin') {
        http_response_code(400);
        exit('Invalid target role.');
    }
    if (!in_array($action, ['save', 'reset'], true)) {
        http_response_code(400);
        exit('Invalid action.');
    }
    try {
        $pdo = db();
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('SELECT setting_value FROM system_settings WHERE setting_key=? FOR UPDATE');
        $stmt->execute(['role_page_permissions_v1']);
        $raw = $stmt->fetchColumn();
        $data = ['version' => 1, 'roles' => []];
        if ($raw !== false) {
            $existing = json_decode((string)$raw, true);
            if (!is_array($existing) || ($existing['version'] ?? null) !== 1
                || !isset($existing['roles']) || !is_array($existing['roles'])) {
                throw new RuntimeException('Existing permission settings are invalid. Nothing was saved.');
            }
            $data = $existing;
        }

        if ($action === 'reset') {
            unset($data['roles'][$formRole]);
        } else {
            $posted = $_POST['access'] ?? [];
            if (!is_array($posted)) throw new RuntimeException('Invalid permission input.');
            $grants = [];
            foreach ($catalog as $permission => $label) {
                $key = str_replace('.', '__', $permission);
                // Never grant a permission beyond the existing PHP role authorization.
                $grants[$permission] = baseline_permission($formRole, $permission)
                    && array_key_exists($key, $posted)
                    && (string)$posted[$key] === '1';
            }
            $data['roles'][$formRole] = $grants;
        }

        if (!$data['roles']) {
            $delete = $pdo->prepare('DELETE FROM system_settings WHERE setting_key=?');
            $delete->execute(['role_page_permissions_v1']);
        } else {
            $json = json_encode($data, JSON_THROW_ON_ERROR);
            $save = $pdo->prepare(
                'INSERT INTO system_settings (setting_key,setting_value,updated_at)
                 VALUES (?, ?, NOW())
                 ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value),updated_at=NOW()'
            );
            $save->execute(['role_page_permissions_v1', $json]);
        }
        $pdo->commit();
        audit_log((int)$user['id'], 'role_page_permissions_' . $action, 'role', null, [
            'target_role' => $formRole,
            'action' => $action,
        ]);
        flash('success', $action === 'reset' ? 'Role restored to baseline permissions.' : 'Role restrictions saved.');
    } catch (Throwable $error) {
        if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
        flash('danger', APP_ENV === 'local' ? $error->getMessage() : 'Unable to save permissions.');
    }
    redirect('employee/page-permissions.php?role=' . rawurlencode($formRole));
}

$matrix = permission_matrix_state();
$invalidMatrix = $matrix !== null && !empty($matrix['invalid']);
$configured = !$invalidMatrix && $matrix !== null && array_key_exists($selectedRole, $matrix['roles']);
$rule = $configured ? $matrix['roles'][$selectedRole] : [];
include __DIR__ . '/../includes/employee-header.php';
?>
<div class="admin-heading">
  <div>
    <span class="admin-eyebrow">ICTS Administration / Access Controls</span>
    <h1 class="fw-bold mb-1">Role Page Mapper</h1>
    <p>Review page access for every role. Only Super Admin can apply additional restrictions.</p>
  </div>
  <a class="btn btn-outline-secondary" href="<?php echo e(url('employee/master-control.php?tab=roles')); ?>">
    <i class="bi bi-arrow-left me-1"></i> Master Overview
  </a>
</div>
<div class="alert alert-info border-0 small">
  <strong>Security model:</strong> These settings can deny existing access, but never grant privileges or expand
  department, zone, ward or officer visibility. Super Admin is always allowed, for recovery.
  All changes are logged to the existing audit trail.
</div>
<?php if ($invalidMatrix): ?>
  <div class="alert alert-danger">Permission settings could not be read. Non-Super-Admin access is denied until the configuration is repaired.</div>
<?php endif; ?>
<div class="card content-card p-4 mb-3">
  <form method="get" class="row g-2 align-items-end">
    <div class="col-md-6">
      <label class="form-label">Select role to review</label>
      <select name="role" class="form-select" onchange="this.form.submit()">
        <?php foreach ($roles as $role): ?>
          <option value="<?php echo e($role); ?>"<?php echo $selectedRole === $role ? ' selected' : ''; ?>><?php echo e($role); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-auto"><button type="submit" class="btn btn-outline-primary">View Permissions</button></div>
    <div class="col-12 text-muted small mt-2">
      <?php echo $selectedRole === 'Super Admin' ? 'Super Admin has fixed full access.' :
          ($configured ? 'Customized restrictions are active for this role.' : 'Using existing role defaults (no custom restrictions).'); ?>
    </div>
  </form>
</div>
<div class="card content-card p-4">
  <?php if ($canEdit && $selectedRole !== 'Super Admin' && !$invalidMatrix): ?>
    <form method="post" action="<?php echo e(url('employee/page-permissions.php?role=' . rawurlencode($selectedRole))); ?>">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="role" value="<?php echo e($selectedRole); ?>">
      <input type="hidden" name="action" value="save">
  <?php endif; ?>
  <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
    <h2 class="admin-panel-heading"><?php echo e($selectedRole); ?> — Page & Action Permissions</h2>
    <span class="admin-pill"><?php echo $configured ? 'Custom' : 'Baseline'; ?></span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead><tr><th>Page / Action</th><th>Base Role Allowed</th><th>Effective Access</th><th><?php echo $canEdit ? 'Allowed after save' : 'Editable by Super Admin only'; ?></th></tr></thead>
      <tbody>
      <?php foreach ($catalog as $permission => $label): ?>
        <?php
          $baseline = baseline_permission($selectedRole, $permission);
          $effective = can_access_permission(['role' => $selectedRole], $permission);
          $checked = $selectedRole === 'Super Admin' ? true : ($configured ? $effective : $baseline);
        ?>
        <tr>
          <td><strong><?php echo e($label); ?></strong><div class="small text-muted"><?php echo e($permission); ?></div></td>
          <td><?php echo $baseline ? '<span class="text-success">Yes</span>' : '<span class="text-muted">No</span>'; ?></td>
          <td><?php echo $effective ? '<span class="badge text-bg-success">Allowed</span>' : '<span class="badge text-bg-secondary">Denied</span>'; ?></td>
          <td>
            <input class="form-check-input" type="checkbox" name="access[<?php echo e(str_replace('.', '__', $permission)); ?>]"
                   value="1"<?php echo $checked ? ' checked' : ''; ?>
                   <?php echo (!$canEdit || !$baseline || $selectedRole === 'Super Admin' || $invalidMatrix) ? ' disabled' : ''; ?>
                   aria-label="<?php echo e($label); ?>">
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php if ($canEdit && $selectedRole !== 'Super Admin' && !$invalidMatrix): ?>
    <div class="d-flex flex-wrap gap-2 mt-4">
      <button class="btn btn-primary" type="submit"><i class="bi bi-shield-check me-1"></i> Save restrictions for <?php echo e($selectedRole); ?></button>
      <button class="btn btn-outline-secondary" type="submit" name="action" value="reset"
              onclick="return confirm('Reset this role to its original PHP permissions?')">Restore role defaults</button>
    </div>
    </form>
  <?php else: ?>
    <p class="text-muted small mt-3 mb-0">Access changes are restricted to Super Admin. Viewing this matrix does not modify permissions.</p>
  <?php endif; ?>
</div>
<p class="text-muted small mt-3">
  The permission mapper applies to the Core PHP employee pages that have been integrated with the new permission gate.
  Existing role checks and complaint data-scope filters remain in force. Public pages and third-party apps are not controlled by this panel.
</p>
<?php include __DIR__ . '/../includes/employee-footer.php'; ?>
