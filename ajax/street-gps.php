<?php
declare(strict_types=1);
/**
 * Suggests nearby *mapped locality points* for a citizen to confirm.
 * This is not a ward-boundary GIS calculation and must never assign a ward
 * without the citizen selecting a returned normalized street record.
 * Does not persist coordinates.
 */
require_once __DIR__ . '/../includes/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    echo json_encode(['result' => 'method_not_allowed']);
    exit;
}

verify_csrf();
$lat=filter_input(INPUT_POST,'latitude',FILTER_VALIDATE_FLOAT);
$lng=filter_input(INPUT_POST,'longitude',FILTER_VALIDATE_FLOAT);
$accuracy=filter_input(INPUT_POST,'accuracy',FILTER_VALIDATE_FLOAT);
if (!is_float($lat) || !is_float($lng) || !is_float($accuracy)
    || !is_finite($lat) || !is_finite($lng) || !is_finite($accuracy)
    || $lat < -90 || $lat > 90 || $lng < -180 || $lng > 180 || $accuracy <= 0) {
    http_response_code(400);
    echo json_encode(['result'=>'invalid']);
    exit;
}

// Approximate search envelope, NOT an administrative boundary determination.
if ($lat < 10.9 || $lat > 11.25 || $lng < 77.15 || $lng > 77.6) {
    echo json_encode(['result'=>'outside']);
    exit;
}
if ($accuracy > 120) {
    echo json_encode(['result'=>'inaccurate']);
    exit;
}

$radius=max(80.0,min(240.0,$accuracy*2.0));
$pad=0.003;
try {
    $query=db()->prepare(
        "SELECT s.id,s.street_name,s.ward_id,w.ward_no,
                z.id AS zone_id,z.zone_no,
                ST_DISTANCE_SPHERE(
                    POINT(?,?),
                    POINT(CAST(TRIM(l.Longitude) AS DECIMAL(12,8)),
                          CAST(TRIM(l.Latitude) AS DECIMAL(12,8)))
                ) AS distance_m
         FROM raw_localitymaster l
         JOIN streets s ON s.id=l.LocalityMasterID
                       AND s.ward_id=l.WardsMasterID AND s.is_active=1
         JOIN wards w ON w.id=s.ward_id AND w.is_active=1
         JOIN zones z ON z.id=w.zone_id AND z.is_active=1
         WHERE TRIM(l.Latitude) REGEXP '^[0-9]+([.][0-9]+)?$'
           AND TRIM(l.Longitude) REGEXP '^[0-9]+([.][0-9]+)?$'
           AND CAST(TRIM(l.Latitude) AS DECIMAL(12,8)) BETWEEN ? AND ?
           AND CAST(TRIM(l.Longitude) AS DECIMAL(12,8)) BETWEEN ? AND ?
         HAVING distance_m <= ?
         ORDER BY distance_m,s.id LIMIT 8"
    );
    $query->execute([$lng,$lat,$lat-$pad,$lat+$pad,$lng-$pad,$lng+$pad,$radius]);
    $rows=$query->fetchAll(PDO::FETCH_ASSOC);
    if (!$rows) {
        echo json_encode(['result'=>'no_match']);
        exit;
    }
    $out=[];
    foreach ($rows as $row) {
        $out[]=[
            'id'=>(int)$row['id'],
            'street_name'=>(string)$row['street_name'],
            'ward_id'=>(int)$row['ward_id'],
            'ward_no'=>(string)$row['ward_no'],
            'zone_id'=>(int)$row['zone_id'],
            'zone_no'=>(string)$row['zone_no'],
            'distance_m'=>round((float)$row['distance_m'])
        ];
    }
    echo json_encode(['result'=>'choices','streets'=>$out],JSON_INVALID_UTF8_SUBSTITUTE);
} catch (Throwable $error) {
    log_message('GPS lookup failed: '.$error->getMessage());
    http_response_code(503);
    echo json_encode(['result'=>'manual']);
}
