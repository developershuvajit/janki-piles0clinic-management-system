<?php
if (!defined('ROOT_PATH')) {
    exit('No direct script access allowed');
}
include VIEWS_PATH . '/layout/header.php';
?>

<!-- Verify OTP Container -->
<div class="row justify-content-center py-5">
    <div class="col-md-5">
        <div class="card p-4 border-0 shadow-lg">
            
            <!-- Header -->
            <div class="text-center mb-4">
                <i class="bi bi-shield-lock-fill text-success display-4"></i>
                <h3 class="fw-bold mt-2 text-slate">Verify OTP Code</h3>
                <p class="text-muted small">We have sent a verification code to <strong><?= esc(\App\Helpers\Session::get('reset_email')) ?></strong>. Enter the 6-digit code below.</p>
            </div>
            
            <!-- Form -->
            <form action="<?= site_url('/verify-otp') ?>" method="POST" novalidate>
                <?= csrf_field() ?>

                <div class="mb-4">
                    <label for="otp_code" class="form-label fw-medium text-center d-block">6-Digit Code</label>
                    <input type="text" class="form-control text-center fs-3 fw-bold" id="otp_code" name="otp_code" maxlength="6" pattern="\d{6}" required autofocus placeholder="000000" style="letter-spacing: 8px;">
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2.5 fw-semibold shadow-sm">
                    <i class="bi bi-patch-check-fill me-1"></i> Verify OTP Code
                </button>
            </form>
            
            <!-- Footer Link -->
            <div class="text-center mt-3 pt-3 border-top">
                <a href="<?= site_url('/forgot-password') ?>" class="text-decoration-none small text-success fw-semibold">
                    <i class="bi bi-arrow-clockwise me-1"></i> Resend Verification Code
                </a>
            </div>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/footer.php'; ?>
