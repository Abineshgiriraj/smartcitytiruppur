<?php
declare(strict_types=1);
/**
 * PDF policy-aware escalation worker. Schedule only after local mapping validation and authorized operational sign-off.
 * --dry-run only reads. --execute requires explicitly verified local main database.
 * Unlike the legacy worker, every category has its own final stage (1..6).
 */
if(PHP_SAPI!=='cli'){http_response_code(403);exit('CLI only');}
require_once __DIR__.'/includes/bootstrap.php';
require_once __DIR__.'/includes/local-pdf-workflow-guard.php';
require_once __DIR__.'/includes/officer-authority.php';
require_once __DIR__.'/includes/pdf-escalation-policy.php';
$execute=false;$expected=null;$complaintId=null;
foreach(array_slice($argv,1) as $arg){
    if($arg==='--execute'){$execute=true;continue;}
    if($arg==='--dry-run')continue;
    if(preg_match('/^--expect-db=([A-Za-z0-9_]+)$/D',$arg,$m)){$expected=$m[1];continue;}
    if(preg_match('/^--complaint-id=([1-9][0-9]*)$/D',$arg,$m)){$complaintId=(int)$m[1];continue;}
    fwrite(STDERR,"Invalid argument {$arg}\n");exit(2);
}
if(!$expected){fwrite(STDERR,"Required --expect-db=<database>, including for dry-run.\n");exit(2);}
$pdo=db();$dbName=(string)$pdo->query('SELECT DATABASE()')->fetchColumn();
enforce_local_pdf_workflow($pdo,$expected,$execute);
$pdo->exec("SET SESSION time_zone = '+05:30'");

