<?php
declare(strict_types=1);
/**
 * Read-only status report for the single configured local smartcitytiruppur DB.
 * Run: php tools/report_local_pdf_workflow.php
 * Optional: --reference=TCMC-2026-123456 or --reference=718
 * No approvals, assignments, or background workers run here.
 */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit("CLI only\n"); }
$reference = null;
foreach (array_slice($argv,1) as $arg) {
    if (preg_match('/^--reference=([A-Za-z0-9-]{1,40})$/D',$arg,$m)) {
        $reference=$m[1]; continue;
    }
    fwrite(STDERR,"Unknown option. Use --reference=COMPLAINT_REFERENCE (optional)\n");
    exit(2);
}
require_once dirname(__DIR__) . '/includes/bootstrap.php';
$pdo=db();
$actual=(string)$pdo->query('SELECT DATABASE()')->fetchColumn();
if (DB_NAME !== 'smartcitytiruppur' || $actual !== 'smartcitytiruppur'
    || APP_ENV !== 'local'
    || !in_array(strtolower((string)DB_HOST),['127.0.0.1','localhost','::1'],true)) {
    fwrite(STDERR,"Refusing: local smartcitytiruppur database is required. No writes.\n");
    exit(2);
}
echo "READ-ONLY PDF WORKFLOW STATUS: {$actual}\n";
$summary=$pdo->query(
    'SELECT COUNT(*) AS total, SUM(is_approved=1) AS approved,
            SUM(is_approved=0) AS draft
     FROM pdf_escalation_policies'
)->fetch(PDO::FETCH_ASSOC);
echo "PDF policies: total=".(int)$summary['total']
    .", approved=".(int)$summary['approved']
    .", draft=".(int)$summary['draft']."\n";
$approvals=$pdo->query(
    "SELECT COUNT(*) AS total,
            SUM(revoked_at IS NULL AND approved_by IS NOT NULL
                AND TRIM(approval_reference)<>'') AS active
     FROM officer_stage_approvals"
)->fetch(PDO::FETCH_ASSOC);
echo "Officer stage approvals: total=".(int)$approvals['total']
    .", active-records=".(int)$approvals['active']."\n";
$pending=(int)$pdo->query(
    "SELECT COUNT(*) FROM complaints
     WHERE is_deleted=0 AND status='Registered' AND assigned_officer_id IS NULL"
)->fetchColumn();
echo "Registered complaints still unassigned: {$pending}\n";
$sql="SELECT c.id,c.reference_no,c.status,c.category_id,
             c.department_id,c.zone_id,c.ward_id,
             cc.category_name,d.department_name,
             p.is_approved,p.approval_reference,p.approved_by,p.approved_at
      FROM complaints c
      JOIN complaint_categories cc ON cc.id=c.category_id
      JOIN departments d ON d.id=c.department_id
      LEFT JOIN pdf_escalation_policies p ON p.category_id=c.category_id
      WHERE c.is_deleted=0";
$params=[];
if ($reference !== null) {
    $sql.=' AND c.reference_no=?';
    $params[]=$reference;
} else {
    $sql.=" AND c.status='Registered' AND c.assigned_officer_id IS NULL";
}
$sql.=' ORDER BY c.id DESC LIMIT 10';
$stmt=$pdo->prepare($sql);$stmt->execute($params);
$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
echo $reference!==null?"Complaint reference: {$reference}\n":"Recent 10 unassigned Registered complaints:\n";
if (!$rows) echo "None found for the selected filter.\n";
foreach ($rows as $row) {
    $id=(int)$row['id'];
    $category=(int)$row['category_id'];
    $approved=(int)($row['is_approved']??0)===1
        && !empty($row['approval_reference'])
        && !empty($row['approved_by'])
        && !empty($row['approved_at']);
    $state=$row['is_approved']===null ? 'MISSING'
        : ($approved?'APPROVED_RECORD':'DRAFT_OR_APPROVAL_INCOMPLETE');
    echo "#{$id} {$row['reference_no']} | {$row['department_name']} / {$row['category_name']}"
        ." | DB category={$category}, zone={$row['zone_id']}, ward={$row['ward_id']}"
        ." | policy={$state}\n";
    if ($reference!==null) {
        $verified=pdf_policy_reviewed($pdo,$category);
        echo "  PDF policy verification: ".($verified?'APPROVED AND VALID':'NOT READY')."\n";
        $query=$pdo->prepare(
            "SELECT COUNT(*) FROM officer_stage_approvals a
             WHERE a.category_id=? AND a.stage_no=1 AND a.revoked_at IS NULL
               AND a.approved_by IS NOT NULL AND TRIM(a.approval_reference)<>''"
        );
        $query->execute([$category]);
        echo "  Recorded Stage 1 approvals (not proof of routing eligibility): "
            .(int)$query->fetchColumn()."\n";
    }
}
echo "No database changes. No allocation or escalation attempted.\n";
