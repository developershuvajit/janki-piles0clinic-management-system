<?php 
$activePage = 'doctor_opd';
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

.btn-emerald {
    background: #059669;
    border-color: #059669;
    color: #fff;
}

.btn-emerald:hover {
    background: #047857;
    border-color: #047857;
    color: #fff;
}

/* Token Badge */
.token-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #dbeafe;
    color: #1d4ed8;
    font-weight: 700;
    padding: 0.35rem 1rem;
    border-radius: 40px;
    font-size: 0.9rem;
    white-space: nowrap;
}

/* Consultation Button */
.consult-btn {
    color: #059669;
    border: 1px solid #059669;
    padding: 0.35rem 1rem;
    border-radius: 8px;
    font-size: 0.78rem;
    background: #fff;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    transition: all 0.2s ease;
}

.consult-btn:hover {
    background: #059669;
    color: #fff;
    border-color: #059669;
}

/* Empty State */
.opd-empty-state {
    text-align: center !important;
    padding: 2.5rem 1rem !important;
    color: #94a3b8 !important;
}

.opd-empty-state i {
    display: block;
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

/* DataTable Button Styling */
#opdTable_wrapper .dt-buttons {
    margin-bottom: 12px;
}

#opdTable_wrapper .dt-button {
    border-radius: 7px !important;
    border: 1px solid #e2e8f0 !important;
    background: #fff !important;
    color: #475569 !important;
    padding: 5px 12px !important;
    font-size: 0.8rem !important;
    margin-right: 4px !important;
}

#opdTable_wrapper .dt-button:hover {
    background: #f8fafc !important;
    color: #059669 !important;
    border-color: #059669 !important;
}

/* Search */
#opdTable_wrapper .dataTables_filter input {
    border: 1px solid #e2e8f0;
    border-radius: 7px;
    padding: 6px 10px;
    outline: none;
}

#opdTable_wrapper .dataTables_filter input:focus {
    border-color: #059669;
}

/* Mobile */
@media (max-width: 768px) {
    .consult-btn {
        padding: 0.3rem 0.65rem;
        font-size: 0.72rem;
    }

    .token-badge {
        padding: 0.3rem 0.7rem;
        font-size: 0.8rem;
    }
}
</style>

<!-- ============================================
     HEADER
     ============================================ -->
<div class="d-flex justify-content-between align-items-center mb-4 mt-4 mx-4">
    <div>
        <h4 class="fw-bold text-slate mb-1">
            <i class="bi bi-stethoscope text-success me-2"></i>
            OPD Patient Consultation Queue
        </h4>

        <p class="text-muted small mb-0">
            Active roster token queue for today (<?= date('d M Y') ?>)
        </p>
    </div>

    <div>
        <span class="badge bg-light text-dark border px-3 py-2">
            <i class="bi bi-people me-1"></i>
            <?= count($queue ?? []) ?> Patients
        </span>
    </div>
</div>

<!-- ============================================
     QUEUE TABLE
     ============================================ -->
