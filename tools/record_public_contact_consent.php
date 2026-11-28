<?php
declare(strict_types=1);
// Local administrator tool for one complaint with independently documented consent.
// Never run against a production database or use for bulk publication.
if (PHP_SAPI !== 'cli') { http_response_code(403); exit('CLI only'); }
require_once __DIR__.'/../includes/bootstrap.php';
require_once __DIR__.'/../includes/citizen-contact-sharing.php';

$options=getopt('', ['reference:', 'expect-db:', 'publish', 'revoke', 'consent-confirmed']);
$ref=$options['reference']??null;
$expected=$options['expect-db']??null;
$publish=array_key_exists('publish',$options);
$revoke=array_key_exists('revoke',$options);
if (APP_ENV!=='local' || !is_string($ref) || !preg_match('/^[0-9]{1,5}$/D',$ref)
    || !is_string($expected) || $expected!=='smartcitytiruppur' || $publish===$revoke
    || ($publish && !array_key_exists('consent-confirmed',$options))) {
    fwrite(STDERR,"Local only. Specify one numeric complaint reference, --expect-db=smartcitytiruppur and either --publish --consent-confirmed or --revoke. Publish only with the contact owner's explicit documented consent.\n");
    exit(1);
}
$pdo=db();
if ($pdo->query('SELECT DATABASE()')->fetchColumn()!==$expected) {
    fwrite(STDERR,"Database mismatch. No changes made.\n"); exit(1);
}
try {
    $pdo->beginTransaction();
    $stmt=$pdo->prepare('SELECT id,mobile_no,email FROM complaints WHERE reference_no=? AND is_deleted=0 FOR UPDATE');
    $stmt->execute([$ref]);
    $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($rows)!==1) throw new RuntimeException('Complaint is missing or ambiguous.');
    $complaint=$rows[0];
    $stmt=$pdo->prepare("INSERT INTO audit_logs (user_id,action,entity_type,entity_id,details_json)
        VALUES (NULL,?,'complaint',?,?)");
    $stmt->execute([$publish?'citizen_contact_share_enabled':'citizen_contact_share_disabled',
        (int)$complaint['id'],json_encode(['fingerprint'=>citizen_contact_fingerprint($complaint),
        'consent_version'=>1,'source'=>'local_admin_documented_consent'],JSON_THROW_ON_ERROR)]);
    $pdo->commit();
    echo $publish?"Public contact sharing recorded for this complaint only.\n":"Public contact sharing revoked for this complaint only.\n";
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    fwrite(STDERR,"Contact sharing update failed. No changes committed.\n"); exit(1);
}
