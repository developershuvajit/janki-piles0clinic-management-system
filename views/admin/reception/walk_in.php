<?php 
$activePage = 'reception_dashboard';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Walk-In Booking Form Card -->
<div class="card border-0 shadow-sm p-4 mt-4"  style="max-width: 600px; margin: 0 auto;">
    <form action="<?= site_url('/reception/walk-in/save') ?>" method="POST">
        <?= csrf_field() ?>

        <h5 class="fw-bold text-slate mb-3"><i class="bi bi-person-workspace text-success me-2"></i>Register Walk-In Appointment</h5>
        <p class="text-muted small">Select an enrolled patient and assign them to an active doctor to generate a daily queue token.</p>
        
        <div class="mb-3">
            <label for="patient_id" class="form-label small fw-semibold">Select Patient <span class="text-danger">*</span></label>
            <select class="form-control form-control-sm form-select" id="patient_id" name="patient_id" required>
                <option value="" disabled selected>Search Patient</option>
                <?php foreach ($patients as $pat): ?>
                    <option value="<?= $pat['id'] ?>"><?= esc($pat['name']) ?> (<?= esc($pat['patient_id']) ?> &bull; <?= esc($pat['phone']) ?>)</option>
                <?php endforeach; ?>
            </select>
            <div class="form-text x-small" style="font-size: 0.75rem;">Patient not enrolled? <a href="<?= site_url('/admin/patients/create') ?>" target="_blank" class="text-success fw-semibold">Add patient to directory</a> first.</div>
        </div>

        <div class="mb-3">
            <label for="doctor_id" class="form-label small fw-semibold">Select Attending Doctor <span class="text-danger">*</span></label>
            <select class="form-control form-control-sm form-select" id="doctor_id" name="doctor_id" required>
                <option value="" disabled selected>Select Doctor</option>
                <?php foreach ($doctors as $doc): ?>
                    <option value="<?= $doc['id'] ?>">Dr. <?= esc($doc['username']) ?> (<?= esc($doc['branch_name'] ?? 'General') ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="row g-2 mb-4">
            <div class="col-md-6">
                <label for="branch_id" class="form-label small fw-semibold">Branch Office</label>
                <select class="form-control form-control-sm form-select" id="branch_id" name="branch_id">
                    <?php foreach ($branches as $br): ?>
                        <option value="<?= $br['id'] ?>"><?= esc($br['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-6">
                <label for="consultation_fee" class="form-label small fw-semibold">OPD Consultation Fee (INR)</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light">₹</span>
                    <input type="number" class="form-control" id="consultation_fee" name="consultation_fee" value="500.00" step="50.00" min="0.00" required>
                </div>
            </div>
        </div>

        <div class="text-end pt-2 border-top">
            <a href="<?= site_url('/admin/reception') ?>" class="btn btn-outline-secondary btn-sm px-3 me-2">Cancel</a>
            <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
                <i class="bi bi-ticket-perforated-fill me-1"></i> Issue Queue Token
            </button>
        </div>
    </form>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
