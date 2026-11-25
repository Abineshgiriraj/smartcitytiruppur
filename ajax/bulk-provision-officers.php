<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
}

try {
    $user = current_user();
    if (!$user || $user['role'] !== 'Super Admin') {
        if (!headers_sent()) http_response_code(403);
        echo json_encode(['ok' => false, 'message' => 'Access denied. Super Administrator privileges required.']);
        exit;
    }
    if (!can_access_permission($user, 'users.manage')) {
        if (!headers_sent()) http_response_code(403);
        echo json_encode(['ok' => false, 'message' => 'Access denied. You lack users.manage permission.']);
        exit;
    }

    $action = trim($_POST['action'] ?? ($_GET['action'] ?? ''));

    if ($action === 'preview') {
        // Read-only preflight analysis strictly scoped to verified department contacts
        $totalActive = (int)db()->query("
            SELECT COUNT(DISTINCT o.id) 
            FROM officers o 
            JOIN officer_department_map odm ON odm.officer_id = o.id 
            WHERE o.is_active = 1
        ")->fetchColumn();

        $alreadyLinked = (int)db()->query("
            SELECT COUNT(DISTINCT o.id) 
            FROM officers o 
            JOIN officer_department_map odm ON odm.officer_id = o.id 
            WHERE o.is_active = 1 AND o.user_id IS NOT NULL
        ")->fetchColumn();

        $unlinkedTotal = (int)db()->query("
            SELECT COUNT(DISTINCT o.id) 
            FROM officers o 
            JOIN officer_department_map odm ON odm.officer_id = o.id 
            WHERE o.is_active = 1 AND o.user_id IS NULL
        ")->fetchColumn();

        // Duplicate numbers in verified officers
        $dupePhones = db()->query("
            SELECT o.mobile_no, COUNT(DISTINCT o.id) as cnt, GROUP_CONCAT(DISTINCT o.id) as ids, GROUP_CONCAT(DISTINCT o.officer_name) as names
            FROM officers o 
            JOIN officer_department_map odm ON odm.officer_id = o.id
            WHERE o.is_active = 1 AND o.mobile_no IS NOT NULL AND o.mobile_no <> ''
            GROUP BY o.mobile_no HAVING COUNT(DISTINCT o.id) > 1
        ")->fetchAll(PDO::FETCH_ASSOC);
        $dupePhoneMap = [];
        $dupeDetails = [];
        foreach ($dupePhones as $dp) {
            $dupePhoneMap[$dp['mobile_no']] = true;
            $dupeDetails[$dp['mobile_no']] = [
                'count' => (int)$dp['cnt'],
                'ids' => $dp['ids'],
                'names' => $dp['names']
            ];
        }

        // Mobiles already registered in users
        $existingPhones = db()->query("SELECT mobile_no FROM users WHERE mobile_no IS NOT NULL AND mobile_no <> ''")->fetchAll(PDO::FETCH_COLUMN);
        $claimedMap = array_fill_keys(array_map('normalize_mobile', $existingPhones), true);

        // Fetch unlinked verified officers
        $unlinkedRows = db()->query("
            SELECT o.id, o.officer_name, o.designation, o.mobile_no 
            FROM officers o 
            JOIN officer_department_map odm ON odm.officer_id = o.id
            WHERE o.is_active = 1 AND o.user_id IS NULL 
            GROUP BY o.id
            ORDER BY o.id ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $eligibleCount = 0;
        $skippedList = [];

        foreach ($unlinkedRows as $row) {
            $rawMobile = (string)$row['mobile_no'];
            $clean = normalize_mobile($rawMobile);

            if (!preg_match('/^[6-9][0-9]{9}$/D', $clean)) {
                $skippedList[] = [
                    'id' => (int)$row['id'],
                    'name' => $row['officer_name'],
                    'designation' => $row['designation'],
                    'mobile' => $rawMobile ?: '(Empty)',
                    'reason' => 'Invalid or missing 10-digit mobile number'
                ];
            } elseif (isset($dupePhoneMap[$clean])) {
                $skippedList[] = [
                    'id' => (int)$row['id'],
                    'name' => $row['officer_name'],
                    'designation' => $row['designation'],
                    'mobile' => $clean,
                    'reason' => "Ambiguous: Number shared by {$dupeDetails[$clean]['count']} officers (IDs: {$dupeDetails[$clean]['ids']})"
                ];
            } elseif (isset($claimedMap[$clean])) {
                $skippedList[] = [
                    'id' => (int)$row['id'],
                    'name' => $row['officer_name'],
                    'designation' => $row['designation'],
                    'mobile' => $clean,
                    'reason' => 'Mobile number already registered to another user account'
                ];
            } else {
                $eligibleCount++;
            }
        }

        echo json_encode([
            'ok' => true,
            'total_active' => $totalActive,
            'already_linked' => $alreadyLinked,
            'unlinked_total' => $unlinkedTotal,
            'eligible_count' => $eligibleCount,
            'skipped_count' => count($skippedList),
            'skipped_list' => array_slice($skippedList, 0, 50),
            'total_skipped_records' => count($skippedList)
        ]);
        exit;
    }

    if ($action === 'process_batch') {
        verify_csrf();

        $batchSize = max(50, min(300, (int)($_POST['batch_size'] ?? 150)));

        // 1. Get ambiguous duplicate mobiles in verified officers
        $dupePhones = db()->query("
            SELECT o.mobile_no, COUNT(DISTINCT o.id) as cnt
            FROM officers o 
            JOIN officer_department_map odm ON odm.officer_id = o.id
            WHERE o.is_active = 1 AND o.mobile_no IS NOT NULL AND o.mobile_no <> ''
            GROUP BY o.mobile_no HAVING COUNT(DISTINCT o.id) > 1
        ")->fetchAll(PDO::FETCH_KEY_PAIR);

        // 2. Get claimed mobiles in users
        $existingPhones = db()->query("SELECT mobile_no FROM users WHERE mobile_no IS NOT NULL AND mobile_no <> ''")->fetchAll(PDO::FETCH_COLUMN);
        $claimedMap = array_fill_keys(array_map('normalize_mobile', $existingPhones), true);

        // 3. Candidate pool strictly from verified department contacts
        $candidateStmt = db()->prepare("
            SELECT o.id, o.officer_name, o.designation, o.mobile_no, o.email, dm.department_id
            FROM officers o
            JOIN officer_department_map dm ON dm.officer_id = o.id
            WHERE o.is_active = 1 AND o.user_id IS NULL
            GROUP BY o.id
            ORDER BY o.id ASC
            LIMIT :lim
        ");
        $candidateStmt->bindValue(':lim', $batchSize * 3, PDO::PARAM_INT);
        $candidateStmt->execute();
        $candidates = $candidateStmt->fetchAll(PDO::FETCH_ASSOC);

        $created = 0;
        $skipped = 0;
        $batchSkippedDetails = [];

        foreach ($candidates as $cand) {
            if ($created >= $batchSize) {
                break;
            }

            $officerId = (int)$cand['id'];
            $rawMobile = (string)$cand['mobile_no'];
            $cleanMobile = normalize_mobile($rawMobile);

            if (!preg_match('/^[6-9][0-9]{9}$/D', $cleanMobile)) {
                $skipped++;
                $batchSkippedDetails[] = [
                    'id' => $officerId,
                    'name' => $cand['officer_name'],
                    'mobile' => $rawMobile ?: '(Empty)',
                    'reason' => 'Invalid mobile format'
                ];
                continue;
            }

            if (isset($dupePhones[$cleanMobile])) {
                $skipped++;
                $batchSkippedDetails[] = [
                    'id' => $officerId,
                    'name' => $cand['officer_name'],
                    'mobile' => $cleanMobile,
                    'reason' => 'Ambiguous duplicate phone number across multiple officers'
                ];
                continue;
            }

            if (isset($claimedMap[$cleanMobile])) {
                // If user exists and is Field Officer, link automatically
                $findUser = db()->prepare('SELECT id, role FROM users WHERE mobile_no = ? LIMIT 1');
                $findUser->execute([$cleanMobile]);
                $existingUser = $findUser->fetch(PDO::FETCH_ASSOC);

                if ($existingUser && in_array($existingUser['role'], ['Field Officer', 'Officer'], true)) {
                    $isLinked = db()->query("SELECT user_id FROM officers WHERE id = {$officerId}")->fetchColumn();
                    if ($isLinked === null) {
                        db()->prepare('UPDATE officers SET user_id = ? WHERE id = ? AND user_id IS NULL')
                            ->execute([(int)$existingUser['id'], $officerId]);
                        $created++;
                        continue;
                    }
                }

                $skipped++;
                $batchSkippedDetails[] = [
                    'id' => $officerId,
                    'name' => $cand['officer_name'],
                    'mobile' => $cleanMobile,
                    'reason' => 'Mobile number already claimed by another user'
                ];
                continue;
            }

            // Create new phone-only user
            try {
                db()->beginTransaction();

                $chk = db()->prepare('SELECT user_id FROM officers WHERE id = ? AND is_active = 1 FOR UPDATE');
                $chk->execute([$officerId]);
                $currUser = $chk->fetchColumn();
                if ($currUser !== null && $currUser !== false) {
                    db()->rollBack();
                    $skipped++;
                    continue;
                }

                $randomPassword = password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT);
                $officerEmail = filter_var((string)$cand['email'], FILTER_VALIDATE_EMAIL) ? strtolower((string)$cand['email']) : null;

                if ($officerEmail !== null) {
                    $chkEmail = db()->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
                    $chkEmail->execute([$officerEmail]);
                    if ($chkEmail->fetchColumn()) {
                        $officerEmail = null;
                    }
                }

                $insertUser = db()->prepare('
                    INSERT INTO users (full_name, email, mobile_no, password_hash, role, department_id, is_active, created_at, updated_at)
                    VALUES (?, ?, ?, ?, "Field Officer", ?, 1, NOW(), NOW())
                ');
                $insertUser->execute([
                    $cand['officer_name'],
                    $officerEmail,
                    $cleanMobile,
                    $randomPassword,
                    $cand['department_id'] ? (int)$cand['department_id'] : null
                ]);
                $newUserId = (int)db()->lastInsertId();

                $linkOfficer = db()->prepare('UPDATE officers SET user_id = ? WHERE id = ? AND user_id IS NULL');
                $linkOfficer->execute([$newUserId, $officerId]);

                if ($linkOfficer->rowCount() !== 1) {
                    db()->rollBack();
                    $skipped++;
                    continue;
                }

                db()->commit();
                $claimedMap[$cleanMobile] = true;
                $created++;
            } catch (Throwable $e) {
                if (db()->inTransaction()) {
                    db()->rollBack();
                }
                $skipped++;
                log_message("Error batch provisioning officer #{$officerId}: " . $e->getMessage());
            }
        }

        // Remaining unlinked count strictly among verified department contacts
        $remainingEligible = (int)db()->query("
            SELECT COUNT(DISTINCT o.id) 
            FROM officers o 
            JOIN officer_department_map odm ON odm.officer_id = o.id
            WHERE o.is_active = 1 AND o.user_id IS NULL 
              AND o.mobile_no REGEXP '^[6-9][0-9]{9}$'
        ")->fetchColumn();

        $totalLinked = (int)db()->query("
            SELECT COUNT(DISTINCT o.id) 
            FROM officers o 
            JOIN officer_department_map odm ON odm.officer_id = o.id
            WHERE o.is_active = 1 AND o.user_id IS NOT NULL
        ")->fetchColumn();
        $isFinished = ($created === 0 && $remainingEligible === 0) || empty($candidates);

        echo json_encode([
            'ok' => true,
            'created' => $created,
            'skipped' => $skipped,
            'remaining_eligible' => $remainingEligible,
            'total_linked' => $totalLinked,
            'is_finished' => $isFinished,
            'skipped_details' => $batchSkippedDetails
        ]);
        exit;
    }

    if ($action === 'skipped_report') {
        // Generate full report of verified officers that could not be provisioned
        $dupePhones = db()->query("
            SELECT o.mobile_no, COUNT(DISTINCT o.id) as cnt, GROUP_CONCAT(DISTINCT o.id) as ids
            FROM officers o 
            JOIN officer_department_map odm ON odm.officer_id = o.id
            WHERE o.is_active = 1 AND o.mobile_no IS NOT NULL AND o.mobile_no <> ''
            GROUP BY o.mobile_no HAVING COUNT(DISTINCT o.id) > 1
        ")->fetchAll(PDO::FETCH_ASSOC);
        $dupePhoneMap = [];
        foreach ($dupePhones as $dp) {
            $dupePhoneMap[$dp['mobile_no']] = $dp['ids'];
        }

        $existingPhones = db()->query("SELECT mobile_no FROM users WHERE mobile_no IS NOT NULL AND mobile_no <> ''")->fetchAll(PDO::FETCH_COLUMN);
        $claimedMap = array_fill_keys(array_map('normalize_mobile', $existingPhones), true);

        $unlinked = db()->query("
            SELECT o.id, o.officer_name, o.designation, o.mobile_no, d.department_name
            FROM officers o
            JOIN officer_department_map dm ON dm.officer_id = o.id
            LEFT JOIN departments d ON d.id = dm.department_id
            WHERE o.is_active = 1 AND o.user_id IS NULL
            GROUP BY o.id
            ORDER BY o.id ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $report = [];
        foreach ($unlinked as $row) {
            $raw = (string)$row['mobile_no'];
            $clean = normalize_mobile($raw);
            $reason = 'Other';

            if (!preg_match('/^[6-9][0-9]{9}$/D', $clean)) {
                $reason = 'Invalid or missing 10-digit mobile number';
            } elseif (isset($dupePhoneMap[$clean])) {
                $reason = "Duplicate phone shared with officers #{$dupePhoneMap[$clean]}";
            } elseif (isset($claimedMap[$clean])) {
                $reason = 'Mobile number already registered to another user';
            }

            $report[] = [
                'id' => (int)$row['id'],
                'name' => $row['officer_name'],
                'designation' => $row['designation'],
                'department' => $row['department_name'] ?: 'None',
                'mobile' => $raw ?: '(Empty)',
                'reason' => $reason
            ];
        }

        echo json_encode([
            'ok' => true,
            'total_unprovisioned' => count($report),
            'report' => $report
        ]);
        exit;
    }

    echo json_encode(['ok' => false, 'message' => 'Invalid action specified.']);
} catch (Throwable $e) {
    if (!headers_sent()) {
        http_response_code(400);
    }
    echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
}
