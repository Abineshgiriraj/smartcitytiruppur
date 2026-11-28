<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/complaint-journey.php';
function check_journey($ok,$label){if(!$ok)throw new RuntimeException($label);}
check_journey(complaint_journey_location(['street_name'=>'Main Road','location_address'=>' main  road '])['street']==='', 'Duplicate location');
check_journey(complaint_journey_contact(['mobile_no'=>'9000000000','email'=>'officer@example.com','public_contact'=>0],false)['phone']==='', 'Unlisted contact');
check_journey(complaint_journey_contact(['mobile_no'=>'9000000000','email'=>'officer@example.com','public_contact'=>1],false)===['phone'=>'9000000000','email'=>''], 'Public directory contact');
$c=['assigned_officer_id'=>12];$a=[['officer_id'=>12,'is_current'=>1,'assigned_at'=>'2026-01-01 10:00:00','designation'=>'AE/JE']];
$p=['stages'=>[['role_label'=>'AE/JE']]];
check_journey(complaint_journey_stage($c,$a,[],$p)===1,'Initial stage');
$t=[['officer_id'=>12,'level_no'=>3,'escalated_at'=>'2026-01-01 10:00:00']];
check_journey(complaint_journey_stage($c,$a,$t,$p)===3,'Actual escalation');
$t[0]['officer_id']=99;
check_journey(complaint_journey_stage($c,$a,$t,$p)===null,'Stale transfer not current stage');
echo "Complaint journey regression checks passed.\n";
