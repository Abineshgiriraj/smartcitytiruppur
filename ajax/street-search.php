<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

$query = trim($_GET['q'] ?? '');

if (mb_strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

$stmt = db()->prepare(
    "SELECT s.id, s.street_name, s.ward_id, w.ward_no, z.id AS zone_id, z.zone_no
     FROM streets s
     JOIN wards w ON w.id = s.ward_id
     JOIN zones z ON z.id = w.zone_id
     WHERE s.is_active = 1 AND w.is_active = 1 AND z.is_active = 1
       AND s.street_name LIKE ?
     ORDER BY s.street_name
     LIMIT 15"
);
$stmt->execute(['%' . $query . '%']);

echo json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE);
