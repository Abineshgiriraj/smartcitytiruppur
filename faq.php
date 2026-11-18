<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'FAQ';
include __DIR__ . '/includes/header.php';
$faqs = [
    ['How do I register a complaint?', 'Open New Complaint, verify your mobile number, select the locality, department and category, then submit the form.'],
    ['How do I track a complaint?', 'Use the complaint reference number. To view every complaint registered to a mobile number, verify that mobile number with OTP.'],
    ['How will I know the resolution period?', 'The Escalation Period page shows the SLA days configured for each complaint category.'],
    ['Can I reopen a complaint?', 'A resolved or closed complaint may be reopened once within seven days, after mobile OTP verification.'],
    ['What attachments are allowed?', 'PDF, PNG, JPG and JPEG files up to 2 MB each. A maximum of four attachments is allowed.'],
];
?>
<section class="page-banner"><div class="container-xxl"><h1 class="fw-bold">Frequently Asked Questions</h1></div></section>
<div class="container-xxl py-5">
    <div class="accordion" id="faqAccordion">
        <?php foreach ($faqs as $index => [$question, $answer]): ?>
            <div class="accordion-item">
                <h2 class="accordion-header"><button class="accordion-button <?php echo $index ? 'collapsed' : ''; ?>" data-bs-toggle="collapse" data-bs-target="#faq<?php echo $index; ?>"><?php echo e($question); ?></button></h2>
                <div id="faq<?php echo $index; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" data-bs-parent="#faqAccordion"><div class="accordion-body"><?php echo e($answer); ?></div></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
