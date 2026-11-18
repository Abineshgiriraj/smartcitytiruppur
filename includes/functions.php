<?php
declare(strict_types=1);

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    $base = rtrim(BASE_URL, '/');
    $path = ltrim($path, '/');

    return $path === '' ? ($base ?: '/') : ($base . '/' . $path);
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';

    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(419);
        exit('Your session token is invalid or expired. Refresh the page and try again.');
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function consume_flashes(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);

    return is_array($messages) ? $messages : [];
}

function complaint_statuses(): array
{
    return [
        'Registered',
        'Assigned',
        'Acknowledged',
        'In Progress',
        'Resolved',
        'Closed',
        'Rejected',
        'Reopened',
    ];
}

function status_badge_class(string $status): string
{
    return match (strtolower(trim($status))) {
        'resolved', 'closed' => 'success',
        'rejected' => 'danger',
        'in progress', 'acknowledged' => 'primary',
        'assigned', 'reopened' => 'warning',
        default => 'secondary',
    };
}

function normalize_mobile(string $mobile): string
{
    return preg_replace('/\D+/', '', $mobile) ?? '';
}

function mask_mobile(string $mobile): string
{
    $mobile = normalize_mobile($mobile);

    if (strlen($mobile) < 4) {
        return $mobile;
    }

    return str_repeat('*', max(0, strlen($mobile) - 4)) . substr($mobile, -4);
}

function valid_date(string $date): bool
{
    $object = DateTime::createFromFormat('Y-m-d', $date);

    return $object && $object->format('Y-m-d') === $date;
}

function generate_reference_number(int $complaintId): string
{
    return sprintf('TCMC-%s-%06d', date('Y'), $complaintId);
}

function log_message(string $message): void
{
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
    @file_put_contents(__DIR__ . '/../storage/logs/app.log', $line, FILE_APPEND);
}

function audit_log(
    ?int $userId,
    string $action,
    string $entityType = '',
    ?int $entityId = null,
    array $details = []
): void {
    try {
        $stmt = db()->prepare(
            'INSERT INTO audit_logs
             (user_id, action, entity_type, entity_id, details_json, ip_address, user_agent)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $userId,
            $action,
            $entityType,
            $entityId,
            json_encode($details, JSON_UNESCAPED_UNICODE),
            $_SERVER['REMOTE_ADDR'] ?? null,
            substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
        ]);
    } catch (Throwable $exception) {
        log_message('Audit log failed: ' . $exception->getMessage());
    }
}

function setting(string $key, string $default = ''): string
{
    try {
        $stmt = db()->prepare('SELECT setting_value FROM system_settings WHERE setting_key = ? LIMIT 1');
        $stmt->execute([$key]);
        $value = $stmt->fetchColumn();

        return $value !== false ? (string) $value : $default;
    } catch (Throwable) {
        return $default;
    }
}

function current_language(): string
{
    $allowed = ['en', 'ta'];

    if (isset($_GET['lang']) && in_array($_GET['lang'], $allowed, true)) {
        $_SESSION['language'] = $_GET['lang'];
    }

    return $_SESSION['language'] ?? 'en';
}

function t(string $key): string
{
    $translations = [
        'en' => [
            'home' => 'Home',
            'complaints' => 'Complaints',
            'new_complaint' => 'New Complaint',
            'complaint_status' => 'Complaint Status',
            'escalation_period' => 'Escalation Period',
            'contacts' => 'Contacts',
            'department_contacts' => 'Department Contacts',
            'grievance_contacts' => 'Grievance Redressal Contacts',
            'site_manager' => 'Site Manager',
            'dashboard' => 'Dashboard',
            'feedback' => 'Feedback',
            'login' => 'Employee Login',
        ],
        'ta' => [
            'home' => 'முகப்பு',
            'complaints' => 'புகார்கள்',
            'new_complaint' => 'புதிய புகார்',
            'complaint_status' => 'புகார் நிலை',
            'escalation_period' => 'தீர்வு காலம்',
            'contacts' => 'தொடர்புகள்',
            'department_contacts' => 'துறை தொடர்புகள்',
            'grievance_contacts' => 'குறைதீர்ப்பு தொடர்புகள்',
            'site_manager' => 'தள மேலாளர்',
            'dashboard' => 'தகவல் பலகை',
            'feedback' => 'கருத்து',
            'login' => 'பணியாளர் உள்நுழைவு',
        ],
    ];

    $language = current_language();

    return $translations[$language][$key] ?? $translations['en'][$key] ?? $key;
}

