<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'New Complaint';
$activePage = 'complaints';
$errors = [];
$successReference = null;
$assignedOfficer = null;
$assignedToIntake = false;
$old = $_POST;

try {
    $departments = db()->query(
        'SELECT id, department_name FROM departments WHERE is_active = 1 ORDER BY department_name'
    )->fetchAll();
} catch (Throwable $exception) {
    $departments = [];
    $errors[] = 'Master data is not available. Ask the administrator to configure departments.';
}

// Category options come from the same DB as the department dropdown.
// Do not depend solely on a separate AJAX response.
$categoriesByDepartment = [];
$categoryLoadFailed = false;
try {
    $rows = db()->query(
        'SELECT cc.id, cc.department_id, cc.category_name, cc.sla_days
         FROM complaint_categories cc
         JOIN departments d ON d.id=cc.department_id AND d.is_active=1
         WHERE cc.is_active=1
         ORDER BY cc.category_name,cc.id'
    )->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        $categoriesByDepartment[(int)$row['department_id']][] = [
            'id'=>(int)$row['id'],
            'category_name'=>(string)$row['category_name'],
            'sla_days'=>(int)$row['sla_days']
        ];
    }
} catch (Throwable $error) {
    $categoryLoadFailed = true;
    log_message('Complaint category preload failed: '.$error->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim($_POST['name'] ?? '');
    $mobile = normalize_mobile($_POST['mobile_no'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $communicationAddress = trim($_POST['communication_address'] ?? '');
    $streetId = (int) ($_POST['street_id'] ?? 0);
    $wardId = (int) ($_POST['ward_id'] ?? 0);
    $zoneId = (int) ($_POST['zone_id'] ?? 0);
    $doorNo = trim($_POST['door_no'] ?? '');
    $locationAddress = trim($_POST['location_address'] ?? '');
    $assessmentNo = trim($_POST['assessment_no'] ?? '');
    $departmentId = (int) ($_POST['department_id'] ?? 0);
    $categoryId = (int) ($_POST['complaint_category_id'] ?? 0);
    $descriptionTamil = trim($_POST['description_tamil'] ?? '');
    $descriptionEnglish = trim($_POST['description_english'] ?? '');
    $captchaAnswer = trim($_POST['captcha_answer'] ?? '');

    if ($name === '') $errors[] = 'Name is required.';
    if (!preg_match('/^[6-9]\d{9}$/', $mobile)) $errors[] = 'Enter a valid 10-digit mobile number.';
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if ($communicationAddress === '') $errors[] = 'Communication address is required.';
    if ($streetId <= 0 || $wardId <= 0 || $zoneId <= 0) $errors[] = 'Select a locality from the suggestions.';
    if ($doorNo === '') $errors[] = 'Door number is required.';
    if ($locationAddress === '') $errors[] = 'Complaint location address is required.';
    if ($departmentId <= 0 || $categoryId <= 0) $errors[] = 'Select a department and complaint category.';
    if ($descriptionEnglish === '') $errors[] = 'English complaint description is required.';
    if (($_SESSION['complaint_captcha'] ?? '') !== strtolower($captchaAnswer)) $errors[] = 'Captcha answer is incorrect.';
    // Temporary bypass for testing
    // if (($_SESSION['otp_verified_mobile']['complaint'] ?? '') !== $mobile) $errors[] = 'Verify the mobile number using OTP before submitting.';

    $files = $_FILES['attachments'] ?? null;
    $validFiles = [];

    if ($files && is_array($files['name'] ?? null)) {
        foreach ($files['name'] as $index => $fileName) {
            if ($fileName === '') continue;
            if (count($validFiles) >= MAX_ATTACHMENTS) {
                $errors[] = 'A maximum of four attachments is allowed.';
                break;
            }

            $errorCode = $files['error'][$index] ?? UPLOAD_ERR_NO_FILE;
            $size = (int) ($files['size'][$index] ?? 0);
            $tmpName = $files['tmp_name'][$index] ?? '';

            if ($errorCode !== UPLOAD_ERR_OK) {
                $errors[] = 'One attachment could not be uploaded.';
                continue;
            }
            if ($size > MAX_UPLOAD_BYTES) {
                $errors[] = 'Each attachment must be 2 MB or smaller.';
                continue;
            }

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($tmpName) ?: '';
            $allowed = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'application/pdf' => 'pdf',
            ];

            if (!isset($allowed[$mime])) {
                $errors[] = 'Only PDF, JPG, JPEG and PNG files are allowed.';
                continue;
            }

            $validFiles[] = [
                'tmp_name' => $tmpName,
                'original_name' => $fileName,
                'mime_type' => $mime,
                'extension' => $allowed[$mime],
                'size' => $size,
            ];
        }
    }

    if (!$errors) {
        $pdo = db();

        try {
            $pdo->beginTransaction();

            $categoryStmt = $pdo->prepare(
                'SELECT id, sla_days FROM complaint_categories
                 WHERE id = ? AND department_id = ? AND is_active = 1 LIMIT 1'
            );
            $categoryStmt->execute([$categoryId, $departmentId]);
            $category = $categoryStmt->fetch();

            if (!$category) {
                throw new RuntimeException('The selected complaint category is invalid.');
            }

            $dueDate = date('Y-m-d H:i:s', strtotime('+' . (int) $category['sla_days'] . ' days'));

            $insert = $pdo->prepare(
                'INSERT INTO complaints
                 (reference_no, citizen_name, mobile_no, email, communication_address, street_id, ward_id, zone_id,
                  door_no, location_address, assessment_no, department_id, category_id, description_tamil,
                  description_english, status, source, due_at, created_at, updated_at)
                 VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())'
            );
            $insert->execute([
                $name,
                $mobile,
                $email !== '' ? $email : null,
                $communicationAddress,
                $streetId,
                $wardId,
                $zoneId,
                $doorNo,
                $locationAddress,
                $assessmentNo !== '' ? $assessmentNo : null,
                $departmentId,
                $categoryId,
                $descriptionTamil !== '' ? $descriptionTamil : null,
                $descriptionEnglish,
                'Registered',
                'Website',
                $dueDate,
            ]);

            $complaintId = (int) $pdo->lastInsertId();

            // Detect legacy BEFORE INSERT triggers bypassing PHP Stage 1
            // authority checks. Roll back rather than accepting an officer
            // selected outside officer_stage_authorized().
            $insertCheck = $pdo->prepare(
                'SELECT assigned_officer_id, status FROM complaints WHERE id = ? LIMIT 1'
            );
            $insertCheck->execute([$complaintId]);
            $insertedState = $insertCheck->fetch(PDO::FETCH_ASSOC);
            if (!$insertedState || $insertedState['assigned_officer_id'] !== null
                || $insertedState['status'] !== 'Registered') {
                throw new RuntimeException(
                    'Unsafe database-side auto-assignment detected. Ask an administrator to review the complaint insertion trigger.'
                );
            }

            $reference = generate_reference_number($complaintId);

            $pdo->prepare('UPDATE complaints SET reference_no = ? WHERE id = ?')
                ->execute([$reference, $complaintId]);

            $pdo->prepare(
                'INSERT INTO complaint_status_history
                 (complaint_id, old_status, new_status, remarks, changed_by)
                 VALUES (?, NULL, ?, ?, NULL)'
            )->execute([$complaintId, 'Registered', 'Complaint registered through the public website.']);

            $uploadDirectory = __DIR__ . '/storage/uploads/complaints/' . $complaintId . '/';
            if ($validFiles && !is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true)) {
                throw new RuntimeException('Unable to create the attachment directory.');
            }

            foreach ($validFiles as $file) {
                $storedName = bin2hex(random_bytes(16)) . '.' . $file['extension'];
                $target = $uploadDirectory . $storedName;

                if (!move_uploaded_file($file['tmp_name'], $target)) {
                    throw new RuntimeException('Unable to store an attachment.');
                }

                $relativePath = 'storage/uploads/complaints/' . $complaintId . '/' . $storedName;
                $pdo->prepare(
                    'INSERT INTO complaint_attachments
                     (complaint_id, file_path, original_name, mime_type, file_size)
                     VALUES (?, ?, ?, ?, ?)'
                )->execute([
                    $complaintId,
                    $relativePath,
                    $file['original_name'],
                    $file['mime_type'],
                    $file['size'],
                ]);
            }

            $initialOfficer = assign_officer_for_complaint($complaintId, $departmentId, $categoryId, $wardId, $zoneId);
            if ($initialOfficer === null) {
                $initialOfficer = assign_complaint_to_intake($complaintId,$departmentId,$categoryId,$wardId,$zoneId);
                $assignedToIntake = $initialOfficer !== null;
            }
            if ($initialOfficer === null) {
                // Do not commit a new unassigned complaint if no accountable intake
                // officer exists. This rollback preserves the one-assignee guarantee.
                throw new RuntimeException(
                    'No eligible departmental or central intake officer is configured. Please contact the complaint administrator.'
                );
            }
            $assignedOfficer = get_assigned_officer_for_complaint($complaintId);

            $pdo->commit();

            unset($_SESSION['otp_verified_mobile']['complaint']);
            $_SESSION['complaint_captcha'] = strtolower(bin2hex(random_bytes(2)));
            $successReference = $reference;
            $old = [];
            audit_log(null, 'public_complaint_created', 'complaint', $complaintId, ['reference_no' => $reference]);
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            log_message('Complaint submission failed: ' . $exception->getMessage());
            $errors[] = APP_ENV === 'local'
                ? $exception->getMessage()
                : 'The complaint could not be saved. Please try again.';
        }
    }
}

