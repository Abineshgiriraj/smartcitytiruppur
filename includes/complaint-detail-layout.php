<?php
declare(strict_types=1);
/**
 * Shared, strictly read-only complaint details layout.
 * $detailAudience: 'public' or 'staff'. Controllers own authorization and writes.
 * Never expose internal status-history rows/remarks to the public template.
 */
$detailAudience = $detailAudience ?? 'public';
$detailIsStaff = $detailAudience === 'staff';
$detailStatus = (string)($complaint['status'] ?? 'Registered');
$detailStatusClass = match ($detailStatus) {
    'Resolved','Closed' => 'scd-status-success',
    'Rejected' => 'scd-status-danger',
    'Assigned','Acknowledged','In Progress' => 'scd-status-progress',
    'Reopened' => 'scd-status-warning',
    default => 'scd-status-neutral',
};
$detailDate = static function ($value, string $format='d M Y, h:i A'): string {
    if (!$value) return '—';
    $time = strtotime((string)$value);
    return $time === false ? '—' : date($format, $time);
};
$detailDash = static function ($value): string {
    return $value === null || trim((string)$value) === '' ? '—' : (string)$value;
};
$detailOwnerVerified = $detailIsStaff
    || ((string)($_SESSION['otp_verified_mobile']['tracking'] ?? '') !== ''
        && hash_equals((string)($complaint['mobile_no'] ?? ''),(string)$_SESSION['otp_verified_mobile']['tracking']));
$detailPhone = $detailOwnerVerified ? $detailDash($complaint['mobile_no']??null)
    : mask_mobile((string)($complaint['mobile_no']??''));
$detailEmail = $detailIsStaff || $detailOwnerVerified
    ? $detailDash($complaint['email']??null) : 'Protected · verify your mobile to view';
$detailAddress = $detailIsStaff || $detailOwnerVerified
    ? $detailDash($complaint['communication_address']??null) : 'Protected · verify your mobile to view';
$detailOfficerPhone = $detailIsStaff
    ? $detailDash($complaint['officer_mobile']??null)
    : (empty($complaint['officer_mobile'])?'—':mask_mobile((string)$complaint['officer_mobile']));
$detailAttachmentUrl = $detailIsStaff?'employee/download-attachment.php':'download-attachment.php';
$detailBackUrl = $detailIsStaff
    ? url($isOfficerPortal ? 'employee/my-complaints.php':'employee/complaints.php')
    : url('complaint-status.php');
