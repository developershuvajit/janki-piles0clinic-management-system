<?php
$activePage = 'patients';
include VIEWS_PATH . '/layout/admin_header.php';
?>

<style>
.patient-edit-card {
    border: 0;
    border-radius: 12px;
    box-shadow: 0 4px 18px rgba(15, 23, 42, .06);
}

.patient-edit-card .form-control,
.patient-edit-card .form-select {
    border-color: #e2e8f0;
    border-radius: 7px;
    font-size: .85rem;
}

.patient-edit-card .form-control:focus,
.patient-edit-card .form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 .15rem rgba(13, 110, 253, .08);
}

.patient-edit-card textarea {
    resize: vertical;
}

.section-title {
    font-size: .8rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .04em;
    padding-bottom: 8px;
    margin-bottom: 14px;
    border-bottom: 1px solid #eef2f7;
}

.patient-id-badge {
    background: #f8fafc;
    color: #475569;
    border: 1px solid #e2e8f0;
    padding: 6px 12px;
    border-radius: 7px;
    font-size: .78rem;
    font-weight: 600;
}

.form-label {
    color: #334155;
}

.health-box {
    background: #f8fafc;
    border-radius: 9px;
    padding: 12px;
    height: 100%;
}

.health-box.danger {
    background: #fff8f8;
}

@media (max-width: 768px) {
    .patient-edit-card {
        padding: 1rem !important;
    }
}
</style>

<!-- =========================
     EDIT PATIENT FORM
     ========================= -->
