<?php 
$activePage = 'certificates';
include VIEWS_PATH . '/layout/doctor_header.php'; 
?>

<!-- Form Card -->
<div class="card border-0 shadow-sm p-4" style="max-width: 600px; margin: 0 auto;">
    <form action="<?= site_url('/admin/doctor/certificates/save') ?>" method="POST">
        <?= csrf_field() ?>

        <h5 class="fw-bold text-slate mb-3"><i class="bi bi-file-earmark-medical text-success me-2"></i>Issue Sick Leave Certificate</h5>
        <p class="text-muted small">Generate a certified medical sick leave document for registered patient profiles.</p>
        
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
            <label for="diagnosis" class="form-label small fw-semibold">Clinical Diagnosis <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-sm" id="diagnosis" name="diagnosis" required placeholder="e.g. Acute Viral Gastroenteritis">
        </div>

        <div class="row g-2 mb-3">
            <div class="col-md-6">
                <label for="start_date" class="form-label small fw-semibold">Leave Start Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control form-control-sm" id="start_date" name="start_date" required>
            </div>
            <div class="col-md-6">
                <label for="end_date" class="form-label small fw-semibold">Leave End Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control form-control-sm" id="end_date" name="end_date" required>
            </div>
        </div>

        <div class="mb-4">
            <label for="reason" class="form-label small fw-semibold">Sick Leave Advice / Reason <span class="text-danger">*</span></label>
            <textarea class="form-control" id="reason" name="reason" rows="3" required placeholder="Describe advice (e.g. Recommended strict bed rest, hydration, and avoidance of heavy labor)"></textarea>
        </div>

        <div class="text-end pt-2 border-top">
            <a href="<?= site_url('/admin/doctor/certificates') ?>" class="btn btn-outline-secondary btn-sm px-3 me-2">Cancel</a>
            <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
                <i class="bi bi-file-earmark-check-fill me-1"></i> Generate Certificate
            </button>
        </div>
    </form>
</div>

<?php include VIEWS_PATH . '/layout/doctor_footer.php'; ?>
