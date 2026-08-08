<?php 
$activePage = 'ipd';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Print Controls -->
<div class="d-print-none text-end mb-4">
    <a href="<?= site_url('/admin/ipd/discharge-summary/pdf/' . $summary['ipd_admission_id']) ?>" class="btn btn-danger btn-sm px-3 shadow-sm me-1">
        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF
    </a>
    <button onclick="window.print()" class="btn btn-primary btn-sm px-4 shadow-sm">
        <i class="bi bi-printer-fill me-1"></i> Print Summary
    </button>
    <a href="<?= site_url('/admin/ipd') ?>" class="btn btn-outline-secondary btn-sm px-3 ms-2">
        Back to Admissions
    </a>
</div>

<!-- Discharge Summary Container -->
<div class="card border-0 shadow-sm p-5 discharge-print-container mx-auto text-slate" style="max-width: 800px; background: #fff; border: 4px double #dee2e6 !important;">
    
    <!-- Clinic Header -->
    <div class="text-center mb-4 pb-3 border-bottom">
        <h3 class="fw-bold text-slate mb-1"><i class="bi bi-heart-pulse-fill text-success me-1"></i>MEDCLINIC HEALTHCARE</h3>
        <p class="text-muted small mb-0">Multi-Specialty Clinical Inpatient Admissions Center</p>
    </div>

    <!-- Title -->
    <div class="text-center mb-4">
        <h4 class="fw-bold text-uppercase" style="letter-spacing: 1.5px; text-decoration: underline;">Clinical Discharge Summary</h4>
    </div>

    <!-- Case file details -->
    <div class="row g-2 mb-4 bg-light p-3 rounded-3 mx-0 small">
        <div class="col-6">
            <strong>Patient Name:</strong> <?= esc($summary['patient_name']) ?><br>
            <strong>Patient Code:</strong> <?= esc($summary['patient_code']) ?><br>
            <strong>Age/Gender:</strong> <?= esc(date('Y') - date('Y', strtotime($summary['dob']))) ?> Yrs / <?= esc(ucfirst($summary['gender'])) ?>
        </div>
        <div class="col-6 text-end">
            <strong>Admission Date:</strong> <?= esc($summary['admission_date']) ?><br>
            <strong>Discharge Date:</strong> <?= esc($summary['discharge_date']) ?><br>
            <strong>Room / Bed:</strong> Room <?= esc($summary['room_number']) ?> (Bed: <?= esc($summary['bed_number']) ?>)
        </div>
    </div>

    <!-- Summary Details -->
    <div class="mb-4">
        <h6 class="fw-bold text-slate bg-light p-2 border-start border-3 border-success mb-2">Final Diagnosis</h6>
        <div class="small ps-2 mb-3" style="white-space: pre-line;"><?= esc($summary['diagnosis']) ?></div>

        <h6 class="fw-bold text-slate bg-light p-2 border-start border-3 border-success mb-2">Treatment & Procedures Summary</h6>
        <div class="small ps-2 mb-3" style="white-space: pre-line;"><?= esc($summary['treatment_summary']) ?></div>

        <h6 class="fw-bold text-slate bg-light p-2 border-start border-3 border-success mb-2">Discharge Advice & Home Medications</h6>
        <div class="small ps-2 mb-3" style="white-space: pre-line;"><?= esc($summary['advice'] ?: 'No home medications advised.') ?></div>

        <h6 class="fw-bold text-slate bg-light p-2 border-start border-3 border-success mb-2">Recommended Diet</h6>
        <div class="small ps-2 mb-3" style="white-space: pre-line;"><?= esc($summary['diet'] ?: 'Standard normal diet.') ?></div>

        <h6 class="fw-bold text-slate bg-light p-2 border-start border-3 border-success mb-2">Follow-Up Instructions</h6>
        <div class="small ps-2 mb-3" style="white-space: pre-line;"><?= esc($summary['follow_up_instructions'] ?: 'Return to OPD clinic in case of symptoms recurrence.') ?></div>
    </div>

    <!-- Stamp and Signatures -->
    <div class="row pt-5 align-items-end">
        <div class="col-6">
            <?php if (!empty($summary['hospital_seal'])): ?>
                <img src="<?= site_url($summary['hospital_seal']) ?>" alt="Hospital Seal" style="max-height: 80px; display: block; margin-bottom: 5px;">
            <?php endif; ?>
            <div class="small text-muted border-top border-light d-inline-block pt-1">Hospital Clinic Seal Stamp</div>
        </div>
        <div class="col-6 text-end">
            <?php if (!empty($summary['doctor_signature'])): ?>
                <img src="<?= site_url($summary['doctor_signature']) ?>" alt="Doctor Signature" style="max-height: 50px; display: inline-block; margin-bottom: 5px;">
            <?php endif; ?>
            <div class="fw-bold text-slate mt-1">Dr. <?= esc($summary['doctor_name']) ?></div>
            <div class="small text-muted border-top border-light pt-1">Attending Consultant Signature</div>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
