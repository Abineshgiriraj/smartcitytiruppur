</main>

<?php if (($showQuickServices ?? true) === true): ?>
    <?php include __DIR__ . '/quick-services.php'; ?>
<?php endif; ?>

<footer class="site-footer">
    <div class="container-xxl">
        <nav class="footer-links" aria-label="Footer links">
            <a href="<?php echo e(url('contact.php')); ?>"><span>›</span>Contact Us</a>
            <a href="<?php echo e(url('department-contacts.php')); ?>"><span>›</span>Complaint Officers</a>
            <a href="<?php echo e(url('faq.php')); ?>"><span>›</span>FAQ</a>
            <a href="<?php echo e(url('sitemap.php')); ?>"><span>›</span>Site Map</a>
            <a href="<?php echo e(url('help.php')); ?>"><span>›</span>Help</a>
            <a href="<?php echo e(url('privacy-policy.php')); ?>"><span>›</span>Privacy Policy</a>
        </nav>

        <div class="footer-bottom">
            <p>Copyrights © <?php echo date('Y'); ?> | All Rights Reserved by <a href="<?php echo e(url('index.php')); ?>">Tiruppur City Municipal Corporation</a></p>
            <p>No of Visitors:&nbsp; <strong><?php echo number_format((int)($visitorCount ?? 0)); ?></strong></p>
            <div class="social-icons">
                <a href="https://www.facebook.com/tiruppurcmc" target="_blank" rel="noopener" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                <button type="button" data-bs-toggle="modal" data-bs-target="#whatsappModal" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></button>
                <button type="button" data-bs-toggle="modal" data-bs-target="#phoneModal" aria-label="Telephone"><i class="bi bi-telephone"></i></button>
                <button type="button" data-bs-toggle="modal" data-bs-target="#emailModal" aria-label="Email"><i class="bi bi-envelope"></i></button>
            </div>
        </div>
    </div>
</footer>

<div class="floating-contact-tools" aria-label="Complaint contact methods">
    <button class="float-contact whatsapp" type="button" data-bs-toggle="modal" data-bs-target="#whatsappModal" title="WhatsApp complaint"><i class="bi bi-whatsapp"></i></button>
    <button class="float-contact phone" type="button" data-bs-toggle="modal" data-bs-target="#phoneModal" title="Call helpline"><i class="bi bi-telephone"></i></button>
    <button class="float-contact email" type="button" data-bs-toggle="modal" data-bs-target="#emailModal" title="Email complaint"><i class="bi bi-envelope"></i></button>
</div>

<div class="modal fade public-modal" id="whatsappModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <div class="modal-header"><h2 class="modal-title h5"><i class="bi bi-whatsapp me-2"></i>7305712225</h2><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body"><ol><li>Write your grievance with grievance, area and contact details.</li><li>Attach a photograph when required.</li><li>Send the message to <strong>7305712225</strong>.</li></ol></div>
    </div></div>
</div>
<div class="modal fade public-modal" id="phoneModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <div class="modal-header"><h2 class="modal-title h5"><i class="bi bi-telephone me-2"></i>155304</h2><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body"><p class="mb-0">Call <strong>155304</strong> for suggestions and complaints.</p></div>
    </div></div>
</div>
<div class="modal fade public-modal" id="emailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <div class="modal-header"><h2 class="modal-title h5"><i class="bi bi-envelope me-2"></i>Email Complaint</h2><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body"><ol><li>Write the grievance and area details.</li><li>Attach a photograph when required.</li><li>Email <strong><?php echo e(setting('support_email', 'commr.tiruppur@tn.gov.in')); ?></strong>.</li></ol></div>
    </div></div>
</div>

<script>
window.APP_BASE_URL = <?php echo json_encode(rtrim(BASE_URL, '/')); ?>;
window.APP_ENV = <?php echo json_encode(APP_ENV); ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo e(url('assets/js/app.js?v=20261121-server-categories')); ?>"></script>
<script src="<?php echo e(url('assets/js/civic-effects.js?v=20261124')); ?>" defer></script>
<script src="<?php echo e(url('assets/js/neo-experience.js?v=20261124')); ?>" defer></script>

</body>
</html>
