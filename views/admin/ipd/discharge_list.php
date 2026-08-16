<?php 
$activePage = 'discharge';
include VIEWS_PATH . '/layout/reception_header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-slate mb-1">Doctor Approved Discharge List</h4>
        <p class="text-muted small mb-0">Receive doctor-approved discharges, generate final billing, and complete patient checkout</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Admission #</th>
                        <th>Patient Name</th>
                        <th>Attending Doctor</th>
                        <th>Admission Date</th>
                        <th>Doctor Approval</th>
                        <th class="text-end">Checkout Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($approved)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No doctor-approved discharges awaiting checkout.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($approved as $app): ?>
                            <tr>
                                <td><span class="badge bg-emerald bg-opacity-10 text-emerald">#ADM-<?= esc((string)$app['id']) ?></span></td>
                                <td>
                                    <div class="fw-bold text-slate"><?= esc($app['patient_name']) ?></div>
                                    <div class="small text-muted">Code: <?= esc($app['patient_code']) ?></div>
                                </td>
                                <td>Dr. <?= esc($app['doctor_name']) ?></td>
                                <td class="small text-muted"><?= date('d M Y, h:i A', strtotime($app['admission_date'])) ?></td>
                                <td><span class="badge bg-success px-2.5 py-1"><i class="bi bi-check-circle-fill me-1"></i> Doctor Approved</span></td>
                                <td class="text-end">
                                    <form action="<?= site_url('/reception/discharge/checkout/' . $app['id']) ?>" method="POST" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-emerald rounded-pill px-3 shadow-sm" onclick="return confirm('Generate final bill and complete checkout for patient <?= esc($app['patient_name']) ?>?');">
                                            <i class="bi bi-box-arrow-right me-1"></i> Final Bill & Checkout
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/reception_footer.php'; ?>