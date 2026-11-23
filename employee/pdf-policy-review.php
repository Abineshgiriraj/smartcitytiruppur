<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/bootstrap.php';
require_once __DIR__.'/../includes/pdf-escalation-policy.php';
$user=require_roles(['Super Admin']);
require_permission('masters.manage');
$pdo=db();
$pageTitle='PDF Escalation Policy Review';
$categoryId=(int)($_GET['category_id']??$_POST['category_id']??0);
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    $reference=trim((string)($_POST['approval_reference']??''));
    if($categoryId<1||strlen($reference)<8||strlen($reference)>255||empty($_POST['confirm_policy'])){
        flash('danger','Category, signed municipal approval reference, and confirmation are required.');
        redirect('employee/pdf-policy-review.php');
    }
    try{
        $pdo->beginTransaction();
        $lock=$pdo->prepare('SELECT * FROM pdf_escalation_policies WHERE category_id=? FOR UPDATE');
        $lock->execute([$categoryId]);$policy=$lock->fetch(PDO::FETCH_ASSOC);
        if(!$policy)throw new RuntimeException('PDF policy does not exist for this category.');
        if((int)$policy['is_approved']===1)throw new RuntimeException('Policy is already approved.');
        if($policy['pdf_sha256']!=='03a496809403a2de6a597a844a36ffc979de47657541342924628c8d33d70c4b'){
            throw new RuntimeException('Source PDF fingerprint differs.');
        }
        $stages=$pdo->prepare('SELECT stage_no,role_label,period_days FROM pdf_escalation_policy_stages WHERE category_id=? ORDER BY stage_no');
        $stages->execute([$categoryId]);$rows=$stages->fetchAll(PDO::FETCH_ASSOC);
        if(count($rows)!==(int)$policy['stage_count'])throw new RuntimeException('Incomplete PDF stages.');
        $sum=0;
        foreach($rows as $offset=>$stage){
            if((int)$stage['stage_no']!==$offset+1||(int)$stage['period_days']<=0){
                throw new RuntimeException('Zero-day or invalid stage: requires separate official correction, not one-click approval.');
            }
            $sum+=(int)$stage['period_days'];
        }
        if($sum!==(int)$policy['total_days'])throw new RuntimeException('PDF SLA totals do not reconcile.');
        $save=$pdo->prepare('UPDATE pdf_escalation_policies
            SET is_approved=1,approval_reference=?,approved_by=?,approved_at=NOW()
            WHERE category_id=? AND is_approved=0');
        $save->execute([$reference,(int)$user['id'],$categoryId]);
        if($save->rowCount()!==1)throw new RuntimeException('Policy status changed.');
        $pdo->commit();
        audit_log((int)$user['id'],'pdf_escalation_policy_approved','complaint_category',$categoryId,
            ['reference'=>$reference,'source_row'=>$policy['source_row']]);
        flash('success','Reviewed PDF stage policy approved. Officer assignments still require separate verified approvals.');
    } catch(Throwable $error){
        if($pdo->inTransaction())$pdo->rollBack();
        flash('danger',APP_ENV==='local'?$error->getMessage():'Policy approval did not complete.');
    }
    redirect('employee/pdf-policy-review.php?category_id='.$categoryId);
}
$policies=[];$selected=null;$stages=[];
try{
    $policies=$pdo->query(
        'SELECT p.category_id,p.source_row,p.total_days,p.stage_count,p.is_approved,
                cc.category_name,d.department_name
         FROM pdf_escalation_policies p
         JOIN complaint_categories cc ON cc.id=p.category_id
         JOIN departments d ON d.id=cc.department_id
         ORDER BY d.department_name,cc.category_name LIMIT 400'
    )->fetchAll(PDO::FETCH_ASSOC);
    if($categoryId>0){
        foreach($policies as $p)if((int)$p['category_id']===$categoryId){$selected=$p;break;}
        $s=$pdo->prepare('SELECT stage_no,role_label,period_days FROM pdf_escalation_policy_stages WHERE category_id=? ORDER BY stage_no');
        $s->execute([$categoryId]);$stages=$s->fetchAll(PDO::FETCH_ASSOC);
    }
}catch(Throwable $error){flash('danger','PDF policy tables not installed. Review the schema migration in the isolated test database.');}
include __DIR__.'/../includes/employee-header.php';
?>
<div class="admin-heading"><div><span class="admin-eyebrow">PDF based workflow</span>
<h1>Escalation Policy Review</h1>
<p>Inspect department-specific levels and deadlines from 21.11.26.pdf before authorizing any policy.</p></div></div>
<div class="alert alert-warning">The PDF specifies role titles and days, <strong>not employee identities</strong>. Policy approval does not approve any officer or activate background escalation. Categories containing zero-day stages require separate signed correction.</div>
<div class="card p-4 mb-3">
<form method="get"><label class="form-label" for="category_id">Complaint category</label>
<select id="category_id" name="category_id" class="form-select" onchange="this.form.submit()">
<option value="">Choose a category</option>
<?php foreach($policies as $p): ?>
<option value="<?php echo (int)$p['category_id']; ?>" <?php echo (int)$p['category_id']===$categoryId?'selected':''; ?>>
<?php echo e($p['department_name'].' / '.$p['category_name'].(' ('.$p['stage_count'].' levels)')); ?></option>
<?php endforeach; ?></select></form></div>
<?php if($selected): ?>
<div class="card p-4"><h2 class="admin-panel-heading mb-2"><?php echo e($selected['category_name']); ?></h2>
<p class="text-muted">PDF row #<?php echo (int)$selected['source_row']; ?> · Total <?php echo (int)$selected['total_days']; ?> days · <?php echo $selected['is_approved']?'Approved':'Awaiting review'; ?></p>
<table class="table"><thead><tr><th>Stage</th><th>Required role</th><th>Days</th></tr></thead><tbody>
<?php foreach($stages as $step): ?><tr><td><?php echo (int)$step['stage_no']; ?></td><td><?php echo e($step['role_label']); ?></td>
<td><?php echo (int)$step['period_days']; ?><?php if((int)$step['period_days']===0): ?> <strong class="text-danger">Requires correction</strong><?php endif; ?></td></tr><?php endforeach; ?>
</tbody></table>
<?php if(!$selected['is_approved']): ?>
<form method="post" class="mt-3">
<?php echo csrf_field(); ?><input type="hidden" name="category_id" value="<?php echo $categoryId; ?>">
<label class="form-label" for="approval_reference">Official policy approval document reference</label>
<input id="approval_reference" class="form-control mb-3" name="approval_reference" minlength="8" maxlength="255" required>
<label class="form-check mb-3"><input class="form-check-input" type="checkbox" name="confirm_policy" value="1" required>
<span class="form-check-label">I verified these specific stages and periods against official municipal authorization.</span></label>
<button type="submit" class="btn btn-primary">Approve reviewed PDF policy</button></form>
<?php endif; ?>
</div>
<?php endif; ?>
<?php include __DIR__.'/../includes/employee-footer.php'; ?>
