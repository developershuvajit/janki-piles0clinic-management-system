<?php 
$activePage = 'patients';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Timeline Columns Grid -->
<div class="row">
    <!-- Left Column: Patient Profile Card & Document Uploader -->
    <div class="col-lg-4 mb-4">
        <!-- Demographics Card -->
        <div class="card border-0 shadow-sm p-4 mb-4">
            <div class="text-center mb-3">
                <?php if ($patient['qr_code_url']): ?>
                    <img src="<?= site_url($patient['qr_code_url']) ?>" alt="QR" class="rounded border p-1 mb-2 shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                <?php endif; ?>
                <h4 class="fw-bold text-slate mb-1"><?= esc($patient['name']) ?></h4>
                <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill fw-semibold mb-2">ID: <?= esc($patient['patient_id']) ?></span>
            </div>

            <!-- Health Alerts / Allergies -->
            <?php if (!empty($patient['allergies'])): ?>
                <div class="alert alert-danger p-2.5 small mb-3 border-start border-4 border-danger rounded-3">
                    <div class="fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> CRITICAL ALLERGIES:</div>
                    <div class="mt-1"><?= esc($patient['allergies']) ?></div>
                </div>
            <?php endif; ?>

            <ul class="list-group list-group-flush mb-2" style="font-size: 0.82rem;">
                <li class="list-group-item px-0 py-2.5">
                    <strong>Gender / Age:</strong><br>
                    <span class="text-muted"><?= esc(ucfirst($patient['gender'])) ?> &bull; <?= esc(date('Y') - date('Y', strtotime($patient['dob']))) ?> Years (DOB: <?= esc($patient['dob']) ?>)</span>
                </li>
                <li class="list-group-item px-0 py-2.5">
                    <strong>Phone / Email:</strong><br>
                    <span class="text-muted"><?= esc($patient['phone']) ?><br><?= esc($patient['email'] ?: 'No email registered') ?></span>
                </li>
                <li class="list-group-item px-0 py-2.5">
                    <strong>Physical Address:</strong><br>
                    <span class="text-muted"><?= esc($patient['address']) ?></span>
                </li>
                <li class="list-group-item px-0 py-2.5">
                    <strong>Medical History:</strong><br>
                    <span class="text-muted"><?= esc($patient['medical_history'] ?: 'No previous history recorded') ?></span>
                </li>
                <li class="list-group-item px-0 py-2.5">
                    <strong>Family History:</strong><br>
                    <span class="text-muted"><?= esc($patient['family_history'] ?: 'None') ?></span>
                </li>
            </ul>
        </div>

        <!-- Document Uploader Card -->
        <div class="card border-0 shadow-sm p-4 mb-4">
            <h6 class="fw-bold text-slate mb-3"><i class="bi bi-upload text-success me-2"></i>Upload Lab Report / PDF</h6>
            <form action="<?= site_url('/admin/patients/upload-doc/' . $patient['id']) ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <input type="file" name="report" class="form-control form-control-sm" required accept=".pdf,.doc,.docx,image/*">
                </div>
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-cloud-arrow-up-fill"></i> Upload Document
                </button>
            </form>

            <!-- Document List -->
            <div class="list-group list-group-flush mt-3" style="max-height: 250px; overflow-y: auto;">
                <div class="fw-semibold text-slate small mb-2">Patient Files Checklist:</div>
                <?php if (empty($documents)): ?>
                    <span class="text-muted x-small py-2 text-center">No reports uploaded.</span>
                <?php else: ?>
                    <?php foreach ($documents as $doc): ?>
                        <div class="list-group-item px-0 py-2 d-flex align-items-center justify-content-between" style="font-size: 0.78rem;">
                            <div class="text-truncate me-2" style="max-width: 170px;">
                                <a href="<?= site_url($doc['file_path']) ?>" target="_blank" class="text-decoration-none fw-semibold text-slate">
                                    <i class="bi bi-file-earmark-pdf text-danger me-1"></i> <?= esc($doc['document_name']) ?>
                                </a>
                            </div>
                            <a href="<?= site_url('/admin/patients/delete-doc/' . $doc['id']) ?>" class="text-danger" onclick="return confirm('Delete this file?');" title="Delete">
                                <i class="bi bi-trash-fill"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Column: Medical History visit timeline -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold text-slate mb-4"><i class="bi bi-clock-history text-success me-2"></i>Chronological visit timeline</h5>

            <?php if (empty($timeline)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                    No visits, prescriptions, or IPD admissions logged for this patient yet.
                </div>
            <?php else: ?>
                <!-- Vertical Timeline -->
                <div class="position-relative ps-4 border-start border-2 border-light ms-2">
                    <?php foreach ($timeline as $item): ?>
                        <div class="position-relative mb-4">
                            <!-- Timeline Indicator Dot -->
                            <span class="position-absolute translate-middle bg-<?= $item['badge'] ?> rounded-circle" style="left: -25px; top: 12px; width: 12px; height: 12px; border: 3px solid #fff; box-shadow: 0 0 0 2px var(--bg-light, #f8fafc);"></span>
                            
                            <!-- Card Container -->
                            <div class="card border-0 bg-light p-3 shadow-none rounded-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="fw-bold text-slate mb-0"><?= esc($item['title']) ?></h6>
                                    <small class="text-muted" style="font-size: 0.72rem;"><?= esc($item['date_display']) ?></small>
                                </div>
                                <div class="text-muted small mb-2">Attending Doctor: <strong>Dr. <?= esc($item['doctor']) ?></strong></div>
                                <div class="text-slate small" style="white-space: pre-line; line-height: 1.45; font-size: 0.8rem;"><?= $item['detail'] ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
