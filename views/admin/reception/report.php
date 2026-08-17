<?php
$activePage = 'reception_report';
include VIEWS_PATH . '/layout/reception_header.php';

/* =========================================================
   SAFE REPORT DATA
   ========================================================= */

$report = is_array($report ?? null) ? $report : [];

$totalCollected = (float)($report['total_collected'] ?? 0);

$splits = is_array($report['splits'] ?? null)
    ? $report['splits']
    : [];

$cashAmount = (float)($splits['cash'] ?? 0);
$cardAmount = (float)($splits['card'] ?? 0);
$upiAmount  = (float)($splits['upi'] ?? 0);

$invoices = is_array($report['invoices'] ?? null)
    ? $report['invoices']
    : [];

$opdRegistrations = (int)($report['opd_registrations'] ?? 0);
$ipdAdmissions    = (int)($report['ipd_admissions'] ?? 0);

$totalPatients = $opdRegistrations + $ipdAdmissions;
?>

<!-- =========================================================
     DATATABLE CSS
     ========================================================= -->
<link rel="stylesheet" href="<?= asset('css/datatable.css') ?>">

<style>

/* =========================================================
   REPORT PAGE
   ========================================================= */

.reception-report-page {
    width: 100%;
}

/* =========================================================
   HEADER
   ========================================================= */

.report-header {
    margin-bottom: 1.5rem;
}

.report-header h4 {
    margin-bottom: .25rem;
}

.report-header p {
    margin-bottom: 0;
}

/* =========================================================
   STAT CARDS
   ========================================================= */

.stat-card {
    width: 100%;
    min-height: 100px;

    background: #ffffff;

    border: 1px solid #f0f2f5;
    border-radius: 12px;

    padding: 1rem 1.2rem;

    display: flex;
    align-items: center;
    justify-content: space-between;

    box-shadow: 0 2px 8px rgba(0, 0, 0, .04);

    transition:
        transform .18s ease,
        box-shadow .18s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, .07);
}

.stat-label {
    font-size: .65rem;
    font-weight: 700;

    text-transform: uppercase;
    letter-spacing: .04em;

    color: #6b7a8f;

    margin-bottom: .35rem;
}

.stat-value {
    font-size: 1.55rem;
    font-weight: 700;

    line-height: 1.15;

    color: #0b1a2b;
}

.stat-icon {
    width: 46px;
    height: 46px;
    min-width: 46px;

    border-radius: 12px;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 1.25rem;
}

/* =========================================================
   VOLUME CARD
   ========================================================= */

.volume-card {
    background: #fff;

    border: 0;
    border-radius: 16px;

    padding: 1.4rem;

    box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
}

.volume-item {
    padding: .85rem 0;

    border-bottom: 1px solid #f1f5f9;

    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 1rem;
}

.volume-item:last-child {
    border-bottom: none;
}

.volume-label {
    display: block;

    color: #0b1a2b;

    font-size: .86rem;
    font-weight: 600;

    line-height: 1.35;
}

.volume-sub {
    display: block;

    margin-top: .2rem;

    color: #94a3b8;

    font-size: .72rem;

    line-height: 1.35;
}

/* =========================================================
   TABLE
   ========================================================= */

#invoiceTable {
    width: 100% !important;
}

#invoiceTable th,
#invoiceTable td {
    vertical-align: middle;
}

#invoiceTable .sno {
    width: 45px;
    min-width: 45px;

    text-align: center;
}

/*
|--------------------------------------------------------------------------
| IMPORTANT:
| Never allow DataTables to receive colspan/rowspan in tbody.
|--------------------------------------------------------------------------
*/

#invoiceTable tbody td {
    white-space: normal;
}

/* =========================================================
   DATATABLE BUTTONS
   ========================================================= */

.dt-buttons {
    margin-bottom: 10px;
}

.dt-button {
    border-radius: 6px !important;
    font-size: 13px !important;
}

/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 991px) {

    .stat-value {
        font-size: 1.35rem;
    }

}

@media (max-width: 767px) {

    .report-header h4 {
        font-size: 1.05rem;
    }

    .report-header p {
        font-size: .75rem;
    }

    .stat-card {
        min-height: 90px;
    }

    .volume-card {
        padding: 1rem;
    }

}

</style>


<!-- =========================================================
     MAIN PAGE
     ========================================================= -->

