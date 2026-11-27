<?php
declare(strict_types=1);
define('APP_NAME', 'Test portal');
function require_login(): array { return ['role'=>'Super Admin','full_name'=>'Test Administrator']; }
function consume_flashes(): array { return []; }
function can_access_permission(array $user, string $permission): bool { return true; }
function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function url(string $path=''): string { return '/test/'.$path; }
foreach (['officer-complaint-report.php','pdf-policy-review.php'] as $route) {
    $_SERVER['REQUEST_URI'] = '/test/employee/'.$route;
    $_GET = $route === 'officer-complaint-report.php' ? ['officer_id'=>'42','view'=>'current'] : ['category_id'=>'12'];
    $selected = ['id'=>42,'officer_name'=>'Fixture officer'];
    $item = ['report_data'=>123];
    $link = 'fixture';
    $active = 'report state';
    $rows = [['id'=>99]];
    $pageTitle = 'Fixture report';
    $before = [$selected,$item,$link,$active,$rows,$pageTitle];
    ob_start();
    include __DIR__.'/../includes/employee-header.php';
    $html = ob_get_clean();
    if ([$selected,$item,$link,$active,$rows,$pageTitle] !== $before) {
        throw new RuntimeException('Shared header overwrote caller state: '.$route);
    }
    if (!str_contains($html,'aria-current="page"') || !str_contains($html,'Fixture report')) {
        throw new RuntimeException('Navigation failed to render: '.$route);
    }
}
echo "Shared header scope regression test passed.\n";
