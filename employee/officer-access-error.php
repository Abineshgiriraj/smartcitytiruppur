<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
$user = current_user();
$pageTitle = 'Officer Access Notice';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo e($pageTitle); ?> | <?php echo e(APP_NAME); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?php echo e(url('assets/css/style.css')); ?>" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center min-vh-100 py-5">
<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
      <div class="card border-0 shadow-sm p-4 p-md-5 text-center">
        <div class="mb-4">
          <img src="<?php echo e(url('assets/images/trippurlogo.png')); ?>" alt="Tiruppur Corporation" style="height: 70px;">
        </div>
        <div class="text-warning mb-3">
          <i class="bi bi-shield-exclamation display-3"></i>
        </div>
        <h2 class="h4 fw-bold text-dark mb-2">Officer Profile Not Linked</h2>
        <p class="text-muted mb-4">
          <?php echo e($errorMessage ?? 'Your employee user account is not linked to any active Municipal Officer profile in the corporation database.'); ?>
        </p>

        <?php if ($user): ?>
          <div class="card bg-light border p-3 text-start mb-4">
            <div class="row g-2 small">
              <div class="col-sm-4 text-muted">User Account:</div>
              <div class="col-sm-8 fw-semibold"><?php echo e($user['full_name']); ?></div>
              <div class="col-sm-4 text-muted">Email:</div>
              <div class="col-sm-8"><?php echo e($user['email']); ?></div>
              <div class="col-sm-4 text-muted">Role:</div>
              <div class="col-sm-8"><span class="badge bg-secondary"><?php echo e($user['role']); ?></span></div>
              <div class="col-sm-4 text-muted">User ID:</div>
              <div class="col-sm-8">#<?php echo (int)$user['id']; ?></div>
            </div>
          </div>
        <?php endif; ?>

        <div class="alert alert-info py-2 small mb-4">
          <i class="bi bi-info-circle me-1"></i> For security, grievance management requires an official link between <code>users.id</code> and <code>officers.user_id</code>. Please contact your Department Head or Corporation Administrator.
        </div>

        <div class="d-flex justify-content-center gap-2">
          <a href="<?php echo e(url('employee/logout.php')); ?>" class="btn btn-outline-danger">
            <i class="bi bi-box-arrow-right me-1"></i> Sign Out
          </a>
          <a href="<?php echo e(url('index.php')); ?>" class="btn btn-primary">
            <i class="bi bi-house me-1"></i> Return to Home
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
