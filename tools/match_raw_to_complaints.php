<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$pdo = db();

echo "Checking how raw_complaints matches complaints:\n";
// Let's check ComplaintID vs complaints.id or reference_no
$sampleRaw = $pdo->query("SELECT ComplaintID, ComplaintReferenceNo, Status, Remarks FROM raw_complaints WHERE Remarks IS NOT NULL AND TRIM(Remarks) != '' LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
foreach ($sampleRaw as $sr) {
    echo "Raw ComplaintID {$sr['ComplaintID']}, Ref {$sr['ComplaintReferenceNo']}:\n";
    $c = $pdo->query("SELECT id, reference_no, status FROM complaints WHERE id = {$sr['ComplaintID']} OR reference_no = '{$sr['ComplaintReferenceNo']}' OR reference_no = '{$sr['ComplaintID']}'")->fetch(PDO::FETCH_ASSOC);
    print_r($c);
    
    // Check complaint_status_history for this complaint
    if ($c) {
        $csh = $pdo->query("SELECT * FROM complaint_status_history WHERE complaint_id = {$c['id']}")->fetchAll(PDO::FETCH_ASSOC);
        echo "  complaint_status_history rows: " . count($csh) . "\n";
        foreach ($csh as $h) {
            echo "    [{$h['changed_at']}] {$h['new_status']}: {$h['remarks']}\n";
        }
    }
}
