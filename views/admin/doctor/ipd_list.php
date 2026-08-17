<?php
$activePage = 'doctor_ipd';
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
    white-space: nowrap;
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
    margin-top: 4px;
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
    justify-content: flex-start;
}

/* ============================================
   VISIT NOTES BUTTON
   ============================================ */
.btn-action-visit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 0.4rem 0.75rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
    background: #eef2ff;
    color: #4f46e5;
    border: 1px solid #c7d2fe;
    white-space: nowrap;
}

.btn-action-visit:hover {
    background: #4f46e5;
    color: #fff;
    border-color: #4f46e5;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

/* ============================================
   PROCEDURE BUTTON
   ============================================ */
.btn-action-procedure {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 0.4rem 0.75rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
    background: #e6f7fe;
    color: #0e7c9e;
    border: 1px solid #a5d8e8;
    white-space: nowrap;
}

.btn-action-procedure:hover {
    background: #0e7c9e;
    color: #fff;
    border-color: #0e7c9e;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(14, 124, 158, 0.3);
}

/* ============================================
   DISCHARGE BUTTON
   ============================================ */
.btn-action-discharge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 0.4rem 0.75rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
    background: #d1fae5;
    color: #059669;
    border: 1px solid #6ee7b7;
    white-space: nowrap;
}

.btn-action-discharge:hover {
    background: #059669;
    color: #fff;
    border-color: #059669;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
}

/* ============================================
   STATUS BADGES
   ============================================ */
.status-badge-admitted {
    background: #fef3c7;
    color: #d97706;
    padding: 0.3rem 0.7rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
    white-space: nowrap;
}

.status-badge-approved {
    background: #d1fae5;
    color: #059669;
    padding: 0.3rem 0.7rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
    white-space: nowrap;
}

/* ============================================
   DATATABLE
   ============================================ */
#ipdTable {
    width: 100% !important;
}

#ipdTable thead th,
#ipdTable tbody td {
    vertical-align: middle;
}

/*
 * Keep horizontal scrolling inside table-responsive.
 * DataTables Responsive extension is intentionally disabled.
 */
.table-responsive {
    overflow-x: auto;
}

/* ============================================
   RESPONSIVE
   ============================================ */
@media (max-width: 992px) {
    .action-group {
        justify-content: flex-start;
    }
}

