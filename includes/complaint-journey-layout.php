<?php
// Only actual recorded handlers. No policy stages or predicted transfers.
$handled=[];
foreach ($detailJourney['assignments'] as $assignment) {
    $id=(int)$assignment['officer_id'];
    if (!$id) continue;
    $assignment['started_at']=$assignment['assigned_at'];
    $handled[$id.'|'.$assignment['started_at']]=$assignment;
}
foreach ($detailJourney['transfers'] as $transfer) {
    $id=(int)$transfer['officer_id'];
    if (!$id) continue;
    $key=$id.'|'.$transfer['escalated_at'];
    if (!isset($handled[$key])) {
        $transfer['started_at']=$transfer['escalated_at'];
        $transfer['unassigned_at']=null;
        $handled[$key]=$transfer;
    }
}
$currentId=(int)($complaint['assigned_officer_id']??0);
$distinct=array_unique(array_map(static fn($a)=>(int)$a['officer_id'],$handled));
if ($currentId) $distinct=array_unique(array_merge($distinct,[$currentId]));
usort($handled,static fn($a,$b)=>strcmp($b['started_at'],$a['started_at']));
?>
<?php if (count($distinct)>1): ?>
<section class="scd-panel" aria-labelledby="scd-assignments-title">
  <div class="scd-section-title"><span class="scd-section-icon"><i class="bi bi-people"></i></span><div><h2 id="scd-assignments-title">Officers Who Handled This Complaint</h2></div></div>
  <ol class="scd-timeline">
    <?php foreach ($handled as $assignment): ?>
      <?php $current=(int)$assignment['officer_id']===$currentId && (int)($assignment['is_current']??0)===1; ?>
      <li class="scd-event"><span class="scd-event-dot"></span>
        <div class="scd-event-top"><strong><?= $current?'Current officer':((int)$assignment['officer_id']===$currentId?'Recorded assignment':'Previous officer') ?></strong><time><?= e($detailDate($assignment['started_at'])) ?></time></div>
        <div class="scd-journey-person"><strong><?= e($assignment['officer_name']?:'Officer details unavailable') ?></strong><span><?= e($assignment['designation']??'') ?></span></div>
        <?php if ($assignment['contacts']['phone']!==''): ?><div><i class="bi bi-telephone"></i> <a href="tel:<?= e($detailContactPhone($assignment['contacts']['phone'])) ?>"><?= e($assignment['contacts']['phone']) ?></a></div><?php endif; ?>
        <?php if ($assignment['contacts']['email']!==''): ?><div><i class="bi bi-envelope"></i> <a href="mailto:<?= e($assignment['contacts']['email']) ?>"><?= e($assignment['contacts']['email']) ?></a></div><?php endif; ?>
        <?php if (!empty($assignment['unassigned_at'])): ?><small class="text-muted">Transferred on <?= e($detailDate($assignment['unassigned_at'])) ?></small><?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ol>
</section>
<?php endif; ?>