<div class="reception-report-page">


    <!-- =====================================================
         HEADER
         ===================================================== -->

    <div class="d-flex justify-content-between align-items-center report-header mt-3 mx-4">

        <div>

            <h4 class="fw-bold text-slate mb-1">

                <i class="bi bi-graph-up-arrow text-success me-2"></i>

                Daily Reception Report

            </h4>

            <p class="text-muted small mb-0">

                Overview of today's collections, registrations, and patient volumes

            </p>

        </div>


        <div>

            <span class="badge bg-light text-dark border px-3 py-2">

                <i class="bi bi-calendar3 me-1"></i>

                <?= esc(date('d M Y')) ?>

            </span>

        </div>

    </div>


    <!-- =====================================================
         STATISTICS
         ===================================================== -->

    <div class="row g-3 mb-4">


        <!-- TOTAL COLLECTION -->
        <div class="col-xl-3 col-md-6">

            <div class="stat-card">

                <div>

                    <div class="stat-label">
                        Total Collections
                    </div>

                    <div class="stat-value text-success">

                        ₹<?= esc(
                            number_format(
                                $totalCollected,
                                2
                            )
                        ) ?>

                    </div>

                </div>


                <div
                    class="stat-icon"
                    style="background:#d1fae5;color:#10b981;"
                >

                    <i class="bi bi-wallet2"></i>

                </div>

            </div>

        </div>


        <!-- CASH -->
        <div class="col-xl-3 col-md-6">

            <div class="stat-card">

                <div>

                    <div class="stat-label">
                        Cash Payments
                    </div>

                    <div class="stat-value">

                        ₹<?= esc(
                            number_format(
                                $cashAmount,
                                2
                            )
                        ) ?>

                    </div>

                </div>


                <div
                    class="stat-icon"
                    style="background:#dbeafe;color:#3b82f6;"
                >

                    <i class="bi bi-cash"></i>

                </div>

            </div>

        </div>


        <!-- CARD -->
        <div class="col-xl-3 col-md-6">

            <div class="stat-card">

                <div>

                    <div class="stat-label">
                        Card Payments
                    </div>

                    <div class="stat-value">

                        ₹<?= esc(
                            number_format(
                                $cardAmount,
                                2
                            )
                        ) ?>

                    </div>

                </div>


                <div
                    class="stat-icon"
                    style="background:#e6f7fe;color:#0e7c9e;"
                >

                    <i class="bi bi-credit-card"></i>

                </div>

            </div>

        </div>


        <!-- UPI -->
        <div class="col-xl-3 col-md-6">

            <div class="stat-card">

                <div>

                    <div class="stat-label">
                        UPI Payments
                    </div>

                    <div class="stat-value">

                        ₹<?= esc(
                            number_format(
                                $upiAmount,
                                2
                            )
                        ) ?>

                    </div>

                </div>


                <div
                    class="stat-icon"
                    style="background:#fef7e8;color:#c5711e;"
                >

                    <i class="bi bi-qr-code"></i>

                </div>

            </div>

        </div>

    </div>


    <!-- =====================================================
         REPORT CONTENT
         ===================================================== -->

    <div class="row g-4">


        <!-- =================================================
             LEFT: INVOICE TABLE
             ================================================= -->

        <div class="col-lg-8">

            <div class="datatable-wrapper">

                <div class="datatable-header">

                    <h5>

                        Invoices Settled Today

                        <small>
                            <?= count($invoices) ?> transactions
                        </small>

                    </h5>

                </div>


                <div class="table-responsive">

                    <!--
                    ==================================================
                    IMPORTANT:
                    THE TABLE ALWAYS HAS EXACTLY 6 COLUMNS.
                    ==================================================
                    -->

                    <table
                        id="invoiceTable"
                        class="table-custom"
                        style="width:100%"
                    >

                        <thead>

                            <tr>

                                <th class="sno">
                                    #
                                </th>

                                <th style="width:120px;">
                                    Invoice #
                                </th>

                                <th style="min-width:160px;">
                                    Patient
                                </th>

                                <th style="width:120px;">
                                    Service
                                </th>

                                <th style="width:120px;">
                                    Paid via
                                </th>

                                <th
                                    style="width:130px;"
                                    class="text-end"
                                >
                                    Paid Amount
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                        <?php if (!empty($invoices)): ?>

                            <?php
                            $sn = 1;

                            foreach ($invoices as $inv):

                                $invoiceId = (int)(
                                    $inv['id'] ?? 0
                                );

                                $patientName =
                                    $inv['patient_name']
                                    ?? 'N/A';

                                $patientCode =
                                    $inv['patient_code']
                                    ?? 'N/A';

                                $serviceType =
                                    $inv['type']
                                    ?? 'N/A';

                                $paymentMethod =
                                    $inv['payment_method']
                                    ?? 'N/A';

                                $paidAmount =
                                    (float)(
                                        $inv['paid_amount']
                                        ?? 0
                                    );
                            ?>

                                <!-- =================================================
                                     EVERY DATA ROW HAS EXACTLY 6 TD
                                     ================================================= -->

                                <tr>

                                    <!-- 1 -->
                                    <td class="sno">

                                        <?= $sn++ ?>

                                    </td>


                                    <!-- 2 -->
                                    <td class="fw-bold text-slate">

                                        INV-<?= esc(
                                            sprintf(
                                                "%05d",
                                                $invoiceId
                                            )
                                        ) ?>

                                    </td>


                                    <!-- 3 -->
                                    <td>

                                        <div class="fw-bold text-slate">

                                            <?= esc(
                                                $patientName
                                            ) ?>

                                        </div>

                                        <span
                                            class="text-muted small"
                                            style="font-size:.75rem;"
                                        >

                                            <?= esc(
                                                $patientCode
                                            ) ?>

                                        </span>

                                    </td>


                                    <!-- 4 -->
                                    <td>

                                        <span
                                            class="badge bg-light text-secondary border"
                                        >

                                            <?= esc(
                                                strtoupper(
                                                    (string)$serviceType
                                                )
                                            ) ?>

                                        </span>

                                    </td>


                                    <!-- 5 -->
                                    <td>

                                        <span
                                            class="badge bg-light text-slate border"
                                        >

                                            <?= esc(
                                                strtoupper(
                                                    (string)$paymentMethod
                                                )
                                            ) ?>

                                        </span>

                                    </td>


                                    <!-- 6 -->
                                    <td
                                        class="text-end fw-bold text-success"
                                    >

                                        ₹<?= esc(
                                            number_format(
                                                $paidAmount,
                                                2
                                            )
                                        ) ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <!--
                            =====================================================
                            IMPORTANT FIX

                            DO NOT USE colspan HERE.

                            DataTables expects EXACTLY 6 TD elements.
                            =====================================================
                            -->

                            <tr class="empty-invoice-row">

                                <td class="sno"></td>

                                <td></td>

                                <td>

                                    <div
                                        class="text-muted py-2"
                                    >

                                        <i class="bi bi-receipt me-2"></i>

                                        No collections recorded today.

                                    </div>

                                </td>

                                <td></td>

                                <td></td>

                                <td></td>

                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        <!-- =================================================
             RIGHT: VISITOR VOLUMES
             ================================================= -->

        <div class="col-lg-4">

            <div class="volume-card">

                <h5 class="fw-bold text-slate mb-3">

                    <i class="bi bi-graph-up text-success me-2"></i>

                    Visitor Volumes

                </h5>


                <!-- OPD -->

                <div class="volume-item">

                    <div>

                        <span class="volume-label">

                            OPD Appointments Today

                        </span>

                        <span class="volume-sub">

                            Walk-ins and online confirmations

                        </span>

                    </div>


                    <span
                        class="badge bg-primary fs-5 px-3 py-2 rounded-pill"
                    >

                        <?= esc(
                            (string)$opdRegistrations
                        ) ?>

                    </span>

                </div>


                <!-- IPD -->

                <div class="volume-item">

                    <div>

                        <span class="volume-label">

                            IPD Bed Admissions Today

                        </span>

                        <span class="volume-sub">

                            Patients admitted in ward rooms

                        </span>

                    </div>


                    <span
                        class="badge bg-danger fs-5 px-3 py-2 rounded-pill"
                    >

                        <?= esc(
                            (string)$ipdAdmissions
                        ) ?>

                    </span>

                </div>


                <!-- TOTAL -->

                <div class="mt-3 pt-3 border-top">

                    <div
                        class="d-flex justify-content-between align-items-center"
                    >

                        <span class="text-muted small">

                            Total Patients Today

                        </span>


                        <span class="fw-bold text-slate">

                            <?= esc(
                                (string)$totalPatients
                            ) ?>

                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- =========================================================
     DATATABLES LIBRARIES
     ========================================================= -->