function pdfEscalationDecision(PDO $pdo,array $c,DateTimeImmutable $now,bool $nullableFk):array
{
    $skip=static fn(string $reason):array=>['ready'=>false,'reason'=>$reason];
    $id=(int)$c['id'];$category=(int)$c['category_id'];$department=(int)$c['department_id'];
    $ward=(int)$c['ward_id'];$zone=(int)$c['zone_id'];$currentOfficer=(int)($c['assigned_officer_id']??0);
    if($currentOfficer<=0)return $skip('UNASSIGNED: initial allocation required');
    if(in_array($c['status'],['Resolved','Closed','Rejected'],true)||(int)$c['is_deleted']!==0)
        return $skip('NOT_OPEN');
    if(!valid_complaint_routing_context($pdo,$department,$category,$zone,$ward))
        return $skip('INVALID_ROUTING_CONTEXT');
    $policy=pdf_policy_routing_ready($pdo,$category);
    if(!$policy)return $skip('PDF_POLICY_MISSING_OR_INVALID');
    $lastStmt=$pdo->prepare(
        'SELECT level_no,officer_id,escalated_at FROM complaint_escalations
         WHERE complaint_id=? ORDER BY escalated_at DESC,id DESC LIMIT 1'
    );
    $lastStmt->execute([$id]);$last=$lastStmt->fetch(PDO::FETCH_ASSOC);
    $level=$last?(int)$last['level_no']:1;
    if($level<1||$level>(int)$policy['stage_count'])return $skip('STAGE_HISTORY_MISMATCH');
    $currentStep=pdf_policy_stage($policy,$level);
    if(!$currentStep)return $skip('CURRENT_PDF_STAGE_MISSING');
    if($last&&(int)$last['officer_id']!==$currentOfficer)return $skip('HISTORY_ASSIGNEE_MISMATCH');
    $currentPerson=$pdo->prepare('SELECT designation FROM officers WHERE id=? AND is_active=1');
    $currentPerson->execute([$currentOfficer]);$currentTitle=$currentPerson->fetchColumn();
    if($currentTitle===false || !pdf_policy_role_matches((string)$currentStep['role_label'],(string)$currentTitle))
        return $skip('CURRENT_OFFICER_NOT_PDF_ROLE');
    if(!officer_stage_authorized($pdo,$category,$level,$currentOfficer,$department,$ward))
        return $skip('INELIGIBLE_CURRENT_STAGE_OFFICER');
    $assignmentStmt=$pdo->prepare(
        'SELECT officer_id,assigned_at FROM complaint_assignments
         WHERE complaint_id=? AND is_current=1 ORDER BY id DESC LIMIT 2'
    );
    $assignmentStmt->execute([$id]);$assignments=$assignmentStmt->fetchAll(PDO::FETCH_ASSOC);
    if(count($assignments)!==1 || (int)$assignments[0]['officer_id']!==$currentOfficer)
        return $skip('CURRENT_ASSIGNMENT_HISTORY_INVALID');
    try {
        $start=new DateTimeImmutable((string)($last?$last['escalated_at']:$assignments[0]['assigned_at']));
        $calculatedDue=$start->modify('+'.(int)$currentStep['period_days'].' days');
        if(empty($c['due_at']))return $skip('MISSING_STAGE_DEADLINE');
        $recordedDue=new DateTimeImmutable((string)$c['due_at']);
    } catch(Throwable $e){return $skip('INVALID_STAGE_DEADLINE');}
    // Never treat a historical total-SLA due_at as the PDF level deadline.
    if(abs($recordedDue->getTimestamp()-$calculatedDue->getTimestamp())>120)
        return $skip('DEADLINE_NOT_ALIGNED_TO_PDF_STAGE: manual review required');
    if($recordedDue>$now)return $skip('WITHIN_SLA');
    if($level>=(int)$policy['stage_count'])return $skip('TERMINAL_STAGE: no next officer');
    $nextLevel=$level+1;$nextStep=pdf_policy_stage($policy,$nextLevel);
    if(!$nextStep || (int)$nextStep['period_days']<=0)return $skip('INVALID_NEXT_PDF_STAGE');
    $candidates=array_values(array_filter(
        pdf_routing_candidates($pdo,$category,$nextLevel,$department,$ward),
        static fn(array $person):bool =>(int)$person['id']!==$currentOfficer
    ));
    // An exact official allocation rule is authoritative for EVERY stage,
    // including when there is only one other approved candidate.
    // Never silently route around a wrong/obsolete exact mapping.
    $exact=$pdo->prepare(
        'SELECT officer_id,escalation_days FROM department_allocations
         WHERE department_id=? AND category_id=? AND zone_id=? AND ward_id=?
           AND stage_no=? AND is_active=1 LIMIT 1'
    );
    $exact->execute([$department,$category,$zone,$ward,$nextLevel]);
    $exactRule=$exact->fetch(PDO::FETCH_ASSOC);
    if($exactRule){
        if((int)$exactRule['escalation_days']!==(int)$nextStep['period_days'])
            return $skip('EXACT_STAGE_SLA_CONFLICTS_WITH_PDF');
        $exactId=(int)$exactRule['officer_id'];
        $candidates=array_values(array_filter($candidates,
            static fn(array $r):bool =>(int)$r['id']===$exactId));
        if(count($candidates)!==1)return $skip('EXACT_NEXT_STAGE_OFFICER_INELIGIBLE');
    } else {
        // Without an exact stage rule, prefer ward-explicit over citywide maps.
        if($candidates){
            $firstRank=(int)$candidates[0]['ward_rank'];
            $candidates=array_values(array_filter($candidates,
                static fn(array $person):bool =>(int)$person['ward_rank']===$firstRank));
        }
        if(count($candidates)>1)return $skip('AMBIGUOUS_MAPPED_NEXT_STAGE_OFFICERS');
    }
    if(!$candidates)return $skip('NO_ELIGIBLE_PDF_NEXT_STAGE_OFFICER');
    $officer=$candidates[0];$linked=$pdo->prepare(
        'SELECT id FROM escalation_officer_levels
         WHERE category_id=? AND level_no=? AND officer_id=? AND is_active=1 LIMIT 1'
    );
    $linked->execute([$category,$nextLevel,(int)$officer['id']]);
    $linkedId=$linked->fetchColumn();
    if($linkedId===false && !$nullableFk)return $skip('SCHEMA_REQUIRES_NULLABLE_ESCALATION_LEVEL_ID');
    return [
        'ready'=>true,'reason'=>'ELIGIBLE','current_level'=>$level,'next_level'=>$nextLevel,
        'next_officer_id'=>(int)$officer['id'],'next_label'=>(string)$nextStep['role_label'],
        'next_period_days'=>(int)$nextStep['period_days'],
        'level_id'=>$linkedId===false?null:(int)$linkedId,
        'deadline'=>$recordedDue->format('Y-m-d H:i:s')
    ];
}

