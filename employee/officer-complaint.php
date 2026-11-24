<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

$auth = require_officer();
$user = $auth['user'];
$officer = $auth['officer'];
$officerId = (int)$officer['id'];

$complaintId = (int)($_GET['id'] ?? 0);
if ($complaintId <= 0) {
    flash('danger', 'Invalid complaint ID.');
    redirect('employee/my-complaints.php');
}

// 1. Fetch complaint strictly scoped to this officer
$stmt = db()->prepare('
    SELECT c.*, d.department_name, cc.category_name, cc.sla_days,
           z.zone_no, w.ward_no, s.street_name,
           o.officer_name, o.designation, o.mobile_no as officer_mobile
    FROM complaints c
    JOIN departments d ON d.id = c.department_id
    JOIN complaint_categories cc ON cc.id = c.category_id
    JOIN zones z ON z.id = c.zone_id
    JOIN wards w ON w.id = c.ward_id
    LEFT JOIN streets s ON s.id = c.street_id
    LEFT JOIN officers o ON o.id = c.assigned_officer_id
    WHERE c.id = ? AND c.is_deleted = 0
    LIMIT 1
');
$stmt->execute([$complaintId]);
$complaint = $stmt->fetch();

if (!$complaint) {
    http_response_code(404);
    $errorMessage = 'Complaint #' . $complaintId . ' was not found.';
    include __DIR__ . '/../employee/officer-access-error.php';
    exit;
}

// Horizontal Privilege Isolation Check
if ((int)$complaint['assigned_officer_id'] !== $officerId) {
    http_response_code(403);
    $errorMessage = 'Access Denied: Complaint ' . htmlspecialchars($complaint['reference_no']) . ' is assigned to another officer. You are only authorized to manage complaints assigned to your officer account.';
    include __DIR__ . '/../employee/officer-access-error.php';
    exit;
}

$pageTitle = 'Complaint ' . $complaint['reference_no'];

// 2. Handle POST Status Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = trim($_POST['action'] ?? '');

    try {
        if ($action === 'update_status') {
            $newStatus = trim($_POST['status'] ?? '');
            $remarks = trim($_POST['remarks'] ?? '');
            // Escalation or reassignment may happen after the page was opened.
            // Recheck ownership and status under a row lock on EVERY write.
            db()->beginTransaction();
            $freshStmt = db()->prepare(
                'SELECT assigned_officer_id,status,is_deleted,resolved_at,closed_at
                 FROM complaints WHERE id=? FOR UPDATE'
            );
            $freshStmt->execute([$complaintId]);
            $fresh = $freshStmt->fetch(PDO::FETCH_ASSOC);
            if (!$fresh || (int)$fresh['is_deleted'] !== 0
                || (int)$fresh['assigned_officer_id'] !== $officerId) {
                throw new RuntimeException('This complaint is no longer assigned to you. Refresh your dashboard.');
            }
            $currentStatus = (string)$fresh['status'];

            if ($currentStatus === 'Closed') {
                throw new RuntimeException('This complaint is closed and cannot be modified.');
            }

            // Valid transitions for field officers
            $allowedTransitions = [
                'Assigned' => ['Assigned', 'In Progress'],
                'In Progress' => ['In Progress', 'Resolved', 'Closed'],
                'Reopened' => ['Reopened', 'In Progress', 'Resolved', 'Closed'],
                'Resolved' => ['Resolved', 'Closed'],
            ];

            $permittedNext = $allowedTransitions[$currentStatus] ?? [];
            if (!in_array($newStatus, $permittedNext, true)) {
                throw new RuntimeException("Invalid status transition from {$currentStatus} to {$newStatus}.");
            }

            if (in_array($newStatus, ['Resolved', 'Closed'], true) && strlen($remarks) < 5) {
                throw new RuntimeException('Please provide detailed action remarks (minimum 5 characters) explaining how this grievance was resolved.');
            }

            if ($remarks === '' && $newStatus === $currentStatus) {
                throw new RuntimeException('Please enter remarks to add a progress note.');
            }

            // Handle optional proof attachment
            if (!empty($_FILES['proof_file']['name']) && $_FILES['proof_file']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['proof_file'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowedExts = ['jpg', 'jpeg', 'png', 'pdf', 'webp'];
                if (!in_array($ext, $allowedExts, true)) {
                    throw new RuntimeException('Invalid proof attachment type. Allowed: JPG, PNG, PDF, WEBP.');
                }
                if ($file['size'] > 5 * 1024 * 1024) {
                    throw new RuntimeException('Proof attachment exceeds maximum allowed size (5MB).');
                }

                $uploadDir = __DIR__ . '/../storage/uploads/';
                if (!is_dir($uploadDir)) {
                    @mkdir($uploadDir, 0777, true);
                }
                $storedName = 'proof_' . $complaintId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $targetPath = $uploadDir . $storedName;

                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    db()->prepare('
                        INSERT INTO complaint_attachments (complaint_id, file_path, original_name, mime_type, file_size, created_at)
                        VALUES (?, ?, ?, ?, ?, NOW())
                    ')->execute([
                        $complaintId,
                        'storage/uploads/' . $storedName,
                        $file['name'],
                        $file['type'] ?: 'application/octet-stream',
                        $file['size']
                    ]);
                }
            }

            $resolvedAt = ($newStatus === 'Resolved') ? date('Y-m-d H:i:s') : $fresh['resolved_at'];
            $closedAt = ($newStatus === 'Closed') ? date('Y-m-d H:i:s') : $fresh['closed_at'];
            if ($newStatus === 'Closed' && $resolvedAt === null) {
                $resolvedAt = date('Y-m-d H:i:s');
            }
            $updateStmt=db()->prepare(
                'UPDATE complaints SET status=?,resolved_at=?,closed_at=?,updated_at=NOW()
                 WHERE id=? AND assigned_officer_id=? AND status=? AND is_deleted=0'
            );
            $updateStmt->execute([$newStatus,$resolvedAt,$closedAt,$complaintId,$officerId,$currentStatus]);
            if ($updateStmt->rowCount() !== 1 && $newStatus !== $currentStatus) {
                throw new RuntimeException('Complaint status changed while updating. Please refresh.');
            }

            db()->prepare('
                INSERT INTO complaint_status_history (complaint_id, old_status, new_status, remarks, changed_by, changed_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ')->execute([
                $complaintId,
                $currentStatus,
                $newStatus,
                $remarks !== '' ? $remarks : 'Status updated by officer',
                (int)$user['id']
            ]);

            db()->commit();

            audit_log((int)$user['id'], 'officer_update_complaint', 'complaint', $complaintId);
            flash('success', 'Complaint ' . $complaint['reference_no'] . ' successfully updated to ' . $newStatus . '.');
            redirect('employee/officer-complaint.php?id=' . $complaintId);
        }
    } catch (Throwable $e) {
        if (db()->inTransaction()) {
            db()->rollBack();
        }
        flash('danger', $e->getMessage());
    }
}

