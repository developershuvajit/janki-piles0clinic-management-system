<?php 
$activePage = 'medicine_issue';
include VIEWS_PATH . '/layout/reception_header.php'; 
?>

<!-- ============================================
     PAGE CSS
     ============================================ -->
<link rel="stylesheet" href="<?= asset('css/datatable.css') ?>">

<style>
    /* Page spacing */
    .medicine-page {
        margin-left: 15px;
        margin-right: 15px;
    }

    /* Emerald Button */
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

    .text-emerald {
        color: #0f7b4a !important;
    }

    /* Medicine icon */
    .medicine-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: rgba(15, 123, 74, 0.10);
        color: #0f7b4a;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }

    /* Issue button */
    .issue-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        color: #0f7b4a !important;
        border: 1px solid #0f7b4a;
        background: #fff;
        padding: 0.38rem 0.85rem;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .issue-btn:hover {
        background: #0f7b4a;
        color: #fff !important;
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(15, 123, 74, 0.18);
    }

    /* Empty state */
    .datatable-empty {
        text-align: center !important;
        padding: 2.8rem 1rem !important;
        color: #94a3b8 !important;
    }

    .datatable-empty i {
        font-size: 2rem;
        display: block;
        margin-bottom: 0.6rem;
    }

    /* DataTable button styling */
    .dt-buttons {
        margin-bottom: 12px !important;
    }

    .dt-button {
        border-radius: 6px !important;
        font-size: 0.78rem !important;
        padding: 5px 10px !important;
        background: #fff !important;
        border: 1px solid #dee2e6 !important;
    }

    .dt-button:hover {
        background: #f8f9fa !important;
        border-color: #adb5bd !important;
    }

    /* DataTable search */
    .dataTables_filter input {
        border: 1px solid #dee2e6 !important;
        border-radius: 7px !important;
        padding: 6px 10px !important;
        margin-left: 5px !important;
        outline: none !important;
    }

    .dataTables_filter input:focus {
        border-color: #0f7b4a !important;
        box-shadow: 0 0 0 2px rgba(15, 123, 74, 0.08) !important;
    }

    /* Responsive mobile */
    @media (max-width: 768px) {
        .medicine-page {
            margin-left: 8px;
            margin-right: 8px;
        }

        .datatable-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 8px;
        }
    }
</style>

<!-- ============================================
     PAGE WRAPPER
     ============================================ -->
<div class="medicine-page">

    <!-- ============================================
         HEADER
         ============================================ -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-slate mb-1">
                <i class="bi bi-capsule-pill text-success me-2"></i>
                Medicine Dispatch
            </h4>

            <p class="text-muted small mb-0">
                View and process pending prescriptions for medicine issue to patients
            </p>
        </div>
    </div>

    <!-- ============================================
         DISPATCH TABLE
         ============================================ -->
    <div class="datatable-wrapper mt-4">

        <div class="datatable-header">
            <h5>
                Pending Prescriptions
                <small><?= count($pending ?? []) ?> medicines to dispatch</small>
            </h5>
        </div>

        <div class="table-responsive">

            <table id="medicineTable"
                   class="table-custom"
                   style="width:100%">

                <thead>
                    <tr>
                        <th class="sno">#</th>

                        <th style="min-width:160px;">
                            Prescribed On
                        </th>

                        <th style="min-width:180px;">
                            Patient Details
                        </th>

                        <th style="min-width:140px;">
                            Prescribed Doctor
                        </th>

                        <th style="min-width:180px;">
                            Medicine Detail
                        </th>

                        <th style="min-width:160px;">
                            Dosage & Frequency
                        </th>

                        <th style="width:120px;">
                            Duration
                        </th>

                        <th style="width:130px;">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($pending)): ?>

                        <?php 
                        $sn = 1;

                        foreach ($pending as $row): 
                        ?>

                            <tr>

                                <!-- Serial -->
                                <td class="sno">
                                    <?= $sn++ ?>
                                </td>

                                <!-- Prescribed Date -->
                                <td>
                                    <div class="fw-semibold text-slate">
                                        <?= date(
                                            'd M Y',
                                            strtotime($row['prescribed_at'])
                                        ) ?>
                                    </div>

                                    <span class="text-muted small"
                                          style="font-size:0.75rem;">

                                        <i class="bi bi-clock me-1"></i>

                                        <?= date(
                                            'h:i A',
                                            strtotime($row['prescribed_at'])
                                        ) ?>

                                    </span>
                                </td>

                                <!-- Patient -->
                                <td>

                                    <div class="fw-bold text-slate">
                                        <?= esc($row['patient_name']) ?>
                                    </div>

                                    <span class="text-muted small"
                                          style="font-size:0.78rem;">

                                        ID:
                                        <?= esc($row['patient_code']) ?>

                                    </span>

                                </td>

                                <!-- Doctor -->
                                <td class="fw-semibold text-slate">

                                    Dr.
                                    <?= esc($row['doctor_name']) ?>

                                </td>

                                <!-- Medicine -->
                                <td>

                                    <div class="fw-bold text-slate">

                                        <i class="bi bi-prescription text-danger me-1"></i>

                                        <?= esc($row['medicine_name']) ?>

                                    </div>

                                </td>

                                <!-- Dosage -->
                                <td>

                                    <div class="fw-semibold"
                                         style="font-size:0.85rem;">

                                        <?= esc($row['dosage']) ?>

                                    </div>

                                    <span class="text-muted"
                                          style="font-size:0.75rem;">

                                        <i class="bi bi-repeat me-1"></i>

                                        <?= esc($row['frequency']) ?>

                                    </span>

                                </td>

                                <!-- Duration -->
                                <td>

                                    <span class="badge bg-light text-dark border">

                                        <?= esc($row['duration']) ?>

                                    </span>

                                </td>

                                <!-- Action -->
                                <td>

                                    <div class="action-group">

                                        <a href="<?= site_url(
                                            '/reception/medicine-issue/dispatch/' . $row['id']
                                        ) ?>"
                                           class="issue-btn"
                                           title="Issue Medicine">

                                            <i class="bi bi-box-arrow-right"></i>

                                            Issue

                                        </a>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <!--
                            IMPORTANT:
                            DataTables expects the same number of columns.
                            This row therefore contains EXACTLY 8 TD elements.
                        -->
                        <tr class="empty-row">

                            <td class="datatable-empty"></td>

                            <td class="datatable-empty"></td>

                            <td class="datatable-empty"></td>

                            <td class="datatable-empty">

                                <i class="bi bi-check-circle"></i>

                            </td>

                            <td class="datatable-empty">
                                No prescriptions currently pending.
                            </td>

                            <td class="datatable-empty"></td>

                            <td class="datatable-empty"></td>

                            <td class="datatable-empty"></td>

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
<link rel="stylesheet"
      href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<link rel="stylesheet"
      href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<!-- ============================================
     JQUERY
     ============================================ -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- ============================================
     DATATABLES
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

    if ($.fn.DataTable) {

        $('#medicineTable').DataTable({

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
                    targets: 7,
                    orderable: false,
                    searchable: false
                }
            ],

            language: {
                search: "Search:",
                searchPlaceholder: "Search medicines...",
                lengthMenu: "Show _MENU_",
                info: "Showing _START_ to _END_ of _TOTAL_ prescriptions",
                infoEmpty: "No prescriptions available",
                zeroRecords: "No matching prescriptions found",
                emptyTable: "No prescriptions currently pending medicine dispatches"
            }

        });

    }

});
</script>

<?php include VIEWS_PATH . '/layout/reception_footer.php'; ?>