$col=$pdo->query("SHOW COLUMNS FROM complaint_escalations LIKE 'escalation_level_id'")->fetch(PDO::FETCH_ASSOC);
if(!$col){fwrite(STDERR,"Missing escalation_level_id column.\n");exit(2);}
$nullable=strtoupper((string)$col['Null'])==='YES';
$query='SELECT id,reference_no,status,is_deleted,assigned_officer_id,department_id,
               category_id,zone_id,ward_id,created_at,due_at FROM complaints
        WHERE is_deleted=0 AND status NOT IN (\'Resolved\',\'Closed\',\'Rejected\')';
$params=[];
if($complaintId!==null){$query.=' AND id=?';$params[]=$complaintId;}
else{
    // A scheduled cycle must prioritize overdue complaints; otherwise
    // the first 500 assigned-but-not-due cases can hide overdue cases.
    $query.=' AND assigned_officer_id IS NOT NULL AND due_at IS NOT NULL
               AND due_at <= NOW()';
}
$query.=' ORDER BY due_at ASC,id ASC LIMIT 500';
$stmt=$pdo->prepare($query);$stmt->execute($params);
$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
echo ($execute?'[LOCAL EXECUTE]':'[DRY RUN]')." DB={$dbName}, cases=".count($rows)."\n";
$locked=false;$applied=0;
try {
    if($execute){
        $locked=((int)$pdo->query("SELECT GET_LOCK('smartcity_pdf_escalation',0)")->fetchColumn()===1);
        if(!$locked){fwrite(STDERR,"Another worker holds escalation lock.\n");exit(2);}
    }
    foreach($rows as $row){
        if(!$execute){
            $decision=pdfEscalationDecision($pdo,$row,new DateTimeImmutable('now'),$nullable);
            echo ($decision['ready']?'[WOULD ESCALATE] ':'[SKIP] ').$row['reference_no'].' '.
                 ($decision['ready']?"L{$decision['current_level']} -> L{$decision['next_level']} officer {$decision['next_officer_id']}":$decision['reason'])."\n";
            continue;
        }
        try {
            $pdo->beginTransaction();
            $lockRow=$pdo->prepare('SELECT id,reference_no,status,is_deleted,assigned_officer_id,
                department_id,category_id,zone_id,ward_id,created_at,due_at FROM complaints WHERE id=? FOR UPDATE');
            $lockRow->execute([(int)$row['id']]);$fresh=$lockRow->fetch(PDO::FETCH_ASSOC);
            if(!$fresh)throw new RuntimeException('Complaint disappeared');
            $now=new DateTimeImmutable('now');
            $decision=pdfEscalationDecision($pdo,$fresh,$now,$nullable);
            if(!$decision['ready']){$pdo->rollBack();echo '[SKIP] '.$fresh['reference_no'].' '.$decision['reason']."\n";continue;}
            $at=$now->format('Y-m-d H:i:s');
            $due=$now->modify('+'.$decision['next_period_days'].' days')->format('Y-m-d H:i:s');
            $pdo->prepare('UPDATE complaints SET assigned_officer_id=?,due_at=?,updated_at=? WHERE id=?')
                ->execute([$decision['next_officer_id'],$due,$at,(int)$fresh['id']]);
            $pdo->prepare('UPDATE complaint_assignments SET is_current=0,unassigned_at=? WHERE complaint_id=? AND is_current=1')
                ->execute([$at,(int)$fresh['id']]);
            $pdo->prepare('INSERT INTO complaint_assignments (complaint_id,officer_id,assigned_by,assigned_at,is_current)
                VALUES(?,?,NULL,?,1)')->execute([(int)$fresh['id'],$decision['next_officer_id'],$at]);
            $text=sprintf('PDF policy escalation to level %d (%s) after %s',
                $decision['next_level'],$decision['next_label'],$decision['deadline']);
            $pdo->prepare('INSERT INTO complaint_status_history
                (complaint_id,old_status,new_status,remarks,changed_by,changed_at)
                VALUES(?,?,?,?,NULL,?)')
                ->execute([(int)$fresh['id'],$fresh['status'],$fresh['status'],$text,$at]);
            $pdo->prepare('INSERT INTO complaint_escalations
                (complaint_id,escalation_level_id,level_no,officer_id,escalated_at) VALUES(?,?,?,?,?)')
                ->execute([(int)$fresh['id'],$decision['level_id'],$decision['next_level'],$decision['next_officer_id'],$at]);
            $pdo->commit();$applied++;
            echo '[ESCALATED] '.$fresh['reference_no'].' L'.$decision['next_level']."\n";
        } catch(Throwable $error){
            if($pdo->inTransaction())$pdo->rollBack();
            fwrite(STDERR,'[ERROR] '.$row['reference_no'].': '.$error->getMessage()."\n");
        }
    }
} finally {
    if($locked)$pdo->query("SELECT RELEASE_LOCK('smartcity_pdf_escalation')");
}
echo "Completed: {$applied} local transitions. Auto-scheduling remains disabled.\n";
