<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/citizen-contact-sharing.php';
$c=['id'=>1,'mobile_no'=>'9000000000','email'=>'fixture@example.invalid'];
$d=['action'=>'citizen_contact_share_enabled','details_json'=>json_encode(['fingerprint'=>citizen_contact_fingerprint($c)])];
function check_contact($ok,$message){if (!$ok) throw new RuntimeException($message);}
check_contact(!citizen_contact_consent_matches($c,null),'Missing consent must be private');
check_contact(citizen_contact_consent_matches($c,$d),'Explicit consent not honored');
check_contact(!citizen_contact_consent_matches(array_replace($c,['email'=>'other@example.invalid']),$d),'Changed email must be private');
check_contact(!citizen_contact_consent_matches(array_replace($c,['mobile_no'=>'9000000001']),$d),'Changed mobile must be private');
check_contact(!citizen_contact_consent_matches(array_replace($c,['id'=>2]),$d),'Consent must not apply to other complaints');
check_contact(!citizen_contact_consent_matches($c,array_replace($d,['action'=>'citizen_contact_share_disabled'])),'Revocation ignored');
echo "Citizen contact sharing checks passed.\n";