$detailFileSize = static function ($bytes): string {
    $num=max(0,(float)$bytes);
    if ($num>=1048576) return number_format($num/1048576,1).' MB';
    return number_format($num/1024,1).' KB';
};
?>
<article class="scd scd-<?php echo $detailIsStaff?'staff':'public'; ?>" aria-labelledby="scd-title">
  <div class="scd-toolbar no-print">
    <a class="scd-back" href="<?php echo e($detailBackUrl); ?>"><i class="bi bi-arrow-left" aria-hidden="true"></i><span><?php echo $detailIsStaff?'Back to complaints':'Back to tracking'; ?></span></a>
    <div class="scd-toolbar-buttons">
      <span class="scd-view-label"><i class="bi bi-shield-check" aria-hidden="true"></i><?php echo $detailIsStaff?'Authorized staff view':'Public complaint view'; ?></span>
      <button type="button" class="scd-print" onclick="window.print()"><i class="bi bi-printer" aria-hidden="true"></i> Print / Save PDF</button>
    </div>
  </div>

  <header class="scd-hero">
    <div class="scd-hero-watermark" aria-hidden="true"><i class="bi bi-buildings"></i></div>
    <div class="scd-hero-inner">
      <div class="scd-brand">
        <img src="<?php echo e(url('assets/images/trippurlogo.png')); ?>" alt="Tiruppur City Municipal Corporation">
        <span><b>TIRUPPUR CITY</b><small>MUNICIPAL CORPORATION</small></span>
        <img src="<?php echo e(url('assets/images/tamilnadulogo.png')); ?>" alt="Government of Tamil Nadu">
      </div>
      <div class="scd-hero-body">
        <div class="scd-hero-heading">
          <div>
            <div class="scd-overline"><span class="scd-pulse-dot" aria-hidden="true"></span> OFFICIAL GRIEVANCE RECORD</div>
            <h1 id="scd-title"><?php echo e($detailDash($complaint['reference_no']??null)); ?></h1>
            <p><?php echo e($detailDash($complaint['department_name']??null)); ?> <span aria-hidden="true">·</span> <?php echo e($detailDash($complaint['category_name']??null)); ?></p>
          </div>
          <span class="scd-status <?php echo $detailStatusClass; ?>"><span aria-hidden="true" class="scd-status-dot"></span><?php echo e($detailStatus); ?></span>
        </div>
        <div class="scd-hero-meta">
          <span><i class="bi bi-calendar3" aria-hidden="true"></i><?php echo e($detailDate($complaint['created_at']??null)); ?></span>
          <span><i class="bi bi-geo-alt" aria-hidden="true"></i>Zone <?php echo e($detailDash($complaint['zone_no']??null)); ?> / Ward <?php echo e($detailDash($complaint['ward_no']??null)); ?></span>
          <span><i class="bi bi-globe2" aria-hidden="true"></i><?php echo e($detailDash($complaint['source']??null)); ?></span>
        </div>
      </div>
    </div>
  </header>

  <div class="scd-overview" aria-label="Complaint overview">
    <div class="scd-summary"><span class="scd-summary-icon"><i class="bi bi-clipboard-check"></i></span><div><small>Complaint status</small><strong><?php echo e($detailStatus); ?></strong></div></div>
    <div class="scd-summary"><span class="scd-summary-icon"><i class="bi bi-person-badge"></i></span><div><small>Assigned officer</small><strong><?php echo e($complaint['officer_name']?:'Not assigned'); ?></strong></div></div>
    <div class="scd-summary"><span class="scd-summary-icon"><i class="bi bi-building"></i></span><div><small>Department</small><strong><?php echo e($detailDash($complaint['department_name']??null)); ?></strong></div></div>
    <div class="scd-summary"><span class="scd-summary-icon"><i class="bi bi-pin-map"></i></span><div><small>Service area</small><strong>Zone <?php echo e($detailDash($complaint['zone_no']??null)); ?> · Ward <?php echo e($detailDash($complaint['ward_no']??null)); ?></strong></div></div>
  </div>

  <div class="scd-grid">
    <div class="scd-primary">
      <section class="scd-panel" aria-labelledby="scd-description-title">
        <div class="scd-section-title"><span class="scd-section-icon"><i class="bi bi-chat-square-text"></i></span><div><small>COMPLAINT OVERVIEW</small><h2 id="scd-description-title">Complaint Description</h2></div></div>
        <div class="scd-description"><?php echo nl2br(e($detailDash($complaint['description_english']??null))); ?></div>
        <div class="scd-subsection">
          <h3>தமிழ் விளக்கம் · Description in Tamil</h3>
          <div class="scd-description scd-description-secondary" lang="ta"><?php echo nl2br(e($detailDash($complaint['description_tamil']??null))); ?></div>
        </div>
      </section>

      <section class="scd-panel" aria-labelledby="scd-citizen-title">
        <div class="scd-section-title"><span class="scd-section-icon"><i class="bi bi-person-vcard"></i></span><div><small>CITIZEN DETAILS</small><h2 id="scd-citizen-title">Citizen Information</h2></div></div>
        <dl class="scd-details">
          <div><dt>Full name</dt><dd><?php echo e($detailDash($complaint['citizen_name']??null)); ?></dd></div>
          <div><dt>Mobile number</dt><dd><?php echo e($detailPhone); ?></dd></div>
          <div><dt>Email address</dt><dd><?php echo e($detailEmail); ?></dd></div>
          <div><dt>Submission source</dt><dd><?php echo e($detailDash($complaint['source']??null)); ?></dd></div>
          <div class="scd-detail-wide"><dt>Communication address</dt><dd><?php echo nl2br(e($detailAddress)); ?></dd></div>
        </dl>
        <?php if (!$detailOwnerVerified && !$detailIsStaff): ?>
          <div class="scd-privacy"><i class="bi bi-lock"></i>Personal contact details are protected. Verify your registered mobile number through complaint tracking to view them.</div>
        <?php endif; ?>
      </section>

      <section class="scd-panel" aria-labelledby="scd-attachments-title">
        <div class="scd-section-title"><span class="scd-section-icon"><i class="bi bi-paperclip"></i></span><div><small>SUPPORTING EVIDENCE</small><h2 id="scd-attachments-title">Attachments</h2></div>
          <span class="scd-count"><?php echo count($attachments); ?> file<?php echo count($attachments)===1?'':'s'; ?></span>
        </div>
        <?php if (!$attachments): ?>
          <div class="scd-empty"><i class="bi bi-folder2-open"></i><strong>No attachments uploaded</strong><span>Supporting images or documents will appear here when available.</span></div>
        <?php else: ?>
          <div class="scd-files">
            <?php foreach ($attachments as $attachment):
              $isImage=str_starts_with((string)($attachment['mime_type']??''),'image/');
              $isPdf=(string)($attachment['mime_type']??'')==='application/pdf';
              $fileIcon=$isImage?'bi-file-earmark-image':($isPdf?'bi-file-earmark-pdf':'bi-file-earmark-text');
            ?>
              <a class="scd-file" href="<?php echo e(url($detailAttachmentUrl.'?id='.(int)$attachment['id'])); ?>">
                <span class="scd-file-icon"><i class="bi <?php echo $fileIcon; ?>" aria-hidden="true"></i></span>
                <span class="scd-file-text"><strong><?php echo e($detailDash($attachment['original_name']??null)); ?></strong>
                  <small><?php echo e(strtoupper((string)($attachment['mime_type']??'File'))); ?> · <?php echo e($detailFileSize($attachment['file_size']??0)); ?></small></span>
                <i class="bi bi-download scd-file-download" aria-hidden="true"></i>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>

      <?php if ($detailIsStaff): ?>
      <section class="scd-panel scd-timeline-panel" aria-labelledby="scd-timeline-title">
        <div class="scd-section-title"><span class="scd-section-icon"><i class="bi bi-clock-history"></i></span><div><small>AUTHORIZED STAFF ONLY</small><h2 id="scd-timeline-title">Activity Timeline</h2></div>
          <span class="scd-count"><?php echo count($history); ?> events</span>
        </div>
        <?php if (!$history): ?>
          <div class="scd-empty"><i class="bi bi-clock"></i><strong>No recorded activity</strong><span>Status updates will appear here as they are recorded.</span></div>
        <?php else: ?>
          <ol class="scd-timeline">
            <?php foreach (array_reverse($history) as $item): ?>
              <li class="scd-event">
                <span class="scd-event-dot" aria-hidden="true"></span>
                <div class="scd-event-top"><strong><?php echo e($detailDash($item['new_status']??null)); ?></strong><time datetime="<?php echo e($item['changed_at']??''); ?>"><?php echo e($detailDate($item['changed_at']??null)); ?></time></div>
                <span class="scd-event-actor"><i class="bi bi-person-circle" aria-hidden="true"></i><?php echo e($item['full_name']?:'System'); ?></span>
                <?php if (!empty($item['remarks'])): ?><p class="scd-event-remarks"><?php echo nl2br(e($item['remarks'])); ?></p><?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ol>
        <?php endif; ?>
      </section>
      <?php endif; ?>

      <?php if (!$detailIsStaff && ($canReopen || in_array($detailStatus,['Resolved','Closed'],true))): ?>
      <section class="scd-panel" aria-labelledby="scd-feedback-title">
        <div class="scd-section-title"><span class="scd-section-icon"><i class="bi bi-star"></i></span><div><small>YOUR EXPERIENCE</small><h2 id="scd-feedback-title">Citizen Feedback</h2></div></div>
        <?php if ($complaintFeedback): ?>
          <div class="scd-feedback-existing"><div class="scd-stars" aria-label="<?php echo (int)$complaintFeedback['rating']; ?> out of 5 stars">
            <?php for($i=1;$i<=5;$i++): ?><i class="bi bi-star<?php echo $i<=(int)$complaintFeedback['rating']?'-fill':''; ?>"></i><?php endfor; ?>
          </div><p><?php echo nl2br(e($complaintFeedback['feedback_text'])); ?></p></div>
        <?php else: ?>
          <form method="post" class="scd-form">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="reference_no" value="<?php echo e($complaint['reference_no']); ?>">
            <input type="hidden" name="feedback_complaint_id" value="<?php echo (int)$complaint['id']; ?>">
            <fieldset class="scd-rating"><legend>How satisfied are you?</legend>
              <?php for($rating=1;$rating<=5;$rating++): ?><label><input type="radio" name="citizen_rating" value="<?php echo $rating; ?>" required><span><?php echo $rating; ?> <i class="bi bi-star-fill" aria-hidden="true"></i></span></label><?php endfor; ?>
            </fieldset>
            <label class="form-label fw-semibold" for="scd-feedback">Your feedback</label>
            <textarea class="form-control" id="scd-feedback" name="citizen_feedback" rows="3" maxlength="2000" placeholder="Tell us about your experience" required></textarea>
            <button class="btn btn-primary mt-3" type="submit"><i class="bi bi-send me-1"></i>Submit feedback</button>
          </form>
        <?php endif; ?>
        <?php if($canReopen): ?>
          <div class="scd-subsection scd-reopen">
            <h3>Need to reopen this complaint?</h3>
            <p>Not satisfied with the resolution? You can request a reopening within the permitted time.</p>
            <form method="post" class="scd-form">
              <?php echo csrf_field(); ?>
              <input type="hidden" name="reference_no" value="<?php echo e($complaint['reference_no']); ?>">
              <input type="hidden" name="reopen_complaint_id" value="<?php echo (int)$complaint['id']; ?>">
              <label for="scd-reopen-reason" class="form-label fw-semibold">Reason for reopening</label>
              <textarea name="reopen_reason" id="scd-reopen-reason" class="form-control" rows="2" maxlength="1000" required></textarea>
              <button type="submit" class="btn btn-outline-primary mt-3"><i class="bi bi-arrow-counterclockwise me-1"></i>Reopen complaint</button>
            </form>
          </div>
        <?php endif; ?>
      </section>
      <?php endif; ?>
    </div>

    <aside class="scd-aside" aria-label="Complaint information">
      <section class="scd-panel scd-officer-card" aria-labelledby="scd-officer-title">
        <div class="scd-section-title"><span class="scd-section-icon"><i class="bi bi-person-check"></i></span><div><small>RESPONSIBLE CONTACT</small><h2 id="scd-officer-title">Assigned Officer</h2></div></div>
        <div class="scd-officer-profile"><span class="scd-officer-avatar"><i class="bi bi-person"></i></span>
          <div><strong><?php echo e($complaint['officer_name']?:'Awaiting assignment'); ?></strong><span><?php echo e($detailDash($complaint['officer_designation']??null)); ?></span></div></div>
        <dl class="scd-side-details">
          <div><dt>Department</dt><dd><?php echo e($detailDash($complaint['department_name']??null)); ?></dd></div>
          <div><dt>Phone number</dt><dd><?php echo e($detailOfficerPhone); ?></dd></div>
          <div><dt>Email</dt><dd><?php echo e($detailDash($complaint['officer_email']??null)); ?></dd></div>
        </dl>
      </section>

      <section class="scd-panel" aria-labelledby="scd-information-title">
        <div class="scd-section-title"><span class="scd-section-icon"><i class="bi bi-file-earmark-text"></i></span><div><small>GRIEVANCE RECORD</small><h2 id="scd-information-title">Complaint Information</h2></div></div>
        <dl class="scd-side-details">
          <div><dt>Reference ID</dt><dd class="scd-id"><?php echo e($detailDash($complaint['reference_no']??null)); ?></dd></div>
          <div><dt>Assessment number</dt><dd><?php echo e($detailDash($complaint['assessment_no']??null)); ?></dd></div>
          <div><dt>Category</dt><dd><?php echo e($detailDash($complaint['category_name']??null)); ?></dd></div>
          <div><dt>Registered on</dt><dd><?php echo e($detailDate($complaint['created_at']??null)); ?></dd></div>
          <div><dt>Closed on</dt><dd><?php echo e($detailDate($complaint['closed_at']??null)); ?></dd></div>
          <div><dt>Zone / Ward</dt><dd>Zone <?php echo e($detailDash($complaint['zone_no']??null)); ?> · Ward <?php echo e($detailDash($complaint['ward_no']??null)); ?></dd></div>
          <div><dt>Street / locality</dt><dd><?php echo e($detailDash($complaint['street_name']??null)); ?></dd></div>
          <div><dt>Location</dt><dd><?php echo e($detailDash($complaint['location_address']??null)); ?></dd></div>
          <div><dt>Door number</dt><dd><?php echo e($detailDash($complaint['door_no']??null)); ?></dd></div>
          <div><dt>Latest remarks</dt><dd><?php echo $detailIsStaff
            ? nl2br(e($history?($history[count($history)-1]['remarks']?:'—'):'—'))
            : 'Internal staff notes are not shown publicly.'; ?></dd></div>
        </dl>
      </section>
      <div class="scd-support no-print"><i class="bi bi-info-circle"></i><span>For help with this grievance, contact <a href="<?php echo e(url('contact.php')); ?>">municipal support</a>.</span></div>
    </aside>
  </div>
  <p class="scd-footer-note"><i class="bi bi-shield-lock" aria-hidden="true"></i> Tiruppur City Municipal Corporation · Official grievance information</p>
</article>
