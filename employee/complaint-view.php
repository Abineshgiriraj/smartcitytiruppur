<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/officer-authority.php';

$user = require_login();
$isOfficerPortal = in_array($user['role'], ['Field Officer','Officer'], true);
$officerPortalId = 0;
if ($isOfficerPortal) {
    $auth = require_officer();
    $officerPortalId = (int)$auth['officer']['id'];
} else {
    $user = require_permission('complaints.view');
}
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

    if ($isOfficerPortal) {
        $allowed = $officerPortalId > 0 && $officerPortalId === (int)$complaint['assigned_officer_id'];
    }

    if (!$allowed) {
        http_response_code(403);
        exit('You do not have access to this complaint.');
    }
}

$canUpdate = $isOfficerPortal || can_access_permission($user, 'complaints.update');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$isOfficerPortal) require_permission('complaints.update');
    if (!$canUpdate) {
        http_response_code(403);
        exit('This account has read-only access.');
    }

    verify_csrf();

    if ($isOfficerPortal) {
        $newStatus=trim((string)($_POST['status']??''));
        $remarks=trim((string)($_POST['remarks']??''));
        $allowedTransitions=[
          'Assigned'=>['Assigned','In Progress'],
          'In Progress'=>['In Progress','Resolved','Closed'],
          'Reopened'=>['Reopened','In Progress','Resolved','Closed'],
          'Resolved'=>['Resolved','Closed']
        ];
        $pdo=db();
        try {
            if(($_POST['action']??'')!=='officer_status_update') {
                throw new RuntimeException('Only status updates are available to field officers.');
            }
            if(mb_strlen($remarks)<5) {
                throw new RuntimeException('Please enter at least five characters explaining the action taken.');
            }
            $pdo->beginTransaction();
            $lock=$pdo->prepare(
                'SELECT assigned_officer_id,status,is_deleted,resolved_at,closed_at FROM complaints
                 WHERE id=? FOR UPDATE'
            );
            $lock->execute([$complaintId]);
            $fresh=$lock->fetch(PDO::FETCH_ASSOC);
            if(!$fresh || (int)$fresh['is_deleted']!==0 ||
               (int)$fresh['assigned_officer_id']!==$officerPortalId) {
                throw new RuntimeException('This complaint is no longer assigned to your account.');
            }
            $old=(string)$fresh['status'];
            if(!in_array($newStatus,$allowedTransitions[$old]??[],true)) {
                throw new RuntimeException('This status transition is not allowed for your complaint.');
            }
            $resolvedAt=$fresh['resolved_at'];
            $closedAt=$fresh['closed_at'];
            if($newStatus==='Resolved') $resolvedAt=date('Y-m-d H:i:s');
            if($newStatus==='Closed') {
                $closedAt=date('Y-m-d H:i:s');
                if(!$resolvedAt)$resolvedAt=$closedAt;
            }
            $update=$pdo->prepare(
                'UPDATE complaints SET status=?,resolved_at=?,closed_at=?,updated_at=NOW()
                 WHERE id=? AND assigned_officer_id=? AND status=? AND is_deleted=0'
            );
            $update->execute([$newStatus,$resolvedAt,$closedAt,$complaintId,$officerPortalId,$old]);
            $pdo->prepare(
                'INSERT INTO complaint_status_history
                 (complaint_id,old_status,new_status,remarks,changed_by)
                 VALUES (?,?,?,?,?)'
            )->execute([$complaintId,$old,$newStatus,$remarks,(int)$user['id']]);
            $pdo->commit();
            audit_log((int)$user['id'],'officer_update_complaint','complaint',$complaintId,
                ['old_status'=>$old,'new_status'=>$newStatus]);
            flash('success','Complaint status updated to '.$newStatus.'.');
        } catch(Throwable $exception) {
            if($pdo->inTransaction())$pdo->rollBack();
            flash('danger',APP_ENV==='local'?$exception->getMessage():'Unable to update complaint.');
        }
        redirect('employee/complaint-view.php?id='.$complaintId);
    }

    // Reapply the verified initial allocation service to an existing
    // Registered/unassigned complaint. Only authorized complaint admins can
    // execute this, and it never creates/guesses officer approvals.
    if (($_POST['action'] ?? '') === 'retry_initial_allocation') {
        if (!can_manage_all_complaints($user)) {
            http_response_code(403);
            exit('Only authorized complaint administrators can retry allocation.');
        }
        $pdo = db();
        try {
            $pdo->beginTransaction();
            $lock = $pdo->prepare(
                'SELECT id,department_id,category_id,ward_id,zone_id,status,assigned_officer_id
                 FROM complaints WHERE id=? AND is_deleted=0 FOR UPDATE'
            );
            $lock->execute([$complaintId]);
            $fresh = $lock->fetch(PDO::FETCH_ASSOC);
            if (!$fresh) throw new RuntimeException('Complaint was not found.');
            if ($fresh['status'] !== 'Registered' || $fresh['assigned_officer_id'] !== null) {
                throw new RuntimeException('Only registered, unassigned complaints can be retried.');
            }
            $active = $pdo->prepare(
                'SELECT id FROM complaint_assignments
                 WHERE complaint_id=? AND is_current=1 LIMIT 1 FOR UPDATE'
            );
            $active->execute([$complaintId]);
            if ($active->fetchColumn() !== false) {
                throw new RuntimeException('Active assignment exists; review history before retrying.');
            }
            $officerId = assign_officer_for_complaint(
                $complaintId,
                (int)$fresh['department_id'],
                (int)$fresh['category_id'],
                (int)$fresh['ward_id'],
                (int)$fresh['zone_id']
            );
            if ($officerId === null) {
                $officerId=assign_complaint_to_intake(
                    $complaintId,(int)$fresh['department_id'],(int)$fresh['category_id'],
                    (int)$fresh['ward_id'],(int)$fresh['zone_id']
                );
            }
            if ($officerId === null) {
                throw new RuntimeException(
                    'No eligible department intake officer or configured central intake officer is available.'
                );
            }
            $pdo->commit();
            audit_log(
                (int)$user['id'], 'verified_initial_allocation_retried',
                'complaint', $complaintId, ['officer_id'=>$officerId]
            );
            flash('success', 'Complaint assigned to an accountable officer (specialist or intake); check routing history.');
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            flash(
                'warning',
                APP_ENV === 'local' ? $exception->getMessage()
                    : 'Allocation was not completed. Verify the category, department, ward and officer designation mappings.'
            );
        }
        redirect('employee/complaint-view.php?id='.$complaintId);
    }

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
                $titleLookup = $pdo->prepare('SELECT designation FROM officers WHERE id=? AND is_active=1 LIMIT 1');
                $titleLookup->execute([$officerId]);
                $desig = $titleLookup->fetchColumn();
                if ($desig === false || officer_stage_forbidden_title((string)$desig, $stage, $officerId)) {
                    throw new RuntimeException('Selected officer has an unauthorized or forbidden designation for this complaint.');
                }
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

