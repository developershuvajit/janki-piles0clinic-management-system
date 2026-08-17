<?php 
$activePage = 'appointments';
include VIEWS_PATH . '/layout/reception_header.php'; 
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
        <h5>Appointment List <small><?= count($appointments ?? []) ?> booked</small></h5>
        <div>
            
            <!-- ===== SCHEDULE BUTTON ADDED ===== -->
            <a href="<?= site_url('/admin/appointments/schedule') ?>" class="btn-register" style="background: #10b981; border-color: #10b981;">
                <i class="bi bi-calendar-event me-1"></i> Schedule
            </a>
            <!-- ================================ -->
        </div>
    </div>

    <div class="table-responsive">
        <table id="appointmentsTable" class="table-custom" style="width:100%">
            <thead>
                <tr>
                    <th class="sno">#</th>
                    <th>Token</th>
                    <th style="min-width:180px;">Patient Details</th>
                    <th>Doctor</th>
                    <th style="min-width:120px;">Date & Time</th>
                    <th style="width:100px;">Booking Type</th>
                    <th style="width:100px;">Status</th>
                    <th style="width:130px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($appointments)):
                    $sn = 1;
                    foreach ($appointments as $ap): ?>
                        <tr>
                            <td class="sno"><?= $sn++ ?></td>
                            <td class="fw-bold text-success fs-5">#<?= esc((string)$ap['token_number']) ?></td>
                            <td>
                                <div class="fw-bold text-slate"><?= esc($ap['patient_name']) ?></div>
                                <span class="text-muted small" style="font-size: 0.78rem;">
                                    ID: <?= esc($ap['patient_code']) ?> &bull; 
                                    <i class="bi bi-telephone"></i> <?= esc($ap['patient_phone']) ?>
                                </span>
                            </td>
                            <td class="fw-semibold text-slate">Dr. <?= esc($ap['doctor_name']) ?></td>
                            <td>
                                <div><?= esc($ap['date']) ?></div>
                                <span class="text-muted small" style="font-size: 0.75rem;">
                                    <i class="bi bi-clock me-1"></i> <?= esc(date('h:i A', strtotime($ap['time_slot']))) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($ap['type'] === 'walk-in'): ?>
                                    <span class="badge bg-light text-primary border">
                                        <i class="bi bi-person-fill"></i> Walk-In
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-light text-info border">
                                        <i class="bi bi-globe"></i> Online
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (($ap['status'] ?? '') === 'approved'): ?>
                                    <span class="badge-status active">Approved</span>
                                <?php elseif (($ap['status'] ?? '') === 'completed'): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1.5 rounded">Completed</span>
                                <?php elseif (($ap['status'] ?? '') === 'cancelled'): ?>
                                    <span class="badge-status inactive">Cancelled</span>
                                <?php else: ?>
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-1.5 rounded">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-group">
                                    <?php if (($ap['status'] ?? '') === 'pending'): ?>
                                        <a href="<?= site_url('/reception/appointments/approve/' . $ap['id']) ?>" 
                                           class="btn-action" style="color:#10b981;"
                                           onclick="return confirm('Approve this appointment?');" 
                                           title="Approve">
                                            <i class="bi bi-check-circle-fill"></i>
                                        </a>
                                        <a href="<?= site_url('/reception/appointments/cancel/' . $ap['id']) ?>" 
                                           class="btn-action delete" 
                                           onclick="return confirm('Cancel this appointment?');" 
                                           title="Cancel">
                                            <i class="bi bi-x-circle-fill"></i>
                                        </a>
                                    <?php elseif (($ap['status'] ?? '') === 'approved'): ?>
                                        <a href="<?= site_url('/reception/appointments/cancel/' . $ap['id']) ?>" 
                                           class="btn-action delete" 
                                           onclick="return confirm('Are you sure you want to cancel this appointment?');" 
                                           title="Cancel">
                                            <i class="bi bi-calendar-x"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center;padding:2.5rem 1rem;color:#94a3b8;">
                            <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                            No appointments found in the system database.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ============================================
     DATATABLES LIBS + INIT
     ============================================ -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
    $('#appointmentsTable').DataTable({
        pageLength: 25,
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
});
</script>

<?php include VIEWS_PATH . '/layout/reception_footer.php'; ?>