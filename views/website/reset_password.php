<?php
if (!defined('ROOT_PATH')) {
    exit('No direct script access allowed');
}
include VIEWS_PATH . '/layout/header.php';
?>

<!-- Reset Password Container -->
<div class="row justify-content-center py-5">
    <div class="col-md-5">
        <div class="card p-4 border-0 shadow-lg">
            
            <!-- Header -->
            <div class="text-center mb-4">
                <i class="bi bi-key-fill text-success display-4"></i>
                <h3 class="fw-bold mt-2 text-slate">Reset Password</h3>
                <p class="text-muted small">Choose a secure, strong password. It must be at least 8 characters long.</p>
            </div>
            
            <!-- Form -->
            <form action="<?= site_url('/reset-password') ?>" method="POST" novalidate>
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="password" class="form-label fw-medium">New Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-lock-fill text-muted"></i></span>
                        <input type="password" class="form-control border-start-0" id="password" name="password" minlength="8" required autofocus placeholder="Enter new password">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password_confirm" class="form-label fw-medium">Confirm New Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-lock-fill text-muted"></i></span>
                        <input type="password" class="form-control border-start-0" id="password_confirm" name="password_confirm" minlength="8" required placeholder="Retype new password">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2.5 fw-semibold shadow-sm">
                    <i class="bi bi-save-fill me-1"></i> Update Password
                </button>
            </form>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/footer.php'; ?>
