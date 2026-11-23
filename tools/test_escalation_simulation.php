<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/officer-authority.php';
require_once __DIR__ . '/../includes/pdf-escalation-policy.php';

$pdo = db();
$complaintId = 970103;
$catId = 329;
$deptId = 7;
$wardId = 17;

echo "--- Testing Full Escalation Path for TCMC-2026-970103 ($catId UGD-Others) ---\n";
$policy = pdf_policy_routing_ready($pdo, $catId);
echo "Policy: {$policy['stage_count']} stages, total {$policy['total_days']} days\n";

foreach ($policy['stages'] as $st) {
    $sNum = (int)$st['stage_no'];
    $role = (string)$st['role_label'];
    $days = (int)$st['period_days'];

    $candidates = pdf_routing_candidates($pdo, $catId, $sNum, $deptId, $wardId);
    $cList = [];
    foreach ($candidates as $c) {
        $cList[] = "ID {$c['id']} ({$c['officer_name']} - {$c['designation']})";
    }
    echo "Stage $sNum [Role: $role, Days: {$days}d]: " . count($candidates) . " candidate(s) => " . implode('; ', $cList) . "\n";
}
