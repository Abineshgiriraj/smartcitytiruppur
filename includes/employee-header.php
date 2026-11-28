<?php
declare(strict_types=1);

// Includes share their caller's scope; isolate all header rendering variables.
(static function (string $pageTitle): void {
$user = require_login();
$flashes = consume_flashes();
$isAdmin = in_array($user['role'], ['Super Admin', 'Admin'], true);
$isManager = in_array($user['role'], ['Super Admin', 'Admin', 'Commissioner', 'Department Head'], true);
$isOfficer = in_array($user['role'], ['Field Officer', 'Officer'], true);
$path = (string)(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '');
$currentPage = basename($path);

// Navigation groups reuse existing routes. Each child retains its own permission.
require_once __DIR__ . '/employee-navigation.php';
$navigation = employee_navigation($user);
$activeNavigation = employee_navigation_active($navigation, $currentPage, $_GET);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex,nofollow">
  <title><?php echo e($pageTitle); ?> | <?php echo e(APP_NAME); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?php echo e(url('assets/css/style.css?v=20260806b')); ?>" rel="stylesheet">
  <link href="<?php echo e(url('assets/css/admin.css?v=20261119a')); ?>" rel="stylesheet">
  <link href="<?php echo e(url('assets/css/admin-refresh.css?v=20261121b')); ?>" rel="stylesheet">
    <link href="<?php echo e(url('assets/css/civic-modern.css?v=20261124')); ?>" rel="stylesheet">
  <?php if ($currentPage === 'complaint-view.php'): ?>
  <link href="<?php echo e(url('assets/css/complaint-detail-modern.css?v=20261128_simple')); ?>" rel="stylesheet">
  <?php endif; ?>
  <?php if ($currentPage === 'escalation-reports.php'): ?>
  <link href="<?php echo e(url('assets/css/escalation-reports.css?v=20261125')); ?>" rel="stylesheet">
  <?php endif; ?>
  <?php if ($currentPage === 'officer-complaint-report.php'): ?>
  <link href="<?php echo e(url('assets/css/officer-complaint-report.css?v=20261125')); ?>" rel="stylesheet">
  <?php endif; ?>
</head>
<body class="employee-body admin-body">
<div class="admin-layout">
  <div class="admin-overlay" id="adminOverlay"></div>
  <aside class="admin-sidebar" id="adminSidebar" aria-label="Administration navigation">
    <a class="admin-brand" href="<?php echo e(url($isOfficer ? 'employee/officer-dashboard.php' : 'employee/dashboard.php')); ?>">
      <img src="<?php echo e(url('assets/images/trippurlogo.png')); ?>" alt="Tiruppur Corporation">
      <span><strong>TIRUPPUR</strong><small><?php echo $isOfficer ? 'Officer Portal' : 'City Corporation'; ?></small></span>
    </a>
    <div class="admin-suite"><span class="admin-suite-dot"></span> <?php echo $isOfficer ? 'Field Officer Portal' : 'Grievance Administration'; ?></div>
    <nav class="admin-menu">
      <?php foreach ($navigation as $groupName => $items): ?>
        <div class="admin-menu-heading"><?= e($groupName) ?></div>
        <?php foreach ($items as $item): $active = $activeNavigation !== null && $activeNavigation['key'] === $item['key']; ?>
          <a class="admin-menu-link<?= $active ? ' is-active' : '' ?>"
             href="<?= e(url('employee/' . $item['links'][0][0])) ?>"<?= $active ? ' aria-current="page"' : '' ?>>
            <i class="bi <?= e($item['icon']) ?>" aria-hidden="true"></i><span><?= e($item['label']) ?></span>
          </a>
        <?php endforeach; ?>
      <?php endforeach; ?>
    </nav>
    <div class="admin-sidebar-bottom">
      <span class="admin-avatar"><?php echo e(strtoupper(substr(trim((string)$user['full_name']), 0, 1))); ?></span>
      <div class="admin-account"><strong><?php echo e($user['full_name']); ?></strong><small><?php echo e($user['role']); ?></small></div>
    </div>
  </aside>
  <div class="admin-main-area">
    <header class="admin-topbar">
      <button id="adminMenuToggle" class="admin-menu-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false"><i class="bi bi-list"></i></button>
      <div class="admin-topbar-title"><span class="text-muted"><?php echo $isOfficer ? 'Officer Portal' : 'Administration'; ?></span><i class="bi bi-chevron-right"></i><strong><?php echo e($pageTitle); ?></strong></div>
      <form class="admin-quick-search" method="get" action="<?php echo e(url($isOfficer ? 'employee/my-complaints.php' : 'employee/complaints.php')); ?>" role="search">
        <i class="bi bi-search"></i>
        <input name="q" aria-label="Search complaints" placeholder="<?php echo $isOfficer ? 'Search my complaints...' : 'Search complaints...'; ?>" autocomplete="off" value="<?php echo e($_GET['q'] ?? ''); ?>">
      </form>
      <a class="admin-top-icon" href="<?php echo e(url('index.php')); ?>" title="Open public website" aria-label="Open public website"><i class="bi bi-box-arrow-up-right"></i></a>
      <a class="admin-logout" href="<?php echo e(url('employee/logout.php')); ?>"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a>
    </header>
    <main class="admin-page container-fluid">
      <?php foreach ($flashes as $flash): ?>
        <div class="alert alert-<?php echo e($flash['type']); ?> alert-dismissible fade show" role="alert">
          <?php echo e($flash['message']); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endforeach; ?>

      <?php if ($activeNavigation !== null && count($activeNavigation['links']) > 1): ?>
        <nav class="d-flex flex-wrap gap-2 mb-3 pb-2 border-bottom" aria-label="<?= e($activeNavigation['label']) ?> sections">
          <?php foreach ($activeNavigation['links'] as $link): $selected = employee_navigation_matches($link[0], $currentPage, $_GET); ?>
            <a class="btn btn-sm <?= $selected ? 'btn-primary' : 'btn-outline-primary' ?>"
               href="<?= e(url('employee/' . $link[0])) ?>"<?= $selected ? ' aria-current="page"' : '' ?>><?= e($link[1]) ?></a>
          <?php endforeach; ?>
        </nav>
      <?php endif; ?>
<?php
})((string)($pageTitle ?? 'Administration'));
?>
