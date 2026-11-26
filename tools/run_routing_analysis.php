<?php
declare(strict_types=1);

/**
 * CLI Tool for Historical Complaint Routing Analysis & Reassignment Preview
 *
 * Usage:
 *   php tools/run_routing_analysis.php --dry-run
 *   php tools/run_routing_analysis.php --reconstruct-all
 *   php tools/run_routing_analysis.php --stats
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/complaint-routing-engine.php';

$pdo = db();
$engine = new ComplaintRoutingEngine($pdo);

$mode = 'dry-run';
$limit = 1000;
$offset = 0;

foreach (array_slice($argv, 1) as $arg) {
    if ($arg === '--reconstruct-all') $mode = 'reconstruct-all';
    if ($arg === '--stats') $mode = 'stats';
    if ($arg === '--dry-run') $mode = 'dry-run';
    if (preg_match('/^--limit=(\d+)$/', $arg, $m)) $limit = (int)$m[1];
    if (preg_match('/^--offset=(\d+)$/', $arg, $m)) $offset = (int)$m[1];
}

echo "========================================================\n";
echo " Smart City Tiruppur — Complaint Routing Analysis Tool  \n";
echo " Mode: $mode | Limit: $limit | Offset: $offset          \n";
echo "========================================================\n\n";

if ($mode === 'stats') {
    printStats($pdo);
    exit(0);
}

if ($mode === 'dry-run') {
    echo "Running dry-run analysis on open unassigned and inactive complaints...\n";
    
    // Analyze open unassigned complaints
    $openUnassigned = $pdo->query("
        SELECT * FROM complaints 
        WHERE is_deleted = 0 
          AND status NOT IN ('Closed', 'Resolved', 'Rejected') 
          AND (assigned_officer_id IS NULL OR assigned_officer_id = 0)
        LIMIT $limit OFFSET $offset
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($openUnassigned) . " open unassigned complaints in range.\n";
    $proposedCount = 0;
    $sourceStats = [];

    foreach ($openUnassigned as $c) {
        $plan = $engine->proposeReassignmentForOpenComplaint($c);
        if ($plan) {
            $proposedCount++;
            $src = $plan['evidence_source'];
            $sourceStats[$src] = ($sourceStats[$src] ?? 0) + 1;
        }
    }

    echo "Dry-run proposed reassignments: $proposedCount / " . count($openUnassigned) . "\n";
    echo "Source breakdown:\n";
    foreach ($sourceStats as $src => $cnt) {
        echo " - $src: $cnt\n";
    }

    // Analyze open complaints assigned to inactive officers
    $openInactive = $pdo->query("
        SELECT c.* FROM complaints c 
        JOIN officers o ON o.id = c.assigned_officer_id 
        WHERE c.is_deleted = 0 
          AND c.status NOT IN ('Closed', 'Resolved', 'Rejected') 
          AND o.is_active = 0
        LIMIT $limit
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo "\nOpen complaints assigned to inactive officers: " . count($openInactive) . "\n";
    $inactiveProposed = 0;
    foreach ($openInactive as $c) {
        $plan = $engine->proposeReassignmentForOpenComplaint($c);
        if ($plan) $inactiveProposed++;
    }
    echo "Dry-run proposed replacements: $inactiveProposed / " . count($openInactive) . "\n";

    printStats($pdo);
    exit(0);
}

if ($mode === 'reconstruct-all') {
    echo "Beginning batch reconstruction of calculated routing and reassignment plans...\n";
    $total = (int)$pdo->query("SELECT COUNT(*) FROM complaints WHERE is_deleted = 0")->fetchColumn();
    echo "Total complaints to process: $total\n";

    $batchSize = 2000;
    $processedTotal = 0;

    for ($i = 0; $i < $total; $i += $batchSize) {
        $startTime = microtime(true);
        $count = $engine->runReconstructionBatch($batchSize, $i);
        $elapsed = round(microtime(true) - $startTime, 2);
        $processedTotal += $count;
        echo "Processed batch $i to " . ($i + $count) . " ($count records in {$elapsed}s). Total: $processedTotal / $total\n";
    }

    echo "\nReconstruction completed successfully!\n";
    printStats($pdo);
}

function printStats(PDO $pdo): void
{
    echo "\n----------------- SYSTEM STATS -----------------\n";
    $routesCount = (int)$pdo->query("SELECT COUNT(*) FROM complaint_calculated_routing")->fetchColumn();
    $plansCount = (int)$pdo->query("SELECT COUNT(*) FROM complaint_reassignment_plans")->fetchColumn();
    $plansProposed = (int)$pdo->query("SELECT COUNT(*) FROM complaint_reassignment_plans WHERE plan_status = 'proposed'")->fetchColumn();
    $plansApplied = (int)$pdo->query("SELECT COUNT(*) FROM complaint_reassignment_plans WHERE plan_status = 'applied'")->fetchColumn();

    echo "Calculated Routing records: $routesCount\n";
    echo "Reassignment Plans: $plansCount (Proposed: $plansProposed, Applied: $plansApplied)\n";
    echo "------------------------------------------------\n";
}
