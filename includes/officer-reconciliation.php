<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

function get_officer_phone_audit_data(PDO $pdo): array
{
    // 1. All active verified officers from department-contacts
    $activeOfficersQuery = "
        SELECT o.id as officer_id, o.officer_name, o.designation, o.mobile_no as officer_phone,
               o.user_id, u.id as matched_user_id, u.full_name as user_name, u.mobile_no as user_phone,
               u.role as user_role, u.is_active as user_active,
               GROUP_CONCAT(DISTINCT d.department_name ORDER BY d.department_name SEPARATOR ', ') as departments,
               (SELECT COUNT(*) FROM complaints c WHERE c.assigned_officer_id = o.id AND c.is_deleted = 0) as assigned_complaints
        FROM officers o
        JOIN officer_department_map odm ON odm.officer_id = o.id
        JOIN departments d ON d.id = odm.department_id
        LEFT JOIN users u ON u.id = o.user_id
        WHERE o.is_active = 1
        GROUP BY o.id
        ORDER BY o.id ASC
    ";
    $activeOfficers = $pdo->query($activeOfficersQuery)->fetchAll(PDO::FETCH_ASSOC);

    // 2. Identify duplicate phones in active officers
    $phoneCounts = [];
    foreach ($activeOfficers as $ao) {
        $ph = trim((string)$ao['officer_phone']);
        if ($ph !== '') {
            $phoneCounts[$ph] = ($phoneCounts[$ph] ?? 0) + 1;
        }
    }

    $matched = [];
    $phoneMismatches = [];
    $unlinkedOfficers = [];
    $inactiveLogins = [];
    $nameInconsistencies = [];
    $duplicatePhones = [];

    foreach ($activeOfficers as $ao) {
        $ph = trim((string)$ao['officer_phone']);
        $uph = trim((string)($ao['user_phone'] ?? ''));
        $hasUser = !empty($ao['matched_user_id']);
        $userActive = (int)($ao['user_active'] ?? 0) === 1;

        if (($phoneCounts[$ph] ?? 0) > 1) {
            $duplicatePhones[] = $ao;
        }

        if (!$hasUser) {
            $unlinkedOfficers[] = $ao;
        } elseif (!$userActive) {
            $inactiveLogins[] = $ao;
        } elseif ($ph !== $uph) {
            $phoneMismatches[] = $ao;
        } else {
            $matched[] = $ao;
        }

        if ($hasUser && strcasecmp(trim($ao['officer_name']), trim((string)$ao['user_name'])) !== 0) {
            $nameInconsistencies[] = $ao;
        }
    }

    // 3. Unlinked active users with Field Officer role
    $unlinkedUsers = $pdo->query("
        SELECT u.id as user_id, u.full_name, u.mobile_no, u.email, u.role
        FROM users u
        WHERE u.is_active = 1
          AND u.role IN ('Field Officer', 'Officer')
          AND NOT EXISTS (
              SELECT 1 FROM officers o WHERE o.user_id = u.id AND o.is_active = 1
          )
        ORDER BY u.id ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    return [
        'total_active_officers' => count($activeOfficers),
        'matched_count' => count($matched),
        'phone_mismatches_count' => count($phoneMismatches),
        'unlinked_officers_count' => count($unlinkedOfficers),
        'inactive_logins_count' => count($inactiveLogins),
        'name_inconsistencies_count' => count($nameInconsistencies),
        'duplicate_phones_count' => count($duplicatePhones),
        'unlinked_users_count' => count($unlinkedUsers),
        'active_officers' => $activeOfficers,
        'matched' => $matched,
        'phone_mismatches' => $phoneMismatches,
        'unlinked_officers' => $unlinkedOfficers,
        'inactive_logins' => $inactiveLogins,
        'name_inconsistencies' => $nameInconsistencies,
        'duplicate_phones' => $duplicatePhones,
        'unlinked_users' => $unlinkedUsers,
    ];
}

/**
 * Validates and applies a phone number correction with safety checks.
 */
function update_officer_phone_verified(PDO $pdo, int $officerId, string $newMobile, int $adminUserId, ?string $reason = null): array
{
    $cleanMobile = normalize_mobile($newMobile);
    if (!preg_match('/^[6-9][0-9]{9}$/', $cleanMobile)) {
        return ['ok' => false, 'message' => 'Please enter a valid 10-digit Indian mobile number starting with 6-9.'];
    }

    $officer = $pdo->query("SELECT * FROM officers WHERE id = {$officerId} AND is_active = 1")->fetch(PDO::FETCH_ASSOC);
    if (!$officer) {
        return ['ok' => false, 'message' => "Officer #{$officerId} not found or inactive."];
    }

    $userId = (int)($officer['user_id'] ?? 0);

    // Conflict check 1: Is this phone used by another officer?
    $stmtOtherOff = $pdo->prepare("SELECT id, officer_name FROM officers WHERE mobile_no = ? AND id <> ? AND is_active = 1 LIMIT 1");
    $stmtOtherOff->execute([$cleanMobile, $officerId]);
    $conflictOff = $stmtOtherOff->fetch(PDO::FETCH_ASSOC);
    if ($conflictOff) {
        return [
            'ok' => false,
            'message' => "Conflict: Mobile {$cleanMobile} is already assigned to active Officer #{$conflictOff['id']} ({$conflictOff['officer_name']}). Please review manually."
        ];
    }

    // Conflict check 2: Is this phone used by another user login?
    $stmtOtherUser = $pdo->prepare("SELECT id, full_name, role FROM users WHERE mobile_no = ? AND id <> ? AND is_active = 1 LIMIT 1");
    $stmtOtherUser->execute([$cleanMobile, $userId]);
    $conflictUser = $stmtOtherUser->fetch(PDO::FETCH_ASSOC);
    if ($conflictUser) {
        return [
            'ok' => false,
            'message' => "Conflict: Mobile {$cleanMobile} is already linked to User #{$conflictUser['id']} ({$conflictUser['full_name']} - {$conflictUser['role']}). Please review manually."
        ];
    }

    // Apply in small transaction
    $oldOfficerPhone = (string)$officer['mobile_no'];
    $pdo->beginTransaction();
    try {
        // Update officer
        $stmtUpdOff = $pdo->prepare("UPDATE officers SET mobile_no = ?, updated_at = NOW() WHERE id = ?");
        $stmtUpdOff->execute([$cleanMobile, $officerId]);

        // Update user if linked
        $oldUserPhone = null;
        if ($userId > 0) {
            $u = $pdo->query("SELECT mobile_no FROM users WHERE id = {$userId}")->fetch(PDO::FETCH_ASSOC);
            $oldUserPhone = $u['mobile_no'] ?? null;
            $stmtUpdUser = $pdo->prepare("UPDATE users SET mobile_no = ?, updated_at = NOW() WHERE id = ?");
            $stmtUpdUser->execute([$cleanMobile, $userId]);
        }

        // Audit log
        audit_log($adminUserId, 'officer_phone_update', 'officer', $officerId);

        $pdo->commit();

        return [
            'ok' => true,
            'message' => "Phone number for Officer #{$officerId} ({$officer['officer_name']}) successfully updated to {$cleanMobile}.",
            'officer_id' => $officerId,
            'user_id' => $userId,
            'old_phone' => $oldOfficerPhone,
            'new_phone' => $cleanMobile
        ];
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return ['ok' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}
