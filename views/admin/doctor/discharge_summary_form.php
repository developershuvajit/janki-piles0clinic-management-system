<?php 
$activePage = 'doctor_discharge';
include VIEWS_PATH . '/layout/doctor_header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-slate mb-1">Generate Inpatient Discharge Summary</h4>
        <p class="text-muted small mb-0">Patient: <strong><?= esc($admission['patient_name']) ?></strong> (Code: <?= esc($admission['patient_code']) ?>)</p>
    </div>
    <a href="<?= site_url('/doctor/discharge') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Back to Discharges
    </a>
</div>

<!-- Patient Info Card -->
<div class="row mb-4">
    <div class="col-12">
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
                        <small class="text-muted d-block">Branch</small>
                        <span class="fw-semibold"><?= esc($admission['branch_name'] ?? 'Main Branch') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-primary text-white py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-file-earmark-medical me-2"></i> Clinical Discharge Documentation Worksheet</h6>
    </div>
    <div class="card-body p-4">
        <form action="<?= site_url('/doctor/discharge/summary/save') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="ipd_admission_id" value="<?= esc((string)$admission['id']) ?>">

            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold">Final Clinical Diagnosis <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="diagnosis" rows="3" placeholder="Enter final confirmed diagnosis..." required><?= esc($summary['diagnosis'] ?? $admission['diagnosis']) ?></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold">Treatment Summary & Course in Hospital <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="treatment_summary" rows="3" placeholder="Summary of stay, interventions, and patient progress..." required><?= esc($summary['treatment_summary'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold">Procedure Summary</label>
                    <textarea class="form-control" name="procedure_summary" rows="3" placeholder="Details of procedures performed during stay..."><?= esc($summary['procedure_summary'] ?? '') ?></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold">Operation / Surgical Notes (if applicable)</label>
                    <textarea class="form-control" name="operation_notes" rows="3" placeholder="Surgical findings, techniques, and anesthesia details..."><?= esc($summary['operation_notes'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 mb-3">
                    <label class="form-label small fw-bold">Discharge Medicine Advice</label>
                    <textarea class="form-control" name="medicine_advice" rows="3" placeholder="Prescribed medicines at home discharge..."><?= esc($summary['medicine_advice'] ?? '') ?></textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label small fw-bold">Dietary & Activity Advice</label>
                    <textarea class="form-control" name="diet" rows="3" placeholder="Dietary restrictions and physical activity rules..."><?= esc($summary['diet'] ?? '') ?></textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label small fw-bold">Follow-Up Instructions & Advice</label>
                    <textarea class="form-control" name="follow_up_instructions" rows="3" placeholder="Follow-up visit dates and emergency warning signs..."><?= esc($summary['follow_up_instructions'] ?? $summary['advice'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                    <i class="bi bi-save me-1"></i> Save Summary & Send to Reception
                </button>
            </div>
        </form>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/doctor_footer.php'; ?>