@media (max-width: 576px) {

    .action-group {
        flex-direction: column;
        gap: 4px;
        align-items: stretch;
    }

    .btn-action-visit,
    .btn-action-procedure,
    .btn-action-discharge {
        width: 100%;
        justify-content: center;
        padding: 0.35rem 0.65rem;
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
            <i class="bi bi-hospital text-success me-2"></i>
            Admitted IPD Patients
        </h4>

        <p class="text-muted small mb-0">
            Inpatient ward admissions under your clinical supervision
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
     IPD ADMISSIONS TABLE
     ============================================ -->
<div class="datatable-wrapper">

    <div class="datatable-header">

        <h5>
            IPD Admissions
            <small>
                <?= count($admissions ?? []) ?> patients under supervision
            </small>
        </h5>

    </div>


    <div class="table-responsive">

        <table
            id="ipdTable"
            class="table-custom"
            style="width:100%"
        >

            <!-- ========================================
                 TABLE HEADER
                 EXACTLY 8 COLUMNS
                 ======================================== -->
            <thead>
                <tr>

                    <th class="sno">
                        #
                    </th>

                    <th style="width:120px;">
                        Admission ID
                    </th>

                    <th style="min-width:180px;">
                        Patient Name
                    </th>

                    <th style="width:130px;">
                        Branch
                    </th>

                    <th style="width:160px;">
                        Admission Date
                    </th>

                    <th style="min-width:180px;">
                        Diagnosis
                    </th>

                    <th style="width:110px;">
                        Status
                    </th>

                    <th style="width:280px;">
                        Clinical Actions
                    </th>

                </tr>
            </thead>


            <!-- ========================================
                 TABLE BODY
                 IMPORTANT:
                 NO COLSPAN EMPTY ROW
                 ======================================== -->
            <tbody>

                <?php if (!empty($admissions)): ?>

                    <?php $sn = 1; ?>

                    <?php foreach ($admissions as $adm): ?>

                        <tr>

                            <!-- ==========================
                                 COLUMN 1 - SERIAL
                                 ========================== -->
                            <td class="sno">
                                <?= $sn++ ?>
                            </td>


                            <!-- ==========================
                                 COLUMN 2 - ADMISSION ID
                                 ========================== -->
                            <td>

                                <span class="adm-badge">
                                    #ADM-<?= esc((string)$adm['id']) ?>
                                </span>

                            </td>


                            <!-- ==========================
                                 COLUMN 3 - PATIENT
                                 ========================== -->
                            <td>

                                <div class="fw-bold text-slate">
                                    <?= esc($adm['patient_name'] ?? 'N/A') ?>
                                </div>

                                <span
                                    class="text-muted small"
                                    style="font-size:0.75rem;"
                                >
                                    Code:
                                    <?= esc($adm['patient_code'] ?? 'N/A') ?>
                                </span>

                            </td>


                            <!-- ==========================
                                 COLUMN 4 - BRANCH
                                 ========================== -->
                            <td>

                                <span class="badge bg-light text-dark border">
                                    <?= esc($adm['branch_name'] ?? 'Main Branch') ?>
                                </span>

                            </td>


                            <!-- ==========================
                                 COLUMN 5 - ADMISSION DATE
                                 ========================== -->
                            <td>

                                <?php
                                $admissionDate = $adm['admission_date'] ?? null;
                                ?>

                                <?php if ($admissionDate): ?>

                                    <div>
                                        <?= date(
                                            'd M Y',
                                            strtotime($admissionDate)
                                        ) ?>
                                    </div>

                                    <span
                                        class="text-muted small"
                                        style="font-size:0.7rem;"
                                    >
                                        <i class="bi bi-clock me-1"></i>

                                        <?= date(
                                            'h:i A',
                                            strtotime($admissionDate)
                                        ) ?>
                                    </span>

                                <?php else: ?>

                                    <span class="text-muted">
                                        N/A
                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- ==========================
                                 COLUMN 6 - DIAGNOSIS
                                 ========================== -->
                            <td>

                                <?php
                                $diagnosis = (string)($adm['diagnosis'] ?? '');
                                $symptoms  = (string)($adm['symptoms'] ?? '');
                                $admId     = (int)($adm['id'] ?? 0);
                                ?>

                                <div class="diagnosis-cell">

                                    <div
                                        class="diagnosis-text"
                                        id="diag-<?= $admId ?>"
                                    >

                                        <strong>
                                            <?= esc(
                                                $diagnosis !== ''
                                                    ? $diagnosis
                                                    : 'N/A'
                                            ) ?>
                                        </strong>


                                        <?php if ($symptoms !== ''): ?>

                                            <br>

                                            <span
                                                class="text-muted"
                                                style="font-size:0.8rem;"
                                            >
                                                <?= esc($symptoms) ?>
                                            </span>

                                        <?php endif; ?>

                                    </div>


                                    <?php if (strlen($diagnosis) > 50): ?>

                                        <span
                                            class="diagnosis-toggle"
                                            onclick="toggleDiagnosis(
                                                <?= $admId ?>,
                                                this
                                            )"
                                        >
                                            <i class="bi bi-eye"></i>
                                            See More
                                        </span>

                                    <?php endif; ?>

                                </div>

                            </td>


                            <!-- ==========================
                                 COLUMN 7 - STATUS
                                 ========================== -->
                            <td>

                                <?php if (
                                    ($adm['discharge_approval'] ?? '') === 'approved'
                                ): ?>

                                    <span class="status-badge-approved">

                                        <i class="bi bi-check-circle-fill me-1"></i>

                                        Approved

                                    </span>

                                <?php else: ?>

                                    <span class="status-badge-admitted">

                                        <i class="bi bi-hospital me-1"></i>

                                        Admitted

                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- ==========================
                                 COLUMN 8 - ACTIONS
                                 ========================== -->
                            <td>

                                <div class="action-group">

                                    <a
                                        href="<?= site_url('/doctor/ipd/visit-notes/' . $admId) ?>"
                                        class="btn-action-visit"
                                        title="Daily Visit Notes & Vitals"
                                    >

                                        <i class="bi bi-journal-medical"></i>

                                        Visit Notes

                                    </a>


                                    <a
                                        href="<?= site_url('/doctor/ipd/procedure-notes/' . $admId) ?>"
                                        class="btn-action-procedure"
                                        title="Procedure & Surgery Notes"
                                    >

                                        <i class="bi bi-tools"></i>

                                        Procedures

                                    </a>


                                    <a
                                        href="<?= site_url('/doctor/discharge/summary/' . $admId) ?>"
                                        class="btn-action-discharge"
                                        title="Approve & Generate Discharge Summary"
                                    >

                                        <i class="bi bi-box-arrow-right"></i>

                                        Discharge

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

    const diagnosis = document.getElementById('diag-' + id);

    if (!diagnosis) {
        return;
    }

    if (diagnosis.classList.contains('expanded')) {

        diagnosis.classList.remove('expanded');

        element.innerHTML =
            '<i class="bi bi-eye"></i> See More';

    } else {

        diagnosis.classList.add('expanded');

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
     DATATABLES JS
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

    $('#ipdTable').DataTable({

        pageLength: 25,

        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],

        /*
         * Keep this false because the table is already
         * inside .table-responsive.
         */
        responsive: false,

        autoWidth: false,

        processing: false,

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

        language: {

            emptyTable:
                'No admitted patients under your supervision.',

            zeroRecords:
                'No matching patients found.',

            search:
                'Search:',

            lengthMenu:
                'Show _MENU_ patients',

            info:
                'Showing _START_ to _END_ of _TOTAL_ patients',

            infoEmpty:
                'Showing 0 to 0 of 0 patients',

            infoFiltered:
                '(filtered from _MAX_ total patients)',

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
include VIEWS_PATH . '/layout/doctor_footer.php';
?>