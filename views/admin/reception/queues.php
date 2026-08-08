<?php 
$activePage = 'reception_queue';
include VIEWS_PATH . '/layout/reception_header.php'; 
?>

<!-- Queue list -->
<div class="table-responsive border-0 shadow-sm rounded-3">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light text-slate">
            <tr>
                <th>Token</th>
                <th>Patient Code & Name</th>
                <th>Assigned Doctor</th>
                <th>Branch</th>
                <th>Time Booked</th>
                <th>Queue Status</th>
                <th class="text-end">Queue Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($queues)): ?>
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-clock-history fs-3 d-block mb-2"></i>
                        No patients currently mapped in active consultation queues today.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($queues as $q): ?>
                    <tr>
                        <td class="fw-bold text-success fs-5">#<?= esc((string)$q['token_number']) ?></td>
                        <td>
                            <div class="fw-bold text-slate"><?= esc($q['patient_name']) ?></div>
                            <span class="text-muted small" style="font-size: 0.78rem;">ID: <?= esc($q['patient_code']) ?> &bull; <?= esc($q['patient_phone']) ?></span>
                        </td>
                        <td class="fw-semibold text-slate">Dr. <?= esc($q['doctor_name']) ?></td>
                        <td class="small"><?= esc($q['branch_name']) ?></td>
                        <td class="small text-muted"><?= esc(date('h:i A', strtotime($q['time_slot']))) ?></td>
                        <td>
                            <?php if ($q['queue_status'] === 'waiting'): ?>
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-1.5 rounded">Waiting</span>
                            <?php elseif ($q['queue_status'] === 'in_consultation'): ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1.5 rounded">In consultation</span>
                            <?php elseif ($q['queue_status'] === 'completed'): ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1.5 rounded">Completed</span>
                            <?php else: ?>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2.5 py-1.5 rounded">Skipped</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end text-nowrap">
                            <?php if ($q['queue_status'] === 'waiting'): ?>
                                <a href="<?= site_url('/admin/reception/queues/update/' . $q['id'] . '?status=in_consultation') ?>" class="btn btn-sm btn-outline-danger px-2.5 py-1 me-1" title="Mark as In Consultation">
                                    <i class="bi bi-play-fill me-1"></i> Start Call
                                </a>
                            <?php elseif ($q['queue_status'] === 'in_consultation'): ?>
                                <a href="<?= site_url('/admin/reception/queues/update/' . $q['id'] . '?status=completed') ?>" class="btn btn-sm btn-outline-success px-2.5 py-1 me-1" title="Mark as Completed">
                                    <i class="bi bi-check-circle me-1"></i> Complete
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($q['queue_status'] !== 'completed' && $q['queue_status'] !== 'skipped'): ?>
                                <a href="<?= site_url('/admin/reception/queues/update/' . $q['id'] . '?status=skipped') ?>" class="btn btn-sm btn-light border px-2.5 py-1" title="Skip Queue">
                                    <i class="bi bi-skip-forward-fill text-secondary"></i> Skip
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

<?php include VIEWS_PATH . '/layout/reception_footer.php'; ?>
