<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$pdo = db();
$stmt = $pdo->prepare(
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
     WHERE c.reference_no = 'TCMC-2026-970103' AND c.is_deleted = 0 LIMIT 1"
);
$stmt->execute();
$c = $stmt->fetch();
echo "Grievance ID: " . $c['reference_no'] . "\n";
echo "Status: " . $c['status'] . "\n";
echo "Department: " . $c['department_name'] . "\n";
echo "Category: " . $c['category_name'] . "\n";
echo "Zone & Ward: " . $c['zone_no'] . " - " . $c['ward_no'] . "\n";
echo "Grievance Allotted Employee Name: " . ($c['officer_name'] ?: 'Pending authorized officer allocation') . "\n";
echo "Grievance Allotted Employee Mobile: " . mask_mobile($c['officer_mobile']) . "\n";
