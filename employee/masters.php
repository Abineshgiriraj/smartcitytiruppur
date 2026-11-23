<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

$user = require_roles(['Super Admin', 'Admin']);
require_permission('masters.view');
$pageTitle = 'Master Management';
$section = $_GET['section'] ?? 'departments';
$allowedSections = ['departments','categories','zones','wards','streets','officers','mappings','allocations','settings'];

if (!in_array($section, $allowedSections, true)) {
    $section = 'departments';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_permission('masters.manage');
    verify_csrf();

    $action = $_POST['action'] ?? '';
    $pdo = db();

    try {
        if ($action === 'add_department') {
            $name = trim($_POST['department_name'] ?? '');
            if ($name === '') throw new RuntimeException('Department name is required.');
            $pdo->prepare('INSERT INTO departments (department_name, is_active) VALUES (?, 1)')->execute([$name]);
        } elseif ($action === 'add_category') {
            $departmentId = (int)($_POST['department_id'] ?? 0);
            $name = trim($_POST['category_name'] ?? '');
            $slaDays = (int)($_POST['sla_days'] ?? 0);
            if ($departmentId <= 0 || $name === '' || $slaDays <= 0) throw new RuntimeException('Department, category and SLA days are required.');
            $pdo->prepare('INSERT INTO complaint_categories (department_id, category_name, sla_days, is_active) VALUES (?, ?, ?, 1)')
                ->execute([$departmentId, $name, $slaDays]);
        } elseif ($action === 'add_zone') {
            $zoneNo = trim($_POST['zone_no'] ?? '');
            $zoneName = trim($_POST['zone_name'] ?? '');
            if ($zoneNo === '') throw new RuntimeException('Zone number is required.');
            $pdo->prepare('INSERT INTO zones (zone_no, zone_name, is_active) VALUES (?, ?, 1)')
                ->execute([$zoneNo, $zoneName !== '' ? $zoneName : null]);
        } elseif ($action === 'add_ward') {
            $zoneId = (int)($_POST['zone_id'] ?? 0);
            $wardNo = trim($_POST['ward_no'] ?? '');
            if ($zoneId <= 0 || $wardNo === '') throw new RuntimeException('Zone and ward number are required.');
            $pdo->prepare('INSERT INTO wards (zone_id, ward_no, ward_name, is_active) VALUES (?, ?, ?, 1)')
                ->execute([$zoneId, $wardNo, trim($_POST['ward_name'] ?? '') ?: null]);
        } elseif ($action === 'add_street') {
            $wardId = (int)($_POST['ward_id'] ?? 0);
            $street = trim($_POST['street_name'] ?? '');
            if ($wardId <= 0 || $street === '') throw new RuntimeException('Ward and street name are required.');
            $pdo->prepare('INSERT INTO streets (ward_id, street_name, is_active) VALUES (?, ?, 1)')
                ->execute([$wardId, $street]);
        } elseif ($action === 'add_officer') {
            $name = trim($_POST['officer_name'] ?? '');
            $linkedUserId = (int)($_POST['user_id'] ?? 0);
            if ($name === '') throw new RuntimeException('Officer name is required.');
            $pdo->prepare(
                'INSERT INTO officers (user_id, officer_name, designation, mobile_no, email, is_active)
                 VALUES (?, ?, ?, ?, ?, 1)'
            )->execute([
                $linkedUserId ?: null,
                $name,
                trim($_POST['designation'] ?? '') ?: null,
                normalize_mobile($_POST['mobile_no'] ?? '') ?: null,
                trim($_POST['email'] ?? '') ?: null,
            ]);
        } elseif ($action === 'map_officer') {
            $officerId = (int)($_POST['officer_id'] ?? 0);
            $departmentId = (int)($_POST['department_id'] ?? 0);
            $categoryId = (int)($_POST['category_id'] ?? 0);
            $wardId = (int)($_POST['ward_id'] ?? 0);

            if ($officerId <= 0) throw new RuntimeException('Select an officer.');

            if ($departmentId > 0) {
                $pdo->prepare('INSERT IGNORE INTO officer_department_map (officer_id, department_id) VALUES (?, ?)')
                    ->execute([$officerId, $departmentId]);
            }
            if ($categoryId > 0) {
                $pdo->prepare('INSERT IGNORE INTO officer_category_map (officer_id, category_id) VALUES (?, ?)')
                    ->execute([$officerId, $categoryId]);
            }
            if ($wardId > 0) {
                $pdo->prepare('INSERT IGNORE INTO officer_ward_map (officer_id, ward_id) VALUES (?, ?)')
                    ->execute([$officerId, $wardId]);
            }

            if ($departmentId <= 0 && $categoryId <= 0 && $wardId <= 0) {
                throw new RuntimeException('Select at least one department, category or ward mapping.');
            }
        } elseif ($action === 'save_allocation') {
            $departmentId = (int)($_POST['department_id'] ?? 0);
            $categoryId = (int)($_POST['category_id'] ?? 0);
            $zoneId = (int)($_POST['zone_id'] ?? 0);
            $wardChoice = trim($_POST['ward_id'] ?? '');
            $stageNo = (int)($_POST['stage_no'] ?? 1);
            $officerId = (int)($_POST['officer_id'] ?? 0);
            $escalationDays = (int)($_POST['escalation_days'] ?? 3);

            if ($departmentId <= 0 || $categoryId <= 0 || $zoneId <= 0 || $wardChoice === '' || $officerId <= 0) {
                throw new RuntimeException('Department, category, zone, ward, and officer are all required.');
            }
            if ($stageNo < 1 || $stageNo > 6) {
                throw new RuntimeException('PDF stage must be between 1 and 6.');
            }
            $policy = pdf_policy_routing_ready($pdo, $categoryId);
            $pdfStage = $policy === null ? null : pdf_policy_stage($policy, $stageNo);
            if (!$pdfStage) {
                throw new RuntimeException('No valid imported PDF policy for this category and stage.');
            }
            if ($escalationDays !== (int)$pdfStage['period_days']) {
                throw new RuntimeException('Stage days must equal the imported PDF period: '
                    . (int)$pdfStage['period_days'] . ' day(s).');
            }

            // Verify category belongs to selected department
            $catCheck = $pdo->prepare('SELECT id FROM complaint_categories WHERE id = ? AND department_id = ? LIMIT 1');
            $catCheck->execute([$categoryId, $departmentId]);
            if (!$catCheck->fetchColumn()) {
                throw new RuntimeException('Selected category does not belong to the selected department.');
            }

            // Support either a specific ward or all active wards in the selected zone
            if ($wardChoice === 'all') {
                $wStmt = $pdo->prepare('SELECT id FROM wards WHERE zone_id = ? AND is_active = 1');
                $wStmt->execute([$zoneId]);
                $targetWardIds = array_map('intval', $wStmt->fetchAll(PDO::FETCH_COLUMN));
                if (!$targetWardIds) {
                    throw new RuntimeException('No active wards found in the selected zone.');
                }
            } else {
                $wardId = (int)$wardChoice;
                $wCheck = $pdo->prepare('SELECT id FROM wards WHERE id = ? AND zone_id = ? LIMIT 1');
                $wCheck->execute([$wardId, $zoneId]);
                if (!$wCheck->fetchColumn()) {
                    throw new RuntimeException('Selected ward does not belong to the selected zone.');
                }
                $targetWardIds = [$wardId];
            }

            // Never allow a legacy/seed officer or wrong-ward mapping to become
            // an active exact rule. Check EVERY target ward before the first
            // write, including the "all wards" path.
            foreach ($targetWardIds as $targetWardId) {
                if (!officer_stage_authorized(
                    $pdo, $categoryId, $stageNo, $officerId,
                    $departmentId, $targetWardId
                )) {
                    throw new RuntimeException('Officer is not eligible for this PDF stage/category'
                        . ' or lacks the required department/category/ward mapping.'
                        . ' No allocation rules were saved.');
                }
                // Stage 1 must be ward-specific where the mapped source assigns a
                // ward; don't blindly treat an all-ward selection as permission.
                if ($stageNo === 1) {
                    $wardStmt = $pdo->prepare(
                        'SELECT COUNT(*) FROM officer_ward_map WHERE officer_id=?'
                    );
                    $wardStmt->execute([$officerId]);
                    $isWardScoped = (int)$wardStmt->fetchColumn() > 0;
                    if (!$isWardScoped) {
                        throw new RuntimeException(
                            'Ward scope is not explicitly recorded for this field officer.'
                            . ' Verify the ward mapping before creating exact allocation rules.'
                        );
                    }
                }
            }
            $pdo->beginTransaction();
            $upsert = $pdo->prepare(
                'INSERT INTO department_allocations
                 (department_id, category_id, zone_id, ward_id, stage_no, officer_id, escalation_days, is_active)
                 VALUES (?, ?, ?, ?, ?, ?, ?, 1)
                 ON DUPLICATE KEY UPDATE
                   officer_id = VALUES(officer_id),
                   escalation_days = VALUES(escalation_days),
                   is_active = 1,
                   updated_at = NOW()'
            );
            foreach ($targetWardIds as $targetWardId) {
                $upsert->execute([$departmentId, $categoryId, $zoneId, $targetWardId, $stageNo, $officerId, $escalationDays]);
            }
            $pdo->commit();
        } elseif ($action === 'delete_allocation') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) throw new RuntimeException('Invalid allocation rule.');
            $pdo->prepare('DELETE FROM department_allocations WHERE id = ?')->execute([$id]);
        } elseif ($action === 'set_central_intake_officer') {
            $intakeId=(int)($_POST['intake_officer_id']??0);
            if($intakeId<=0)throw new RuntimeException('Enter a valid intake officer ID.');
            $stmt=$pdo->prepare('SELECT id,officer_name,designation FROM officers WHERE id=? AND is_active=1');
            $stmt->execute([$intakeId]);
            $officer=$stmt->fetch(PDO::FETCH_ASSOC);
            if(!$officer || officer_stage_forbidden_title((string)$officer['designation'],1,$intakeId)) {
                throw new RuntimeException('The selected intake officer is inactive or not eligible to receive intake complaints.');
            }
            $pdo->prepare(
                "INSERT INTO system_settings (setting_key,setting_value,updated_at)
                 VALUES ('central_intake_officer_id',?,NOW())
                 ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value),updated_at=NOW()"
            )->execute([(string)$intakeId]);
            audit_log((int)$user['id'],'central_intake_officer_configured','officer',$intakeId,[
                'officer_name'=>$officer['officer_name']
            ]);
        } elseif ($action === 'save_setting') {
            $key = trim($_POST['setting_key'] ?? '');
            $value = trim($_POST['setting_value'] ?? '');
            $allowedKeys = [
                'support_phone',
                'support_email',
                'office_address',
                'site_manager_name',
                'site_manager_designation',
                'site_manager_email',
                'site_manager_mobile',
            ];

            if (!in_array($key, $allowedKeys, true)) throw new RuntimeException('Invalid setting key.');

            $pdo->prepare(
                'INSERT INTO system_settings (setting_key, setting_value, updated_at)
                 VALUES (?, ?, NOW())
                 ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()'
            )->execute([$key, $value]);
        } elseif ($action === 'toggle') {
            $table = $_POST['table'] ?? '';
            $id = (int)($_POST['id'] ?? 0);
            $allowedTables = ['departments','complaint_categories','zones','wards','streets','officers','department_allocations'];

            if (!in_array($table, $allowedTables, true) || $id <= 0) throw new RuntimeException('Invalid record.');
            $pdo->exec("UPDATE `$table` SET is_active = IF(is_active = 1, 0, 1) WHERE id = " . $id);
        } else {
            throw new RuntimeException('Invalid master action.');
        }

        audit_log((int)$user['id'], $action, 'master');
        flash('success', 'Master data updated successfully.');
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        flash('danger', APP_ENV === 'local' ? $exception->getMessage() : 'Unable to update master data.');
    }

    redirect('employee/masters.php?section=' . urlencode($section));
}

