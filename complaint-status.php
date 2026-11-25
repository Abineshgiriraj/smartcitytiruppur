<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Complaint Status';
$activePage = 'complaints';
$errors = [];
$complaints = [];
$searchType = $_POST['search_type'] ?? 'reference';
$referenceNo = trim($_POST['reference_no'] ?? ($_GET['reference_no'] ?? ''));
$mobileNo = normalize_mobile($_POST['mobile_no'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    if (isset($_POST['reopen_complaint_id'])) {
        // Moved to complaint-view.php
    } else {
        if ($searchType === 'reference') {
            if ($referenceNo === '') {
                $errors[] = 'Enter a complaint reference number.';
            } else {
                $stmt = db()->prepare(
                    "SELECT c.*, d.department_name, cc.category_name, w.ward_no, z.zone_no,
                            o.officer_name, o.mobile_no AS officer_mobile
                     FROM complaints c
                     JOIN departments d ON d.id = c.department_id
                     JOIN complaint_categories cc ON cc.id = c.category_id
                     JOIN wards w ON w.id = c.ward_id
                     JOIN zones z ON z.id = c.zone_id
                     LEFT JOIN officers o ON o.id = c.assigned_officer_id
                     WHERE c.reference_no = ? AND c.is_deleted = 0"
                );
                $stmt->execute([$referenceNo]);
                $row = $stmt->fetch();
                if ($row) $complaints[] = $row;
            }
        } else {
            if (!preg_match('/^[6-9]\d{9}$/', $mobileNo)) {
                $errors[] = 'Enter a valid registered mobile number.';
            } elseif (($_SESSION['otp_verified_mobile']['tracking'] ?? '') !== $mobileNo) {
                $errors[] = 'Verify the mobile number with OTP before searching.';
            } else {
                $stmt = db()->prepare(
                    "SELECT c.*, d.department_name, cc.category_name, w.ward_no, z.zone_no,
                            o.officer_name, o.mobile_no AS officer_mobile
                     FROM complaints c
                     JOIN departments d ON d.id = c.department_id
                     JOIN complaint_categories cc ON cc.id = c.category_id
                     JOIN wards w ON w.id = c.ward_id
                     JOIN zones z ON z.id = c.zone_id
                     LEFT JOIN officers o ON o.id = c.assigned_officer_id
                     WHERE c.mobile_no = ? AND c.is_deleted = 0
                     ORDER BY c.created_at DESC"
                );
                $stmt->execute([$mobileNo]);
                $complaints = $stmt->fetchAll();
            }
        }

        if (!$errors && !$complaints) {
            $errors[] = 'No complaint records were found.';
        }
    }
}
elseif ($referenceNo !== '') {
    $stmt = db()->prepare(
        "SELECT c.*, d.department_name, cc.category_name, w.ward_no, z.zone_no,
                o.officer_name, o.mobile_no AS officer_mobile
         FROM complaints c
         JOIN departments d ON d.id = c.department_id
         JOIN complaint_categories cc ON cc.id = c.category_id
         JOIN wards w ON w.id = c.ward_id
         JOIN zones z ON z.id = c.zone_id
         LEFT JOIN officers o ON o.id = c.assigned_officer_id
         WHERE c.reference_no = ? AND c.is_deleted = 0"
    );
    $stmt->execute([$referenceNo]);
    $row = $stmt->fetch();

    if ($row) {
        $complaints[] = $row;
    } else {
        $errors[] = 'No complaint records were found.';
    }
}

$showQuickServices = false;
include __DIR__ . '/includes/header.php';
?>
<section class="tracking-hero">
    <div class="container-xxl">
        <div class="tracking-layout">
            <div>
                <h1 class="hero-title">TRACKING</h1>
                <?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?php echo e($error); ?></li><?php endforeach; ?></ul></div><?php endif; ?>
                <form method="post">
                    <?php echo csrf_field(); ?>
                    <div class="tracking-options">
                        <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="search_type" id="type_reference" value="reference" <?php echo $searchType !== 'mobile' ? 'checked' : ''; ?>><label class="form-check-label" for="type_reference">Reference Number</label></div>
                        <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="search_type" id="type_mobile" value="mobile" <?php echo $searchType === 'mobile' ? 'checked' : ''; ?>><label class="form-check-label" for="type_mobile">Mobile Number</label></div>
                    </div>
                    <div class="tracking-form">
                        <div class="tracking-input"><input class="form-control" name="reference_no" value="<?php echo e($referenceNo); ?>" placeholder="Complaint Reference No"><i class="bi bi-person"></i></div>
                        <span class="tracking-or">(Or)</span>
                        <div class="tracking-input"><input class="form-control" id="tracking_mobile" name="mobile_no" maxlength="10" value="<?php echo e($mobileNo); ?>" placeholder="Enter Mobile No"><i class="bi bi-shield-lock"></i></div>
                        <button class="tracking-submit" type="submit" aria-label="Search"><i class="bi bi-search"></i></button>
                    </div>
                    <div id="mobile_otp_area" class="tracking-otp <?php echo $searchType === 'mobile' ? '' : 'd-none'; ?>">
                        <div class="row g-2 align-items-center"><div class="col-md-4"><button class="btn btn-outline-info w-100" type="button" data-send-otp data-mobile-target="#tracking_mobile" data-purpose="tracking">Send OTP</button></div><div class="col-md-4"><input class="form-control" id="tracking_otp" maxlength="6" placeholder="6-digit OTP"></div><div class="col-md-4"><button class="btn btn-outline-success w-100" type="button" data-verify-otp data-mobile-target="#tracking_mobile" data-otp-target="#tracking_otp" data-purpose="tracking">Verify OTP</button></div><div class="col-12"><span data-otp-state class="small text-muted">Mobile search requires OTP verification.</span></div></div>
                    </div>
                </form>
            </div>
            <aside class="side-service-panel" aria-label="Quick Services">
                <div class="side-service-title">Quick Services</div>
                <a class="side-service-item" href="<?php echo e(url('new-complaint.php')); ?>"><i class="bi bi-pencil-square"></i><span><strong>New Complaint</strong><small>What is troubling you?</small></span></a>
                <a class="side-service-item" href="<?php echo e(url('complaint-status.php')); ?>"><i class="bi bi-search"></i><span><strong>Track Complaint</strong><small>What happened so far?</small></span></a>
                <a class="side-service-item" href="<?php echo e(url('contact.php')); ?>"><i class="bi bi-chat-dots"></i><span><strong>Contact Us</strong><small>Get in touch with us</small></span></a>
                <a class="side-service-item" href="<?php echo e(url('feedback.php')); ?>"><i class="bi bi-megaphone"></i><span><strong>Feedback</strong><small>Tell us what you think.</small></span></a>
            </aside>
        </div>
    </div>
</section>

<div class="container-xxl py-5">
<?php foreach ($complaints as $complaintIndex => $complaint): ?>
    <?php $tabId = 'complaint' . (int)$complaint['id']; ?>
    <article class="status-result-card mb-4">
        <header class="status-result-head d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div><div class="small text-muted">Complaint Reference Number</div><h2 class="h4 fw-bold mb-1"><?php echo e($complaint['reference_no']); ?></h2><div class="small text-muted">Registered: <?php echo e(date('d-m-Y h:i A', strtotime($complaint['created_at']))); ?></div></div>
            <span class="badge text-bg-<?php echo e(status_badge_class($complaint['status'])); ?> fs-6"><?php echo e($complaint['status']); ?></span>
        </header>
        <ul class="nav status-tabs px-3" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#<?php echo $tabId; ?>-status" type="button">Status</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#<?php echo $tabId; ?>-personal" type="button">Personal Info</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#<?php echo $tabId; ?>-officer" type="button">Officer Info</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#<?php echo $tabId; ?>-location" type="button">Location Info</button></li>
        </ul>
        <div class="tab-content status-tab-body">
            <div class="tab-pane fade show active" id="<?php echo $tabId; ?>-status"><div class="row g-4"><div class="col-lg-6"><dl class="detail-list"><dt>Complaint Status</dt><dd><?php echo e($complaint['status']); ?></dd><dt>Department</dt><dd><?php echo e($complaint['department_name']); ?></dd><dt>Category</dt><dd><?php echo e($complaint['category_name']); ?></dd><dt>Due Date</dt><dd><?php echo e($complaint['due_at'] ? date('d-m-Y', strtotime($complaint['due_at'])) : '-'); ?></dd></dl></div><div class="col-lg-6"><div class="p-3 rounded border bg-light"><h3 class="h6 fw-bold">Current progress</h3><p class="mb-0">This grievance is currently marked <strong><?php echo e($complaint['status']); ?></strong>. You can view the complaint information without accessing internal staff activity.</p></div></div></div></div>
            <div class="tab-pane fade" id="<?php echo $tabId; ?>-personal"><dl class="detail-list"><dt>Name</dt><dd><?php echo e($complaint['citizen_name']); ?></dd><dt>Mobile No</dt><dd><?php echo e(mask_mobile($complaint['mobile_no'])); ?></dd><dt>Communication Address</dt><dd><?php echo nl2br(e($complaint['communication_address'])); ?></dd></dl></div>
            <div class="tab-pane fade" id="<?php echo $tabId; ?>-officer"><?php if ($complaint['officer_name']): ?><dl class="detail-list"><dt>Name</dt><dd><?php echo e($complaint['officer_name']); ?></dd><dt>Mobile Number</dt><dd><?php echo e(mask_mobile((string)$complaint['officer_mobile'])); ?></dd></dl><?php else: ?><p class="text-muted mb-0">Officer assignment is pending.</p><?php endif; ?></div>
            <div class="tab-pane fade" id="<?php echo $tabId; ?>-location"><dl class="detail-list"><dt>Location Name</dt><dd><?php echo e($complaint['location_address']); ?></dd><dt>Ward No</dt><dd><?php echo e($complaint['ward_no']); ?></dd><dt>Zone</dt><dd><?php echo e($complaint['zone_no']); ?></dd><dt>Door No</dt><dd><?php echo e($complaint['door_no']); ?></dd><dt>Assessment No</dt><dd><?php echo e($complaint['assessment_no'] ?: '-'); ?></dd></dl></div>
        </div>
        <div class="px-3 pb-3">
            <a href="<?php echo e(url('complaint-view.php?reference_no=' . urlencode($complaint['reference_no']))); ?>" class="btn btn-info w-100 fw-bold py-2 text-white text-uppercase" style="background-color: #2ca8df; border-color: #2ca8df;">VIEW COMPLAINT INFO</a>
        </div>
    </article>
<?php endforeach; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
