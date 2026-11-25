<?php
declare(strict_types=1);
/**
 * GPS Ward & Zone Resolver for Tiruppur City Municipal Corporation.
 * Directly resolves GPS coordinates to municipal Ward & Zone.
 * Does not use or require streets/locality table lookup.
 */
require_once __DIR__ . '/../includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, private');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    echo json_encode(['ok' => false, 'result' => 'method_not_allowed']);
    exit;
}

try {
    verify_csrf();
} catch (Throwable $e) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'result' => 'csrf_error', 'message' => 'Session expired. Please refresh the page.']);
    exit;
}

$action = trim($_POST['action'] ?? 'gps_resolve');

// 1. Return all 60 wards with zones for manual selection fallback
if ($action === 'get_wards') {
    try {
        $wards = db()->query(
            "SELECT w.id AS ward_id, w.ward_no, w.ward_name, z.id AS zone_id, z.zone_no, z.zone_name
             FROM wards w
             JOIN zones z ON z.id = w.zone_id AND z.is_active = 1
             WHERE w.is_active = 1
             ORDER BY w.id ASC"
        )->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['ok' => true, 'wards' => $wards], JSON_UNESCAPED_UNICODE);
        exit;
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'message' => 'Unable to load wards list.']);
        exit;
    }
}

// 2. Direct GPS to Ward & Zone resolution
$lat = filter_var($_POST['latitude'] ?? null, FILTER_VALIDATE_FLOAT);
$lng = filter_var($_POST['longitude'] ?? null, FILTER_VALIDATE_FLOAT);
$accuracy = filter_var($_POST['accuracy'] ?? null, FILTER_VALIDATE_FLOAT);
if ($accuracy === false || $accuracy === null || $accuracy <= 0) {
    $accuracy = 50.0;
}

if (!is_float($lat) || !is_float($lng)
    || !is_finite($lat) || !is_finite($lng)
    || $lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'result' => 'invalid', 'message' => 'Invalid GPS coordinates provided.']);
    exit;
}

// Tiruppur Corporation bounding box
if ($lat < 10.90 || $lat > 11.30 || $lng < 77.10 || $lng > 77.65) {
    echo json_encode([
        'ok' => true,
        'result' => 'outside',
        'confident' => false,
        'message' => 'GPS coordinates appear outside Tiruppur Municipal Corporation boundaries. Please select Ward manually.'
    ]);
    exit;
}

// Helper functions for GIS boundary Point-In-Polygon matching and Nominatim reverse geocode
function point_in_polygon(array $point, array $vs): bool {
    $x = $point[0]; $y = $point[1];
    $inside = false;
    $count = count($vs);
    for ($i = 0, $j = $count - 1; $i < $count; $j = $i++) {
        $xi = $vs[$i][0]; $yi = $vs[$i][1];
        $xj = $vs[$j][0]; $yj = $vs[$j][1];
        $intersect = (($yi > $y) !== ($yj > $y))
            && ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);
        if ($intersect) $inside = !$inside;
    }
    return $inside;
}

function match_point_to_gis_ward(float $lat, float $lng): ?array {
    $geoPath = __DIR__ . '/../assets/data/ward_boundaries.json';
    if (!file_exists($geoPath)) return null;

    static $geoData = null;
    if ($geoData === null) {
        $raw = file_get_contents($geoPath);
        $geoData = $raw ? json_decode($raw, true) : null;
    }
    if (!isset($geoData['features'])) return null;

    foreach ($geoData['features'] as $f) {
        if (isset($f['bbox']) && count($f['bbox']) === 4) {
            if ($lng < $f['bbox'][0] || $lng > $f['bbox'][2] || $lat < $f['bbox'][1] || $lat > $f['bbox'][3]) {
                continue;
            }
        }
        $geom = $f['geometry'] ?? [];
        $type = $geom['type'] ?? '';
        $coords = $geom['coordinates'] ?? [];
        $props = $f['properties'] ?? [];
        $wardNum = (int)($props['Ward_Numbe'] ?? ($props['Name'] ?? 0));
        $zoneNum = (int)($props['Zone'] ?? 0);

        if ($type === 'Polygon' && !empty($coords[0])) {
            if (point_in_polygon([$lng, $lat], $coords[0])) {
                return ['ward_num' => $wardNum, 'zone_num' => $zoneNum];
            }
        } elseif ($type === 'MultiPolygon') {
            foreach ($coords as $poly) {
                if (!empty($poly[0]) && point_in_polygon([$lng, $lat], $poly[0])) {
                    return ['ward_num' => $wardNum, 'zone_num' => $zoneNum];
                }
            }
        }
    }
    return null;
}

