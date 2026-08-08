<?php 
$activePage = 'inventory';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Low Stock Directory -->
<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold text-slate mb-1"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Low Stock Warnings</h5>
            <p class="text-muted small mb-0">List of pharmaceuticals whose aggregate stock levels are at or below minimum threshold limits.</p>
        </div>
        <a href="<?= site_url('/admin/inventory') ?>" class="btn btn-outline-secondary btn-sm px-3">
            Back to Pharmacy
        </a>
    </div>
    
    <div class="table-responsive border-0 shadow-none">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>SKU Code</th>
                    <th>Category</th>
                    <th>Minimum Level</th>
                    <th>Aggregate Quantity</th>
                    <th>Reorder Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($lowStock)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">No medicines currently below safety stock thresholds.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($lowStock as $row): ?>
                        <?php $isOut = (int)$row['total_qty'] === 0; ?>
                        <tr class="<?= $isOut ? 'table-danger' : 'table-warning' ?>">
                            <td>
                                <div class="fw-bold text-slate"><?= esc($row['name']) ?></div>
                                <span class="text-muted small">Generic: <?= esc($row['generic_name'] ?: 'N/A') ?></span>
                            </td>
                            <td><span class="badge bg-light text-slate border"><?= esc($row['sku']) ?></span></td>
                            <td><?= esc($row['category']) ?></td>
                            <td><?= esc((string)$row['min_stock_level']) ?> <?= esc($row['unit']) ?></td>
                            <td>
                                <h5 class="mb-0 fw-bold <?= $isOut ? 'text-danger' : 'text-slate' ?>">
                                    <?= esc((string)$row['total_qty']) ?>
                                </h5>
                            </td>
                            <td>
                                <?php if ($isOut): ?>
                                    <span class="badge bg-danger px-2.5 py-1.5 rounded-pill"><i class="bi bi-x-circle me-1"></i> Out of Stock</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark px-2.5 py-1.5 rounded-pill"><i class="bi bi-exclamation-triangle me-1"></i> Low Stock</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
