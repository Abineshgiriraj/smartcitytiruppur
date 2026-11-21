<?php
declare(strict_types=1);
/**
 * Read-only assignment readiness inspection.
 *
 * Usage: php tests/check_assignment_readiness.php --expect-db=smartcitytiruppur
 *        php tests/check_assignment_readiness.php --expect-db=smartcity_escalation_test
 *
 * Never mutates complaints, approvals or SQL schema. Prints aggregate counts only.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}
$expected = null;
foreach (array_slice($argv, 1) as $argument) {
    if (str_starts_with($argument, '--expect-db=')) {
        $expected = substr($argument, strlen('--expect-db='));
    }
}
if ($expected === null || !preg_match('/^[A-Za-z0-9_]+$/D', $expected)) {
    fwrite(STDERR, "Specify --expect-db=<exact_database_name>\n");
    exit(2);
}
require_once __DIR__.'/../includes/bootstrap.php';
$pdo = db();
$actual = (string)$pdo->query('SELECT DATABASE()')->fetchColumn();
if ($actual !== $expected) {
    fwrite(STDERR, "Database mismatch: expected {$expected}, connected to {$actual}. Nothing changed.\n");
    exit(2);
}
echo "Read-only assignment readiness: {$actual}\n";
$exists = $pdo->prepare(
    'SELECT COUNT(*) FROM information_schema.TABLES
     WHERE TABLE_SCHEMA=? AND TABLE_NAME=?'
);
$exists->execute([$actual,'officer_stage_approvals']);
$hasRegistry = ((int)$exists->fetchColumn() > 0);
echo "Approval registry: ".($hasRegistry?'INSTALLED':'MISSING')."\n";
if ($hasRegistry) {
    $count = (int)$pdo->query(
        "SELECT COUNT(*) FROM officer_stage_approvals
         WHERE stage_no=1 AND revoked_at IS NULL AND TRIM(approval_reference) <> ''"
    )->fetchColumn();
    echo "Stage 1 approval records (not independent authority verification): {$count}\n";
}
$triggers = $pdo->prepare(
    "SELECT COUNT(*) FROM information_schema.TRIGGERS
     WHERE TRIGGER_SCHEMA=? AND EVENT_OBJECT_TABLE='complaints'
       AND TRIGGER_NAME='trg_complaints_auto_assign'"
);
$triggers->execute([$actual]);
$triggerFound = (int)$triggers->fetchColumn() > 0;
echo "Unsafe historical complaint auto-assign trigger: ".($triggerFound?'PRESENT — REVIEW REQUIRED':'ABSENT')."\n";
$counts = $pdo->query(
    "SELECT COUNT(*) AS total,
            COALESCE(SUM(assigned_officer_id IS NULL
              AND status NOT IN ('Resolved','Closed','Rejected')),0) AS pending_unassigned,
            COALESCE(SUM(assigned_officer_id IS NOT NULL),0) AS with_officer
     FROM complaints WHERE is_deleted=0"
)->fetch(PDO::FETCH_ASSOC);
echo "Total nondeleted complaints: ".(int)$counts['total']."\n";
echo "Open unassigned complaints: ".(int)$counts['pending_unassigned']."\n";
echo "Complaints with officer ID set: ".(int)$counts['with_officer']."\n";
echo "No mutations executed.\n";
exit(($hasRegistry && !$triggerFound)?0:3);