if (!$isOfficerPortal) {
// Show eligible recipients. Keep the current officer as a separate
// option so a status-only edit does not accidentally unassign the complaint.
$officers=[];
try {
    $stage=complaint_last_stage(db(),$complaintId);
    foreach(pdf_routing_candidates(db(),(int)$complaint['category_id'],$stage,
        (int)$complaint['department_id'],(int)$complaint['ward_id']) as $candidate) {
        $officers[]=['id'=>$candidate['id'],'officer_name'=>$candidate['officer_name'].' ('.$candidate['designation'].')'];
    }
    if (empty($officers)) {
        $pol = pdf_policy_routing_ready(db(), (int)$complaint['category_id']);
        $step = $pol ? pdf_policy_stage($pol, $stage) : null;
        if ($step && !empty($step['role_label'])) {
            foreach (pdf_department_role_candidates(db(), (int)$complaint['department_id'], (string)$step['role_label'], (int)$complaint['ward_id']) as $cand) {
                $officers[] = ['id' => $cand['id'], 'officer_name' => $cand['officer_name'] . ' (' . $cand['designation'] . ')'];
            }
        }
        if (empty($officers)) {
            foreach (pdf_department_ward_fallback_candidates(db(), (int)$complaint['department_id'], (int)$complaint['ward_id']) as $cand) {
                $officers[] = ['id' => $cand['id'], 'officer_name' => $cand['officer_name'] . ' (' . $cand['designation'] . ')'];
            }
        }
    }
} catch(Throwable $error) { log_message('Mapped recipient list unavailable: '.$error->getMessage()); }
$currentId=(int)($complaint['assigned_officer_id'] ?? 0);
if($currentId>0 && !in_array($currentId,array_map(static fn($o)=>(int)$o['id'],$officers),true)) {
    $officers[]=['id'=>$currentId,'officer_name'=>
        (string)($complaint['officer_name'] ?: 'Current officer').' (current)'];
}

// Intake assignments must be visible to complaint administrators.
$intakeCheck=db()->prepare(
    "SELECT COUNT(*) FROM complaint_status_history
     WHERE complaint_id=? AND remarks LIKE '%specialist routing review required%'
       AND new_status='Assigned'"
);
$intakeCheck->execute([$complaintId]);
$needsRoutingReview=(int)$intakeCheck->fetchColumn()>0
    && !officer_stage_authorized(
        db(),(int)$complaint['category_id'],complaint_last_stage(db(),$complaintId),
        (int)($complaint['assigned_officer_id']??0),(int)$complaint['department_id'],
        (int)$complaint['ward_id']
    );

} else {
    $officers=[];
    $needsRoutingReview=false;
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
                <dt>Name</dt><dd><?php echo e($complaint['officer_name'] ?: 'Pending officer allocation'); ?></dd>
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

    <?php if ($needsRoutingReview): ?>
        <div class="alert alert-warning">
            <strong>Department / Central Intake — Specialist Routing Required.</strong>
            This employee is the accountable intake recipient, not a verified PDF Stage 1 specialist.
            An administrator should correct the mapping and reroute the complaint.
        </div>
    <?php endif; ?>
    <div class="complaint-reference-actions">
        <?php if ($canUpdate && can_manage_all_complaints($user)
            && $complaint['status'] === 'Registered'
            && $complaint['assigned_officer_id'] === null): ?>
            <div class="alert alert-info">
                This complaint has no assignee yet. Retry will first select a mapped PDF specialist and then an intake officer if specialist routing is unavailable.
            </div>
            <form method="post" class="mb-3">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" value="<?php echo $complaintId; ?>">
                <input type="hidden" name="action" value="retry_initial_allocation">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-clockwise me-1"></i>Retry automatic assignment
                </button>
            </form>
        <?php endif; ?>
        <?php if ($isOfficerPortal && $canUpdate): ?>
          <h2><i class="bi bi-check2-square me-2"></i>Update My Complaint</h2>
          <?php if($complaint['status']==='Closed'): ?>
            <div class="alert alert-secondary">This complaint is closed. No further officer updates are allowed.</div>
          <?php else: ?>
            <?php
              $officerActions=match($complaint['status']){
                'Assigned'=>[['In Progress','Start Work'],['Assigned','Add Work Note']],
                'In Progress'=>[['In Progress','Add Work Note'],['Resolved','Mark Resolved'],['Closed','Close Complaint']],
                'Reopened'=>[['In Progress','Resume Work'],['Reopened','Add Work Note'],['Resolved','Mark Resolved'],['Closed','Close Complaint']],
                'Resolved'=>[['Closed','Close Complaint'],['Resolved','Add Work Note']],
                default=>[]
              };
            ?>
            <form method="post" class="row g-3">
              <?php echo csrf_field(); ?>
              <input type="hidden" name="id" value="<?php echo $complaintId; ?>">
              <input type="hidden" name="action" value="officer_status_update">
              <div class="col-md-4">
                <label class="form-label fw-bold">Choose Action</label>
                <select class="form-select" name="status" required>
                  <?php foreach($officerActions as $item): ?>
                    <option value="<?php echo e($item[0]); ?>"><?php echo e($item[1]); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">Officer Remarks</label>
                <textarea class="form-control" name="remarks" rows="3" minlength="5"
                  placeholder="Explain the work performed (at least 5 characters)" required></textarea>
              </div>
              <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Save Update</button>
              </div>
            </form>
          <?php endif; ?>
        <?php elseif ($canUpdate): ?>
            <h2>Update Complaint</h2>
            <div class="alert alert-warning small">New officer assignments require valid department/category/ward maps and the exact PDF stage role.
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
