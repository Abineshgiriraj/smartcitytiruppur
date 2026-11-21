<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

$departmentId = (int)($_GET['department_id'] ?? 0);

if ($departmentId <= 0) {
    echo json_encode([]);
    exit;
}

$stmt = db()->prepare(
    'SELECT id, category_name, sla_days
     FROM complaint_categories
     WHERE department_id = ? AND is_active = 1
     ORDER BY category_name'
);
$stmt->execute([$departmentId]);

echo json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE);
