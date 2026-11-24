<?php
declare(strict_types=1);
$user = require_login();
$pageTitle = $pageTitle ?? 'Administration';
$flashes = consume_flashes();
$isAdmin = in_array($user['role'], ['Super Admin', 'Admin'], true);
$isManager = in_array($user['role'], ['Super Admin', 'Admin', 'Commissioner', 'Department Head'], true);
$isOfficer = in_array($user['role'], ['Field Officer', 'Officer'], true);
$path = (string)(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '');
$currentPage = basename($path);

if ($isOfficer) {
    $menuGroups = [
        'My Workspace' => [
            ['officer-dashboard.php', 'Dashboard', 'bi-grid-1x2', true],
            ['my-complaints.php', 'My Complaints', 'bi-inbox', true],
            ['my-complaints.php?status=Assigned', 'Needs Action', 'bi-lightning-charge', true],
            ['officer-feedback.php', 'Citizen Feedback', 'bi-chat-left-heart', true],
            ['officer-profile.php', 'My Profile', 'bi-person-circle', true],
        ],
    ];
} else {
    $menuGroups = [
        'Workspace' => [
            ['dashboard.php', 'Overview', 'bi-grid-1x2-fill', true],
            ['reports.php', 'Complaint Data Export', 'bi-file-spreadsheet', true],
            ['icts-reports.php', 'ICTS Reports', 'bi-bar-chart-line', true],
            ['feedback.php', 'Citizen Feedback', 'bi-chat-left-text', true],
        ],
        'Complaint Management' => [
            ['../new-complaint.php', 'New Complaint (Public Form)', 'bi-plus-circle', true],
            ['complaints.php', 'All Complaints', 'bi-inboxes', true],
            ['complaint-centre.php?view=repeated', 'Potential Repeats', 'bi-files', true],
            ['complaint-centre.php?view=completed', 'Closed Complaints', 'bi-check2-circle', true],
            ['complaint-centre.php?view=category', 'Category Dashboard', 'bi-grid-3x3-gap', true],
            ['complaint-centre.php?view=zone', 'Zone Dashboard', 'bi-building', true],
            ['complaint-centre.php?view=map', 'Map / Area Coverage', 'bi-geo-alt', true],
        ],
        'Management' => [
            ['officers.php', 'Officer Directory', 'bi-people', $isManager],
            ['escalation.php', 'Escalation Monitor', 'bi-diagram-3', $isManager],
            ['masters.php?section=allocations', 'Allocation Rules', 'bi-bezier2', $isAdmin],
            ['allocation-inspector.php', 'Allocation Inspector', 'bi-search', $isAdmin],
            ['user-mapping-details.php', 'User Mapping Details', 'bi-person-lines-fill', $isAdmin],
            ['pdf-policy-review.php', 'PDF Policy Review', 'bi-file-earmark-check', $isAdmin],
            ['officer-readiness.php', 'Officer Readiness', 'bi-diagram-2', $isAdmin],
            ['officer-approvals.php', 'Officer Stage Approvals', 'bi-person-check', $isAdmin],
            ['escalation-audit.php', 'Escalation Audit', 'bi-shield-exclamation', $isAdmin],
            ['master-control.php', 'Advanced Masters', 'bi-database-check', $isAdmin],
            ['page-permissions.php', 'Role Page Mapper', 'bi-shield-lock', $isAdmin],
            ['masters.php', 'Master Data', 'bi-sliders', $isAdmin],
            ['users.php', 'Users & Roles', 'bi-person-gear', $isAdmin],
        ],
    ];
}
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
      <?php foreach ($menuGroups as $groupName => $items): ?>
        <div class="admin-menu-heading"><?php echo e($groupName); ?></div>
        <?php foreach ($items as [$target, $label, $icon, $visible]): ?>
          <?php
            if (!$visible) continue;
            if (!$isOfficer) {
                $routePermissions = [
                    'dashboard.php' => 'dashboard.view',
                    'complaints.php' => 'complaints.view',
                    'complaint-centre.php' => 'complaints.view',
                    'reports.php' => 'reports.view',
                    'icts-reports.php' => 'reports.view',
                    'feedback.php' => 'feedback.view',
                    'officers.php' => 'officers.view',
                    'escalation.php' => 'escalation.view',
                    'masters.php' => 'masters.view',
                    'allocation-inspector.php' => 'masters.view',
                    'user-mapping-details.php' => 'masters.view',
                    'pdf-policy-review.php' => 'masters.view',
                    'officer-readiness.php' => 'masters.view',
                    'officer-approvals.php' => 'masters.view',
                    'escalation-audit.php' => 'masters.view',
                    'master-control.php' => 'masters.view',
                    'users.php' => 'users.view',
                    'page-permissions.php' => 'roles.view',
                ];
                $routeFile = basename(explode('?', $target, 2)[0]);
                if (isset($routePermissions[$routeFile]) && !can_access_permission($user, $routePermissions[$routeFile])) continue;
            }
          ?>
          <?php
            $baseTarget = basename(explode('?', $target, 2)[0]);
            $targetQuery = (string)(parse_url($target, PHP_URL_QUERY) ?: '');
            parse_str($targetQuery, $targetParams);
            $active = $currentPage === $baseTarget;
            if ($baseTarget === 'complaint-centre.php') {
                $active = $active && ($_GET['view'] ?? 'category') === ($targetParams['view'] ?? 'category');
            } elseif ($baseTarget === 'masters.php') {
                $active = $active && (
                    isset($targetParams['section'])
                    ? ($_GET['section'] ?? '') === $targetParams['section']
                    : ($_GET['section'] ?? '') !== 'allocations'
                );
            } elseif ($baseTarget === 'my-complaints.php') {
                if (isset($targetParams['status'])) {
                    $active = $currentPage === 'my-complaints.php' && ($_GET['status'] ?? '') === $targetParams['status'];
                } else {
                    $active = $currentPage === 'my-complaints.php' && empty($_GET['status']);
                }
            }
          ?>
          <a class="admin-menu-link<?php echo $active ? ' is-active' : ''; ?>"
             href="<?php echo e(url('employee/' . $target)); ?>"<?php echo $active ? ' aria-current="page"' : ''; ?>>
            <i class="bi <?php echo e($icon); ?>" aria-hidden="true"></i><span><?php echo e($label); ?></span>
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
