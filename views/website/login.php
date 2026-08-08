<?php
if (!defined('ROOT_PATH')) {
    exit('No direct script access allowed');
}
include VIEWS_PATH . '/layout/header.php';
?>

<!-- Single Common Login Container -->
<div class="row justify-content-center py-5">
    <div class="col-md-5 col-lg-4">
        <div class="card p-4 border-0 shadow-lg rounded-4">
            
            <!-- Header -->
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-2" style="width:60px; height:60px;">
                    <i class="bi bi-shield-lock-fill fs-2"></i>
                </div>
                <h3 class="fw-bold mt-2 text-slate mb-1">MedClinic Portal Login</h3>
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1.5 rounded-pill small fw-bold">
                    <i class="bi bi-person-check-fill me-1"></i> Single Login for All Staff Roles
                </span>
                <p class="text-muted small mt-2 mb-0">Sign in with your username or email. You will be automatically redirected to your assigned dashboard.</p>
            </div>
            
            <!-- AJAX Alerts -->
            <div id="alert-container" class="alert d-none"></div>

            <!-- Form -->
            <form id="login-form" action="<?= site_url('/login') ?>" method="POST" novalidate>
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="username" class="form-label fw-medium">Username or Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person-fill text-muted"></i></span>
                        <input type="text" class="form-control border-start-0" id="username" name="username" value="<?= esc(old('username')) ?>" required autofocus placeholder="Enter your username or email">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-medium">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-key-fill text-muted"></i></span>
                        <input type="password" class="form-control border-start-0" id="password" name="password" required placeholder="Enter your password">
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember_me" name="remember_me">
                        <label class="form-check-label small text-muted" for="remember_me">
                            Remember Me
                        </label>
                    </div>
                    <a href="<?= site_url('/forgot-password') ?>" class="text-decoration-none small text-primary fw-semibold">Forgot Password?</a>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2.5 fw-semibold shadow-sm rounded-3">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Dashboard
                </button>
            </form>
            
            <!-- Credentials Hint -->
            <div class="text-center mt-4 pt-3 border-top text-muted small">
                <i class="bi bi-info-circle text-primary me-1"></i> Logins: <strong class="text-dark">admin</strong>, <strong class="text-dark">doctor</strong>, <strong class="text-dark">receptionist</strong><br>
                Default Password: <strong class="text-dark">Admin@1234</strong>
            </div>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/footer.php'; ?>