<div class="datatable-wrapper">
    
    <div class="datatable-header">
        <h5>
            Consultation Queue
            <small>
                <?= count($queue ?? []) ?> patients waiting/under consultation
            </small>
        </h5>
    </div>

    <div class="table-responsive">

        <table id="opdTable" class="table-custom" style="width:100%">

            <!-- ============================================
                 EXACTLY 7 HEADER COLUMNS
                 ============================================ -->
            <thead>
                <tr>
                    <th class="sno">#</th>
                    <th style="width:120px;">Token #</th>
                    <th style="min-width:180px;">Patient Name</th>
                    <th style="width:140px;">Appointment Time</th>
                    <th style="width:110px;">Type</th>
                    <th style="width:150px;">Status</th>
                    <th style="width:170px;">Action</th>
                </tr>
            </thead>

            <tbody>

                <?php if (!empty($queue)): ?>

                    <?php 
                    $sn = 1;

                    foreach ($queue as $q): 
                    ?>

                        <!-- ============================================
                             EXACTLY 7 TD COLUMNS
                             ============================================ -->
                        <tr>

                            <!-- # -->
                            <td class="sno">
                                <?= $sn++ ?>
                            </td>

                            <!-- Token -->
                            <td>
                                <span class="token-badge">
                                    #<?= esc((string)($q['token_number'] ?? 'N/A')) ?>
                                </span>
                            </td>

                            <!-- Patient -->
                            <td>
                                <div class="fw-bold text-slate">
                                    <?= esc($q['patient_name'] ?? 'Unknown Patient') ?>
                                </div>

                                <span class="text-muted small" style="font-size:0.75rem;">
                                    Code:
                                    <?= esc($q['patient_code'] ?? 'N/A') ?>
                                </span>
                            </td>

                            <!-- Appointment Time -->
                            <td>

                                <?php
                                $timeSlot = $q['time_slot'] ?? null;
                                ?>

                                <?php if ($timeSlot): ?>

                                    <div>
                                        <?= esc(date('d M Y', strtotime($timeSlot))) ?>
                                    </div>

                                    <span class="text-muted small" style="font-size:0.75rem;">
                                        <i class="bi bi-clock me-1"></i>
                                        <?= esc(date('h:i A', strtotime($timeSlot))) ?>
                                    </span>

                                <?php else: ?>

                                    <span class="text-muted">
                                        N/A
                                    </span>

                                <?php endif; ?>

                            </td>

                            <!-- Type -->
                            <td>

                                <span
                                    class="badge bg-light text-secondary border text-uppercase"
                                    style="font-size:0.7rem;font-weight:600;"
                                >
                                    <?= esc($q['type'] ?? 'N/A') ?>
                                </span>

                            </td>

                            <!-- Status -->
                            <td>

                                <?php 
                                $queueStatus = $q['queue_status'] ?? 'waiting';
                                ?>

                                <?php if ($queueStatus === 'in_consultation'): ?>

                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-1.5 rounded">

                                        <i class="bi bi-clock-history me-1"></i>

                                        In Consultation

                                    </span>

                                <?php elseif ($queueStatus === 'completed'): ?>

                                    <span class="badge-status active">

                                        <i class="bi bi-check-circle-fill me-1"></i>

                                        Completed

                                    </span>

                                <?php elseif ($queueStatus === 'skipped'): ?>

                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2.5 py-1.5 rounded">

                                        <i class="bi bi-skip-forward-fill me-1"></i>

                                        Skipped

                                    </span>

                                <?php else: ?>

                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2.5 py-1.5 rounded">

                                        <i class="bi bi-hourglass-split me-1"></i>

                                        Waiting

                                    </span>

                                <?php endif; ?>

                            </td>

                            <!-- Action -->
                            <td>

                                <?php if ($queueStatus !== 'completed' && $queueStatus !== 'skipped'): ?>

                                    <div class="action-group">

                                        <a
                                            href="<?= site_url('/doctor/opd/consult/' . (int)($q['id'] ?? 0)) ?>"
                                            class="consult-btn"
                                            title="Consult Patient"
                                        >

                                            <i class="bi bi-stethoscope me-1"></i>

                                            Consult

                                        </a>

                                    </div>

                                <?php elseif ($queueStatus === 'completed'): ?>

                                    <span class="text-muted small">

                                        <i class="bi bi-check-circle-fill text-success me-1"></i>

                                        Finished

                                    </span>

                                <?php else: ?>

                                    <span class="text-muted small">

                                        <i class="bi bi-skip-forward-fill me-1"></i>

                                        Skipped

                                    </span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <!--
                        IMPORTANT:
                        DataTables requires the number of cells in
                        every tbody row to match the thead.

                        Therefore NO colspan is used here.
                        We provide exactly 7 TDs.
                    -->

                    <tr class="opd-empty-row">

                        <td class="opd-empty-state">
                            <i class="bi bi-calendar-check"></i>
                            No appointments
                        </td>

                        <td class="opd-empty-state">
                            —
                        </td>

                        <td class="opd-empty-state">
                            No patients in queue
                        </td>

                        <td class="opd-empty-state">
                            —
                        </td>

                        <td class="opd-empty-state">
                            —
                        </td>

                        <td class="opd-empty-state">
                            —
                        </td>

                        <td class="opd-empty-state">
                            —
                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>
</div>

<!-- ============================================
     DATATABLES LIBRARIES
     ============================================ -->

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

<!-- ============================================
     DATATABLE INIT
     ============================================ -->
<script>
$(document).ready(function () {

    // Prevent duplicate initialization
    if ($.fn.DataTable.isDataTable('#opdTable')) {
        $('#opdTable').DataTable().destroy();
    }

    $('#opdTable').DataTable({

        pageLength: 25,

        responsive: true,

        processing: false,

        autoWidth: false,

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
            emptyTable: "No appointments in queue for today.",
            zeroRecords: "No matching patients found.",
            search: "Search:"
        }

    });

});
</script>

<?php include VIEWS_PATH . '/layout/doctor_footer.php'; ?>