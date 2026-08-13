<?php 
$activePage = 'patients';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Registration Form Card -->
<div class="card border-0 shadow-sm p-4">
    <form action="<?= site_url('/admin/patients/save') ?>" method="POST" autocomplete="off">
        <?= csrf_field() ?>

        <h5 class="fw-bold text-slate mb-3"><i class="bi bi-person-plus text-success me-2"></i>Patient Clinical Registration Form</h5>
        
        <?php if ($isBranchAdmin && !empty($branchId)): ?>
            <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-building me-2"></i>
                <strong>Branch:</strong> <?= esc($branches[0]['name'] ?? '') ?>
                <span class="badge bg-primary ms-2">Branch Admin</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <input type="hidden" name="branch_id" value="<?= $branchId ?>">
        <?php endif; ?>
        
        <div class="row g-3 mb-4">
            <!-- Basic Demographics -->
            <div class="col-md-4">
                <label for="name" class="form-label small fw-semibold">Full Patient Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm" id="name" name="name" required placeholder="e.g. Rahul Sharma">
            </div>
            
            <div class="col-md-4">
                <label for="phone" class="form-label small fw-semibold">Phone Number <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm" id="phone" name="phone" required placeholder="e.g. +91 98765 43210">
            </div>
            
            <div class="col-md-4">
                <label for="email" class="form-label small fw-semibold">Email Address</label>
                <input type="email" class="form-control form-control-sm" id="email" name="email" placeholder="e.g. rahul@email.com">
            </div>
            
            <div class="col-md-3">
                <label for="gender" class="form-label small fw-semibold">Gender <span class="text-danger">*</span></label>
                <select class="form-control form-control-sm form-select" id="gender" name="gender" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="dob" class="form-label small fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                <input type="date" class="form-control form-control-sm" id="dob" name="dob" required max="<?= date('Y-m-d') ?>">
            </div>

            <div class="col-md-3">
                <label for="blood_group" class="form-label small fw-semibold">Blood Group</label>
                <select class="form-control form-control-sm form-select" id="blood_group" name="blood_group">
                    <option value="">Unknown</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select>
            </div>

            <!-- Branch - Super Admin দেখতে পাবে, Branch Admin দেখতে পাবে না (hidden) -->
            <?php if ($isSuperAdmin): ?>
            <div class="col-md-3">
                <label for="branch_id" class="form-label small fw-semibold">Primary Branch Office</label>
                <select class="form-control form-control-sm form-select" id="branch_id" name="branch_id">
                    <option value="">Headquarters / General Register</option>
                    <?php foreach ($branches as $branch): ?>
                        <option value="<?= $branch['id'] ?>"><?= esc($branch['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="col-md-9">
                <label for="address" class="form-label small fw-semibold">Permanent Physical Address <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm" id="address" name="address" required placeholder="Street name, City, Pincode">
            </div>

            <div class="col-md-3">
                <label for="emergency_contact" class="form-label small fw-semibold">Emergency Contact Person / Phone</label>
                <input type="text" class="form-control form-control-sm" id="emergency_contact" name="emergency_contact" placeholder="Name - Phone #">
            </div>

            <!-- Health History & Warnings -->
            <div class="col-md-4">
                <label for="allergies" class="form-label small fw-semibold text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Allergies & Drug Reactions</label>
                <textarea class="form-control text-danger border-danger border-opacity-50" id="allergies" name="allergies" rows="3" placeholder="List drug or food allergies (e.g. Penicillin)"></textarea>
            </div>
            
            <div class="col-md-4">
                <label for="medical_history" class="form-label small fw-semibold"><i class="bi bi-activity me-1"></i>Previous Medical History</label>
                <textarea class="form-control" id="medical_history" name="medical_history" rows="3" placeholder="Diabetes, Hypertension, Heart issues, surgeries, etc."></textarea>
            </div>
            
            <div class="col-md-4">
                <label for="family_history" class="form-label small fw-semibold"><i class="bi bi-diagram-3 me-1"></i>Family Health History</label>
                <textarea class="form-control" id="family_history" name="family_history" rows="3" placeholder="Hereditary illnesses, history of heart attacks, etc."></textarea>
            </div>
        </div>

        <div class="text-end pt-3">
            <a href="<?= site_url('/admin/patients') ?>" class="btn btn-outline-secondary btn-sm px-3 me-2">Cancel</a>
            <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
                <i class="bi bi-save me-1"></i> Register Patient Profile
            </button>
        </div>
    </form>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>