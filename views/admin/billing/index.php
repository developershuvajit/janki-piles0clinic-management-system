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

<!-- ============================================
     PAGE CSS
     ============================================ -->
<link rel="stylesheet" href="<?= asset('css/datatable.css') ?>">

<!-- ============================================
     STATISTICS OVERVIEW
     ============================================ -->
<div class="row mt-4">
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

<!-- ============================================
     INVOICE LEDGER TABLE
     ============================================ -->
<div class="datatable-wrapper mt-4">
    <div class="datatable-header">
        <h5>Invoice Ledger <small><?= count($invoices ?? []) ?> invoices</small></h5>
        <div>
            <a href="<?= site_url('/admin/billing/create') ?>" class="btn-register" style="background: #6366f1; border-color: #6366f1;">
                <i class="bi bi-plus-circle-fill me-1"></i> New Invoice
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table id="billingTable" class="table-custom" style="width:100%">
            <thead>
                <tr>
                    <th class="sno">#</th>
                    <th style="min-width:100px;">Invoice ID</th>
                    <th style="min-width:160px;">Patient Details</th>
                    <th style="width:120px;">Branch</th>
                    <th style="width:100px;">Service Type</th>
                    <th style="width:130px;">Tax & GST</th>
                    <th style="width:100px;">Total Bill</th>
                    <th style="width:100px;">Collected</th>
                    <th style="width:100px;">Outstanding</th>
                    <th style="width:100px;">Refunds</th>
                    <th style="width:100px;">Status</th>
                    <th style="width:160px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($invoices)):
                    $sn = 1;
                    foreach ($invoices as $inv): ?>
                        <tr>
                            <td class="sno"><?= $sn++ ?></td>
                            <td class="fw-bold text-slate">INV-<?= esc(sprintf("%05d", $inv['id'])) ?></td>
                            <td>
                                <div class="fw-bold text-slate"><?= esc($inv['patient_name']) ?></div>
                                <span class="text-muted small" style="font-size: 0.78rem;">Code: <?= esc($inv['patient_code']) ?></span>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-dark">
                                    <?= esc($inv['branch_name'] ?? 'N/A') ?>
                                </span>
                            </td>
                            <td><span class="badge bg-light text-secondary border"><?= esc(strtoupper($inv['type'] ?? 'N/A')) ?></span></td>
                            <td style="font-size: 0.78rem;">
                                Tax: ₹<?= esc(number_format((float)($inv['tax'] ?? 0), 2)) ?><br>
                                GST: ₹<?= esc(number_format((float)($inv['gst'] ?? 0), 2)) ?>
                            </td>
                            <td class="fw-bold text-slate">₹<?= esc(number_format((float)$inv['total'], 2)) ?></td>
                            <td class="text-success fw-bold">₹<?= esc(number_format((float)$inv['paid_amount'], 2)) ?></td>
                            <td class="text-danger fw-bold">₹<?= esc(number_format((float)$inv['outstanding'], 2)) ?></td>
                            <td class="text-warning">₹<?= esc(number_format((float)$inv['refunded_amount'], 2)) ?></td>
                            <td>
                                <?php if ($inv['payment_status'] === 'paid'): ?>
                                    <span class="badge-status active">Paid</span>
                                <?php elseif ($inv['payment_status'] === 'partial'): ?>
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-1.5 rounded">Partial</span>
                                <?php elseif ($inv['payment_status'] === 'refunded'): ?>
                                    <span class="badge-status inactive">Refunded</span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1.5 rounded">Unpaid</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-group">
                                    <?php if ($inv['payment_status'] !== 'paid' && $inv['payment_status'] !== 'refunded'): ?>
                                        <a href="<?= site_url('/admin/billing/collect/' . $inv['id']) ?>" class="btn-action" title="Collect Payment" style="color: #10b981;">
                                            <i class="bi bi-cash-stack"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ((float)$inv['paid_amount'] > 0.00): ?>
                                        <a href="<?= site_url('/admin/billing/refund/' . $inv['id']) ?>" class="btn-action" title="Refund" style="color: #f59e0b;">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <a href="<?= site_url('/admin/billing/receipt/' . $inv['id']) ?>" class="btn-action" title="Print Receipt" style="color: #6366f1;">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                    
                                    <a href="<?= site_url('/admin/billing/edit/' . $inv['id']) ?>" class="btn-action" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="12" style="text-align:center;padding:2.5rem 1rem;color:#94a3b8;">
                            <i class="bi bi-receipt fs-3 d-block mb-2"></i>
                            No billing invoices found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ============================================
     DATATABLES LIBS + INIT
     ============================================ -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
    $('#billingTable').DataTable({
        pageLength: 25,
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        order: [[0, 'asc']]
    });
});
</script>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>