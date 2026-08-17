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

<!-- ============================================
     PAGE CSS
     ============================================ -->
<style>
.bg-emerald-gradient {
    background: linear-gradient(135deg, #059669, #047857);
}
.btn-emerald {
    background: #059669;
    border-color: #059669;
    color: #fff;
}
.btn-emerald:hover {
    background: #047857;
    border-color: #047857;
    color: #fff;
}
.text-slate {
    color: #0b1a2b;
}
.profile-card {
    border-radius: 16px;
    overflow: hidden;
    border: none;
    box-shadow: 0 4px 16px rgba(0,0,0,0.06);
}
.profile-card .form-control {
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    padding: 0.6rem 0.9rem;
    font-size: 0.9rem;
    transition: all 0.2s;
    background: #f8fafc;
}
.profile-card .form-control:focus {
    border-color: #059669;
    box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.12);
    background: #fff;
}
.profile-card .form-control:disabled {
    background: #f1f5f9;
    color: #475569;
    cursor: not-allowed;
}
.profile-card .form-label {
    font-weight: 600;
    font-size: 0.78rem;
    color: #475569;
    margin-bottom: 0.3rem;
}
</style>

<!-- ============================================
     HEADER
     ============================================ -->
<div class="d-flex justify-content-between align-items-center mb-4 mt-4 mx-4">
    <div>
        <h4 class="fw-bold text-slate mb-1"><i class="bi bi-person-gear text-success me-2"></i>My Profile & Account Security</h4>
        <p class="text-muted small mb-0">Update account credentials and change security password</p>
    </div>
</div>

<!-- ============================================
     PROFILE CARD
     ============================================ -->
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="profile-card">
            <!-- Card Header -->
            <div class="bg-emerald-gradient text-white px-4 py-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-person-badge me-2"></i> Account Details
                </h6>
            </div>
            
            <!-- Card Body -->
            <div class="p-4">
                <form action="<?= site_url(($userRole === 'receptionist' ? '/reception' : '/admin') . '/profile/update') ?>" method="POST">
                    <?= csrf_field() ?>

                    <input type="hidden" name="username" value="<?= esc($user['username']) ?>">
    <input type="hidden" name="email" value="<?= esc($user['email']) ?>">

                    <!-- Username -->
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-person me-1"></i> Username</label>
                        <input type="text" class="form-control" value="<?= esc($user['username']) ?>" disabled>
                        <div class="text-muted small mt-1" style="font-size: 0.7rem;">
                            <i class="bi bi-info-circle"></i> Username cannot be changed
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="form-label"><i class="bi bi-envelope me-1"></i> Email Address</label>
                        <input type="email" class="form-control" value="<?= esc($user['email']) ?>" disabled>
                        <div class="text-muted small mt-1" style="font-size: 0.7rem;">
                            <i class="bi bi-info-circle"></i> Email cannot be changed
                        </div>
                    </div>

                    <!-- Divider -->
                    <hr class="my-4" style="border-color: #e2e8f0;">

                    <!-- Change Password Section -->
                    <h6 class="fw-bold text-slate mb-3">
                        <i class="bi bi-shield-lock text-success me-2"></i> Change Security Password
                    </h6>

                    <!-- New Password -->
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-key me-1"></i> New Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Enter new password (min 8 characters)">
                        <div class="text-muted small mt-1" style="font-size: 0.7rem;">
                            <i class="bi bi-info-circle"></i> Minimum 8 characters
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-4">
                        <label class="form-label"><i class="bi bi-check-circle me-1"></i> Confirm New Password</label>
                        <input type="password" class="form-control" name="password_confirm" placeholder="Re-enter new password">
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-emerald rounded-pill px-4 py-2 fw-bold shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Update Security Credentials
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ============================================
     JAVASCRIPT FOR PASSWORD VALIDATION
     ============================================ -->
<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.querySelector('input[name="password"]');
    const confirm = document.querySelector('input[name="password_confirm"]');
    
    // Only validate if password field is not empty
    if (password.value.trim() !== '') {
        if (password.value.length < 8) {
            e.preventDefault();
            alert('Password must be at least 8 characters long.');
            password.focus();
            return;
        }
        if (password.value !== confirm.value) {
            e.preventDefault();
            alert('Passwords do not match. Please re-enter.');
            confirm.focus();
            return;
        }
    }
});
</script>

<?php 
if ($userRole === 'receptionist') {
    include VIEWS_PATH . '/layout/reception_footer.php'; 
} else {
    include VIEWS_PATH . '/layout/admin_footer.php'; 
}
?>