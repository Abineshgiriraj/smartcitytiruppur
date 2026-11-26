<?php
declare(strict_types=1);
// CLI only. Default is a read-only preview. No passwords/contact values exported.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__.'/../includes/bootstrap.php';
require_once __DIR__.'/../includes/legacy-complaint-history.php';

function fail_legacy(string $message): never { throw new RuntimeException($message); }
function legacy_positive_id(string $value): int {
    if (!preg_match('/^[1-9][0-9]*$/D',$value) || (string)(int)$value !== $value) {
        fail_legacy('Invalid positive integer ID.');
    }
    return (int)$value;
}

$opts = getopt('', ['approved-csv:', 'admin-user-id:', 'apply', 'confirm-db:']);
$apply = isset($opts['apply']);
$pdo = db();
$lock = false;
try {
    $database = (string)$pdo->query('SELECT DATABASE()')->fetchColumn();
    if (!isset($opts['approved-csv'])) {
        echo "Read-only coverage by original employee and action code (not approvals):\n";
        $rows = $pdo->query("SELECT m.UserMasterID legacy_user_id,l.ActionCodeMasterID action_code,
            COUNT(DISTINCT l.ComplaintLifeCycleID) events,COUNT(DISTINCT c.id) complaints
            FROM raw_complaintlifecycle l
            JOIN raw_userdesignationmapper m ON m.UserDesignationMapperID=l.UserDesignationMapperID
            JOIN raw_usermaster ru ON ru.UserMasterID=m.UserMasterID
            JOIN raw_rolemaster rr ON rr.RoleMasterID=ru.RoleMasterID AND rr.Role='Employee'
            JOIN raw_complaints rc ON rc.ComplaintID=l.ComplaintID
            JOIN complaints c ON c.reference_no=rc.ComplaintReferenceNo AND c.is_deleted=0
            GROUP BY m.UserMasterID,l.ActionCodeMasterID ORDER BY m.UserMasterID,l.ActionCodeMasterID");
        fputcsv(STDOUT,['legacy_user_id','action_code','events','complaints']);
        foreach ($rows as $row) fputcsv(STDOUT,array_values($row));
        if ($apply) fail_legacy('Apply requires a reviewed CSV.');
        exit(0);
    }
    if ($apply && (!isset($opts['confirm-db']) || $opts['confirm-db'] !== $database)) {
        fail_legacy('Apply requires --confirm-db matching the connected database.');
    }
    $admin = legacy_positive_id((string)($opts['admin-user-id']??''));
    $adminCheck = $pdo->prepare("SELECT 1 FROM users WHERE id=? AND role='Super Admin' AND is_active=1");
    $adminCheck->execute([$admin]);
    if (!$adminCheck->fetchColumn()) fail_legacy('Approving ID must be an active Super Admin.');
    $file = fopen((string)$opts['approved-csv'],'rb');
    if (!$file) fail_legacy('Cannot read approval CSV.');
    $expected = ['legacy_user_id','officer_id','login_user_id','action_code','evidence_reference'];
    $header = fgetcsv($file);
    if ($header !== $expected) fail_legacy('Incorrect approval CSV header.');
    $approvals = [];
    while (($row=fgetcsv($file)) !== false) {
        if ($row === [null]) continue;
        if (count($row)!==5) fail_legacy('Incorrect approval CSV row.');
        [$legacy,$officer,$login,$action,$evidence] = array_map('trim',$row);
        $legacy=legacy_positive_id($legacy); $officer=legacy_positive_id($officer); $login=legacy_positive_id($login);
        if (!in_array($action,['0','1'],true) || $evidence==='' || strlen($evidence)>255) {
            fail_legacy('Each row needs a reviewed action code (0 or 1) and nonempty evidence reference.');
        }
        $key=$legacy.':'.$action;
        if (isset($approvals[$key])) fail_legacy('Duplicate employee/action approval.');
        $approvals[$key]=[$legacy,$officer,$login,(int)$action,$evidence];
    }
    fclose($file);
    if (!$approvals) fail_legacy('No approved rows. Nothing imported.');
    // A reviewed batch must not assign one source employee to multiple identities
    // or combine unrelated source employees into one identity in this first rollout.
    $legacyToOfficer=[]; $officerToLegacy=[];
    foreach ($approvals as [$legacy,$officer,$login,$action,$evidence]) {
        if ((isset($legacyToOfficer[$legacy]) && $legacyToOfficer[$legacy]!==[$officer,$login])
            || (isset($officerToLegacy[$officer]) && $officerToLegacy[$officer]!==$legacy)) {
            fail_legacy('Conflicting reviewed identities in CSV.');
        }
        $legacyToOfficer[$legacy]=[$officer,$login]; $officerToLegacy[$officer]=$legacy;
    }
    $identity=$pdo->prepare("SELECT 1 FROM raw_usermaster ru
        JOIN raw_rolemaster rr ON rr.RoleMasterID=ru.RoleMasterID AND rr.Role='Employee'
        JOIN officers o ON o.id=? AND o.user_id=? AND o.is_active=1
        JOIN users u ON u.id=o.user_id AND u.is_active=1
            AND u.role IN ('Officer','Field Officer','Admin','Super Admin')
        WHERE ru.UserMasterID=?");
    // Reject ambiguous source references rather than duplicating historical records.
    $source = "FROM raw_complaintlifecycle l
        JOIN raw_userdesignationmapper m ON m.UserDesignationMapperID=l.UserDesignationMapperID
        JOIN raw_complaints rc ON rc.ComplaintID=l.ComplaintID
        JOIN complaints c ON c.reference_no=rc.ComplaintReferenceNo AND c.is_deleted=0
        WHERE m.UserMasterID=? AND l.ActionCodeMasterID=?
        AND rc.ComplaintReferenceNo IS NOT NULL AND TRIM(rc.ComplaintReferenceNo)<>''
        AND NOT EXISTS (SELECT 1 FROM raw_complaints dup
            WHERE dup.ComplaintReferenceNo=rc.ComplaintReferenceNo AND dup.ComplaintID<>rc.ComplaintID)";
    $count=$pdo->prepare("SELECT COUNT(DISTINCT l.ComplaintLifeCycleID) events,COUNT(DISTINCT c.id) complaints $source");
    if ($apply) {
        if (!legacy_history_available($pdo)) fail_legacy('Run the additive migration on the test database first.');
        $lock=(int)$pdo->query("SELECT GET_LOCK('smartcity_legacy_history_import',0)")->fetchColumn()===1;
        if (!$lock) fail_legacy('Another history import is running.');
        $pdo->beginTransaction();
        $existing=$pdo->prepare("SELECT * FROM legacy_complaint_history_approvals
             WHERE legacy_user_id=? AND action_code=? FOR UPDATE");
        $conflicts=$pdo->prepare("SELECT COUNT(*) FROM legacy_complaint_history_approvals
            WHERE (legacy_user_id=? AND (officer_id<>? OR login_user_id<>?))
               OR (officer_id=? AND legacy_user_id<>?)");
        $addApproval=$pdo->prepare("INSERT INTO legacy_complaint_history_approvals
            (legacy_user_id,action_code,officer_id,login_user_id,approved_by,evidence_reference)
            VALUES (?,?,?,?,?,?)");
        $insert=$pdo->prepare("INSERT INTO legacy_complaint_history
            (source_lifecycle_id,source_complaint_id,complaint_id,approval_id,source_action_at)
            SELECT l.ComplaintLifeCycleID,rc.ComplaintID,c.id,?,l.StatusDate $source
            AND NOT EXISTS (SELECT 1 FROM legacy_complaint_history already
                WHERE already.source_lifecycle_id=l.ComplaintLifeCycleID)");
        $collision=$pdo->prepare("SELECT COUNT(*) $source AND EXISTS
            (SELECT 1 FROM legacy_complaint_history already
             WHERE already.source_lifecycle_id=l.ComplaintLifeCycleID AND already.approval_id<>?)");
    }
    foreach ($approvals as [$legacy,$officer,$login,$action,$evidence]) {
        $identity->execute([$officer,$login,$legacy]);
        if (!$identity->fetchColumn()) fail_legacy('Employee/account/role mismatch for legacy ID '.$legacy);
        $count->execute([$legacy,$action]); $totals=$count->fetch(PDO::FETCH_ASSOC);
        if (!(int)$totals['events']) fail_legacy('No unambiguous source events for legacy ID '.$legacy);
        echo ($apply?'APPLY ':'PREVIEW ').$legacy.' => '.$officer.' action '.$action.
            ': '.$totals['events'].' events / '.$totals['complaints']." complaints\n";
        if (!$apply) continue;
        $conflicts->execute([$legacy,$officer,$login,$officer,$legacy]);
        if ((int)$conflicts->fetchColumn()) fail_legacy('Conflicting existing approval.');
        $existing->execute([$legacy,$action]); $old=$existing->fetch(PDO::FETCH_ASSOC);
        if ($old) {
            if ($old['revoked_at']!==null || (int)$old['officer_id']!==$officer || (int)$old['login_user_id']!==$login
                || (string)$old['evidence_reference']!==$evidence) fail_legacy('Existing approval differs or is revoked; review manually.');
            $approval=(int)$old['id'];
        } else {
            $addApproval->execute([$legacy,$action,$officer,$login,$admin,$evidence]);
            $approval=(int)$pdo->lastInsertId();
        }
        $collision->execute([$legacy,$action,$approval]);
        if ((int)$collision->fetchColumn()) fail_legacy('Source event belongs to another approval.');
        $insert->execute([$approval,$legacy,$action]);
        echo 'New historical events: '.$insert->rowCount()."\n";
    }
    if ($apply) $pdo->commit();
    echo $apply?"Committed approved history only. Current ownership unchanged.\n":"Preview complete. No database changes.\n";
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    fwrite(STDERR,"Legacy history import stopped: ".$e->getMessage()."\n");
    exit(1);
} finally {
    if ($lock) $pdo->query("SELECT RELEASE_LOCK('smartcity_legacy_history_import')");
}
