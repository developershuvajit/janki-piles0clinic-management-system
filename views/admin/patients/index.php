<?php
$activePage = 'patients';
include VIEWS_PATH . '/layout/admin_header.php';
?>

<!-- ============================================
     PAGE CSS
     ============================================ -->
<link rel="stylesheet" href="<?= asset('css/datatable.css') ?>">

<style>
    /* ============================================
       PATIENT PAGE
       ============================================ */

    .patient-id {
        font-weight: 700;
        color: #2563eb;
        white-space: nowrap;
    }

    .patient-name {
        font-weight: 700;
        color: #0b1a2b;
    }

    .patient-code {
        display: block;
        margin-top: 2px;
        font-size: 0.72rem;
        color: #94a3b8;
    }

    .gender-text {
        font-weight: 600;
        color: #334155;
    }

    .dob-text {
        display: block;
        margin-top: 2px;
        font-size: 0.72rem;
        color: #94a3b8;
    }

    .phone-text {
        font-weight: 600;
        color: #334155;
        white-space: nowrap;
    }

    .email-text {
        color: #475569;
        font-size: 0.84rem;
    }

    .blood-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 42px;
        padding: 0.25rem 0.55rem;
        border-radius: 6px;
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
        font-weight: 700;
        font-size: 0.75rem;
    }

    .branch-badge {
        display: inline-block;
        padding: 0.3rem 0.65rem;
        border-radius: 6px;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }

    /* ============================================
       ACTION BUTTONS
       ============================================ */

    .patient-actions {
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .patient-actions .btn-action {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .patient-actions .btn-action:hover {
        transform: translateY(-1px);
    }

    .patient-actions .history {
        color: #2563eb;
        border: 1px solid #bfdbfe;
        background: #eff6ff;
    }

    .patient-actions .history:hover {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
    }

    .patient-actions .edit {
        color: #059669;
        border: 1px solid #a7f3d0;
        background: #ecfdf5;
    }

    .patient-actions .edit:hover {
        background: #059669;
        color: #fff;
        border-color: #059669;
    }

    .patient-actions .delete {
        color: #dc2626;
        border: 1px solid #fecaca;
        background: #fef2f2;
    }

    .patient-actions .delete:hover {
        background: #dc2626;
        color: #fff;
        border-color: #dc2626;
    }

    /* ============================================
       REGISTER BUTTON
       ============================================ */

    .btn-register {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        background: #059669;
        color: #fff;
        text-decoration: none;
        font-size: 0.8rem;
        font-weight: 600;
        border: 1px solid #059669;
        transition: all 0.2s ease;
    }

    .btn-register:hover {
        background: #047857;
        border-color: #047857;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);
    }

    /* ============================================
       EMPTY STATE
       ============================================ */

    .empty-patient-state {
        text-align: center;
        padding: 3rem 1rem !important;
        color: #94a3b8;
    }

    .empty-patient-state i {
        font-size: 2.5rem;
        display: block;
        margin-bottom: 0.75rem;
        color: #cbd5e1;
    }

    .empty-patient-state strong {
        display: block;
        color: #64748b;
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }

    .empty-patient-state span {
        font-size: 0.78rem;
    }

    /* ============================================
       DATATABLE CUSTOMIZATION
       ============================================ */

    #patientsTable_wrapper {
        width: 100%;
    }

    #patientsTable_wrapper .dt-buttons {
        margin-bottom: 12px;
    }

    #patientsTable_wrapper .dt-button {
        border-radius: 7px !important;
        border: 1px solid #e2e8f0 !important;
        background: #fff !important;
        color: #334155 !important;
        font-size: 0.75rem !important;
        padding: 0.4rem 0.7rem !important;
        box-shadow: none !important;
    }

    #patientsTable_wrapper .dt-button:hover {
        background: #f8fafc !important;
        color: #059669 !important;
        border-color: #a7f3d0 !important;
    }

    #patientsTable_wrapper .dataTables_filter input {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.45rem 0.7rem;
        margin-left: 6px;
        outline: none;
    }

    #patientsTable_wrapper .dataTables_filter input:focus {
        border-color: #059669;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.08);
    }

    #patientsTable_wrapper .dataTables_length select {
        border: 1px solid #e2e8f0;
        border-radius: 7px;
        padding: 0.35rem 1.8rem 0.35rem 0.5rem;
        outline: none;
    }

    #patientsTable_wrapper .dataTables_info {
        color: #64748b;
        font-size: 0.78rem;
    }

    #patientsTable_wrapper .dataTables_paginate .paginate_button {
        border-radius: 6px !important;
    }

    /* ============================================
       RESPONSIVE
       ============================================ */

    @media (max-width: 768px) {
        .datatable-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 12px;
        }

        .btn-register {
            width: 100%;
            justify-content: center;
        }

        #patientsTable_wrapper .dataTables_filter,
        #patientsTable_wrapper .dataTables_length {
            width: 100%;
            text-align: left;
            margin-bottom: 10px;
        }

        #patientsTable_wrapper .dataTables_filter input {
            width: calc(100% - 65px);
        }

        .patient-actions {
            justify-content: flex-start;
        }
    }
</style>

<!-- ============================================
     PAGE HEADER
     ============================================ -->
<div class="d-flex justify-content-between align-items-center mb-4 mt-4 mx-4">
    <div>
        <h4 class="fw-bold text-slate mb-1">
            <i class="bi bi-people-fill text-success me-2"></i>
            Patient Management
        </h4>

        <p class="text-muted small mb-0">
            View, manage and maintain all registered patients
        </p>
    </div>
</div>

