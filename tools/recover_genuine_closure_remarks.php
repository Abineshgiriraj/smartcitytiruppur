<?php
declare(strict_types=1);

/**
 * Recover Genuine Historical Closure Remarks from raw_complaints
 * into complaint_status_history.
 *
 * Uses transaction protection and batching.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}

require_once __DIR__ . '/../includes/bootstrap.php';
$pdo = db();

echo "Starting recovery of genuine historical closure remarks from raw_complaints...\n";

$pdo->beginTransaction();
try {
    $inserted = $pdo->exec("
        INSERT INTO complaint_status_history (complaint_id, old_status, new_status, remarks, changed_by, changed_at)
        SELECT c.id, 
               'In Progress', 
               'Closed', 
               TRIM(rc.Remarks), 
               NULL, 
               COALESCE(c.closed_at, rc.ResolveDateTime, c.updated_at, c.created_at)
        FROM complaints c
        JOIN raw_complaints rc ON rc.ComplaintID = c.id
        WHERE rc.Remarks IS NOT NULL 
          AND TRIM(rc.Remarks) != ''
          AND NOT EXISTS (
              SELECT 1 FROM complaint_status_history h 
              WHERE h.complaint_id = c.id AND h.new_status = 'Closed'
          )
    ");
    
    $pdo->commit();
    echo "SUCCESS: Recovered $inserted genuine closure remarks into complaint_status_history.\n";
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "FATAL ERROR during remarks recovery: " . $e->getMessage() . "\n";
    exit(1);
}
