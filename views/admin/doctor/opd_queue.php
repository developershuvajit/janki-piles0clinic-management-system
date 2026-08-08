<?php 
$activePage = 'doctor_opd';
include VIEWS_PATH . '/layout/doctor_header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-slate mb-1">OPD Patient Consultation Queue</h4>
        <p class="text-muted small mb-0">Active roster token queue for today (<?= date('d M Y') ?>)</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Token #</th>
                        <th>Patient Name</th>
                        <th>Appointment Time</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($queue)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No appointments in queue for today.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($queue as $q): ?>
                            <tr>
                                <td><span class="badge bg-primary fs-6 px-3 py-1.5 rounded-pill">Token #<?= esc((string)$q['token_number']) ?></span></td>
                                <td>
                                    <div class="fw-bold text-slate"><?= esc($q['patient_name']) ?></div>
                                    <div class="small text-muted">Code: <?= esc($q['patient_code'] ?? 'N/A') ?></div>
                                </td>
                                <td><?= date('h:i A', strtotime($q['time_slot'])) ?></td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-secondary text-uppercase"><?= esc($q['type']) ?></span></td>
                                <td>
                                    <?php if ($q['queue_status'] === 'in_consultation'): ?>
                                        <span class="badge bg-warning bg-opacity-20 text-warning border border-warning px-2.5 py-1">In Consultation</span>
                                    <?php elseif ($q['queue_status'] === 'completed'): ?>
                                        <span class="badge bg-success bg-opacity-20 text-success border border-success px-2.5 py-1">Completed</span>
                                    <?php else: ?>
                                        <span class="badge bg-info bg-opacity-20 text-info border border-info px-2.5 py-1">Waiting</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($q['queue_status'] !== 'completed'): ?>
                                        <a href="<?= site_url('/doctor/opd/consult/' . $q['id']) ?>" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                            <i class="bi bi-stethoscope me-1"></i> Consult Patient
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small"><i class="bi bi-check-circle-fill text-success me-1"></i> Finished</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/doctor_footer.php'; ?>
