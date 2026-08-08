<?php 
$activePage = 'doctor_patients';
include VIEWS_PATH . '/layout/doctor_header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-slate mb-1">Clinical Timeline — <?= esc($patient['name']) ?></h4>
        <p class="text-muted small mb-0">Code: <strong><?= esc($patient['patient_id']) ?></strong> | Gender: <?= ucfirst(esc($patient['gender'])) ?> | Phone: <?= esc($patient['phone']) ?></p>
    </div>
    <a href="<?= site_url('/doctor/patients') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Back to Directory
    </a>
</div>

<div class="row">
    <!-- Clinical Details Side Card -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-person-vcard me-2"></i> Patient Information</h6>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>DOB:</strong> <?= date('d M Y', strtotime($patient['dob'])) ?></p>
                <p class="mb-2"><strong>Blood Group:</strong> <span class="badge bg-danger bg-opacity-10 text-danger"><?= esc($patient['blood_group'] ?: 'Not Specified') ?></span></p>
                <p class="mb-2"><strong>Emergency Contact:</strong> <?= esc($patient['emergency_contact'] ?: 'None') ?></p>
                <p class="mb-2"><strong>Known Allergies:</strong> <span class="text-danger fw-bold"><?= esc($patient['allergies'] ?: 'None Reported') ?></span></p>
                <p class="mb-0"><strong>Medical History:</strong> <?= esc($patient['medical_history'] ?: 'None Reported') ?></p>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-light py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-receipt me-2"></i> Previous Bills (Read Only)</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php if (empty($bills)): ?>
                        <li class="list-group-item small text-muted text-center py-3">No billing records on file.</li>
                    <?php else: ?>
                        <?php foreach ($bills as $b): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold small">#INV-<?= esc((string)$b['id']) ?> (<?= strtoupper(esc($b['type'])) ?>)</div>
                                    <div class="small text-muted"><?= date('d M Y', strtotime($b['created_at'])) ?></div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-slate">₹<?= number_format((float)$b['total'], 2) ?></div>
                                    <span class="badge bg-<?= $b['payment_status'] === 'paid' ? 'success' : 'warning' ?> bg-opacity-10 text-<?= $b['payment_status'] === 'paid' ? 'success' : 'warning' ?> small">
                                        <?= ucfirst(esc($b['payment_status'])) ?>
                                    </span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Timeline & Prescriptions List -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-light py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-file-earmark-medical me-2"></i> Previous Prescriptions</h6>
            </div>
            <div class="card-body p-4">
                <?php if (empty($prescriptions)): ?>
                    <p class="text-muted text-center py-4 mb-0">No previous prescriptions recorded.</p>
                <?php else: ?>
                    <?php foreach ($prescriptions as $pr): ?>
                        <div class="p-3 bg-light rounded-3 mb-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary px-2.5 py-1">Prescription #<?= esc((string)$pr['id']) ?></span>
                                <span class="small text-muted"><i class="bi bi-calendar3 me-1"></i> <?= date('d M Y, h:i A', strtotime($pr['created_at'])) ?></span>
                            </div>
                            <p class="mb-1"><strong>Symptoms:</strong> <?= esc($pr['symptoms']) ?></p>
                            <p class="mb-1"><strong>Diagnosis:</strong> <?= esc($pr['diagnosis']) ?></p>
                            <p class="mb-2"><strong>Treatment Advice:</strong> <?= esc($pr['treatment']) ?></p>
                            <a href="<?= site_url('/doctor/prescriptions/print/' . $pr['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="bi bi-printer me-1"></i> Print Prescription
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/doctor_footer.php'; ?>
