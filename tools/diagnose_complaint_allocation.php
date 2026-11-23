<?php
declare(strict_types=1);
/**
 * Read-only diagnosis for a complaint awaiting Stage 1 allocation from existing mappings.
 * php tools/diagnose_complaint_allocation.php --expect-db=smartcitytiruppur --reference=TCMC-2026-576866
 */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit("CLI only\n"); }
$expected=''; $reference='';
foreach (array_slice($argv,1) as $arg) {
    if (str_starts_with($arg,'--expect-db=')) {
        $expected=substr($arg,strlen('--expect-db='));
    } elseif (str_starts_with($arg,'--reference=')) {
        $reference=substr($arg,strlen('--reference='));
    } else {
        fwrite(STDERR,"Unknown argument: {$arg}\n"); exit(2);
    }
}
if (!preg_match('/^[A-Za-z0-9_]+$/D',$expected)
    || !preg_match('/^[A-Za-z0-9-]{1,40}$/D',$reference)) {
    fwrite(STDERR,"Required: --expect-db=<exact_db> --reference=<complaint_reference>\n"); exit(2);
}
require_once dirname(__DIR__).'/includes/bootstrap.php';
$pdo=db();
$dbName=(string)$pdo->query('SELECT DATABASE()')->fetchColumn();
if ($dbName!==$expected) {
    fwrite(STDERR,"Wrong database: {$dbName}; expected {$expected}. No changes.\n"); exit(2);
}
$stmt=$pdo->prepare(
    'SELECT c.id,c.reference_no,c.department_id,c.category_id,c.ward_id,c.zone_id,
            c.assigned_officer_id,c.status,cc.category_name,d.department_name
     FROM complaints c
     JOIN complaint_categories cc ON cc.id=c.category_id
     JOIN departments d ON d.id=c.department_id
     WHERE c.reference_no=? AND c.is_deleted=0 LIMIT 1'
);
$stmt->execute([$reference]);$complaint=$stmt->fetch(PDO::FETCH_ASSOC);
if (!$complaint) { fwrite(STDERR,"Complaint not found in {$dbName}\n"); exit(2); }
$id=(int)$complaint['id'];
$cat=(int)$complaint['category_id'];
$dep=(int)$complaint['department_id'];
$ward=(int)$complaint['ward_id'];
$zone=(int)$complaint['zone_id'];
echo "Read-only allocation diagnosis: {$dbName}\n";
echo "Complaint {$complaint['reference_no']} (#{$id}) {$complaint['status']}; assigned_id="
    .($complaint['assigned_officer_id']===null?'NONE':$complaint['assigned_officer_id'])."\n";
echo "Department {$complaint['department_name']} (#{$dep}), category {$complaint['category_name']} (#{$cat})\n";
echo "Ward #{$ward}, Zone #{$zone}\n";
echo "Category/ward/zone context: ".
    (valid_complaint_routing_context($pdo,$dep,$cat,$zone,$ward)?'VALID':'INVALID')."\n";

$policyExists = $pdo->prepare(
    "SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=?
       AND TABLE_NAME IN ('pdf_escalation_policies','pdf_escalation_policy_stages')"
);
$policyExists->execute([$dbName]);
$policySchemaReady = (int)$policyExists->fetchColumn() === 2;
$firstStageRole = null;
echo "PDF policy schema: ".($policySchemaReady ? 'INSTALLED' : 'MISSING/INCOMPLETE')."\n";
if ($policySchemaReady) {
    $policyStmt = $pdo->prepare(
        "SELECT p.is_approved,p.stage_count,p.total_days,
                p.approval_reference,p.approved_by,p.approved_at,
                s.role_label,s.period_days
         FROM pdf_escalation_policies p
         LEFT JOIN pdf_escalation_policy_stages s
           ON s.category_id=p.category_id AND s.stage_no=1
         WHERE p.category_id=? LIMIT 1"
    );
    $policyStmt->execute([$cat]);
    $policy = $policyStmt->fetch(PDO::FETCH_ASSOC);
    if (!$policy) {
        echo "PDF category policy: MISSING in smartcitytiruppur\n";
    } else {
        $firstStageRole = (string)($policy['role_label'] ?? '');
        $reviewed = pdf_policy_routing_ready($pdo, $cat);
        echo "PDF category policy: ".($reviewed ? 'VALID PDF ROUTING RULE' : 'MISSING/INVALID PDF ROUTING RULE')."\n";
        echo "PDF stage count: {$policy['stage_count']}; total period: {$policy['total_days']} days\n";
        echo "PDF Stage 1 role: ".($firstStageRole ?: 'MISSING')."; days: ".($policy['period_days'] ?? 'MISSING')."\n";
        if (!$reviewed) {
            echo "BLOCKER: Stage 1 requires a structurally valid imported PDF policy.\n";
        }
    }
}

