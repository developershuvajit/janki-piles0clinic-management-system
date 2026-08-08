<?php 
$activePage = 'doctor_reports';
include VIEWS_PATH . '/layout/doctor_header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-slate mb-1">Doctor Clinical Activity Reports</h4>
        <p class="text-muted small mb-0">Monthly OPD, IPD, and consultation metrics</p>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card p-4 border-0 shadow-sm rounded-4 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase small fw-bold mb-1">Monthly OPD Consults</h6>
                    <h2 class="fw-bold text-primary mb-0"><?= esc((string)$monthly_opd) ?></h2>
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary fs-3">
                    <i class="bi bi-stethoscope"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card p-4 border-0 shadow-sm rounded-4 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase small fw-bold mb-1">Monthly IPD Admissions</h6>
                    <h2 class="fw-bold text-info mb-0"><?= esc((string)$monthly_ipd) ?></h2>
                </div>
                <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info fs-3">
                    <i class="bi bi-hospital"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card p-4 border-0 shadow-sm rounded-4 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase small fw-bold mb-1">Total Prescriptions Written</h6>
                    <h2 class="fw-bold text-success mb-0"><?= esc((string)$total_consultations) ?></h2>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success fs-3">
                    <i class="bi bi-file-earmark-medical"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/doctor_footer.php'; ?>
