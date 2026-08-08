<?php 
$activePage = 'doctor_billing';
include VIEWS_PATH . '/layout/doctor_header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-slate mb-1">Patient Billing Summaries (Read Only)</h4>
        <p class="text-muted small mb-0">Review consultation, procedure, surgery, and medicine charge breakdowns for active patients</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Invoice #</th>
                        <th>Patient Name</th>
                        <th>Type</th>
                        <th>Total Bill</th>
                        <th>Paid Amount</th>
                        <th>Pending Balance</th>
                        <th>Payment Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bills)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No billing summaries found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bills as $b): 
                            $pending = (float)$b['total'] - (float)$b['paid_amount'];
                            if ($pending < 0) $pending = 0.00;
                        ?>
                            <tr>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary">#INV-<?= esc((string)$b['id']) ?></span></td>
                                <td>
                                    <div class="fw-bold text-slate"><?= esc($b['patient_name']) ?></div>
                                    <div class="small text-muted">Code: <?= esc($b['patient_code']) ?></div>
                                </td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-secondary text-uppercase"><?= esc($b['type']) ?></span></td>
                                <td class="fw-bold text-slate">₹<?= number_format((float)$b['total'], 2) ?></td>
                                <td class="text-success fw-bold">₹<?= number_format((float)$b['paid_amount'], 2) ?></td>
                                <td class="text-danger fw-bold">₹<?= number_format($pending, 2) ?></td>
                                <td>
                                    <span class="badge bg-<?= $b['payment_status'] === 'paid' ? 'success' : 'warning' ?> bg-opacity-20 text-<?= $b['payment_status'] === 'paid' ? 'success' : 'warning' ?> px-2.5 py-1">
                                        <?= ucfirst(esc($b['payment_status'])) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="<?= site_url('/doctor/billing-summary/view/' . $b['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="bi bi-eye me-1"></i> Breakdown
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/doctor_footer.php'; ?>
