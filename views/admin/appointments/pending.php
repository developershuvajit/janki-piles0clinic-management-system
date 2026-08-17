<?php
$activePage = 'appointments';
include VIEWS_PATH . '/layout/admin_header.php';
?>

<!-- ============================================
     PAGE CSS
     ============================================ -->
<link rel="stylesheet" href="<?= asset('css/datatable.css') ?>">

<!-- ============================================
     PAGE HTML
     ============================================ -->
<div class="datatable-wrapper mt-4">

    <div class="datatable-header">
        <h5>
            Pending Appointments
            <small><?= count($appointments ?? []) ?> waiting for approval</small>
        </h5>

        <a href="<?= site_url('/admin/appointments') ?>"
           class="btn-register"
           style="background: #6366f1; border-color: #6366f1;">
            <i class="bi bi-arrow-left me-1"></i>
            Back to All
        </a>
    </div>

    <div class="table-responsive">

        <table id="pendingTable"
               class="table-custom"
               style="width:100%">

            <thead>
                <tr>
                    <th class="sno">#</th>

                    <th style="min-width:180px;">
                        Patient Details
                    </th>

                    <th>
                        Requested Doctor
                    </th>

                    <th style="width:120px;">
                        Branch
                    </th>

                    <th style="min-width:140px;">
                        Requested Date &amp; Time
                    </th>

                    <th style="width:120px;">
                        Status
                    </th>

                    <th style="width:180px;">
                        Verification Actions
                    </th>
                </tr>
            </thead>

            <tbody>

                <?php if (!empty($appointments)): ?>

                    <?php $sn = 1; ?>

                    <?php foreach ($appointments as $ap): ?>

                        <tr>

                            <!-- Serial Number -->
                            <td class="sno">
                                <?= $sn++ ?>
                            </td>

                            <!-- Patient Details -->
                            <td>
                                <div class="fw-bold text-slate">
                                    <?= esc($ap['patient_name'] ?? 'N/A') ?>
                                </div>

                                <span class="text-muted small"
                                      style="font-size: 0.78rem;">

                                    ID:
                                    <?= esc($ap['patient_code'] ?? 'N/A') ?>

                                    &bull;

                                    <i class="bi bi-telephone"></i>

                                    <?= esc($ap['patient_phone'] ?? 'N/A') ?>

                                </span>
                            </td>

                            <!-- Doctor -->
                            <td class="fw-semibold text-slate">

                                Dr.
                                <?= esc($ap['doctor_name'] ?? 'N/A') ?>

                            </td>

                            <!-- Branch -->
                            <td>

                                <span class="badge bg-secondary bg-opacity-10 text-dark">

                                    <?= esc($ap['branch_name'] ?? 'N/A') ?>

                                </span>

                            </td>

                            <!-- Date & Time -->
                            <td>

                                <div>
                                    <?= esc($ap['date'] ?? 'N/A') ?>
                                </div>

                                <?php
                                $formattedTime = 'N/A';

                                if (!empty($ap['time_slot'])) {
                                    $timestamp = strtotime($ap['time_slot']);

                                    if ($timestamp !== false) {
                                        $formattedTime = date(
                                            'h:i A',
                                            $timestamp
                                        );
                                    }
                                }
                                ?>

                                <span class="text-muted small"
                                      style="font-size: 0.75rem;">

                                    <i class="bi bi-clock me-1"></i>

                                    <?= esc($formattedTime) ?>

                                </span>

                            </td>

                            <!-- Status -->
                            <td>

                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-1.5 rounded">

                                    Pending

                                </span>

                            </td>

                            <!-- Actions -->
                            <td>

                                <div class="action-group">

                                    <!-- Approve -->
                                    <a href="<?= site_url('/admin/appointments/approve/' . $ap['id']) ?>"
                                       class="btn-action"
                                       style="color:#10b981;"
                                       title="Approve">

                                        <i class="bi bi-check-circle-fill"></i>

                                    </a>

                                    <!-- Decline -->
                                    <a href="<?= site_url('/admin/appointments/cancel/' . $ap['id']) ?>"
                                       class="btn-action delete"
                                       onclick="return confirm('Cancel this request?');"
                                       title="Decline">

                                        <i class="bi bi-x-circle-fill"></i>

                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

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
     DATATABLES JS
     ============================================ -->

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

    $('#pendingTable').DataTable({

        /* ==============================
           BASIC SETTINGS
        ============================== */

        pageLength: 25,

        responsive: true,

        autoWidth: false,

        /* ==============================
           DOM
        ============================== */

        dom: 'Bfrtip',

        /* ==============================
           EXPORT BUTTONS
        ============================== */

        buttons: [
            'copy',
            'csv',
            'excel',
            'pdf',
            'print'
        ],

        /* ==============================
           DEFAULT SORT
        ============================== */

        order: [
            [0, 'asc']
        ],

        /* ==============================
           LANGUAGE
        ============================== */

        language: {

            emptyTable: `
                <div style="
                    padding: 2.5rem 1rem;
                    text-align: center;
                    color: #94a3b8;
                    width: 100%;
                ">
                    <i class="bi bi-calendar-check"
                       style="
                           font-size: 2rem;
                           display: block;
                           margin-bottom: 10px;
                       ">
                    </i>

                    <div style="
                        font-size: 14px;
                        font-weight: 500;
                    ">
                        No pending online appointments
                        requiring administrator approval.
                    </div>
                </div>
            `,

            search: '',

            searchPlaceholder: 'Search appointments...',

            lengthMenu: 'Show _MENU_ entries',

            info: 'Showing _START_ to _END_ of _TOTAL_ appointments',

            infoEmpty: 'Showing 0 to 0 of 0 appointments',

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