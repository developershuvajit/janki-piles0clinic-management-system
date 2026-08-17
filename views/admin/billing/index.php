<?php
$activePage = 'billing';
include VIEWS_PATH . '/layout/admin_header.php';

// ============================================
// CALCULATE AGGREGATE TOTALS
// ============================================
$totalBilled = 0.00;
$totalPaid = 0.00;
$totalOutstanding = 0.00;
$totalRefunded = 0.00;

foreach (($invoices ?? []) as $inv) {
    $totalBilled += (float)($inv['total'] ?? 0);
    $totalPaid += (float)($inv['paid_amount'] ?? 0);
    $totalOutstanding += (float)($inv['outstanding'] ?? 0);
    $totalRefunded += (float)($inv['refunded_amount'] ?? 0);
}
?>

<!-- ============================================
     PAGE CSS
     ============================================ -->
<link rel="stylesheet" href="<?= asset('css/datatable.css') ?>">

<style>
.text-slate {
    color: #0b1a2b;
}

/* ============================================
   STAT CARDS
   ============================================ */
.billing-stat-card {
    background: #fff;
    border: 0;
    border-radius: 14px;
    padding: 1rem 1.2rem;
    box-shadow: 0 2px 10px rgba(15, 23, 42, 0.05);
    height: 100%;
    transition: all 0.2s ease;
}

.billing-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(15, 23, 42, 0.08);
}

.billing-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
}

.billing-stat-label {
    color: #64748b;
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 3px;
}

.billing-stat-value {
    color: #0b1a2b;
    font-size: 1.35rem;
    font-weight: 750;
    line-height: 1.2;
}

/* ============================================
   TABLE
   ============================================ */
#billingTable {
    width: 100% !important;
}

#billingTable th,
#billingTable td {
    vertical-align: middle;
}

.invoice-id {
    font-weight: 700;
    color: #0b1a2b;
    white-space: nowrap;
}

.patient-name {
    font-weight: 700;
    color: #0b1a2b;
}

.patient-code {
    font-size: 0.74rem;
    color: #94a3b8;
}

.branch-badge {
    display: inline-block;
    padding: 0.3rem 0.55rem;
    border-radius: 6px;
    background: #f1f5f9;
    color: #334155;
    font-size: 0.72rem;
    font-weight: 600;
    white-space: nowrap;
}

.service-badge {
    display: inline-block;
    padding: 0.3rem 0.55rem;
    border-radius: 6px;
    background: #f8fafc;
    color: #475569;
    border: 1px solid #e2e8f0;
    font-size: 0.7rem;
    font-weight: 600;
    white-space: nowrap;
}

.tax-cell {
    font-size: 0.76rem;
    line-height: 1.5;
    color: #64748b;
}

.amount {
    white-space: nowrap;
    font-weight: 700;
}

/* ============================================
   ACTION BUTTONS
   ============================================ */
.action-group {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: nowrap;
}

