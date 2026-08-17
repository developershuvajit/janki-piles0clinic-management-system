<?php
$activePage = 'inventory';
include VIEWS_PATH . '/layout/admin_header.php';
?>

<!-- ============================================
     PAGE CSS
     ============================================ -->
<link rel="stylesheet" href="<?= asset('css/datatable.css') ?>">

<style>
    /* =========================================
       INVENTORY PAGE SPACING
       ========================================= */

    .inventory-page-container {
        margin-left: 30px;
        margin-right: 30px;
    }

    /* =========================================
       DATATABLE
       ========================================= */

    #inventoryTable {
        width: 100% !important;
    }

    #inventoryTable thead th {
        white-space: nowrap;
    }

    #inventoryTable tbody td {
        vertical-align: middle;
    }

    .dataTables_wrapper {
        width: 100%;
    }

    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 6px 10px;
        outline: none;
    }

    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.08);
    }

    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 5px 25px 5px 8px;
        outline: none;
    }

    .dataTables_wrapper .dt-buttons {
        margin-bottom: 8px;
    }

    .dataTables_wrapper .dt-button {
        border: 1px solid #e2e8f0 !important;
        background: #fff !important;
        color: #475569 !important;
        border-radius: 6px !important;
        padding: 5px 10px !important;
        font-size: 0.78rem !important;
        box-shadow: none !important;
    }

    .dataTables_wrapper .dt-button:hover {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
    }

    /* =========================================
       INVENTORY ROW STATES
       ========================================= */

    .expired-row {
        background-color: #fef2f2 !important;
    }

    .expired-row:hover {
        background-color: #fee2e2 !important;
    }

    .low-stock-row {
        background-color: #fffbeb !important;
    }

    .low-stock-row:hover {
        background-color: #fef3c7 !important;
    }

    /* =========================================
       MOBILE
       ========================================= */

    @media (max-width: 991.98px) {
        .inventory-page-container {
            margin-left: 18px;
            margin-right: 18px;
        }
    }

    @media (max-width: 575.98px) {
        .inventory-page-container {
            margin-left: 10px;
            margin-right: 10px;
        }
    }
</style>


<!-- ============================================
     INVENTORY PAGE CONTAINER
     ============================================ -->