$departments = db()->query('SELECT * FROM departments ORDER BY department_name')->fetchAll();
$categories = db()->query(
    'SELECT cc.*, d.department_name FROM complaint_categories cc
     JOIN departments d ON d.id = cc.department_id
     ORDER BY d.department_name, cc.category_name'
)->fetchAll();
$zones = db()->query('SELECT * FROM zones ORDER BY zone_no')->fetchAll();
$wards = db()->query(
    'SELECT w.*, z.zone_no FROM wards w JOIN zones z ON z.id = w.zone_id ORDER BY z.zone_no, w.ward_no'
)->fetchAll();
$streets = db()->query(
    'SELECT s.*, w.ward_no, z.zone_no FROM streets s
     JOIN wards w ON w.id = s.ward_id
     JOIN zones z ON z.id = w.zone_id
     ORDER BY s.street_name LIMIT 1000'
)->fetchAll();
$officers = db()->query('SELECT * FROM officers ORDER BY officer_name')->fetchAll();
$employeeUsers = db()->query("SELECT id, full_name, role FROM users WHERE is_active = 1 ORDER BY full_name")->fetchAll();

$mappings = db()->query(
    "SELECT o.officer_name,
            GROUP_CONCAT(DISTINCT d.department_name ORDER BY d.department_name SEPARATOR ', ') AS departments,
            GROUP_CONCAT(DISTINCT cc.category_name ORDER BY cc.category_name SEPARATOR ', ') AS categories,
            GROUP_CONCAT(DISTINCT CONCAT('Z', z.zone_no, '-W', w.ward_no) ORDER BY z.zone_no, w.ward_no SEPARATOR ', ') AS wards
     FROM officers o
     LEFT JOIN officer_department_map odm ON odm.officer_id = o.id
     LEFT JOIN departments d ON d.id = odm.department_id
     LEFT JOIN officer_category_map ocm ON ocm.officer_id = o.id
     LEFT JOIN complaint_categories cc ON cc.id = ocm.category_id
     LEFT JOIN officer_ward_map owm ON owm.officer_id = o.id
     LEFT JOIN wards w ON w.id = owm.ward_id
     LEFT JOIN zones z ON z.id = w.zone_id
     GROUP BY o.id
     ORDER BY o.officer_name"
)->fetchAll();

