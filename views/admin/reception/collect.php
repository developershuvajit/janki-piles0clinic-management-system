<?php 
$activePage = 'reception_dashboard';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Payment Sheet Container -->
<div class="row justify-content-center mt-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-4">
            <form action="<?= site_url('/reception/billing/pay') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="bill_id" value="<?= $bill['id'] ?>">

                <h5 class="fw-bold text-slate mb-3"><i class="bi bi-wallet2 text-success me-2"></i>Process Invoice Payment</h5>
                
                <div class="bg-light p-3 rounded-3 mb-4">
                    <div class="row g-2 small text-slate mb-2">
                        <div class="col-6 text-muted">Invoice No:</div>
                        <div class="col-6 text-end fw-bold">INV-<?= esc(sprintf("%05d", $bill['id'])) ?></div>
                        
                        <div class="col-6 text-muted">Patient:</div>
                        <div class="col-6 text-end fw-semibold"><?= esc($bill['patient_name']) ?> (<?= esc($bill['patient_code']) ?>)</div>
                        
                        <div class="col-6 text-muted">Clinic Branch:</div>
                        <div class="col-6 text-end"><?= esc($bill['branch_name']) ?></div>
                    </div>
                    <hr class="my-2 text-muted">
                    <div class="row g-2 small text-slate">
                        <div class="col-6 text-muted">Subtotal Cost:</div>
                        <div class="col-6 text-end">₹<?= esc(number_format((float)$bill['subtotal'], 2)) ?></div>
                        
                        <div class="col-6 text-muted">Discounts Applied:</div>
                        <div class="col-6 text-end text-success">- ₹<?= esc(number_format((float)$bill['discount'], 2)) ?></div>
                        
                        <div class="col-6 text-muted">Tax Additions:</div>
                        <div class="col-6 text-end">+ ₹<?= esc(number_format((float)$bill['tax'], 2)) ?></div>
                    </div>
                    <hr class="my-2 text-muted">
                    <div class="row g-2 align-items-center">
                        <div class="col-6 fw-bold text-slate fs-5">Total Payable:</div>
                        <div class="col-6 text-end fw-bold text-success fs-4">₹<?= esc(number_format((float)$bill['total'], 2)) ?></div>
                    </div>
                </div>

                <!-- Select Payment Method -->
                <div class="mb-4">
                    <label class="form-label small fw-semibold d-block mb-3">Select Payment Method</label>
                    
                    <div class="row g-2">
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="payment_method" id="pay-cash" value="cash" checked required>
                            <label class="btn btn-outline-success w-100 py-3 fw-bold" for="pay-cash">
                                <i class="bi bi-cash d-block fs-3 mb-1"></i> Cash
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="payment_method" id="pay-card" value="card" required>
                            <label class="btn btn-outline-primary w-100 py-3 fw-bold" for="pay-card">
                                <i class="bi bi-credit-card d-block fs-3 mb-1"></i> Card
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="payment_method" id="pay-upi" value="upi" required>
                            <label class="btn btn-outline-info w-100 py-3 fw-bold" for="pay-upi">
                                <i class="bi bi-qr-code d-block fs-3 mb-1"></i> UPI
                            </label>
                        </div>
                    </div>
                </div>

                <div class="text-end pt-3 border-top">
                    <a href="<?= site_url('/reception/billing') ?>" class="btn btn-outline-secondary btn-sm px-3 me-2">Cancel</a>
                    <button type="submit" class="btn btn-success btn-sm px-4 shadow-sm">
                        <i class="bi bi-check-circle-fill me-1"></i> Complete Collection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
