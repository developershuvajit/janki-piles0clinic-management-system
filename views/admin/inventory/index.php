<?php 
$activePage = 'inventory';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Statistics Summary & Quick Actions -->
<div class="row mb-4 align-items-center">
    <div class="col-md-6 mb-2">
        <h5 class="fw-bold text-slate mb-1"><i class="bi bi-capsule text-success me-2"></i>Pharmacy Stock Master</h5>
        <p class="text-muted small mb-0">Monitor active pharmaceutical batches, expiry dates, and supplier catalogs.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="<?= site_url('/admin/inventory/low-stock') ?>" class="btn btn-outline-danger btn-sm px-3 me-2">
            <i class="bi bi-exclamation-triangle me-1"></i> Low Stock Alerts
        </a>
        <a href="<?= site_url('/admin/inventory/purchase') ?>" class="btn btn-primary btn-sm px-3 shadow-sm me-2">
            <i class="bi bi-cart-plus me-1"></i> Purchase Batch
        </a>
        <button type="button" class="btn btn-success btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#supplierModal">
            <i class="bi bi-person-plus me-1"></i> Add Supplier
        </button>
    </div>
</div>

<!-- Active Stock Batches Table -->
<div class="card border-0 shadow-sm p-4">
    <h6 class="fw-bold text-slate mb-3"><i class="bi bi-list-stars text-success me-2"></i>Active Pharmaceutical Batches</h6>
    
    <div class="table-responsive border-0 shadow-none">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>SKU & Category</th>
                    <th>Batch ID</th>
                    <th>Expiry Date</th>
                    <th>Available Stock</th>
                    <th>Rates</th>
                    <th>Supplier Mapped</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($stocks)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-capsule-pill fs-3 d-block mb-2"></i>
                            No medicine batches currently stocked in inventory.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($stocks as $st): ?>
                        <?php 
                        $isExpired = strtotime($st['expiry_date']) < time();
                        $isLow = $st['quantity'] <= 5;
                        ?>
                        <tr class="<?= $isExpired ? 'table-danger opacity-75' : ($isLow ? 'table-warning' : '') ?>">
                            <td>
                                <div class="fw-bold text-slate"><?= esc($st['medicine_name']) ?></div>
                                <span class="text-muted small" style="font-size: 0.76rem;">Unit: <?= esc($st['unit']) ?></span>
                            </td>
                            <td>
                                <span class="badge bg-light text-slate border"><?= esc($st['sku']) ?></span>
                                <div class="text-muted small mt-1"><?= esc($st['category']) ?></div>
                            </td>
                            <td class="fw-semibold text-slate">#<?= esc($st['batch_number']) ?></td>
                            <td>
                                <div class="fw-semibold <?= $isExpired ? 'text-danger' : 'text-slate' ?>">
                                    <?= esc($st['expiry_date']) ?>
                                </div>
                                <?php if ($isExpired): ?>
                                    <span class="x-small text-danger fw-bold"><i class="bi bi-exclamation-octagon"></i> EXPIRED</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <h5 class="mb-0 fw-bold <?= $isExpired ? 'text-muted' : ($isLow ? 'text-warning' : 'text-success') ?>">
                                    <?= esc((string)$st['quantity']) ?>
                                </h5>
                            </td>
                            <td>
                                Pur: ₹<?= esc(number_format((float)$st['purchase_price'], 2)) ?><br>
                                Sell: <strong class="text-success">₹<?= esc(number_format((float)$st['selling_price'], 2)) ?></strong>
                            </td>
                            <td><?= esc($st['supplier_name'] ?? 'N/A') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal to Add Supplier -->
<div class="modal fade" id="supplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg text-slate">
            <form action="<?= site_url('/admin/inventory/supplier/save') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2"></i>Add Supplier Record</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="sup-name" class="form-label small fw-semibold">Supplier/Wholesaler Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="sup-name" name="name" required placeholder="e.g. MediLife Wholesale">
                    </div>
                    <div class="mb-3">
                        <label for="sup-phone" class="form-label small fw-semibold">Contact Phone Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="sup-phone" name="phone" required placeholder="e.g. 022-25556677">
                    </div>
                    <div class="mb-3">
                        <label for="sup-email" class="form-label small fw-semibold">Email Address</label>
                        <input type="email" class="form-control form-control-sm" id="sup-email" name="email" placeholder="e.g. sales@wholesaler.com">
                    </div>
                    <div class="mb-3">
                        <label for="sup-address" class="form-label small fw-semibold">Physical Address</label>
                        <textarea class="form-control form-control-sm" id="sup-address" name="address" rows="2" placeholder="Street address, City, Pincode"></textarea>
                    </div>
                </div>
                <div class="modal-footer pt-0 border-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-4">Save Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
