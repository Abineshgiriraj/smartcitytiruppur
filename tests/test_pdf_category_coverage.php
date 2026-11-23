<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/includes/pdf-escalation-policy.php';
$root=dirname(__DIR__);
$pdf=json_decode((string)file_get_contents($root.'/database/pdf_escalation_policy.json'),true,512,JSON_THROW_ON_ERROR);
$categoryMaster=$root.'/database/category_master_from_uploaded_sql.json';
if(!is_file($categoryMaster)){
    fwrite(STDERR,"Missing existing main-repository category source file: {$categoryMaster}\n");exit(2);
}
$sql=json_decode((string)file_get_contents($categoryMaster),true,512,JSON_THROW_ON_ERROR);
$departments=[];
foreach($sql['departments'] as $d)$departments[(int)$d[0]]=(string)$d[1];
$categories=[];
foreach($sql['categories'] as $row){
    $department=$departments[(int)$row[1]]??null;
    if($department===null)throw new RuntimeException('Category with no department');
    $key=pdf_policy_key($department).'|'.pdf_policy_key((string)$row[2]);
    if(isset($categories[$key]))throw new RuntimeException('Duplicate category: '.$key);
    $categories[$key]=$row;
}
$matched=[];
foreach(pdf_policy_source_rows($pdf) as $policy){
    $key=pdf_policy_key((string)$policy['department']).'|'.pdf_policy_key((string)$policy['category']);
    if(!isset($categories[$key]))throw new RuntimeException('PDF row '.$policy['s_no'].' not found: '.$key);
    $matched[$key]=true;
}
$extras=array_diff_key($categories,$matched);
if(count($matched)!==301 || count($categories)!==306 || count($extras)!==5){
    throw new RuntimeException('PDF vs SQL category coverage mismatch: '.count($matched).' / '.count($categories).' / '.count($extras));
}
echo "PASS All 301 PDF workflows match the 306-category uploaded SQL master.\n";
echo "PASS Exactly five SQL-only categories have NO PDF workflow:\n";
foreach($extras as $key=>$row)echo '  SQL category #'.$row[0].' '.str_replace('|',' / ',$key)."\n";
echo "No database connection or mutations.\n";
