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

/** Render only approved free text, known safe imported status text, or a
 * neutral summary of the recorded status. Never expose raw internal notes. */
function citizen_status_event(array $row): array
{
    $status = (string)$row['new_status'];
    $safeStatus = in_array($status, ['Registered','Assigned','Acknowledged','In Progress','Resolved','Closed','Rejected','Reopened','Pending'], true)
        ? $status : 'Updated';
    $remark = '';
    $kind = 'Recorded status update';
    if ((int)($row['can_show_text'] ?? 0) === 1) {
        $remark = trim((string)($row['remarks'] ?? ''));
        if ($remark !== '') $kind = 'Municipal staff update';
    }
    if ($remark === '') {
        // Imported status placeholders carry no private free text.
        if (($row['visibility'] ?? null) !== 'citizen_remark_private'
            && trim((string)($row['remarks'] ?? '')) === 'Status updated in ICTS system.') {
            $remark = 'Status updated in ICTS system.';
        } else {
            $remark = $safeStatus === 'Updated' ? 'Complaint status updated.' : 'Complaint status recorded as '.$safeStatus.'.';
        }
    }
    return ['new_status'=>$safeStatus,'changed_at'=>$row['changed_at'],'remarks'=>$remark,'event_label'=>$kind];
}

/** Public progress includes all recorded status events, with free text gated
 * by explicit publication or verified-owner access to older audited officer updates. */
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
    $stmt = $pdo->prepare("SELECT v.new_status,v.changed_at,v.remarks,v.visibility,
          CASE WHEN ($allowed) THEN 1 ELSE 0 END AS can_show_text FROM (
        SELECT h.id,h.complaint_id,h.changed_by,h.old_status,h.new_status,h.changed_at,h.remarks,
               $visibility AS visibility
        FROM complaint_status_history h WHERE h.complaint_id=?
      ) v ORDER BY v.changed_at ASC,v.id ASC");
    $stmt->execute([$complaintId]);
    return array_map('citizen_status_event',$stmt->fetchAll(PDO::FETCH_ASSOC));
}
