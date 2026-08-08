<?php 
$activePage = 'billing';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-4 text-slate">
            <form action="<?= site_url('/admin/billing/pay') ?>" method="POST" id="pay-form">
                <?= csrf_field() ?>
                <input type="hidden" name="bill_id" value="<?= $bill['id'] ?>">

                <h5 class="fw-bold text-slate mb-3"><i class="bi bi-wallet2 text-success me-2"></i>Collect Invoice Payment</h5>
                
                <div class="bg-light p-3 rounded-3 mb-4 small">
                    <div class="row g-2 mb-2">
                        <div class="col-6 text-muted">Invoice No:</div>
                        <div class="col-6 text-end fw-bold">INV-<?= esc(sprintf("%05d", $bill['id'])) ?></div>
                        
                        <div class="col-6 text-muted">Patient:</div>
                        <div class="col-6 text-end fw-semibold"><?= esc($bill['patient_name']) ?></div>
                    </div>
                    <hr class="my-2 text-muted">
                    
                    <div class="row g-2 align-items-center mb-2">
                        <div class="col-6 text-muted">Base Subtotal:</div>
                        <div class="col-6 text-end fw-semibold">₹<span id="display-subtotal"><?= esc(number_format((float)$bill['subtotal'], 2)) ?></span></div>
                    </div>
                    
                    <div class="row g-2 align-items-center mb-2">
                        <div class="col-6 text-muted">Discount Applied:</div>
                        <div class="col-6 text-end text-success">- ₹<span id="display-discount"><?= esc(number_format((float)$bill['discount'], 2)) ?></span></div>
                    </div>
                    
                    <div class="row g-2 align-items-center mb-2">
                        <div class="col-6 text-muted">Base Tax:</div>
                        <div class="col-6 text-end">+ ₹<span id="display-tax"><?= esc(number_format((float)$bill['tax'], 2)) ?></span></div>
                    </div>
                    
                    <div class="row g-2 align-items-center mb-2">
                        <div class="col-6 text-muted">Add GST (INR):</div>
                        <div class="col-6">
                            <input type="number" class="form-control form-control-sm text-end" id="gst-input" name="gst" value="<?= esc($bill['gst']) ?>" step="5.00" min="0.00" style="max-width: 100px; float: right;">
                        </div>
                    </div>
                    
                    <hr class="my-2 text-muted">
                    
                    <div class="row g-2 align-items-center">
                        <div class="col-6 fw-bold text-slate fs-5">Total Bill:</div>
                        <div class="col-6 text-end fw-bold text-success fs-4">₹<span id="display-total"><?= esc(number_format((float)$bill['total'], 2)) ?></span></div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="paid_amount" class="form-label small fw-semibold">Collected Amount (INR) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control form-control-sm text-success fw-bold fs-5" id="paid_amount" name="paid_amount" value="<?= esc($bill['outstanding']) ?>" step="0.50" min="0.00" max="<?= esc($bill['total']) ?>" required>
                    <div class="form-text small">Enter amount received from patient. Maximum: ₹<?= esc(number_format((float)$bill['total'], 2)) ?></div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold d-block mb-2">Payment Method</label>
                    <select class="form-control form-control-sm form-select" id="payment_method" name="payment_method" required>
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="upi">UPI / QR Scan</option>
                    </select>
                </div>

                <div class="text-end pt-3 border-top">
                    <a href="<?= site_url('/admin/billing') ?>" class="btn btn-outline-secondary btn-sm px-3 me-2">Cancel</a>
                    <button type="submit" class="btn btn-success btn-sm px-4 shadow-sm">
                        <i class="bi bi-check-circle-fill me-1"></i> Save Collection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const gstInput = document.getElementById('gst-input');
    const paidInput = document.getElementById('paid_amount');
    
    const subtotal = parseFloat(<?= (float)$bill['subtotal'] ?>);
    const discount = parseFloat(<?= (float)$bill['discount'] ?>);
    const baseTax = parseFloat(<?= (float)$bill['tax'] ?>);

    function reCalculateTotal() {
        const gst = parseFloat(gstInput.value) || 0.00;
        const total = subtotal - discount + baseTax + gst;
        
        document.getElementById('display-total').innerText = total.toFixed(2);
        paidInput.max = total;
        paidInput.value = total.toFixed(2);
    }

    gstInput.addEventListener('input', reCalculateTotal);
});
</script>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
