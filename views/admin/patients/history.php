<?php
$activePage = 'patients';
include VIEWS_PATH . '/layout/admin_header.php';
?>

<style>
.patient-card {
    border: 0;
    border-radius: 12px;
    box-shadow: 0 4px 18px rgba(15, 23, 42, .06);
}

.patient-profile {
    border-bottom: 1px solid #eef2f7;
    padding-bottom: 18px;
    margin-bottom: 14px;
}

.patient-avatar {
    width: 58px;
    height: 58px;
    border-radius: 12px;
    background: #ecfdf5;
    color: #059669;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin: 0 auto 10px;
}

.patient-id {
    background: #f8fafc;
    color: #64748b;
    border: 1px solid #e2e8f0;
    padding: 5px 11px;
    border-radius: 20px;
    font-size: .75rem;
    font-weight: 600;
}

.info-list {
    font-size: .82rem;
}

.info-list .list-group-item {
    border-color: #f1f5f9;
    padding: 10px 0;
}

.info-label {
    display: block;
    color: #334155;
    font-weight: 600;
    margin-bottom: 2px;
}

.info-value {
    color: #64748b;
    line-height: 1.45;
}

.allergy-alert {
    background: #fff7f7;
    border: 1px solid #fecaca;
    border-left: 4px solid #dc2626;
    border-radius: 8px;
    padding: 10px 12px;
    font-size: .8rem;
    color: #991b1b;
}

.card-title {
    font-size: .95rem;
    font-weight: 700;
    color: #0f172a;
}

.upload-box {
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 9px;
    padding: 12px;
}

.document-list {
    max-height: 250px;
    overflow-y: auto;
}

.document-item {
    border-bottom: 1px solid #f1f5f9;
    padding: 9px 0;
    font-size: .78rem;
}

.document-item:last-child {
    border-bottom: 0;
}

.timeline-wrapper {
    position: relative;
    padding-left: 28px;
    margin-left: 7px;
    border-left: 2px solid #eef2f7;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-dot {
    position: absolute;
    left: -36px;
    top: 14px;
    width: 13px;
    height: 13px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #f1f5f9;
}

.timeline-content {
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 10px;
    padding: 14px;
}

.timeline-title {
    font-size: .88rem;
    font-weight: 700;
    color: #0f172a;
}

.timeline-date {
    font-size: .72rem;
    color: #64748b;
}

.timeline-doctor {
    font-size: .76rem;
    color: #64748b;
    margin-top: 4px;
}

.timeline-detail {
    color: #334155;
    font-size: .8rem;
    line-height: 1.5;
    white-space: pre-line;
    margin-top: 8px;
}

.empty-state {
    padding: 55px 20px;
    text-align: center;
    color: #94a3b8;
}

.empty-state i {
    font-size: 2rem;
    display: block;
    margin-bottom: 8px;
}

@media (max-width: 991px) {
    .timeline-content {
        padding: 12px;
    }
}
</style>

<!-- ============================================
     PATIENT HISTORY PAGE
     ============================================ -->
