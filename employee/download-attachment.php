<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/complaint-officer-access.php';

$user = require_login();
$isOfficer = in_array($user['role'],['Field Officer','Officer'],true);
$officerId = $isOfficer ? (int)require_officer()['officer']['id'] : 0;
if (!$isOfficer) require_permission('complaints.view');
$attachmentId = (int)($_GET['id'] ?? 0);

$stmt = db()->prepare(
    'SELECT a.*, c.id AS complaint_id, c.reference_no, c.department_id, c.zone_id, c.ward_id, c.assigned_officer_id
     FROM complaint_attachments a
     JOIN complaints c ON c.id = a.complaint_id
     WHERE a.id = ? LIMIT 1'
);
$stmt->execute([$attachmentId]);
$attachment = $stmt->fetch();

if (!$attachment) {
    http_response_code(404);
    exit('Attachment not found.');
}

$allowed = can_manage_all_complaints($user);

if (!$allowed && $user['role'] === 'Department Head') $allowed = (int)$user['department_id'] === (int)$attachment['department_id'];
if (!$allowed && $user['role'] === 'Zone Officer') $allowed = (int)$user['zone_id'] === (int)$attachment['zone_id'];
if (!$allowed && $user['role'] === 'Ward Officer') $allowed = (int)$user['ward_id'] === (int)$attachment['ward_id'];
if (!$allowed && $isOfficer) {
    $allowed = $officerId > 0 && $officerId === (int)$attachment['assigned_officer_id'];
    if (!$allowed && $officerId > 0) {
        // Grant historical officers read-only access to attachments for their
        // previously assigned complaints, never to unrelated attachments.
        $allowed = complaint_officer_was_assigned(
            db(), (int)$attachment['complaint_id'], $officerId,
            (int)($attachment['assigned_officer_id'] ?? 0),
            (string)$attachment['reference_no']
        );
    }
}

if (!$allowed) {
    http_response_code(403);
    exit('Access denied.');
}

$path = realpath(__DIR__ . '/../' . $attachment['file_path']);
$storageRoot = realpath(__DIR__ . '/../storage/uploads/complaints');

if (!$path || !$storageRoot || !str_starts_with($path, $storageRoot) || !is_file($path)) {
    http_response_code(404);
    exit('Attachment file not found.');
}

$disposition = (isset($_GET['view']) && $_GET['view'] === 'inline' && str_starts_with((string)$attachment['mime_type'], 'image/')) ? 'inline' : 'attachment';

header('Content-Type: ' . $attachment['mime_type']);
header('Content-Length: ' . filesize($path));
header('Content-Disposition: ' . $disposition . '; filename="' . basename($attachment['original_name']) . '"');
readfile($path);
exit;