$allocFilterDept = (int)($_GET['filter_dept'] ?? 0);
$allocFilterZone = (int)($_GET['filter_zone'] ?? 0);
$allocFilterStage = (int)($_GET['filter_stage'] ?? 0);
$allocations = [];
if ($section === 'allocations') {
    $aWhere = ['1=1'];
    $aParams = [];
    if ($allocFilterDept > 0) {
        $aWhere[] = 'da.department_id = ?';
        $aParams[] = $allocFilterDept;
    }
    if ($allocFilterZone > 0) {
        $aWhere[] = 'da.zone_id = ?';
        $aParams[] = $allocFilterZone;
    }
    if ($allocFilterStage > 0) {
        $aWhere[] = 'da.stage_no = ?';
        $aParams[] = $allocFilterStage;
    }
    $aStmt = db()->prepare(
        'SELECT da.*, d.department_name, cc.category_name, z.zone_no, w.ward_no,
                o.officer_name, o.mobile_no AS officer_mobile
         FROM department_allocations da
         JOIN departments d ON d.id = da.department_id
         JOIN complaint_categories cc ON cc.id = da.category_id
         JOIN zones z ON z.id = da.zone_id
         JOIN wards w ON w.id = da.ward_id
         JOIN officers o ON o.id = da.officer_id
         WHERE ' . implode(' AND ', $aWhere) . '
         ORDER BY d.department_name, cc.category_name, z.zone_no, w.ward_no, da.stage_no
         LIMIT 500'
    );
    $aStmt->execute($aParams);
    $allocations = $aStmt->fetchAll();
}