<link
    rel="stylesheet"
    href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"
>

<link
    rel="stylesheet"
    href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css"
>


<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>


<!-- =========================================================
     DATATABLE INITIALIZATION
     ========================================================= -->

<script>
(function () {

    function initInvoiceTable() {

        if (typeof jQuery === 'undefined') {
            return;
        }

        if (typeof jQuery.fn.DataTable === 'undefined') {
            return;
        }

        const table = jQuery('#invoiceTable');

        if (!table.length) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent duplicate initialization
        |--------------------------------------------------------------------------
        */

        if (jQuery.fn.DataTable.isDataTable('#invoiceTable')) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Initialize
        |--------------------------------------------------------------------------
        */

        table.DataTable({

            pageLength: 25,

            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, 'All']
            ],

            responsive: true,

            autoWidth: false,

            processing: false,

            dom: 'Bfrtip',

            buttons: [
                'copy',
                'csv',
                'excel',
                'pdf',
                'print'
            ],

            order: [
                [0, 'asc']
            ],

            columnDefs: [

                {
                    targets: 0,
                    orderable: true,
                    searchable: false
                },

                {
                    targets: 5,
                    className: 'text-end'
                }

            ],

            language: {

                emptyTable: 'No collections recorded today.',

                zeroRecords: 'No matching collections found.',

                search: 'Search:',

                lengthMenu: 'Show _MENU_ entries',

                info: 'Showing _START_ to _END_ of _TOTAL_ transactions',

                infoEmpty: 'Showing 0 to 0 of 0 transactions',

                infoFiltered: '(filtered from _MAX_ total transactions)',

                paginate: {
                    first: 'First',
                    last: 'Last',
                    next: 'Next',
                    previous: 'Previous'
                }

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Run after DOM ready
    |--------------------------------------------------------------------------
    */

    if (document.readyState === 'loading') {

        document.addEventListener(
            'DOMContentLoaded',
            initInvoiceTable
        );

    } else {

        initInvoiceTable();

    }

})();
</script>


<?php
include VIEWS_PATH . '/layout/reception_footer.php';
?>