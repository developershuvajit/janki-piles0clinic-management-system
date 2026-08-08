<?php 
$activePage = 'doctor_prescriptions';
include VIEWS_PATH . '/layout/doctor_header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-slate mb-1">Write New Clinical Prescription</h4>
        <p class="text-muted small mb-0">Select patient, record symptoms & diagnosis, and specify recommended medicines</p>
    </div>
    <a href="<?= site_url('/doctor/prescriptions') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Back to Prescriptions
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-primary text-white py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-prescription2 me-2"></i> Clinical Prescription Worksheet</h6>
    </div>
    <div class="card-body p-4">
        <form action="<?= site_url('/doctor/prescriptions/save') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold">Select Patient</label>
                    <select class="form-select" name="patient_id" required>
                        <option value="">-- Choose Registered Patient --</option>
                        <?php foreach ($patients as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= esc($p['name']) ?> (Code: <?= esc($p['patient_id']) ?> | Phone: <?= esc($p['phone']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold">Follow-Up Date (Optional)</label>
                    <input type="date" class="form-control" name="follow_up_date" min="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 mb-3">
                    <label class="form-label small fw-bold">Patient Symptoms</label>
                    <textarea class="form-control" name="symptoms" rows="3" placeholder="Enter patient reported symptoms..." required></textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label small fw-bold">Clinical Diagnosis</label>
                    <textarea class="form-control" name="diagnosis" rows="3" placeholder="Enter diagnosis..." required></textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label small fw-bold">Treatment & Investigation Advice</label>
                    <textarea class="form-control" name="treatment" rows="3" placeholder="Enter treatment plan / investigation advice..." required></textarea>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">General Instructions / Advice</label>
                <input type="text" class="form-control" name="advice" placeholder="e.g. Drink warm water, avoid oily food, take rest for 3 days">
            </div>

            <!-- Medicines Table Builder -->
            <div class="border-top pt-4 mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-slate mb-0"><i class="bi bi-capsule me-2"></i> Prescribed Medicines List</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" id="add-medicine-row">
                        <i class="bi bi-plus-lg me-1"></i> Add Medicine Row
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="medicines-table">
                        <thead class="table-light">
                            <tr>
                                <th style="width:30%;">Medicine Name</th>
                                <th style="width:20%;">Dosage</th>
                                <th style="width:20%;">Frequency</th>
                                <th style="width:15%;">Duration</th>
                                <th style="width:15%;">Instructions</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="medicines[0][name]" placeholder="e.g. Paracetamol 650mg" required>
                                </td>
                                <td><input type="text" class="form-control form-control-sm" name="medicines[0][dosage]" placeholder="1 Tablet"></td>
                                <td><input type="text" class="form-control form-control-sm" name="medicines[0][frequency]" placeholder="1-0-1 (After Food)"></td>
                                <td><input type="text" class="form-control form-control-sm" name="medicines[0][duration]" placeholder="5 Days"></td>
                                <td><input type="text" class="form-control form-control-sm" name="medicines[0][instructions]" placeholder="After Food"></td>
                                <td><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bi bi-check-circle me-1"></i> Issue & Save Prescription
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowIndex = 1;
    const addBtn = document.getElementById('add-medicine-row');
    const tableBody = document.querySelector('#medicines-table tbody');

    addBtn.addEventListener('click', function() {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" class="form-control form-control-sm" name="medicines[${rowIndex}][name]" placeholder="e.g. Amoxicillin 500mg" required></td>
            <td><input type="text" class="form-control form-control-sm" name="medicines[${rowIndex}][dosage]" placeholder="1 Capsule"></td>
            <td><input type="text" class="form-control form-control-sm" name="medicines[${rowIndex}][frequency]" placeholder="1-1-1"></td>
            <td><input type="text" class="form-control form-control-sm" name="medicines[${rowIndex}][duration]" placeholder="3 Days"></td>
            <td><input type="text" class="form-control form-control-sm" name="medicines[${rowIndex}][instructions]" placeholder="With water"></td>
            <td><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button></td>
        `;
        tableBody.appendChild(tr);
        rowIndex++;
    });

    tableBody.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
            const rows = tableBody.querySelectorAll('tr');
            if (rows.length > 1) {
                e.target.closest('tr').remove();
            }
        }
    });
});
</script>

<?php include VIEWS_PATH . '/layout/doctor_footer.php'; ?>