// 3. Fetch History, Attachments, Reopen History, Feedback
$historyStmt = db()->prepare('
    SELECT h.*, u.full_name as user_name, u.role as user_role
    FROM complaint_status_history h
    LEFT JOIN users u ON u.id = h.changed_by
    WHERE h.complaint_id = ?
    ORDER BY h.changed_at ASC, h.id ASC
');
$historyStmt->execute([$complaintId]);
$history = $historyStmt->fetchAll();

$attachmentStmt = db()->prepare('
    SELECT id, original_name, file_path, mime_type, file_size, created_at
    FROM complaint_attachments
    WHERE complaint_id = ?
    ORDER BY id ASC
');
$attachmentStmt->execute([$complaintId]);
$attachments = $attachmentStmt->fetchAll();

$reopenStmt = db()->prepare('
    SELECT id, reopened_by_mobile, reason, created_at
    FROM complaint_reopen_history
    WHERE complaint_id = ?
    ORDER BY id DESC
');
$reopenStmt->execute([$complaintId]);
$reopenHistory = $reopenStmt->fetchAll();

$feedbackStmt = db()->prepare('
    SELECT id, name, mobile_no, rating, feedback_text, created_at
    FROM feedback
    WHERE complaint_id = ?
    ORDER BY id DESC
');
$feedbackStmt->execute([$complaintId]);
$feedbacks = $feedbackStmt->fetchAll();

include __DIR__ . '/../includes/employee-header.php';
?>

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="<?php echo e(url('employee/officer-dashboard.php')); ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(url('employee/my-complaints.php')); ?>">My Complaints</a></li>
            <li class="breadcrumb-item active"><?php echo e($complaint['reference_no']); ?></li>
        </ol>
    </nav>
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
        <h1 class="h3 fw-bold mb-0">
            Complaint Details: <span class="text-primary"><?php echo e($complaint['reference_no']); ?></span>
        </h1>
        <a href="<?php echo e(url('employee/my-complaints.php')); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to My Complaints
        </a>
    </div>
</div>

<!-- Header Status Bar -->
<?php
    $isOverdue = false;
    $dueText = 'SLA Deadline: ' . ($complaint['due_at'] ? date('d-M-Y H:i', strtotime($complaint['due_at'])) : 'Standard');
    if ($complaint['due_at']) {
        $dueTime = strtotime($complaint['due_at']);
        $isOverdue = time() > $dueTime && in_array($complaint['status'], ['Assigned', 'In Progress', 'Reopened'], true);
        if ($isOverdue) {
            $diffHours = round((time() - $dueTime) / 3600);
            $dueText = '<span class="text-danger fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i>OVERDUE BY ' . $diffHours . ' HOURS (Due was: ' . date('d-M-Y H:i', $dueTime) . ')</span>';
        } else {
            $diffHours = round(($dueTime - time()) / 3600);
            $dueText = '<span class="text-success fw-bold"><i class="bi bi-clock-history me-1"></i>Within SLA: ' . max(1, $diffHours) . ' hours remaining (Due: ' . date('d-M-Y H:i', $dueTime) . ')</span>';
        }
    }
    $statusBadgeClass = match($complaint['status']) {
        'Assigned' => 'bg-warning text-dark',
        'In Progress' => 'bg-info text-dark',
        'Reopened' => 'bg-danger',
        'Resolved' => 'bg-success',
        'Closed' => 'bg-secondary',
        default => 'bg-secondary'
    };
?>
<div class="card content-card border-0 shadow-sm mb-4 <?php echo $isOverdue ? 'border-start border-4 border-danger' : ''; ?>">
    <div class="card-body py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <span class="badge <?php echo $statusBadgeClass; ?> fs-6 px-3 py-2"><?php echo e($complaint['status']); ?></span>
            <span class="text-muted small">Registered: <strong><?php echo date('d-M-Y H:i', strtotime($complaint['created_at'])); ?></strong></span>
            <?php if ($complaint['reopen_count'] > 0): ?>
                <span class="badge bg-danger px-2 py-1"><i class="bi bi-arrow-repeat me-1"></i>Reopened &times;<?php echo (int)$complaint['reopen_count']; ?></span>
            <?php endif; ?>
        </div>
        <div class="small">
            <?php echo $dueText; ?>
        </div>
    </div>
</div>

<!-- Reopen Warning Alert if Reopened -->
<?php if ($complaint['status'] === 'Reopened' || !empty($reopenHistory)): ?>
    <div class="card content-card border-danger shadow-sm mb-4">
        <div class="card-header bg-danger text-white py-2 fw-bold">
            <i class="bi bi-arrow-repeat me-2"></i>Citizen Reopen Explanation &amp; History
        </div>
        <div class="card-body p-3">
            <?php if ($complaint['status'] === 'Reopened'): ?>
                <div class="alert alert-warning py-2 px-3 mb-3">
                    <strong><i class="bi bi-info-circle me-1"></i>Action Required:</strong> The citizen was unsatisfied with the earlier resolution and reopened this complaint. Please contact the citizen or carry out field verification.
                </div>
            <?php endif; ?>
            <ul class="list-group list-group-flush">
                <?php foreach ($reopenHistory as $rh): ?>
                    <li class="list-group-item px-0 py-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-danger-subtle text-danger fw-bold">Reopen Request</span>
                            <small class="text-muted"><?php echo date('d-M-Y H:i', strtotime($rh['created_at'])); ?></small>
                        </div>
                        <div class="fw-semibold text-danger"><?php echo e($rh['reason']); ?></div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Left Column: Details & Attachments & Feedback -->
    <div class="col-lg-7">
        <!-- Citizen Information -->
        <div class="card content-card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0"><i class="bi bi-person text-primary me-2"></i>Citizen Contact Details</h5>
            </div>
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="text-muted small fw-semibold">Citizen Name</label>
                        <div class="fw-bold fs-6"><?php echo e($complaint['citizen_name']); ?></div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small fw-semibold">Mobile Number</label>
                        <div>
                            <a href="tel:<?php echo e($complaint['mobile_no']); ?>" class="btn btn-sm btn-outline-success py-1 px-2">
                                <i class="bi bi-telephone-fill me-1"></i><?php echo e($complaint['mobile_no']); ?>
                            </a>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small fw-semibold">Email ID</label>
                        <div><?php echo e($complaint['email'] ?: 'Not provided'); ?></div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small fw-semibold">Submission Source</label>
                        <div><span class="badge bg-light text-dark border"><?php echo e($complaint['source'] ?: 'Website'); ?></span></div>
                    </div>
                    <div class="col-12">
                        <label class="text-muted small fw-semibold">Communication Address</label>
                        <div class="text-dark bg-light p-2 rounded"><?php echo nl2br(e($complaint['communication_address'] ?: '-')); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grievance Information & Description -->
        <div class="card content-card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0"><i class="bi bi-geo-alt text-primary me-2"></i>Grievance Location &amp; Description</h5>
            </div>
            <div class="card-body p-3">
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="text-muted small fw-semibold">Department</label>
                        <div class="fw-semibold text-primary"><?php echo e($complaint['department_name']); ?></div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small fw-semibold">Category (SLA)</label>
                        <div class="fw-semibold"><?php echo e($complaint['category_name']); ?> <span class="badge bg-secondary ms-1"><?php echo (int)$complaint['sla_days']; ?> days</span></div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small fw-semibold">Zone &amp; Ward</label>
                        <div>Zone <?php echo e($complaint['zone_no']); ?> &bull; Ward <?php echo e($complaint['ward_no']); ?></div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small fw-semibold">Street / Area</label>
                        <div><?php echo e($complaint['street_name'] ?: ($complaint['location_address'] ?: '-')); ?></div>
                    </div>
                    <?php if ($complaint['landmark']): ?>
                        <div class="col-12">
                            <label class="text-muted small fw-semibold">Landmark</label>
                            <div><?php echo e($complaint['landmark']); ?></div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="border-top pt-3">
                    <label class="text-muted small fw-semibold">Complaint Description (English)</label>
                    <div class="p-3 bg-light rounded mb-3" style="white-space: pre-wrap; font-size: 15px;"><?php echo e($complaint['description_english'] ?: '-'); ?></div>

                    <?php if ($complaint['description_tamil']): ?>
                        <label class="text-muted small fw-semibold">Complaint Description (Tamil)</label>
                        <div class="p-3 bg-light rounded" style="white-space: pre-wrap; font-size: 15px;"><?php echo e($complaint['description_tamil']); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Attachments -->
        <div class="card content-card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0"><i class="bi bi-paperclip text-primary me-2"></i>Attachments &amp; Photos (<?php echo count($attachments); ?>)</h5>
            </div>
            <div class="card-body p-3">
                <?php if (empty($attachments)): ?>
                    <p class="text-muted mb-0 small">No attachments uploaded for this complaint.</p>
                <?php else: ?>
                    <div class="row g-2">
                        <?php foreach ($attachments as $att): ?>
                            <?php
                                $isImg = str_starts_with((string)$att['mime_type'], 'image/');
                                $fileUrl = url($att['file_path'] ?? ('complaint-attachment.php?id=' . $att['id']));
                            ?>
                            <div class="col-sm-6">
                                <div class="border rounded p-2 d-flex align-items-center gap-2">
                                    <div class="fs-4 text-primary">
                                        <i class="bi <?php echo $isImg ? 'bi-image' : 'bi-file-earmark-pdf'; ?>"></i>
                                    </div>
                                    <div class="flex-grow-1 text-truncate">
                                        <a href="<?php echo e($fileUrl); ?>" target="_blank" class="fw-semibold text-decoration-none text-truncate d-block">
                                            <?php echo e($att['original_name'] ?: 'Attachment #' . $att['id']); ?>
                                        </a>
                                        <small class="text-muted"><?php echo number_format(((int)$att['file_size']) / 1024, 1); ?> KB</small>
                                    </div>
                                    <a href="<?php echo e($fileUrl); ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="Open file">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Citizen Feedback Section (if any) -->
        <?php if (!empty($feedbacks)): ?>
            <div class="card content-card border-0 shadow-sm mb-4 border-start border-4 border-warning">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-warning"><i class="bi bi-star-fill me-2"></i>Citizen Feedback &amp; Rating</h5>
                </div>
                <div class="card-body p-3">
                    <?php foreach ($feedbacks as $fb): ?>
                        <div class="p-3 bg-light rounded mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="bi <?php echo $i <= (int)$fb['rating'] ? 'bi-star-fill text-warning' : 'bi-star text-muted'; ?>"></i>
                                    <?php endfor; ?>
                                    <strong class="ms-1"><?php echo (int)$fb['rating']; ?> / 5</strong>
                                </div>
                                <small class="text-muted"><?php echo date('d-M-Y H:i', strtotime($fb['created_at'])); ?></small>
                            </div>
                            <div class="mt-2 text-dark"><?php echo nl2br(e($fb['feedback_text'])); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Column: Officer Action Box & Activity Timeline -->
    <div class="col-lg-5">
        <!-- Officer Action Form -->
        <div class="card content-card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-tools me-2"></i>Take Action / Update Status</h5>
            </div>
            <div class="card-body p-3">
                <?php if ($complaint['status'] === 'Closed'): ?>
                    <div class="alert alert-secondary mb-0">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-lock-fill fs-4"></i>
                            <div>
                                <strong>Complaint Closed</strong>
                                <div class="small">This complaint was officially closed on <?php echo $complaint['closed_at'] ? date('d-M-Y H:i', strtotime($complaint['closed_at'])) : '-'; ?>. No further status actions are permitted.</div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <form method="post" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="action" value="update_status">

                        <div class="mb-3">
                            <label class="form-label fw-bold required">What did you do?</label>
                            <div class="d-grid gap-2" role="group" aria-label="Choose complaint action">
                                <?php
                                  $choices = match($complaint['status']) {
                                    'Assigned' => [
                                      ['In Progress','Start Work','bi-play-circle','primary'],
                                      ['Assigned','Add a Note','bi-chat-dots','secondary']
                                    ],
                                    'In Progress','Reopened' => [
                                      ['In Progress','Work In Progress','bi-tools','primary'],
                                      ['Resolved','Mark Resolved','bi-check-circle','success'],
                                      ['Closed','Close Complaint','bi-archive','dark'],
                                    ],
                                    'Resolved' => [
                                      ['Closed','Close Complaint','bi-archive','success'],
                                      ['Resolved','Add a Note','bi-chat-dots','secondary'],
                                    ],
                                    default => []
                                  };
                                ?>
                                <?php foreach($choices as $i=>$choice): ?>
                                  <label class="border rounded-3 p-3 d-flex align-items-center gap-2 bg-light"
                                    style="cursor:pointer">
                                    <input type="radio" class="form-check-input m-0" name="status"
                                       value="<?php echo e($choice[0]); ?>" <?php echo $i===0?'checked':''; ?> required>
                                    <i class="bi <?php echo e($choice[2]); ?> text-<?php echo e($choice[3]); ?> fs-5"></i>
                                    <strong><?php echo e($choice[1]); ?></strong>
                                  </label>
                                <?php endforeach; ?>
                            </div>
                            <div class="form-text">Choose one action, write a short work note, and press Save Update.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold required">Officer Remarks / Work Summary</label>
                            <textarea name="remarks" class="form-control" rows="4" placeholder="Describe the action taken, site inspection findings, or resolution details..." required></textarea>
                            <div class="form-text small">Mandatory when resolving or closing the complaint. Visible in timeline.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Proof of Work / Photo (Optional)</label>
                            <input type="file" name="proof_file" class="form-control" accept="image/*,application/pdf">
                            <div class="form-text small">Upload on-site completion photo or resolution report (JPG, PNG, PDF up to 5MB).</div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm">
                            <i class="bi bi-check2-circle me-1"></i> Save Complaint Update
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Activity Logs Timeline -->
        <div class="card content-card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Activity &amp; Status Timeline</h5>
            </div>
            <div class="card-body p-3">
                <?php if (empty($history)): ?>
                    <p class="text-muted small mb-0">No timeline events recorded.</p>
                <?php else: ?>
                    <div class="complaint-activity-list">
                        <?php foreach (array_reverse($history) as $item): ?>
                            <?php
                                $badgeClass = match($item['new_status']) {
                                    'Registered' => 'bg-secondary',
                                    'Assigned' => 'bg-warning text-dark',
                                    'In Progress' => 'bg-info text-dark',
                                    'Reopened' => 'bg-danger',
                                    'Resolved' => 'bg-success',
                                    'Closed' => 'bg-dark',
                                    default => 'bg-primary'
                                };
                            ?>
                            <div class="border-start border-3 ps-3 pb-3 position-relative mb-2" style="border-color: #0d6efd !important;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="badge <?php echo $badgeClass; ?>"><?php echo e($item['new_status']); ?></span>
                                    <small class="text-muted"><?php echo date('d-M-Y H:i', strtotime($item['changed_at'])); ?></small>
                                </div>
                                <div class="fw-semibold text-dark mt-1">
                                    <?php echo e($item['user_name'] ?: ($item['new_status'] === 'Registered' ? 'Citizen' : 'System')); ?>
                                    <?php if ($item['user_role']): ?>
                                        <small class="text-muted">(<?php echo e($item['user_role']); ?>)</small>
                                    <?php endif; ?>
                                </div>
                                <?php if ($item['remarks']): ?>
                                    <div class="bg-light p-2 rounded mt-1 small text-dark border">
                                        <?php echo nl2br(e($item['remarks'])); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/employee-footer.php'; ?>
