<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/includes/pdf-escalation-policy.php';
$source=json_decode((string)file_get_contents(dirname(__DIR__).'/database/pdf_escalation_policy.json'),true,512,JSON_THROW_ON_ERROR);
$rows=pdf_policy_source_rows($source);
$lookup=[];$histogram=[];
foreach($rows as $item){
    $lookup[(int)$item['s_no']]=$item;
    $n=count($item['stages']);$histogram[$n]=($histogram[$n]??0)+1;
}
$expectedHistogram=[1=>16,2=>1,3=>3,4=>104,5=>96,6=>81];ksort($histogram);ksort($expectedHistogram);
if($histogram!==$expectedHistogram)throw new RuntimeException('Stage count distribution wrong');
$assertions=[
    'PDF has 301 workflows'=>count($rows)===301,
    'PDF has 1409 stages'=>array_sum(array_map(static fn($x):int=>count($x['stages']),$rows))===1409,
    'Town Plan 5 stages'=>count($lookup[106]['stages'])===5,
    'Town Plan Stage 1 four days'=>$lookup[106]['stages'][0]['days']===4,
    'New streetlight 6 levels'=>count($lookup[135]['stages'])===6,
    'New streetlight Stage 1 30 days'=>$lookup[135]['stages'][0]['days']===30,
    'New streetlight Stage 5 zero-day exception'=>$lookup[135]['stages'][4]['days']===0,
    'Public distribution one terminal stage'=>count($lookup[142]['stages'])===1,
    'Sewage 6-stage 19-day example'=>$lookup[9]['total_days']===19 && count($lookup[9]['stages'])===6,
    'PDF first stage for transport is Deputy Manager 6d'=>$lookup[297]['stages'][0]['role']==='Deputy Manager of Commercial' && $lookup[297]['stages'][0]['days']===6,
    'Commissioner role accepts actual COMMR'=>pdf_policy_role_matches('COMMR','COMMR'),
    'Commissioner role rejects Deputy'=>!pdf_policy_role_matches('COMMR','DEPUTY COMMR'),
    'Deputy role rejects Commissioner'=>!pdf_policy_role_matches('DEPUTY COMMR','COMMR'),
    'Sanitary Inspector alias works'=>pdf_policy_role_matches('SI','Sanitary Inspector'),
    'AE cannot be matched by AEE'=>!pdf_policy_role_matches('AE','AEE'),
    'AE can be Assistant Engineer'=>pdf_policy_role_matches('AE(TP)','Assistant Engineer'),
    'AE or JE can be Junior Engineer'=>pdf_policy_role_matches('AE/JE','Junior Engineer'),
    'No imaginary Stage 7'=>pdf_policy_stage(['stage_count'=>6,'stages'=>array_map(
        static fn($s):array=>['stage_no'=>$s['stage'],'period_days'=>$s['days']],$lookup[9]['stages'])],7)===null,
];
$count=0;
foreach($assertions as $label=>$ok){
    if(!$ok){fwrite(STDERR,"FAIL {$label}\n");exit(1);}
    echo "PASS {$label}\n";$count++;
}
echo "{$count}/{$count} PDF-source checks passed. No DB connection or mutations.\n";
