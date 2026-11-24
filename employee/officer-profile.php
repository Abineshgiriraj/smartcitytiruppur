<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

$auth = require_officer();
$user = $auth['user'];
$officer = $auth['officer'];

$pageTitle = 'My Officer Profile';

// Handle Change Password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = trim($_POST['action'] ?? '');

    try {
        if ($action === 'change_email') {
            $email=strtolower(trim((string)($_POST['new_email'] ?? '')));
            $password=(string)($_POST['current_password'] ?? '');
            $otp=trim((string)($_POST['confirm_otp'] ?? ''));
            if (!filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($email)>250) {
                throw new RuntimeException('Enter a valid email address.');
            }
            if (strcasecmp($email,(string)($user['email']??''))===0) {
                throw new RuntimeException('This is already your login email.');
            }
            $verifyPassword=db()->prepare('SELECT password_hash FROM users WHERE id=? AND is_active=1');
            $verifyPassword->execute([(int)$user['id']]);
            $hash=(string)$verifyPassword->fetchColumn();
            $authenticated=$password!=='' && password_verify($password,$hash);
            if (!$authenticated && $otp!=='') {
                $otpResult=login_user_by_otp((string)$user['mobile_no'],$otp);
                $authenticated=(bool)($otpResult['ok']??false);
            }
            if (!$authenticated) {
                throw new RuntimeException('Verify your current password or request an OTP for your registered mobile before changing email.');
            }
            $duplicate=db()->prepare('SELECT id FROM users WHERE email=? AND id<>? LIMIT 1');
            $duplicate->execute([$email,(int)$user['id']]);
            if ($duplicate->fetchColumn()) throw new RuntimeException('That email is already linked to another account.');
            db()->prepare('UPDATE users SET email=?, updated_at=NOW() WHERE id=? AND is_active=1')
                ->execute([$email,(int)$user['id']]);
            audit_log((int)$user['id'],'change_login_email','user',(int)$user['id']);
            flash('success','Login email updated. You can now use it for password login.');
            redirect('employee/officer-profile.php');
        }
        if ($action === 'change_password') {
            $currentPwd = $_POST['current_password'] ?? '';
            $newPwd = $_POST['new_password'] ?? '';
            $confirmPwd = $_POST['confirm_password'] ?? '';

            if (strlen($newPwd) < 8) {
                throw new RuntimeException('New password must be at least 8 characters long.');
            }
            if ($newPwd !== $confirmPwd) {
                throw new RuntimeException('New password and confirmation do not match.');
            }

            // Verify current password
            $pwdStmt = db()->prepare('SELECT password_hash FROM users WHERE id = ?');
            $pwdStmt->execute([(int)$user['id']]);
            $currentHash = (string)$pwdStmt->fetchColumn();

            if (!password_verify($currentPwd, $currentHash)) {
                throw new RuntimeException('Current password is incorrect.');
            }

            // Update password
            $newHash = password_hash($newPwd, PASSWORD_DEFAULT);
            db()->prepare('UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?')
                ->execute([$newHash, (int)$user['id']]);

            audit_log((int)$user['id'], 'change_password', 'user', (int)$user['id']);
            flash('success', 'Your password has been updated successfully.');
            redirect('employee/officer-profile.php');
        }
    } catch (Throwable $e) {
        flash('danger', $e->getMessage());
    }
}

include __DIR__ . '/../includes/employee-header.php';
?>

