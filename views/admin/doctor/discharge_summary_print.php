<?php 
$activePage = 'doctor_discharge';
include VIEWS_PATH . '/layout/doctor_header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <div>
        <h4 class="fw-bold text-slate mb-1">Print Inpatient Discharge Summary</h4>
        <p class="text-muted small mb-0">Discharge Summary #<?= esc((string)$summary['id']) ?></p>
    </div>
    <div>
        <button onclick="window.print();" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm me-2">
            <i class="bi bi-printer me-1"></i> Print Summary
        </button>
        <a href="<?= site_url('/doctor/discharge') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="card border-0 shadow-lg rounded-4 p-5" id="printable-summary">
    <div class="border-bottom pb-4 mb-4 text-center">
        <h3 class="fw-bold text-primary mb-1">CLINICAL DISCHARGE SUMMARY</h3>
        <p class="text-muted small mb-0">Multi-Specialty Healthcare & Inpatient Center</p>
    </div>

    <div class="row mb-4">
        <div class="col-6">
            <p class="mb-1"><strong>Patient Name:</strong> <?= esc($summary['patient_name']) ?></p>
            <p class="mb-1"><strong>Patient ID Code:</strong> <?= esc($summary['patient_code']) ?></p>
            <p class="mb-1"><strong>Gender / DOB:</strong> <?= ucfirst(esc($summary['gender'] ?? 'N/A')) ?> (<?= date('d M Y', strtotime($summary['dob'] ?? 'now')) ?>)</p>
            <p class="mb-0"><strong>Contact:</strong> <?= esc($summary['patient_phone'] ?? 'N/A') ?></p>
        </div>
        <div class="col-6 text-end">
            <p class="mb-1"><strong>Admission Date:</strong> <?= date('d M Y, h:i A', strtotime($summary['admission_date'])) ?></p>
            <p class="mb-1"><strong>Discharge Date:</strong> <?= $summary['discharge_date'] ? date('d M Y, h:i A', strtotime($summary['discharge_date'])) : 'Pending Checkout' ?></p>
            <p class="mb-1"><strong>Branch:</strong> <?= esc($summary['branch_name'] ?? 'Main Branch') ?></p>
            <p class="mb-0"><strong>Attending Doctor:</strong> Dr. <?= esc($summary['doctor_name']) ?></p>
        </div>
    </div>

    <hr>

    <div class="mb-3">
        <h6 class="fw-bold text-primary border-bottom pb-1">1. Final Clinical Diagnosis</h6>
        <p class="text-slate mb-0"><?= nl2br(esc($summary['diagnosis'] ?? 'N/A')) ?></p>
    </div>

    <div class="mb-3">
        <h6 class="fw-bold text-primary border-bottom pb-1">2. Hospital Stay & Treatment Summary</h6>
        <p class="text-slate mb-0"><?= nl2br(esc($summary['treatment_summary'] ?? 'N/A')) ?></p>
    </div>

    <?php if (!empty($summary['procedure_summary'])): ?>
        <div class="mb-3">
            <h6 class="fw-bold text-primary border-bottom pb-1">3. Procedure Notes</h6>
            <p class="text-slate mb-0"><?= nl2br(esc($summary['procedure_summary'])) ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($summary['operation_notes'])): ?>
        <div class="mb-3">
            <h6 class="fw-bold text-primary border-bottom pb-1">4. Surgical / Operation Notes</h6>
            <p class="text-slate mb-0"><?= nl2br(esc($summary['operation_notes'])) ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($summary['medicine_advice'])): ?>
        <div class="mb-3">
            <h6 class="fw-bold text-primary border-bottom pb-1">5. Medicine Advice at Discharge</h6>
            <p class="text-slate mb-0"><?= nl2br(esc($summary['medicine_advice'])) ?></p>
        </div>
    <?php endif; ?>

    <div class="mb-3">
        <h6 class="fw-bold text-primary border-bottom pb-1">6. Dietary & Activity Instructions</h6>
        <p class="text-slate mb-0"><?= nl2br(esc($summary['diet'] ?: 'Standard light healthy diet. Avoid spicy and fried foods.')) ?></p>
    </div>

    <div class="mb-4">
        <h6 class="fw-bold text-primary border-bottom pb-1">7. Follow-Up Advice</h6>
        <p class="text-slate mb-0"><?= nl2br(esc($summary['follow_up_instructions'] ?: $summary['advice'] ?: 'Follow up after 7 days or in case of emergency.')) ?></p>
    </div>

    <div class="row pt-5 mt-5">
        <div class="col-6">
            <p class="mb-0 text-muted small">Hospital Administrative Seal</p>
        </div>
        <div class="col-6 text-end">
            <div class="fw-bold text-slate">Dr. <?= esc($summary['doctor_name']) ?></div>
            <p class="mb-0 text-muted small">Attending Medical Officer Signature</p>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, .sidebar, .admin-header-bar { display: none !important; }
    .admin-content { margin-left: 0 !important; padding: 0 !important; }
    #printable-summary { border: none !important; shadow: none !important; }
}
</style>

<?php include VIEWS_PATH . '/layout/doctor_footer.php'; ?>