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

/** Resolve historical responsibility using exact saved ownership or a unique dated interval.
 * Never substitute today's officer for an earlier event without evidence. */
function citizen_responsible_officer(array $assignments, string $at): ?array
{
    if ($at==='') return null;
    $matches=[];
    foreach ($assignments as $a) {
        if (empty($a['assigned_at']) || $a['assigned_at']>$at) continue;
        if (!empty($a['unassigned_at']) && $a['unassigned_at']<=$at) continue;
        if (empty($a['unassigned_at']) && (int)$a['is_current']!==1) continue;
        $matches[(int)$a['officer_id']]=$a;
    }
    // A same-second transfer can be ambiguous; new manual events have explicit snapshots.
    if (count($matches)!==1) return null;
    foreach ($assignments as $a) {
        if (($a['unassigned_at']??null)===$at && !isset($matches[(int)$a['officer_id']])) return null;
    }
    return reset($matches);
}

function citizen_record_responsibility(PDO $pdo,int $historyId,int $actorId,int $officerId): void
{
    if (!$pdo->inTransaction() || $historyId<=0 || $actorId<=0) throw new LogicException('Invalid responsibility snapshot.');
    $pdo->prepare("INSERT INTO audit_logs (user_id,action,entity_type,entity_id,details_json)
       VALUES (?,'citizen_responsible_officer','complaint_status_history',?,?)")
       ->execute([$actorId,$historyId,json_encode(['officer_id'=>$officerId],JSON_THROW_ON_ERROR)]);
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
        'officer_name'=>(string)($row['responsible_name']??''),
        'designation'=>(string)($row['responsible_designation']??'')];
}

function get_complaint_public_timeline(PDO $pdo,int $complaintId): array
{
    $visibility=citizen_remark_visibility_sql();
    $stmt=$pdo->prepare("SELECT h.id,h.new_status,h.changed_at,h.remarks,
        $visibility AS visibility,
        (SELECT a.details_json FROM audit_logs a WHERE a.entity_type='complaint_status_history'
          AND a.entity_id=h.id AND a.action='citizen_responsible_officer' ORDER BY a.id DESC LIMIT 1) AS responsibility
        FROM complaint_status_history h JOIN complaints c ON c.id=h.complaint_id AND c.is_deleted=0
        WHERE h.complaint_id=? ORDER BY h.changed_at,h.id");
    $stmt->execute([$complaintId]);$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt=$pdo->prepare("SELECT a.officer_id,a.assigned_at,a.unassigned_at,a.is_current,o.officer_name,o.designation
        FROM complaint_assignments a JOIN officers o ON o.id=a.officer_id
        WHERE a.complaint_id=? ORDER BY a.assigned_at,a.id");
    $stmt->execute([$complaintId]);$assignments=$stmt->fetchAll(PDO::FETCH_ASSOC);
    $lookup=$pdo->prepare('SELECT officer_name,designation FROM officers WHERE id=?');
    $out=[];
    foreach ($rows as $row) {
        $person=null;
        if ($row['responsibility']!==null) {
            $snapshot=json_decode($row['responsibility'],true);
            $officerId=(int)($snapshot['officer_id']??0);
            if ($officerId>0) {
                $lookup->execute([$officerId]);$person=$lookup->fetch(PDO::FETCH_ASSOC)?:null;
            }
        } else {
            $person=citizen_responsible_officer($assignments,(string)$row['changed_at']);
        }
        // Generic intake or administrative identities are not field officers.
        $designation=(string)($person['designation']??'');
        if (preg_match('/operator|call.?cent|corporation officer|admin|tester/i',$designation)) $person=null;
        $row['responsible_name']=$person['officer_name']??'';
        $row['responsible_designation']=$person['designation']??'';
        $row['can_show_text']=$row['visibility']==='citizen_remark_published'?1:0;
        $out[]=citizen_status_event($row);
    }
    return $out;
}
function citizen_remarks(PDO $pdo,int $complaintId,bool $verifiedOwner): array
{
    return get_complaint_public_timeline($pdo,$complaintId);
}
