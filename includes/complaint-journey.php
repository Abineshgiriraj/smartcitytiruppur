<?php
declare(strict_types=1);
require_once __DIR__.'/pdf-escalation-policy.php';

/** Match the existing public department directory; never publish inactive or unmapped profile contacts. */
function complaint_journey_contact(array $person, bool $staff): array
{
    $phone=trim((string)($person['mobile_no']??''));
    $email=trim((string)($person['email']??''));
    $listed=(int)($person['public_contact']??0)===1;
    // Personal email providers are not evidence of a published official mailbox.
    $officialEmail=filter_var($email,FILTER_VALIDATE_EMAIL) && preg_match('/@(?:[a-z0-9-]+\.)*(?:gov\.in|nic\.in)$/i',$email);
    return [
        'phone'=>($staff||$listed) && preg_match('/^\+?[0-9][0-9 ()-]{5,19}$/D',$phone) ? $phone : '',
        'email'=>($staff||($listed&&$officialEmail)) && filter_var($email,FILTER_VALIDATE_EMAIL) ? $email : '',
    ];
}
function complaint_journey_location(array $complaint): array
{
    $street=trim((string)($complaint['street_name']??''));
    $location=trim((string)($complaint['location_address']??''));
    $normalize=static fn($s)=>mb_strtolower(preg_replace('/\s+/u',' ',trim($s))??trim($s),'UTF-8');
    if (in_array(strtolower($street),['not specified','-','—'],true)) $street='';
    if ($street!=='' && $location!=='' && $normalize($street)===$normalize($location)) $street='';
    return ['street'=>$street,'location'=>$location];
}

/** Current stage must be supported by actual assignment and transfer records. */
function complaint_journey_stage(array $complaint,array $assignments,array $transfers,?array $policy): ?int
{
    $owner=(int)($complaint['assigned_officer_id']??0);
    if (!$owner) return null;
    $current=array_values(array_filter($assignments,static fn($a)=>(int)$a['is_current']===1));
    if (count($current)!==1 || (int)$current[0]['officer_id']!==$owner) return null;
    if ($transfers) {
        $last=end($transfers);
        if ((int)$last['officer_id']===$owner && $last['escalated_at']===$current[0]['assigned_at']) return (int)$last['level_no'];
        return null;
    }
    $first=$policy['stages'][0]??null;
    return $first && pdf_policy_role_matches((string)$first['role_label'],(string)($current[0]['designation']??'')) ? 1 : null;
}
function complaint_journey(PDO $pdo,array $complaint,bool $staff): array
{
    $id=(int)$complaint['id'];
    $owner=(int)($complaint['assigned_officer_id']??0);
    $publicContact="(o.is_active=1 AND EXISTS (SELECT 1 FROM officer_department_map dm JOIN departments d ON d.id=dm.department_id AND d.is_active=1 WHERE dm.officer_id=o.id))";
    $columns="o.officer_name,o.designation,o.mobile_no,o.email,$publicContact AS public_contact";
    $stmt=$pdo->prepare("SELECT a.id,a.officer_id,a.assigned_at,a.unassigned_at,a.is_current,$columns
        FROM complaint_assignments a LEFT JOIN officers o ON o.id=a.officer_id
        WHERE a.complaint_id=? ORDER BY a.assigned_at,a.id");
    $stmt->execute([$id]);$assignments=$stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt=$pdo->prepare("SELECT ce.id,ce.level_no,ce.officer_id,ce.escalated_at,$columns
        FROM complaint_escalations ce LEFT JOIN officers o ON o.id=ce.officer_id
        WHERE ce.complaint_id=? ORDER BY ce.escalated_at,ce.id");
    $stmt->execute([$id]);$transfers=$stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt=$pdo->prepare("SELECT $columns FROM officers o WHERE o.id=?");
    $stmt->execute([$owner]);$person=$stmt->fetch(PDO::FETCH_ASSOC)?:[];
    $approved=pdf_policy_reviewed($pdo,(int)$complaint['category_id']);
    $policy=$approved??pdf_policy_routing_ready($pdo,(int)$complaint['category_id']);
    $stage=complaint_journey_stage($complaint,$assignments,$transfers,$policy);
    $contacts=complaint_journey_contact($person,$staff);
    foreach ($assignments as &$row) {
        $row['contacts']=complaint_journey_contact($row,$staff);
        $row['stage']=null;
        foreach ($transfers as $transfer) {
            if ((int)$row['officer_id']===(int)$transfer['officer_id'] && $row['assigned_at']===$transfer['escalated_at']) {
                $row['stage']=(int)$transfer['level_no'];break;
            }
        }
        if ((int)$row['is_current']===1 && (int)$row['officer_id']===$owner) $row['stage']=$stage;
    }
    unset($row);
    foreach ($transfers as &$row) $row['contacts']=complaint_journey_contact($row,$staff);
    unset($row);
    return ['assignments'=>$assignments,'transfers'=>$transfers,'contacts'=>$contacts,'stage'=>$stage,'policy'=>$policy,'approved'=>$approved!==null];
}