$settings = [];
foreach ([
    'support_phone',
    'support_email',
    'office_address',
    'site_manager_name',
    'site_manager_designation',
    'site_manager_email',
    'site_manager_mobile',
] as $key) {
    $settings[$key] = setting($key);
}

include __DIR__ . '/../includes/employee-header.php';
?>
<h1 class="h3 fw-bold mb-4">Master Management</h1>

<ul class="nav nav-tabs mb-4 no-print">
    <?php foreach ([
        'departments' => 'Departments',
        'categories' => 'Categories',
        'zones' => 'Zones',
        'wards' => 'Wards',
        'streets' => 'Streets',
        'officers' => 'Officers',
        'mappings' => 'Officer Mappings',
        'allocations' => 'Department Allocation',
        'settings' => 'Site Settings',
    ] as $key => $label): ?>
        <li class="nav-item"><a class="nav-link <?php echo $section === $key ? 'active' : ''; ?>" href="?section=<?php echo e($key); ?>"><?php echo e($label); ?></a></li>
    <?php endforeach; ?>
</ul>

<?php if ($section === 'departments'): ?>
<div class="row g-4">
    <div class="col-lg-4"><form method="post" class="card content-card p-4"><?php echo csrf_field(); ?><input type="hidden" name="action" value="add_department"><h2 class="h5 fw-bold">Add Department</h2><label class="form-label">Department Name</label><input class="form-control mb-3" name="department_name" required><button class="btn btn-primary">Save Department</button></form></div>
    <div class="col-lg-8"><?php $tableRows=$departments; $table='departments'; ?><div class="card content-card p-4"><div class="table-responsive"><table class="table table-bordered"><thead class="table-dark"><tr><th>Name</th><th>Status</th><th>Action</th></tr></thead><tbody><?php foreach ($tableRows as $row): ?><tr><td><?php echo e($row['department_name']); ?></td><td><?php echo (int)$row['is_active'] ? 'Active' : 'Inactive'; ?></td><td><form method="post"><?php echo csrf_field(); ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="table" value="<?php echo e($table); ?>"><input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>"><button class="btn btn-sm btn-outline-secondary">Toggle</button></form></td></tr><?php endforeach; ?></tbody></table></div></div></div>
