<?php
if (!defined('ROOT_PATH')) {
    exit('No direct script access allowed');
}
include VIEWS_PATH . '/layout/header.php';
?>

<!-- Forgot Password Container -->
<div class="row justify-content-center py-5">
    <div class="col-md-5">
        <div class="card p-4 border-0 shadow-lg">
            
            <!-- Header -->
            <div class="text-center mb-4">
                <i class="bi bi-envelope-open-fill text-success display-4"></i>
                <h3 class="fw-bold mt-2 text-slate">Forgot Password</h3>
                <p class="text-muted small">Enter your email address and we will send you a 6-digit OTP code to verify your identity.</p>
            </div>
            
            <!-- Form -->
            <form action="<?= site_url('/forgot-password') ?>" method="POST" novalidate>
                <?= csrf_field() ?>

                <div class="mb-4">
                    <label for="email" class="form-label fw-medium">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope-fill text-muted"></i></span>
                        <input type="email" class="form-control border-start-0" id="email" name="email" required autofocus placeholder="e.g. admin@clinic.com">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2.5 fw-semibold shadow-sm">
                    <i class="bi bi-send-fill me-1"></i> Send Verification OTP
                </button>
            </form>
            
            <!-- Footer Link -->
            <div class="text-center mt-3 pt-3 border-top">
                <a href="<?= site_url('/login') ?>" class="text-decoration-none small text-success fw-semibold">
                    <i class="bi bi-arrow-left me-1"></i> Return to Login
                </a>
            </div>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/footer.php'; ?>
