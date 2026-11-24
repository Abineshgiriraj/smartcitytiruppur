<?php
declare(strict_types=1);
/**
 * Suggest only active localities from the SAME authoritative streets table
 * as ajax/street-search.php. Raw GIS coordinates are used solely as hints.
 * A citizen must confirm the matched locality; there is no ward polygon lookup.
 * Location coordinates are never persisted.
 */
require_once __DIR__ . '/../includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, private');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    echo json_encode(['result' => 'method_not_allowed']);
    exit;
}
verify_csrf();
$lat = filter_input(INPUT_POST, 'latitude', FILTER_VALIDATE_FLOAT);
$lng = filter_input(INPUT_POST, 'longitude', FILTER_VALIDATE_FLOAT);
$accuracy = filter_input(INPUT_POST, 'accuracy', FILTER_VALIDATE_FLOAT);
if (!is_float($lat) || !is_float($lng) || !is_float($accuracy)
    || !is_finite($lat) || !is_finite($lng) || !is_finite($accuracy)
    || $lat < -90 || $lat > 90 || $lng < -180 || $lng > 180 || $accuracy <= 0) {
    http_response_code(400);
    echo json_encode(['result' => 'invalid']);
    exit;
}
// These are coarse search bounds, never municipal ward boundaries.
if ($lat < 10.9 || $lat > 11.25 || $lng < 77.15 || $lng > 77.6) {
    echo json_encode(['result'=>'outside']);
    exit;
}
if ($accuracy > 120) {
    echo json_encode(['result'=>'inaccurate']);
    exit;
}
$maxDistance = max(80.0, min(240.0, $accuracy * 2.0));
$latitudeWindow = 0.004;
$longitudeWindow = 0.004;
try {
    // IDs, labels and wards are ALWAYS sourced from streets -> wards -> zones.
    // raw_localitymaster supplies only matching point coordinates.
    // Compare both the imported ID + ward ID AND the normalized name
    // to avoid silently accepting a mismatched legacy row.
    $stmt = db()->prepare(
        "SELECT s.id, s.street_name, s.ward_id, w.ward_no,
                z.id AS zone_id, z.zone_no,
                CAST(TRIM(l.Latitude) AS DECIMAL(12,8)) AS point_lat,
                CAST(TRIM(l.Longitude) AS DECIMAL(12,8)) AS point_lng
         FROM streets s
         JOIN wards w ON w.id = s.ward_id AND w.is_active = 1
         JOIN zones z ON z.id = w.zone_id AND z.is_active = 1
         JOIN raw_localitymaster l
            ON l.LocalityMasterID = s.id
           AND l.WardsMasterID = s.ward_id
           AND LOWER(TRIM(l.Locality)) = LOWER(TRIM(s.street_name))
         WHERE s.is_active = 1
           AND TRIM(l.Latitude) REGEXP '^[0-9]+([.][0-9]+)?$'
           AND TRIM(l.Longitude) REGEXP '^[0-9]+([.][0-9]+)?$'
           AND CAST(TRIM(l.Latitude) AS DECIMAL(12,8)) BETWEEN ? AND ?
           AND CAST(TRIM(l.Longitude) AS DECIMAL(12,8)) BETWEEN ? AND ?
         LIMIT 2000"
    );
    $stmt->execute([$lat - $latitudeWindow, $lat + $latitudeWindow,
                    $lng - $longitudeWindow, $lng + $longitudeWindow]);
    $matches=[];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $pointLat=(float)$r['point_lat'];
        $pointLng=(float)$r['point_lng'];
        $dLat=deg2rad($pointLat-$lat);
        $dLng=deg2rad($pointLng-$lng);
        $a=sin($dLat/2)**2 + cos(deg2rad($lat))*cos(deg2rad($pointLat))*sin($dLng/2)**2;
        $distance=6371000 * 2 * atan2(sqrt($a), sqrt(max(0.0,1-$a)));
        if($distance>$maxDistance)continue;
        $matches[]=[
            'id'=>(int)$r['id'],
            'street_name'=>(string)$r['street_name'],
            'ward_id'=>(int)$r['ward_id'],
            'ward_no'=>(string)$r['ward_no'],
            'zone_id'=>(int)$r['zone_id'],
            'zone_no'=>(string)$r['zone_no'],
            'distance_m'=>(int)round($distance)
        ];
    }
    usort($matches,static fn(array $a,array $b):int =>
        $a['distance_m']<=>$b['distance_m'] ?: $a['id']<=>$b['id']);
    echo json_encode([
        'result'=>$matches?'choices':'no_match',
        'list_verified'=>true,
        'streets'=>array_slice($matches,0,8)
    ], JSON_INVALID_UTF8_SUBSTITUTE);
} catch (Throwable $error) {
    // Do not log the citizen's coordinates.
    log_message('Canonical GPS locality lookup unavailable: '.$error->getMessage());
    http_response_code(503);
    echo json_encode(['result'=>'manual']);
}
