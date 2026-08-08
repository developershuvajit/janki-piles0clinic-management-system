<?php 
$activePage = 'inventory';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Purchase Form Card -->
<div class="card border-0 shadow-sm p-4" style="max-width: 600px; margin: 0 auto;">
    <form action="<?= site_url('/admin/inventory/purchase/save') ?>" method="POST">
        <?= csrf_field() ?>

        <h5 class="fw-bold text-slate mb-3"><i class="bi bi-cart-plus text-success me-2"></i>Record Purchase Supply Batch</h5>
        <p class="text-muted small">Update pharmacy inventory logs by adding incoming pharmaceutical supply batches.</p>
        
        <div class="mb-3">
            <label for="medicine_id" class="form-label small fw-semibold">Select Medicine <span class="text-danger">*</span></label>
            <select class="form-control form-control-sm form-select" id="medicine_id" name="medicine_id" required>
                <option value="" disabled selected>Choose Medicine</option>
                <?php foreach ($medicines as $med): ?>
                    <option value="<?= $med['id'] ?>"><?= esc($med['name']) ?> (SKU: <?= esc($med['sku']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-md-6">
                <label for="batch_number" class="form-label small fw-semibold">Batch Number <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm" id="batch_number" name="batch_number" required placeholder="e.g. B-PCM-09">
            </div>
            <div class="col-md-6">
                <label for="expiry_date" class="form-label small fw-semibold">Expiry Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control form-control-sm" id="expiry_date" name="expiry_date" required min="<?= date('Y-m-d') ?>">
            </div>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-md-6">
                <label for="quantity" class="form-label small fw-semibold">Quantity Mapped <span class="text-danger">*</span></label>
                <input type="number" class="form-control form-control-sm" id="quantity" name="quantity" required min="1" placeholder="e.g. 100">
            </div>
            <div class="col-md-6">
                <label for="supplier_id" class="form-label small fw-semibold">Supplier / Wholesaler</label>
                <select class="form-control form-control-sm form-select" id="supplier_id" name="supplier_id">
                    <option value="">None / Direct Purchase</option>
                    <?php foreach ($suppliers as $sup): ?>
                        <option value="<?= $sup['id'] ?>"><?= esc($sup['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row g-2 mb-4">
            <div class="col-md-6">
                <label for="purchase_price" class="form-label small fw-semibold">Purchase Price (Per Unit INR) <span class="text-danger">*</span></label>
                <input type="number" class="form-control form-control-sm" id="purchase_price" name="purchase_price" required step="0.05" min="0.05" placeholder="0.00">
            </div>
            <div class="col-md-6">
                <label for="selling_price" class="form-label small fw-semibold">Selling Price (Per Unit INR) <span class="text-danger">*</span></label>
                <input type="number" class="form-control form-control-sm" id="selling_price" name="selling_price" required step="0.05" min="0.05" placeholder="0.00">
            </div>
        </div>

        <div class="text-end pt-2 border-top">
            <a href="<?= site_url('/admin/inventory') ?>" class="btn btn-outline-secondary btn-sm px-3 me-2">Cancel</a>
            <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
                <i class="bi bi-cloud-upload me-1"></i> Log Batch
            </button>
        </div>
    </form>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
