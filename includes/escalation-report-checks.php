<?php
declare(strict_types=1);

/**
 * Read-only report helpers. These previews MUST NOT assign officers, create
 * approvals, or suggest that an upcoming assignment is guaranteed.
 * Requires includes/officer-authority.php.
 */
function er_stage_officer_preview(
    PDO $pdo, array $complaint, array $policy, int $stage, int $excludeOfficerId = 0
): array {
    $none = static fn(string $label):array => [
        'candidate'=>null, 'count'=>0, 'label'=>$label, 'issue'=>true
    ];
    $step = pdf_policy_stage($policy,$stage);
    if (!$step) return $none('No valid PDF stage definition');
    if ($stage < 2) return $none('Stage 1 is the original assignment');
    $category=(int)$complaint['category_id'];
    $department=(int)$complaint['department_id'];
    $zone=(int)$complaint['zone_id'];
    $ward=(int)$complaint['ward_id'];
    try {
        $candidates=array_values(array_filter(
            pdf_routing_candidates($pdo,$category,$stage,$department,$ward),
            static fn(array $officer):bool => (int)$officer['id']!==$excludeOfficerId
        ));
        // Follow the worker's exact mapped stage rule first, without falling
        // back to another person if the official mapping is invalid.
        $exact=$pdo->prepare(
            'SELECT officer_id,escalation_days FROM department_allocations
             WHERE department_id=? AND category_id=? AND zone_id=? AND ward_id=?
               AND stage_no=? AND is_active=1 LIMIT 1'
        );
        $exact->execute([$department,$category,$zone,$ward,$stage]);
        $mapped=$exact->fetch(PDO::FETCH_ASSOC);
        if ($mapped) {
            if ((int)$mapped['escalation_days']!==(int)$step['period_days']) {
                return $none('Exact stage mapping conflicts with the PDF SLA');
            }
            $candidates=array_values(array_filter($candidates,
                static fn(array $officer):bool=>(int)$officer['id']===(int)$mapped['officer_id']));
            if(count($candidates)!==1) return $none('Configured stage officer is not eligible');
            $pick=$candidates[0];
            $mode='Exact mapped officer; tentative until an escalation is recorded';
        } else {
            if(!$candidates)return $none('No eligible officer mapped for this stage');
            $rank=(int)$candidates[0]['ward_rank'];
            $candidates=array_values(array_filter($candidates,
                static fn(array $officer):bool=>(int)$officer['ward_rank']===$rank));
            $pick=pdf_pick_least_loaded_officer($pdo,$candidates);
            $mode=count($candidates)>1
                ? 'Workload-selected candidate; could change before transfer'
                : 'One eligible candidate; not yet assigned';
        }
        if(!$pick)return $none('No eligible officer available for this stage');
        return [
            'candidate'=>$pick, 'count'=>count($candidates),
            'label'=>$mode, 'issue'=>false
        ];
    } catch(Throwable $error) {
        log_message('Escalation report officer lookup failed: '.$error->getMessage());
        return $none('Stage officer lookup unavailable; review mappings');
    }
}

/**
 * Rechecks the current case against the principal PDF escalation-worker
 * preconditions. A positive result is NOT proof that a scheduler ran, that
 * every runtime condition was met, or that the complaint was escalated.
 */
