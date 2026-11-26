<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/bootstrap.php';
require_once __DIR__.'/../includes/legacy-complaint-history.php';
$auth=require_officer();
$officerId=(int)$auth['officer']['id']; $loginId=(int)$auth['user']['id'];
$pageTitle='Historical Complaints';
$pdo=db(); $ready=legacy_history_available($pdo);
$counts=$ready?legacy_history_counts($pdo,$officerId,$loginId):null;
$rows=[]; $total=0;
$page=max(1,min(100000,(int)($_GET['page']??1))); $perPage=25;
$search=substr(trim((string)($_GET['q']??'')),0,75);
if ($ready) {
    $history=legacy_history_relationship_sql();
    $from="FROM ($history) h JOIN complaints c ON c.id=h.complaint_id
        LEFT JOIN departments d ON d.id=c.department_id
        LEFT JOIN complaint_categories cc ON cc.id=c.category_id
        WHERE h.officer_id=? AND h.login_user_id=? AND c.is_deleted=0";
    $args=[$officerId,$loginId];
    if ($search!=='') { $from.=" AND c.reference_no=?"; $args[]=$search; }
    $stmt=$pdo->prepare("SELECT COUNT(*) $from"); $stmt->execute($args); $total=(int)$stmt->fetchColumn();
    $page=min($page,max(1,(int)ceil($total/$perPage))); $offset=($page-1)*$perPage;
    $stmt=$pdo->prepare("SELECT c.reference_no,c.status,c.created_at,h.last_action_at,
        d.department_name,cc.category_name $from
        ORDER BY h.last_action_at DESC,c.id DESC LIMIT $perPage OFFSET $offset");
    $stmt->execute($args); $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
}
header('Cache-Control: no-store, private');
include __DIR__.'/../includes/employee-header.php';
?>
<div class="d-flex justify-content-between flex-wrap gap-2 mb-3"><div>
<h1 class="h3">Historical Complaints</h1>
<p class="text-muted">Verified original employee activity. Read-only; not current assignment or proof of resolution.</p></div>
<a class="btn btn-outline-primary align-self-start" href="<?=e(url('employee/my-complaints.php'))?>">Current complaints</a></div>
<?php if (!$ready): ?>
<div class="alert alert-info">Historical recovery has not been installed by the administrator.</div>
<?php else: ?>
<div class="row g-3 mb-3">
<?php foreach (['lifetime_total'=>'Current + approved historical (unique)','historical_total'=>'Approved historical complaints','additional_history'=>'Historical beyond current ownership'] as $key=>$label): ?>
<div class="col-md-4"><div class="card content-card p-3"><div class="text-muted"><?=e($label)?></div><div class="h3"><?=number_format((int)($counts[$key]??0))?></div></div></div>
<?php endforeach; ?></div>
<form class="d-flex gap-2 mb-3" method="get"><input class="form-control" name="q" value="<?=e($search)?>" placeholder="Exact complaint reference"><button class="btn btn-primary">Search</button></form>
<div class="card content-card p-3"><div class="table-responsive"><table class="table">
<thead><tr><th>Reference</th><th>Department</th><th>Category</th><th>Current status</th><th>Registered</th><th>Last approved source action</th></tr></thead><tbody>
<?php foreach ($rows as $row): ?><tr><?php foreach (['reference_no','department_name','category_name','status','created_at','last_action_at'] as $key): ?><td><?=e((string)($row[$key]??'—'))?></td><?php endforeach; ?></tr><?php endforeach; ?>
<?php if (!$rows): ?><tr><td colspan="6">No approved historical complaints found.</td></tr><?php endif; ?>
</tbody></table></div>
<div class="d-flex justify-content-between align-items-center"><span><?=number_format($total)?> results</span><div>
<?php if ($page>1): ?><a class="btn btn-outline-secondary btn-sm" href="?<?=e(http_build_query(['q'=>$search,'page'=>$page-1]))?>">Previous</a><?php endif; ?>
<?php if ($page*$perPage<$total): ?><a class="btn btn-outline-secondary btn-sm" href="?<?=e(http_build_query(['q'=>$search,'page'=>$page+1]))?>">Next</a><?php endif; ?>
</div></div></div>
<?php endif; ?>
<?php include __DIR__.'/../includes/employee-footer.php'; ?>