<div class="card patient-edit-card p-4 mt-4">

    <form action="<?= site_url('/admin/patients/update/' . $patient['id']) ?>" method="POST" autocomplete="off">
        <?= csrf_field() ?>

        <!-- Header -->
        <div class="d-flex align-items-center mb-4">
            <div>
                <h5 class="fw-bold text-slate mb-1">
                    <i class="bi bi-pencil-square text-success me-2"></i>
                    Update Patient Profile
                </h5>
                <small class="text-muted">Update patient demographic and clinical information</small>
            </div>

            <span class="patient-id-badge ms-auto">
                ID: <?= esc($patient['patient_id']) ?>
            </span>
        </div>

        <!-- Branch Information -->
        <?php if ((isset($isBranchAdmin) && $isBranchAdmin) || (isset($isReceptionist) && $isReceptionist)): ?>
            <?php if (!empty($branchId) && !empty($branches)): ?>

                <div class="alert alert-info py-2 px-3 mb-4 d-flex align-items-center">
                    <i class="bi bi-building me-2"></i>

                    <div>
                        <strong>Branch:</strong>
                        <?= esc($branches[0]['name'] ?? '') ?>
                    </div>

                    <span class="badge bg-primary ms-2">
                        <?= isset($isBranchAdmin) && $isBranchAdmin ? 'Branch Admin' : 'Receptionist' ?>
                    </span>

                    <button type="button"
                            class="btn-close ms-auto"
                            data-bs-dismiss="alert"></button>
                </div>

                <input type="hidden" name="branch_id" value="<?= $branchId ?>">

            <?php endif; ?>
        <?php endif; ?>

        <!-- =========================
             BASIC INFORMATION
             ========================= -->
        <div class="section-title">
            <i class="bi bi-person me-1"></i> Basic Information
        </div>

        <div class="row g-3 mb-4">

            <div class="col-md-4">
                <label for="name" class="form-label small fw-semibold">
                    Full Patient Name <span class="text-danger">*</span>
                </label>

                <input type="text"
                       class="form-control form-control-sm"
                       id="name"
                       name="name"
                       value="<?= esc($patient['name']) ?>"
                       required>
            </div>

            <div class="col-md-4">
                <label for="phone" class="form-label small fw-semibold">
                    Phone Number <span class="text-danger">*</span>
                </label>

                <input type="text"
                       class="form-control form-control-sm"
                       id="phone"
                       name="phone"
                       value="<?= esc($patient['phone']) ?>"
                       required>
            </div>

            <div class="col-md-4">
                <label for="email" class="form-label small fw-semibold">
                    Email Address
                </label>

                <input type="email"
                       class="form-control form-control-sm"
                       id="email"
                       name="email"
                       value="<?= esc($patient['email']) ?>">
            </div>

            <div class="col-md-3">
                <label for="gender" class="form-label small fw-semibold">
                    Gender <span class="text-danger">*</span>
                </label>

                <select class="form-control form-control-sm form-select"
                        id="gender"
                        name="gender"
                        required>

                    <option value="male" <?= $patient['gender'] === 'male' ? 'selected' : '' ?>>
                        Male
                    </option>

                    <option value="female" <?= $patient['gender'] === 'female' ? 'selected' : '' ?>>
                        Female
                    </option>

                    <option value="other" <?= $patient['gender'] === 'other' ? 'selected' : '' ?>>
                        Other
                    </option>

                </select>
            </div>

            <div class="col-md-3">
                <label for="dob" class="form-label small fw-semibold">
                    Date of Birth <span class="text-danger">*</span>
                </label>

                <input type="date"
                       class="form-control form-control-sm"
                       id="dob"
                       name="dob"
                       value="<?= esc($patient['dob']) ?>"
                       required
                       max="<?= date('Y-m-d') ?>">
            </div>

            <div class="col-md-2">
                <label for="blood_group" class="form-label small fw-semibold">
                    Blood Group
                </label>

                <select class="form-control form-control-sm form-select"
                        id="blood_group"
                        name="blood_group">

                    <option value="" <?= empty($patient['blood_group']) ? 'selected' : '' ?>>
                        Unknown
                    </option>

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

            <!-- Super Admin Branch -->
            <?php if (isset($isSuperAdmin) && $isSuperAdmin): ?>

                <div class="col-md-2">
                    <label for="branch_id" class="form-label small fw-semibold">
                        Primary Branch
                    </label>

                    <select class="form-control form-control-sm form-select"
                            id="branch_id"
                            name="branch_id">

                        <option value="">
                            Headquarters / General
                        </option>

                        <?php foreach ($branches as $branch): ?>

                            <option value="<?= $branch['id'] ?>"
                                <?= (int)$patient['branch_id'] === (int)$branch['id'] ? 'selected' : '' ?>>
                                <?= esc($branch['name']) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>
                </div>

            <?php endif; ?>

            <div class="col-md-2">
                <label for="status" class="form-label small fw-semibold">
                    Patient Status
                </label>

                <select class="form-control form-control-sm form-select"
                        id="status"
                        name="status">

                    <option value="active" <?= $patient['status'] === 'active' ? 'selected' : '' ?>>
                        Active
                    </option>

                    <option value="inactive" <?= $patient['status'] === 'inactive' ? 'selected' : '' ?>>
                        Inactive
                    </option>

                </select>
            </div>

            <div class="col-md-9">
                <label for="address" class="form-label small fw-semibold">
                    Permanent Address <span class="text-danger">*</span>
                </label>

                <input type="text"
                       class="form-control form-control-sm"
                       id="address"
                       name="address"
                       value="<?= esc($patient['address']) ?>"
                       required>
            </div>

            <div class="col-md-3">
                <label for="emergency_contact" class="form-label small fw-semibold">
                    Emergency Contact
                </label>

                <input type="text"
                       class="form-control form-control-sm"
                       id="emergency_contact"
                       name="emergency_contact"
                       value="<?= esc($patient['emergency_contact']) ?>">
            </div>

        </div>

        <!-- =========================
             CLINICAL INFORMATION
             ========================= -->
        <div class="section-title">
            <i class="bi bi-heart-pulse me-1"></i> Clinical Information
        </div>

        <div class="row g-3">

            <div class="col-md-4">
                <div class="health-box danger">

                    <label for="allergies"
                           class="form-label small fw-semibold text-danger">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Allergies & Drug Reactions
                    </label>

                    <textarea class="form-control text-danger border-danger border-opacity-50"
                              id="allergies"
                              name="allergies"
                              rows="4"><?= esc($patient['allergies']) ?></textarea>

                </div>
            </div>

            <div class="col-md-4">
                <div class="health-box">

                    <label for="medical_history"
                           class="form-label small fw-semibold">

                        <i class="bi bi-activity me-1"></i>
                        Previous Medical History

                    </label>

                    <textarea class="form-control"
                              id="medical_history"
                              name="medical_history"
                              rows="4"><?= esc($patient['medical_history']) ?></textarea>

                </div>
            </div>

            <div class="col-md-4">
                <div class="health-box">

                    <label for="family_history"
                           class="form-label small fw-semibold">

                        <i class="bi bi-diagram-3 me-1"></i>
                        Family Health History

                    </label>

                    <textarea class="form-control"
                              id="family_history"
                              name="family_history"
                              rows="4"><?= esc($patient['family_history']) ?></textarea>

                </div>
            </div>

        </div>

        <!-- =========================
             ACTIONS
             ========================= -->
        <div class="d-flex justify-content-end gap-2 pt-4 mt-3 border-top">

            <a href="<?= site_url('/admin/patients') ?>"
               class="btn btn-outline-secondary btn-sm px-4">
                Cancel
            </a>

            <button type="submit"
                    class="btn btn-primary btn-sm px-4 shadow-sm">

                <i class="bi bi-check2-circle me-1"></i>
                Update Patient

            </button>

        </div>

    </form>

</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>