<!-- ============================================
     PATIENT TABLE
     ============================================ -->
<div class="datatable-wrapper mt-4">

    <div class="datatable-header">

        <h5>
            Patient List
            <small><?= count($patients ?? []) ?> registered</small>
        </h5>

        <a href="<?= site_url('/admin/patients/create') ?>" class="btn-register">
            <i class="bi bi-person-plus-fill"></i>
            Register Patient
        </a>

    </div>

    <div class="table-responsive">

        <table
            id="patientsTable"
            class="table-custom"
            style="width:100%"
        >

            <thead>
                <tr>
                    <th class="sno">#</th>
                    <th style="min-width:120px;">Patient ID</th>
                    <th style="min-width:180px;">Name</th>
                    <th style="min-width:110px;">Gender / DOB</th>
                    <th style="min-width:130px;">Phone</th>
                    <th style="min-width:180px;">Email</th>
                    <th style="width:80px;">Blood</th>
                    <th style="min-width:120px;">Branch</th>
                    <th style="width:90px;">Status</th>
                    <th style="width:130px;">Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php if (!empty($patients)): ?>

                    <?php $sn = 1; ?>

                    <?php foreach ($patients as $pat): ?>

                        <tr>

                            <!-- # -->
                            <td class="sno">
                                <?= $sn++ ?>
                            </td>

                            <!-- Patient ID -->
                            <td>
                                <span class="patient-id">
                                    <?= esc($pat['patient_id'] ?? 'N/A') ?>
                                </span>
                            </td>

                            <!-- Name -->
                            <td>
                                <div class="patient-name">
                                    <?= esc($pat['name'] ?? 'N/A') ?>
                                </div>

                                <?php if (!empty($pat['id'])): ?>
                                    <span class="patient-code">
                                        Record ID: <?= esc((string)$pat['id']) ?>
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Gender / DOB -->
                            <td>
                                <span class="gender-text">
                                    <?= esc(ucfirst($pat['gender'] ?? 'N/A')) ?>
                                </span>

                                <?php if (!empty($pat['dob'])): ?>
                                    <span class="dob-text">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        <?= esc($pat['dob']) ?>
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Phone -->
                            <td>
                                <span class="phone-text">
                                    <?= esc($pat['phone'] ?? 'N/A') ?>
                                </span>
                            </td>

                            <!-- Email -->
                            <td>
                                <span class="email-text">
                                    <?= esc($pat['email'] ?? 'N/A') ?>
                                </span>
                            </td>

                            <!-- Blood -->
                            <td>
                                <span class="blood-badge">
                                    <?= esc($pat['blood_group'] ?? '—') ?>
                                </span>
                            </td>

                            <!-- Branch -->
                            <td>
                                <span class="branch-badge">
                                    <?= esc($pat['branch_name'] ?? 'N/A') ?>
                                </span>
                            </td>

                            <!-- Status -->
                            <td>

                                <?php
                                $status = strtolower(
                                    trim($pat['status'] ?? 'inactive')
                                );

                                $statusClass =
                                    $status === 'active'
                                        ? 'active'
                                        : 'inactive';
                                ?>

                                <span class="badge-status <?= $statusClass ?>">
                                    <?= esc(ucfirst($status)) ?>
                                </span>

                            </td>

                            <!-- Actions -->
                            <td>

                                <div class="patient-actions">

                                    <!-- History -->
                                    <a
                                        href="<?= site_url('/admin/patients/history/' . urlencode($pat['patient_id'])) ?>"
                                        class="btn-action history"
                                        title="Patient History"
                                    >
                                        <i class="bi bi-clock-history"></i>
                                    </a>

                                    <!-- Edit -->
                                    <a
                                        href="<?= site_url('/admin/patients/edit/' . urlencode($pat['id'])) ?>"
                                        class="btn-action edit"
                                        title="Edit Patient"
                                    >
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>

                                    <!-- Delete -->
                                    <a
                                        href="<?= site_url('/admin/patients/delete/' . urlencode($pat['id'])) ?>"
                                        class="btn-action delete"
                                        onclick="return confirm('Are you sure you want to delete this patient?');"
                                        title="Delete Patient"
                                    >
                                        <i class="bi bi-trash-fill"></i>
                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <!--
                        IMPORTANT:
                        Keep exactly 10 TDs even for empty state.
                        This prevents DataTables Incorrect Column Count.
                    -->
                    <tr class="empty-row">

                        <td class="sno">—</td>

                        <td>—</td>

                        <td class="empty-patient-state">
                            <i class="bi bi-people"></i>
                            <strong>No patients registered</strong>
                            <span>Registered patients will appear here.</span>
                        </td>

                        <td>—</td>

                        <td>—</td>

                        <td>—</td>

                        <td>—</td>

                        <td>—</td>

                        <td>—</td>

                        <td>—</td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

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
     DATATABLES BUTTONS
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

        $('#patientsTable').DataTable({

            pageLength: 25,

            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],

            responsive: true,

            autoWidth: false,

            processing: true,

            dom: '<"dt-top"Bf>rt<"dt-bottom"lip>',

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
                    targets: 9,
                    orderable: false,
                    searchable: false
                }
            ],

            language: {
                search: "Search:",
                searchPlaceholder: "Search patients...",
                lengthMenu: "Show _MENU_",
                info: "Showing _START_ to _END_ of _TOTAL_ patients",
                infoEmpty: "No patients available",
                zeroRecords: "No matching patients found",
                emptyTable: "No patients registered yet.",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }

        });

    }

});
</script>

<?php
include VIEWS_PATH . '/layout/admin_footer.php';
?>