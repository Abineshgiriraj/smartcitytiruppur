</main>
<script>
window.APP_BASE_URL = <?php echo json_encode(rtrim(BASE_URL, '/')); ?>;
window.APP_ENV = <?php echo json_encode(APP_ENV); ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo e(url('assets/js/app.js')); ?>"></script>
</body>
</html>