</div>
<?php elseif ($section === 'categories'): ?>
<div class="row g-4">
    <div class="col-lg-4"><form method="post" class="card content-card p-4"><?php echo csrf_field(); ?><input type="hidden" name="action" value="add_category"><h2 class="h5 fw-bold">Add Complaint Category</h2><label class="form-label">Department</label><select class="form-select mb-3" name="department_id" required><option value="">Select</option><?php foreach ($departments as $row): ?><option value="<?php echo (int)$row['id']; ?>"><?php echo e($row['department_name']); ?></option><?php endforeach; ?></select><label class="form-label">Category Name</label><input class="form-control mb-3" name="category_name" required><label class="form-label">SLA Days</label><input class="form-control mb-3" type="number" min="1" name="sla_days" required><button class="btn btn-primary">Save Category</button></form></div>
    <div class="col-lg-8"><div class="card content-card p-4"><div class="table-responsive"><table class="table table-bordered"><thead class="table-dark"><tr><th>Department</th><th>Category</th><th>SLA</th><th>Status</th><th>Action</th></tr></thead><tbody><?php foreach ($categories as $row): ?><tr><td><?php echo e($row['department_name']); ?></td><td><?php echo e($row['category_name']); ?></td><td><?php echo (int)$row['sla_days']; ?></td><td><?php echo (int)$row['is_active'] ? 'Active' : 'Inactive'; ?></td><td><form method="post"><?php echo csrf_field(); ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="table" value="complaint_categories"><input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>"><button class="btn btn-sm btn-outline-secondary">Toggle</button></form></td></tr><?php endforeach; ?></tbody></table></div></div></div>