<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">My Officer Profile</h1>
        <p class="text-muted mb-0">Manage your municipal officer account and security credentials</p>
    </div>
    <div>
        <a href="<?php echo e(url('employee/officer-dashboard.php')); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Dashboard
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Officer Profile Details Card -->
    <div class="col-lg-6">
        <div class="card content-card border-0 shadow-sm p-4 h-100">
            <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white fw-bold shadow-sm" style="width: 64px; height: 64px; font-size: 26px;">
                    <?php echo e(strtoupper(substr(trim((string)$officer['officer_name']), 0, 1))); ?>
                </div>
                <div>
                    <h4 class="fw-bold mb-1"><?php echo e($officer['officer_name']); ?></h4>
                    <span class="badge bg-primary px-2 py-1"><i class="bi bi-shield-check me-1"></i><?php echo e($officer['designation'] ?: 'Field Officer'); ?></span>
                    <span class="badge bg-light text-dark border ms-1">Officer #<?php echo (int)$officer['id']; ?></span>
                </div>
            </div>

            <h5 class="fw-bold mb-3"><i class="bi bi-person-lines-fill text-primary me-2"></i>Official Details</h5>
            <div class="row g-3">
                <div class="col-sm-6">
                    <label class="text-muted small fw-semibold">Department</label>
                    <div class="fw-semibold"><?php echo e($officer['department_name'] ?: 'Municipal Administration'); ?></div>
                </div>
                <div class="col-sm-6">
                    <label class="text-muted small fw-semibold">Designation</label>
                    <div class="fw-semibold"><?php echo e($officer['designation'] ?: 'Field Officer'); ?></div>
                </div>
                <div class="col-sm-6">
                    <label class="text-muted small fw-semibold">Official Mobile Number</label>
                    <div class="fw-semibold"><i class="bi bi-telephone text-muted me-1"></i><?php echo e($officer['mobile_no'] ?: $user['mobile_no'] ?: '-'); ?></div>
                </div>
                <div class="col-sm-6">
                    <label class="text-muted small fw-semibold">Official Email</label>
                    <div class="fw-semibold"><i class="bi bi-envelope text-muted me-1"></i><?php echo e($user['email'] ?: 'Not added yet'); ?></div><small class="text-muted">Your login email (different from the municipality's official directory contact)</small>
                </div>
                <div class="col-sm-6">
                    <label class="text-muted small fw-semibold">User Role</label>
                    <div><span class="badge bg-secondary"><?php echo e($user['role']); ?></span></div>
                </div>
                <div class="col-sm-6">
                    <label class="text-muted small fw-semibold">Account Status</label>
                    <div><span class="badge bg-success">Active</span></div>
                </div>
                <div class="col-sm-6">
                    <label class="text-muted small fw-semibold">Last Login</label>
                    <div class="text-muted small"><?php echo !empty($user['last_login_at']) ? date('d-M-Y H:i:s', strtotime($user['last_login_at'])) : 'First login session'; ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security & Password Card -->
    <div class="col-lg-6">
      <div class="card content-card border-0 shadow-sm p-4 mb-3">
        <h5 class="fw-bold mb-2"><i class="bi bi-envelope-at text-primary me-2"></i>Change Login Email</h5>
        <p class="text-muted small">Email is optional. To add or change it, verify with your current password or a fresh mobile OTP.</p>
        <form method="post">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="action" value="change_email">
          <div class="mb-3">
            <label class="form-label fw-semibold">New Email Address</label>
            <input class="form-control" type="email" name="new_email" maxlength="250"
                   value="<?php echo e($user['email'] ?? ''); ?>" required autocomplete="email">
          </div>
          <div class="mb-3">
            <label class="form-label">Current Password (if you have one)</label>
            <input class="form-control" type="password" name="current_password" autocomplete="current-password">
          </div>
          <div class="mb-3">
            <label class="form-label">Or verify by Mobile OTP</label>
            <div class="input-group">
              <input class="form-control" type="text" inputmode="numeric" name="confirm_otp" maxlength="6" pattern="[0-9]{6}" placeholder="6-digit OTP">
              <button type="button" class="btn btn-outline-primary" id="sendEmailChangeOtp">Send OTP</button>
            </div>
            <div id="emailChangeOtpFeedback" class="form-text" role="status">Uses your registered mobile number ending <?php echo e(substr((string)($user['mobile_no']??''),-4)); ?>.</div>
          </div>
          <button class="btn btn-primary" type="submit">Save Email</button>
        </form>
      </div>
      <script>
        document.addEventListener('DOMContentLoaded',function(){
          const btn=document.getElementById('sendEmailChangeOtp');
          if(!btn)return;
          btn.addEventListener('click',async function(){
            const feedback=document.getElementById('emailChangeOtpFeedback');
            btn.disabled=true;feedback.textContent='Requesting OTP...';
            const data=new FormData();
            data.append('purpose','login');
            data.append('mobile',<?php echo json_encode((string)$user['mobile_no']); ?>);
            data.append('csrf_token',<?php echo json_encode(csrf_token()); ?>);
            try{
              const response=await fetch(<?php echo json_encode(url('ajax/send-otp.php')); ?>,{
                method:'POST',body:data,credentials:'same-origin'
              });
              const result=await response.json();
              feedback.textContent=result.message||'Unable to request OTP.';
              if(result.dev_otp)feedback.textContent+=' Test OTP: '+result.dev_otp;
            }catch(e){feedback.textContent='OTP request failed. Try again.';}
            btn.disabled=false;
          });
        });
      </script>

        <div class="card content-card border-0 shadow-sm p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-shield-lock text-primary me-2"></i>Change Password</h5>
            <p class="text-muted small mb-4">Ensure your account uses a secure password of at least 8 characters.</p>

            <form method="post">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="change_password">

                <div class="mb-3">
                    <label class="form-label required">Current Password</label>
                    <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
                </div>

                <div class="mb-3">
                    <label class="form-label required">New Password</label>
                    <input type="password" name="new_password" class="form-control" required minlength="8" autocomplete="new-password">
                    <div class="form-text small">At least 8 characters.</div>
                </div>

                <div class="mb-4">
                    <label class="form-label required">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required minlength="8" autocomplete="new-password">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-key me-1"></i> Update Password
                </button>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/employee-footer.php'; ?>
