<?php
declare(strict_types=1);

$pageTitle = $pageTitle ?? APP_NAME;
$activePage = $activePage ?? '';
$flashes = consume_flashes();
$language = current_language();
$visitorCount = 0;

try {
    if (empty($_SESSION['public_visit_recorded'])) {
        $pagePath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        db()->prepare(
            "INSERT INTO visitor_stats (visit_date, page_path, visit_count)
             VALUES (CURDATE(), ?, 1)
             ON DUPLICATE KEY UPDATE visit_count = visit_count + 1"
        )->execute([$pagePath]);
        $_SESSION['public_visit_recorded'] = true;
    }

    $visitorCount = (int) db()->query(
        'SELECT COALESCE(SUM(visit_count), 0) FROM visitor_stats'
    )->fetchColumn();
} catch (Throwable $exception) {
    log_message('Visitor counter failed: ' . $exception->getMessage());
}
?>
<!doctype html>
<html lang="<?php echo e($language); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($pageTitle); ?> | <?php echo e(APP_NAME); ?></title>
    <meta name="description" content="Tiruppur City Municipal Corporation public grievance redressal portal">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?php echo e(url('assets/css/style.css?v=20260806')); ?>" rel="stylesheet">
    <link href="<?php echo e(url('assets/css/citizen-refresh.css?v=20261121b')); ?>" rel="stylesheet">
    <link href="<?php echo e(url('assets/css/civic-modern.css?v=20261124')); ?>" rel="stylesheet">
    <link href="<?php echo e(url('assets/css/neo-civic-experience.css?v=20261124')); ?>" rel="stylesheet">
</head>
<body class="public-body">
<header class="public-header">
    <div class="utility-strip">
        <div class="container-xxl d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="utility-left d-flex flex-wrap align-items-center gap-4">
                <a href="tel:155304">
                    <i class="bi bi-telephone"></i>
                    <span class="support-label">Customer Support:</span>
                    <strong>155304</strong>
                </a>
                <a href="mailto:<?php echo e(setting('support_email', 'commr.Tiruppur@tn.gov.in')); ?>">
                    <i class="bi bi-envelope"></i>
                    <?php echo e(setting('support_email', 'commr.Tiruppur@tn.gov.in')); ?>
                </a>
            </div>
            <div class="utility-right d-flex align-items-center gap-3">
                <a href="?lang=en" class="<?php echo $language === 'en' ? 'active' : ''; ?>">English</a>
                <a href="?lang=ta" class="<?php echo $language === 'ta' ? 'active' : ''; ?>">தமிழ்</a>
                <a href="<?php echo e(url('employee/login.php')); ?>"><i class="bi bi-person-lock me-1"></i><?php echo e(t('login')); ?></a>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark main-navbar">
        <div class="container-xxl">
            <a class="navbar-brand brand-wrap" href="<?php echo e(url('index.php')); ?>">
                <img src="<?php echo e(url('assets/images/trippurlogo.png')); ?>" alt="Tiruppur City Municipal Corporation" class="brand-emblem">
                <span class="brand-copy">
                    <strong>TIRUPPUR CITY</strong>
                    <strong>MUNICIPAL CORPORATION</strong>
                </span>
                <img src="<?php echo e(url('assets/images/tamilnadulogo.png')); ?>" alt="Government of Tamil Nadu" class="brand-state-emblem">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activePage === 'home' ? 'active' : ''; ?>" href="<?php echo e(url('index.php')); ?>"><?php echo e(t('home')); ?></a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo $activePage === 'complaints' ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown"><?php echo e(t('complaints')); ?></a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo e(url('new-complaint.php')); ?>"><span>›</span><?php echo e(t('new_complaint')); ?></a></li>
                            <li><a class="dropdown-item" href="<?php echo e(url('complaint-status.php')); ?>"><span>›</span><?php echo e(t('complaint_status')); ?></a></li>
                            <li><a class="dropdown-item" href="<?php echo e(url('escalation-period.php')); ?>"><span>›</span><?php echo e(t('escalation_period')); ?></a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo $activePage === 'contacts' ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown"><?php echo e(t('contacts')); ?></a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo e(url('department-contacts.php')); ?>"><span>›</span><?php echo e(t('department_contacts')); ?></a></li>
                            <li><a class="dropdown-item" href="<?php echo e(url('grievance-contacts.php')); ?>"><span>›</span><?php echo e(t('grievance_contacts')); ?></a></li>
                            <li><a class="dropdown-item" href="<?php echo e(url('site-manager.php')); ?>"><span>›</span><?php echo e(t('site_manager')); ?></a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activePage === 'dashboard' ? 'active' : ''; ?>" href="<?php echo e(url('dashboard.php')); ?>"><?php echo e(t('dashboard')); ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<?php foreach ($flashes as $flash): ?>
<div class="container-xxl mt-3">
    <div class="alert alert-<?php echo e($flash['type']); ?> alert-dismissible fade show shadow-sm">
        <?php echo e($flash['message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endforeach; ?>

<main class="public-main">
