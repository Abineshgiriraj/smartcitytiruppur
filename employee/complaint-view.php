<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/officer-authority.php';

$user = require_permission('complaints.view');
$pageTitle = 'Complaint Details';
$complaintId = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

$stmt = db()->prepare(
    "SELECT c.*, d.department_name, cc.category_name, cc.sla_days,
            z.zone_no, w.ward_no, s.street_name,
            o.officer_name, o.mobile_no AS officer_mobile
     FROM complaints c
     JOIN departments d ON d.id = c.department_id
     JOIN complaint_categories cc ON cc.id = c.category_id
     JOIN zones z ON z.id = c.zone_id
     JOIN wards w ON w.id = c.ward_id
     JOIN streets s ON s.id = c.street_id
     LEFT JOIN officers o ON o.id = c.assigned_officer_id
     WHERE c.id = ? AND c.is_deleted = 0 LIMIT 1"
);
$stmt->execute([$complaintId]);
$complaint = $stmt->fetch();

if (!$complaint) {
    http_response_code(404);
    exit('Complaint not found.');
}

if (!can_manage_all_complaints($user)) {
    $allowed = true;

    if ($user['role'] === 'Department Head' && (int)$user['department_id'] !== (int)$complaint['department_id']) $allowed = false;
    if ($user['role'] === 'Zone Officer' && (int)$user['zone_id'] !== (int)$complaint['zone_id']) $allowed = false;
    if ($user['role'] === 'Ward Officer' && (int)$user['ward_id'] !== (int)$complaint['ward_id']) $allowed = false;

    if ($user['role'] === 'Field Officer') {
        $officerStmt = db()->prepare('SELECT id FROM officers WHERE user_id = ? LIMIT 1');
        $officerStmt->execute([(int)$user['id']]);
        $allowed = (int)$officerStmt->fetchColumn() === (int)$complaint['assigned_officer_id'];
    }

    if (!$allowed) {
        http_response_code(403);
        exit('You do not have access to this complaint.');
    }
}

$canUpdate = can_access_permission($user, 'complaints.update');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_permission('complaints.update');
    if (!$canUpdate) {
        http_response_code(403);
        exit('This account has read-only access.');
    }

    verify_csrf();

    $newStatus = trim($_POST['status'] ?? '');
    $remarks = trim($_POST['remarks'] ?? '');
    $officerId = (int)($_POST['assigned_officer_id'] ?? 0);
    $pdo = db();

    try {
        $pdo->beginTransaction();
        // Lock the current record, not stale values from the earlier page load.
        $lock=$pdo->prepare('SELECT * FROM complaints WHERE id=? AND is_deleted=0 FOR UPDATE');
        $lock->execute([$complaintId]);
        $fresh=$lock->fetch(PDO::FETCH_ASSOC);
        if (!$fresh) throw new RuntimeException('Complaint is no longer available.');
        $oldStatus=$fresh['status'];
        $currentOfficer=(int)($fresh['assigned_officer_id'] ?? 0);

        if (!in_array($newStatus, complaint_statuses(), true)) {
            throw new RuntimeException('Invalid complaint status.');
        }

        if ($officerId !== $currentOfficer) {
            if ($officerId > 0) {
                $stage=complaint_last_stage($pdo,$complaintId);
                if (!valid_complaint_routing_context(
                    $pdo,(int)$fresh['department_id'],(int)$fresh['category_id'],
                    (int)$fresh['zone_id'],(int)$fresh['ward_id']
                ) || !officer_stage_authorized(
                    $pdo,(int)$fresh['category_id'],$stage,$officerId,
                    (int)$fresh['department_id'],(int)$fresh['ward_id']
                )) throw new RuntimeException('New officer is not approved for this category and stage.');
            }
            $pdo->prepare(
                'UPDATE complaint_assignments SET is_current=0,unassigned_at=NOW()
                 WHERE complaint_id=? AND is_current=1'
            )->execute([$complaintId]);
            if ($officerId > 0) {
                $pdo->prepare(
                    'INSERT INTO complaint_assignments
                     (complaint_id,officer_id,assigned_by,assigned_at,is_current)
                     VALUES (?,?,?,NOW(),1)'
                )->execute([$complaintId,$officerId,(int)$user['id']]);
            }
        }

        $resolvedAt = in_array($newStatus, ['Resolved','Closed'], true) ? date('Y-m-d H:i:s') : null;
        $closedAt = $newStatus === 'Closed' ? date('Y-m-d H:i:s') : null;

        $pdo->prepare(
            'UPDATE complaints
             SET status = ?, assigned_officer_id = NULLIF(?, 0), resolved_at = ?, closed_at = ?, updated_at = NOW()
             WHERE id = ?'
        )->execute([$newStatus, $officerId, $resolvedAt, $closedAt, $complaintId]);

        $pdo->prepare(
            'INSERT INTO complaint_status_history
             (complaint_id, old_status, new_status, remarks, changed_by)
             VALUES (?, ?, ?, ?, ?)'
        )->execute([$complaintId, $oldStatus, $newStatus, $remarks !== '' ? $remarks : null, (int)$user['id']]);

        $pdo->commit();
        audit_log((int)$user['id'], 'complaint_updated', 'complaint', $complaintId, ['old_status' => $oldStatus, 'new_status' => $newStatus]);
        flash('success', 'Complaint updated successfully.');
        redirect('employee/complaint-view.php?id=' . $complaintId);
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        flash('danger', APP_ENV === 'local' ? $exception->getMessage() : 'Unable to update complaint.');
        redirect('employee/complaint-view.php?id=' . $complaintId);
    }
}

