<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

try {
    verify_csrf();

    $mobile = normalize_mobile($_POST['mobile'] ?? '');
    $purpose = trim($_POST['purpose'] ?? 'complaint');
    $allowedPurposes = ['complaint', 'tracking', 'login'];

    if (!preg_match('/^[6-9]\d{9}$/', $mobile) || !in_array($purpose, $allowedPurposes, true)) {
        echo json_encode(['ok' => false, 'message' => 'Invalid mobile number or OTP purpose.']);
        exit;
    }

    if ($purpose === 'login') {
        $userStmt = db()->prepare('SELECT id, is_active FROM users WHERE mobile_no = ? LIMIT 2');
        $userStmt->execute([$mobile]);
        $accounts = $userStmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($accounts) > 1) {
            echo json_encode(['ok'=>false,'message'=>'Duplicate employee mobile number. Contact the administrator.']);
            exit;
        }
        $userRecord = $accounts[0] ?? null;
        if (!$userRecord) {
            echo json_encode(['ok' => false, 'message' => 'No employee account found with this mobile number.']);
            exit;
        }
        if (!(int)$userRecord['is_active']) {
            echo json_encode(['ok' => false, 'message' => 'This account is currently disabled. Please contact the administrator.']);
            exit;
        }
    }

    $rateStmt = db()->prepare(
        'SELECT created_at FROM otp_requests
         WHERE mobile_no = ? AND purpose = ?
         ORDER BY id DESC LIMIT 1'
    );
    $rateStmt->execute([$mobile, $purpose]);
    $lastCreated = $rateStmt->fetchColumn();

    if ($lastCreated && strtotime((string)$lastCreated) > time() - OTP_RESEND_SECONDS) {
        echo json_encode([
            'ok' => false,
            'message' => 'Please wait before requesting another OTP.',
        ]);
        exit;
    }

    // No SMS provider is integrated here. Do not claim successful delivery on live.
    // Integrate an approved SMS API before enabling production OTP sign-ins.
    if ($purpose === 'login' && APP_ENV !== 'local') {
        echo json_encode(['ok'=>false,
            'message'=>'Mobile OTP delivery has not been configured on this server. Contact the administrator.']);
        exit;
    }

    $otp = (string)random_int(100000, 999999);
    $hash = password_hash($otp, PASSWORD_DEFAULT);
    $expiresAt = date('Y-m-d H:i:s', time() + OTP_TTL_SECONDS);

    $stmt = db()->prepare(
        'INSERT INTO otp_requests
         (mobile_no, purpose, otp_hash, expires_at, verified_at, attempt_count, created_at)
         VALUES (?, ?, ?, ?, NULL, 0, NOW())'
    );
    $stmt->execute([$mobile, $purpose, $hash, $expiresAt]);

    $_SESSION['latest_otp_request'][$purpose] = (int)db()->lastInsertId();

    // Integrate an approved SMS provider here on the live server.
    $response = [
        'ok' => true,
        'message' => APP_ENV === 'local'
            ? 'Local testing OTP generated for ' . mask_mobile($mobile) . '.'
            : 'OTP request created (SMS delivery requires a configured gateway).',
    ];

    if (APP_ENV === 'local') {
        $response['dev_otp'] = $otp;
        $response['message'] .= ' Local development OTP: ' . $otp;
    }

    echo json_encode($response);
} catch (Throwable $exception) {
    log_message('Send OTP failed: ' . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Unable to send OTP.']);
}
