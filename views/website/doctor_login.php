<?php include VIEWS_PATH . '/layout/public_header.php'; ?>

<section class="py-5 bg-light min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header text-white text-center py-4 border-0" style="background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                        <div class="mb-2">
                            <i class="bi bi-activity fs-1"></i>
                        </div>
                        <h4 class="fw-bold mb-1">Doctor Portal</h4>
                        <p class="small text-white-50 mb-0">Physician Clinical Console Sign In</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($flashError = \App\Helpers\Session::getFlash('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show rounded-3 small" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= esc($flashError) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($flashSuccess = \App\Helpers\Session::getFlash('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show rounded-3 small" role="alert">
                                <i class="bi bi-check-circle-fill me-1"></i> <?= esc($flashSuccess) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form action="<?= site_url('/login') ?>" method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="portal_target" value="doctor">

                            <div class="mb-3">
                                <label for="username" class="form-label fw-bold small text-secondary">Username or Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-secondary"><i class="bi bi-person-badge"></i></span>
                                    <input type="text" class="form-control" id="username" name="username" value="<?= esc(\App\Helpers\Session::getFlash('old_username') ?? '') ?>" placeholder="Enter doctor username" required autofocus>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold small text-secondary">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-secondary"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember_me" name="remember_me">
                                    <label class="form-check-label small text-secondary" for="remember_me">Remember Me</label>
                                </div>
                                <a href="<?= site_url('/forgot-password') ?>" class="small text-primary fw-bold text-decoration-none">Forgot Password?</a>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 fw-bold shadow-sm">
                                <i class="bi bi-stethoscope me-2"></i> Access Doctor Console
                            </button>
                        </form>
                    </div>
                    <div class="card-footer bg-light text-center py-3 border-0">
                        <span class="small text-muted">Authorized Medical Officers Only</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include VIEWS_PATH . '/layout/public_footer.php'; ?>
