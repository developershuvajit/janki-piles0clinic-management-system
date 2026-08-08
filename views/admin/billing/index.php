<?php 
$activePage = 'billing';
include VIEWS_PATH . '/layout/admin_header.php'; 

// Calculate aggregate sums
$totalBilled = 0.00;
$totalPaid = 0.00;
$totalOutstanding = 0.00;
$totalRefunded = 0.00;
foreach ($invoices as $inv) {
    $totalBilled += (float)$inv['total'];
    $totalPaid += (float)$inv['paid_amount'];
    $totalOutstanding += (float)$inv['outstanding'];
    $totalRefunded += (float)$inv['refunded_amount'];
}
?>

<!-- Statistics Overview -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card p-3 border-0 shadow-sm bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <h6 class="text-muted text-uppercase mb-1 small fw-bold">Total Invoiced</h6>
                <h3 class="mb-0 fw-bold text-slate">₹<?= esc(number_format($totalBilled, 2)) ?></h3>
            </div>
            <div class="bg-primary bg-opacity-10 p-3 rounded text-primary fs-4">
                <i class="bi bi-receipt"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card p-3 border-0 shadow-sm bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <h6 class="text-muted text-uppercase mb-1 small fw-bold">Total Collected</h6>
                <h3 class="mb-0 fw-bold text-success">₹<?= esc(number_format($totalPaid, 2)) ?></h3>
            </div>
            <div class="bg-success bg-opacity-10 p-3 rounded text-success fs-4">
                <i class="bi bi-wallet2"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card p-3 border-0 shadow-sm bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <h6 class="text-muted text-uppercase mb-1 small fw-bold">Outstanding Balance</h6>
                <h3 class="mb-0 fw-bold text-danger">₹<?= esc(number_format($totalOutstanding, 2)) ?></h3>
            </div>
            <div class="bg-danger bg-opacity-10 p-3 rounded text-danger fs-4">
                <i class="bi bi-exclamation-circle"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card p-3 border-0 shadow-sm bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <h6 class="text-muted text-uppercase mb-1 small fw-bold">Refunded Amount</h6>
                <h3 class="mb-0 fw-bold text-warning">₹<?= esc(number_format($totalRefunded, 2)) ?></h3>
            </div>
            <div class="bg-warning bg-opacity-10 p-3 rounded text-warning fs-4">
                <i class="bi bi-arrow-counterclockwise"></i>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Ledger Table -->
<div class="table-responsive border-0 shadow-sm rounded-3 bg-white">
    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
        <thead class="bg-light text-slate">
            <tr>
                <th>Invoice ID</th>
                <th>Patient Details</th>
                <th>Clinic Branch</th>
                <th>Service Type</th>
                <th>Tax & GST</th>
                <th>Total Bill</th>
                <th>Collected</th>
                <th>Outstanding</th>
                <th>Refunds</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($invoices)): ?>
                <tr>
                    <td colspan="11" class="text-center py-5 text-muted">No billing invoices found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($invoices as $inv): ?>
                    <tr>
                        <td class="fw-bold text-slate">INV-<?= esc(sprintf("%05d", $inv['id'])) ?></td>
                        <td>
                            <div class="fw-semibold text-slate"><?= esc($inv['patient_name']) ?></div>
                            <span class="text-muted small">Code: <?= esc($inv['patient_code']) ?></span>
                        </td>
                        <td class="small"><?= esc($inv['branch_name']) ?></td>
                        <td><span class="badge bg-light text-secondary border"><?= esc(strtoupper($inv['type'])) ?></span></td>
                        <td>
                            Tax: ₹<?= esc(number_format((float)$inv['tax'], 2)) ?><br>
                            GST: ₹<?= esc(number_format((float)$inv['gst'], 2)) ?>
                        </td>
                        <td class="fw-bold text-slate">₹<?= esc(number_format((float)$inv['total'], 2)) ?></td>
                        <td class="text-success fw-bold">₹<?= esc(number_format((float)$inv['paid_amount'], 2)) ?></td>
                        <td class="text-danger fw-bold">₹<?= esc(number_format((float)$inv['outstanding'], 2)) ?></td>
                        <td class="text-warning">₹<?= esc(number_format((float)$inv['refunded_amount'], 2)) ?></td>
                        <td>
                            <?php if ($inv['payment_status'] === 'paid'): ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded">Paid</span>
                            <?php elseif ($inv['payment_status'] === 'partial'): ?>
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 rounded">Partial</span>
                            <?php elseif ($inv['payment_status'] === 'refunded'): ?>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1 rounded">Refunded</span>
                            <?php else: ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded">Unpaid</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end text-nowrap">
                            <?php if ($inv['payment_status'] !== 'paid' && $inv['payment_status'] !== 'refunded'): ?>
                                <a href="<?= site_url('/admin/billing/collect/' . $inv['id']) ?>" class="btn btn-sm btn-primary px-2.5 py-1 me-1 shadow-sm">Collect</a>
                            <?php endif; ?>
                            
                            <?php if ((float)$inv['paid_amount'] > 0.00): ?>
                                <a href="<?= site_url('/admin/billing/refund/' . $inv['id']) ?>" class="btn btn-sm btn-outline-warning px-2.5 py-1 me-1">Refund</a>
                            <?php endif; ?>
                            
                            <a href="<?= site_url('/admin/billing/receipt/' . $inv['id']) ?>" class="btn btn-sm btn-light border px-2 py-1"><i class="bi bi-printer"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