</div>
<?php elseif ($section === 'zones'): ?>
<div class="row g-4"><div class="col-lg-4"><form method="post" class="card content-card p-4"><?php echo csrf_field(); ?><input type="hidden" name="action" value="add_zone"><h2 class="h5 fw-bold">Add Zone</h2><label class="form-label">Zone Number</label><input class="form-control mb-3" name="zone_no" required><label class="form-label">Zone Name</label><input class="form-control mb-3" name="zone_name"><button class="btn btn-primary">Save Zone</button></form></div><div class="col-lg-8"><div class="card content-card p-4"><table class="table table-bordered"><thead class="table-dark"><tr><th>Zone</th><th>Name</th><th>Status</th></tr></thead><tbody><?php foreach ($zones as $row): ?><tr><td><?php echo e($row['zone_no']); ?></td><td><?php echo e($row['zone_name']); ?></td><td><?php echo (int)$row['is_active'] ? 'Active' : 'Inactive'; ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
<?php elseif ($section === 'wards'): ?>
<div class="row g-4"><div class="col-lg-4"><form method="post" class="card content-card p-4"><?php echo csrf_field(); ?><input type="hidden" name="action" value="add_ward"><h2 class="h5 fw-bold">Add Ward</h2><label class="form-label">Zone</label><select class="form-select mb-3" name="zone_id" required><option value="">Select</option><?php foreach ($zones as $row): ?><option value="<?php echo (int)$row['id']; ?>">Zone <?php echo e($row['zone_no']); ?></option><?php endforeach; ?></select><label class="form-label">Ward Number</label><input class="form-control mb-3" name="ward_no" required><label class="form-label">Ward Name</label><input class="form-control mb-3" name="ward_name"><button class="btn btn-primary">Save Ward</button></form></div><div class="col-lg-8"><div class="card content-card p-4"><table class="table table-bordered"><thead class="table-dark"><tr><th>Zone</th><th>Ward</th><th>Name</th><th>Status</th></tr></thead><tbody><?php foreach ($wards as $row): ?><tr><td><?php echo e($row['zone_no']); ?></td><td><?php echo e($row['ward_no']); ?></td><td><?php echo e($row['ward_name']); ?></td><td><?php echo (int)$row['is_active'] ? 'Active' : 'Inactive'; ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
<?php elseif ($section === 'streets'): ?>
<div class="row g-4"><div class="col-lg-4"><form method="post" class="card content-card p-4"><?php echo csrf_field(); ?><input type="hidden" name="action" value="add_street"><h2 class="h5 fw-bold">Add Street</h2><label class="form-label">Ward</label><select class="form-select mb-3" name="ward_id" required><option value="">Select</option><?php foreach ($wards as $row): ?><option value="<?php echo (int)$row['id']; ?>">Zone <?php echo e($row['zone_no']); ?> / Ward <?php echo e($row['ward_no']); ?></option><?php endforeach; ?></select><label class="form-label">Street Name</label><input class="form-control mb-3" name="street_name" required><button class="btn btn-primary">Save Street</button></form></div><div class="col-lg-8"><div class="card content-card p-4"><p class="text-muted">Showing the first 1,000 streets.</p><div class="table-responsive"><table class="table table-bordered"><thead class="table-dark"><tr><th>Street</th><th>Zone</th><th>Ward</th><th>Status</th></tr></thead><tbody><?php foreach ($streets as $row): ?><tr><td><?php echo e($row['street_name']); ?></td><td><?php echo e($row['zone_no']); ?></td><td><?php echo e($row['ward_no']); ?></td><td><?php echo (int)$row['is_active'] ? 'Active' : 'Inactive'; ?></td></tr><?php endforeach; ?></tbody></table></div></div></div></div>
<?php elseif ($section === 'officers'): ?>
<div class="row g-4"><div class="col-lg-4"><form method="post" class="card content-card p-4"><?php echo csrf_field(); ?><input type="hidden" name="action" value="add_officer"><h2 class="h5 fw-bold">Add Officer</h2><label class="form-label">Linked Employee User (optional)</label><select class="form-select mb-3" name="user_id"><option value="">None</option><?php foreach ($employeeUsers as $employeeUser): ?><option value="<?php echo (int)$employeeUser['id']; ?>"><?php echo e($employeeUser['full_name'] . ' — ' . $employeeUser['role']); ?></option><?php endforeach; ?></select><label class="form-label">Officer Name</label><input class="form-control mb-3" name="officer_name" required><label class="form-label">Designation</label><input class="form-control mb-3" name="designation"><label class="form-label">Mobile</label><input class="form-control mb-3" name="mobile_no"><label class="form-label">Email</label><input class="form-control mb-3" type="email" name="email"><button class="btn btn-primary">Save Officer</button></form></div><div class="col-lg-8"><div class="card content-card p-4"><div class="table-responsive"><table class="table table-bordered"><thead class="table-dark"><tr><th>Officer</th><th>Designation</th><th>Mobile</th><th>Email</th><th>Status</th></tr></thead><tbody><?php foreach ($officers as $row): ?><tr><td><?php echo e($row['officer_name']); ?></td><td><?php echo e($row['designation']); ?></td><td><?php echo e($row['mobile_no']); ?></td><td><?php echo e($row['email']); ?></td><td><?php echo (int)$row['is_active'] ? 'Active' : 'Inactive'; ?></td></tr><?php endforeach; ?></tbody></table></div></div></div></div>
<?php elseif ($section === 'mappings'): ?>
<div class="row g-4"><div class="col-lg-5"><form method="post" class="card content-card p-4"><?php echo csrf_field(); ?><input type="hidden" name="action" value="map_officer"><h2 class="h5 fw-bold">Assign Officer Responsibilities</h2><label class="form-label">Officer</label><select class="form-select mb-3" name="officer_id" required><option value="">Select</option><?php foreach ($officers as $row): ?><option value="<?php echo (int)$row['id']; ?>"><?php echo e($row['officer_name']); ?></option><?php endforeach; ?></select><label class="form-label">Department (optional)</label><select class="form-select mb-3" name="department_id"><option value="">None</option><?php foreach ($departments as $row): ?><option value="<?php echo (int)$row['id']; ?>"><?php echo e($row['department_name']); ?></option><?php endforeach; ?></select><label class="form-label">Category (optional)</label><select class="form-select mb-3" name="category_id"><option value="">None</option><?php foreach ($categories as $row): ?><option value="<?php echo (int)$row['id']; ?>"><?php echo e($row['department_name'] . ' / ' . $row['category_name']); ?></option><?php endforeach; ?></select><label class="form-label">Ward (optional)</label><select class="form-select mb-3" name="ward_id"><option value="">None</option><?php foreach ($wards as $row): ?><option value="<?php echo (int)$row['id']; ?>">Zone <?php echo e($row['zone_no']); ?> / Ward <?php echo e($row['ward_no']); ?></option><?php endforeach; ?></select><button class="btn btn-primary">Save Mapping</button></form></div><div class="col-lg-7"><div class="card content-card p-4"><div class="table-responsive"><table class="table table-bordered"><thead class="table-dark"><tr><th>Officer</th><th>Departments</th><th>Categories</th><th>Wards</th></tr></thead><tbody><?php foreach ($mappings as $row): ?><tr><td><?php echo e($row['officer_name']); ?></td><td><?php echo e($row['departments'] ?: '-'); ?></td><td><?php echo e($row['categories'] ?: '-'); ?></td><td><?php echo e($row['wards'] ?: '-'); ?></td></tr><?php endforeach; ?></tbody></table></div></div></div></div>
<?php elseif ($section === 'allocations'): ?>
<div class="row g-4">
    <div class="col-lg-4">
        <form method="post" class="card content-card p-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="save_allocation">
            <h2 class="h5 fw-bold mb-1">Auto-Allocation Rule</h2>
            <p class="small text-muted mb-3">Map Department + Category + Zone + Ward + Stage to the exact officer who should receive new complaints automatically.</p>

            <label class="form-label">Department</label>
            <select class="form-select mb-3" name="department_id" id="alloc_department_id" required>
                <option value="">Select Department</option>
                <?php foreach ($departments as $row): ?>
                    <option value="<?php echo (int)$row['id']; ?>"><?php echo e($row['department_name']); ?></option>
                <?php endforeach; ?>
            </select>

            <label class="form-label">Complaint Category</label>
            <select class="form-select mb-3" name="category_id" id="alloc_category_id" required>
                <option value="">Select Department First</option>
            </select>

            <label class="form-label">Zone</label>
            <select class="form-select mb-3" name="zone_id" id="alloc_zone_id" required>
                <option value="">Select Zone</option>
                <?php foreach ($zones as $row): ?>
                    <option value="<?php echo (int)$row['id']; ?>">Zone <?php echo e($row['zone_no']); ?><?php echo $row['zone_name'] ? ' - ' . e($row['zone_name']) : ''; ?></option>
                <?php endforeach; ?>
            </select>

            <label class="form-label">Ward</label>
            <select class="form-select mb-3" name="ward_id" id="alloc_ward_id" required>
                <option value="">Select Zone First</option>
            </select>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label">Stage</label>
                    <select class="form-select" name="stage_no" required>
                        <option value="1" selected>Stage 1 (Initial)</option>
                        <option value="2">Stage 2</option>
                        <option value="3">Stage 3</option>
                        <option value="4">Stage 4</option>
                        <option value="5">Stage 5</option>
                        <option value="6">Stage 6</option>

                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label">SLA Days</label>
                    <input class="form-control" type="number" min="1" max="365" name="escalation_days" value="3" required>
                </div>
            </div>

            <label class="form-label">Assigned Officer</label>
            <select class="form-select mb-3" name="officer_id" required>
                <option value="">Select Officer</option>
                <?php foreach ($officers as $row): ?>
                    <?php if ((int)$row['is_active'] === 1): ?>
                        <option value="<?php echo (int)$row['id']; ?>"><?php echo e($row['officer_name'] . ($row['mobile_no'] ? ' (' . $row['mobile_no'] . ')' : '')); ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>

            <button class="btn btn-primary w-100">Save Allocation Rule</button>
        </form>
    </div>

    <div class="col-lg-8">
        <div class="card content-card p-4">
            <form method="get" class="row g-2 align-items-end mb-3">
                <input type="hidden" name="section" value="allocations">
                <div class="col-md-4">
                    <label class="form-label small mb-1">Filter Department</label>
                    <select class="form-select form-select-sm" name="filter_dept">
                        <option value="0">All Departments</option>
                        <?php foreach ($departments as $row): ?>
                            <option value="<?php echo (int)$row['id']; ?>" <?php echo $allocFilterDept === (int)$row['id'] ? 'selected' : ''; ?>><?php echo e($row['department_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Filter Zone</label>
                    <select class="form-select form-select-sm" name="filter_zone">
                        <option value="0">All Zones</option>
                        <?php foreach ($zones as $row): ?>
                            <option value="<?php echo (int)$row['id']; ?>" <?php echo $allocFilterZone === (int)$row['id'] ? 'selected' : ''; ?>>Zone <?php echo e($row['zone_no']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Stage</label>
                    <select class="form-select form-select-sm" name="filter_stage">
                        <option value="0">All</option>
                        <?php for ($s = 1; $s <= 7; $s++): ?>
                            <option value="<?php echo $s; ?>" <?php echo $allocFilterStage === $s ? 'selected' : ''; ?>>Stage <?php echo $s; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-1">
                    <button class="btn btn-sm btn-primary">Filter</button>
                    <a class="btn btn-sm btn-outline-secondary" href="?section=allocations">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Department / Category</th>
                            <th>Zone / Ward</th>
                            <th>Stage</th>
                            <th>Officer</th>
                            <th>SLA</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$allocations): ?>
                            <tr><td colspan="7" class="text-center text-muted py-3">No custom allocation rules configured yet. New complaints will use the most specific ward+department officer mapping automatically until rules are added.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($allocations as $row): ?>
                            <tr>
                                <td><strong><?php echo e($row['department_name']); ?></strong><br><small class="text-muted"><?php echo e($row['category_name']); ?></small></td>
                                <td>Zone <?php echo e($row['zone_no']); ?> / Ward <?php echo e($row['ward_no']); ?></td>
                                <td><span class="badge text-bg-info">Stage <?php echo (int)$row['stage_no']; ?></span></td>
                                <td><?php echo e($row['officer_name']); ?><?php if ($row['officer_mobile']): ?><br><small class="text-muted"><?php echo e($row['officer_mobile']); ?></small><?php endif; ?></td>
                                <td><?php echo (int)$row['escalation_days']; ?>d</td>
                                <td><?php echo (int)$row['is_active'] ? '<span class="badge text-bg-success">Active</span>' : '<span class="badge text-bg-secondary">Inactive</span>'; ?></td>
                                <td class="text-nowrap">
                                    <form method="post" class="d-inline"><?php echo csrf_field(); ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="table" value="department_allocations"><input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>"><button class="btn btn-sm btn-outline-secondary">Toggle</button></form>
                                    <form method="post" class="d-inline" onsubmit="return confirm('Delete this allocation rule?');"><?php echo csrf_field(); ?><input type="hidden" name="action" value="delete_allocation"><input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>"><button class="btn btn-sm btn-outline-danger">Delete</button></form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
(function () {
    const allCategories = <?php echo json_encode(array_map(fn($c) => ['id' => (int)$c['id'], 'department_id' => (int)$c['department_id'], 'name' => $c['category_name']], $categories), JSON_UNESCAPED_UNICODE); ?>;
    const allWards = <?php echo json_encode(array_map(fn($w) => ['id' => (int)$w['id'], 'zone_id' => (int)$w['zone_id'], 'label' => 'Ward ' . $w['ward_no'] . ($w['ward_name'] ? ' - ' . $w['ward_name'] : '')], $wards), JSON_UNESCAPED_UNICODE); ?>;

    const deptEl = document.getElementById('alloc_department_id');
    const catEl = document.getElementById('alloc_category_id');
    const zoneEl = document.getElementById('alloc_zone_id');
    const wardEl = document.getElementById('alloc_ward_id');

    deptEl.addEventListener('change', function () {
        const did = parseInt(this.value || '0', 10);
        catEl.innerHTML = '<option value="">Select Category</option>';
        allCategories.filter(c => c.department_id === did).forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.textContent = c.name;
            catEl.appendChild(opt);
        });
    });

    zoneEl.addEventListener('change', function () {
        const zid = parseInt(this.value || '0', 10);
        wardEl.innerHTML = '<option value="">Select Ward</option>';
        if (zid > 0) {
            const allOpt = document.createElement('option');
            allOpt.value = 'all';
            allOpt.textContent = '-- All Active Wards in Selected Zone --';
            wardEl.appendChild(allOpt);
        }
        allWards.filter(w => w.zone_id === zid).forEach(w => {
            const opt = document.createElement('option');
            opt.value = w.id;
            opt.textContent = w.label;
            wardEl.appendChild(opt);
        });
    });
})();
</script>
<?php elseif ($section === 'settings'): ?>
<div class="card content-card p-4 mb-4">
    <h2 class="h5 fw-bold">Central Complaint Intake Officer</h2>
    <p class="text-muted">Choose a real, active complaint intake employee. Only if no eligible PDF specialist
    or department intake officer can be found will the system send complaints here for manual routing.
    This is not a PDF Stage 1 assignment.</p>
    <form method="post" class="row g-3 align-items-end">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="action" value="set_central_intake_officer">
        <div class="col-md-5">
            <label class="form-label" for="intake_officer_id">Intake Officer ID</label>
            <input id="intake_officer_id" name="intake_officer_id" type="number" min="2"
                   class="form-control" value="<?php echo e(setting('central_intake_officer_id')); ?>" required>
            <div class="form-text">Find the ID in Officer Directory; use the person designated to receive and redirect complaints.</div>
        </div>
        <div class="col-md-4">
            <button class="btn btn-primary">Save Intake Officer</button>
        </div>
    </form>
</div>
<div class="row g-4"><?php foreach ($settings as $key => $value): ?><div class="col-md-6"><form method="post" class="card content-card p-4 h-100"><?php echo csrf_field(); ?><input type="hidden" name="action" value="save_setting"><input type="hidden" name="setting_key" value="<?php echo e($key); ?>"><label class="form-label fw-bold"><?php echo e(ucwords(str_replace('_',' ',$key))); ?></label><?php if ($key === 'office_address'): ?><textarea class="form-control mb-3" name="setting_value" rows="4"><?php echo e($value); ?></textarea><?php else: ?><input class="form-control mb-3" name="setting_value" value="<?php echo e($value); ?>"><?php endif; ?><button class="btn btn-primary">Save Setting</button></form></div><?php endforeach; ?></div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/employee-footer.php'; ?>
