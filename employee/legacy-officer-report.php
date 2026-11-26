<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/bootstrap.php';
require_once __DIR__.'/../includes/legacy-complaint-history.php';
// Separate all-municipality report. Do not bypass Department Head scoping.
require_roles(['Super Admin','Admin','Commissioner']);
require_permission('complaints.view');
$pdo=db(); $pageTitle='Historical Officer Report'; $ready=legacy_history_available($pdo); $rows=[];
if ($ready) {
    $history=legacy_history_relationship_sql();
    $rows=$pdo->query("SELECT o.id,o.officer_name,o.designation,
      COALESCE(x.current_total,0) current_total,COALESCE(x.history_total,0) history_total,
      COALESCE(x.lifetime_total,0) lifetime_total
      FROM officers o JOIN users u ON u.id=o.user_id AND u.is_active=1
      LEFT JOIN (SELECT officer_id,SUM(is_current) current_total,SUM(is_historical) history_total,COUNT(*) lifetime_total
        FROM (SELECT officer_id,complaint_id,MAX(is_current) is_current,MAX(is_historical) is_historical
          FROM (SELECT assigned_officer_id officer_id,id complaint_id,1 is_current,0 is_historical
                FROM complaints WHERE is_deleted=0 AND assigned_officer_id IS NOT NULL
                UNION ALL SELECT officer_id,complaint_id,0,1 FROM ($history) h) source_rows
          GROUP BY officer_id,complaint_id) unique_rows GROUP BY officer_id) x ON x.officer_id=o.id
      WHERE o.is_active=1 AND u.role IN ('Field Officer','Officer','Admin','Super Admin')
      ORDER BY lifetime_total DESC,o.id")->fetchAll(PDO::FETCH_ASSOC);
}
header('Cache-Control: no-store, private');
include __DIR__.'/../includes/employee-header.php';
?>
<h1 class="h3">Historical Officer Report</h1>
<p class="text-muted">Approved legacy activity and current ownership. Unique per officer; totals across officers may overlap.</p>
<a class="btn btn-outline-primary mb-3" href="<?=e(url('employee/officer-complaint-report.php'))?>">Current officer report</a>
<?php if (!$ready): ?><div class="alert alert-info">Historical recovery migration is not installed.</div><?php else: ?>
<div class="card content-card p-3"><div class="table-responsive"><table class="table">
<thead><tr><th>Officer ID</th><th>Officer</th><th>Designation</th><th>Current ownership</th><th>Approved historical</th><th>Unique combined</th></tr></thead><tbody>
<?php foreach ($rows as $row): ?><tr><?php foreach (['id','officer_name','designation','current_total','history_total','lifetime_total'] as $key): ?><td><?=e((string)($row[$key]??'—'))?></td><?php endforeach; ?></tr><?php endforeach; ?>
</tbody></table></div></div><?php endif; ?>
<?php include __DIR__.'/../includes/employee-footer.php'; ?>
