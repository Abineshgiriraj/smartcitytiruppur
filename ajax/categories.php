<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');

// Legacy callers request a specific department; the public category finder
// uses all=1 so that every existing category remains discoverable.
$departmentId = (int)($_GET['department_id'] ?? 0);
$all = ($_GET['all'] ?? '') === '1';
if (!$all && $departmentId <= 0) {
    echo json_encode([], JSON_UNESCAPED_UNICODE);
    exit;
}
try {
    $sql = "SELECT cc.id, cc.department_id, d.department_name,
                   cc.category_name, cc.sla_days
            FROM complaint_categories cc
            INNER JOIN departments d ON d.id=cc.department_id
            WHERE cc.is_active=1 AND d.is_active=1
              AND TRIM(cc.category_name) NOT IN ('','-')";
    $parameters=[];
    if (!$all) {
        $sql .= ' AND cc.department_id=?';
        $parameters[]=$departmentId;
    }
    $sql .= ' ORDER BY d.department_name,cc.category_name,cc.id LIMIT 1000';
    $stmt=db()->prepare($sql);
    $stmt->execute($parameters);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC),
        JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
} catch (Throwable $error) {
    log_message('Public complaint-category lookup failed: '.$error->getMessage());
    http_response_code(503);
    echo json_encode(['error'=>'Unable to load complaint categories.']);
}
