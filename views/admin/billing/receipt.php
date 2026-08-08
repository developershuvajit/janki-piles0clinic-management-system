<?php 
$activePage = 'billing';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Print Controls -->
<div class="d-print-none text-end mb-4">
    <button onclick="window.print()" class="btn btn-primary btn-sm px-4 shadow-sm">
        <i class="bi bi-printer-fill me-1"></i> Print Receipt
    </button>
    <a href="<?= site_url('/admin/billing') ?>" class="btn btn-outline-secondary btn-sm px-3 ms-2">
        Back to Ledger
    </a>
</div>

<!-- Receipt Sheet Container -->
<div class="card border-0 shadow-sm p-5 receipt-container mx-auto" style="max-width: 800px; background: #fff;">
    <!-- Invoice Logo Header -->
    <div class="row align-items-center mb-4 pb-4 border-bottom text-slate">
        <div class="col-6">
            <h3 class="fw-bold text-slate mb-1"><i class="bi bi-heart-pulse-fill text-success me-2"></i><?= esc($bill['branch_name']) ?></h3>
            <p class="text-muted small mb-0">
                <?= esc($bill['branch_address']) ?><br>
                Phone: <?= esc($bill['branch_phone']) ?> &bull; Email: <?= esc($bill['branch_email']) ?>
            </p>
        </div>
        <div class="col-6 text-end">
            <h4 class="fw-bold text-success mb-1">INVOICE RECEIPT</h4>
            <span class="badge bg-success px-3 py-1.5 rounded-pill fw-semibold"><?= strtoupper($bill['payment_status']) ?></span>
            <div class="text-muted small mt-2">
                Invoice No: <strong>INV-<?= esc(sprintf("%05d", $bill['id'])) ?></strong><br>
                Date Issued: <strong><?= esc(date('Y-m-d H:i', strtotime($bill['updated_at']))) ?></strong>
            </div>
        </div>
    </div>

    <!-- Patient Metadata Row -->
    <div class="row mb-4 bg-light p-3 rounded-3 mx-0 text-slate">
        <div class="col-6">
            <div class="text-muted x-small text-uppercase fw-bold mb-1">Billed To:</div>
            <div class="fw-bold text-slate"><?= esc($bill['patient_name']) ?></div>
            <div class="small text-muted">
                Patient Code: <strong><?= esc($bill['patient_code']) ?></strong><br>
                Phone: <?= esc($bill['patient_phone']) ?><br>
                Address: <?= esc($bill['patient_address']) ?>
            </div>
        </div>
        <div class="col-6 text-end">
            <div class="text-muted x-small text-uppercase fw-bold mb-1">Payment Details:</div>
            <div class="fw-bold text-slate">Method: <?= esc(strtoupper($bill['payment_method'])) ?></div>
            <div class="text-muted small mt-2">
                Collected: <strong class="text-success">₹<?= esc(number_format((float)$bill['paid_amount'], 2)) ?></strong><br>
                Outstanding: <strong class="text-danger">₹<?= esc(number_format((float)$bill['outstanding'], 2)) ?></strong>
                <?php if ((float)$bill['refunded_amount'] > 0.00): ?>
                    <br>Refunded: <strong class="text-warning">₹<?= esc(number_format((float)$bill['refunded_amount'], 2)) ?></strong>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Billing Line Items Table -->
    <table class="table table-bordered mb-4 text-slate">
        <thead class="bg-light">
            <tr>
                <th>Service Description</th>
                <th class="text-end">Units</th>
                <th class="text-end">Base Rate</th>
                <th class="text-end">Line Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <?php if ($bill['type'] === 'opd'): ?>
                        <strong>Outpatient Consultation Fee</strong><br>
                        <span class="text-muted small">Standard clinical visit and consultation.</span>
                    <?php elseif ($bill['type'] === 'ipd'): ?>
                        <strong>Inpatient Ward Bed Stay</strong><br>
                        <span class="text-muted small">Stay charges + procedures logged during admission.</span>
                    <?php else: ?>
                        <strong>Appointment Slot Booking</strong><br>
                        <span class="text-muted small">Online consultation booking.</span>
                    <?php endif; ?>
                </td>
                <td class="text-end">1</td>
                <td class="text-end">₹<?= esc(number_format((float)$bill['subtotal'], 2)) ?></td>
                <td class="text-end fw-semibold">₹<?= esc(number_format((float)$bill['subtotal'], 2)) ?></td>
            </tr>
        </tbody>
    </table>

    <!-- Billing Summary Columns -->
    <div class="row justify-content-end text-slate small">
        <div class="col-md-5">
            <div class="row g-2 mb-1">
                <div class="col-6 text-muted">Subtotal:</div>
                <div class="col-6 text-end">₹<?= esc(number_format((float)$bill['subtotal'], 2)) ?></div>
                
                <div class="col-6 text-muted">Discount Applied:</div>
                <div class="col-6 text-end text-success">- ₹<?= esc(number_format((float)$bill['discount'], 2)) ?></div>
                
                <div class="col-6 text-muted">Tax Additions:</div>
                <div class="col-6 text-end">+ ₹<?= esc(number_format((float)$bill['tax'], 2)) ?></div>

                <div class="col-6 text-muted">GST Additions:</div>
                <div class="col-6 text-end">+ ₹<?= esc(number_format((float)$bill['gst'], 2)) ?></div>
            </div>
            <hr class="my-2">
            <div class="row g-2 align-items-center">
                <div class="col-6 fw-bold text-slate fs-5">Total Invoice:</div>
                <div class="col-6 text-end fw-bold text-success fs-4">₹<?= esc(number_format((float)$bill['total'], 2)) ?></div>
            </div>
        </div>
    </div>

    <!-- Receipt Footer Statement -->
    <div class="text-center mt-5 pt-4 border-top text-muted small">
        <p class="mb-1">Thank you for visiting MedClinic.</p>
        <p class="x-small mb-0">For medical queries or follow-up consult bookings, visit our portal or contact the clinic reception desks.</p>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
