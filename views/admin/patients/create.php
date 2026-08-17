<?php
$activePage = 'patients';
include VIEWS_PATH . '/layout/admin_header.php';
?>

<style>
    .patient-form-card {
        border: 0;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, .06);
        background: #fff;
    }

    .form-section-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 18px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eef2f7;
    }

    .form-label {
        color: #475569;
        margin-bottom: 6px;
    }

    .form-control,
    .form-select {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 13px;
        padding: .55rem .75rem;
        background: #fff;
        transition: all .2s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, .08);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 92px;
    }

    .required {
        color: #ef4444;
    }

    .branch-alert {
        border: 0;
        border-radius: 10px;
        background: #eff6ff;
        color: #1e40af;
        font-size: 13px;
    }

    .medical-box {
        border: 1px solid #eef2f7;
        border-radius: 12px;
        padding: 14px;
        background: #fafbfc;
    }

    .medical-box.danger {
        border-color: #fecaca;
        background: #fffafa;
    }

    .form-actions {
        border-top: 1px solid #eef2f7;
        margin-top: 20px;
        padding-top: 18px;
    }

    .btn-save {
        border-radius: 8px;
        font-weight: 600;
        padding: 8px 18px;
    }
</style>

<div class="card patient-form-card p-4 mt-3">

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h5 class="fw-bold text-dark mb-1">
                <i class="bi bi-person-plus-fill text-primary me-2"></i>
                Patient Registration
            </h5>
            <p class="text-muted small mb-0">
                Create a new patient clinical profile
            </p>
        </div>

        <span class="badge bg-light text-secondary border px-3 py-2">
            <i class="bi bi-clipboard2-pulse me-1"></i>
            Clinical Profile
        </span>
    </div>

    <form action="<?= site_url('/admin/patients/save') ?>" method="POST" autocomplete="off">
        <?= csrf_field() ?>

        <?php if ((isset($isBranchAdmin) && $isBranchAdmin) || (isset($isReceptionist) && $isReceptionist)): ?>
            <?php if (!empty($branchId) && !empty($branches)): ?>

                <div class="alert branch-alert alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-building me-2"></i>
                    <strong>Branch:</strong> <?= esc($branches[0]['name'] ?? '') ?>

                    <span class="badge bg-primary ms-2">
                        <?= isset($isBranchAdmin) && $isBranchAdmin ? 'Branch Admin' : 'Receptionist' ?>
                    </span>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>

                <input type="hidden" name="branch_id" value="<?= $branchId ?>">

            <?php endif; ?>
        <?php endif; ?>


        <!-- BASIC INFORMATION -->
        <div class="form-section-title">
            <i class="bi bi-person-vcard me-2 text-primary"></i>
            Basic Information
        </div>

        <div class="row g-3 mb-4">

            <div class="col-md-4">
                <label for="name" class="form-label small fw-semibold">
                    Full Patient Name <span class="required">*</span>
                </label>

                <input type="text"
                       class="form-control"
                       id="name"
                       name="name"
                       required
                       placeholder="Patient name">
            </div>


            <div class="col-md-4">
                <label for="phone" class="form-label small fw-semibold">
                    Phone Number <span class="required">*</span>
                </label>

                <input type="text"
                       class="form-control"
                       id="phone"
                       name="phone"
                       required
                       placeholder="Phone number">
            </div>


            <div class="col-md-4">
                <label for="email" class="form-label small fw-semibold">
                    Email Address
                </label>

                <input type="email"
                       class="form-control"
                       id="email"
                       name="email">
            </div>


            <div class="col-md-3">
                <label for="gender" class="form-label small fw-semibold">
                    Gender <span class="required">*</span>
                </label>

                <select class="form-select"
                        id="gender"
                        name="gender"
                        required>

                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>

                </select>
            </div>


            <div class="col-md-3">
                <label for="dob" class="form-label small fw-semibold">
                    Date of Birth <span class="required">*</span>
                </label>

                <input type="date"
                       class="form-control"
                       id="dob"
                       name="dob"
                       required
                       max="<?= date('Y-m-d') ?>">
            </div>


            <div class="col-md-3">
                <label for="blood_group" class="form-label small fw-semibold">
                    Blood Group
                </label>

                <select class="form-select"
                        id="blood_group"
                        name="blood_group">

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


            <?php if (isset($isSuperAdmin) && $isSuperAdmin): ?>

                <div class="col-md-3">
                    <label for="branch_id" class="form-label small fw-semibold">
                        Primary Branch
                    </label>

                    <select class="form-select"
                            id="branch_id"
                            name="branch_id">

                        <option value="">Headquarters / General Register</option>

                        <?php foreach ($branches as $branch): ?>

                            <option value="<?= $branch['id'] ?>">
                                <?= esc($branch['name']) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>
                </div>

            <?php endif; ?>


            <div class="col-md-9">
                <label for="address" class="form-label small fw-semibold">
                    Permanent Address <span class="required">*</span>
                </label>

                <input type="text"
                       class="form-control"
                       id="address"
                       name="address"
                       required
                       placeholder="Patient address">
            </div>


            <div class="col-md-3">
                <label for="emergency_contact" class="form-label small fw-semibold">
                    Emergency Contact
                </label>

                <input type="text"
                       class="form-control"
                       id="emergency_contact"
                       name="emergency_contact">
            </div>

        </div>


        <!-- MEDICAL INFORMATION -->
        <div class="form-section-title">
            <i class="bi bi-heart-pulse me-2 text-danger"></i>
            Medical & Family History
        </div>

        <div class="row g-3">

            <div class="col-md-4">

                <div class="medical-box danger">

                    <label for="allergies"
                           class="form-label small fw-semibold text-danger">

                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Allergies & Drug Reactions

                    </label>

                    <textarea class="form-control text-danger border-danger border-opacity-25"
                              id="allergies"
                              name="allergies"
                              rows="3"></textarea>

                </div>

            </div>


            <div class="col-md-4">

                <div class="medical-box">

                    <label for="medical_history"
                           class="form-label small fw-semibold">

                        <i class="bi bi-activity me-1 text-primary"></i>
                        Previous Medical History

                    </label>

                    <textarea class="form-control"
                              id="medical_history"
                              name="medical_history"
                              rows="3"></textarea>

                </div>

            </div>


            <div class="col-md-4">

                <div class="medical-box">

                    <label for="family_history"
                           class="form-label small fw-semibold">

                        <i class="bi bi-diagram-3 me-1 text-primary"></i>
                        Family Health History

                    </label>

                    <textarea class="form-control"
                              id="family_history"
                              name="family_history"
                              rows="3"></textarea>

                </div>

            </div>

        </div>


        <!-- ACTIONS -->
        <div class="form-actions d-flex justify-content-end">

            <a href="<?= site_url('/admin/patients') ?>"
               class="btn btn-outline-secondary btn-sm px-4 me-2">

                <i class="bi bi-x-lg me-1"></i>
                Cancel

            </a>

            <button type="submit"
                    class="btn btn-primary btn-save shadow-sm">

                <i class="bi bi-person-check-fill me-1"></i>
                Register Patient

            </button>

        </div>

    </form>

</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>