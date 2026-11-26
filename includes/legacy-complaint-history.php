<?php
declare(strict_types=1);

// Historical reporting is deliberately separate from ownership and write access.
function legacy_history_available(PDO $pdo): bool
{
    $stmt = $pdo->query("SELECT COUNT(*) FROM information_schema.TABLES
        WHERE TABLE_SCHEMA=DATABASE() AND TABLE_TYPE='BASE TABLE'
          AND TABLE_NAME IN ('legacy_complaint_history','legacy_complaint_history_approvals')");
    return (int)$stmt->fetchColumn() === 2;
}

// No raw user credentials, citizen contact data or attachments are exposed.
// Pin the approval to both the officer and the login originally reviewed.
// An inactive/relinked account or revoked approval immediately loses access.
function legacy_history_relationship_sql(): string
{
    return "SELECT a.officer_id, a.login_user_id, h.complaint_id,
                   MAX(h.source_action_at) AS last_action_at
            FROM legacy_complaint_history h
            JOIN legacy_complaint_history_approvals a ON a.id=h.approval_id
            JOIN officers o ON o.id=a.officer_id AND o.user_id=a.login_user_id AND o.is_active=1
            JOIN users u ON u.id=a.login_user_id AND u.is_active=1
                AND u.role IN ('Field Officer','Officer','Super Admin','Admin')
            JOIN complaints lc ON lc.id=h.complaint_id AND lc.is_deleted=0
            WHERE a.revoked_at IS NULL
            GROUP BY a.officer_id,a.login_user_id,h.complaint_id";
}

function legacy_history_counts(PDO $pdo, int $officerId, int $loginUserId): ?array
{
    if (!legacy_history_available($pdo)) return null;
    $history = legacy_history_relationship_sql();
    $stmt = $pdo->prepare("SELECT COUNT(*) AS lifetime_total,
        COALESCE(SUM(x.is_historical),0) AS historical_total,
        COALESCE(SUM(x.is_historical AND x.is_current=0),0) AS additional_history
        FROM (SELECT complaint_id,MAX(is_historical) is_historical,MAX(is_current) is_current
              FROM (SELECT c.id complaint_id,0 is_historical,1 is_current
                    FROM complaints c WHERE c.is_deleted=0 AND c.assigned_officer_id=?
                    UNION ALL SELECT h.complaint_id,1,0 FROM ($history) h
                    WHERE h.officer_id=? AND h.login_user_id=?) combined
              GROUP BY complaint_id) x");
    $stmt->execute([$officerId,$officerId,$loginUserId]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}
