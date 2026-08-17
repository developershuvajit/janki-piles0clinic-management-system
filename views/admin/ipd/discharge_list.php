<?php 
$activePage = 'discharge';
include VIEWS_PATH . '/layout/reception_header.php'; 
?>

<!-- ============================================
     PAGE CSS
     ============================================ -->
<link rel="stylesheet" href="<?= asset('css/datatable.css') ?>">

<style>
/* ============================================
   PAGE
   ============================================ */
.discharge-page {
    padding: 0 12px;
}

/* ============================================
   EMERALD COLORS
   ============================================ */
.btn-emerald {
    background: #0f7b4a;
    border-color: #0f7b4a;
    color: #fff;
}

.btn-emerald:hover {
    background: #0a5d38;
    border-color: #0a5d38;
    color: #fff;
}

.bg-emerald {
    background-color: #0f7b4a !important;
}

.text-emerald {
    color: #0f7b4a !important;
}

.border-emerald {
    border-color: #0f7b4a !important;
}

/* ============================================
   CHECKOUT BUTTON
   ============================================ */
.checkout-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;

    color: #0f7b4a !important;
    border: 1px solid #0f7b4a !important;
    background: #ffffff;

    padding: 0.38rem 0.85rem;
    border-radius: 8px;

    font-size: 0.78rem;
    font-weight: 600;

    text-decoration: none;
    transition: all .15s ease;
}

.checkout-btn:hover {
    background: #0f7b4a;
    color: #ffffff !important;
    transform: translateY(-1px);
}

/* ============================================
   DATATABLE BUTTONS
   ============================================ */
#dischargeTable_wrapper .dt-buttons {
    margin-bottom: 12px;
}

#dischargeTable_wrapper .dt-button {
    border: 1px solid #e2e8f0 !important;
    background: #ffffff !important;
    color: #475569 !important;

    border-radius: 6px !important;
    padding: 5px 10px !important;

    font-size: 12px !important;
    box-shadow: none !important;
}

#dischargeTable_wrapper .dt-button:hover {
    background: #f8fafc !important;
    color: #0f172a !important;
}

/* ============================================
   SEARCH
   ============================================ */
#dischargeTable_wrapper .dataTables_filter input {
    border: 1px solid #e2e8f0;
    border-radius: 7px;

    padding: 6px 10px;
    margin-left: 6px;

    outline: none;
}

#dischargeTable_wrapper .dataTables_filter input:focus {
    border-color: #0f7b4a;
}

/* ============================================
   EMPTY STATE
   ============================================ */
.discharge-empty-icon {
    font-size: 1.8rem;
    color: #94a3b8;
}

.discharge-empty-text {
    color: #94a3b8;
    font-size: 0.85rem;
}

/* ============================================
   MOBILE
   ============================================ */
@media (max-width: 768px) {

    .discharge-page {
        padding: 0;
    }

    .datatable-header {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 8px;
    }

    .checkout-btn {
        padding: 0.35rem 0.7rem;
    }
}
</style>


