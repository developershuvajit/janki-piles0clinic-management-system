<?php 
$activePage = 'doctor_prescriptions';
include VIEWS_PATH . '/layout/doctor_header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <div>
        <h4 class="fw-bold text-slate mb-1">Print Clinical Prescription</h4>
        <p class="text-muted small mb-0">Prescription #RX-<?= esc((string)$prescription['id']) ?></p>
    </div>
    <div>
        <button onclick="window.print();" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm me-2">
            <i class="bi bi-printer me-1"></i> Print RX
        </button>
        <a href="<?= site_url('/doctor/prescriptions') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="card border-0 shadow-lg rounded-4 p-5" id="printable-prescription">
    <div class="border-bottom pb-4 mb-4 text-center">
        <h3 class="fw-bold text-primary mb-1">MEDICAL PRESCRIPTION</h3>
        <p class="text-muted small mb-0">Healthcare & Outpatient Clinic</p>
    </div>

    <div class="row mb-4">
        <div class="col-6">
            <p class="mb-1"><strong>Patient Name:</strong> <?= esc($prescription['patient_name']) ?></p>
            <p class="mb-1"><strong>Patient Code:</strong> <?= esc($prescription['patient_code'] ?? 'N/A') ?></p>
            <p class="mb-0"><strong>Age / Gender:</strong> <?= ucfirst(esc($prescription['gender'] ?? 'N/A')) ?></p>
        </div>
        <div class="col-6 text-end">
            <p class="mb-1"><strong>Date:</strong> <?= date('d M Y, h:i A', strtotime($prescription['created_at'])) ?></p>
            <p class="mb-1"><strong>Prescribing Doctor:</strong> Dr. <?= esc($prescription['doctor_name']) ?></p>
            <p class="mb-0"><strong>Follow-Up Date:</strong> <?= $prescription['follow_up_date'] ? date('d M Y', strtotime($prescription['follow_up_date'])) : 'As needed' ?></p>
        </div>
    </div>

    <hr>

    <div class="mb-3">
        <p class="mb-1"><strong>Symptoms:</strong> <?= esc($prescription['symptoms']) ?></p>
        <p class="mb-1"><strong>Diagnosis:</strong> <span class="fw-bold text-primary"><?= esc($prescription['diagnosis']) ?></span></p>
        <p class="mb-0"><strong>Treatment Advice:</strong> <?= esc($prescription['treatment']) ?></p>
    </div>

    <h6 class="fw-bold text-primary mt-4 mb-3"><i class="bi bi-prescription2 me-1"></i> Prescribed Medications (Rx):</h6>
    <div class="table-responsive mb-4">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Medicine Name</th>
                    <th>Dosage</th>
                    <th>Frequency</th>
                    <th>Duration</th>
                    <th>Instructions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($medicines)): ?>
                    <tr><td colspan="6" class="text-center py-3 text-muted">No medicines listed in this prescription.</td></tr>
                <?php else: ?>
                    <?php $i = 1; foreach ($medicines as $m): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td class="fw-bold text-slate"><?= esc($m['medicine_name']) ?></td>
                            <td><?= esc($m['dosage']) ?></td>
                            <td><?= esc($m['frequency']) ?></td>
                            <td><?= esc($m['duration']) ?></td>
                            <td><?= esc($m['instructions'] ?: 'As directed') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (!empty($prescription['advice'])): ?>
        <div class="p-3 bg-light rounded-3 mb-4">
            <strong>General Instructions / Dietary Advice:</strong>
            <div><?= esc($prescription['advice']) ?></div>
        </div>
    <?php endif; ?>

    <div class="row pt-5 mt-5">
        <div class="col-6">
            <p class="mb-0 text-muted small">Digital Prescription Verification</p>
        </div>
        <div class="col-6 text-end">
            <div class="fw-bold text-slate">Dr. <?= esc($prescription['doctor_name']) ?></div>
            <p class="mb-0 text-muted small">Authorized Medical Signature</p>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, .sidebar, .admin-header-bar { display: none !important; }
    .admin-content { margin-left: 0 !important; padding: 0 !important; }
    #printable-prescription { border: none !important; shadow: none !important; }
}
</style>

<?php include VIEWS_PATH . '/layout/doctor_footer.php'; ?>
