<?php
declare(strict_types=1);
require_once __DIR__.'/officer-authority.php';

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

    // Do not route across an invalid category/department or ward/zone pairing.
    if (!valid_complaint_routing_context($pdo,$departmentId,$categoryId,(int)$zoneId,$wardId)) {
        log_message("Initial assignment blocked: invalid routing context for complaint {$complaintId}");
        return null;
    }

    // Registration must use the PDF's FIRST STAGE period, not the total
    // category resolution period or an old seeded department default.
    $policy=pdf_policy_reviewed($pdo,$categoryId);
    $firstStage=$policy===null?null:pdf_policy_stage($policy,1);
    if(!$firstStage || (int)$firstStage['period_days']<=0) {
        log_message("Initial allocation blocked: unapproved PDF Stage 1 policy for complaint {$complaintId}");
        return null;
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

    // An explicit exact rule cannot be silently bypassed if it points to
    // an unapproved officer. Keep the complaint unassigned for review.
    if ($officerId !== null && !officer_stage_authorized(
        $pdo, $categoryId, 1, $officerId, $departmentId, $wardId
    )) {
        log_message("Exact Stage 1 allocation denied for complaint {$complaintId}: unapproved officer {$officerId}");
        return null;
    }

    // Search verified Stage 1 officers FIRST, rather than truncating
    // unverified legacy candidate lists at 250 rows. Rank officers assigned
    // to the complaint's ward ahead of verified city-wide officers.
    if ($officerId === null) {
        try {
            $approvedStmt = $pdo->prepare(
                "SELECT o.id, o.officer_name,
                        CASE
                            WHEN EXISTS (SELECT 1 FROM officer_ward_map w
                                         WHERE w.officer_id=o.id AND w.ward_id=?) THEN 0
                            WHEN NOT EXISTS (SELECT 1 FROM officer_ward_map w
                                             WHERE w.officer_id=o.id) THEN 1
                            ELSE 2
                        END AS ward_priority,
                        (SELECT COUNT(*) FROM officer_ward_map w
                         WHERE w.officer_id=o.id) AS ward_count
                 FROM officer_stage_approvals a
                 JOIN officers o ON o.id=a.officer_id AND o.is_active=1
                 WHERE a.category_id=? AND a.stage_no=1
                   AND a.revoked_at IS NULL AND a.approved_by IS NOT NULL
                   AND TRIM(a.approval_reference) <> ''
                 ORDER BY ward_priority, ward_count, o.id"
            );
            $approvedStmt->execute([$wardId, $categoryId]);
            foreach ($approvedStmt->fetchAll(PDO::FETCH_ASSOC) as $candidate) {
                $candidateId = (int)$candidate['id'];
                if (!officer_stage_authorized(
                    $pdo, $categoryId, 1, $candidateId, $departmentId, $wardId
                )) {
                    continue;
                }
                $officerId = $candidateId;
                $officerName = (string)$candidate['officer_name'];
                $allocationSource = 'Verified Stage 1 officer (approved routing)';
                break;
            }
        } catch (Throwable $exception) {
            log_message(
                "Approved Stage 1 allocation registry unavailable for complaint {$complaintId}: "
                . $exception->getMessage()
            );
            return null;
        }
    }

    if ($officerId === null) {
        log_message("Initial assignment pending: no approved mapped officer for complaint {$complaintId}");
        return null;
    }
    // Old seed maps 286 categories to a Commissioner as Stage 1.
    // A candidate must be explicitly approved, not merely active or mapped.
    if (!officer_stage_authorized($pdo,$categoryId,1,$officerId,$departmentId,$wardId)) {
        log_message("Initial assignment blocked: unapproved Stage 1 officer {$officerId} for complaint {$complaintId}");
        return null;
    }

    // The officer's actual designation must satisfy the exact PDF role.
    $titleLookup=$pdo->prepare('SELECT designation FROM officers WHERE id=? LIMIT 1');
    $titleLookup->execute([$officerId]);
    $designation=$titleLookup->fetchColumn();
    if($designation===false || !pdf_policy_role_matches((string)$firstStage['role_label'],(string)$designation)) {
        log_message("Initial officer role mismatch for complaint {$complaintId}; PDF requires " . $firstStage['role_label']);
        return null;
    }
    // An exact ward-rule SLA that disagrees with the reviewed PDF must be
    // explicitly reconciled; do not silently override it.
    if($customSlaDays!==null && $customSlaDays !== (int)$firstStage['period_days']) {
        log_message("Exact allocation SLA conflicts with PDF Stage 1 for complaint {$complaintId}");
        return null;
    }
    $customSlaDays=(int)$firstStage['period_days'];

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
