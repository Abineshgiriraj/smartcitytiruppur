<?php
declare(strict_types=1);
/**
 * Read-only readiness report for one complaint and its exact PDF stages.
 * CLI: php tools/check_pdf_automation_readiness.php
 *        --expect-db=smartcity_escalation_test --reference=TCMC-2026-950324
 * Never inserts staff approvals or changes assignment history.
 */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit('CLI only'); }
$expected = null; $reference = null;
foreach (array_slice($argv,1) as $arg) {
    if (preg_match('/^--expect-db=([A-Za-z0-9_]+)$/D',$arg,$m)) { $expected=$m[1]; continue; }
    if (preg_match('/^--reference=([A-Za-z0-9-]{1,40})$/D',$arg,$m)) { $reference=$m[1]; continue; }
    fwrite(STDERR,"Unknown option: {$arg}\n"); exit(2);
}
if (!$expected || !$reference) {
    fwrite(STDERR,"Required --expect-db=DB --reference=TCMC-...\n"); exit(2);
}
require_once dirname(__DIR__).'/includes/bootstrap.php';
$pdo=db();
$name=(string)$pdo->query('SELECT DATABASE()')->fetchColumn();
if ($name!==$expected) { fwrite(STDERR,"DB mismatch: {$name}; no changes.\n"); exit(2); }
$q=$pdo->prepare(
    'SELECT id,reference_no,status,is_deleted,assigned_officer_id,
            department_id,category_id,zone_id,ward_id
     FROM complaints WHERE reference_no=? LIMIT 1'
);
$q->execute([$reference]);$c=$q->fetch(PDO::FETCH_ASSOC);
if (!$c) { fwrite(STDERR,"Complaint not found; no changes.\n"); exit(2); }
echo "READ-ONLY PDF WORKFLOW READINESS\nDatabase={$name} Complaint={$c['reference_no']} Status={$c['status']}\n";
$dep=(int)$c['department_id'];$cat=(int)$c['category_id'];
$ward=(int)$c['ward_id'];$zone=(int)$c['zone_id'];
echo "Category ID={$cat}, Department ID={$dep}, Zone ID={$zone}, Ward ID={$ward}\n";
if (!valid_complaint_routing_context($pdo,$dep,$cat,$zone,$ward)) {
    echo "BLOCKED: invalid complaint department/category/ward/zone context\n"; exit(3);
}
$policy=pdf_policy_reviewed($pdo,$cat);
if (!$policy) { echo "BLOCKED: PDF policy missing/unapproved/invalid\n"; exit(3); }
echo "APPROVED PDF stages={$policy['stage_count']}, total days={$policy['total_days']}\n";
$rules=$pdo->prepare(
    'SELECT officer_id,escalation_days FROM department_allocations
     WHERE department_id=? AND category_id=? AND zone_id=? AND ward_id=?
       AND stage_no=? AND is_active=1 LIMIT 1'
);
$approved=$pdo->prepare(
    "SELECT o.id,o.officer_name,o.designation
     FROM officer_stage_approvals a
     JOIN officers o ON o.id=a.officer_id AND o.is_active=1
     WHERE a.category_id=? AND a.stage_no=? AND a.revoked_at IS NULL
       AND a.approved_by IS NOT NULL AND TRIM(a.approval_reference)<>''
     ORDER BY o.id"
);
$allReady=true;
for ($stageNo=1;$stageNo<=(int)$policy['stage_count'];$stageNo++) {
    $stage=pdf_policy_stage($policy,$stageNo);
    if (!$stage) { echo "Stage {$stageNo}: BLOCKED missing policy stage\n"; $allReady=false;continue; }
    $role=(string)$stage['role_label']; $days=(int)$stage['period_days'];
    $approved->execute([$cat,$stageNo]);
    $candidates=[];
    foreach ($approved->fetchAll(PDO::FETCH_ASSOC) as $officer) {
        $oid=(int)$officer['id'];
        if (officer_stage_authorized($pdo,$cat,$stageNo,$oid,$dep,$ward)
            && pdf_policy_role_matches($role,(string)$officer['designation'])) {
            $candidates[$oid]=(string)$officer['officer_name'];
        }
    }
    $rules->execute([$dep,$cat,$zone,$ward,$stageNo]);
    $rule=$rules->fetch(PDO::FETCH_ASSOC);
    if ($rule) {
        $exactId=(int)$rule['officer_id'];
        $reason=((int)$rule['escalation_days']!==$days)
            ? 'EXACT_RULE_DAYS_CONFLICT'
            : (!array_key_exists($exactId,$candidates) ? 'EXACT_RULE_OFFICER_NOT_AUTHORIZED' : 'READY');
        $label="exact officer #{$exactId}";
    } elseif (count($candidates)===1) {
        $reason='READY';
        $label='unique authorized officer #'.array_key_first($candidates);
    } elseif (!$candidates) {
        $reason='NO_AUTHORIZED_OFFICER';
        $label='none';
    } else {
        $reason='MULTIPLE_APPROVED_OFFICERS_NEED_EXACT_WARD_RULE';
        $label=count($candidates).' eligible';
    }
    if ($stageNo>1 && count($candidates)===1 && (int)$c['assigned_officer_id']===array_key_first($candidates)) {
        // This is informational only; this officer cannot be their own next recipient.
        $label.=' (check next-stage officer differs from current)';
    }
    echo sprintf("Stage %d: role=%s days=%d eligible=%d mapping=%s outcome=%s\n",
        $stageNo,$role,$days,count($candidates),$label,$reason);
    if ($reason!=='READY') $allReady=false;
}
echo $allReady
    ? "READY: stage-level personnel/routing configuration resolves; transaction-level checks still apply.\n"
    : "NOT READY: review missing officers, authority references and exact ward rules.\n";
echo "No records modified. This is NOT a substitute for verifying official appointments.\n";
exit($allReady?0:3);
