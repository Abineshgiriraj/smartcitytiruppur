<?php
declare(strict_types=1);

function citizen_remark_visibility_sql(): string
{
    return "(SELECT a.action FROM audit_logs a
      WHERE a.entity_type='complaint_status_history' AND a.entity_id=h.id
        AND a.action IN ('citizen_remark_published','citizen_remark_private')
      ORDER BY a.id DESC LIMIT 1)";
}

function citizen_remark_record_visibility(PDO $pdo, int $historyId, int $userId, bool $publish): void
{
    if (!$pdo->inTransaction() || $historyId <= 0 || $userId <= 0) {
        throw new LogicException('Remark visibility requires a valid history row and transaction.');
    }
    $pdo->prepare("INSERT INTO audit_logs (user_id,action,entity_type,entity_id)
      VALUES (?,?,'complaint_status_history',?)")
      ->execute([$userId,$publish?'citizen_remark_published':'citizen_remark_private',$historyId]);
}

/** Only explicitly published rows are visible to reference-only visitors.
 * Older officer updates with matching audit evidence are available to verified owners.
 */
function citizen_remarks(PDO $pdo, int $complaintId, bool $verifiedOwner): array
{
    $visibility = citizen_remark_visibility_sql();
    $allowed = "v.visibility='citizen_remark_published'";
    if ($verifiedOwner) {
        $allowed .= " OR (v.visibility IS NULL AND EXISTS (
          SELECT 1 FROM audit_logs a WHERE a.entity_type='complaint' AND a.entity_id=v.complaint_id
            AND a.action='officer_update_complaint' AND a.user_id=v.changed_by
            AND a.created_at BETWEEN v.changed_at AND DATE_ADD(v.changed_at,INTERVAL 10 SECOND)
            AND JSON_UNQUOTE(JSON_EXTRACT(IF(JSON_VALID(a.details_json),a.details_json,'{}'),'$.old_status'))=v.old_status
            AND JSON_UNQUOTE(JSON_EXTRACT(IF(JSON_VALID(a.details_json),a.details_json,'{}'),'$.new_status'))=v.new_status
        ) AND NOT EXISTS (
          SELECT 1 FROM complaint_status_history h2 WHERE h2.complaint_id=v.complaint_id
            AND h2.changed_by=v.changed_by AND h2.id<>v.id AND h2.old_status=v.old_status AND h2.new_status=v.new_status
            AND h2.changed_at BETWEEN DATE_SUB(v.changed_at,INTERVAL 10 SECOND) AND DATE_ADD(v.changed_at,INTERVAL 10 SECOND)
        ))";
    }
    $stmt = $pdo->prepare("SELECT v.new_status,v.changed_at,v.remarks FROM (
        SELECT h.id,h.complaint_id,h.changed_by,h.old_status,h.new_status,h.changed_at,h.remarks,
               $visibility AS visibility
        FROM complaint_status_history h WHERE h.complaint_id=? AND h.changed_by IS NOT NULL
          AND h.remarks IS NOT NULL AND TRIM(h.remarks)<>''
      ) v WHERE ($allowed) ORDER BY v.changed_at ASC,v.id ASC");
    $stmt->execute([$complaintId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
