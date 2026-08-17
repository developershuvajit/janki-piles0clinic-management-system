<?php 
$activePage = 'doctor_discharge';
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

/* ============================================
   ADMISSION BADGE
   ============================================ */
.adm-badge {
    background: #dbeafe;
    color: #1d4ed8;
    font-weight: 700;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.8rem;
    display: inline-block;
}

/* ============================================
   DIAGNOSIS
   ============================================ */
.diagnosis-cell {
    max-width: 180px;
    position: relative;
}

.diagnosis-text {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    font-size: 0.85rem;
    line-height: 1.4;
}

.diagnosis-text.expanded {
    -webkit-line-clamp: unset;
    display: block;
}

.diagnosis-toggle {
    display: inline-block;
    font-size: 0.7rem;
    color: #6366f1;
    cursor: pointer;
    font-weight: 600;
    margin-top: 3px;
    padding: 2px 8px;
    border-radius: 4px;
    background: #eef2ff;
    border: 1px solid #c7d2fe;
    transition: all 0.2s ease;
}

.diagnosis-toggle:hover {
    background: #4f46e5;
    color: #fff;
    border-color: #4f46e5;
}

/* ============================================
   ACTION GROUP
   ============================================ */
.action-group {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
    justify-content: flex-end;
}

/* ============================================
   APPROVE BUTTON
   ============================================ */
.btn-action-approve {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 0.4rem 1rem;
    border-radius: 8px;
    font-size: 0.78rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
    background: #d1fae5;
    color: #059669;
    border: 1px solid #6ee7b7;
    white-space: nowrap;
}

.btn-action-approve:hover {
    background: #059669;
    color: #fff;
    border-color: #059669;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
}

.btn-action-approve i {
    font-size: 0.9rem;
}

/* ============================================
   SUMMARY BUTTON
   ============================================ */
.btn-action-summary {
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
    white-space: nowrap;
}

.btn-action-summary:hover {
    background: #4f46e5;
    color: #fff;
    border-color: #4f46e5;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

.btn-action-summary i {
    font-size: 0.9rem;
}

/* ============================================
   STATUS BADGES
   ============================================ */
.status-badge-pending {
    background: #fef3c7;
    color: #d97706;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
    white-space: nowrap;
}

.status-badge-approved {
    background: #d1fae5;
    color: #059669;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
    white-space: nowrap;
}

/* ============================================
   EMPTY STATE
   IMPORTANT:
   Do NOT use colspan for DataTables.
   ============================================ */
.empty-state-cell {
    text-align: center !important;
    padding: 2.5rem 1rem !important;
    color: #94a3b8 !important;
}

/* ============================================
   DATATABLE CUSTOM FIX
   ============================================ */
#dischargeTable {
    width: 100% !important;
}

#dischargeTable th,
#dischargeTable td {
    vertical-align: middle;
}

#dischargeTable .dataTables_empty {
    text-align: center !important;
    padding: 2.5rem 1rem !important;
}

/* ============================================
   RESPONSIVE
   ============================================ */
@media (max-width: 768px) {

    .action-group {
        justify-content: flex-start;
    }

    .btn-action-approve,
    .btn-action-summary {
        padding: 0.35rem 0.7rem;
        font-size: 0.72rem;
    }

    .diagnosis-cell {
        max-width: 150px;
    }
}