function er_escalation_readiness(
    PDO $pdo, array $complaint, ?array $policy, int $level,
    array $events, array $assignments, array $next
): array {
    $result=static fn(string $state,string $title,string $detail):array =>
        ['state'=>$state,'title'=>$title,'detail'=>$detail];
    if(in_array((string)$complaint['status'],['Resolved','Closed','Rejected'],true))
        return $result('complete','Complaint closed or terminal','No further automatic escalation is expected for this status.');
    $currentId=(int)($complaint['assigned_officer_id']??0);
    if($currentId<1)return $result('blocked','Initial assignment missing',
        'Allocate an authorized Stage 1 officer before checking higher-stage escalation.');
    if(!valid_complaint_routing_context($pdo,(int)$complaint['department_id'],
        (int)$complaint['category_id'],(int)$complaint['zone_id'],(int)$complaint['ward_id']))
        return $result('blocked','Department/category/ward/zone mismatch',
            'The assigned routing context is invalid. Review the location and category mapping.');
    if(!$policy)return $result('blocked','PDF policy not routable',
        'The official stage policy is missing, incomplete, or fails validation.');
    if($level<1||$level>(int)$policy['stage_count'])
        return $result('blocked','Stage history mismatch','Recorded stage falls outside the configured PDF stages.');
    $current=pdf_policy_stage($policy,$level);
    if(!$current)return $result('blocked','Current PDF stage missing','The category has no valid definition for this stage.');
    $last=$events?end($events):null;
    if($last && (int)$last['officer_id']!==$currentId)
        return $result('blocked','Escalation history does not match assigned officer',
            'The latest recorded escalation recipient differs from the current complaint assignee.');
    $officer=$pdo->prepare('SELECT designation FROM officers WHERE id=? AND is_active=1');
    $officer->execute([$currentId]);
    $title=$officer->fetchColumn();
    if($title===false || !pdf_policy_role_matches((string)$current['role_label'],(string)$title))
        return $result('blocked','Current officer designation does not match PDF stage',
            'Review the assigned officer designation; routing cannot proceed with the wrong role.');
    if(!officer_stage_authorized($pdo,(int)$complaint['category_id'],$level,$currentId,
        (int)$complaint['department_id'],(int)$complaint['ward_id']))
        return $result('blocked','Current officer is not stage-eligible',
            'Department, category, ward or stage authorization needs review.');
    $active=array_values(array_filter($assignments,
        static fn(array $a):bool=>(int)$a['is_current']===1));
    if(count($active)!==1||(int)$active[0]['officer_id']!==$currentId)
        return $result('blocked','Current assignment history is inconsistent',
            'There must be exactly one current assignment matching the complaint officer.');
    if(empty($complaint['due_at']))return $result('blocked','Stage deadline is missing',
        'The complaint needs a valid current-stage due date.');
    try {
        $start=new DateTimeImmutable((string)($last?$last['escalated_at']:$active[0]['assigned_at']));
        $expected=$start->modify('+'.(int)$current['period_days'].' days');
        $due=new DateTimeImmutable((string)$complaint['due_at']);
    }catch(Throwable $error){
        return $result('blocked','Stage date is invalid','Assignment date, escalation date or due date could not be interpreted.');
    }
    if(abs($due->getTimestamp()-$expected->getTimestamp())>120)
        return $result('blocked','Deadline conflicts with the PDF stage SLA',
            'The recorded deadline is not the assignment/transfer date plus the current-stage PDF days.');
    if($due>new DateTimeImmutable('now'))return $result('within','Within current-stage SLA',
        'The current-stage deadline has not passed. Escalation is not due.');
    if($level>=(int)$policy['stage_count'])return $result('terminal','Final PDF stage overdue',
        'The case has reached its final defined stage. The worker has no next officer to assign.');
    if(!$next['candidate'])return $result('blocked','Escalation blocked: '.(string)$next['label'],
        'The next-stage officer must satisfy the existing PDF and mapping rules.');
    // The worker also requires a valid escalation_level_id unless its FK is nullable.
    try{
        $column=$pdo->query("SHOW COLUMNS FROM complaint_escalations LIKE 'escalation_level_id'")->fetch(PDO::FETCH_ASSOC);
        if(!$column)return $result('blocked','Escalation schema is incomplete',
            'Column complaint_escalations.escalation_level_id is missing.');
        if(strtoupper((string)$column['Null'])!=='YES'){
            $linked=$pdo->prepare(
                'SELECT id FROM escalation_officer_levels WHERE category_id=? AND level_no=? AND officer_id=? AND is_active=1 LIMIT 1'
            );
            $linked->execute([(int)$complaint['category_id'],$level+1,(int)$next['candidate']['id']]);
            if($linked->fetchColumn()===false)return $result('blocked','Next-stage escalation-level mapping missing',
                'The table requires a non-null escalation_level_id for the selected next-stage officer.');
        }
    }catch(Throwable $error){
        log_message('Escalation report schema check failed: '.$error->getMessage());
        return $result('blocked','Escalation schema could not be verified',
            'Check the escalation tables and database permissions.');
    }
    return $result('due','Overdue — no recorded transfer yet',
        'The visible routing checks pass, but no escalation is recorded. Check whether the local PDF escalation worker is scheduled and inspect its read-only dry-run output. This page never runs it.');
}
