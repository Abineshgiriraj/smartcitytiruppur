<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

if (current_user()) {
    redirect('employee/dashboard.php');
}

$errors = [];
$activeTab = 'password';
$emailVal = '';
$mobileVal = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $loginType = trim($_POST['login_type'] ?? 'password');
    $activeTab = $loginType === 'otp' ? 'otp' : 'password';

    if ($loginType === 'otp') {
        $mobileVal = trim($_POST['mobile_no'] ?? '');
        $otp = trim($_POST['otp'] ?? '');

        $result = login_user_by_otp($mobileVal, $otp);
        if ($result['ok']) {
            flash('success', 'Logged in successfully.');
            $u = current_user();
            if ($u && in_array($u['role'], ['Field Officer', 'Officer'], true)) {
                redirect('employee/officer-dashboard.php');
            } else {
                redirect('employee/dashboard.php');
            }
        } else {
            $errors[] = $result['message'];
        }
    } else {
        $emailVal = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!login_user($emailVal, $password)) {
            $errors[] = 'Invalid email/mobile or password.';
        } else {
            flash('success', 'Logged in successfully.');
            $u = current_user();
            if ($u && in_array($u['role'], ['Field Officer', 'Officer'], true)) {
                redirect('employee/officer-dashboard.php');
            } else {
                redirect('employee/dashboard.php');
            }
        }
    }
}

$pageTitle = 'Employee Login';
include __DIR__ . '/../includes/header.php';
?>
<section class="page-banner">
    <div class="container text-center">
        <h1 class="fw-bold">Employee Portal Login</h1>
        <p class="mb-0 text-white-50">Authorized Tiruppur City Municipal Corporation Officers and Staff</p>
    </div>
