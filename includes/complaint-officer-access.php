<?php
declare(strict_types=1);

/**
 * Verify prior complaint ownership for an authenticated municipal officer.
 *
 * An actual complaint transfer may be recorded in complaint_assignments, or
 * in complaint_escalations when older assignment flags have become stale.
 * Never rely on inferred stage titles or a prospective eligible officer.
 * Caller must enforce authenticated officer status and non-deleted complaint.
 */
function complaint_officer_was_assigned(
    PDO $pdo,
    int $complaintId,
    int $officerId,
    ?int $currentOfficerId,
    string $referenceNo
): bool {
    if ($complaintId < 1 || $officerId < 1
        || !str_starts_with($referenceNo, 'TCMC-2026-')
        || $currentOfficerId === $officerId) {
        return false;
    }

    // Don't trust is_current alone: some historical assignments have stale
    // flags. A recorded assignment to another current owner proves prior
    // handling, even if that old flag was not updated.
    $stmt = $pdo->prepare(
        'SELECT
            EXISTS (
                SELECT 1 FROM complaint_assignments a
                WHERE a.complaint_id=? AND a.officer_id=?
            ) AS historical_assignment,
            EXISTS (
                SELECT 1 FROM complaint_escalations ce
                WHERE ce.complaint_id=? AND ce.officer_id=?
                  AND ce.level_no BETWEEN 2 AND 6
            ) AS historical_escalation'
    );
    $stmt->execute([$complaintId, $officerId, $complaintId, $officerId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result !== false
        && ((int)$result['historical_assignment'] === 1
            || (int)$result['historical_escalation'] === 1);
}
