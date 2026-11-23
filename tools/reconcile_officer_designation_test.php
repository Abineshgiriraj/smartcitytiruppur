<?php
declare(strict_types=1);
/**
 * Reconcile one officer's designation from the local main database into the
 * isolated PDF test database. NEVER grants authority or assigns complaints.
 *
 * Preview (read-only):
 *   php tools/reconcile_officer_designation_test.php --expect-db=smartcity_escalation_test --reference=TCMC-2026-950326 --source-officer-id=16 --target-officer-id=4
 *
 * After verifying the two identities and official appointment, optionally:
 *   php tools/reconcile_officer_designation_test.php --expect-db=smartcity_escalation_test --reference=TCMC-2026-950326 --source-officer-id=16 --target-officer-id=4 --apply-test
 *
 * Does not modify source DB, policies, officer approvals, or complaints.
 */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit('CLI only'); }
$options = ['expect-db'=>null,'reference'=>null,'source-officer-id'=>null,'target-officer-id'=>null];
$apply = false;
foreach (array_slice($argv,1) as $arg) {
    if ($arg === '--apply-test') { $apply = true; continue; }
    if (preg_match('/^--(expect-db|reference|source-officer-id|target-officer-id)=(.+)$/D',$arg,$m)) {
        if ($options[$m[1]] !== null) { fwrite(STDERR, "Repeated option {$m[1]}\n"); exit(2); }
        $options[$m[1]] = $m[2];
        continue;
    }
    fwrite(STDERR, "Unknown argument {$arg}\n"); exit(2);
}
if ($options['expect-db'] !== 'smartcity_escalation_test'
    || !preg_match('/^[A-Z0-9-]{5,40}$/D',(string)$options['reference'])
    || !ctype_digit((string)$options['source-officer-id'])
    || !ctype_digit((string)$options['target-officer-id'])
    || (int)$options['source-officer-id'] < 1 || (int)$options['target-officer-id'] < 1) {
    fwrite(STDERR, "Required --expect-db=smartcity_escalation_test --reference=TCMC-... "
        ."--source-officer-id=N --target-officer-id=N\n"); exit(2);
}
require_once dirname(__DIR__).'/includes/bootstrap.php';
require_once dirname(__DIR__).'/includes/officer-authority.php';
if (APP_ENV !== 'local'
    || !in_array(strtolower((string)DB_HOST),['127.0.0.1','localhost','::1'],true)) {
    fwrite(STDERR, "Refusing: configured host must be local XAMPP.\n"); exit(2);
}
$pdo=db();
$actual=(string)$pdo->query('SELECT DATABASE()')->fetchColumn();
if ($actual !== 'smartcity_escalation_test') {
    fwrite(STDERR, "Refusing: connected to {$actual}, not smartcity_escalation_test.\n"); exit(2);
}
$sourceId=(int)$options['source-officer-id'];
$targetId=(int)$options['target-officer-id'];
$reference=(string)$options['reference'];
$normalizeName = static function(string $value):string {
    return trim(preg_replace('/\s+/u',' ',
        preg_replace('/[^A-Z0-9]+/',' ',strtoupper($value)) ?? '') ?? '');
};
$normalizePhone = static fn(?string $value):string =>
    preg_replace('/\D+/','',(string)$value) ?? '';

