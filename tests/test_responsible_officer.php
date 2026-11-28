<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/citizen-remarks.php';
$rows=[
 ['officer_id'=>10,'assigned_at'=>'2026-11-01 09:00:00','unassigned_at'=>'2026-11-02 09:00:00','is_current'=>0],
 ['officer_id'=>20,'assigned_at'=>'2026-11-02 09:00:00','unassigned_at'=>null,'is_current'=>1],
];
function expect_owner($rows,$time,$expected){$actual=citizen_responsible_officer($rows,$time);if (($actual['officer_id']??null)!==$expected)throw new RuntimeException('Incorrect responsibility at '.$time);}
expect_owner($rows,'2026-11-01 12:00:00',10);
expect_owner($rows,'2026-11-03 12:00:00',20);
expect_owner($rows,'2026-10-01 12:00:00',null);
expect_owner($rows,'2026-11-02 09:00:00',null);
$event=citizen_status_event(['new_status'=>'In Progress','changed_at'=>'2026-11-03 12:00:00','remarks'=>'Testing','can_show_text'=>1,'responsible_name'=>'Fixture officer','responsible_designation'=>'AE/JE','actor_name'=>'Operator']);
if($event['officer_name']!=='Fixture officer'||$event['remarks']!=='Testing')throw new RuntimeException('Incorrect attribution');
echo "Responsibility tests passed.\n";
