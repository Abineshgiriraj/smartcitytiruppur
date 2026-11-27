<?php
declare(strict_types=1);
/**
 * Shared, strictly read-only complaint details layout.
 * $detailAudience: 'public' or 'staff'. Controllers own authorization and writes.
 * Public controllers supply only citizen-visible remarks.
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
$detailArea = $detailDash($complaint['zone_no']??null).' · '.$detailDash($complaint['ward_no']??null);
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
          <span><i class="bi bi-geo-alt" aria-hidden="true"></i><?php echo e($detailArea); ?></span>
          <span><i class="bi bi-globe2" aria-hidden="true"></i><?php echo e($detailDash($complaint['source']??null)); ?></span>
        </div>
      </div>
    </div>
  </header>

  <div class="scd-overview" aria-label="Complaint overview">
    <div class="scd-summary"><span class="scd-summary-icon"><i class="bi bi-clipboard-check"></i></span><div><small>Complaint status</small><strong><?php echo e($detailStatus); ?></strong></div></div>
    <div class="scd-summary"><span class="scd-summary-icon"><i class="bi bi-person-badge"></i></span><div><small>Assigned officer</small><strong><?php echo e($complaint['officer_name']?:'Not assigned'); ?></strong></div></div>
    <div class="scd-summary"><span class="scd-summary-icon"><i class="bi bi-building"></i></span><div><small>Department</small><strong><?php echo e($detailDash($complaint['department_name']??null)); ?></strong></div></div>
    <div class="scd-summary"><span class="scd-summary-icon"><i class="bi bi-pin-map"></i></span><div><small>Service area</small><strong><?php echo e($detailArea); ?></strong></div></div>
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
              $downloadUrl=url($detailAttachmentUrl.'?id='.(int)$attachment['id']);
              $inlineUrl=url($detailAttachmentUrl.'?id='.(int)$attachment['id'].'&view=inline');
              $fileName=$detailDash($attachment['original_name']??null);
            ?>
              <div class="scd-file <?php echo $isImage ? 'scd-file-with-image' : ''; ?>">
                <?php if ($isImage): ?>
                  <div class="scd-file-preview-wrap">
                    <img src="<?php echo e($inlineUrl); ?>" alt="<?php echo e($fileName); ?>" class="scd-file-thumb" loading="lazy" onclick="openComplaintImageModal('<?php echo e($inlineUrl); ?>', '<?php echo e(addslashes($fileName)); ?>', '<?php echo e($downloadUrl); ?>')">
                    <button type="button" class="scd-file-zoom-btn" title="View large size" onclick="openComplaintImageModal('<?php echo e($inlineUrl); ?>', '<?php echo e(addslashes($fileName)); ?>', '<?php echo e($downloadUrl); ?>')">
                      <i class="bi bi-arrows-fullscreen"></i>
                    </button>
                  </div>
                <?php else: ?>
                  <span class="scd-file-icon"><i class="bi <?php echo $fileIcon; ?>" aria-hidden="true"></i></span>
                <?php endif; ?>
                <div class="scd-file-text">
                  <?php if ($isImage): ?>
                    <strong class="scd-file-clickable" onclick="openComplaintImageModal('<?php echo e($inlineUrl); ?>', '<?php echo e(addslashes($fileName)); ?>', '<?php echo e($downloadUrl); ?>')"><?php echo e($fileName); ?></strong>
                  <?php else: ?>
                    <a href="<?php echo e($downloadUrl); ?>" class="text-decoration-none text-dark"><strong><?php echo e($fileName); ?></strong></a>
                  <?php endif; ?>
                  <small><?php echo e(strtoupper((string)($attachment['mime_type']??'File'))); ?> · <?php echo e($detailFileSize($attachment['file_size']??0)); ?></small>
                </div>
                <a href="<?php echo e($downloadUrl); ?>" class="scd-file-download" title="Download attachment" download>
                  <i class="bi bi-download" aria-hidden="true"></i>
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>

      <!-- Lightbox / Modal for Large Image View -->
      <div id="complaintImageModal" class="scd-image-modal" style="display:none;" onclick="closeComplaintImageModal(event)">
        <div class="scd-image-modal-content">
          <div class="scd-image-modal-header">
            <span id="complaintImageModalTitle" class="scd-image-modal-title"></span>
            <div class="scd-image-modal-actions">
              <a id="complaintImageModalDownload" href="#" class="btn btn-sm btn-outline-light me-2" download>
                <i class="bi bi-download me-1"></i> Download
              </a>
              <button type="button" class="btn btn-sm btn-light" onclick="closeComplaintImageModalDirect()">
                <i class="bi bi-x-lg"></i>
              </button>
            </div>
          </div>
          <div class="scd-image-modal-body">
            <img id="complaintImageModalImg" src="" alt="" style="display:none;">
          </div>
        </div>
      </div>

      <script>
      function openComplaintImageModal(url, title, downloadUrl) {
        var modal = document.getElementById('complaintImageModal');
        var img = document.getElementById('complaintImageModalImg');
        img.src = url;
        img.style.display = 'block';
        document.getElementById('complaintImageModalTitle').textContent = title;
        document.getElementById('complaintImageModalDownload').href = downloadUrl;
        modal.style.display = 'flex';
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
      }
      function closeComplaintImageModal(e) {
        if (e.target.id === 'complaintImageModal' || e.target.closest('.btn-light')) {
          closeComplaintImageModalDirect();
        }
      }
      function closeComplaintImageModalDirect() {
        var modal = document.getElementById('complaintImageModal');
        var img = document.getElementById('complaintImageModalImg');
        if (modal) {
          modal.classList.remove('active');
          modal.style.display = 'none';
          if (img) {
            img.src = '';
            img.style.display = 'none';
          }
          document.body.style.overflow = '';
        }
      }
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeComplaintImageModalDirect();
      });
      </script>

      <section class="scd-panel scd-timeline-panel" aria-labelledby="scd-timeline-title">
        <div class="scd-section-title"><span class="scd-section-icon"><i class="bi bi-clock-history"></i></span><div><small><?= $detailIsStaff ? 'AUTHORIZED STAFF ONLY' : 'COMPLAINT UPDATES' ?></small><h2 id="scd-timeline-title"><?= $detailIsStaff ? 'Activity Timeline' : 'Officer Remarks' ?></h2></div>
          <span class="scd-count"><?php echo count($history); ?> <?= $detailIsStaff ? 'events' : 'remarks' ?></span>
        </div>
        <?php if (!$history): ?>
          <div class="scd-empty"><i class="bi bi-clock"></i><strong><?= $detailIsStaff ? 'No recorded activity' : 'No citizen-facing remarks available yet' ?></strong><span><?= $detailIsStaff ? 'Status updates will appear here as they are recorded.' : 'Published officer updates will appear here.' ?></span></div>
        <?php else: ?>
          <ol class="scd-timeline">
            <?php foreach (array_reverse($history) as $item): ?>
              <li class="scd-event">
                <span class="scd-event-dot" aria-hidden="true"></span>
                <div class="scd-event-top"><strong><?php echo e($detailDash($item['new_status']??null)); ?></strong><time datetime="<?php echo e($item['changed_at']??''); ?>"><?php echo e($detailDate($item['changed_at']??null)); ?></time></div>
                <span class="scd-event-actor"><i class="bi bi-person-circle" aria-hidden="true"></i><?= e($detailIsStaff ? (($item['full_name']??'')?:'System') : 'Municipal staff update') ?></span>
                <?php if (trim((string)($item['remarks']??''))!==''): ?><p class="scd-event-remarks"><?= nl2br(e($item['remarks'])) ?></p><?php endif; ?>
                 <?php if ($detailIsStaff && !empty($item['changed_by']) && trim((string)($item['remarks']??''))!==''): ?>
                   <?php $remarkPublished = ($item['remark_visibility']??'')==='citizen_remark_published'; ?>
                   <small class="text-muted"><?= $remarkPublished ? 'Published to citizens' : (($item['remark_visibility']??'')==='citizen_remark_private' ? 'Internal only' : 'Legacy / not explicitly reviewed') ?></small>
                   <?php if (!empty($canReviewRemarks)): ?>
                     <form method="post" class="mt-2 no-print" onsubmit="return confirm('Change remark visibility? Publish only citizen-facing text without confidential details.');">
                       <?= csrf_field() ?>
                       <input type="hidden" name="id" value="<?= (int)$complaint['id'] ?>">
                       <input type="hidden" name="action" value="review_remark_visibility">
                       <input type="hidden" name="history_id" value="<?= (int)$item['id'] ?>">
                       <button class="btn btn-sm btn-outline-secondary" name="remark_visibility" value="<?= $remarkPublished ? 'private' : 'publish' ?>" type="submit"><?= $remarkPublished ? 'Make internal' : 'Publish reviewed remark' ?></button>
                       <?php if (!$remarkPublished && empty($item['remark_visibility'])): ?><button class="btn btn-sm btn-outline-secondary" type="submit" name="remark_visibility" value="private">Keep internal</button><?php endif; ?>
                     </form>
                   <?php endif; ?>
                 <?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ol>
        <?php endif; ?>
        <?php if (!$detailIsStaff && !$detailOwnerVerified): ?>
          <p class="small text-muted mt-3 mb-0">For older verified officer updates, <a href="<?= e(url('complaint-status.php')) ?>">verify your registered mobile number through complaint tracking</a>.</p>
        <?php endif; ?>
      </section>

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
          <div><dt>Zone / Ward</dt><dd><?php echo e($detailArea); ?></dd></div>
          <div><dt>Street / locality</dt><dd><?php echo e($detailDash($complaint['street_name']??null)); ?></dd></div>
          <div><dt>Location</dt><dd><?php echo e($detailDash($complaint['location_address']??null)); ?></dd></div>
          <div><dt>Door number</dt><dd><?php echo e($detailDash($complaint['door_no']??null)); ?></dd></div>
          <div><dt>Latest remarks</dt><dd><?php
            $latestRemark = '';
            foreach (array_reverse($history ?? []) as $remarkRow) {
              if (trim((string)($remarkRow['remarks']??'')) !== '') {
                $latestRemark = (string)$remarkRow['remarks'];
                break;
              }
            }
            echo $latestRemark !== '' ? nl2br(e($latestRemark)) : ($detailIsStaff ? '—' : 'No citizen-facing remarks available yet.');
           ?></dd></div>
        </dl>
      </section>
      <div class="scd-support no-print"><i class="bi bi-info-circle"></i><span>For help with this grievance, contact <a href="<?php echo e(url('contact.php')); ?>">municipal support</a>.</span></div>
    </aside>
  </div>
  <p class="scd-footer-note"><i class="bi bi-shield-lock" aria-hidden="true"></i> Tiruppur City Municipal Corporation · Official grievance information</p>
</article>
