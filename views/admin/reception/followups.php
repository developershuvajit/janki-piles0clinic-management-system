<?php 
$activePage = 'reception_followups';
include VIEWS_PATH . '/layout/reception_header.php'; 
?>

<!-- ============================================
     PAGE CSS
     ============================================ -->
<link rel="stylesheet" href="<?= asset('css/datatable.css') ?>">

<style>
/* ============================================
   QUICK STATS CARDS
   ============================================ */
.stat-card {
    background: #fff;
    border-radius: 14px;
    padding: 1rem 1.2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: 1px solid #f0f2f5;
    transition: .15s;
    text-decoration: none;
    color: inherit;
    border-left: 4px solid transparent;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, .06);
}

.stat-card .stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    flex-shrink: 0;
}

.stat-card .stat-label {
    font-size: .65rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .03em;
    color: #6b7a8f;
}

.stat-card .stat-value {
    font-size: 1.7rem;
    font-weight: 700;
    line-height: 1.1;
    color: #0b1a2b;
}

.stat-card.border-warning {
    border-left-color: #f59e0b;
}

.stat-card.border-danger {
    border-left-color: #ef4444;
}

.stat-card.border-info {
    border-left-color: #3b82f6;
}

.stat-card.border-success {
    border-left-color: #10b981;
}

.stat-card.bg-active-warning {
    background: #fffbeb;
}

.stat-card.bg-active-danger {
    background: #fef2f2;
}

.stat-card.bg-active-info {
    background: #eff6ff;
}

.stat-card.bg-active-success {
    background: #ecfdf5;
}

/* ============================================
   DATATABLE PAGE SPACING
   ============================================ */
.followup-page {
    padding: 0 12px;
}

/* ============================================
   DATATABLE BUTTONS
   ============================================ */
#followupTable_wrapper .dt-buttons {
    margin-bottom: 12px;
}

#followupTable_wrapper .dt-button {
    border: 1px solid #e2e8f0 !important;
    background: #fff !important;
    color: #475569 !important;
    border-radius: 6px !important;
    padding: 5px 10px !important;
    font-size: 12px !important;
    box-shadow: none !important;
}

#followupTable_wrapper .dt-button:hover {
    background: #f8fafc !important;
    color: #0f172a !important;
}

/* Search box */
#followupTable_wrapper .dataTables_filter input {
    border: 1px solid #e2e8f0;
    border-radius: 7px;
    padding: 6px 10px;
    margin-left: 6px;
    outline: none;
}

#followupTable_wrapper .dataTables_filter input:focus {
    border-color: #6366f1;
}

/* Pagination */
#followupTable_wrapper .dataTables_paginate {
    margin-top: 10px;
}

/* ============================================
   EMPTY STATE
   ============================================ */
.followup-empty-icon {
    font-size: 1.8rem;
    color: #94a3b8;
    margin-bottom: 4px;
}

.followup-empty-text {
    color: #94a3b8;
    font-size: 0.85rem;
}

/* ============================================
   MOBILE
   ============================================ */
@media (max-width: 768px) {
    .followup-page {
        padding: 0;
    }

    .datatable-header {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 12px;
    }

    .datatable-header .btn-group {
        width: 100%;
        overflow-x: auto;
    }

    .datatable-header .btn-group .btn {
        white-space: nowrap;
    }

    .stat-card .stat-value {
        font-size: 1.45rem;
    }
}
</style>

