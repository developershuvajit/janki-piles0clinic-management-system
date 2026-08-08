<?php 
$activePage = 'doctor_billing';
include VIEWS_PATH . '/layout/doctor_header.php'; 
$pending = (float)$bill['total'] - (float)$bill['paid_amount'];
if ($pending < 0) $pending = 0.00;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-slate mb-1">Patient Bill Summary Breakdown (Read Only)</h4>
        <p class="text-muted small mb-0">Invoice #INV-<?= esc((string)$bill['id']) ?> | Patient: <strong><?= esc($bill['patient_name']) ?></strong></p>
    </div>
    <a href="<?= site_url('/doctor/billing-summary') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Back to Summaries
    </a>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-calculator me-2"></i> Charges Breakdown Overview</h6>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Category Charge Item</th>
                                <th class="text-end">Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Consultation & OPD Charges</td>
                                <td class="text-end">₹<?= number_format((float)$bill['subtotal'], 2) ?></td>
                            </tr>
                            <tr>
                                <td>GST / Taxes</td>
                                <td class="text-end">₹<?= number_format((float)($bill['gst'] ?? $bill['tax'] ?? 0), 2) ?></td>
                            </tr>
                            <tr>
                                <td>Discounts Applied</td>
                                <td class="text-end text-danger">- ₹<?= number_format((float)$bill['discount'], 2) ?></td>
                            </tr>
                            <tr class="table-light fw-bold">
                                <td>Total Invoiced Amount</td>
                                <td class="text-end text-primary fs-5">₹<?= number_format((float)$bill['total'], 2) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row text-center border-top pt-3">
                    <div class="col-md-4">
                        <div class="small text-muted text-uppercase fw-bold">Total Invoiced</div>
                        <div class="fs-4 fw-bold text-slate">₹<?= number_format((float)$bill['total'], 2) ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted text-uppercase fw-bold">Amount Paid</div>
                        <div class="fs-4 fw-bold text-success">₹<?= number_format((float)$bill['paid_amount'], 2) ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted text-uppercase fw-bold">Pending Balance</div>
                        <div class="fs-4 fw-bold text-danger">₹<?= number_format($pending, 2) ?></div>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-light rounded-3 text-center border">
                    <span class="small text-muted"><i class="bi bi-info-circle me-1"></i> Doctor accounts do not possess financial cashier privileges to process payments or refunds.</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/doctor_footer.php'; ?>
