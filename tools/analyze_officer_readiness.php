<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/pdf-escalation-policy.php';
require_once __DIR__ . '/../includes/officer-authority.php';

$pdo = db();
$dbName = (string)$pdo->query('SELECT DATABASE()')->fetchColumn();
echo "Analyzing officer readiness on {$dbName}...\n";

// Load all categories with PDF policies
$policies = $pdo->query(
    "SELECT p.category_id, p.stage_count, p.total_days, p.is_approved,
            cc.category_name, cc.department_id, d.department_name
     FROM pdf_escalation_policies p
     JOIN complaint_categories cc ON cc.id = p.category_id
     JOIN departments d ON d.id = cc.department_id
     ORDER BY d.department_name, cc.category_name"
)->fetchAll(PDO::FETCH_ASSOC);

echo "Total PDF Policies: " . count($policies) . "\n";

// Let's analyze Cat 294 (Poor Lighting) specifically first
$cat294Stages = $pdo->query(
    "SELECT s.stage_no, s.role_label, s.period_days
     FROM pdf_escalation_policy_stages s
     WHERE s.category_id = 294
     ORDER BY s.stage_no"
)->fetchAll(PDO::FETCH_ASSOC);

echo "\n--- Candidate Analysis for Category 294 (Poor Lighting) ---\n";
foreach ($cat294Stages as $st) {
    $stageNo = (int)$st['stage_no'];
    $role = (string)$st['role_label'];

    // Find active officers whose designation matches role
    $stmt = $pdo->prepare(
        "SELECT o.id, o.officer_name, o.designation,
                EXISTS(SELECT 1 FROM officer_department_map dm WHERE dm.officer_id = o.id AND dm.department_id = 3) as in_dept,
                EXISTS(SELECT 1 FROM officer_category_map cm WHERE cm.officer_id = o.id AND cm.category_id = 294) as in_cat,
                (SELECT GROUP_CONCAT(wm.ward_id ORDER BY wm.ward_id SEPARATOR ',') FROM officer_ward_map wm WHERE wm.officer_id = o.id) as wards
         FROM officers o
         WHERE o.is_active = 1
         ORDER BY o.id"
    );
    $stmt->execute();
    $allActive = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $matching = [];
    foreach ($allActive as $o) {
        if (pdf_policy_role_matches($role, (string)$o['designation'])) {
            $matching[] = $o;
        }
    }

    echo "Stage {$stageNo} ({$role}, {$st['period_days']}d): Total matching role = " . count($matching) . "\n";
    foreach ($matching as $m) {
        $wardList = $m['wards'] ?: 'All (City-wide)';
        $deptCat = ($m['in_dept'] ? 'Dept:YES ' : 'Dept:NO ') . ($m['in_cat'] ? 'Cat:YES' : 'Cat:NO');
        echo "   -> Officer #{$m['id']} {$m['officer_name']} [{$m['designation']}] | {$deptCat} | Wards: {$wardList}\n";
    }
}
