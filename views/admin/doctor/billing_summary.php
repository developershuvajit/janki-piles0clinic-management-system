<?php 
$activePage = 'doctor_billing';
include VIEWS_PATH . '/layout/doctor_header.php'; 
?>

<!-- ============================================
     PAGE CSS
     ============================================ -->
<link rel="stylesheet" href="<?= asset('css/datatable.css') ?>">

<style>
.text-slate {
    color: #0b1a2b;
}

/* Invoice Badge */
.inv-badge {
    background: #dbeafe;
    color: #1d4ed8;
    font-weight: 700;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.8rem;
}

/* ===== ACTION BUTTONS ===== */
.action-group {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.btn-action-breakdown {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 0.4rem 1rem;
    border-radius: 8px;
    font-size: 0.78rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
    background: #eef2ff;
    color: #4f46e5;
    border: 1px solid #c7d2fe;
}
.btn-action-breakdown:hover {
    background: #4f46e5;
    color: #fff;
    border-color: #4f46e5;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}
.btn-action-breakdown i {
    font-size: 0.9rem;
}

/* Payment Status Badges */
.status-paid {
    background: #d1fae5;
    color: #059669;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
}
.status-unpaid {
    background: #fef3c7;
    color: #d97706;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
}
.status-partial {
    background: #e6f7fe;
    color: #0e7c9e;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
}
.status-refunded {
    background: #fee2e2;
    color: #dc2626;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
}
.status-cancelled {
    background: #f1f5f9;
    color: #64748b;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Amount styling */
.amount-total {
    font-weight: 700;
    color: #0b1a2b;
}
.amount-paid {
    font-weight: 700;
    color: #059669;
}
.amount-pending {
    font-weight: 700;
    color: #dc2626;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 576px) {
    .action-group {
        flex-direction: column;
        gap: 4px;
        align-items: stretch;
    }
    .btn-action-breakdown {
        justify-content: center;
        padding: 0.3rem 0.7rem;
        font-size: 0.7rem;
    }
}
</style>

<!-- ============================================
     HEADER
     ============================================ -->
<div class="d-flex justify-content-between align-items-center mb-4 mt-4 mx-4">
    <div>
        <h4 class="fw-bold text-slate mb-1"><i class="bi bi-receipt-cutoff text-success me-2"></i>Patient Billing Summaries (Read Only)</h4>
        <p class="text-muted small mb-0">Review consultation, procedure, surgery, and medicine charge breakdowns for active patients</p>
    </div>
    <div>
        <span class="badge bg-light text-dark border px-3 py-2">
            <i class="bi bi-receipt me-1"></i> <?= count($bills ?? []) ?> Invoices
        </span>
    </div>
</div>

<!-- ============================================
     BILLING TABLE
     ============================================ -->
<div class="datatable-wrapper">
    <div class="datatable-header">
        <h5>Billing Summaries <small><?= count($bills ?? []) ?> invoices</small></h5>
    </div>

    <div class="table-responsive">
        <table id="billingTable" class="table-custom" style="width:100%">
            <thead>
                <tr>
                    <th class="sno">#</th>
                    <th style="width:120px;">Invoice #</th>
                    <th style="min-width:180px;">Patient Name</th>
                    <th style="width:110px;">Type</th>
                    <th style="width:120px;">Total Bill</th>
                    <th style="width:120px;">Paid Amount</th>
                    <th style="width:120px;">Pending Balance</th>
                    <th style="width:140px;">Payment Status</th>
                    <th style="width:150px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($bills)):
                    $sn = 1;
                    foreach ($bills as $b): 
                        $pending = max(0, (float)$b['total'] - (float)$b['paid_amount']);
                        $status = $b['payment_status'] ?? 'unpaid';
                    ?>
                        <tr>
                            <td class="sno"><?= $sn++ ?></td>
                            <td>
                                <span class="inv-badge">#INV-<?= esc(sprintf("%05d", $b['id'])) ?></span>
                            </td>
                            <td>
                                <div class="fw-bold text-slate"><?= esc($b['patient_name']) ?></div>
                                <span class="text-muted small" style="font-size: 0.75rem;">Code: <?= esc($b['patient_code']) ?></span>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border text-uppercase" style="font-size: 0.7rem; font-weight: 600;">
                                    <?= esc($b['type']) ?>
                                </span>
                            </td>
                            <td class="amount-total">₹<?= number_format((float)$b['total'], 2) ?></td>
                            <td class="amount-paid">₹<?= number_format((float)$b['paid_amount'], 2) ?></td>
                            <td class="amount-pending">₹<?= number_format($pending, 2) ?></td>
                            <td>
                                <?php if ($status === 'paid'): ?>
                                    <span class="status-paid"><i class="bi bi-check-circle-fill me-1"></i> Paid</span>
                                <?php elseif ($status === 'partial'): ?>
                                    <span class="status-partial"><i class="bi bi-clock-history me-1"></i> Partial</span>
                                <?php elseif ($status === 'refunded'): ?>
                                    <span class="status-refunded"><i class="bi bi-arrow-counterclockwise me-1"></i> Refunded</span>
                                <?php elseif ($status === 'cancelled'): ?>
                                    <span class="status-cancelled"><i class="bi bi-x-circle-fill me-1"></i> Cancelled</span>
                                <?php else: ?>
                                    <span class="status-unpaid"><i class="bi bi-hourglass-split me-1"></i> Unpaid</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-group">
                                    <a href="<?= site_url('/doctor/billing-summary/view/' . $b['id']) ?>" 
                                       class="btn-action-breakdown" 
                                       title="View Breakdown">
                                        <i class="bi bi-eye"></i> Breakdown
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="9" style="text-align:center;padding:2.5rem 1rem;color:#94a3b8;">
                            <i class="bi bi-receipt fs-3 d-block mb-2"></i>
                            No billing summaries found.
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

<?php include VIEWS_PATH . '/layout/doctor_footer.php'; ?>