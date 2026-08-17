<?php 
$activePage = 'billing';
include VIEWS_PATH . '/layout/reception_header.php'; 
?>

<!-- ============================================
     PAGE CSS
     ============================================ -->
<link rel="stylesheet" href="<?= asset('css/datatable.css') ?>">

<!-- ============================================
     BILLING DIRECTORY TABLE
     ============================================ -->
<div class="datatable-wrapper mt-4">
    <div class="datatable-header">
        <h5>Billing Directory <small><?= count($bills ?? []) ?> invoices</small></h5>
    </div>

    <div class="table-responsive">
        <table id="billingTable" class="table-custom" style="width:100%">
            <thead>
                <tr>
                    <th class="sno">#</th>
                    <th style="width:110px;">Invoice #</th>
                    <th style="min-width:180px;">Patient Details</th>
                    <th style="width:140px;">Service Type</th>
                    <th style="width:100px;">Subtotal</th>
                    <th style="width:140px;">Tax & Discounts</th>
                    <th style="width:110px;">Total Payable</th>
                    <th style="width:150px;">Payment Status</th>
                    <th style="width:180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($bills)):
                    $sn = 1;
                    foreach ($bills as $bill): 
                        $status = strtolower($bill['payment_status'] ?? 'unpaid');
                        $badgeClass = 'bg-warning bg-opacity-10 text-warning border-warning';
                        $statusText = 'Unpaid';
                        $statusIcon = 'hourglass-split';
                        
                        if ($status === 'paid') {
                            $badgeClass = 'bg-success bg-opacity-10 text-success border-success';
                            $statusText = 'Paid';
                            $statusIcon = 'check-circle-fill';
                        } elseif ($status === 'partial') {
                            $badgeClass = 'bg-info bg-opacity-10 text-info border-info';
                            $statusText = 'Partial';
                            $statusIcon = 'clock-history';
                        } elseif ($status === 'refunded') {
                            $badgeClass = 'bg-danger bg-opacity-10 text-danger border-danger';
                            $statusText = 'Refunded';
                            $statusIcon = 'arrow-counterclockwise';
                        } elseif ($status === 'cancelled') {
                            $badgeClass = 'bg-secondary bg-opacity-10 text-secondary border-secondary';
                            $statusText = 'Cancelled';
                            $statusIcon = 'x-circle-fill';
                        }
                        
                        $outstanding = (float)($bill['outstanding'] ?? 0);
                        $total = (float)($bill['total'] ?? 0);
                        $paid = (float)($bill['paid_amount'] ?? 0);
                        $showCollectBtn = ($status === 'unpaid' || $status === 'partial');
                        $showReceiptBtn = ($status === 'paid' || $status === 'partial');
                    ?>
                        <tr>
                            <td class="sno"><?= $sn++ ?></td>
                            <td class="fw-bold text-slate">INV-<?= esc(sprintf("%05d", $bill['id'])) ?></td>
                            <td>
                                <div class="fw-bold text-slate"><?= esc($bill['patient_name'] ?? 'N/A') ?></div>
                                <span class="text-muted small" style="font-size: 0.78rem;">ID: <?= esc($bill['patient_code'] ?? 'N/A') ?></span>
                            </td>
                            <td>
                                <?php if (($bill['type'] ?? '') === 'opd'): ?>
                                    <span class="badge bg-light text-primary border">OPD Consultation</span>
                                <?php elseif (($bill['type'] ?? '') === 'ipd'): ?>
                                    <span class="badge bg-light text-danger border">IPD Bed Stay</span>
                                <?php elseif (($bill['type'] ?? '') === 'pharmacy'): ?>
                                    <span class="badge bg-light text-success border">Pharmacy</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-info border">Other</span>
                                <?php endif; ?>
                            </td>
                            <td>₹<?= esc(number_format((float)($bill['subtotal'] ?? 0), 2)) ?></td>
                            <td class="small text-slate" style="font-size: 0.78rem;">
                                Disc: ₹<?= esc(number_format((float)($bill['discount'] ?? 0), 2)) ?><br>
                                Tax: ₹<?= esc(number_format((float)($bill['tax'] ?? 0), 2)) ?>
                            </td>
                            <td class="fw-bold text-slate">₹<?= esc(number_format($total, 2)) ?></td>
                            <td>
                                <span class="badge <?= $badgeClass ?> border px-2.5 py-1.5 rounded">
                                    <i class="bi bi-<?= $statusIcon ?> me-1"></i>
                                    <?= $statusText ?>
                                    <?php if ($status === 'partial'): ?>
                                        <span class="d-block small">Paid: ₹<?= esc(number_format($paid, 2)) ?></span>
                                        <span class="d-block small">Due: ₹<?= esc(number_format($outstanding, 2)) ?></span>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-group">
                                    <?php if ($showCollectBtn): ?>
                                        <a href="<?= site_url('/reception/billing/collect/' . $bill['id']) ?>" 
                                           class="btn-action" 
                                           title="Collect Payment" 
                                           style="color: #6366f1;">
                                            <i class="bi bi-cash-coin"></i>
                                        </a>
                                    <?php elseif ($showReceiptBtn): ?>
                                        <a href="<?= site_url('/reception/billing/receipt/' . $bill['id']) ?>" 
                                           class="btn-action" 
                                           title="Print Receipt" 
                                           style="color: #10b981;">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    <?php elseif ($status === 'refunded'): ?>
                                        <span class="badge bg-secondary">Refunded</span>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                    
                                    <?php if ($status === 'paid' || $status === 'partial'): ?>
                                        <a href="<?= site_url('/reception/billing/refund/' . $bill['id']) ?>" 
                                           class="btn-action" 
                                           title="Refund" 
                                           style="color: #f59e0b;">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="9" style="text-align:center;padding:2.5rem 1rem;color:#94a3b8;">
                            <i class="bi bi-receipt fs-3 d-block mb-2"></i>
                            No billing invoices located in active directory.
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

<?php include VIEWS_PATH . '/layout/reception_footer.php'; ?>