<?php 
$userRole = \App\Helpers\Session::get('role_slug');
if ($userRole === 'receptionist') {
    $activePage = 'reception_profile';
    include VIEWS_PATH . '/layout/reception_header.php'; 
} else {
    $activePage = 'profile';
    include VIEWS_PATH . '/layout/admin_header.php'; 
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-slate mb-1">My Profile & Account Security</h4>
        <p class="text-muted small mb-0">Update account credentials and change security password</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-7 mx-auto">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-emerald text-white py-3" style="background: linear-gradient(135deg, #059669, #047857);">
                <h6 class="fw-bold mb-0"><i class="bi bi-person-gear me-2"></i> Account Details</h6>
            </div>
            <div class="card-body p-4">
                <form action="<?= site_url(($userRole === 'receptionist' ? '/reception' : '/admin') . '/profile/update') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Username</label>
                        <input type="text" class="form-control" value="<?= esc($user['username']) ?>" disabled>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Email Address</label>
                        <input type="email" class="form-control" value="<?= esc($user['email']) ?>" disabled>
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold text-slate mb-3"><i class="bi bi-shield-lock me-1"></i> Change Security Password</h6>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">New Password</label>
                        <input type="password" class="form-control" name="password" placeholder="••••••••">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Confirm New Password</label>
                        <input type="password" class="form-control" name="password_confirm" placeholder="••••••••">
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-emerald rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Update Security Credentials
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
if ($userRole === 'receptionist') {
    include VIEWS_PATH . '/layout/reception_footer.php'; 
} else {
    include VIEWS_PATH . '/layout/admin_footer.php'; 
}
?>
