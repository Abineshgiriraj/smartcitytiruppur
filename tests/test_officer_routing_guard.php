<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/officer-authority.php';
$cases=[
    ['COMMR at Stage 1','COMMR',1,3,true],
    ['COMMR at Stage 3','COMMR',3,3,true],
    ['COMMR at Stage 4','COMMR',4,3,false],
    ['City Commissioner at Stage 1','CITY COMMISSIONER',1,9,true],
    ['Deputy COMMR at Stage 1','DEPUTY COMMR',1,9,true],
    ['Deputy COMMR at Stage 3','DEPUTY COMMR',3,9,false],
    ['Officer 1 blocked','AE (UGD)',1,1,true],
    ['Admin always blocked','SUPER ADMIN',4,10,true],
    ['Assistant Engineer Stage 1','AE (UGD)',1,10,false],
    ['Assistant Executive Engineer Stage 1','AEE',1,11,false],
    ['Wrong stage blocked','AE',5,12,true],
];
$failed=0;
foreach($cases as [$name,$title,$stage,$id,$expected]){
    $actual=officer_stage_forbidden_title($title,$stage,$id);
    $pass=$actual===$expected;
    echo ($pass?'PASS ':'FAIL ').$name.PHP_EOL;
    if(!$pass)$failed++;
}
echo (count($cases)-$failed).'/'.count($cases).' pure checks passed'.PHP_EOL;
exit($failed?1:0);