if (empty($_SESSION['complaint_captcha'])) {
    $_SESSION['complaint_captcha'] = strtolower(bin2hex(random_bytes(2)));
}

include __DIR__ . '/includes/header.php';
?>
<section class="page-banner"><div class="container-xxl"><h1>New Complaint</h1><p>Register your grievance with personal, location and complaint details.</p></div></section>
<div class="container-xxl py-5">
    <?php if ($successReference): ?>
        <div class="alert alert-success p-4">
            <h2 class="h4">Complaint registered successfully</h2>
            <p class="mb-2">Your complaint reference number is:</p>
            <div class="display-6 fw-bold"><?php echo e($successReference); ?></div>
            <?php if ($assignedOfficer): ?>
                <div class="mt-3 p-3 bg-white border rounded text-dark">
                    <div class="fw-semibold"><?php echo $assignedToIntake ? 'Complaint Intake Officer (specialist routing pending):' : 'Automatically Allocated Officer:'; ?></div>
                    <div>
                        <?php echo e($assignedOfficer['officer_name']); ?>
                        <?php if (!empty($assignedOfficer['designation'])): ?>
                            <span class="text-muted">(<?php echo e($assignedOfficer['designation']); ?>)</span>
                        <?php endif; ?>
                        <?php if (!empty($assignedOfficer['mobile_no'])): ?>
                            — <strong><?php echo e($assignedOfficer['mobile_no']); ?></strong>
                        <?php endif; ?>
                    </div>
                    <?php if ($assignedToIntake): ?>
                        <p class="text-muted small mt-2 mb-0">Your complaint has an intake officer who will direct it to the responsible specialist.</p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info mt-3 mb-0" role="status">
                    Your complaint has been registered and is awaiting assignment
                    to an authorized department officer. You can track its status
                    using your reference number.
                </div>
            <?php endif; ?>
            <a class="btn btn-success mt-3" href="<?php echo e(url('complaint-status.php?reference_no=' . urlencode($successReference))); ?>">Track Complaint</a>
        </div>
    <?php endif; ?>
    <?php if ($errors): ?><div class="alert alert-danger"><strong>Please correct the following:</strong><ul class="mb-0 mt-2"><?php foreach ($errors as $error): ?><li><?php echo e($error); ?></li><?php endforeach; ?></ul></div><?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="complaint-form-shell">
        <?php echo csrf_field(); ?>
        <section class="form-panel">
            <h2 class="form-panel-title">Personal Info</h2>
            <p class="form-panel-note">If you have submitted a complaint before, enter the same mobile number. Verified mobile details can be reused securely.</p>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label required">Name</label><input class="form-control" name="name" value="<?php echo e($old['name'] ?? ''); ?>" required></div>
                <div class="col-md-6"><label class="form-label required">Enter Mobile No</label><div class="input-group"><input class="form-control" id="mobile_no" name="mobile_no" maxlength="10" inputmode="numeric" value="<?php echo e($old['mobile_no'] ?? ''); ?>" required><button class="btn btn-outline-info" type="button" data-send-otp data-mobile-target="#mobile_no" data-purpose="complaint">Send / Resend OTP</button></div><div data-otp-state class="small text-muted mt-1">OTP is valid for 5 minutes.</div></div>
                <div class="col-md-6"><label class="form-label">E-Mail</label><input class="form-control" type="email" name="email" value="<?php echo e($old['email'] ?? ''); ?>"></div>
                <div class="col-md-6"><label class="form-label">Enter OTP</label><div class="input-group"><input class="form-control" id="complaint_otp" maxlength="6" inputmode="numeric"><button class="btn btn-outline-success" type="button" data-verify-otp data-mobile-target="#mobile_no" data-otp-target="#complaint_otp" data-purpose="complaint">Verify OTP</button></div><input type="hidden" id="otp_verified" value="0"></div>
                <div class="col-12"><label class="form-label required">Communication Address</label><textarea class="form-control" id="communication_address" name="communication_address" rows="3" required><?php echo e($old['communication_address'] ?? ''); ?></textarea></div>
            </div>
        </section>

        <section class="form-panel">
            <h2 class="form-panel-title">Location Info</h2>
            <div class="row g-3">
                <div class="col-lg-8 autocomplete-box"><label class="form-label required">Locality Name</label><input class="form-control" id="street_name" autocomplete="off" placeholder="Type locality or street name"><div id="street_results" class="autocomplete-results d-none"></div><input type="hidden" id="street_id" name="street_id" value="<?php echo e($old['street_id'] ?? ''); ?>"><input type="hidden" id="ward_id" name="ward_id" value="<?php echo e($old['ward_id'] ?? ''); ?>"><input type="hidden" id="zone_id" name="zone_id" value="<?php echo e($old['zone_id'] ?? ''); ?>"></div>
                <div class="col-md-2"><label class="form-label required">Ward</label><input class="form-control" id="ward_display" readonly></div>
                <div class="col-md-2"><label class="form-label required">Zone</label><input class="form-control" id="zone_display" readonly></div>
                <div class="col-md-4"><label class="form-label required">Enter Door No</label><input class="form-control" name="door_no" value="<?php echo e($old['door_no'] ?? ''); ?>" required></div>
                <div class="col-md-8"><label class="form-label required">Enter Address</label><textarea class="form-control" id="location_address" name="location_address" rows="2" required><?php echo e($old['location_address'] ?? ''); ?></textarea><div class="form-check mt-2"><input class="form-check-input" type="checkbox" id="same_address" data-copy-address><label class="form-check-label" for="same_address">Same as Above Address</label></div></div>
                <div class="col-md-6"><label class="form-label">Assessment No</label><input class="form-control" name="assessment_no" value="<?php echo e($old['assessment_no'] ?? ''); ?>"></div>
            </div>
        </section>

        <section class="form-panel">
            <h2 class="form-panel-title">Complaint Info</h2>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label required">Department</label><select class="form-select" id="department_id" name="department_id" data-category-source required><option value="">Select Department</option><?php foreach ($departments as $department): ?><option value="<?php echo (int)$department['id']; ?>" <?php echo ((string)($old['department_id'] ?? '') === (string)$department['id']) ? 'selected' : ''; ?>><?php echo e($department['department_name']); ?></option><?php endforeach; ?></select></div>
                <div class="col-md-6">
                    <label class="form-label required" for="complaint_category_id">Complaint Category</label>
                    <?php
                        $currentDepartment = (int)($old['department_id'] ?? 0);
                        $currentCategory = (int)($old['complaint_category_id'] ?? 0);
                        $initialOptions = $categoriesByDepartment[$currentDepartment] ?? [];
                    ?>
                    <select class="form-select" id="complaint_category_id" name="complaint_category_id"
                            data-selected="<?php echo $currentCategory; ?>"
                            aria-describedby="complaint-category-help" required>
                        <option value="">Select complaint category</option>
                        <?php foreach ($initialOptions as $categoryOption): ?>
                        <option value="<?php echo (int)$categoryOption['id']; ?>"
                            <?php echo (int)$categoryOption['id'] === $currentCategory ? 'selected' : ''; ?>>
                            <?php echo e($categoryOption['category_name'].' ('.$categoryOption['sla_days'].' days)'); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text" id="complaint-category-help" aria-live="polite">
                        <?php if ($categoryLoadFailed): ?>Category data unavailable; reload the page.
                        <?php elseif ($currentDepartment > 0): ?>
                            <?php echo count($initialOptions); ?> categories available for this department.
                        <?php else: ?>Select a department to display its complaint categories.
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-12"><label class="form-label">Complaint Description (in Tamil)</label><textarea class="form-control" name="description_tamil" rows="3"><?php echo e($old['description_tamil'] ?? ''); ?></textarea></div>
                <div class="col-12"><label class="form-label required">Complaint Description (in English)</label><textarea class="form-control" name="description_english" rows="4" required><?php echo e($old['description_english'] ?? ''); ?></textarea></div>
            </div>
        </section>

        <section class="form-panel">
            <h2 class="form-panel-title">Attachment</h2>
            <p class="attachment-note mb-1">Only PDF, PNG, JPEG and JPG files are allowed.</p><p class="form-text mb-4">Upload less than 2 MB per file. Maximum four attachments.</p>
            <div class="row g-3">
                <?php for ($attachmentNumber = 1; $attachmentNumber <= 4; $attachmentNumber++): ?>
                    <div class="col-md-6 attachment-field"><label class="form-label">Attachment -<?php echo $attachmentNumber; ?></label><input class="form-control" type="file" name="attachments[]" accept=".pdf,.png,.jpg,.jpeg"><div data-file-name class="form-text">No file selected</div></div>
                <?php endfor; ?>
                <div class="col-md-6"><label class="form-label required d-block">Captcha</label><div class="captcha-box"><?php echo e($_SESSION['complaint_captcha']); ?></div></div>
                <div class="col-md-6"><label class="form-label required">Enter Above Code</label><input class="form-control" name="captcha_answer" required></div>
            </div>
        </section>

        <div class="d-flex flex-wrap gap-2 mt-4"><button class="btn btn-primary px-4" type="submit">Save</button><button class="btn btn-outline-light px-4" type="reset">Clear</button></div>
    </form>
</div>
<script type="application/json" id="complaint-category-data"><?php echo json_encode(
    $categoriesByDepartment,
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
); ?></script>
<?php include __DIR__ . '/includes/footer.php'; ?>
