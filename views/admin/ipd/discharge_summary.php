<?php 
$activePage = 'ipd';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Discharge Summary form card -->
<div class="card border-0 shadow-sm p-4 text-slate" style="max-width: 750px; margin: 0 auto;">
    <form action="<?= site_url('/admin/ipd/discharge-summary/save') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="ipd_admission_id" value="<?= $admission['id'] ?>">

        <h5 class="fw-bold text-slate mb-3"><i class="bi bi-file-earmark-medical text-success me-2"></i>Compile Discharge Summary</h5>
        <p class="text-muted small">Record stay recovery parameters, outpatient medication schedules, and clinical diet advices.</p>
        
        <div class="row g-2 mb-3 bg-light p-3 rounded-3 small">
            <div class="col-md-6"><strong>Patient:</strong> <?= esc($admission['patient_name']) ?> (<?= esc($admission['patient_code']) ?>)</div>
            <div class="col-md-6"><strong>Attending Doctor:</strong> Dr. <?= esc($admission['doctor_name']) ?></div>
            <div class="col-md-6"><strong>Admit Date:</strong> <?= esc($admission['admission_date']) ?></div>
            <div class="col-md-6"><strong>Discharge Date:</strong> <?= esc($admission['discharge_date'] ?? 'N/A') ?></div>
            <div class="col-md-12"><strong>Bed Details:</strong> Room <?= esc($admission['room_number']) ?> (Bed: <?= esc($admission['bed_number']) ?>)</div>
        </div>

        <div class="mb-3">
            <label for="diagnosis" class="form-label small fw-semibold">Final Diagnosis <span class="text-danger">*</span></label>
            <textarea class="form-control" id="diagnosis" name="diagnosis" rows="2" required placeholder="Describe final clinical impression..."><?= esc($summary['diagnosis'] ?? '') ?></textarea>
        </div>

        <div class="mb-3">
            <label for="treatment_summary" class="form-label small fw-semibold">Treatment / Action Summary <span class="text-danger">*</span></label>
            <textarea class="form-control" id="treatment_summary" name="treatment_summary" rows="3" required placeholder="Summarize procedures, operations, medication regimes during ward stay..."><?= esc($summary['treatment_summary'] ?? '') ?></textarea>
        </div>

        <div class="mb-3">
            <label for="advice" class="form-label small fw-semibold">Discharge Advice / Home Medications</label>
            <textarea class="form-control" id="advice" name="advice" rows="2" placeholder="e.g. Paracetamol 650mg TDS for 3 days..."><?= esc($summary['advice'] ?? '') ?></textarea>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-md-6">
                <label for="diet" class="form-label small fw-semibold">Dietary Instructions</label>
                <textarea class="form-control" id="diet" name="diet" rows="2" placeholder="e.g. Light food, avoid spicy meals..."><?= esc($summary['diet'] ?? '') ?></textarea>
            </div>
            <div class="col-md-6">
                <label for="follow_up" class="form-label small fw-semibold">Follow-Up Instructions</label>
                <textarea class="form-control" id="follow_up" name="follow_up_instructions" rows="2" placeholder="e.g. Return to OPD after 7 days..."><?= esc($summary['follow_up_instructions'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="row g-2 mb-4">
            <div class="col-md-6">
                <label for="sig" class="form-label small fw-semibold">Upload Doctor Signature Scan</label>
                <input type="file" class="form-control form-control-sm" id="sig" name="doctor_signature" accept="image/*">
                <?php if (!empty($summary['doctor_signature'])): ?>
                    <span class="text-success small mt-1 d-block"><i class="bi bi-check-circle"></i> Signature file uploaded</span>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label for="seal" class="form-label small fw-semibold">Upload Hospital Seal Scan</label>
                <input type="file" class="form-control form-control-sm" id="seal" name="hospital_seal" accept="image/*">
                <?php if (!empty($summary['hospital_seal'])): ?>
                    <span class="text-success small mt-1 d-block"><i class="bi bi-check-circle"></i> Hospital seal uploaded</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="text-end pt-2 border-top">
            <a href="<?= site_url('/admin/ipd') ?>" class="btn btn-outline-secondary btn-sm px-3 me-2">Cancel</a>
            <button type="submit" class="btn btn-success btn-sm px-4 shadow-sm">
                <i class="bi bi-file-earmark-check-fill me-1"></i> Save Summary
            </button>
        </div>
    </form>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
