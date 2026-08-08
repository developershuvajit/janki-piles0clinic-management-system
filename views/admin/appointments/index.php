<?php 
$activePage = 'appointments';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Action Links Row -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0 small">Overview of clinical consultations and patient token bookings.</p>
    <div>
        <a href="<?= site_url('/admin/appointments/pending') ?>" class="btn btn-outline-primary btn-sm px-3 me-2 shadow-sm">
            <i class="bi bi-clock-history me-1"></i> Pending Approvals
        </a>
        <a href="<?= site_url('/admin/appointments/schedule') ?>" class="btn btn-success btn-sm px-3 shadow-sm">
            <i class="bi bi-calendar-event me-1"></i> Shift schedules
        </a>
    </div>
</div>

<!-- Appointments Table -->
<div class="table-responsive border-0 shadow-sm rounded-3">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light text-slate">
            <tr>
                <th>Token</th>
                <th>Patient Details</th>
                <th>Doctor</th>
                <th>Schedule Date & Time</th>
                <th>Booking Type</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($appointments)): ?>
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                        No appointments found in the system database.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($appointments as $ap): ?>
                    <tr>
                        <td class="fw-bold text-success fs-5">#<?= esc((string)$ap['token_number']) ?></td>
                        <td>
                            <div class="fw-bold text-slate"><?= esc($ap['patient_name']) ?></div>
                            <span class="text-muted small" style="font-size: 0.78rem;">ID: <?= esc($ap['patient_code']) ?> &bull; <i class="bi bi-telephone"></i> <?= esc($ap['patient_phone']) ?></span>
                        </td>
                        <td class="fw-semibold text-slate">Dr. <?= esc($ap['doctor_name']) ?></td>
                        <td>
                            <div><?= esc($ap['date']) ?></div>
                            <span class="text-muted small" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i> <?= esc(date('h:i A', strtotime($ap['time_slot']))) ?></span>
                        </td>
                        <td>
                            <?php if ($ap['type'] === 'walk-in'): ?>
                                <span class="badge bg-light text-primary border"><i class="bi bi-person-fill"></i> Walk-In</span>
                            <?php else: ?>
                                <span class="badge bg-light text-info border"><i class="bi bi-globe"></i> Online</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($ap['status'] === 'approved'): ?>
                                <span class="badge badge-active px-2.5 py-1.5 rounded">Approved</span>
                            <?php elseif ($ap['status'] === 'completed'): ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1.5 rounded">Completed</span>
                            <?php elseif ($ap['status'] === 'cancelled'): ?>
                                <span class="badge badge-inactive px-2.5 py-1.5 rounded">Cancelled</span>
                            <?php else: ?>
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-1.5 rounded">Pending Approval</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?php if ($ap['status'] === 'approved'): ?>
                                <a href="<?= site_url('/admin/appointments/cancel/' . $ap['id']) ?>" class="btn btn-sm btn-outline-danger px-2.5 py-1" onclick="return confirm('Are you sure you want to cancel this appointment?');">
                                    <i class="bi bi-calendar-x me-1"></i> Cancel
                                </a>
                            <?php else: ?>
                                <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
