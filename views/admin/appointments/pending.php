<?php 
$activePage = 'appointments';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Pending Directory Table -->
<div class="table-responsive border-0 shadow-sm rounded-3">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light text-slate">
            <tr>
                <th>Patient Details</th>
                <th>Requested Doctor</th>
                <th>Branch</th>
                <th>Requested Date & Time</th>
                <th>Status</th>
                <th class="text-end">Verification Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($appointments)): ?>
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-check fs-3 d-block mb-2"></i>
                        No pending online appointments requiring administrator approval.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($appointments as $ap): ?>
                    <tr>
                        <td>
                            <div class="fw-bold text-slate"><?= esc($ap['patient_name']) ?></div>
                            <span class="text-muted small" style="font-size: 0.78rem;">ID: <?= esc($ap['patient_code']) ?> &bull; <i class="bi bi-telephone"></i> <?= esc($ap['patient_phone']) ?></span>
                        </td>
                        <td class="fw-semibold text-slate">Dr. <?= esc($ap['doctor_name']) ?></td>
                        <td class="small"><?= esc($ap['branch_name']) ?></td>
                        <td>
                            <div><?= esc($ap['date']) ?></div>
                            <span class="text-muted small" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i> <?= esc(date('h:i A', strtotime($ap['time_slot']))) ?></span>
                        </td>
                        <td>
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-1.5 rounded">Pending Approval</span>
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="<?= site_url('/admin/appointments/approve/' . $ap['id']) ?>" class="btn btn-sm btn-primary px-3 py-1 me-1 shadow-sm">
                                <i class="bi bi-check-circle me-1"></i> Approve
                            </a>
                            <a href="<?= site_url('/admin/appointments/cancel/' . $ap['id']) ?>" class="btn btn-sm btn-light border text-danger px-2.5 py-1" onclick="return confirm('Cancel this request?');">
                                <i class="bi bi-trash"></i> Decline
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
