<?php 
$activePage = 'billing';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Refund Form Container -->
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-4 text-slate">
            <form action="<?= site_url('/admin/billing/refund/save') ?>" method="POST" onsubmit="return confirm('Process this refund? This will adjust paid and outstanding balance columns.');">
                <?= csrf_field() ?>
                <input type="hidden" name="bill_id" value="<?= $bill['id'] ?>">

                <h5 class="fw-bold text-slate mb-3"><i class="bi bi-arrow-counterclockwise text-warning me-2"></i>Issue Invoice Refund</h5>
                
                <div class="bg-light p-3 rounded-3 mb-4 small">
                    <div class="row g-2 mb-1">
                        <div class="col-6 text-muted">Invoice No:</div>
                        <div class="col-6 text-end fw-bold">INV-<?= esc(sprintf("%05d", $bill['id'])) ?></div>
                        
                        <div class="col-6 text-muted">Patient:</div>
                        <div class="col-6 text-end fw-semibold"><?= esc($bill['patient_name']) ?></div>
                        
                        <div class="col-6 text-muted">Total Invoice:</div>
                        <div class="col-6 text-end">₹<?= esc(number_format((float)$bill['total'], 2)) ?></div>

                        <div class="col-6 text-muted">Paid Amount:</div>
                        <div class="col-6 text-end text-success fw-bold">₹<?= esc(number_format((float)$bill['paid_amount'], 2)) ?></div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="refund_amount" class="form-label small fw-semibold">Refund Amount (INR) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control form-control-sm text-danger fw-bold fs-5" id="refund_amount" name="refund_amount" value="<?= esc($bill['paid_amount']) ?>" step="0.50" min="0.50" max="<?= esc($bill['paid_amount']) ?>" required>
                    <div class="form-text small">Enter refund value. Maximum: ₹<?= esc(number_format((float)$bill['paid_amount'], 2)) ?></div>
                </div>

                <div class="mb-4">
                    <label for="refund_reason" class="form-label small fw-semibold">Reason for Refund <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="refund_reason" name="refund_reason" rows="3" required placeholder="Describe reason for medical checkout refund..."></textarea>
                </div>

                <div class="text-end pt-3 border-top">
                    <a href="<?= site_url('/admin/billing') ?>" class="btn btn-outline-secondary btn-sm px-3 me-2">Cancel</a>
                    <button type="submit" class="btn btn-warning btn-sm px-4 shadow-sm text-white">
                        <i class="bi bi-check-lg me-1"></i> Settle Refund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