</section>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-8">
            <div class="card content-card border-0 shadow-sm p-4 p-md-5">
                <div class="text-center mb-4">
                    <img src="<?php echo e(url('assets/images/trippurlogo.png')); ?>" alt="Tiruppur Corporation" style="height: 60px;" class="mb-2">
                    <h2 class="h4 fw-bold mb-1">Welcome Back</h2>
                    <p class="text-muted small">Choose your preferred login method</p>
                </div>

                <?php if ($errors): ?>
                    <div class="alert alert-danger py-2 mb-4" role="alert">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Login Option Tabs -->
                <ul class="nav nav-pills nav-fill mb-4 p-1 bg-light rounded-3" id="loginMethodTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-2 fw-semibold <?php echo $activeTab === 'password' ? 'active' : ''; ?>"
                                id="password-tab"
                                data-bs-toggle="pill"
                                data-bs-target="#password-pane"
                                type="button"
                                role="tab"
                                aria-controls="password-pane"
                                aria-selected="<?php echo $activeTab === 'password' ? 'true' : 'false'; ?>">
                            <i class="bi bi-key-fill me-1"></i> Email &amp; Password
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-2 fw-semibold <?php echo $activeTab === 'otp' ? 'active' : ''; ?>"
                                id="otp-tab"
                                data-bs-toggle="pill"
                                data-bs-target="#otp-pane"
                                type="button"
                                role="tab"
                                aria-controls="otp-pane"
                                aria-selected="<?php echo $activeTab === 'otp' ? 'true' : 'false'; ?>">
                            <i class="bi bi-phone-fill me-1"></i> Mobile &amp; OTP
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="loginMethodContent">
                    <!-- Option 1: Email and Password Login -->
                    <div class="tab-pane fade <?php echo $activeTab === 'password' ? 'show active' : ''; ?>"
                         id="password-pane"
                         role="tabpanel"
                         aria-labelledby="password-tab"
                         tabindex="0">
                        <form method="post" action="<?php echo e(url('employee/login.php')); ?>" id="passwordLoginForm">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="login_type" value="password">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email or Mobile Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-envelope text-muted"></i></span>
                                    <input class="form-control form-control-lg"
                                           type="text"
                                           name="email"
                                           value="<?php echo e($emailVal); ?>"
                                           placeholder="e.g. admin@smartcitytiruppur.gov.in"
                                           required
                                           autofocus>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-semibold mb-0">Password</label>
                                    <a href="<?php echo e(url('employee/forgot-password.php')); ?>" class="small text-decoration-none">Forgot password?</a>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-lock text-muted"></i></span>
                                    <input class="form-control form-control-lg"
                                           type="password"
                                           name="password"
                                           id="passwordInput"
                                           placeholder="••••••••"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordBtn" title="Toggle password visibility">
                                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <button class="btn btn-primary btn-lg w-100 fw-semibold mt-2" type="submit">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login with Password
                            </button>
                        </form>
                    </div>

                    <!-- Option 2: Mobile Number and OTP Login -->
                    <div class="tab-pane fade <?php echo $activeTab === 'otp' ? 'show active' : ''; ?>"
                         id="otp-pane"
                         role="tabpanel"
                         aria-labelledby="otp-tab"
                         tabindex="0">
                        <form method="post" action="<?php echo e(url('employee/login.php')); ?>" id="otpLoginForm">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="login_type" value="otp">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Registered Mobile Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white fw-semibold text-muted">+91</span>
                                    <input class="form-control form-control-lg"
                                           type="tel"
                                           name="mobile_no"
                                           id="loginMobileInput"
                                           value="<?php echo e($mobileVal); ?>"
                                           placeholder="Enter 10-digit mobile"
                                           maxlength="10"
                                           pattern="[6-9][0-9]{9}"
                                           required>
                                    <button class="btn btn-outline-primary fw-semibold px-3"
                                            type="button"
                                            id="btnSendLoginOtp">
                                        Send OTP
                                    </button>
                                </div>
                                <div class="form-text small">Enter the mobile number linked to your employee account.</div>
                            </div>

                            <!-- Live Feedback / OTP status box -->
                            <div id="otpFeedbackBox" class="alert d-none py-2 px-3 small mb-3"></div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Enter 6-Digit OTP</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-shield-lock text-muted"></i></span>
                                    <input class="form-control form-control-lg text-center fw-bold fs-4"
                                           type="text"
                                           name="otp"
                                           id="loginOtpInput"
                                           placeholder="• • • • • •"
                                           maxlength="6"
                                           pattern="[0-9]{6}"
                                           autocomplete="one-time-code"
                                           style="letter-spacing: 0.35rem;"
                                           required>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <span class="form-text small" id="otpTimerText"></span>
                                    <a href="javascript:void(0)" class="small text-decoration-none d-none" id="resendOtpLink">Resend OTP</a>
                                </div>
                            </div>

                            <button class="btn btn-success btn-lg w-100 fw-semibold" type="submit" id="btnLoginWithOtp">
                                <i class="bi bi-shield-check me-1"></i> Verify &amp; Login
                            </button>
                        </form>
                    </div>
                </div>

                <div class="border-top pt-3 mt-4 text-center">
                    <span class="text-muted small">Need help accessing your portal?</span><br>
                    <a href="<?php echo e(url('contact.php')); ?>" class="small text-decoration-none">Contact System Administrator</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Password Visibility Toggle
    const toggleBtn = document.getElementById('togglePasswordBtn');
    const pwdInput = document.getElementById('passwordInput');
    const pwdIcon = document.getElementById('togglePasswordIcon');
    if (toggleBtn && pwdInput) {
        toggleBtn.addEventListener('click', function () {
            const isPassword = pwdInput.type === 'password';
            pwdInput.type = isPassword ? 'text' : 'password';
            pwdIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    }

    // 2. Mobile OTP Sending and Countdown
    const sendBtn = document.getElementById('btnSendLoginOtp');
    const mobileInput = document.getElementById('loginMobileInput');
    const otpInput = document.getElementById('loginOtpInput');
    const feedbackBox = document.getElementById('otpFeedbackBox');
    const timerText = document.getElementById('otpTimerText');
    const resendLink = document.getElementById('resendOtpLink');
    let countdownInterval = null;

    function showFeedback(message, isSuccess, devOtp) {
        if (!feedbackBox) return;
        feedbackBox.className = 'alert ' + (isSuccess ? 'alert-success' : 'alert-danger') + ' py-2 px-3 small mb-3';
        let html = message;
        if (devOtp) {
            html += '<div class="mt-1 fw-bold text-primary"><i class="bi bi-info-circle me-1"></i>Development OTP: <span class="badge bg-primary fs-6">' + devOtp + '</span> (Auto-filled)</div>';
        }
        feedbackBox.innerHTML = html;
        feedbackBox.classList.remove('d-none');
    }

    function startTimer(seconds) {
        let remaining = seconds;
        if (countdownInterval) clearInterval(countdownInterval);
        if (resendLink) resendLink.classList.add('d-none');
        if (sendBtn) sendBtn.disabled = true;

        countdownInterval = setInterval(function () {
            remaining--;
            if (timerText) {
                timerText.textContent = remaining > 0 ? ('Resend available in ' + remaining + 's') : '';
            }
            if (remaining <= 0) {
                clearInterval(countdownInterval);
                if (sendBtn) sendBtn.disabled = false;
                if (resendLink) resendLink.classList.remove('d-none');
                if (timerText) timerText.textContent = '';
            }
        }, 1000);
    }

    async function sendLoginOtp() {
        const mobile = mobileInput ? mobileInput.value.trim() : '';
        if (!/^[6-9]\d{9}$/.test(mobile)) {
            showFeedback('Please enter a valid 10-digit mobile number starting with 6, 7, 8, or 9.', false);
            if (mobileInput) mobileInput.focus();
            return;
        }

        const token = document.querySelector('meta[name="csrf-token"]')?.content
            || document.querySelector('input[name="csrf_token"]')?.value
            || '';

        const form = new FormData();
        form.append('mobile', mobile);
        form.append('purpose', 'login');
        form.append('csrf_token', token);

        sendBtn.disabled = true;
        sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending...';

        try {
            const baseUrl = window.APP_BASE_URL || '<?php echo rtrim(BASE_URL, "/"); ?>';
            const response = await fetch(baseUrl + '/ajax/send-otp.php', {
                method: 'POST',
                body: form
            });
            const data = await response.json();

            if (data.ok) {
                showFeedback(data.message, true, data.dev_otp);
                if (data.dev_otp && otpInput) {
                    otpInput.value = data.dev_otp;
                }
                if (otpInput) otpInput.focus();
                startTimer(30);
            } else {
                showFeedback(data.message || 'Unable to send OTP. Please check your mobile number.', false);
                sendBtn.disabled = false;
            }
        } catch (err) {
            showFeedback('Network error while requesting OTP. Please try again.', false);
            sendBtn.disabled = false;
        } finally {
            sendBtn.innerHTML = 'Send OTP';
        }
    }

    if (sendBtn) {
        sendBtn.addEventListener('click', sendLoginOtp);
    }
    if (resendLink) {
        resendLink.addEventListener('click', sendLoginOtp);
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