<div class="row g-4 mt-1">

    <!-- ========================================
         LEFT COLUMN
         ======================================== -->
    <div class="col-lg-4">

        <!-- Patient Profile -->
        <div class="card patient-card p-4 mb-4">

            <div class="patient-profile text-center">

                <div class="patient-avatar">
                    <i class="bi bi-person"></i>
                </div>

                <h4 class="fw-bold text-slate mb-2">
                    <?= esc($patient['name']) ?>
                </h4>

                <span class="patient-id">
                    ID: <?= esc($patient['patient_id']) ?>
                </span>

            </div>

            <!-- Allergy Alert -->
            <?php if (!empty($patient['allergies'])): ?>

                <div class="allergy-alert mb-3">
                    <div class="fw-bold mb-1">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Critical Allergies
                    </div>

                    <?= esc($patient['allergies']) ?>
                </div>

            <?php endif; ?>

            <!-- Patient Information -->
            <ul class="list-group list-group-flush info-list mb-0">

                <li class="list-group-item">
                    <span class="info-label">Gender / Age</span>

                    <span class="info-value">
                        <?= esc(ucfirst($patient['gender'])) ?>
                        &bull;
                        <?= esc(date('Y') - date('Y', strtotime($patient['dob']))) ?> Years
                        <br>
                        DOB: <?= esc($patient['dob']) ?>
                    </span>
                </li>

                <li class="list-group-item">
                    <span class="info-label">Phone / Email</span>

                    <span class="info-value">
                        <?= esc($patient['phone']) ?><br>
                        <?= esc($patient['email'] ?: 'No email registered') ?>
                    </span>
                </li>

                <li class="list-group-item">
                    <span class="info-label">Physical Address</span>

                    <span class="info-value">
                        <?= esc($patient['address']) ?>
                    </span>
                </li>

                <li class="list-group-item">
                    <span class="info-label">Medical History</span>

                    <span class="info-value">
                        <?= esc($patient['medical_history'] ?: 'No previous history recorded') ?>
                    </span>
                </li>

                <li class="list-group-item">
                    <span class="info-label">Family History</span>

                    <span class="info-value">
                        <?= esc($patient['family_history'] ?: 'None') ?>
                    </span>
                </li>

            </ul>

        </div>


        <!-- ====================================
             DOCUMENT UPLOAD
             ==================================== -->
        <div class="card patient-card p-4 mb-4">

            <div class="card-title mb-3">
                <i class="bi bi-folder2-open text-success me-2"></i>
                Patient Documents
            </div>

            <div class="upload-box">

                <form action="<?= site_url('/admin/patients/upload-doc/' . $patient['id']) ?>"
                      method="POST"
                      enctype="multipart/form-data">

                    <?= csrf_field() ?>

                    <div class="mb-2">

                        <input type="file"
                               name="report"
                               class="form-control form-control-sm"
                               required
                               accept=".pdf,.doc,.docx,image/*">

                    </div>

                    <button type="submit"
                            class="btn btn-primary btn-sm w-100">

                        <i class="bi bi-cloud-arrow-up me-1"></i>
                        Upload Document

                    </button>

                </form>

            </div>


            <!-- Document List -->
            <div class="document-list mt-3">

                <div class="fw-semibold text-slate small mb-2">
                    Uploaded Files
                </div>

                <?php if (empty($documents)): ?>

                    <div class="text-muted text-center py-3"
                         style="font-size:.78rem;">
                        <i class="bi bi-folder2-open d-block fs-5 mb-1"></i>
                        No reports uploaded.
                    </div>

                <?php else: ?>

                    <?php foreach ($documents as $doc): ?>

                        <div class="document-item d-flex align-items-center justify-content-between">

                            <div class="text-truncate me-2"
                                 style="max-width:220px;">

                                <a href="<?= site_url($doc['file_path']) ?>"
                                   target="_blank"
                                   class="text-decoration-none fw-semibold text-slate">

                                    <i class="bi bi-file-earmark-text text-danger me-1"></i>
                                    <?= esc($doc['document_name']) ?>

                                </a>

                            </div>

                            <a href="<?= site_url('/admin/patients/delete-doc/' . $doc['id']) ?>"
                               class="text-danger"
                               onclick="return confirm('Delete this file?');"
                               title="Delete">

                                <i class="bi bi-trash-fill"></i>

                            </a>

                        </div>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

        </div>

    </div>


    <!-- ========================================
         RIGHT COLUMN - TIMELINE
         ======================================== -->
    <div class="col-lg-8">

        <div class="card patient-card p-4">

            <div class="d-flex align-items-center justify-content-between mb-4">

                <div>
                    <h5 class="card-title mb-1">
                        <i class="bi bi-clock-history text-success me-2"></i>
                        Medical Visit Timeline
                    </h5>

                    <small class="text-muted">
                        Patient consultations, prescriptions and admissions
                    </small>
                </div>

            </div>


            <?php if (empty($timeline)): ?>

                <div class="empty-state">

                    <i class="bi bi-calendar-x"></i>

                    <div>
                        No visits, prescriptions, or IPD admissions
                        logged for this patient yet.
                    </div>

                </div>

            <?php else: ?>


                <!-- Vertical Timeline -->
                <div class="timeline-wrapper">

                    <?php foreach ($timeline as $item): ?>

                        <div class="timeline-item">

                            <!-- Timeline Dot -->
                            <span class="timeline-dot bg-<?= $item['badge'] ?>"></span>


                            <!-- Timeline Content -->
                            <div class="timeline-content">

                                <div class="d-flex justify-content-between align-items-start gap-3">

                                    <div class="timeline-title">
                                        <?= esc($item['title']) ?>
                                    </div>

                                    <div class="timeline-date text-nowrap">
                                        <?= esc($item['date_display']) ?>
                                    </div>

                                </div>

                                <div class="timeline-doctor">
                                    <i class="bi bi-person-badge me-1"></i>
                                    Attending Doctor:
                                    <strong>
                                        Dr. <?= esc($item['doctor']) ?>
                                    </strong>
                                </div>

                                <div class="timeline-detail">
                                    <?= $item['detail'] ?>
                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>