// Show only approved new recipients. Keep the current officer as a separate
// option so a status-only edit does not accidentally unassign the complaint.
$officers=[];
try {
    $stage=complaint_last_stage(db(),$complaintId);
    $approved=db()->prepare(
        'SELECT DISTINCT o.id,o.officer_name FROM officer_stage_approvals a
         JOIN officers o ON o.id=a.officer_id AND o.is_active=1
         WHERE a.category_id=? AND a.stage_no=? AND a.revoked_at IS NULL
         ORDER BY o.officer_name LIMIT 500'
    );
    $approved->execute([(int)$complaint['category_id'],$stage]);
    foreach($approved->fetchAll(PDO::FETCH_ASSOC) as $candidate) {
        if(officer_stage_authorized(
            db(),(int)$complaint['category_id'],$stage,(int)$candidate['id'],
            (int)$complaint['department_id'],(int)$complaint['ward_id']
        )) $officers[]=$candidate;
    }
} catch(Throwable $error) { log_message('Approved recipient list unavailable: '.$error->getMessage()); }
$currentId=(int)($complaint['assigned_officer_id'] ?? 0);
if($currentId>0 && !in_array($currentId,array_map(static fn($o)=>(int)$o['id'],$officers),true)) {
    $officers[]=['id'=>$currentId,'officer_name'=>
        (string)($complaint['officer_name'] ?: 'Current officer').' (current, unverified)'];
}

$historyStmt = db()->prepare(
    'SELECT h.*, u.full_name
     FROM complaint_status_history h
     LEFT JOIN users u ON u.id = h.changed_by
     WHERE h.complaint_id = ?
     ORDER BY h.changed_at ASC, h.id ASC'
);
$historyStmt->execute([$complaintId]);
$history = $historyStmt->fetchAll();

$attachmentStmt = db()->prepare('SELECT id, original_name, mime_type, file_size FROM complaint_attachments WHERE complaint_id = ? ORDER BY id');
$attachmentStmt->execute([$complaintId]);
$attachments = $attachmentStmt->fetchAll();

