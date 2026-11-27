<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/citizen-remarks.php';
$base = ['new_status'=>'Assigned','changed_at'=>'2026-01-01 12:00:00','visibility'=>null,'can_show_text'=>0];
$internal = citizen_status_event($base+['remarks'=>'Intelligent Reassignment applied: confidential batch information']);
if ($internal['remarks'] !== 'Complaint status recorded as Assigned.' || str_contains(json_encode($internal),'confidential')) throw new RuntimeException('Internal text exposed');
$legacy = citizen_status_event(array_replace($base,['new_status'=>'In Progress','remarks'=>'Status updated in ICTS system.']));
if ($legacy['remarks'] !== 'Status updated in ICTS system.') throw new RuntimeException('Safe imported update missing');
$published = citizen_status_event(array_replace($base,['can_show_text'=>1,'remarks'=>'Road repair completed.']));
if ($published['remarks'] !== 'Road repair completed.' || $published['event_label'] !== 'Municipal staff update') throw new RuntimeException('Published remark missing');
$private = citizen_status_event(array_replace($base,['visibility'=>'citizen_remark_private','remarks'=>'Status updated in ICTS system.']));
if ($private['remarks'] !== 'Complaint status recorded as Assigned.') throw new RuntimeException('Private note exposed');
echo "Citizen status event regression checks passed.\n";
