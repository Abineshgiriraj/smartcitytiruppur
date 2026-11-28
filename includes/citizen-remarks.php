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

function citizen_status_event(array $row): array
{
    $status=(string)($row['new_status']??'Updated');
    $status=in_array($status,['Registered','Assigned','Acknowledged','In Progress','Resolved','Closed','Rejected','Reopened','Pending'],true)?$status:'Updated';
    $raw=trim((string)($row['remarks']??''));
    $published=(int)($row['can_show_text']??0)===1 && ($row['visibility']??'')!=='citizen_remark_private';
    $text=$published?$raw:'';
    if ($text==='' && ($row['visibility']??'')!=='citizen_remark_private' && $raw==='Status updated in ICTS system.') $text=$raw;
    if ($text==='' && ($row['visibility']??'')!=='citizen_remark_private' && $raw==='Complaint registered through the public website.') $text=$raw;
    if ($text==='') $text=$status==='Updated'?'Complaint status updated.':'Complaint status recorded as '.$status.'.';
    return ['new_status'=>$status,'changed_at'=>$row['changed_at'],'remarks'=>$text,
        'event_label'=>$published&&$raw!==''?'Municipal staff update':'Recorded status update',
        'officer_name'=>$published?(string)($row['actor_name']??''):'',
        'designation'=>$published?(string)($row['actor_designation']??''):''];
}

function get_complaint_public_timeline(PDO $pdo,int $complaintId): array
{
    $visibility=citizen_remark_visibility_sql();
    $stmt=$pdo->prepare("SELECT h.new_status,h.changed_at,h.remarks,
        COALESCE(NULLIF(o.officer_name,''),NULLIF(u.full_name,''),'') AS actor_name,
        COALESCE(NULLIF(o.designation,''),u.role,'') AS actor_designation,
        $visibility AS visibility
        FROM complaint_status_history h
        JOIN complaints c ON c.id=h.complaint_id AND c.is_deleted=0
        LEFT JOIN users u ON u.id=h.changed_by
        LEFT JOIN officers o ON o.user_id=u.id
        WHERE h.complaint_id=? ORDER BY h.changed_at,h.id");
    $stmt->execute([$complaintId]);
    $out=[];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $row['can_show_text']=$row['visibility']==='citizen_remark_published'?1:0;
        $out[]=citizen_status_event($row);
    }
    return $out;
}
function citizen_remarks(PDO $pdo,int $complaintId,bool $verifiedOwner): array
{
    return get_complaint_public_timeline($pdo,$complaintId);
}