<div class="inventory-page-container">


    <!-- ========================================
         HEADER + QUICK ACTIONS
         ======================================== -->

    <div class="row mt-4 mb-4 align-items-center">

        <div class="col-md-6 mb-2">

            <h5 class="fw-bold text-slate mb-1">

                <i class="bi bi-capsule text-success me-2"></i>

                Pharmacy Stock Master

            </h5>

            <p class="text-muted small mb-0">

                Monitor active pharmaceutical batches, expiry dates, and supplier catalogs.

            </p>

        </div>


        <div class="col-md-6 text-md-end">

            <a href="<?= site_url('/admin/inventory/low-stock') ?>"
               class="btn btn-outline-danger btn-sm px-3 me-2">

                <i class="bi bi-exclamation-triangle me-1"></i>

                Low Stock Alerts

            </a>


            <a href="<?= site_url('/admin/inventory/purchase') ?>"
               class="btn btn-primary btn-sm px-3 shadow-sm me-2">

                <i class="bi bi-cart-plus me-1"></i>

                Purchase Batch

            </a>


            <button type="button"
                    class="btn btn-success btn-sm px-3 shadow-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#supplierModal">

                <i class="bi bi-person-plus me-1"></i>

                Add Supplier

            </button>

        </div>

    </div>


    <!-- ========================================
         ACTIVE STOCK BATCHES
         ======================================== -->

    <div class="datatable-wrapper mt-4">

        <div class="datatable-header">

            <h5>

                Active Pharmaceutical Batches

                <small>
                    <?= count($stocks ?? []) ?> batches
                </small>

            </h5>

        </div>


        <div class="table-responsive">

            <table id="inventoryTable"
                   class="table-custom"
                   style="width:100%;">

                <thead>

                    <tr>

                        <th class="sno">
                            #
                        </th>

                        <th style="min-width:160px;">
                            Medicine Name
                        </th>

                        <th style="width:120px;">
                            SKU &amp; Category
                        </th>

                        <th style="width:120px;">
                            Batch ID
                        </th>

                        <th style="width:120px;">
                            Expiry Date
                        </th>

                        <th style="width:100px;">
                            Available Stock
                        </th>

                        <th style="width:140px;">
                            Rates
                        </th>

                        <th style="width:140px;">
                            Supplier Mapped
                        </th>

                    </tr>

                </thead>


                <!--
                    IMPORTANT:
                    Empty হলে এখানে কোনো <tr> রাখা হয়নি।
                    DataTables নিজেই emptyTable message দেখাবে।
                -->

                <tbody>

                    <?php if (!empty($stocks)): ?>

                        <?php $sn = 1; ?>

                        <?php foreach ($stocks as $st): ?>

                            <?php

                            $isExpired = !empty($st['expiry_date'])
                                && strtotime($st['expiry_date']) < time();

                            $isLow = (int)$st['quantity'] <= 5;

                            ?>

                            <tr class="<?= $isExpired
                                ? 'expired-row'
                                : ($isLow ? 'low-stock-row' : '') ?>">


                                <!-- =========================
                                     SERIAL NUMBER
                                     ========================= -->

                                <td class="sno">

                                    <?= $sn++ ?>

                                </td>


                                <!-- =========================
                                     MEDICINE
                                     ========================= -->

                                <td>

                                    <div class="fw-bold text-slate">

                                        <?= esc($st['medicine_name']) ?>

                                    </div>

                                    <span class="text-muted small"
                                          style="font-size:0.76rem;">

                                        Unit:
                                        <?= esc($st['unit']) ?>

                                    </span>

                                </td>


                                <!-- =========================
                                     SKU + CATEGORY
                                     ========================= -->

                                <td>

                                    <span class="badge bg-light text-slate border">

                                        <?= esc($st['sku']) ?>

                                    </span>

                                    <div class="text-muted small mt-1">

                                        <?= esc($st['category']) ?>

                                    </div>

                                </td>


                                <!-- =========================
                                     BATCH
                                     ========================= -->

                                <td class="fw-semibold text-slate">

                                    #<?= esc($st['batch_number']) ?>

                                </td>


                                <!-- =========================
                                     EXPIRY
                                     ========================= -->

                                <td>

                                    <div class="fw-semibold <?= $isExpired
                                        ? 'text-danger'
                                        : 'text-slate' ?>">

                                        <?= esc($st['expiry_date']) ?>

                                    </div>


                                    <?php if ($isExpired): ?>

                                        <span class="text-danger small fw-bold"
                                              style="font-size:0.7rem;">

                                            <i class="bi bi-exclamation-octagon"></i>

                                            EXPIRED

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- =========================
                                     STOCK
                                     ========================= -->

                                <td>

                                    <h5 class="mb-0 fw-bold <?= $isExpired
                                        ? 'text-muted'
                                        : ($isLow
                                            ? 'text-warning'
                                            : 'text-success') ?>">

                                        <?= esc((string)$st['quantity']) ?>

                                    </h5>


                                    <?php if ($isLow && !$isExpired): ?>

                                        <span class="text-warning small"
                                              style="font-size:0.7rem;">

                                            <i class="bi bi-exclamation-triangle"></i>

                                            Low stock

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- =========================
                                     RATES
                                     ========================= -->

                                <td>

                                    <div style="font-size:0.8rem;">

                                        Pur:
                                        ₹<?= esc(
                                            number_format(
                                                (float)$st['purchase_price'],
                                                2
                                            )
                                        ) ?>

                                        <br>

                                        Sell:

                                        <strong class="text-success">

                                            ₹<?= esc(
                                                number_format(
                                                    (float)$st['selling_price'],
                                                    2
                                                )
                                            ) ?>

                                        </strong>

                                    </div>

                                </td>


                                <!-- =========================
                                     SUPPLIER
                                     ========================= -->

                                <td>

                                    <span class="badge bg-secondary bg-opacity-10 text-dark">

                                        <?= esc(
                                            $st['supplier_name'] ?? 'N/A'
                                        ) ?>

                                    </span>

                                </td>


                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>


