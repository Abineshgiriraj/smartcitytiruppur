<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/officer-authority.php';

$user = require_roles(['Super Admin', 'Admin']);
require_permission('masters.view');
$pageTitle = 'Officer Stage Approvals';
$canEdit = can_access_permission($user, 'masters.manage');
$pdo = db();
$setupReady = false;
$setupError = '';
$approvals = [];
$categories = [];
$pending = [];

try {
    $probe = $pdo->query(
        "SELECT COUNT(*) FROM information_schema.TABLES
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'officer_stage_approvals'"
    );
    $setupReady = ((int)$probe->fetchColumn() === 1);
} catch (Throwable $exception) {
    $setupError = 'Unable to verify approval registry. Contact the database administrator.';
    log_message('Approval registry check failed: '.$exception->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_permission('masters.manage');
    verify_csrf();
    if (!$setupReady) {
        flash('danger', 'Approval registry is not installed. Ask the database administrator to review the migration.');
        redirect('employee/officer-approvals.php');
    }
    $action = trim((string)($_POST['action'] ?? ''));
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $stageNo = (int)($_POST['stage_no'] ?? 0);
    $officerId = (int)($_POST['officer_id'] ?? 0);
    $reference = trim((string)($_POST['approval_reference'] ?? ''));

    try {
        if ($action === 'approve') {
            if ($categoryId < 1 || $officerId < 1 || $stageNo < 1 || $stageNo > 6
                || mb_strlen($reference) < 8 || mb_strlen($reference) > 255
                || empty($_POST['confirm_authority'])) {
                throw new RuntimeException('Enter a category, stage, officer ID, approval document reference and confirm verified authority.');
            }
            $validate = $pdo->prepare(
                'SELECT cc.department_id, cc.category_name, o.officer_name, o.designation,
                        o.is_active,
                        EXISTS(SELECT 1 FROM officer_department_map dm
                               WHERE dm.officer_id=o.id AND dm.department_id=cc.department_id) AS department_ok,
                        EXISTS(SELECT 1 FROM officer_category_map cm
                               WHERE cm.officer_id=o.id AND cm.category_id=cc.id) AS category_ok
                 FROM complaint_categories cc
                 JOIN officers o ON o.id=?
                 WHERE cc.id=? AND cc.is_active=1 LIMIT 1'
            );
            $validate->execute([$officerId, $categoryId]);
            $candidate = $validate->fetch(PDO::FETCH_ASSOC);
            $pdf=pdf_policy_reviewed($pdo,$categoryId);
            $expectedStage=$pdf===null?null:pdf_policy_stage($pdf,$stageNo);
            if (!$expectedStage || !$candidate ||
                !pdf_policy_role_matches((string)$expectedStage['role_label'],(string)$candidate['designation'])) {
                throw new RuntimeException('Officer title does not match a reviewed PDF stage for this category.');
            }
            $forbidden=officer_stage_forbidden_title((string)$candidate['designation'],$stageNo,$officerId);
            if($forbidden && strtoupper(trim((string)$expectedStage['role_label']))==='COMMR'
               && $stageNo>1 && $officerId!==1
               && pdf_policy_role_matches('COMMR',(string)$candidate['designation'])) $forbidden=false;
            if (!$candidate || !(int)$candidate['is_active']
                || !(int)$candidate['department_ok'] || !(int)$candidate['category_ok']
                || $forbidden) {
                throw new RuntimeException('Officer is not eligible: verify active status, designation, department and category mapping.');
            }
            $pdo->beginTransaction();
            // Do not silently reactivate revoked approval: require an auditable
            // fresh change outside this simple form.
            $existing=$pdo->prepare(
                'SELECT id,revoked_at FROM officer_stage_approvals
                 WHERE category_id=? AND stage_no=? AND officer_id=? FOR UPDATE'
            );
            $existing->execute([$categoryId,$stageNo,$officerId]);
            $previous=$existing->fetch(PDO::FETCH_ASSOC);
            if ($previous) throw new RuntimeException(
                'An approval record already exists (possibly revoked). Review its history before changing it.'
            );
            $save=$pdo->prepare(
                'INSERT INTO officer_stage_approvals
                 (category_id,stage_no,officer_id,approval_reference,approved_by,approved_at)
                 VALUES (?,?,?,?,?,NOW())'
            );
            $save->execute([$categoryId,$stageNo,$officerId,$reference,(int)$user['id']]);
            $pdo->commit();
            audit_log((int)$user['id'],'officer_stage_approved','officer',$officerId,[
                'category_id'=>$categoryId,'stage_no'=>$stageNo,'reference'=>$reference
            ]);
            flash('success','Officer approval recorded. No existing complaints were reassigned.');
        } elseif ($action === 'revoke') {
            $approvalId=(int)($_POST['approval_id'] ?? 0);
            if ($approvalId<1 || mb_strlen($reference)<8) {
                throw new RuntimeException('Select an approval and provide a revocation document reference.');
            }
            $pdo->beginTransaction();
            $revoke=$pdo->prepare(
                'UPDATE officer_stage_approvals SET revoked_at=NOW()
                 WHERE id=? AND revoked_at IS NULL'
            );
            $revoke->execute([$approvalId]);
            if ($revoke->rowCount()!==1) throw new RuntimeException('Approval does not exist or is already revoked.');
            $pdo->commit();
            audit_log((int)$user['id'],'officer_stage_revoked','officer_approval',$approvalId,[
                'reference'=>$reference
            ]);
            flash('success','Approval revoked. Existing assignments remain unchanged; review them separately.');
        } else {
            throw new RuntimeException('Unknown approval action.');
        }
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        flash('danger',APP_ENV==='local'?$exception->getMessage():'Approval update was not completed.');
    }
    redirect('employee/officer-approvals.php');
}

$categories = $pdo->query(
    'SELECT cc.id,cc.category_name,d.department_name
     FROM complaint_categories cc JOIN departments d ON d.id=cc.department_id
     WHERE cc.is_active=1 ORDER BY d.department_name,cc.category_name LIMIT 600'
)->fetchAll(PDO::FETCH_ASSOC);

if ($setupReady) {
    try {
        $approvals=$pdo->query(
            'SELECT a.id,a.category_id,a.stage_no,a.officer_id,a.approval_reference,
                    a.approved_at,a.revoked_at,o.officer_name,o.designation,
                    cc.category_name,d.department_name,u.full_name AS approver
             FROM officer_stage_approvals a
             JOIN officers o ON o.id=a.officer_id
             JOIN complaint_categories cc ON cc.id=a.category_id
             JOIN departments d ON d.id=cc.department_id
             LEFT JOIN users u ON u.id=a.approved_by
             ORDER BY a.approved_at DESC,a.id DESC LIMIT 150'
        )->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $exception) {
        $setupError='Approval registry could not be read.';
        log_message('Officer approvals read failed: '.$exception->getMessage());
    }
}
try {
    $pending = $pdo->query(
        "SELECT c.id,c.reference_no,c.created_at,c.status,cc.category_name,d.department_name,
                w.ward_no
         FROM complaints c
         JOIN complaint_categories cc ON cc.id=c.category_id
         JOIN departments d ON d.id=c.department_id
         JOIN wards w ON w.id=c.ward_id
         WHERE c.is_deleted=0 AND c.assigned_officer_id IS NULL
           AND c.status NOT IN ('Resolved','Closed','Rejected')
         ORDER BY c.created_at DESC,c.id DESC LIMIT 30"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $exception) {
    log_message('Pending assignment queue could not be read: '.$exception->getMessage());
}

include __DIR__.'/../includes/employee-header.php';
?>
<div class="admin-heading">
  <div><span class="admin-eyebrow">Governed routing</span>
    <h1>Officer Stage Approvals</h1>
    <p>Verify official officer authority before assigning any citizen grievance.</p></div>
  <span class="admin-pill"><i class="bi bi-shield-lock"></i> Approval required</span>
</div>
<?php if (!$setupReady): ?>
<div class="alert alert-warning" role="alert">
  <strong>Officer approval registry not installed.</strong>
  Routing remains safely blocked. An administrator must review
  <code>database/officer_stage_approvals.sql</code> and install it in the correct database
  after backup and authorization. No officers are approved automatically.
  <?php if ($setupError): ?><p class="mb-0 mt-2"><?php echo e($setupError); ?></p><?php endif; ?>
</div>
<?php endif; ?>
<div class="row g-3 mb-4">
  <div class="col-lg-5">
    <div class="card p-4 h-100">
      <h2 class="admin-panel-heading mb-2">Document a verified officer</h2>
      <p class="small text-muted">The approving administrator must have written municipal authorization for the specific category, stage and officer. An officer ID or designation alone is insufficient.</p>
      <?php if ($canEdit && $setupReady): ?>
      <form method="post" class="d-grid gap-3">
        <?php echo csrf_field(); ?><input type="hidden" name="action" value="approve">
        <label class="form-label">Department / Category
          <select name="category_id" class="form-select mt-1" required>
            <option value="">Choose complaint category</option>
            <?php foreach($categories as $category): ?>
              <option value="<?php echo (int)$category['id']; ?>"><?php echo e($category['department_name'].' / '.$category['category_name']); ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label class="form-label">Authorized stage
          <select name="stage_no" class="form-select mt-1" required>
            <option value="1">1 — Initial officer</option>
            <option value="2">2 — Second stage</option>
            <option value="3">3 — Third stage</option>
            <option value="4">4 — Stage 4 (some workflows finish here)</option>
            <option value="5">5 — Stage 5 (where PDF specifies)</option>
            <option value="6">6 — Stage 6 (where PDF specifies)</option>
          </select>
        </label>
        <label class="form-label">Verified officer ID
          <input class="form-control mt-1" type="number" min="1" name="officer_id" required placeholder="Existing officer ID">
        </label>
        <label class="form-label">Signed approval reference
          <input class="form-control mt-1" maxlength="255" minlength="8" name="approval_reference" required placeholder="Official approval / order number">
        </label>
        <label class="d-flex align-items-start gap-2 small">
          <input type="checkbox" class="form-check-input mt-1" name="confirm_authority" value="1" required>
          <span>I have verified this officer's official authority for this specific complaint category and escalation stage.</span>
        </label>
        <button class="btn btn-primary" type="submit"><i class="bi bi-shield-check me-2"></i>Record Approval</button>
      </form>
      <?php elseif (!$canEdit): ?>
      <div class="alert alert-info">You can review approvals, but your role does not permit changes.</div>
      <?php endif; ?>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="card p-4 h-100">
      <h2 class="admin-panel-heading mb-2">Existing approval records</h2>
      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead><tr><th>Category / Stage</th><th>Officer</th><th>Approval</th><th>Status</th><th></th></tr></thead>
          <tbody>
            <?php if (!$approvals): ?><tr><td colspan="5" class="text-muted py-4">No approval records are available.</td></tr><?php endif; ?>
            <?php foreach($approvals as $row): ?>
            <tr>
              <td><strong><?php echo e($row['category_name']); ?></strong><br>
                  <small class="text-muted"><?php echo e($row['department_name']); ?> · Stage <?php echo (int)$row['stage_no']; ?></small></td>
              <td><?php echo e($row['officer_name']); ?><br><small><?php echo e($row['designation']); ?> · #<?php echo (int)$row['officer_id']; ?></small></td>
              <td><?php echo e($row['approval_reference']); ?><br>
                  <small class="text-muted"><?php echo e($row['approver'] ?: 'Unverified approver'); ?></small></td>
              <td><?php echo $row['revoked_at'] ? 'Revoked' : 'Active'; ?></td>
              <td>
                <?php if ($canEdit && !$row['revoked_at']): ?>
                <form method="post" class="d-grid gap-1" onsubmit="return confirm('Revoke this approval?');">
                  <?php echo csrf_field(); ?><input type="hidden" name="action" value="revoke">
                  <input type="hidden" name="approval_id" value="<?php echo (int)$row['id']; ?>">
                  <input class="form-control form-control-sm" type="text" name="approval_reference" minlength="8" required placeholder="Revocation reference">
                  <button type="submit" class="btn btn-sm btn-outline-danger">Revoke</button>
                </form>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<div class="card p-4">
  <div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
    <div><h2 class="admin-panel-heading">Complaints awaiting authorized allocation</h2>
      <p class="text-muted small mb-0">New approvals never reassign complaints automatically. Open each case and assign an approved officer using the complaint update form.</p></div>
    <a class="btn btn-outline-primary btn-sm" href="<?php echo e(url('employee/complaints.php')); ?>">Open Complaint Inbox</a>
  </div>
  <div class="table-responsive"><table class="table table-hover align-middle">
    <thead><tr><th>Reference</th><th>Registered</th><th>Department / Category</th><th>Ward</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php if (!$pending): ?><tr><td colspan="6" class="text-muted">No open unassigned complaints found.</td></tr><?php endif; ?>
    <?php foreach($pending as $item): ?>
    <tr><td><?php echo e($item['reference_no']); ?></td><td><?php echo e($item['created_at']); ?></td>
      <td><?php echo e($item['department_name'].' / '.$item['category_name']); ?></td>
      <td><?php echo e($item['ward_no']); ?></td><td><?php echo e($item['status']); ?></td>
      <td><a class="btn btn-sm btn-outline-primary" href="<?php echo e(url('employee/complaint-view.php?id='.(int)$item['id'])); ?>">Review</a></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</div>
<?php include __DIR__.'/../includes/employee-footer.php'; ?>