$probe=$pdo->prepare(
    'SELECT COUNT(*) FROM information_schema.TABLES
     WHERE TABLE_SCHEMA=? AND TABLE_NAME=?'
);
$probe->execute([$dbName,'officer_stage_approvals']);
$hasApproval=((int)$probe->fetchColumn()===1);
echo 'Officer approval table: '.($hasApproval?'INSTALLED':'MISSING')."\n";
if ($hasApproval) {
    $query=$pdo->prepare(
        "SELECT a.officer_id,a.approved_by,a.approval_reference,a.revoked_at,
                o.designation,o.is_active,
                EXISTS(SELECT 1 FROM officer_department_map m WHERE m.officer_id=o.id AND m.department_id=?) AS dept_ok,
                EXISTS(SELECT 1 FROM officer_category_map m WHERE m.officer_id=o.id AND m.category_id=?) AS cat_ok,
                EXISTS(SELECT 1 FROM officer_ward_map m WHERE m.officer_id=o.id) AS ward_scoped,
                EXISTS(SELECT 1 FROM officer_ward_map m WHERE m.officer_id=o.id AND m.ward_id=?) AS ward_ok
         FROM officer_stage_approvals a JOIN officers o ON o.id=a.officer_id
         WHERE a.category_id=? AND a.stage_no=1 ORDER BY a.id LIMIT 1000"
    );
    $query->execute([$dep,$cat,$ward,$cat]);
    $rows=$query->fetchAll(PDO::FETCH_ASSOC);
    echo 'Historical Stage 1 approval records (not required for routing): '.count($rows)."\n";
    $valid=0;
    foreach($rows as $row) {
        $officerId=(int)$row['officer_id'];
        $ok=officer_stage_authorized($pdo,$cat,1,$officerId,$dep,$ward);
        if($ok)$valid++;
        $reasons=[];
        if($row['revoked_at']!==null)$reasons[]='revoked';
        if($row['approved_by']===null || trim((string)$row['approval_reference'])==='')$reasons[]='approval reference/approver missing';
        if(!(int)$row['is_active'])$reasons[]='inactive officer';
        if(!(int)$row['dept_ok'])$reasons[]='no department mapping';
        if(!(int)$row['cat_ok'])$reasons[]='no category mapping';
        if((int)$row['ward_scoped']===1 && !(int)$row['ward_ok'])$reasons[]='wrong ward scope';
        if(officer_stage_forbidden_title((string)$row['designation'],1,$officerId))$reasons[]='prohibited Stage 1 role';
        echo 'Officer #'.$officerId.' ('.$row['designation'].'): '.($ok?'ELIGIBLE':'BLOCKED')
            .($reasons?' ['.implode('; ',$reasons).']':'')."\n";
    }
    echo "Eligible mapped Stage 1 officers: {$valid}\n";
} else {
    echo "Note: Approval registry is not required for mapped routing.\n";
}
$probe->execute([$dbName,'department_allocations']);
if ((int)$probe->fetchColumn()===1) {
    $rule=$pdo->prepare(
        'SELECT officer_id,is_active,escalation_days FROM department_allocations
         WHERE department_id=? AND category_id=? AND zone_id=? AND ward_id=?
           AND stage_no=1 ORDER BY id'
    );
    $rule->execute([$dep,$cat,$zone,$ward]);
    $rules=$rule->fetchAll(PDO::FETCH_ASSOC);
    echo 'Exact department allocation rows: '.count($rules)."\n";
    foreach($rules as $row) {
        $officer=(int)$row['officer_id'];
        $ok=officer_stage_authorized($pdo,$cat,1,$officer,$dep,$ward);
        echo "Exact rule officer #{$officer}, active={$row['is_active']}, approved=".($ok?'YES':'NO')."\n";
        if((int)$row['is_active']===1 && !$ok)echo "BLOCKER: exact-rule officer is ineligible; no unsafe fallback.\n";
    }
}
$mapped=$pdo->prepare(
    'SELECT COUNT(DISTINCT o.id) FROM officers o
     JOIN officer_department_map dm ON dm.officer_id=o.id AND dm.department_id=?
     JOIN officer_category_map cm ON cm.officer_id=o.id AND cm.category_id=?
     WHERE o.is_active=1'
);
$mapped->execute([$dep,$cat]);
echo 'Active officers matching both category and department (not official approvals): '.(int)$mapped->fetchColumn()."\n";
$officerCandidates = $pdo->prepare(
    "SELECT o.id,o.officer_name,o.designation,
            EXISTS(SELECT 1 FROM officer_ward_map wm WHERE wm.officer_id=o.id) AS has_ward_map,
            EXISTS(SELECT 1 FROM officer_ward_map wm WHERE wm.officer_id=o.id AND wm.ward_id=?) AS in_ward
     FROM officers o
     JOIN officer_department_map dm ON dm.officer_id=o.id AND dm.department_id=?
     JOIN officer_category_map cm ON cm.officer_id=o.id AND cm.category_id=?
     WHERE o.is_active=1
     ORDER BY o.id LIMIT 30"
);
$officerCandidates->execute([$ward,$dep,$cat]);
echo "Mapped candidate review (NOT an approval list; maximum 30):\n";
foreach ($officerCandidates->fetchAll(PDO::FETCH_ASSOC) as $candidate) {
    $oid=(int)$candidate['id'];
    $title=(string)$candidate['designation'];
    $wardScope=((int)$candidate['has_ward_map']===0?'CITY-WIDE/UNSPECIFIED'
        :((int)$candidate['in_ward']===1?'MATCHED':'DIFFERENT WARD'));
    $roleMatch=$firstStageRole !== null && $firstStageRole !== ''
        ? (pdf_policy_role_matches($firstStageRole,$title)?'YES':'NO') : 'UNKNOWN';
    $eligible=officer_stage_authorized($pdo,$cat,1,$oid,$dep,$ward);
    echo "  #{$oid} {$candidate['officer_name']} | {$title} | ward={$wardScope}"
       ." | PDF Stage 1 role={$roleMatch} | eligible=".($eligible?'YES':'NO')."\n";
}
echo "Note: Routing relies on existing mappings; no separate approval is required.\n";

