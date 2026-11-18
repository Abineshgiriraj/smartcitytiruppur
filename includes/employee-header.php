<?php
declare(strict_types=1);

$user = require_login();
$pageTitle = $pageTitle ?? 'Employee Portal';
$flashes = consume_flashes();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($pageTitle); ?> | <?php echo e(APP_NAME); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo e(url('assets/css/style.css?v=20260806b')); ?>" rel="stylesheet">
</head>
<body class="employee-body">
<nav class="navbar navbar-expand-lg navbar-dark employee-navbar">
    <div class="container-fluid px-lg-4">
        <a class="navbar-brand employee-brand" href="<?php echo e(url('employee/dashboard.php')); ?>">
            <img src="<?php echo e(url('assets/images/trippurlogo.png')); ?>" alt="Tiruppur Corporation">
            <span>Smart City Tiruppur</span>
            <img src="<?php echo e(url('assets/images/smartcitylogo.png')); ?>" alt="Smart City Tiruppur">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#employeeNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="employeeNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?php echo e(url('employee/dashboard.php')); ?>">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo e(url('employee/complaints.php')); ?>">Complaints</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo e(url('employee/reports.php')); ?>">Reports</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo e(url('employee/feedback.php')); ?>">Feedback</a></li>
                <?php if (in_array($user['role'], ['Super Admin', 'Admin'], true)): ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(url('employee/masters.php')); ?>">Masters</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(url('employee/users.php')); ?>">Users</a></li>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center gap-3 text-white">
                <span class="small"><?php echo e($user['full_name']); ?> (<?php echo e($user['role']); ?>)</span>
                <a class="btn btn-sm btn-outline-light" href="<?php echo e(url('employee/logout.php')); ?>">Logout</a>
            </div>
        </div>
    </div>
</nav>

<?php foreach ($flashes as $flash): ?>
<div class="container-fluid px-lg-4 mt-3">
    <div class="alert alert-<?php echo e($flash['type']); ?> alert-dismissible fade show">
        <?php echo e($flash['message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endforeach; ?>

<main class="container-fluid px-lg-4 py-4">
