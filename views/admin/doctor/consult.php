<?php 
$activePage = 'doctor_dashboard';
include VIEWS_PATH . '/layout/doctor_header.php'; 
?>

<div class="row">
    <!-- Left Column: Patient Profile & Timeline History -->
    <div class="col-lg-5 mb-4">
        <!-- Demographics -->
        <div class="card border-0 shadow-sm p-4 mb-4">
            <h6 class="fw-bold text-slate mb-3"><i class="bi bi-person-card text-success me-2"></i>Patient Clinical Chart</h6>
            
            <?php if (!empty($patient['allergies'])): ?>
                <div class="alert alert-danger p-2.5 small mb-3 border-start border-4 border-danger rounded-3">
                    <strong><i class="bi bi-exclamation-triangle-fill"></i> ALLERGIES:</strong> <?= esc($patient['allergies']) ?>
                </div>
            <?php endif; ?>

            <ul class="list-group list-group-flush mb-0" style="font-size: 0.8rem;">
                <li class="list-group-item px-0 py-2"><strong>Name:</strong> <?= esc($patient['name']) ?> (<?= esc($patient['patient_id']) ?>)</li>
                <li class="list-group-item px-0 py-2"><strong>Age/Gender:</strong> <?= esc(date('Y') - date('Y', strtotime($patient['dob']))) ?> Yrs &bull; <?= esc(ucfirst($patient['gender'])) ?></li>
                <li class="list-group-item px-0 py-2"><strong>Blood Group:</strong> <?= esc($patient['blood_group'] ?: 'Unknown') ?></li>
                <li class="list-group-item px-0 py-2"><strong>History:</strong> <?= esc($patient['medical_history'] ?: 'None recorded') ?></li>
            </ul>
        </div>

        <!-- Visit Feed -->
        <div class="card border-0 shadow-sm p-4" style="max-height: 450px; overflow-y: auto;">
            <h6 class="fw-bold text-slate mb-3"><i class="bi bi-clock-history text-success me-2"></i>Visit History Timeline</h6>
            <?php if (empty($timeline)): ?>
                <span class="text-muted small">No previous visits recorded.</span>
            <?php else: ?>
                <div class="position-relative ps-3 border-start border-light ms-1" style="font-size: 0.8rem;">
                    <?php foreach ($timeline as $item): ?>
                        <div class="mb-3">
                            <div class="fw-bold text-slate"><?= esc($item['title']) ?></div>
                            <div class="text-muted x-small"><?= esc($item['date_display']) ?></div>
                            <div class="text-muted mt-1" style="white-space: pre-line; font-size: 0.75rem;"><?= esc($item['detail']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Column: Consultation Worksheet & Prescriptions Form -->
    <div class="col-lg-7 mb-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <form action="<?= site_url('/doctor/opd/consult/save') ?>" method="POST" id="consultation-form">
                <?= csrf_field() ?>
                <input type="hidden" name="appointment_id" value="<?= $appointment['id'] ?>">

                <h5 class="fw-bold text-slate mb-4"><i class="bi bi-activity text-success me-2"></i>Consultation Worksheet</h5>
                
                <!-- Clinical Inputs -->
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="symptoms" class="form-label small fw-semibold mb-0">Symptoms / Chief Complaints <span class="text-danger">*</span></label>
                            <button type="button" class="btn btn-outline-primary btn-xs py-0.5 px-2 small shadow-sm" id="btn-ai-assist" style="font-size: 0.72rem;">
                                <i class="bi bi-cpu-fill me-1"></i> AI Diagnostics Assist
                            </button>
                        </div>
                        <textarea class="form-control" id="symptoms" name="symptoms" rows="2" required placeholder="Describe chief complaints, pain severity, duration"></textarea>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="diagnosis" class="form-label small fw-semibold">Diagnosis / Assessment <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="diagnosis" name="diagnosis" rows="2" required placeholder="Diagnosis or clinical impression"></textarea>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="treatment" class="form-label small fw-semibold">Treatment / Action Plan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="treatment" name="treatment" rows="2" required placeholder="Treatment course or therapeutic actions"></textarea>
                    </div>
                    
                    <div class="col-md-8">
                        <label for="advice" class="form-label small fw-semibold">Advice / Diet / Instructions</label>
                        <input type="text" class="form-control form-control-sm" id="advice" name="advice" placeholder="e.g. Avoid cold drinks, take rest">
                    </div>

                    <div class="col-md-4">
                        <label for="follow_up_date" class="form-label small fw-semibold">Follow-Up Date</label>
                        <input type="date" class="form-control form-control-sm" id="follow_up_date" name="follow_up_date" min="<?= date('Y-m-d') ?>">
                    </div>
                </div>

                <!-- Prescribe Medications -->
                <div class="border-top pt-3 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-slate mb-0"><i class="bi bi-prescription text-danger me-1"></i>Prescribe Medications</h6>
                        <button type="button" class="btn btn-outline-success btn-sm px-3 fw-bold" id="btn-add-med">
                            <i class="bi bi-plus-circle"></i> Add Medicine
                        </button>
                    </div>

                    <div class="table-responsive border rounded bg-white">
                        <table class="table align-middle mb-0" style="font-size: 0.85rem;" id="med-table">
                            <thead class="bg-light">
                                <tr>
                                    <th>Medicine Name</th>
                                    <th>Dosage</th>
                                    <th>Frequency</th>
                                    <th>Duration</th>
                                    <th>Instructions</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="med-tbody">
                                <!-- JS will inject rows here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="text-end pt-3 border-top">
                    <a href="<?= site_url('/admin/doctor') ?>" class="btn btn-outline-secondary btn-sm px-3 me-2">Cancel</a>
                    <button type="submit" class="btn btn-success btn-sm px-4 shadow-sm">
                        <i class="bi bi-cloud-upload-fill me-1"></i> Save Consultation & Prescription
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS dynamic medicine row builder & AI Assist -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const medTableBody = document.getElementById('med-tbody');
    const btnAddMed = document.getElementById('btn-add-med');
    const btnAiAssist = document.getElementById('btn-ai-assist');
    const symptomsInput = document.getElementById('symptoms');
    let medIndex = 0;

    function createMedicineRow(name = '', dosage = '', freq = '', dur = '', inst = '') {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <input type="text" name="medicines[${medIndex}][name]" class="form-control form-control-sm" required value="${name}" placeholder="e.g. Paracetamol 650">
            </td>
            <td>
                <input type="text" name="medicines[${medIndex}][dosage]" class="form-control form-control-sm" value="${dosage}" placeholder="e.g. 1 Tab">
            </td>
            <td>
                <input type="text" name="medicines[${medIndex}][frequency]" class="form-control form-control-sm" value="${freq}" placeholder="e.g. 1-0-1">
            </td>
            <td>
                <input type="text" name="medicines[${medIndex}][duration]" class="form-control form-control-sm" value="${dur}" placeholder="e.g. 5 Days">
            </td>
            <td>
                <input type="text" name="medicines[${medIndex}][instructions]" class="form-control form-control-sm" value="${inst}" placeholder="e.g. After meal">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-light border btn-remove-med">
                    <i class="bi bi-trash-fill text-danger"></i>
                </button>
            </td>
        `;

        row.querySelector('.btn-remove-med').addEventListener('click', function() {
            row.remove();
        });

        medTableBody.appendChild(row);
        medIndex++;
    }

    // Add first row by default
    createMedicineRow();

    btnAddMed.addEventListener('click', () => createMedicineRow());

    // AI Assist Event Handler
    btnAiAssist.addEventListener('click', function() {
        const symptoms = symptomsInput.value.trim();
        if (symptoms === '') {
            alert('Please enter patient symptoms first.');
            return;
        }

        btnAiAssist.disabled = true;
        btnAiAssist.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Analyzing...';

        fetch('<?= site_url("/admin/doctor/ai-assist") ?>?symptoms=' + encodeURIComponent(symptoms))
            .then(res => res.json())
            .then(data => {
                document.getElementById('diagnosis').value = data.diagnosis;
                document.getElementById('treatment').value = 'Therapeutic course and medical administration.';
                document.getElementById('advice').value = data.advice;

                // Populate medicines dynamically
                medTableBody.innerHTML = '';
                medIndex = 0;

                const lines = data.prescription.split('\n');
                lines.forEach(line => {
                    if (line.trim() !== '') {
                        createMedicineRow(line.trim(), '1 Tab', '1-0-1', '5 Days', 'After meal');
                    }
                });
            })
            .catch(err => {
                console.error(err);
                alert('AI recommendation service currently offline.');
            })
            .finally(() => {
                btnAiAssist.disabled = false;
                btnAiAssist.innerHTML = '<i class="bi bi-cpu-fill me-1"></i> AI Diagnostics Assist';
            });
    });
});
</script>

<?php include VIEWS_PATH . '/layout/doctor_footer.php'; ?>
