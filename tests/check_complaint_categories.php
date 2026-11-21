<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(403);exit("CLI only\n");}
$expected=null;
foreach(array_slice($argv,1) as $argument){
    if(str_starts_with($argument,'--expect-db=')){
        $expected=substr($argument,strlen('--expect-db='));
    }
}
if(!$expected||!preg_match('/^[A-Za-z0-9_]+$/D',$expected)){
    fwrite(STDERR,"Usage: php tests/check_complaint_categories.php --expect-db=smartcity_escalation_test\n");
    exit(2);
}
require_once __DIR__.'/../includes/bootstrap.php';
$pdo=db();
$actual=(string)$pdo->query('SELECT DATABASE()')->fetchColumn();
if($actual!==$expected){
    fwrite(STDERR,"Database mismatch: {$actual}; expected {$expected}. No changes made.\n");
    exit(2);
}
echo "Read-only complaint categories: {$actual}\n";
$sql="SELECT d.id,d.department_name,COUNT(cc.id) AS total_categories,
             COALESCE(SUM(cc.is_active=1),0) AS active_categories,
             COALESCE(SUM(cc.is_active=1 AND TRIM(cc.category_name) NOT IN ('','-')),0) AS visible_categories
      FROM departments d LEFT JOIN complaint_categories cc ON cc.department_id=d.id
      WHERE d.is_active=1 GROUP BY d.id,d.department_name ORDER BY d.department_name";
foreach($pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $row){
    printf("Dept #%d | %s | total=%d active=%d visible=%d\n",
        $row['id'],$row['department_name'],$row['total_categories'],
        $row['active_categories'],$row['visible_categories']);
}
echo "No mutations executed.\n";