$loadComplaint=$pdo->prepare(
    "SELECT id,department_id,category_id,zone_id,ward_id,status,is_deleted,
            assigned_officer_id
     FROM complaints WHERE reference_no=? LIMIT 1"
);
$loadComplaint->execute([$reference]);
$complaint=$loadComplaint->fetch(PDO::FETCH_ASSOC);
if (!$complaint || $complaint['status'] !== 'Registered'
    || (int)$complaint['is_deleted'] !== 0 || $complaint['assigned_officer_id'] !== null) {
    fwrite(STDERR, "Refusing: target complaint must be Registered and unassigned in TEST DB.\n");
    exit(3);
}
$departmentId=(int)$complaint['department_id'];
$categoryId=(int)$complaint['category_id'];
$wardId=(int)$complaint['ward_id'];
$zoneId=(int)$complaint['zone_id'];
if (!valid_complaint_routing_context($pdo,$departmentId,$categoryId,$zoneId,$wardId)) {
    fwrite(STDERR, "Refusing: invalid complaint department/category/ward context.\n"); exit(3);
}
$policy=pdf_policy_reviewed($pdo,$categoryId);
$stage=$policy===null?null:pdf_policy_stage($policy,1);
if (!$stage) {
    fwrite(STDERR, "Refusing: target category has no approved PDF Stage 1 policy.\n"); exit(3);
}
$loadTarget=$pdo->prepare(
    'SELECT id,officer_name,mobile_no,designation,is_active
     FROM officers WHERE id=? LIMIT 1'
);
$loadTarget->execute([$targetId]);
$target=$loadTarget->fetch(PDO::FETCH_ASSOC);
// This is a deliberately fixed, trusted local schema: NEVER interpolate CLI SQL identifiers.
$loadSource=$pdo->prepare(
    'SELECT id,officer_name,mobile_no,designation,is_active
     FROM smartcitytiruppur.officers WHERE id=? LIMIT 1'
);
try {
    $loadSource->execute([$sourceId]);
    $source=$loadSource->fetch(PDO::FETCH_ASSOC);
} catch (Throwable $error) {
    fwrite(STDERR, "Could not read smartcitytiruppur.officers using this DB user. "
        ."No changes. Details: {$error->getMessage()}\n"); exit(3);
}
if (!$source || !$target) {
    fwrite(STDERR, "Source/target officer ID was not found. No changes.\n"); exit(3);
}
$sourcePhone=$normalizePhone($source['mobile_no']);
$targetPhone=$normalizePhone($target['mobile_no']);
if (strlen($sourcePhone)!==10 || $sourcePhone!==$targetPhone
    || $normalizeName((string)$source['officer_name'])
        !== $normalizeName((string)$target['officer_name'])) {
    fwrite(STDERR, "Identity mismatch: require the SAME full normalized name and 10-digit mobile. "
        ."No changes.\n"); exit(3);
}
$srcDuplicates=$pdo->prepare(
    'SELECT COUNT(*) FROM smartcitytiruppur.officers WHERE mobile_no=?'
);
$dstDuplicates=$pdo->prepare('SELECT COUNT(*) FROM officers WHERE mobile_no=?');
$srcDuplicates->execute([(string)$source['mobile_no']]);
$dstDuplicates->execute([(string)$target['mobile_no']]);
if ((int)$srcDuplicates->fetchColumn()!==1 || (int)$dstDuplicates->fetchColumn()!==1) {
    fwrite(STDERR, "Non-unique mobile in source or target. Manual identity review required.\n"); exit(3);
}
if ((int)$source['is_active']!==1 || (int)$target['is_active']!==1) {
    fwrite(STDERR, "Inactive source or target officer. No changes.\n"); exit(3);
}
$sourceDesignation=trim((string)$source['designation']);
$targetDesignation=trim((string)$target['designation']);
if ($sourceDesignation==='' || !pdf_policy_role_matches((string)$stage['role_label'],$sourceDesignation)) {
    fwrite(STDERR, "Source designation does not match approved PDF Stage 1 role "
        .$stage['role_label'].". No changes.\n"); exit(3);
}
$mapped=$pdo->prepare(
    'SELECT
        EXISTS(SELECT 1 FROM officer_department_map
               WHERE officer_id=? AND department_id=?) AS department_ok,
        EXISTS(SELECT 1 FROM officer_category_map
               WHERE officer_id=? AND category_id=?) AS category_ok,
        EXISTS(SELECT 1 FROM officer_ward_map
               WHERE officer_id=? AND ward_id=?) AS ward_ok'
);
$mapped->execute([$targetId,$departmentId,$targetId,$categoryId,$targetId,$wardId]);
$scope=$mapped->fetch(PDO::FETCH_ASSOC);
if (!$scope || !(int)$scope['department_ok'] || !(int)$scope['category_ok']
    || !(int)$scope['ward_ok']) {
    fwrite(STDERR, "Target officer is not mapped to complaint department/category/ward. "
        ."No changes.\n"); exit(3);
}
echo "LOCAL TEST DESIGNATION RECONCILIATION\n";
echo "Complaint {$reference}, category #{$categoryId}, ward #{$wardId}\n";
echo "Source: smartcitytiruppur officer #{$sourceId}, {$source['officer_name']}, "
    ."title={$sourceDesignation}\n";
echo "Target: smartcity_escalation_test officer #{$targetId}, {$target['officer_name']}, "
    ."title=".($targetDesignation===''?'[MISSING]':$targetDesignation)."\n";
echo "Same normalized name and mobile ending ".substr($targetPhone,-4)
    ."; explicit department/category/ward mappings confirmed.\n";
echo "Required PDF Stage 1: {$stage['role_label']}, {$stage['period_days']} day(s).\n";
if ($targetDesignation!=='' && $targetDesignation!==$sourceDesignation) {
    fwrite(STDERR, "Conflict: nonempty test designation differs. Refusing to overwrite.\n"); exit(3);
}
if (!$apply) {
    echo "DRY RUN: no changes. This comparison does not prove municipal appointment authority.\n";
    echo "If authorized identity/title evidence is verified, rerun with --apply-test to "
       ."copy ONLY the designation into the TEST database.\n";
    exit(0);
}
if ($targetDesignation===$sourceDesignation) {
    echo "ALREADY MATCHED: no update necessary. No approvals or assignments created.\n";
    exit(0);
}
try {
    $pdo->beginTransaction();
    $update=$pdo->prepare(
        "UPDATE officers SET designation=?, updated_at=NOW()
         WHERE id=? AND is_active=1
           AND (designation IS NULL OR TRIM(designation)='')
           AND mobile_no=?"
    );
    $update->execute([$sourceDesignation,$targetId,$target['mobile_no']]);
    if ($update->rowCount()!==1) {
        throw new RuntimeException('Officer record changed during verification or update.');
    }
    $pdo->commit();
    if (function_exists('log_message')) {
        log_message('TEST ONLY copied officer title from main #'.$sourceId
            .' to test #'.$targetId.'; category='.$categoryId
            .'; complaint='.$reference.'; source role='.$sourceDesignation);
    }
    echo "UPDATED TEST officer #{$targetId} designation to {$sourceDesignation}.\n";
    echo "NO officer_stage_approvals created. NO complaints/assignments/escalations changed.\n";
    echo "Stage 1 remains blocked until a genuinely authorized officer approval exists.\n";
} catch (Throwable $error) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    fwrite(STDERR, "Rolled back: {$error->getMessage()}\n"); exit(3);
}