<div class="discharge-page">

    <!-- ============================================
         HEADER
         ============================================ -->
   


    <!-- ============================================
         DISCHARGE TABLE
         ============================================ -->
    <div class="datatable-wrapper mt-4">

        <div class="datatable-header">

            <h5>
                Approved Discharges 
                <small>
                    <?= count($approved ?? []) ?> patients ready for checkout
                </small>
            </h5>

        </div>


        <div class="table-responsive">

            <table id="dischargeTable"
                   class="table-custom"
                   style="width:100%">

                <!-- ====================================
                     TABLE HEADER
                     ==================================== -->
                <thead>

                    <tr>

                        <th class="sno">
                            #
                        </th>

                        <th style="width:120px;">
                            Admission #
                        </th>

                        <th style="min-width:180px;">
                            Patient Name
                        </th>

                        <th style="min-width:140px;">
                            Attending Doctor
                        </th>

                        <th style="min-width:160px;">
                            Admission Date
                        </th>

                        <th style="width:160px;">
                            Doctor Approval
                        </th>

                        <th style="width:180px;">
                            Checkout Action
                        </th>

                    </tr>

                </thead>


                <!-- ====================================
                     TABLE BODY
                     ==================================== -->
                <tbody>

                    <?php if (!empty($approved)): ?>

                        <?php 
                        $sn = 1;

                        foreach ($approved as $app):
                        ?>

                            <tr>

                                <!-- ==================================
                                     1. SERIAL NUMBER
                                     ================================== -->
                                <td class="sno">
                                    <?= $sn++ ?>
                                </td>


                                <!-- ==================================
                                     2. ADMISSION ID
                                     ================================== -->
                                <td>

                                    <span class="badge bg-emerald bg-opacity-10 text-emerald border border-emerald border-opacity-25">

                                        #ADM-<?= esc((string)$app['id']) ?>

                                    </span>

                                </td>


                                <!-- ==================================
                                     3. PATIENT
                                     ================================== -->
                                <td>

                                    <div class="fw-bold text-slate">
                                        <?= esc($app['patient_name']) ?>
                                    </div>

                                    <span class="text-muted small"
                                          style="font-size:0.78rem;">

                                        Code:
                                        <?= esc($app['patient_code']) ?>

                                    </span>

                                </td>


                                <!-- ==================================
                                     4. DOCTOR
                                     ================================== -->
                                <td class="fw-semibold text-slate">

                                    Dr.
                                    <?= esc($app['doctor_name']) ?>

                                </td>


                                <!-- ==================================
                                     5. ADMISSION DATE
                                     ================================== -->
                                <td>

                                    <div>
                                        <?= date(
                                            'd M Y',
                                            strtotime($app['admission_date'])
                                        ) ?>
                                    </div>

                                    <span class="text-muted small"
                                          style="font-size:0.75rem;">

                                        <i class="bi bi-clock me-1"></i>

                                        <?= date(
                                            'h:i A',
                                            strtotime($app['admission_date'])
                                        ) ?>

                                    </span>

                                </td>


                                <!-- ==================================
                                     6. APPROVAL STATUS
                                     ================================== -->
                                <td>

                                    <span class="badge-status active">

                                        <i class="bi bi-check-circle-fill me-1"></i>

                                        Approved

                                    </span>

                                </td>


                                <!-- ==================================
                                     7. CHECKOUT ACTION
                                     ================================== -->
                                <td>

                                    <form
                                        action="<?= site_url('/reception/discharge/checkout/' . $app['id']) ?>"
                                        method="POST"
                                        class="d-inline"
                                    >

                                        <?= csrf_field() ?>

                                        <button
                                            type="submit"
                                            class="checkout-btn"
                                            title="Final Bill & Checkout"
                                            onclick="return confirm('Generate final bill and complete checkout for patient <?= esc($app['patient_name']) ?>?');"
                                        >

                                            <i class="bi bi-box-arrow-right"></i>

                                            Checkout

                                        </button>

                                    </form>

                                </td>

                            </tr>

                        <?php endforeach; ?>


                    <?php else: ?>

                        <!-- =================================================
                             IMPORTANT DATATABLE FIX
                             
                             DO NOT USE:
                             <td colspan="7">
                             
                             DataTables requires the same number of TD
                             elements as TH elements.

                             This table has 7 columns, therefore exactly
                             7 TD elements are provided here.
                             ================================================= -->

                        <tr>

                            <!-- TD 1 -->
                            <td style="
                                text-align:center;
                                padding:2.5rem 0.5rem;
                                color:#94a3b8;
                            ">
                                <i class="bi bi-check-circle fs-3"></i>
                            </td>


                            <!-- TD 2 -->
                            <td style="
                                padding:2.5rem 0.5rem;
                                color:#94a3b8;
                            ">
                                <span class="discharge-empty-text">
                                    No doctor-approved discharges awaiting checkout.
                                </span>
                            </td>


                            <!-- TD 3 -->
                            <td style="padding:2.5rem 0.5rem;"></td>


                            <!-- TD 4 -->
                            <td style="padding:2.5rem 0.5rem;"></td>


                            <!-- TD 5 -->
                            <td style="padding:2.5rem 0.5rem;"></td>


                            <!-- TD 6 -->
                            <td style="padding:2.5rem 0.5rem;"></td>


                            <!-- TD 7 -->
                            <td style="padding:2.5rem 0.5rem;"></td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>


<!-- ============================================
     DATATABLES CSS
     ============================================ -->
<link
    rel="stylesheet"
    href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"
>

<link
    rel="stylesheet"
    href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css"
>


<!-- ============================================
     JQUERY
     ============================================ -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>


<!-- ============================================
     DATATABLES CORE
     ============================================ -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>


<!-- ============================================
     DATATABLE BUTTONS
     ============================================ -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>


<!-- ============================================
     EXPORT DEPENDENCIES
     ============================================ -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>


<!-- ============================================
     DATATABLE INITIALIZATION
     ============================================ -->
<script>
$(document).ready(function () {

    $('#dischargeTable').DataTable({

        /* Records per page */
        pageLength: 25,

        /* Page length options */
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],

        /* Keep horizontal table layout */
        responsive: false,

        /* Prevent automatic width problems */
        autoWidth: false,

        /* Processing indicator */
        processing: true,

        /* Buttons + Search + Table */
        dom: 'Bfrtip',

        /* Export buttons */
        buttons: [

            {
                extend: 'copy',
                text: '<i class="bi bi-copy me-1"></i> Copy'
            },

            {
                extend: 'csv',
                text: '<i class="bi bi-filetype-csv me-1"></i> CSV'
            },

            {
                extend: 'excel',
                text: '<i class="bi bi-file-earmark-excel me-1"></i> Excel'
            },

            {
                extend: 'pdf',
                text: '<i class="bi bi-file-earmark-pdf me-1"></i> PDF'
            },

            {
                extend: 'print',
                text: '<i class="bi bi-printer me-1"></i> Print'
            }

        ],

        /* Default ordering */
        order: [
            [0, 'asc']
        ],

        /* Column configuration */
        columnDefs: [

            /* Serial */
            {
                targets: 0,
                orderable: true,
                searchable: false
            },

            /* Checkout Action */
            {
                targets: 6,
                orderable: false,
                searchable: false
            }

        ],

        /* Language */
        language: {

            search: "Search:",

            searchPlaceholder: "Search admissions...",

            lengthMenu: "Show _MENU_ records",

            info: "Showing _START_ to _END_ of _TOTAL_ records",

            infoEmpty: "Showing 0 to 0 of 0 records",

            zeroRecords: "No matching discharge records found",

            emptyTable: "No approved discharge records available"

        }

    });

});
</script>


<?php include VIEWS_PATH . '/layout/reception_footer.php'; ?>