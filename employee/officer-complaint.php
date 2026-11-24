<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

// Compatibility for existing bookmarked officer Manage links.
// The standard complaint page enforces ownership and allowed status transitions.
require_officer();
$id=(int)($_GET['id'] ?? 0);
if($id<=0) {
    redirect('employee/my-complaints.php');
}
if($_SERVER['REQUEST_METHOD']!=='GET') {
    http_response_code(405);
    exit('Use the complaint details page to submit status updates.');
}
redirect('employee/complaint-view.php?id='.$id);
