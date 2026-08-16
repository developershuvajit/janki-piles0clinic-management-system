<?php 
$activePage = 'medicine_issue';
include VIEWS_PATH . '/layout/reception_header.php'; 
?>

<!-- Dispatch Table -->
<div class="table-responsive border-0 shadow-sm rounded-3">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light text-slate">
            <tr>
                <th>Prescribed On</th>
                <th>Patient Details</th>
                <th>Prescribed Doctor</th>
                <th>Medicine Detail</th>
                <th>Dosage & Frequency</th>
                <th>Duration</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pending)): ?>
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-capsule-pill fs-3 d-block mb-2"></i>
                        No prescriptions currently pending medicine dispatches.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($pending as $row): ?>
                    <tr>
                        <td class="small text-muted"><?= esc(date('Y-m-d h:i A', strtotime($row['prescribed_at']))) ?></td>
                        <td>
                            <div class="fw-bold text-slate"><?= esc($row['patient_name']) ?></div>
                            <span class="text-muted small" style="font-size: 0.78rem;">ID: <?= esc($row['patient_code']) ?></span>
                        </td>
                        <td class="fw-semibold text-slate">Dr. <?= esc($row['doctor_name']) ?></td>
                        <td class="fw-bold text-slate"><i class="bi bi-prescription text-danger me-1"></i> <?= esc($row['medicine_name']) ?></td>
                        <td>
                            <div class="small fw-semibold"><?= esc($row['dosage']) ?></div>
                            <span class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-repeat me-1"></i> Frequency: <?= esc($row['frequency']) ?></span>
                        </td>
                        <td class="small"><?= esc($row['duration']) ?></td>
                        <td class="text-end">
                            <a href="<?= site_url('/reception/medicine-issue/dispatch/' . $row['id']) ?>" class="btn btn-sm btn-success px-3 py-1 shadow-sm">
                                <i class="bi bi-box-arrow-right me-1"></i> Issue
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include VIEWS_PATH . '/layout/reception_footer.php'; ?>