include __DIR__ . '/../includes/employee-header.php';
?>
<div class="complaint-reference-page">
    <div class="complaint-reference-brand">
        <img src="<?php echo e(url('assets/images/trippurlogo.png')); ?>" alt="Tiruppur Corporation">
        <div>TIRUPPUR CITY<br><strong>MUNICIPAL CORPORATION</strong></div>
        <img src="<?php echo e(url('assets/images/tamilnadulogo.png')); ?>" alt="Government of Tamil Nadu">
    </div>

    <div class="complaint-reference-top">
        <section class="complaint-reference-panel">
            <h2>User Information</h2>
            <dl class="complaint-reference-details">
                <dt>Name</dt><dd><?php echo e($complaint['citizen_name']); ?></dd>
                <dt>Phone</dt><dd><?php echo e($complaint['mobile_no']); ?></dd>
                <dt>Email ID</dt><dd><?php echo e($complaint['email'] ?: '-'); ?></dd>
                <dt>Address</dt><dd><?php echo nl2br(e($complaint['communication_address'])); ?></dd>
                <dt>Source</dt><dd><?php echo e($complaint['source']); ?></dd>
            </dl>
        </section>
        <section class="complaint-reference-panel">
            <h2>Grievance Information</h2>
            <dl class="complaint-reference-details">
                <dt>Grievance ID</dt><dd><?php echo e($complaint['reference_no']); ?></dd>
                <dt>Assessment No</dt><dd><?php echo e($complaint['assessment_no'] ?: '-'); ?></dd>
                <dt>Date &amp; Time</dt><dd><?php echo e(date('d-M-Y H:i', strtotime($complaint['created_at']))); ?></dd>
                <dt>Complaint Status</dt><dd><?php echo e($complaint['status']); ?></dd>
                <dt>Department</dt><dd><?php echo e($complaint['department_name']); ?></dd>
                <dt>Category</dt><dd><?php echo e($complaint['category_name']); ?></dd>
                <dt>Zone &amp; Ward</dt><dd>ZONE - <?php echo e($complaint['zone_no']); ?> - <?php echo e($complaint['ward_no']); ?></dd>
                <dt>Closed Date</dt><dd><?php echo e($complaint['closed_at'] ? date('d-m-Y H:i', strtotime($complaint['closed_at'])) : '-'); ?></dd>
                <dt>Remarks</dt><dd><?php echo e($history ? ($history[count($history)-1]['remarks'] ?: '-') : '-'); ?></dd>
            </dl>
        </section>
        <section class="complaint-reference-panel">
            <h2>Grievance Allotted Employee</h2>
            <dl class="complaint-reference-details">
                <dt>Name</dt><dd><?php echo e($complaint['officer_name'] ?: 'Pending authorized officer allocation'); ?></dd>
                <dt>Phone Number</dt><dd><?php echo e($complaint['officer_mobile'] ?: '-'); ?></dd>
                <dt>Email ID</dt><dd>-</dd>
            </dl>
        </section>
    </div>

    <div class="complaint-reference-bottom">
        <section class="complaint-reference-box">
            <h2>Description</h2>
            <div class="complaint-description"><?php echo nl2br(e($complaint['description_english'])); ?></div>
            <h3>Description (in Tamil)</h3>
            <div class="complaint-description"><?php echo $complaint['description_tamil'] ? nl2br(e($complaint['description_tamil'])) : '-'; ?></div>
            <div class="complaint-reference-divider"></div>
            <h2>User Attachments</h2>
            <?php if (!$attachments): ?><div class="complaint-no-attachment">No Attachments Found</div><?php endif; ?>
            <?php foreach ($attachments as $attachment): ?>
                <a class="complaint-attachment-link" href="<?php echo e(url('employee/download-attachment.php?id=' . (int)$attachment['id'])); ?>"><?php echo e($attachment['original_name']); ?></a>
            <?php endforeach; ?>
        </section>

        <aside class="complaint-reference-box complaint-activity-box">
            <h2>Activity Logs</h2>
            <?php if (!$history): ?><p class="text-muted">No activity recorded.</p><?php endif; ?>
            <?php foreach (array_reverse($history) as $item): ?>
                <div class="complaint-activity-item">
                    <strong><?php echo e($item['new_status']); ?></strong>
                    <div><?php echo e($item['full_name'] ?: ($complaint['officer_name'] ?: 'System')); ?></div>
                    <?php if ($item['remarks']): ?><div class="mt-2">Remarks:<br><strong><?php echo e($item['remarks']); ?></strong></div><?php endif; ?>
                    <div><?php echo e(date('d-M-Y', strtotime($item['changed_at']))); ?></div>
                </div>
            <?php endforeach; ?>
            <h2 class="mt-3">Closed Attachments</h2>
            <div class="complaint-no-attachment">No Attachments Found</div>
        </aside>
    </div>

    <div class="complaint-reference-actions">
        <?php if ($canUpdate): ?>
            <h2>Update Complaint</h2>
            <div class="alert alert-warning small">New officer assignments require verified category/stage approval.
              Existing assignments may remain unchanged for status-only updates. No approval is generated automatically.</div>
            <form method="post" class="row g-3">
                <?php echo csrf_field(); ?><input type="hidden" name="id" value="<?php echo $complaintId; ?>">
                <div class="col-md-4"><label class="form-label">Assigned Officer</label><select class="form-select" name="assigned_officer_id"><option value="">Unassigned</option><?php foreach ($officers as $officer): ?><option value="<?php echo (int)$officer['id']; ?>" <?php echo (int)$complaint['assigned_officer_id'] === (int)$officer['id'] ? 'selected' : ''; ?>><?php echo e($officer['officer_name']); ?></option><?php endforeach; ?></select></div>
                <div class="col-md-3"><label class="form-label">Status</label><select class="form-select" name="status"><?php foreach (complaint_statuses() as $statusItem): ?><option <?php echo $complaint['status'] === $statusItem ? 'selected' : ''; ?>><?php echo e($statusItem); ?></option><?php endforeach; ?></select></div>
                <div class="col-md-3"><label class="form-label">Remarks</label><input class="form-control" name="remarks"></div>
                <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100" type="submit">Save Update</button></div>
            </form>
        <?php else: ?><div class="alert alert-info mb-0">This account has read-only access.</div><?php endif; ?>
    </div>
</div>
<?php include __DIR__ . '/../includes/employee-footer.php'; ?>
