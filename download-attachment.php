<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

$attachmentId = (int)($_GET['id'] ?? 0);

if ($attachmentId <= 0) {
    http_response_code(400);
    exit('Invalid attachment request.');
}

$stmt = db()->prepare(
    'SELECT a.*
     FROM complaint_attachments a
     JOIN complaints c ON c.id = a.complaint_id
     WHERE a.id = ? AND c.is_deleted = 0
     LIMIT 1'
);
$stmt->execute([$attachmentId]);
$attachment = $stmt->fetch();

if (!$attachment) {
    http_response_code(404);
    exit('Attachment not found.');
}

$path = realpath(__DIR__ . '/' . $attachment['file_path']);
$storageRoot = realpath(__DIR__ . '/storage/uploads/complaints');

if (!$path || !$storageRoot || !str_starts_with($path, $storageRoot) || !is_file($path)) {
    http_response_code(404);
    exit('Attachment file not found.');
}

header('Content-Type: ' . $attachment['mime_type']);
header('Content-Length: ' . filesize($path));
header('Content-Disposition: attachment; filename="' . basename($attachment['original_name']) . '"');
readfile($path);
exit;
