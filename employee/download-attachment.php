<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

$user = require_login();
$attachmentId = (int)($_GET['id'] ?? 0);

$stmt = db()->prepare(
    'SELECT a.*, c.department_id, c.zone_id, c.ward_id, c.assigned_officer_id
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
if (!$allowed && $user['role'] === 'Field Officer') {
    $officerStmt = db()->prepare('SELECT id FROM officers WHERE user_id = ? LIMIT 1');
    $officerStmt->execute([(int)$user['id']]);
    $allowed = (int)$officerStmt->fetchColumn() === (int)$attachment['assigned_officer_id'];
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

header('Content-Type: ' . $attachment['mime_type']);
header('Content-Length: ' . filesize($path));
header('Content-Disposition: attachment; filename="' . basename($attachment['original_name']) . '"');
readfile($path);
exit;