.btn-action {
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    text-decoration: none;
    background: #fff;
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.btn-action:hover {
    transform: translateY(-1px);
    background: #f8fafc;
    box-shadow: 0 4px 10px rgba(15, 23, 42, 0.08);
}

.btn-action i {
    font-size: 0.9rem;
}

/* ============================================
   STATUS
   ============================================ */
.billing-status {
    display: inline-flex;
    align-items: center;
    padding: 0.3rem 0.65rem;
    border-radius: 7px;
    font-size: 0.72rem;
    font-weight: 700;
    white-space: nowrap;
}

.billing-status.paid {
    background: #d1fae5;
    color: #047857;
}

.billing-status.partial {
    background: #fef3c7;
    color: #b45309;
}

.billing-status.refunded {
    background: #f1f5f9;
    color: #64748b;
}

.billing-status.unpaid {
    background: #fee2e2;
    color: #dc2626;
}

/* ============================================
   DATATABLE BUTTONS
   ============================================ */
.dataTables_wrapper .dt-buttons {
    margin-bottom: 10px;
}

.dataTables_wrapper .dt-button {
    border-radius: 6px !important;
    border: 1px solid #e2e8f0 !important;
    background: #fff !important;
    color: #334155 !important;
    font-size: 0.78rem !important;
    padding: 5px 10px !important;
}

.dataTables_wrapper .dt-button:hover {
    background: #f8fafc !important;
    color: #0f172a !important;
}

/* ============================================
   RESPONSIVE
   ============================================ */
@media (max-width: 768px) {
    .billing-stat-value {
        font-size: 1.15rem;
    }

    .billing-stat-icon {
        width: 42px;
        height: 42px;
        font-size: 1.1rem;
    }

    .action-group {
        gap: 4px;
    }

    .btn-action {
        width: 32px;
        height: 32px;
    }
}
</style>

<!-- ============================================
     STATISTICS OVERVIEW
     ============================================ -->
<div class="row g-3 mt-3">

    <!-- Total Invoiced -->
    <div class="col-xl-3 col-md-6">
        <div class="billing-stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="billing-stat-label">Total Invoiced</div>
                    <div class="billing-stat-value">
                        ₹<?= esc(number_format($totalBilled, 2)) ?>
                    </div>
                </div>

                <div class="billing-stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-receipt"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Collected -->
    <div class="col-xl-3 col-md-6">
        <div class="billing-stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="billing-stat-label">Total Collected</div>
                    <div class="billing-stat-value text-success">
                        ₹<?= esc(number_format($totalPaid, 2)) ?>
                    </div>
                </div>

                <div class="billing-stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Outstanding -->
    <div class="col-xl-3 col-md-6">
        <div class="billing-stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="billing-stat-label">Outstanding Balance</div>
                    <div class="billing-stat-value text-danger">
                        ₹<?= esc(number_format($totalOutstanding, 2)) ?>
                    </div>
                </div>

                <div class="billing-stat-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-exclamation-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Refunded -->
    <div class="col-xl-3 col-md-6">
        <div class="billing-stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="billing-stat-label">Refunded Amount</div>
                    <div class="billing-stat-value text-warning">
                        ₹<?= esc(number_format($totalRefunded, 2)) ?>
                    </div>
                </div>

                <div class="billing-stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ============================================
     INVOICE LEDGER TABLE
     ============================================ -->
<div class="datatable-wrapper mt-4">

    <div class="datatable-header">

        <h5>
            Invoice Ledger
            <small><?= count($invoices ?? []) ?> invoices</small>
        </h5>

        <div>
            <a href="<?= site_url('/admin/billing/create') ?>"
               class="btn-register"
               style="background:#6366f1;border-color:#6366f1;">

                <i class="bi bi-plus-circle-fill me-1"></i>
                New Invoice

            </a>
        </div>

    </div>

    <div class="table-responsive">

        <table id="billingTable"
               class="table-custom"
               style="width:100%">

            <thead>
                <tr>

                    <th class="sno">#</th>

                    <th style="min-width:110px;">
                        Invoice ID
                    </th>

                    <th style="min-width:170px;">
                        Patient Details
                    </th>

                    <th style="width:120px;">
                        Branch
                    </th>

                    <th style="width:110px;">
                        Service Type
                    </th>

                    <th style="width:130px;">
                        Tax &amp; GST
                    </th>

                    <th style="width:110px;">
                        Total Bill
                    </th>

                    <th style="width:110px;">
                        Collected
                    </th>

                    <th style="width:110px;">
                        Outstanding
                    </th>

                    <th style="width:100px;">
                        Refunds
                    </th>

                    <th style="width:110px;">
                        Status
                    </th>

                    <th style="width:160px;">
                        Actions
                    </th>

                </tr>
            </thead>

            <tbody>

                <?php
                if (!empty($invoices)):
                    $sn = 1;

                    foreach ($invoices as $inv):
                ?>

                    <tr>

                        <!-- # -->
                        <td class="sno">
                            <?= $sn++ ?>
                        </td>

                        <!-- Invoice ID -->
                        <td>
                            <span class="invoice-id">
                                INV-<?= esc(sprintf("%05d", $inv['id'])) ?>
                            </span>
                        </td>

                        <!-- Patient -->
                        <td>

                            <div class="patient-name">
                                <?= esc($inv['patient_name'] ?? 'N/A') ?>
                            </div>

                            <span class="patient-code">
                                Code:
                                <?= esc($inv['patient_code'] ?? 'N/A') ?>
                            </span>

                        </td>

                        <!-- Branch -->
                        <td>

                            <span class="branch-badge">
                                <?= esc($inv['branch_name'] ?? 'N/A') ?>
                            </span>

                        </td>

                        <!-- Service Type -->
                        <td>

                            <span class="service-badge">
                                <?= esc(strtoupper($inv['type'] ?? 'N/A')) ?>
                            </span>

                        </td>

                        <!-- Tax & GST -->
                        <td>

                            <div class="tax-cell">

                                Tax:
                                ₹<?= esc(
                                    number_format(
                                        (float)($inv['tax'] ?? 0),
                                        2
                                    )
                                ) ?>

                                <br>

                                GST:
                                ₹<?= esc(
                                    number_format(
                                        (float)($inv['gst'] ?? 0),
                                        2
                                    )
                                ) ?>

                            </div>

                        </td>

                        <!-- Total -->
                        <td>
                            <span class="amount text-slate">
                                ₹<?= esc(
                                    number_format(
                                        (float)($inv['total'] ?? 0),
                                        2
                                    )
                                ) ?>
                            </span>
                        </td>

                        <!-- Collected -->
                        <td>
                            <span class="amount text-success">
                                ₹<?= esc(
                                    number_format(
                                        (float)($inv['paid_amount'] ?? 0),
                                        2
                                    )
                                ) ?>
                            </span>
                        </td>

                        <!-- Outstanding -->
                        <td>
                            <span class="amount text-danger">
                                ₹<?= esc(
                                    number_format(
                                        (float)($inv['outstanding'] ?? 0),
                                        2
                                    )
                                ) ?>
                            </span>
                        </td>

                        <!-- Refund -->
                        <td>
                            <span class="amount text-warning">
                                ₹<?= esc(
                                    number_format(
                                        (float)($inv['refunded_amount'] ?? 0),
                                        2
                                    )
                                ) ?>
                            </span>
                        </td>

                        <!-- Status -->
                        <td>

                            <?php
                            $paymentStatus = strtolower(
                                trim($inv['payment_status'] ?? 'unpaid')
                            );
                            ?>

                            <?php if ($paymentStatus === 'paid'): ?>

                                <span class="billing-status paid">
                                    <i class="bi bi-check-circle-fill me-1"></i>
                                    Paid
                                </span>

                            <?php elseif ($paymentStatus === 'partial'): ?>

                                <span class="billing-status partial">
                                    <i class="bi bi-clock-history me-1"></i>
                                    Partial
                                </span>

                            <?php elseif ($paymentStatus === 'refunded'): ?>

                                <span class="billing-status refunded">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                                    Refunded
                                </span>

                            <?php else: ?>

                                <span class="billing-status unpaid">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    Unpaid
                                </span>

                            <?php endif; ?>

                        </td>

                        <!-- Actions -->
                        <td>

                            <div class="action-group">

                                <?php
                                if (
                                    $paymentStatus !== 'paid' &&
                                    $paymentStatus !== 'refunded'
                                ):
                                ?>

                                    <a href="<?= site_url('/admin/billing/collect/' . $inv['id']) ?>"
                                       class="btn-action"
                                       title="Collect Payment"
                                       style="color:#10b981;">

                                        <i class="bi bi-cash-stack"></i>

                                    </a>

                                <?php endif; ?>


                                <?php
                                if (
                                    (float)($inv['paid_amount'] ?? 0) > 0
                                ):
                                ?>

                                    <a href="<?= site_url('/admin/billing/refund/' . $inv['id']) ?>"
                                       class="btn-action"
                                       title="Refund"
                                       style="color:#f59e0b;">

                                        <i class="bi bi-arrow-counterclockwise"></i>

                                    </a>

                                <?php endif; ?>


                                <a href="<?= site_url('/admin/billing/receipt/' . $inv['id']) ?>"
                                   class="btn-action"
                                   title="Print Receipt"
                                   style="color:#6366f1;">

                                    <i class="bi bi-printer"></i>

                                </a>


                                <a href="<?= site_url('/admin/billing/edit/' . $inv['id']) ?>"
                                   class="btn-action"
                                   title="Edit"
                                   style="color:#475569;">

                                    <i class="bi bi-pencil-fill"></i>

                                </a>

                            </div>

                        </td>

                    </tr>

                <?php
                    endforeach;
                endif;
                ?>

            </tbody>

        </table>

    </div>

</div>

<!-- ============================================
     DATATABLES CSS
     ============================================ -->
<link rel="stylesheet"
      href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<link rel="stylesheet"
      href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<!-- ============================================
     JQUERY
     ============================================ -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- ============================================
     DATATABLE
     ============================================ -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- ============================================
     DATATABLE BUTTONS
     ============================================ -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<!-- ============================================
     EXPORT LIBRARIES
     ============================================ -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<!-- ============================================
     DATATABLE INITIALIZATION
     ============================================ -->
<script>
$(document).ready(function () {

    if ($.fn.DataTable) {

        $('#billingTable').DataTable({

            pageLength: 25,

            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],

            responsive: true,

            autoWidth: false,

            processing: true,

            dom: 'Bfrtip',

            buttons: [
                {
                    extend: 'copy',
                    text: '<i class="bi bi-copy"></i> Copy'
                },
                {
                    extend: 'csv',
                    text: '<i class="bi bi-filetype-csv"></i> CSV'
                },
                {
                    extend: 'excel',
                    text: '<i class="bi bi-file-earmark-excel"></i> Excel'
                },
                {
                    extend: 'pdf',
                    text: '<i class="bi bi-file-earmark-pdf"></i> PDF'
                },
                {
                    extend: 'print',
                    text: '<i class="bi bi-printer"></i> Print'
                }
            ],

            order: [
                [0, 'asc']
            ],

            language: {

                search: "Search invoices:",

                searchPlaceholder: "Search invoice, patient, branch...",

                emptyTable:
                    '<div style="padding:30px;text-align:center;color:#94a3b8;">' +
                    '<i class="bi bi-receipt" style="font-size:32px;display:block;margin-bottom:10px;"></i>' +
                    'No billing invoices found.' +
                    '</div>',

                zeroRecords:
                    'No matching invoices found.'

            }

        });

    }

});
</script>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>