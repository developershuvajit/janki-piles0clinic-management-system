<?php 
$activePage = 'doctor_dashboard';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Print Controls -->
<div class="d-print-none text-end mb-4">
    <button onclick="window.print()" class="btn btn-primary btn-sm px-4 shadow-sm">
        <i class="bi bi-printer-fill me-1"></i> Print Certificate
    </button>
    <a href="<?= site_url('/admin/doctor/certificates') ?>" class="btn btn-outline-secondary btn-sm px-3 ms-2">
        Back to Certificates
    </a>
</div>

<!-- Certificate Sheet Container -->
<div class="card border-0 shadow-sm p-5 certificate-print-container mx-auto" style="max-width: 750px; background: #fff; border: 5px double #dee2e6 !important;">
    
    <!-- Clinic Header -->
    <div class="text-center mb-5 pb-3 border-bottom">
        <h2 class="fw-bold text-slate mb-1"><i class="bi bi-heart-pulse-fill text-success me-1"></i>MEDCLINIC HEALTHCARE</h2>
        <p class="text-muted small mb-0">Certified Outpatient & Inpatient Clinical Services Console</p>
    </div>

    <!-- Certificate Title -->
    <div class="text-center mb-5">
        <h4 class="fw-bold text-slate text-uppercase" style="letter-spacing: 2px; text-decoration: underline;">Medical Certificate of Fitness / Sickness</h4>
        <div class="text-muted small mt-1">Certificate Reference: <strong>MC-<?= esc(sprintf("%04d", $cert['id'])) ?></strong></div>
    </div>

    <!-- Certificate Body -->
    <div class="text-slate mb-5" style="line-height: 2; font-size: 1.05rem;">
        <p>This is to certify that <strong><?= esc($cert['patient_name']) ?></strong>, 
        Patient ID: <strong><?= esc($cert['patient_code']) ?></strong>, 
        Age/Gender: <strong><?= esc(date('Y') - date('Y', strtotime($cert['dob']))) ?> Yrs / <?= esc(ucfirst($cert['gender'])) ?></strong>, 
        was under my professional clinical treatment and observation.</p>

        <p>The patient was diagnosed with <strong><?= esc($cert['diagnosis']) ?></strong> and required a period of clinical recovery.</p>

        <p>Accordingly, I have advised sick leave for recovery starting from <strong><?= esc($cert['start_date']) ?></strong> 
        to <strong><?= esc($cert['end_date']) ?></strong>. 
        During this timeframe, the patient was advised to follow: <em><?= esc($cert['reason']) ?></em>.</p>
    </div>

    <!-- Date and Signature columns -->
    <div class="row pt-5 mt-5">
        <div class="col-6">
            <div class="small text-muted">Date of Issue:</div>
            <div class="fw-bold text-slate"><?= esc(date('Y-m-d', strtotime($cert['created_at']))) ?></div>
        </div>
        <div class="col-6 text-end">
            <div class="d-inline-block text-center border-top border-secondary pt-2" style="width: 200px;">
                <div class="fw-bold text-slate">Dr. <?= esc($cert['doctor_name']) ?></div>
                <div class="small text-muted">Attending Practitioner Signature</div>
            </div>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
