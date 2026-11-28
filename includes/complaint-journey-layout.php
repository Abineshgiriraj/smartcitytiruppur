<?php
/** Read-only shared journey. Every actual event comes from a recorded assignment or transfer. */
?>
<section class="scd-panel" aria-labelledby="scd-journey-title">
  <div class="scd-section-title"><span class="scd-section-icon"><i class="bi bi-diagram-3"></i></span><div><small>COMPLAINT JOURNEY</small><h2 id="scd-journey-title">Escalation Progress</h2></div></div>
  <?php if ($detailJourney['policy']): ?>
    <p class="small text-muted"><?= $detailJourney['approved']?'Approved category policy':'Imported category policy (not formally approved)' ?> · <?= (int)$detailJourney['policy']['stage_count'] ?> stages · <?= (int)$detailJourney['policy']['total_days'] ?> days total</p>
    <ol class="scd-stage-track">
      <?php foreach ($detailJourney['policy']['stages'] as $step):
        $number=(int)$step['stage_no'];
        $reached=false;
        foreach ($detailJourney['transfers'] as $transfer) if ((int)$transfer['level_no']===$number) $reached=true;
        foreach ($detailJourney['assignments'] as $assignment) if ($assignment['stage']===$number) $reached=true;
        $current=$detailStage===$number;
      ?>
        <li class="<?= $current?'scd-stage-current':($reached?'scd-stage-reached':'') ?>">
          <span class="scd-stage-number"><?= $number ?></span>
          <strong><?= e($step['role_label']) ?></strong>
          <small><?= (int)$step['period_days'] ?> day(s)</small>
          <small><?= $current?($detailTerminal?'Last recorded stage':'Current stage'):($reached?'Recorded transfer / assignment':'Policy stage only') ?></small>
        </li>
      <?php endforeach; ?>
    </ol>
    <p class="small text-muted mb-0">Policy stages are not proof of past assignments. <?= $detailStage===null?'Current stage is not confirmed by the available records.':'' ?><?= $detailTerminal?' This complaint is marked '.$detailStatus.'.':'' ?></p>
  <?php else: ?><p class="text-muted">No validated escalation policy available. Recorded transfers are shown below.</p><?php endif; ?>
</section>
<section class="scd-panel" aria-labelledby="scd-assignments-title">
  <div class="scd-section-title"><span class="scd-section-icon"><i class="bi bi-people"></i></span><div><small>OFFICER ACCOUNTABILITY</small><h2 id="scd-assignments-title">Officer Assignment History</h2></div></div>
  <?php if (!$detailJourney['assignments']): ?><p class="scd-compact-empty">No dated assignment records available. The current officer, if any, is shown above.</p><?php else: ?>
    <ol class="scd-timeline">
      <?php foreach (array_reverse($detailJourney['assignments']) as $assignment):
        $isCurrent=(int)$assignment['is_current']===1 && (int)$assignment['officer_id']===(int)($complaint['assigned_officer_id']??0);
      ?>
        <li class="scd-event"><span class="scd-event-dot"></span>
          <div class="scd-event-top"><strong><?= $isCurrent?'Current assignment':'Previous assignment' ?><?= $assignment['stage']!==null?' · Stage '.(int)$assignment['stage']:'' ?></strong><time><?= e($detailDate($assignment['assigned_at'])) ?></time></div>
          <div class="scd-journey-person"><strong><?= e($assignment['officer_name']?:'Officer record unavailable') ?></strong> <span><?= e($detailDash($assignment['designation']??null)) ?></span></div>
          <?php if ($assignment['contacts']['phone']!==''): ?><div class="small"><i class="bi bi-telephone"></i> <a href="tel:<?= e($detailContactPhone($assignment['contacts']['phone'])) ?>"><?= e($assignment['contacts']['phone']) ?></a></div><?php endif; ?>
          <?php if ($assignment['contacts']['email']!==''): ?><div class="small"><i class="bi bi-envelope"></i> <a href="mailto:<?= e($assignment['contacts']['email']) ?>"><?= e($assignment['contacts']['email']) ?></a></div><?php endif; ?>
          <?php if (!$assignment['contacts']['phone'] && !$assignment['contacts']['email']): ?><small class="text-muted">Contact not available</small><?php endif; ?>
          <p class="small text-muted mb-0">Assigned: <?= e($detailDate($assignment['assigned_at'])) ?><?php if ($assignment['unassigned_at']): ?> · Assignment ended: <?= e($detailDate($assignment['unassigned_at'])) ?><?php elseif (!$isCurrent): ?> · End time not recorded<?php endif; ?></p>
        </li>
      <?php endforeach; ?>
    </ol>
  <?php endif; ?>
  <div class="scd-subsection">
    <h3>Recorded escalation transfers (<?= count($detailJourney['transfers']) ?>)</h3>
    <?php if (!$detailJourney['transfers']): ?><p class="small text-muted mb-0">No actual escalation transfer recorded.</p><?php else: ?>
      <ol class="scd-timeline">
        <?php foreach (array_reverse($detailJourney['transfers']) as $transfer): ?>
          <li class="scd-event"><span class="scd-event-dot"></span><div class="scd-event-top"><strong>Escalated to Stage <?= (int)$transfer['level_no'] ?></strong><time><?= e($detailDate($transfer['escalated_at'])) ?></time></div>
            <div class="scd-journey-person"><strong><?= e($transfer['officer_name']?:'Officer record unavailable') ?></strong> <span><?= e($detailDash($transfer['designation']??null)) ?></span></div>
            <?php if ($transfer['contacts']['phone']!==''): ?><div class="small"><a href="tel:<?= e($detailContactPhone($transfer['contacts']['phone'])) ?>"><?= e($transfer['contacts']['phone']) ?></a></div><?php endif; ?>
            <?php if ($transfer['contacts']['email']!==''): ?><div class="small"><a href="mailto:<?= e($transfer['contacts']['email']) ?>"><?= e($transfer['contacts']['email']) ?></a></div><?php endif; ?>
            <p class="small text-muted mb-0">Recorded escalation transfer. See action history for recorded reasons.</p>
          </li>
        <?php endforeach; ?>
      </ol>
    <?php endif; ?>
  </div>
  <p class="small text-muted mt-3 mb-0">Historical names and designations use the linked officer records; contact details are current directory contacts, not historical snapshots.</p>
</section>
