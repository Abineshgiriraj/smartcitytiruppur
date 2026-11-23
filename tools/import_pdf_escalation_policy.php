<?php
declare(strict_types=1);

/**
 * Read-only by default. Imports PDF policy *definitions*, never officer approvals.
 *
 * php tools/import_pdf_escalation_policy.php --expect-db=smartcity_escalation_test
 * php tools/import_pdf_escalation_policy.php --expect-db=smartcity_escalation_test --apply-test
 */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit('CLI only'); }
$expected = null; $apply = false;
foreach (array_slice($argv,1) as $argument) {
    if (str_starts_with($argument,'--expect-db=')) {
        $expected = substr($argument, strlen('--expect-db='));
    } elseif ($argument === '--apply-test') { $apply = true; }
    else { fwrite(STDERR,"Unknown option: {$argument}\n");exit(2); }
}
if ($expected === null || !preg_match('/^[a-zA-Z0-9_]+$/D', $expected)) {
    fwrite(STDERR,"Pass exact --expect-db=DATABASE\n");exit(2);
}
if ($apply && $expected !== 'smartcity_escalation_test') {
    fwrite(STDERR,"Writes blocked outside smartcity_escalation_test.\n");exit(2);
}
require_once dirname(__DIR__).'/includes/bootstrap.php';
require_once dirname(__DIR__).'/includes/pdf-escalation-policy.php';
$pdo=db();
$actual=(string)$pdo->query('SELECT DATABASE()')->fetchColumn();
if ($actual !== $expected) {
    fwrite(STDERR,"Wrong database: connected {$actual}, expected {$expected}. No changes.\n");exit(2);
}
if ($apply && (APP_ENV!=='local' || !in_array(strtolower(DB_HOST), ['127.0.0.1','localhost','::1'],true))) {
    fwrite(STDERR,"Test-only import requires local XAMPP database.\n");exit(2);
}
$path=dirname(__DIR__).'/database/pdf_escalation_policy.json';
if (!is_file($path)) { fwrite(STDERR,"Missing PDF policy JSON: {$path}\n"); exit(2); }
$source=json_decode((string)file_get_contents($path),true,512,JSON_THROW_ON_ERROR);
$expectedPdf='03a496809403a2de6a597a844a36ffc979de47657541342924628c8d33d70c4b';
if (($source['source_sha256']??'')!==$expectedPdf) {
    fwrite(STDERR,"Unexpected PDF provenance hash; no changes.\n");exit(2);
}
$policies=pdf_policy_source_rows($source);
$allDept=$pdo->query('SELECT id,department_name FROM departments')->fetchAll(PDO::FETCH_ASSOC);
$allCategory=$pdo->query('SELECT id,department_id,category_name FROM complaint_categories')->fetchAll(PDO::FETCH_ASSOC);
$departmentNames=[];
foreach ($allDept as $d) {
    $key=pdf_policy_key((string)$d['department_name']);
    if(isset($departmentNames[$key])) throw new RuntimeException('Ambiguous department '.$key);
    $departmentNames[$key]=(int)$d['id'];
}
$categoryNames=[];
foreach ($allCategory as $c) {
    $key=(int)$c['department_id'].'|'.pdf_policy_key((string)$c['category_name']);
    if(isset($categoryNames[$key]))throw new RuntimeException('Ambiguous category '.$key);
    $categoryNames[$key]=(int)$c['id'];
}
$mapped=[];$unmatched=[];$zero=[];
foreach ($policies as $policy) {
    $d=$departmentNames[pdf_policy_key((string)$policy['department'])]??0;
    $id=$categoryNames[$d.'|'.pdf_policy_key((string)$policy['category'])]??0;
    if (!$d || !$id) $unmatched[]=['pdf_row'=>$policy['s_no'],'department'=>$policy['department'],'category'=>$policy['category']];
    else $mapped[]=['category_id'=>$id,'policy'=>$policy];
    foreach($policy['stages'] as $stage) {
        if($stage['days']===0) $zero[]='PDF row '.$policy['s_no'].' stage '.$stage['stage'].' '.$stage['role'];
    }
}
echo "PDF rows: ".count($policies)."; Stage entries: 1409\n";
echo "Mapped DB categories: ".count($mapped)."; Unmatched: ".count($unmatched)."\n";
foreach($unmatched as $x) echo 'UNMATCHED #'.$x['pdf_row'].' '.$x['department'].' / '.$x['category']."\n";
echo "Zero-day stage exceptions: ".count($zero)."\n";
foreach($zero as $x) echo "POLICY REVIEW REQUIRED: {$x}\n";
if (!$apply) { echo "READ ONLY: no changes.\n"; exit($unmatched ? 3 : 0); }
if ($unmatched) {fwrite(STDERR,"Cannot import until every PDF category is reconciled.\n");exit(3);}
$tableCheck=$pdo->query(
    "SELECT COUNT(*) FROM information_schema.TABLES
     WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN
       ('pdf_escalation_policies','pdf_escalation_policy_stages')"
)->fetchColumn();
if((int)$tableCheck!==2){fwrite(STDERR,"Install reviewed PDF schema in test DB first.\n");exit(3);}
$lock=$pdo->query("SELECT GET_LOCK('smartcity_pdf_policy_import',3)")->fetchColumn();
if((int)$lock!==1){fwrite(STDERR,"Could not acquire import lock.\n");exit(3);}
$insertPolicy=$pdo->prepare(
    'INSERT INTO pdf_escalation_policies
     (category_id,source_row,total_days,stage_count,pdf_sha256,is_approved)
     VALUES (?,?,?,?,?,0)'
);
$insertStage=$pdo->prepare(
    'INSERT INTO pdf_escalation_policy_stages (category_id,stage_no,role_label,period_days)
     VALUES (?,?,?,?)'
);
$existing=$pdo->prepare(
    'SELECT source_row,total_days,stage_count,pdf_sha256,is_approved
     FROM pdf_escalation_policies WHERE category_id=? FOR UPDATE'
);
$existingStages=$pdo->prepare(
    'SELECT stage_no,role_label,period_days FROM pdf_escalation_policy_stages
     WHERE category_id=? ORDER BY stage_no'
);
try {
    $pdo->beginTransaction(); $created=0; $verified=0;
    foreach($mapped as $item){
        $id=$item['category_id'];$policy=$item['policy'];
        $existing->execute([$id]);$before=$existing->fetch(PDO::FETCH_ASSOC);
        if ($before) {
            $existingStages->execute([$id]);$stages=$existingStages->fetchAll(PDO::FETCH_ASSOC);
            $identical=(int)$before['source_row']===(int)$policy['s_no']
                && (int)$before['stage_count']===count($policy['stages'])
                && (int)$before['total_days']===(int)$policy['total_days']
                && $before['pdf_sha256']===$expectedPdf
                && count($stages)===count($policy['stages']);
            foreach($stages as $k=>$row){
                $p=$policy['stages'][$k]??null;
                if (!$p || (int)$row['stage_no']!==$p['stage']
                    || $row['role_label']!==$p['role']
                    || (int)$row['period_days']!==$p['days']) $identical=false;
            }
            if (!$identical) throw new RuntimeException('Existing PDF policy differs for category #'.$id.'; manual review');
            $verified++; continue;
        }
        $insertPolicy->execute([$id,$policy['s_no'],$policy['total_days'],count($policy['stages']),$expectedPdf]);
        foreach($policy['stages'] as $stage){
            $insertStage->execute([$id,$stage['stage'],$stage['role'],$stage['days']]);
        }
        $created++;
    }
    $pdo->commit();
    echo "TEST IMPORT COMPLETE: {$created} new draft policies; {$verified} existing identical; zero officer approvals.\n";
} catch(Throwable $e){
    if($pdo->inTransaction())$pdo->rollBack();
    fwrite(STDERR,"IMPORT ROLLED BACK: ".$e->getMessage()."\n");exit(3);
} finally {$pdo->query("SELECT RELEASE_LOCK('smartcity_pdf_policy_import')");}
