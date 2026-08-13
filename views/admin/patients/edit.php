<?php 
$activePage = 'patients';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Edit Form Card -->
<div class="card border-0 shadow-sm p-4">
    <form action="<?= site_url('/admin/patients/update/' . $patient['id']) ?>" method="POST" autocomplete="off">
        <?= csrf_field() ?>

        <div class="d-flex align-items-center mb-3">
            <h5 class="fw-bold text-slate mb-0"><i class="bi bi-pencil-square text-success me-2"></i>Update Patient Profile</h5>
            <span class="ms-auto badge bg-light text-secondary border fs-6">ID: <?= esc($patient['patient_id']) ?></span>
        </div>
        
        <?php if ((isset($isBranchAdmin) && $isBranchAdmin) || (isset($isReceptionist) && $isReceptionist)): ?>
            <?php if (!empty($branchId) && !empty($branches)): ?>
                <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-building me-2"></i>
                    <strong>Branch:</strong> <?= esc($branches[0]['name'] ?? '') ?>
                    <span class="badge bg-primary ms-2"><?= isset($isBranchAdmin) && $isBranchAdmin ? 'Branch Admin' : 'Receptionist' ?></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <input type="hidden" name="branch_id" value="<?= $branchId ?>">
            <?php endif; ?>
        <?php endif; ?>
        
        <div class="row g-3 mb-4">
            <!-- Basic Demographics -->
            <div class="col-md-4">
                <label for="name" class="form-label small fw-semibold">Full Patient Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm" id="name" name="name" value="<?= esc($patient['name']) ?>" required placeholder="e.g. Rahul Sharma">
            </div>
            
            <div class="col-md-4">
                <label for="phone" class="form-label small fw-semibold">Phone Number <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm" id="phone" name="phone" value="<?= esc($patient['phone']) ?>" required placeholder="e.g. +91 98765 43210">
            </div>
            
            <div class="col-md-4">
                <label for="email" class="form-label small fw-semibold">Email Address</label>
                <input type="email" class="form-control form-control-sm" id="email" name="email" value="<?= esc($patient['email']) ?>" placeholder="e.g. rahul@email.com">
            </div>
            
            <div class="col-md-3">
                <label for="gender" class="form-label small fw-semibold">Gender <span class="text-danger">*</span></label>
                <select class="form-control form-control-sm form-select" id="gender" name="gender" required>
                    <option value="male" <?= $patient['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
                    <option value="female" <?= $patient['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
                    <option value="other" <?= $patient['gender'] === 'other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="dob" class="form-label small fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                <input type="date" class="form-control form-control-sm" id="dob" name="dob" value="<?= esc($patient['dob']) ?>" required max="<?= date('Y-m-d') ?>">
            </div>

            <div class="col-md-2">
                <label for="blood_group" class="form-label small fw-semibold">Blood Group</label>
                <select class="form-control form-control-sm form-select" id="blood_group" name="blood_group">
                    <option value="" <?= empty($patient['blood_group']) ? 'selected' : '' ?>>Unknown</option>
                    <option value="A+" <?= $patient['blood_group'] === 'A+' ? 'selected' : '' ?>>A+</option>
                    <option value="A-" <?= $patient['blood_group'] === 'A-' ? 'selected' : '' ?>>A-</option>
                    <option value="B+" <?= $patient['blood_group'] === 'B+' ? 'selected' : '' ?>>B+</option>
                    <option value="B-" <?= $patient['blood_group'] === 'B-' ? 'selected' : '' ?>>B-</option>
                    <option value="AB+" <?= $patient['blood_group'] === 'AB+' ? 'selected' : '' ?>>AB+</option>
                    <option value="AB-" <?= $patient['blood_group'] === 'AB-' ? 'selected' : '' ?>>AB-</option>
                    <option value="O+" <?= $patient['blood_group'] === 'O+' ? 'selected' : '' ?>>O+</option>
                    <option value="O-" <?= $patient['blood_group'] === 'O-' ? 'selected' : '' ?>>O-</option>
                </select>
            </div>

            <!-- Branch - শুধু Super Admin দেখতে পাবে -->
            <?php if (isset($isSuperAdmin) && $isSuperAdmin): ?>
            <div class="col-md-2">
                <label for="branch_id" class="form-label small fw-semibold">Primary Branch Office</label>
                <select class="form-control form-control-sm form-select" id="branch_id" name="branch_id">
                    <option value="">Headquarters / General Register</option>
                    <?php foreach ($branches as $branch): ?>
                        <option value="<?= $branch['id'] ?>" <?= (int)$patient['branch_id'] === (int)$branch['id'] ? 'selected' : '' ?>><?= esc($branch['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="col-md-2">
                <label for="status" class="form-label small fw-semibold">Patient Status</label>
                <select class="form-control form-control-sm form-select" id="status" name="status">
                    <option value="active" <?= $patient['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $patient['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <div class="col-md-9">
                <label for="address" class="form-label small fw-semibold">Permanent Physical Address <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm" id="address" name="address" value="<?= esc($patient['address']) ?>" required placeholder="Street name, City, Pincode">
            </div>

            <div class="col-md-3">
                <label for="emergency_contact" class="form-label small fw-semibold">Emergency Contact Person / Phone</label>
                <input type="text" class="form-control form-control-sm" id="emergency_contact" name="emergency_contact" value="<?= esc($patient['emergency_contact']) ?>" placeholder="Name - Phone #">
            </div>

            <!-- Health History & Warnings -->
            <div class="col-md-4">
                <label for="allergies" class="form-label small fw-semibold text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Allergies & Drug Reactions</label>
                <textarea class="form-control text-danger border-danger border-opacity-50" id="allergies" name="allergies" rows="3" placeholder="List drug or food allergies"><?= esc($patient['allergies']) ?></textarea>
            </div>
            
            <div class="col-md-4">
                <label for="medical_history" class="form-label small fw-semibold"><i class="bi bi-activity me-1"></i>Previous Medical History</label>
                <textarea class="form-control" id="medical_history" name="medical_history" rows="3" placeholder="Diabetes, Hypertension, surgeries, etc."><?= esc($patient['medical_history']) ?></textarea>
            </div>
            
            <div class="col-md-4">
                <label for="family_history" class="form-label small fw-semibold"><i class="bi bi-diagram-3 me-1"></i>Family Health History</label>
                <textarea class="form-control" id="family_history" name="family_history" rows="3" placeholder="Hereditary illness histories"><?= esc($patient['family_history']) ?></textarea>
            </div>
        </div>

        <div class="text-end pt-3">
            <a href="<?= site_url('/admin/patients') ?>" class="btn btn-outline-secondary btn-sm px-3 me-2">Cancel</a>
            <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
                <i class="bi bi-save me-1"></i> Update Patient Profile
            </button>
        </div>
    </form>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>