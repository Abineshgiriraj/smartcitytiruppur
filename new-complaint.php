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

// Preload categories by department
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
            'id' => (int)$row['id'],
            'category_name' => (string)$row['category_name'],
            'sla_days' => (int)$row['sla_days']
        ];
    }
} catch (Throwable $error) {
    $categoryLoadFailed = true;
    log_message('Complaint category preload failed: ' . $error->getMessage());
}

// Preload all active 60 wards with zone mapping
$wardsList = [];
try {
    $wardsList = db()->query(
        'SELECT w.id AS ward_id, w.ward_no, w.ward_name, z.id AS zone_id, z.zone_no, z.zone_name
         FROM wards w
         JOIN zones z ON z.id = w.zone_id AND z.is_active = 1
         WHERE w.is_active = 1
         ORDER BY w.id ASC'
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $error) {
    log_message('Wards preload failed: ' . $error->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim($_POST['name'] ?? '');
    $mobile = normalize_mobile($_POST['mobile_no'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $locationName = trim($_POST['location_name'] ?? ($_POST['street_name'] ?? ''));
    $wardId = (int)($_POST['ward_id'] ?? 0);
    $zoneId = (int)($_POST['zone_id'] ?? 0);
    $doorNo = trim($_POST['door_no'] ?? '');
    $assessmentNo = trim($_POST['assessment_no'] ?? '');
    $latitude = trim($_POST['latitude'] ?? '');
    $longitude = trim($_POST['longitude'] ?? '');
    $departmentId = (int)($_POST['department_id'] ?? 0);
    $categoryId = (int)($_POST['complaint_category_id'] ?? 0);
    $description = trim($_POST['description'] ?? ($_POST['description_english'] ?? ''));
    $captchaAnswer = trim($_POST['captcha_answer'] ?? '');

    // 1. Personal validations
    if ($name === '') $errors[] = 'Name is required.';
    if (!preg_match('/^[6-9]\d{9}$/', $mobile)) $errors[] = 'Enter a valid 10-digit mobile number.';
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';

    // 2. Location validations (Direct GPS / Locality)
    if ($locationName === '') {
        $errors[] = 'Location / Area Name is required.';
    }

    if ($wardId <= 0 || $zoneId <= 0) {
        $errors[] = 'Ward and Zone are required. Capture your GPS location or choose Ward from the dropdown.';
    } else {
        $wardCheck = db()->prepare(
            'SELECT w.id, w.ward_no, z.id AS zone_id, z.zone_no 
             FROM wards w 
             JOIN zones z ON z.id = w.zone_id AND z.is_active = 1 
             WHERE w.id = ? AND w.is_active = 1 LIMIT 1'
        );
        $wardCheck->execute([$wardId]);
        $wardRow = $wardCheck->fetch(PDO::FETCH_ASSOC);

        if (!$wardRow) {
            $errors[] = 'The selected Ward is invalid or inactive.';
        } else {
            $zoneId = (int)$wardRow['zone_id'];
        }
    }

    if ($doorNo === '') $errors[] = 'Door / Building / Flat number is required.';
    if ($departmentId <= 0 || $categoryId <= 0) $errors[] = 'Select a department and complaint category.';
    if ($description === '') $errors[] = 'Complaint description is required.';
    if (($_SESSION['complaint_captcha'] ?? '') !== strtolower($captchaAnswer)) $errors[] = 'Captcha answer is incorrect.';

    // 3. Multi-file upload processing
    $files = $_FILES['attachments'] ?? null;
    $validFiles = [];

    $allowedMimeMap = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        'application/pdf' => 'pdf',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.ms-excel' => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
    ];

    if ($files && is_array($files['name'] ?? null)) {
        foreach ($files['name'] as $index => $fileName) {
            if ($fileName === '') continue;

            if (count($validFiles) >= MAX_ATTACHMENTS) {
                $errors[] = 'A maximum of ' . MAX_ATTACHMENTS . ' attachments is allowed.';
                break;
            }

            $errorCode = $files['error'][$index] ?? UPLOAD_ERR_NO_FILE;
            $size = (int)($files['size'][$index] ?? 0);
            $tmpName = $files['tmp_name'][$index] ?? '';

            if ($errorCode !== UPLOAD_ERR_OK) {
                $errors[] = "Attachment '{$fileName}' could not be uploaded (error code: {$errorCode}).";
                continue;
            }

            if ($size > MAX_UPLOAD_BYTES) {
                $errors[] = "Attachment '{$fileName}' exceeds the " . round(MAX_UPLOAD_BYTES / (1024 * 1024)) . " MB size limit.";
                continue;
            }

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($tmpName) ?: '';
            $ext = strtolower(pathinfo((string)$fileName, PATHINFO_EXTENSION));

            $normalizedExt = null;
            if (isset($allowedMimeMap[$mime])) {
                $normalizedExt = $allowedMimeMap[$mime];
            } elseif (in_array($ext, ['docx', 'xlsx', 'doc', 'xls', 'pdf', 'png', 'jpg', 'jpeg'], true)) {
                $validMimes = ['application/zip', 'application/octet-stream', 'application/x-zip-compressed', 'text/plain'];
                if (in_array($mime, $validMimes, true) || isset($allowedMimeMap[$mime])) {
                    $normalizedExt = in_array($ext, ['jpg', 'jpeg'], true) ? 'jpg' : $ext;
                }
            }

            if ($normalizedExt === null) {
                $errors[] = "Attachment '{$fileName}' has an unsupported file format. Allowed: PDF, DOC, DOCX, XLS, XLSX, PNG, JPG.";
                continue;
            }

            $validFiles[] = [
                'tmp_name' => $tmpName,
                'original_name' => $fileName,
                'mime_type' => $mime,
                'extension' => $normalizedExt,
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

            $dueDate = date('Y-m-d H:i:s', strtotime('+' . (int)$category['sla_days'] . ' days'));
            $derivedLocationAddress = trim($doorNo . ', ' . $locationName);

            // Check if description has Tamil script
            $hasTamil = (bool)preg_match('/[\x{0B80}-\x{0BFF}]/u', $description);
            $descEnglish = $description;
            $descTamil = $hasTamil ? $description : null;

            $insert = $pdo->prepare(
                'INSERT INTO complaints
                 (reference_no, citizen_name, mobile_no, email, communication_address, street_id, ward_id, zone_id,
                  door_no, location_address, landmark, latitude, longitude, assessment_no, department_id, category_id, description_tamil,
                  description_english, status, source, due_at, created_at, updated_at)
                 VALUES (NULL, ?, ?, ?, ?, NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())'
            );
            $insert->execute([
                $name,
                $mobile,
                $email !== '' ? $email : null,
                $derivedLocationAddress,
                $wardId,
                $zoneId,
                $doorNo,
                $derivedLocationAddress,
                $locationName,
                $latitude !== '' ? $latitude : null,
                $longitude !== '' ? $longitude : null,
                $assessmentNo !== '' ? $assessmentNo : null,
                $departmentId,
                $categoryId,
                $descTamil,
                $descEnglish,
                'Registered',
                'Website',
                $dueDate,
            ]);

            $complaintId = (int)$pdo->lastInsertId();

            // Detect legacy BEFORE INSERT triggers
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

            // Save attachments
            $uploadDirectory = __DIR__ . '/storage/uploads/complaints/' . $complaintId . '/';
            if ($validFiles && !is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true)) {
                throw new RuntimeException('Unable to create the attachment directory.');
            }

            foreach ($validFiles as $file) {
                $storedName = bin2hex(random_bytes(16)) . '.' . $file['extension'];
                $target = $uploadDirectory . $storedName;

                $moved = (PHP_SAPI === 'cli' && file_exists($file['tmp_name']))
                    ? copy($file['tmp_name'], $target)
                    : move_uploaded_file($file['tmp_name'], $target);

                if (!$moved) {
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

            // Automatic allocation following PDF policy
            $initialOfficer = assign_officer_for_complaint($complaintId, $departmentId, $categoryId, $wardId, $zoneId);
            if ($initialOfficer === null) {
                $initialOfficer = assign_complaint_to_intake($complaintId, $departmentId, $categoryId, $wardId, $zoneId);
                $assignedToIntake = ($initialOfficer !== null);
            }

            if ($initialOfficer === null) {
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

<section class="page-banner bg-primary text-white py-4" style="background: linear-gradient(135deg, #0d3c61 0%, #15578a 100%);">
    <div class="container-xxl">
        <h1 class="h2 fw-bold mb-1"><i class="bi bi-pencil-square me-2"></i>New Complaint Registration</h1>
        <p class="mb-0 text-white-50">Register your grievance with automatic GPS Ward/Zone capture, voice-enabled description, and automated officer allocation.</p>
    </div>
</section>

<div class="container-xxl py-5">
    <?php if ($successReference): ?>
        <div class="alert alert-success border-0 shadow-sm p-4 mb-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="bg-success text-white p-3 rounded-circle"><i class="bi bi-check-lg fs-2"></i></div>
                <div>
                    <h2 class="h4 fw-bold mb-1 text-success">Complaint Registered Successfully</h2>
                    <p class="mb-0 text-muted">Your grievance has been recorded in the Smart City Tiruppur Grievance Management System.</p>
                </div>
            </div>
            <div class="p-3 bg-white border rounded text-dark mb-3">
                <div class="small text-muted fw-bold">COMPLAINT REFERENCE NUMBER</div>
                <div class="display-6 fw-bold text-primary"><?php echo e($successReference); ?></div>
            </div>

            <?php if ($assignedOfficer): ?>
                <div class="p-3 bg-white border rounded text-dark mb-3">
                    <div class="fw-bold text-success mb-1">
                        <i class="bi bi-person-check-fill me-1"></i>
                        <?php echo $assignedToIntake ? 'Complaint Intake Officer (specialist routing pending):' : 'Automatically Allocated Field Officer:'; ?>
                    </div>
                    <div class="fs-5 fw-semibold text-dark">
                        <?php echo e($assignedOfficer['officer_name']); ?>
                        <?php if (!empty($assignedOfficer['designation'])): ?>
                            <span class="badge bg-light text-dark border ms-1"><?php echo e($assignedOfficer['designation']); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($assignedOfficer['mobile_no'])): ?>
                        <div class="small text-muted mt-1"><i class="bi bi-telephone me-1"></i>Official Contact: <strong><?php echo e($assignedOfficer['mobile_no']); ?></strong></div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info py-2 px-3 small">
                    <i class="bi bi-info-circle me-1"></i>Your complaint has been registered and is awaiting officer assignment.
                </div>
            <?php endif; ?>

            <a class="btn btn-success fw-bold px-4" href="<?php echo e(url('complaint-status.php?reference_no=' . urlencode($successReference))); ?>">
                <i class="bi bi-search me-1"></i> Track Complaint Status
            </a>
            <a class="btn btn-outline-secondary px-4 ms-2" href="<?php echo e(url('new-complaint.php')); ?>">
                <i class="bi bi-plus-circle me-1"></i> Register Another Complaint
            </a>
        </div>
    <?php endif; ?>

    <?php if ($errors): ?>
        <div class="alert alert-danger border-0 shadow-sm p-3 mb-4">
            <div class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-1"></i>Please correct the following:</div>
            <ul class="mb-0 ps-3">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="complaint-form-shell" id="newComplaintForm">
        <?php echo csrf_field(); ?>

        <!-- Hidden GPS coordinates -->
        <input type="hidden" id="latitude" name="latitude" value="<?php echo e($old['latitude'] ?? ''); ?>">
        <input type="hidden" id="longitude" name="longitude" value="<?php echo e($old['longitude'] ?? ''); ?>">

        <!-- 1. Personal Information Panel -->
        <section class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h2 class="h5 fw-bold mb-0 text-primary"><i class="bi bi-person-fill me-2"></i>1. Personal Information</h2>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold required">Citizen Full Name</label>
                        <input class="form-control" name="name" value="<?php echo e($old['name'] ?? ''); ?>" placeholder="Enter your full name" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold required">Mobile Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-phone"></i></span>
                            <input class="form-control" id="mobile_no" name="mobile_no" maxlength="10" inputmode="numeric"
                                   placeholder="10-digit mobile number" value="<?php echo e($old['mobile_no'] ?? ''); ?>" required>
                            <button class="btn btn-outline-primary" type="button" data-send-otp data-mobile-target="#mobile_no" data-purpose="complaint">
                                Send OTP
                            </button>
                        </div>
                        <div data-otp-state class="small text-muted mt-1">OTP verification ensures official status alerts.</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Enter OTP</label>
                        <div class="input-group">
                            <input class="form-control" id="complaint_otp" maxlength="6" inputmode="numeric" placeholder="6-digit OTP">
                            <button class="btn btn-outline-success" type="button" data-verify-otp data-mobile-target="#mobile_no" data-otp-target="#complaint_otp" data-purpose="complaint">
                                Verify OTP
                            </button>
                        </div>
                        <input type="hidden" id="otp_verified" value="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email Address <span class="text-muted fw-normal">(Optional)</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                            <input class="form-control" type="email" name="email" value="<?php echo e($old['email'] ?? ''); ?>" placeholder="name@example.com">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. Location & GPS Ward Verification Panel -->
        <section class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h2 class="h5 fw-bold mb-0 text-primary"><i class="bi bi-geo-alt-fill me-2"></i>2. Location &amp; Ward Verification</h2>
                <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" id="use-complaint-location">
                    <i class="bi bi-crosshair me-1"></i> Capture Location
                </button>
            </div>
            <div class="card-body p-4">
                <!-- GPS Status / Matched Ward Banner -->
                <div id="complaint-location-status" style="display: none;"></div>

                <div class="row g-3">
                    <!-- Location / Area Name (Simple Text - No Table Dropdown) -->
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold required">Location / Area / Landmark</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-pin-map"></i></span>
                            <input class="form-control form-control-lg" id="location_name" name="location_name"
                                   placeholder="Enter area, colony, street or landmark..."
                                   value="<?php echo e($old['location_name'] ?? ($old['street_name'] ?? '')); ?>" required>
                        </div>
                        <div class="form-text small">Enter your locality or nearest landmark.</div>
                    </div>

                    <!-- Ward (Direct Selector: auto-filled on GPS, manually editable anytime) -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold required" for="ward_id">Ward</label>
                        <select class="form-select form-select-lg" id="ward_id" name="ward_id" required>
                            <option value="">Select Ward</option>
                            <?php
                                $zonesGroup = [];
                                foreach ($wardsList as $w) {
                                    $zonesGroup[(int)$w['zone_id']][] = $w;
                                }
                                foreach ($zonesGroup as $zId => $wArr):
                            ?>
                                <optgroup label="<?php echo e($wArr[0]['zone_no'] ?? ('Zone ' . $zId)); ?>" data-zone-id="<?php echo (int)$zId; ?>">
                                    <?php foreach ($wArr as $w): ?>
                                        <option value="<?php echo (int)$w['ward_id']; ?>"
                                                data-zone-id="<?php echo (int)$w['zone_id']; ?>"
                                                data-zone-no="<?php echo e($w['zone_no']); ?>"
                                                data-ward-no="<?php echo e($w['ward_no']); ?>"
                                                <?php echo ((int)($old['ward_id'] ?? 0) === (int)$w['ward_id']) ? 'selected' : ''; ?>>
                                            <?php echo e($w['ward_no'] . ($w['ward_name'] ? ' (' . $w['ward_name'] . ')' : '')); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Zone (Direct Selector: auto-filled on GPS or ward selection, manually editable anytime) -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold required" for="zone_id">Zone</label>
                        <select class="form-select form-select-lg" id="zone_id" name="zone_id" required>
                            <option value="">Select Zone</option>
                            <?php
                                $uniqueZones = [];
                                foreach ($wardsList as $w) {
                                    $zid = (int)$w['zone_id'];
                                    if (!isset($uniqueZones[$zid])) {
                                        $uniqueZones[$zid] = [
                                            'zone_id' => $zid,
                                            'zone_no' => $w['zone_no'],
                                            'zone_name' => $w['zone_name'],
                                        ];
                                    }
                                }
                                foreach ($uniqueZones as $z):
                            ?>
                                <option value="<?php echo (int)$z['zone_id']; ?>"
                                        <?php echo ((int)($old['zone_id'] ?? 0) === (int)$z['zone_id']) ? 'selected' : ''; ?>>
                                    <?php echo e($z['zone_no'] . ($z['zone_name'] ? ' (' . $z['zone_name'] . ')' : '')); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Door No / Building -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold required">Door No / Flat / Building / Plot No</label>
                        <input class="form-control" id="door_no" name="door_no" value="<?php echo e($old['door_no'] ?? ''); ?>" placeholder="e.g. Door No. 14-B, 2nd Floor" required>
                    </div>

                    <!-- Assessment No -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Property Assessment No <span class="text-muted fw-normal">(Optional)</span></label>
                        <input class="form-control" name="assessment_no" value="<?php echo e($old['assessment_no'] ?? ''); ?>" placeholder="Enter assessment / tax number if available">
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. Complaint Details Panel -->
        <section class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h2 class="h5 fw-bold mb-0 text-primary"><i class="bi bi-chat-left-text-fill me-2"></i>3. Complaint Details</h2>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold required">Department</label>
                        <select class="form-select form-select-lg" id="department_id" name="department_id" data-category-source required>
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $department): ?>
                                <option value="<?php echo (int)$department['id']; ?>" <?php echo ((string)($old['department_id'] ?? '') === (string)$department['id']) ? 'selected' : ''; ?>>
                                    <?php echo e($department['department_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold required" for="complaint_category_id">Complaint Category</label>
                        <?php
                            $currentDepartment = (int)($old['department_id'] ?? 0);
                            $currentCategory = (int)($old['complaint_category_id'] ?? 0);
                            $initialOptions = $categoriesByDepartment[$currentDepartment] ?? [];
                        ?>
                        <select class="form-select form-select-lg" id="complaint_category_id" name="complaint_category_id"
                                data-selected="<?php echo $currentCategory; ?>" required>
                            <option value="">Select complaint category</option>
                            <?php foreach ($initialOptions as $categoryOption): ?>
                                <option value="<?php echo (int)$categoryOption['id']; ?>" <?php echo ((int)$categoryOption['id'] === $currentCategory) ? 'selected' : ''; ?>>
                                    <?php echo e($categoryOption['category_name'] . ' (' . $categoryOption['sla_days'] . ' days)'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text small" id="complaint-category-help">
                            <?php if ($categoryLoadFailed): ?>
                                Category data unavailable; please reload the page.
                            <?php elseif ($currentDepartment > 0): ?>
                                <?php echo count($initialOptions); ?> categories available.
                            <?php else: ?>
                                Select a department to display its complaint categories.
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Single Complaint Description with Voice Input Button -->
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-semibold required mb-0">Complaint Description</label>
                            <button type="button" class="btn btn-outline-primary btn-sm fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#voiceInputModal">
                                <i class="bi bi-mic-fill me-1 text-danger"></i> Voice Input
                            </button>
                        </div>
                        <textarea class="form-control" id="complaint_description" name="description" rows="4"
                                  placeholder="Describe findings, observations, and issues noted during inspection or grievance..." required><?php echo e($old['description'] ?? ($old['description_english'] ?? '')); ?></textarea>
                        <div class="form-text small">Type your complaint or click <strong>Voice Input</strong> to speak in English or Tamil.</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. Multiple Attachments & Security Panel -->
        <section class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h2 class="h5 fw-bold mb-0 text-primary"><i class="bi bi-paperclip me-2"></i>4. Attachments &amp; Verification</h2>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <!-- Single Multiple-File Dropzone -->
                    <div class="col-lg-7">
                        <label class="form-label fw-semibold mb-2">Upload Files &amp; Photos</label>
                        <div class="file-dropzone border border-2 border-dashed rounded p-4 text-center bg-light" id="dropzoneContainer" style="cursor: pointer; transition: all 0.2s ease;">
                            <div class="mb-2"><i class="bi bi-cloud-arrow-up-fill text-primary display-5"></i></div>
                            <h6 class="fw-bold mb-1">Click to browse or drag &amp; drop files here</h6>
                            <p class="text-muted small mb-3">
                                Supported formats: <strong>PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG</strong><br>
                                Maximum <strong>5 files</strong>, up to <strong>5 MB</strong> each.
                            </p>
                            <button type="button" class="btn btn-outline-primary btn-sm px-4 fw-semibold" id="browseFilesBtn">
                                <i class="bi bi-folder2-open me-1"></i> Browse Files
                            </button>
                            <input type="file" id="complaint_attachments" name="attachments[]" multiple class="d-none"
                                   accept=".pdf,.png,.jpg,.jpeg,.doc,.docx,.xls,.xlsx">
                        </div>

                        <!-- Live File Previews -->
                        <div id="fileListPreview" class="mt-3"></div>
                    </div>

                    <!-- Captcha Section -->
                    <div class="col-lg-5">
                        <label class="form-label fw-semibold mb-2">Security Verification</label>
                        <div class="p-3 bg-light rounded border">
                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">Security Code</label>
                                <div class="p-3 bg-white text-center rounded border font-monospace fs-3 fw-bold tracking-wider text-primary shadow-sm" style="letter-spacing: 6px;">
                                    <?php echo e($_SESSION['complaint_captcha']); ?>
                                </div>
                            </div>
                            <div>
                                <label class="form-label fw-semibold required">Enter Above Code</label>
                                <input class="form-control form-control-lg" name="captcha_answer" placeholder="Type the characters above" autocomplete="off" required>
                                <div class="form-text small">Case-insensitive verification.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Submit Actions -->
        <div class="d-flex flex-wrap gap-3">
            <button class="btn btn-primary btn-lg px-5 fw-bold shadow" type="submit">
                <i class="bi bi-send-check-fill me-2"></i> Submit Complaint
            </button>
            <button class="btn btn-outline-secondary btn-lg px-4" type="reset">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Clear Form
            </button>
        </div>
    </form>
</div>

<!-- ========================================================================= -->
<!-- Voice Input & Recording Modal (Matching Commissioner Inspection Portal)   -->
<!-- ========================================================================= -->
<div class="modal fade" id="voiceInputModal" tabindex="-1" aria-labelledby="voiceInputModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold" id="voiceInputModalLabel">
                    <i class="bi bi-pencil-fill text-dark me-2"></i>Voice Input &amp; Recording
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Select Language -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted mb-1">Select Language:</label>
                    <select class="form-select form-select-lg" id="voiceLangSelect">
                        <option value="en-IN" selected>English (India)</option>
                        <option value="ta-IN">Tamil (தமிழ்)</option>
                    </select>
                </div>

                <!-- Status indicator -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-semibold text-muted" id="voiceStatusText">Click Re-record or speak into your microphone...</span>
                    <span class="badge bg-secondary" id="voiceStatusBadge">Idle</span>
                </div>

                <!-- Transcribed Text Box -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted mb-1">Transcribed Text:</label>
                    <textarea class="form-control" id="voiceTranscribedText" rows="6"
                              placeholder="Your speech will appear here..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-top py-2 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-primary" id="btnVoiceReRecord">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Re-record
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary fw-semibold" id="btnVoiceUseText">
                        <i class="bi bi-check-lg me-1"></i> Use Text
                    </button>
                    <button type="button" class="btn btn-success fw-semibold" id="btnVoiceSaveRecording">
                        <i class="bi bi-save me-1"></i> Save Recording
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="application/json" id="complaint-category-data"><?php echo json_encode(
    $categoriesByDepartment,
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
); ?></script>

<script src="<?php echo e(url('assets/js/street-gps.js?v=20261124c')); ?>" defer></script>
<script src="<?php echo e(url('assets/data/ward_boundaries.js?v=20261124')); ?>"></script>
<script src="<?php echo e(url('assets/js/street-gps.js?v=20261124d')); ?>" defer></script>

<?php include __DIR__ . '/includes/footer.php'; ?>
