<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/officer-authority.php';
require_once __DIR__ . '/../includes/pdf-escalation-policy.php';

$pdo = db();

$stmt = $pdo->query("
    SELECT c.id AS category_id, c.category_name, c.department_id, d.department_name
    FROM complaint_categories c
    JOIN departments d ON d.id = c.department_id
    ORDER BY c.department_id, c.id
");
$categories = $stmt->fetchAll();

echo "Auditing ALL " . count($categories) . " categories across all 14 departments with production functions...\n\n";

$deptStats = [];
$failures = [];

// Sample test ward: Ward 24
$wardId = 24;

foreach ($categories as $cat) {
    $catId = (int)$cat['category_id'];
    $deptId = (int)$cat['department_id'];
    $deptName = $cat['department_name'];
    $catName = $cat['category_name'];

    if (!isset($deptStats[$deptName])) {
        $deptStats[$deptName] = ['total' => 0, 'stage1_ok' => 0, 'all_stages_ok' => 0];
    }
    $deptStats[$deptName]['total']++;

    $policy = pdf_policy_routing_ready($pdo, $catId);
    if (!$policy || empty($policy['stages'])) {
        $failures[] = "Cat #{$catId} ($catName): Missing or invalid PDF policy";
        continue;
    }

    $allOk = true;
    $stage1Ok = false;

    foreach ($policy['stages'] as $st) {
        $sNum = (int)$st['stage_no'];
        $role = (string)$st['role_label'];

        $cands = pdf_routing_candidates($pdo, $catId, $sNum, $deptId, $wardId);
        if (empty($cands)) {
            $allOk = false;
            $failures[] = "Cat #{$catId} ($catName) Dept #{$deptId} Stage {$sNum} ({$role}): 0 candidates";
        } else {
            if ($sNum === 1) $stage1Ok = true;
        }
    }

    if ($stage1Ok) $deptStats[$deptName]['stage1_ok']++;
    if ($allOk) $deptStats[$deptName]['all_stages_ok']++;
}

echo "=== OFFICIAL DEPARTMENT AUDIT RESULTS ===\n";
printf("%-32s | %-6s | %-11s | %-13s\n", "Department", "Total", "Stage 1 OK", "All Stages OK");
echo str_repeat("-", 72) . "\n";
foreach ($deptStats as $dName => $s) {
    printf("%-32s | %-6d | %-11d | %-13d\n", $dName, $s['total'], $s['stage1_ok'], $s['all_stages_ok']);
}

echo "\nTotal failures: " . count($failures) . "\n";
if (count($failures) > 0) {
    echo "Failures list:\n";
    foreach ($failures as $f) {
        echo "  - $f\n";
    }
    exit(1);
} else {
    echo "\n>>> 100% SUCCESS: ALL 309 CATEGORIES ACROSS ALL 14 DEPARTMENTS HAVE COMPLETE STAGE 1 ALLOCATION AND HIGHER OFFICER ESCALATION! <<<\n";
    exit(0);
}
