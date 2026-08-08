<?php 
$activePage = 'doctor_profile';
include VIEWS_PATH . '/layout/doctor_header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-slate mb-1">Doctor Profile & Availability Schedule</h4>
        <p class="text-muted small mb-0">Manage your clinical qualifications, experience, and consultation hours</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-person-gear me-2"></i> Clinical Profile Details</h6>
            </div>
            <div class="card-body p-4">
                <form action="<?= site_url('/doctor/profile/update') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Doctor Username</label>
                            <input type="text" class="form-control" value="<?= esc($user['username']) ?>" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Email Address</label>
                            <input type="email" class="form-control" value="<?= esc($user['email']) ?>" disabled>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Medical Qualification</label>
                            <input type="text" class="form-control" name="qualification" value="<?= esc($profile['qualification'] ?? 'MBBS, MS (General Surgery)') ?>" placeholder="e.g. MBBS, MS (General Surgery)">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Years of Experience</label>
                            <input type="text" class="form-control" name="experience" value="<?= esc($profile['experience'] ?? '12 Years') ?>" placeholder="e.g. 10 Years">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Clinical Specialization</label>
                        <input type="text" class="form-control" name="specialization" value="<?= esc($profile['specialization'] ?? 'Proctology & General Surgery') ?>" placeholder="e.g. Proctology & General Surgery">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Availability Schedule & OPD Timing</label>
                        <textarea class="form-control" name="availability_schedule" rows="3" placeholder="e.g. Mon-Sat: 09:00 AM - 01:00 PM & 04:00 PM - 08:00 PM"><?= esc($profile['availability_schedule'] ?? 'Monday to Saturday: 09:00 AM – 02:00 PM & 05:00 PM – 08:00 PM') ?></textarea>
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold text-slate mb-3"><i class="bi bi-shield-lock me-1"></i> Change Password</h6>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">New Password</label>
                            <input type="password" class="form-control" name="password" placeholder="••••••••">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Confirm New Password</label>
                            <input type="password" class="form-control" name="password_confirm" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Update Profile Details
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/doctor_footer.php'; ?>