<div class="followup-page">

    <!-- ============================================
         HEADER
         ============================================ -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-slate mb-1">
                <i class="bi bi-calendar2-check-fill text-success me-2"></i>
                Patient Follow-up Management
            </h4>

            <p class="text-muted small mb-0">
                Track due, upcoming, missed, and completed patient post-treatment visit schedules.
            </p>
        </div>

        <div>
            <a href="https://web.whatsapp.com"
               target="_blank"
               class="btn btn-outline-success btn-sm rounded-pill px-3">
                <i class="bi bi-whatsapp me-1"></i>
                Open WhatsApp Web
            </a>
        </div>
    </div>


    <!-- ============================================
         METRICS CARDS
         ============================================ -->
    <div class="row g-3 mb-4">

        <!-- Due -->
        <div class="col-md-3">
            <a href="<?= site_url('/reception/followups?tab=due') ?>"
               class="stat-card border-warning <?= ($active_tab === 'due') ? 'bg-active-warning' : '' ?>">

                <div>
                    <div class="stat-label">Due Today</div>

                    <div class="stat-value text-warning">
                        <?= esc((string)($metrics['due'] ?? 0)) ?>
                    </div>
                </div>

                <div class="stat-icon"
                     style="background:#fef3c7;color:#f59e0b;">
                    <i class="bi bi-clock-history"></i>
                </div>

            </a>
        </div>


        <!-- Missed -->
        <div class="col-md-3">
            <a href="<?= site_url('/reception/followups?tab=missed') ?>"
               class="stat-card border-danger <?= ($active_tab === 'missed') ? 'bg-active-danger' : '' ?>">

                <div>
                    <div class="stat-label">Missed Follow-ups</div>

                    <div class="stat-value text-danger">
                        <?= esc((string)($metrics['missed'] ?? 0)) ?>
                    </div>
                </div>

                <div class="stat-icon"
                     style="background:#fee2e2;color:#ef4444;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>

            </a>
        </div>


        <!-- Upcoming -->
        <div class="col-md-3">
            <a href="<?= site_url('/reception/followups?tab=upcoming') ?>"
               class="stat-card border-info <?= ($active_tab === 'upcoming') ? 'bg-active-info' : '' ?>">

                <div>
                    <div class="stat-label">Upcoming Visits</div>

                    <div class="stat-value text-info">
                        <?= esc((string)($metrics['upcoming'] ?? 0)) ?>
                    </div>
                </div>

                <div class="stat-icon"
                     style="background:#dbeafe;color:#3b82f6;">
                    <i class="bi bi-calendar-event"></i>
                </div>

            </a>
        </div>


        <!-- Completed -->
        <div class="col-md-3">
            <a href="<?= site_url('/reception/followups?tab=completed') ?>"
               class="stat-card border-success <?= ($active_tab === 'completed') ? 'bg-active-success' : '' ?>">

                <div>
                    <div class="stat-label">Completed</div>

                    <div class="stat-value text-success">
                        <?= esc((string)($metrics['completed'] ?? 0)) ?>
                    </div>
                </div>

                <div class="stat-icon"
                     style="background:#d1fae5;color:#10b981;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>

            </a>
        </div>

    </div>


    <!-- ============================================
         FOLLOW-UP TABLE
         ============================================ -->
    <div class="datatable-wrapper mt-4">

        <div class="datatable-header">

            <h5>
                <?= ucfirst($active_tab) ?> Patient Follow-up List
                <small>
                    <?= count($followups ?? []) ?> records
                </small>
            </h5>

            <div class="btn-group btn-group-sm" role="group">

                <a href="<?= site_url('/reception/followups?tab=due') ?>"
                   class="btn btn-outline-secondary <?= ($active_tab === 'due') ? 'active' : '' ?>">
                    Due Today
                </a>

                <a href="<?= site_url('/reception/followups?tab=missed') ?>"
                   class="btn btn-outline-secondary <?= ($active_tab === 'missed') ? 'active' : '' ?>">
                    Missed
                </a>

                <a href="<?= site_url('/reception/followups?tab=upcoming') ?>"
                   class="btn btn-outline-secondary <?= ($active_tab === 'upcoming') ? 'active' : '' ?>">
                    Upcoming
                </a>

                <a href="<?= site_url('/reception/followups?tab=completed') ?>"
                   class="btn btn-outline-secondary <?= ($active_tab === 'completed') ? 'active' : '' ?>">
                    Completed
                </a>

            </div>

        </div>


        <div class="table-responsive">

            <table id="followupTable"
                   class="table-custom"
                   style="width:100%">

                <thead>
                    <tr>
                        <th class="sno">#</th>

                        <th style="min-width:120px;">
                            Patient Code
                        </th>

                        <th style="min-width:150px;">
                            Patient Name
                        </th>

                        <th style="min-width:140px;">
                            Contact Phone
                        </th>

                        <th style="min-width:130px;">
                            Scheduled Date
                        </th>

                        <th style="width:100px;">
                            Channel
                        </th>

                        <th style="min-width:160px;">
                            Clinical Notes
                        </th>

                        <th style="width:200px;">
                            Actions
                        </th>
                    </tr>
                </thead>


                <tbody>

                    <?php if (!empty($followups)): ?>

                        <?php 
                        $sn = 1;

                        foreach ($followups as $f):

                            $waMsg =
                                "Namaste " .
                                $f['patient_name'] .
                                ", reminder for your post-treatment follow-up checkup at Janki Piles Clinic scheduled for " .
                                date('d M Y', strtotime($f['next_visit_date'])) .
                                ". Please call helpline to confirm your appointment time.";

                            $waLink = \App\Models\Communication::getWhatsAppLink(
                                $f['patient_phone'],
                                $waMsg
                            );
                        ?>

                            <tr>

                                <!-- 1. Serial -->
                                <td class="sno">
                                    <?= $sn++ ?>
                                </td>


                                <!-- 2. Patient Code -->
                                <td class="fw-bold text-slate">
                                    <?= esc($f['patient_code']) ?>
                                </td>


                                <!-- 3. Patient Name -->
                                <td>
                                    <div class="fw-bold text-dark">
                                        <?= esc($f['patient_name']) ?>
                                    </div>
                                </td>


                                <!-- 4. Phone -->
                                <td>

                                    <a href="tel:<?= esc($f['patient_phone']) ?>"
                                       class="text-decoration-none text-dark">

                                        <i class="bi bi-telephone me-1 text-muted"></i>

                                        <?= esc($f['patient_phone']) ?>

                                    </a>

                                </td>


                                <!-- 5. Scheduled Date -->
                                <td>

                                    <span class="badge bg-light text-dark border">

                                        <i class="bi bi-calendar-event me-1"></i>

                                        <?= esc(
                                            date(
                                                'd M Y',
                                                strtotime($f['next_visit_date'])
                                            )
                                        ) ?>

                                    </span>

                                </td>


                                <!-- 6. Channel -->
                                <td>

                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">

                                        <?= esc(
                                            strtoupper(
                                                $f['channel']
                                            )
                                        ) ?>

                                    </span>

                                </td>


                                <!-- 7. Notes -->
                                <td>

                                    <span class="text-muted small"
                                          style="font-size:0.82rem;">

                                        <?= esc(
                                            $f['notes']
                                            ?: 'Follow-up review'
                                        ) ?>

                                    </span>

                                </td>


                                <!-- 8. Actions -->
                                <td>

                                    <div class="action-group">

                                        <a href="<?= $waLink ?>"
                                           target="_blank"
                                           class="btn-action"
                                           title="WhatsApp Reminder"
                                           style="color:#25D366;">

                                            <i class="bi bi-whatsapp"></i>

                                        </a>


                                        <a href="<?= site_url('/reception/walk-in?patient_id=' . $f['patient_id']) ?>"
                                           class="btn-action"
                                           title="Book Walk-in"
                                           style="color:#6366f1;">

                                            <i class="bi bi-box-arrow-in-right"></i>

                                        </a>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <!--
                            IMPORTANT:
                            DataTables does NOT support colspan inside tbody.
                            Therefore exactly 8 TDs are used here.
                        -->

                        <tr>

                            <!-- TD 1 -->
                            <td style="text-align:center;padding:2.5rem 0.5rem;color:#94a3b8;">
                                <i class="bi bi-calendar-x fs-3"></i>
                            </td>

                            <!-- TD 2 -->
                            <td style="padding:2.5rem 0.5rem;color:#94a3b8;">
                                <span class="followup-empty-text">
                                    No <?= esc($active_tab) ?> follow-ups found in system.
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

                            <!-- TD 8 -->
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

    $('#followupTable').DataTable({

        pageLength: 25,

        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],

        responsive: false,

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
            searchPlaceholder: "Search follow-ups...",
            lengthMenu: "Show _MENU_ records",
            info: "Showing _START_ to _END_ of _TOTAL_ records",
            infoEmpty: "Showing 0 to 0 of 0 records",
            zeroRecords: "No matching follow-ups found",
            emptyTable: "No follow-up records available"
        }

    });

});
</script>


<?php include VIEWS_PATH . '/layout/reception_footer.php'; ?>