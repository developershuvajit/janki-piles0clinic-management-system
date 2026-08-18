<?php 
$activePage = 'doctor_ipd';
include VIEWS_PATH . '/layout/doctor_header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4 mx-4 mt-4">
    <div>
        <h4 class="fw-bold text-slate mb-1">Daily Visit & Vitals Progress Notes</h4>
        <p class="text-muted small mb-0">Patient: <strong><?= esc($admission['patient_name']) ?></strong> 
            (<?= esc($admission['patient_code'] ?? 'N/A') ?>)
            <?php if (!empty($admission['branch_name'])): ?>
                | Branch: <?= esc($admission['branch_name']) ?>
            <?php endif; ?>
        </p>
    </div>
    <a href="<?= site_url('/doctor/ipd') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Back to IPD List
    </a>
</div>

<div class="row">
    <!-- Patient Info Card -->
    <div class="col-lg-12 mb-3">
        <div class="card border-0 shadow-sm rounded-4 bg-light">
            <div class="card-body py-2 px-4">
                <div class="row text-center">
                    <div class="col-3">
                        <small class="text-muted d-block">Diagnosis</small>
                        <span class="fw-semibold"><?= esc($admission['diagnosis']) ?></span>
                    </div>
                    <div class="col-3">
                        <small class="text-muted d-block">Admission Date</small>
                        <span class="fw-semibold"><?= date('d M Y', strtotime($admission['admission_date'])) ?></span>
                    </div>
                    <div class="col-3">
                        <small class="text-muted d-block">Attending Doctor</small>
                        <span class="fw-semibold">Dr. <?= esc($admission['doctor_name']) ?></span>
                    </div>
                    <div class="col-3">
                        <small class="text-muted d-block">Status</small>
                        <span class="badge bg-warning bg-opacity-10 text-warning">Admitted</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Log Entry Form -->
    <div class="col-lg-5 mb-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-primary text-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-plus-circle me-2"></i> Record Daily Clinical Progress Note</h6>
            </div>
            <div class="card-body p-4">
                <form action="<?= site_url('/doctor/ipd/visit-notes/' . $admission['id'] . '/save') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Temp (°F)</label>
                            <input type="text" class="form-control" name="vit_temp" placeholder="98.6">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">BP (mmHg)</label>
                            <input type="text" class="form-control" name="vit_bp" placeholder="120/80">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Pulse (bpm)</label>
                            <input type="text" class="form-control" name="vit_pulse" placeholder="72">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Clinical Progress & Round Notes <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="notes" rows="4" placeholder="Enter patient recovery progress, vitals evaluation, and medical instructions..." required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-bold">
                        <i class="bi bi-check-circle me-1"></i> Save Visit Note
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- History Timeline -->
    <div class="col-lg-7 mb-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i> Nursing & Visit Log History</h6>
                <span class="badge bg-secondary"><?= count($nursing_logs ?? []) ?> records</span>
            </div>
            <div class="card-body p-4" style="max-height: 450px; overflow-y: auto;">
                <?php if (empty($nursing_logs)): ?>
                    <p class="text-muted text-center py-4 mb-0">No visit notes recorded yet.</p>
                <?php else: ?>
                    <?php foreach ($nursing_logs as $log): ?>
                        <div class="p-3 bg-light rounded-3 mb-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <span class="badge bg-danger bg-opacity-10 text-danger me-1">Temp: <?= esc($log['vit_temp'] ?: 'N/A') ?></span>
                                    <span class="badge bg-primary bg-opacity-10 text-primary me-1">BP: <?= esc($log['vit_bp'] ?: 'N/A') ?></span>
                                    <span class="badge bg-success bg-opacity-10 text-success">Pulse: <?= esc($log['vit_pulse'] ?: 'N/A') ?></span>
                                </div>
                                <span class="small text-muted"><i class="bi bi-clock me-1"></i> <?= date('d M Y, h:i A', strtotime($log['recorded_at'])) ?></span>
                            </div>
                            <p class="mb-0 text-slate small"><?= esc($log['notes']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/doctor_footer.php'; ?>