$trigger=$pdo->prepare(
    "SELECT COUNT(*) FROM information_schema.TRIGGERS
     WHERE TRIGGER_SCHEMA=? AND EVENT_OBJECT_TABLE='complaints'
       AND TRIGGER_NAME='trg_complaints_auto_assign'"
);
$trigger->execute([$dbName]);
echo "Unsafe historical trigger: ".((int)$trigger->fetchColumn()>0?'PRESENT':'ABSENT')."\n";

echo "\n=== Current automatic allocation routing decision ===\n";
if ($complaint['status'] !== 'Registered' || $complaint['assigned_officer_id'] !== null) {
    echo "NOT PENDING: Initial routing requires an unassigned Registered complaint.\n";
} elseif (!valid_complaint_routing_context($pdo,$dep,$cat,$zone,$ward)) {
    echo "BLOCKED: Complaint category/department/ward/zone mismatch.\n";
} elseif (!($routingPolicy=pdf_policy_routing_ready($pdo,$cat))) {
    echo "BLOCKED: PDF policy missing or invalid, including a zero-day stage.\n";
} else {
    $stage1=pdf_policy_stage($routingPolicy,1);
    $candidates=pdf_routing_candidates($pdo,$cat,1,$dep,$ward);
    $explicit=array_values(array_filter($candidates,static fn(array $x):bool=>(int)$x['ward_rank']===0));
    $unscoped=array_values(array_filter($candidates,static fn(array $x):bool=>(int)$x['ward_rank']===1));
    echo "Eligible matched officers: ".count($candidates)."\n";
    echo "Explicitly mapped to ward {$ward}: ".count($explicit)."\n";
    echo "No explicit ward mapping: ".count($unscoped)."\n";
    $activeRules=array_values(array_filter($rules??[],static fn(array $x):bool=>(int)$x['is_active']===1));
    if(count($activeRules)>1) {
        echo "BLOCKED: Multiple active exact allocation rules. Review conflicting rules.\n";
    } elseif(count($activeRules)===1) {
        $exact=$activeRules[0];$oid=(int)$exact['officer_id'];
        if(!officer_stage_authorized($pdo,$cat,1,$oid,$dep,$ward)) {
            echo "BLOCKED: Exact rule officer #{$oid} is not eligible for this PDF stage and ward.\n";
        } elseif((int)$exact['escalation_days']!==(int)$stage1['period_days']) {
            echo "BLOCKED: Exact rule SLA conflicts with the PDF Stage 1 days.\n";
        } else {
            echo "PREVIEW READY: Exact rule selects Officer #{$oid} with Stage 1 {$stage1['period_days']} day(s).\n";
        }
    } else {
        $priority=$candidates?(int)$candidates[0]['ward_rank']:null;
        $best=array_values(array_filter($candidates,
            static fn(array $x):bool=>$priority!==null && (int)$x['ward_rank']===$priority));
        if(count($best)===0) {
            echo "BLOCKED: No officer satisfies designation + active status + category + department + ward.\n";
            echo "FIX: Review the officer's designation, category/department and ward mappings.\n";
        } else {
            $winner=pdf_pick_least_loaded_officer($pdo,$best);
            echo "PREVIEW READY: Officer #{$winner['id']} {$winner['officer_name']} ({$winner['designation']}); Stage 1 {$stage1['period_days']} day(s); open workload={$winner['open_complaints']}.\n";
            if(count($best)>1){
                echo "Multiple eligible officers found: workload-based selection used, no manual allocation rule required.\n";
            }
        }
        foreach(array_slice($best,0,12) as $person){
            echo "  Best-match #{$person['id']} {$person['officer_name']} | {$person['designation']}\n";
        }
        if(count($best)>12) echo "  Plus ".(count($best)-12)." other equally eligible officers.\n";
    }
}

echo "No database changes executed.\n";