function fetch_osm_reverse_geocode(float $lat, float $lng): ?array {
    $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat=" . urlencode((string)$lat) . "&lon=" . urlencode((string)$lng) . "&zoom=18&addressdetails=1";
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT      => 'SmartCityTiruppur/1.0 (contact: admin@localhost)',
        CURLOPT_TIMEOUT        => 3,
        CURLOPT_CONNECTTIMEOUT => 2,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $res = curl_exec($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status >= 200 && $status < 300 && $res) {
        $data = json_decode($res, true);
        if ($data) {
            $addr = $data['address'] ?? [];
            $poi = $data['name'] ?? ($addr['office'] ?? ($addr['amenity'] ?? ($addr['building'] ?? ($addr['shop'] ?? ''))));
            $road = $addr['road'] ?? ($addr['street'] ?? ($addr['residential'] ?? ''));
            $suburb = $addr['suburb'] ?? ($addr['neighbourhood'] ?? ($addr['subdivision'] ?? ($addr['county'] ?? '')));
            $city = $addr['city'] ?? ($addr['town'] ?? 'Tiruppur');

            $parts = [];
            if ($poi && $poi !== $road) $parts[] = $poi;
            if ($road) $parts[] = $road;
            if ($suburb && $suburb !== $road && $suburb !== $poi) $parts[] = $suburb;
            if (!$parts && $city) $parts[] = $city;

            $shortLocality = implode(', ', array_slice($parts, 0, 2));

            return [
                'display_name' => (string)($data['display_name'] ?? ''),
                'short_locality' => $shortLocality ?: ($data['display_name'] ?? ''),
            ];
        }
    }
    return null;
}