@media (max-width: 576px) {

    .action-group {
        flex-direction: column;
        gap: 4px;
        align-items: stretch;
    }

    .btn-action-approve,
    .btn-action-summary {
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
        <h4 class="fw-bold text-slate mb-1">
            <i class="bi bi-box-arrow-right text-success me-2"></i>
            IPD Patient Discharge Approvals
        </h4>

        <p class="text-muted small mb-0">
            Approve patient discharges and generate final medical discharge summaries
        </p>
    </div>

    <div>
        <span class="badge bg-light text-dark border px-3 py-2">
            <i class="bi bi-people me-1"></i>
            <?= count($admissions ?? []) ?> Patients
        </span>
    </div>

</div>


<!-- ============================================
     DISCHARGE APPROVALS TABLE
     ============================================ -->
<div class="datatable-wrapper">

    <div class="datatable-header">
        <h5>
            Discharge Approvals
            <small>
                <?= count($admissions ?? []) ?> patients ready for discharge
            </small>
        </h5>
    </div>

    <div class="table-responsive">

        <table
            id="dischargeTable"
            class="table-custom"
            style="width:100%"
        >

            <thead>
                <tr>
                    <th class="sno">#</th>
                    <th style="width:120px;">Admission #</th>
                    <th style="min-width:180px;">Patient Name</th>
                    <th style="width:130px;">Branch</th>
                    <th style="width:160px;">Admit Date</th>
                    <th style="min-width:180px;">Diagnosis</th>
                    <th style="width:140px;">Discharge Status</th>
                    <th style="width:200px;">Actions</th>
                </tr>
            </thead>

            <tbody>

            <?php if (!empty($admissions)): ?>

                <?php
                $sn = 1;

                foreach ($admissions as $adm):

                    $admissionId = (int)($adm['id'] ?? 0);

                    $patientName = $adm['patient_name'] ?? 'Unknown Patient';

                    $diagnosis = $adm['diagnosis'] ?? 'Not specified';

                    $symptoms = $adm['symptoms'] ?? '';

                    $dischargeApproval = $adm['discharge_approval'] ?? 'pending';

                    $admissionDate = $adm['admission_date'] ?? null;
                ?>

                    <tr>

                        <!-- # -->
                        <td class="sno">
                            <?= $sn++ ?>
                        </td>


                        <!-- Admission ID -->
                        <td>
                            <span class="adm-badge">
                                #ADM-<?= esc((string)$admissionId) ?>
                            </span>
                        </td>


                        <!-- Patient -->
                        <td>

                            <div class="fw-bold text-slate">
                                <?= esc($patientName) ?>
                            </div>

                            <span
                                class="text-muted small"
                                style="font-size:0.75rem;"
                            >
                                Code:
                                <?= esc($adm['patient_code'] ?? 'N/A') ?>
                            </span>

                        </td>


                        <!-- Branch -->
                        <td>

                            <span class="badge bg-light text-dark border">
                                <?= esc($adm['branch_name'] ?? 'Main Branch') ?>
                            </span>

                        </td>


                        <!-- Admission Date -->
                        <td>

                            <?php if ($admissionDate): ?>

                                <div>
                                    <?= date('d M Y', strtotime($admissionDate)) ?>
                                </div>

                                <span
                                    class="text-muted small"
                                    style="font-size:0.7rem;"
                                >
                                    <i class="bi bi-clock me-1"></i>
                                    <?= date('h:i A', strtotime($admissionDate)) ?>
                                </span>

                            <?php else: ?>

                                <span class="text-muted">
                                    N/A
                                </span>

                            <?php endif; ?>

                        </td>


                        <!-- Diagnosis -->
                        <td>

                            <div class="diagnosis-cell">

                                <div
                                    class="diagnosis-text"
                                    id="diag-<?= $admissionId ?>"
                                >

                                    <strong>
                                        <?= esc($diagnosis) ?>
                                    </strong>

                                    <?php if (!empty($symptoms)): ?>

                                        <br>

                                        <span
                                            class="text-muted"
                                            style="font-size:0.8rem;"
                                        >
                                            <?= esc($symptoms) ?>
                                        </span>

                                    <?php endif; ?>

                                </div>


                                <?php if (strlen($diagnosis) > 50 || strlen($symptoms) > 80): ?>

                                    <span
                                        class="diagnosis-toggle"
                                        onclick="toggleDiagnosis(
                                            <?= $admissionId ?>,
                                            this
                                        )"
                                    >
                                        <i class="bi bi-eye"></i>
                                        See More
                                    </span>

                                <?php endif; ?>

                            </div>

                        </td>


                        <!-- Discharge Status -->
                        <td>

                            <?php if ($dischargeApproval === 'approved'): ?>

                                <span class="status-badge-approved">

                                    <i class="bi bi-check-circle-fill me-1"></i>

                                    Approved

                                </span>

                            <?php else: ?>

                                <span class="status-badge-pending">

                                    <i class="bi bi-clock me-1"></i>

                                    Pending

                                </span>

                            <?php endif; ?>

                        </td>


                        <!-- Actions -->
                        <td>

                            <div class="action-group">

                                <?php if ($dischargeApproval !== 'approved'): ?>

                                    <a
                                        href="<?= site_url('/doctor/discharge/approve/' . $admissionId) ?>"
                                        class="btn-action-approve"
                                        title="Approve Discharge"
                                        onclick="return confirm(
                                            'Approve discharge for patient <?= esc($patientName) ?>?'
                                        );"
                                    >

                                        <i class="bi bi-check-lg"></i>

                                        Approve

                                    </a>

                                <?php endif; ?>


                                <a
                                    href="<?= site_url('/doctor/discharge/summary/' . $admissionId) ?>"
                                    class="btn-action-summary"
                                    title="Generate Discharge Summary"
                                >

                                    <i class="bi bi-file-earmark-medical"></i>

                                    Summary

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
     DIAGNOSIS TOGGLE
     ============================================ -->
<script>
function toggleDiagnosis(id, element) {

    const diagElement = document.getElementById('diag-' + id);

    if (!diagElement) {
        return;
    }

    if (diagElement.classList.contains('expanded')) {

        diagElement.classList.remove('expanded');

        element.innerHTML =
            '<i class="bi bi-eye"></i> See More';

    } else {

        diagElement.classList.add('expanded');

        element.innerHTML =
            '<i class="bi bi-eye-slash"></i> See Less';
    }
}
</script>


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
     DATATABLES
     ============================================ -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

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

    const table = $('#dischargeTable');

    if (!table.length) {
        return;
    }

    table.DataTable({

        pageLength: 25,

        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],

        searching: true,

        ordering: true,

        info: true,

        paging: true,

        autoWidth: false,

        dom: 'Bfrtip',

        buttons: [
            {
                extend: 'copy',
                text: '<i class="bi bi-copy me-1"></i> Copy',
                className: 'btn btn-sm btn-outline-secondary'
            },
            {
                extend: 'csv',
                text: '<i class="bi bi-filetype-csv me-1"></i> CSV',
                className: 'btn btn-sm btn-outline-secondary'
            },
            {
                extend: 'excel',
                text: '<i class="bi bi-file-earmark-excel me-1"></i> Excel',
                className: 'btn btn-sm btn-outline-success'
            },
            {
                extend: 'pdf',
                text: '<i class="bi bi-file-earmark-pdf me-1"></i> PDF',
                className: 'btn btn-sm btn-outline-danger'
            },
            {
                extend: 'print',
                text: '<i class="bi bi-printer me-1"></i> Print',
                className: 'btn btn-sm btn-outline-primary'
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
                orderable: false
            }
        ],

        language: {
            search: "",
            searchPlaceholder: "Search patients...",
            lengthMenu: "Show _MENU_ patients",
            info: "Showing _START_ to _END_ of _TOTAL_ patients",
            infoEmpty: "No patients available",
            zeroRecords: "No matching patients found",
            emptyTable: "No admitted patients available for discharge approval"
        }

    });

});
</script>


<?php 
include VIEWS_PATH . '/layout/doctor_footer.php'; 
?>