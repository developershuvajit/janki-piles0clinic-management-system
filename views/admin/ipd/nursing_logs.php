<?php 
$activePage = 'ipd';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<div class="row">
    <!-- Left Column: Patient Info & Vitals Logs List -->
    <div class="col-lg-5 mb-4">
        <!-- Patient Info Card -->
        <div class="card border-0 shadow-sm p-4 mb-4">
            <h6 class="fw-bold text-slate mb-3"><i class="bi bi-hospital text-success me-2"></i>Inpatient Bed File</h6>
            <ul class="list-group list-group-flush mb-0" style="font-size: 0.8rem;">
                <li class="list-group-item px-0 py-2"><strong>Patient Name:</strong> <?= esc($admission['patient_name']) ?> (<?= esc($admission['patient_code']) ?>)</li>
                <li class="list-group-item px-0 py-2"><strong>Attending Doctor:</strong> Dr. <?= esc($admission['doctor_name']) ?></li>
                <li class="list-group-item px-0 py-2"><strong>Mapped Room / Bed:</strong> <?= esc($admission['room_number']) ?> &bull; Bed: <strong><?= esc($admission['bed_number']) ?></strong></li>
                <li class="list-group-item px-0 py-2"><strong>Daily Bed Rate:</strong> ₹<?= esc(number_format((float)$admission['price_per_day'], 2)) ?></li>
                <li class="list-group-item px-0 py-2"><strong>Admission Date:</strong> <?= esc(date('Y-m-d H:i', strtotime($admission['admission_date']))) ?></li>
                <li class="list-group-item px-0 py-2"><strong>Admitting Diagnosis:</strong> <span class="text-danger fw-semibold"><?= esc($admission['diagnosis']) ?></span></li>
            </ul>
        </div>

        <!-- Nursing Vitals History -->
        <div class="card border-0 shadow-sm p-4 mb-4" style="max-height: 400px; overflow-y: auto;">
            <h6 class="fw-bold text-slate mb-3"><i class="bi bi-heart-pulse text-danger me-2"></i>Nursing Vitals History</h6>
            
            <div class="list-group list-group-flush">
                <?php if (empty($logs)): ?>
                    <span class="text-muted small">No vitals logs recorded.</span>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <div class="list-group-item px-0 py-2.5" style="font-size: 0.8rem;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-light text-slate border">Temp: <?= esc($log['vit_temp'] ?: 'N/A') ?>&deg;F &bull; BP: <?= esc($log['vit_bp'] ?: 'N/A') ?> &bull; Pulse: <?= esc($log['vit_pulse'] ?: 'N/A') ?></span>
                                <small class="text-muted"><?= esc(date('M d H:i', strtotime($log['recorded_at']))) ?></small>
                            </div>
                            <div class="text-muted mt-1" style="font-size: 0.76rem;"><?= esc($log['notes']) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Column: Forms to Log Vitals, Procedures, and Discharge controls -->
    <div class="col-lg-7 mb-4">
        <!-- Log Vitals Form -->
        <div class="card border-0 shadow-sm p-4 mb-4">
            <h6 class="fw-bold text-slate mb-3"><i class="bi bi-pencil-square text-success me-2"></i>Log Patient Vitals</h6>
            <form action="<?= site_url('/admin/ipd/nursing-logs/' . $admission['id'] . '/save') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <label for="temp" class="form-label small fw-semibold">Temp (&deg;F)</label>
                        <input type="text" class="form-control form-control-sm" id="temp" name="temp" placeholder="98.6">
                    </div>
                    <div class="col-md-4">
                        <label for="bp" class="form-label small fw-semibold">Blood Pressure</label>
                        <input type="text" class="form-control form-control-sm" id="bp" name="bp" placeholder="120/80">
                    </div>
                    <div class="col-md-4">
                        <label for="pulse" class="form-label small fw-semibold">Pulse Rate (bpm)</label>
                        <input type="text" class="form-control form-control-sm" id="pulse" name="pulse" placeholder="72">
                    </div>
                    <div class="col-md-12 mt-2">
                        <label for="notes" class="form-label small fw-semibold">Nurse Notes / Remarks <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="notes" name="notes" required placeholder="Describe condition, complaints, or fluid charts">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm px-3 shadow-sm float-end">
                    <i class="bi bi-save me-1"></i> Save Vitals Log
                </button>
            </form>
        </div>

        <!-- Log Procedure Form -->
        <div class="card border-0 shadow-sm p-4 mb-4">
            <h6 class="fw-bold text-slate mb-3"><i class="bi bi-slash-circle-fill text-info me-2"></i>Record Clinical Procedures</h6>
            <form action="<?= site_url('/admin/ipd/procedures/' . $admission['id'] . '/save') ?>" method="POST" class="mb-3">
                <?= csrf_field() ?>
                <div class="row g-2 mb-2">
                    <div class="col-md-5">
                        <label for="procedure_name" class="form-label small fw-semibold">Procedure Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="procedure_name" name="procedure_name" required placeholder="e.g. Suturing, IV Fluid Setup">
                    </div>
                    <div class="col-md-4">
                        <label for="doctor_id" class="form-label small fw-semibold">Attending Doctor <span class="text-danger">*</span></label>
                        <select class="form-control form-control-sm form-select" id="doctor_id" name="doctor_id" required>
                            <?php foreach ($doctors as $doc): ?>
                                <option value="<?= $doc['id'] ?>">Dr. <?= esc($doc['username']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="cost" class="form-label small fw-semibold">Procedure Cost <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-sm" id="cost" name="cost" required step="50.00" placeholder="0.00">
                    </div>
                </div>
                <button type="submit" class="btn btn-info btn-sm text-white px-3 shadow-sm float-end">
                    <i class="bi bi-plus-circle me-1"></i> Log Procedure
                </button>
            </form>

            <!-- Mapped Procedures -->
            <div class="fw-semibold text-slate small mb-2 mt-4">Logged Stay Procedures:</div>
            <div class="table-responsive border rounded bg-white">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.78rem;">
                    <thead class="bg-light">
                        <tr>
                            <th>Procedure Name</th>
                            <th>Doctor</th>
                            <th class="text-end">Cost (INR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($procedures)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-2 text-muted">No procedures logged during stay.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($procedures as $pr): ?>
                                <tr>
                                    <td class="fw-bold"><?= esc($pr['name']) ?></td>
                                    <td>Dr. <?= esc($pr['doctor_name']) ?></td>
                                    <td class="text-end fw-semibold">₹<?= esc(number_format((float)$pr['cost'], 2)) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Discharge Controls Card -->
        <div class="card border-danger border-opacity-25 shadow-sm bg-danger bg-opacity-10 p-4">
            <h6 class="fw-bold text-danger mb-2"><i class="bi bi-box-arrow-right me-2"></i>Discharge Patient & Generate Bill</h6>
            <p class="text-muted small">Discharging patient releases the ward bed and compiles stay days + procedure costs into a billing invoice.</p>
            
            <form action="<?= site_url('/admin/ipd/discharge/' . $admission['id']) ?>" method="POST" onsubmit="return confirm('Process clinical discharge? This will release the bed and finalize the invoice.');">
                <?= csrf_field() ?>
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label for="discount" class="form-label small fw-semibold">Discount Amount (INR)</label>
                        <input type="number" class="form-control form-control-sm" id="discount" name="discount" step="50.00" value="0.00">
                    </div>
                    <div class="col-md-6">
                        <label for="tax" class="form-label small fw-semibold">Tax Additions (INR)</label>
                        <input type="number" class="form-control form-control-sm" id="tax" name="tax" step="50.00" value="0.00">
                    </div>
                </div>
                <button type="submit" class="btn btn-danger btn-sm w-100 py-2.5 fw-semibold shadow-sm">
                    <i class="bi bi-receipt-cutoff me-1"></i> Finalize Discharge & Collect Bill
                </button>
            </form>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