</div>


<!-- ============================================
     ADD SUPPLIER MODAL
     ============================================ -->

<div class="modal fade"
     id="supplierModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content border-0 shadow-lg text-slate">

            <form action="<?= site_url('/admin/inventory/supplier/save') ?>"
                  method="POST">

                <?= csrf_field() ?>


                <!-- MODAL HEADER -->

                <div class="modal-header bg-success text-white">

                    <h5 class="modal-title fw-bold">

                        <i class="bi bi-person-plus-fill me-2"></i>

                        Add Supplier Record

                    </h5>

                    <button type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                    </button>

                </div>


                <!-- MODAL BODY -->

                <div class="modal-body p-4">


                    <!-- NAME -->

                    <div class="mb-3">

                        <label for="sup-name"
                               class="form-label small fw-semibold">

                            Supplier/Wholesaler Name

                            <span class="text-danger">*</span>

                        </label>

                        <input type="text"
                               class="form-control form-control-sm"
                               id="sup-name"
                               name="name"
                               required
                               placeholder="e.g. MediLife Wholesale">

                    </div>


                    <!-- PHONE -->

                    <div class="mb-3">

                        <label for="sup-phone"
                               class="form-label small fw-semibold">

                            Contact Phone Number

                            <span class="text-danger">*</span>

                        </label>

                        <input type="text"
                               class="form-control form-control-sm"
                               id="sup-phone"
                               name="phone"
                               required
                               placeholder="e.g. 022-25556677">

                    </div>


                    <!-- EMAIL -->

                    <div class="mb-3">

                        <label for="sup-email"
                               class="form-label small fw-semibold">

                            Email Address

                        </label>

                        <input type="email"
                               class="form-control form-control-sm"
                               id="sup-email"
                               name="email"
                               placeholder="e.g. sales@wholesaler.com">

                    </div>


                    <!-- ADDRESS -->

                    <div class="mb-3">

                        <label for="sup-address"
                               class="form-label small fw-semibold">

                            Physical Address

                        </label>

                        <textarea class="form-control form-control-sm"
                                  id="sup-address"
                                  name="address"
                                  rows="2"
                                  placeholder="Street address, City, Pincode"></textarea>

                    </div>

                </div>


                <!-- MODAL FOOTER -->

                <div class="modal-footer pt-0 border-0">

                    <button type="button"
                            class="btn btn-outline-secondary btn-sm px-3"
                            data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button type="submit"
                            class="btn btn-success btn-sm px-4">

                        Save Supplier

                    </button>

                </div>

            </form>

        </div>

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
     DATATABLES JS
     ============================================ -->

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>


<!-- ============================================
     DATATABLE INITIALIZATION
     ============================================ -->

<script>
$(document).ready(function () {

    $('#inventoryTable').DataTable({

        pageLength: 25,

        responsive: true,

        autoWidth: false,

        processing: true,

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

        language: {

            search: '',

            searchPlaceholder: 'Search inventory...',

            lengthMenu: 'Show _MENU_ entries',

            info: 'Showing _START_ to _END_ of _TOTAL_ batches',

            infoEmpty: 'Showing 0 to 0 of 0 batches',

            zeroRecords: 'No matching inventory records found.',

            emptyTable: 'No medicine batches currently stocked in inventory.',

            paginate: {
                first: 'First',
                last: 'Last',
                next: 'Next',
                previous: 'Previous'
            }

        }

    });

});
</script>


<?php
include VIEWS_PATH . '/layout/admin_footer.php';
?>