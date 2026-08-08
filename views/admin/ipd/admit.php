<?php 
$activePage = 'ipd';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Admission Form Card -->
<div class="card border-0 shadow-sm p-4" style="max-width: 600px; margin: 0 auto;">
    <form action="<?= site_url('/admin/ipd/admit/save') ?>" method="POST" autocomplete="off">
        <?= csrf_field() ?>

        <h5 class="fw-bold text-slate mb-3"><i class="bi bi-hospital text-success me-2"></i>Inpatient Care Admission Form</h5>
        <p class="text-muted small">Admit a patient, allocate an available bed, and assign an attending physician.</p>
        
        <div class="mb-3">
            <label for="patient_id" class="form-label small fw-semibold">Select Patient <span class="text-danger">*</span></label>
            <select class="form-control form-control-sm form-select" id="patient_id" name="patient_id" required>
                <option value="" disabled selected>Search Patient</option>
                <?php foreach ($patients as $pat): ?>
                    <option value="<?= $pat['id'] ?>"><?= esc($pat['name']) ?> (<?= esc($pat['patient_id']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="doctor_id" class="form-label small fw-semibold">Attending Doctor / Physician <span class="text-danger">*</span></label>
            <select class="form-control form-control-sm form-select" id="doctor_id" name="doctor_id" required>
                <option value="" disabled selected>Select Doctor</option>
                <?php foreach ($doctors as $doc): ?>
                    <option value="<?= $doc['id'] ?>">Dr. <?= esc($doc['username']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="bed_id" class="form-label small fw-semibold">Select Available Ward Bed <span class="text-danger">*</span></label>
            <select class="form-control form-control-sm form-select" id="bed_id" name="bed_id" required>
                <option value="" disabled selected>Select Room / Bed</option>
                <?php foreach ($beds as $bed): ?>
                    <option value="<?= $bed['id'] ?>"><?= esc($bed['room_number']) ?> &bull; Bed: <?= esc($bed['bed_number']) ?> &bull; <?= esc(ucfirst($bed['type'])) ?> (₹<?= esc(number_format((float)$bed['price_per_day'], 2)) ?>/Day)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="admission_date" class="form-label small fw-semibold">Admission Date & Time <span class="text-danger">*</span></label>
            <input type="datetime-local" class="form-control form-control-sm" id="admission_date" name="admission_date" value="<?= date('Y-m-d\TH:i') ?>" required>
        </div>

        <div class="mb-3">
            <label for="symptoms" class="form-label small fw-semibold">Chief Symptoms</label>
            <textarea class="form-control" id="symptoms" name="symptoms" rows="2" placeholder="List symptoms on admission"></textarea>
        </div>

        <div class="mb-4">
            <label for="diagnosis" class="form-label small fw-semibold">Admission Diagnosis <span class="text-danger">*</span></label>
            <textarea class="form-control" id="diagnosis" name="diagnosis" rows="2" required placeholder="Diagnosis justifying inpatient stay"></textarea>
        </div>

        <div class="text-end pt-2 border-top">
            <a href="<?= site_url('/admin/ipd') ?>" class="btn btn-outline-secondary btn-sm px-3 me-2">Cancel</a>
            <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
                <i class="bi bi-heart-pulse-fill me-1"></i> Register Admission
            </button>
        </div>
    </form>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