try {
    // 1. First, check official GIS Ward Boundaries using Point-In-Polygon
    $gisMatch = match_point_to_gis_ward($lat, $lng);
    $matchedWard = null;

    if ($gisMatch !== null) {
        $targetNum = $gisMatch['ward_num'];
        $wardPadded = sprintf('WARD-(%02d)', $targetNum);
        $wardUnpadded = sprintf('WARD-(%d)', $targetNum);
        $wardPlain = sprintf('WARD-%02d', $targetNum);

        $stmt = db()->prepare(
            'SELECT w.id AS ward_id, w.ward_no, w.ward_name, z.id AS zone_id, z.zone_no, z.zone_name
             FROM wards w
             JOIN zones z ON z.id = w.zone_id AND z.is_active = 1
             WHERE w.ward_no IN (?, ?, ?) AND w.is_active = 1
             LIMIT 1'
        );
        $stmt->execute([$wardPadded, $wardUnpadded, $wardPlain]);
        $matchedWard = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 2. Fetch real-world address from OpenStreetMap Nominatim reverse geocode
    $osm = fetch_osm_reverse_geocode($lat, $lng);
    $detectedLocality = $osm['short_locality'] ?? '';

    // 3. If GIS polygon matched a ward
    if ($matchedWard) {
        if (!$detectedLocality) {
            // Fallback to nearest locality inside this ward
            $locStmt = db()->prepare(
                'SELECT TRIM(Locality) AS locality_name FROM raw_localitymaster
                 WHERE WardsMasterID = ? AND Locality IS NOT NULL AND TRIM(Locality) != "" LIMIT 1'
            );
            $locStmt->execute([$matchedWard['ward_id']]);
            $locRow = $locStmt->fetch(PDO::FETCH_ASSOC);
            $detectedLocality = $locRow['locality_name'] ?? ($matchedWard['ward_name'] ?: $matchedWard['ward_no']);
        }

        echo json_encode([
            'ok' => true,
            'result' => 'resolved',
            'confident' => true,
            'source' => 'gis_polygon',
            'latitude' => $lat,
            'longitude' => $lng,
            'accuracy_m' => round($accuracy),
            'ward_id' => (int)$matchedWard['ward_id'],
            'ward_no' => (string)$matchedWard['ward_no'],
            'zone_id' => (int)$matchedWard['zone_id'],
            'zone_no' => (string)$matchedWard['zone_no'],
            'locality' => $detectedLocality,
            'display_name' => $osm['display_name'] ?? '',
            'message' => 'GPS matched to ' . $matchedWard['ward_no'] . ' · ' . $matchedWard['zone_no']
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Fallback: nearest municipal point search if outside exact polygons
    $window = 0.05;
    $stmt = db()->prepare(
        "SELECT l.WardsMasterID AS ward_id, w.ward_no, z.id AS zone_id, z.zone_no,
                TRIM(l.Locality) AS locality_name,
                CAST(TRIM(l.Latitude) AS DECIMAL(12,8)) AS point_lat,
                CAST(TRIM(l.Longitude) AS DECIMAL(12,8)) AS point_lng
         FROM raw_localitymaster l
         JOIN wards w ON w.id = l.WardsMasterID AND w.is_active = 1
         JOIN zones z ON z.id = w.zone_id AND z.is_active = 1
         WHERE TRIM(l.Latitude) REGEXP '^[0-9]+([.][0-9]+)?$'
           AND TRIM(l.Longitude) REGEXP '^[0-9]+([.][0-9]+)?$'
           AND CAST(TRIM(l.Latitude) AS DECIMAL(12,8)) BETWEEN ? AND ?
           AND CAST(TRIM(l.Longitude) AS DECIMAL(12,8)) BETWEEN ? AND ?
         LIMIT 1000"
    );
    $stmt->execute([$lat - $window, $lat + $window, $lng - $window, $lng + $window]);
    $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $nearest = null;
    $minDist = INF;

    foreach ($candidates as $row) {
        $pLat = (float)$row['point_lat'];
        $pLng = (float)$row['point_lng'];
        $dLat = deg2rad($pLat - $lat);
        $dLng = deg2rad($pLng - $lng);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat)) * cos(deg2rad($pLat)) * sin($dLng / 2) ** 2;
        $distance = 6371000 * 2 * atan2(sqrt($a), sqrt(max(0.0, 1 - $a)));

        if ($distance < $minDist) {
            $minDist = $distance;
            $nearest = [
                'ward_id' => (int)$row['ward_id'],
                'ward_no' => (string)$row['ward_no'],
                'zone_id' => (int)$row['zone_id'],
                'zone_no' => (string)$row['zone_no'],
                'locality' => $detectedLocality ?: (string)($row['locality_name'] ?? ''),
                'distance_m' => (int)round($distance),
            ];
        }
    }

    if ($nearest !== null) {
        echo json_encode([
            'ok' => true,
            'result' => 'resolved',
            'confident' => ($nearest['distance_m'] <= 1500),
            'source' => 'nearest_locality',
            'latitude' => $lat,
            'longitude' => $lng,
            'accuracy_m' => round($accuracy),
            'locality' => $nearest['locality'],
            'ward_id' => $nearest['ward_id'],
            'ward_no' => $nearest['ward_no'],
            'zone_id' => $nearest['zone_id'],
            'zone_no' => $nearest['zone_no'],
            'distance_m' => $nearest['distance_m'],
            'display_name' => $osm['display_name'] ?? '',
            'message' => 'GPS matched to ' . $nearest['ward_no'] . ' · ' . $nearest['zone_no']
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'ok' => true,
            'result' => 'unresolved',
            'confident' => false,
            'latitude' => $lat,
            'longitude' => $lng,
            'locality' => $detectedLocality,
            'display_name' => $osm['display_name'] ?? '',
            'message' => 'No nearby municipal reference found. Please select your Ward manually.'
        ]);
    }
} catch (Throwable $e) {
    log_message('GPS resolve error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'result' => 'error',
        'message' => 'Unable to resolve GPS location. Please select Ward manually.'
    ]);
}
