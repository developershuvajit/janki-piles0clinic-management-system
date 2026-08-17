<?php
$activePage = 'reception_queue';
include VIEWS_PATH . '/layout/reception_header.php';
?>

<!-- ============================================
     PAGE CSS
     ============================================ -->
<link rel="stylesheet" href="<?= asset('css/datatable.css') ?>">

<style>
    /* =========================================
       RECEPTION QUEUE PAGE SPACING
       ========================================= */

    .queue-page-container {
        margin-left: 30px;
        margin-right: 30px;
    }

    /* =========================================
       DATATABLE CLEAN DESIGN
       ========================================= */

    #queueTable {
        width: 100% !important;
    }

    #queueTable thead th {
        white-space: nowrap;
    }

    #queueTable tbody td {
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
        color: #334155 !important;
    }

    /* =========================================
       MOBILE
       ========================================= */

    @media (max-width: 991.98px) {

        .queue-page-container {
            margin-left: 18px;
            margin-right: 18px;
        }

    }

    @media (max-width: 575.98px) {

        .queue-page-container {
            margin-left: 10px;
            margin-right: 10px;
        }

    }
</style>


<!-- ============================================
     QUEUE PAGE CONTAINER
     ============================================ -->

<div class="queue-page-container">


    <!-- ========================================
         ACTIVE CONSULTATION QUEUE
         ======================================== -->

    <div class="datatable-wrapper mt-4">

        <div class="datatable-header">

            <h5>
                Active Consultation Queue

                <small>
                    <?= count($queues ?? []) ?> patients
                </small>
            </h5>

        </div>


        <!-- ====================================
             TABLE
             ==================================== -->

        <div class="table-responsive">

            <table id="queueTable"
                   class="table-custom"
                   style="width:100%;">

                <thead>

                    <tr>

                        <th class="sno">
                            #
                        </th>

                        <th style="width:80px;">
                            Token
                        </th>

                        <th style="min-width:180px;">
                            Patient Code &amp; Name
                        </th>

                        <th style="min-width:140px;">
                            Assigned Doctor
                        </th>

                        <th style="width:120px;">
                            Branch
                        </th>

                        <th style="width:120px;">
                            Time Booked
                        </th>

                        <th style="width:130px;">
                            Queue Status
                        </th>

                        <th style="width:200px;">
                            Queue Action
                        </th>

                    </tr>

                </thead>


                <!--
                    IMPORTANT:
                    Empty হলে কোনো <tr> রাখা হয়নি।
                    এতে DataTables Incorrect column count
                    error হবে না।
                -->

                <tbody>

                    <?php if (!empty($queues)): ?>

                        <?php $sn = 1; ?>

                        <?php foreach ($queues as $q): ?>

                            <tr>


                                <!-- =========================
                                     SERIAL NUMBER
                                     ========================= -->

                                <td class="sno">

                                    <?= $sn++ ?>

                                </td>


                                <!-- =========================
                                     TOKEN
                                     ========================= -->

                                <td class="fw-bold text-success fs-5">

                                    #<?= esc(
                                        (string)$q['token_number']
                                    ) ?>

                                </td>


                                <!-- =========================
                                     PATIENT
                                     ========================= -->

                                <td>

                                    <div class="fw-bold text-slate">

                                        <?= esc(
                                            $q['patient_name']
                                        ) ?>

                                    </div>

                                    <span class="text-muted small"
                                          style="font-size:0.78rem;">

                                        ID:
                                        <?= esc(
                                            $q['patient_code']
                                        ) ?>

                                        &bull;

                                        <?= esc(
                                            $q['patient_phone']
                                        ) ?>

                                    </span>

                                </td>


                                <!-- =========================
                                     DOCTOR
                                     ========================= -->

                                <td class="fw-semibold text-slate">

                                    Dr.
                                    <?= esc(
                                        $q['doctor_name']
                                    ) ?>

                                </td>


                                <!-- =========================
                                     BRANCH
                                     ========================= -->

                                <td>

                                    <span class="badge bg-secondary bg-opacity-10 text-dark">

                                        <?= esc(
                                            $q['branch_name']
                                        ) ?>

                                    </span>

                                </td>


                                <!-- =========================
                                     TIME
                                     ========================= -->

                                <td class="text-muted">

                                    <?= esc(
                                        date(
                                            'h:i A',
                                            strtotime(
                                                $q['time_slot']
                                            )
                                        )
                                    ) ?>

                                </td>


                                <!-- =========================
                                     QUEUE STATUS
                                     ========================= -->

                                <td>

                                    <?php if (
                                        $q['queue_status'] === 'waiting'
                                    ): ?>

                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-1.5 rounded">

                                            Waiting

                                        </span>


                                    <?php elseif (
                                        $q['queue_status'] === 'in_consultation'
                                    ): ?>

                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1.5 rounded">

                                            In consultation

                                        </span>


                                    <?php elseif (
                                        $q['queue_status'] === 'completed'
                                    ): ?>

                                        <span class="badge-status active">

                                            Completed

                                        </span>


                                    <?php else: ?>

                                        <span class="badge-status inactive">

                                            Skipped

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- =========================
                                     ACTIONS
                                     ========================= -->

                                <td>

                                    <div class="action-group">


                                        <!-- START CONSULTATION -->

                                        <?php if (
                                            $q['queue_status'] === 'waiting'
                                        ): ?>

                                            <a href="<?= site_url(
                                                '/admin/reception/queues/update/'
                                                . $q['id']
                                                . '?status=in_consultation'
                                            ) ?>"
                                               class="btn-action"
                                               title="Start Consultation"
                                               style="color:#ef4444;">

                                                <i class="bi bi-play-fill"></i>

                                            </a>


                                        <!-- COMPLETE -->

                                        <?php elseif (
                                            $q['queue_status'] === 'in_consultation'
                                        ): ?>

                                            <a href="<?= site_url(
                                                '/admin/reception/queues/update/'
                                                . $q['id']
                                                . '?status=completed'
                                            ) ?>"
                                               class="btn-action"
                                               title="Complete"
                                               style="color:#10b981;">

                                                <i class="bi bi-check-circle"></i>

                                            </a>

                                        <?php endif; ?>


                                        <!-- SKIP QUEUE -->

                                        <?php if (
                                            $q['queue_status'] !== 'completed'
                                            &&
                                            $q['queue_status'] !== 'skipped'
                                        ): ?>

                                            <a href="<?= site_url(
                                                '/admin/reception/queues/update/'
                                                . $q['id']
                                                . '?status=skipped'
                                            ) ?>"
                                               class="btn-action"
                                               title="Skip Queue"
                                               style="color:#94a3b8;">

                                                <i class="bi bi-skip-forward-fill"></i>

                                            </a>

                                        <?php else: ?>

                                            <span class="text-muted small">
                                                -
                                            </span>

                                        <?php endif; ?>

                                    </div>

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

    $('#queueTable').DataTable({

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

            searchPlaceholder: 'Search queue...',

            lengthMenu: 'Show _MENU_ entries',

            info: 'Showing _START_ to _END_ of _TOTAL_ patients',

            infoEmpty: 'Showing 0 to 0 of 0 patients',

            zeroRecords: 'No matching queue records found.',

            emptyTable: 'No patients currently mapped in active consultation queues today.',

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
include VIEWS_PATH . '/layout/reception_footer.php';
?>