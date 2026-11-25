    </main>
    <footer class="admin-footer">
      <span>© <?php echo date('Y'); ?> Tiruppur City Corporation</span>
      <span>Smart City Tiruppur · Secure administration</span>
    </footer>
  </div>
</div>
<script>
window.APP_BASE_URL = <?php echo json_encode(rtrim(BASE_URL, '/')); ?>;
window.APP_ENV = <?php echo json_encode(APP_ENV); ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo e(url('assets/js/app.js')); ?>"></script>
<script src="<?php echo e(url('assets/js/admin.js?v=20261119a')); ?>"></script>
<script src="<?php echo e(url('assets/js/civic-effects.js?v=20261124')); ?>" defer></script>
</body>
</html>
