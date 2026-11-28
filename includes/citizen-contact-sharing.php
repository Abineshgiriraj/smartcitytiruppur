<?php
declare(strict_types=1);

/** Consent applies only to the exact contact values that were authorized. */
function citizen_contact_fingerprint(array $complaint): string
{
    return hash('sha256',json_encode([
        (int)$complaint['id'],(string)($complaint['mobile_no']??''),
        (string)($complaint['email']??''),
    ],JSON_THROW_ON_ERROR));
}

function citizen_contact_consent_matches(array $complaint,?array $decision): bool
{
    if (!$decision || ($decision['action']??'')!=='citizen_contact_share_enabled') return false;
    $data=json_decode((string)($decision['details_json']??''),true);
    return is_array($data) && is_string($data['fingerprint']??null)
        && hash_equals(citizen_contact_fingerprint($complaint),$data['fingerprint']);
}

function citizen_contact_is_public(PDO $pdo,array $complaint): bool
{
    $stmt=$pdo->prepare("SELECT action,details_json FROM audit_logs
      WHERE entity_type='complaint' AND entity_id=?
        AND action IN ('citizen_contact_share_enabled','citizen_contact_share_disabled')
      ORDER BY id DESC LIMIT 1");
    $stmt->execute([(int)$complaint['id']]);
    return citizen_contact_consent_matches($complaint,$stmt->fetch(PDO::FETCH_ASSOC)?:null);
}

/** Local development OTPs are disclosed to the requester, so are not ownership proof. */
function citizen_contact_can_manage(PDO $pdo,array $complaint): bool
{
    if (APP_ENV==='local') return false;
    $mobile=(string)($complaint['mobile_no']??'');
    $verified=(string)($_SESSION['otp_verified_mobile']['tracking']??'');
    $requestId=(int)($_SESSION['latest_otp_request']['tracking']??0);
    if ($mobile==='' || $verified==='' || !hash_equals($mobile,$verified) || $requestId<=0) return false;
    $stmt=$pdo->prepare("SELECT id FROM otp_requests WHERE id=? AND mobile_no=? AND purpose='tracking'
       AND verified_at IS NOT NULL AND verified_at>=DATE_SUB(NOW(),INTERVAL 15 MINUTE)
       AND verified_at<=expires_at LIMIT 1");
    $stmt->execute([$requestId,$mobile]);
    return (bool)$stmt->fetchColumn();
}

function citizen_contact_save(PDO $pdo,string $reference,bool $publish): bool
{
    $pdo->beginTransaction();
    try {
        $stmt=$pdo->prepare("SELECT id,mobile_no,email FROM complaints WHERE reference_no=? AND is_deleted=0 LIMIT 1 FOR UPDATE");
        $stmt->execute([$reference]);
        $complaint=$stmt->fetch(PDO::FETCH_ASSOC);
        if (!$complaint || !citizen_contact_can_manage($pdo,$complaint)) {
            $pdo->rollBack();
            return false;
        }
        $stmt=$pdo->prepare("INSERT INTO audit_logs (user_id,action,entity_type,entity_id,details_json)
          VALUES (NULL,?,'complaint',?,?)");
        $stmt->execute([$publish?'citizen_contact_share_enabled':'citizen_contact_share_disabled',
            (int)$complaint['id'],json_encode(['fingerprint'=>citizen_contact_fingerprint($complaint),'consent_version'=>1],JSON_THROW_ON_ERROR)]);
        $pdo->commit();
        return true;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $e;
    }
}