function resolve_zone_id_for_ward(int $wardId): ?int
{
    if ($wardId <= 0) {
        return null;
    }

    $stmt = db()->prepare('SELECT zone_id FROM wards WHERE id = ? LIMIT 1');
    $stmt->execute([$wardId]);
    $zoneId = $stmt->fetchColumn();

    return $zoneId !== false ? (int) $zoneId : null;
}

function get_assigned_officer_for_complaint(int $complaintId): ?array
{
    $stmt = db()->prepare(
        'SELECT o.id, o.officer_name, o.designation, o.mobile_no, o.email, c.due_at
         FROM complaints c
         JOIN officers o ON o.id = c.assigned_officer_id
         WHERE c.id = ? LIMIT 1'
    );
    $stmt->execute([$complaintId]);
    $row = $stmt->fetch();

    return $row ?: null;
}

function assign_officer_for_complaint(
    int $complaintId,
    int $departmentId,
    int $categoryId,
    int $wardId,
    ?int $zoneId = null
): ?int {
    $pdo = db();

    if ($zoneId === null || $zoneId <= 0) {
        $zoneId = resolve_zone_id_for_ward($wardId);
    }

    $officerId = null;
    $officerName = null;
    $customSlaDays = null;
    $allocationSource = 'System auto-allocation';

    // Tier 1: Exact Department Allocation rule (Department + Category + Zone + Ward + Stage 1)
    if ($zoneId !== null && $zoneId > 0) {
        try {
            $allocStmt = $pdo->prepare(
                'SELECT da.officer_id, da.escalation_days, o.officer_name
                 FROM department_allocations da
                 JOIN officers o ON o.id = da.officer_id AND o.is_active = 1
                 WHERE da.department_id = ?
                   AND da.category_id = ?
                   AND da.zone_id = ?
                   AND da.ward_id = ?
                   AND da.stage_no = 1
                   AND da.is_active = 1
                 LIMIT 1'
            );
            $allocStmt->execute([$departmentId, $categoryId, $zoneId, $wardId]);
            $allocRow = $allocStmt->fetch();
            if ($allocRow) {
                $officerId = (int) $allocRow['officer_id'];
                $officerName = (string) $allocRow['officer_name'];
                $customSlaDays = (int) $allocRow['escalation_days'];
                $allocationSource = 'Department Allocation rule (Stage 1)';
            }
        } catch (Throwable $exception) {
            log_message('department_allocations lookup skipped: ' . $exception->getMessage());
        }
    }

    // Fallback tiers: prefer ward-specific field officers over city-wide (all-60-ward) officers
    if ($officerId === null) {
        $queries = [
            [
                'Ward + Department match (most specific officer)',
                'SELECT o.id, o.officer_name
                 FROM officers o
                 JOIN officer_department_map odm ON odm.officer_id = o.id
                 JOIN officer_ward_map owm ON owm.officer_id = o.id
                 LEFT JOIN officer_category_map ocm ON ocm.officer_id = o.id AND ocm.category_id = ?
                 WHERE odm.department_id = ? AND owm.ward_id = ? AND o.is_active = 1
                 ORDER BY
                   (SELECT COUNT(*) FROM officer_ward_map w2 WHERE w2.officer_id = o.id) ASC,
                   (ocm.category_id IS NOT NULL) DESC,
                   o.id ASC
                 LIMIT 1',
                [$categoryId, $departmentId, $wardId],
            ],
            [
                'Category + Ward match',
                'SELECT o.id, o.officer_name
                 FROM officers o
                 JOIN officer_category_map ocm ON ocm.officer_id = o.id
                 JOIN officer_ward_map owm ON owm.officer_id = o.id
                 WHERE ocm.category_id = ? AND owm.ward_id = ? AND o.is_active = 1
                 ORDER BY (SELECT COUNT(*) FROM officer_ward_map w2 WHERE w2.officer_id = o.id) ASC, o.id ASC
                 LIMIT 1',
                [$categoryId, $wardId],
            ],
            [
                'Escalation Level 1 officer',
                'SELECT o.id, o.officer_name, eol.escalation_period_days AS sla_days
                 FROM escalation_officer_levels eol
                 JOIN officers o ON o.id = eol.officer_id AND o.is_active = 1
                 WHERE eol.category_id = ? AND eol.level_no = 1 AND eol.is_active = 1
                 LIMIT 1',
                [$categoryId],
            ],
            [
                'Category match',
                'SELECT o.id, o.officer_name
                 FROM officers o
                 JOIN officer_category_map ocm ON ocm.officer_id = o.id
                 WHERE ocm.category_id = ? AND o.is_active = 1
                 ORDER BY (SELECT COUNT(*) FROM officer_ward_map w2 WHERE w2.officer_id = o.id) ASC, o.id ASC
                 LIMIT 1',
                [$categoryId],
            ],
            [
                'Department match',
                'SELECT o.id, o.officer_name
                 FROM officers o
                 JOIN officer_department_map odm ON odm.officer_id = o.id
                 WHERE odm.department_id = ? AND o.is_active = 1
                 ORDER BY (SELECT COUNT(*) FROM officer_ward_map w2 WHERE w2.officer_id = o.id) ASC, o.id ASC
                 LIMIT 1',
                [$departmentId],
            ],
        ];

        foreach ($queries as [$sourceLabel, $sql, $params]) {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $row = $stmt->fetch();

            if ($row) {
                $officerId = (int) $row['id'];
                $officerName = (string) $row['officer_name'];
                $allocationSource = $sourceLabel;
                if (isset($row['sla_days']) && (int) $row['sla_days'] > 0) {
                    $customSlaDays = (int) $row['sla_days'];
                }
                break;
            }
        }
    }

    if ($officerId === null) {
        return null;
    }

    // Ensure idempotent assignment row in complaint_assignments
    $pdo->prepare(
        'UPDATE complaint_assignments SET is_current = 0, unassigned_at = NOW()
         WHERE complaint_id = ? AND is_current = 1'
    )->execute([$complaintId]);

    $pdo->prepare(
        'INSERT INTO complaint_assignments
         (complaint_id, officer_id, assigned_by, assigned_at, is_current)
         VALUES (?, ?, NULL, NOW(), 1)'
    )->execute([$complaintId, $officerId]);

    if ($customSlaDays !== null && $customSlaDays > 0) {
        $dueDate = date('Y-m-d H:i:s', strtotime('+' . $customSlaDays . ' days'));
        $pdo->prepare(
            "UPDATE complaints SET assigned_officer_id = ?, status = 'Assigned', due_at = ? WHERE id = ?"
        )->execute([$officerId, $dueDate, $complaintId]);
    } else {
        $pdo->prepare(
            "UPDATE complaints SET assigned_officer_id = ?, status = 'Assigned' WHERE id = ?"
        )->execute([$officerId, $complaintId]);
    }

    $remarks = sprintf(
        'Automatically allocated to %s (%s).',
        $officerName ?: ('Officer #' . $officerId),
        $allocationSource
    );

    $pdo->prepare(
        'INSERT INTO complaint_status_history
         (complaint_id, old_status, new_status, remarks, changed_by)
         VALUES (?, ?, ?, ?, NULL)'
    )->execute([$complaintId, 'Registered', 'Assigned', $remarks]);

    return $officerId;
}
