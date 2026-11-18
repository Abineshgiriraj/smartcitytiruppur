<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

try {
    verify_csrf();

    $mobile = normalize_mobile($_POST['mobile'] ?? '');
    $otp = trim($_POST['otp'] ?? '');
    $purpose = trim($_POST['purpose'] ?? 'complaint');
    $requestId = (int)($_SESSION['latest_otp_request'][$purpose] ?? 0);

    if (!preg_match('/^[6-9]\d{9}$/', $mobile) || !preg_match('/^\d{6}$/', $otp) || $requestId <= 0) {
        echo json_encode(['ok' => false, 'message' => 'OTP details are invalid.']);
        exit;
    }

    $stmt = db()->prepare(
        'SELECT id, otp_hash, expires_at, attempt_count, verified_at
         FROM otp_requests
         WHERE id = ? AND mobile_no = ? AND purpose = ?
         LIMIT 1'
    );
    $stmt->execute([$requestId, $mobile, $purpose]);
    $record = $stmt->fetch();

    if (!$record || $record['verified_at']) {
        echo json_encode(['ok' => false, 'message' => 'OTP request is invalid or already used.']);
        exit;
    }

    if ((int)$record['attempt_count'] >= 5) {
        echo json_encode(['ok' => false, 'message' => 'Too many incorrect OTP attempts. Request a new OTP.']);
        exit;
    }

    if (strtotime($record['expires_at']) < time()) {
        echo json_encode(['ok' => false, 'message' => 'OTP has expired. Request a new OTP.']);
        exit;
    }

    db()->prepare('UPDATE otp_requests SET attempt_count = attempt_count + 1 WHERE id = ?')
        ->execute([(int)$record['id']]);

    if (!password_verify($otp, $record['otp_hash'])) {
        echo json_encode(['ok' => false, 'message' => 'OTP is incorrect.']);
        exit;
    }

    db()->prepare('UPDATE otp_requests SET verified_at = NOW() WHERE id = ?')
        ->execute([(int)$record['id']]);

    $_SESSION['otp_verified_mobile'][$purpose] = $mobile;

    echo json_encode(['ok' => true, 'message' => 'Mobile number verified successfully.']);
} catch (Throwable $exception) {
    log_message('Verify OTP failed: ' . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Unable to verify OTP.']);
}
