<?php
declare(strict_types=1);
/**
 * Read-only diagnosis for a complaint not assigned to a verified Stage 1 officer.
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
    echo 'Stage 1 approval records: '.count($rows)."\n";
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
    echo "Eligible approved Stage 1 officers: {$valid}\n";
} else {
    echo "BLOCKER: Missing registry means no officer can satisfy Stage 1 authorization.\n";
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
        $ok=$hasApproval && officer_stage_authorized($pdo,$cat,1,$officer,$dep,$ward);
        echo "Exact rule officer #{$officer}, active={$row['is_active']}, approved=".($ok?'YES':'NO')."\n";
        if((int)$row['is_active']===1 && !$ok)echo "BLOCKER: exact rule is unapproved; current service refuses fallback.\n";
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
$trigger=$pdo->prepare(
    "SELECT COUNT(*) FROM information_schema.TRIGGERS
     WHERE TRIGGER_SCHEMA=? AND EVENT_OBJECT_TABLE='complaints'
       AND TRIGGER_NAME='trg_complaints_auto_assign'"
);
$trigger->execute([$dbName]);
echo "Unsafe historical trigger: ".((int)$trigger->fetchColumn()>0?'PRESENT':'ABSENT')."\n";
echo "No database changes executed.\n";
