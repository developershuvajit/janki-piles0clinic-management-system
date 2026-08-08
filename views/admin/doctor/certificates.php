<?php 
$activePage = 'certificates';
include VIEWS_PATH . '/layout/doctor_header.php'; 
?>

<!-- Action Links Row -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0 small">List of generated medical certificates and sick leave records.</p>
    <a href="<?= site_url('/admin/doctor/certificates/create') ?>" class="btn btn-primary btn-sm px-3 shadow-sm">
        <i class="bi bi-plus-circle me-1"></i> Issue Certificate
    </a>
</div>

<!-- Table list -->
<div class="table-responsive border-0 shadow-sm rounded-3">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light text-slate">
            <tr>
                <th>Certificate #</th>
                <th>Patient Details</th>
                <th>Diagnosis</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Reason</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($certificates)): ?>
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-file-earmark-medical fs-3 d-block mb-2"></i>
                        No medical certificates issued yet.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($certificates as $cert): ?>
                    <tr>
                        <td class="fw-bold text-slate">MC-<?= esc(sprintf("%04d", $cert['id'])) ?></td>
                        <td>
                            <div class="fw-bold text-slate"><?= esc($cert['patient_name']) ?></div>
                            <span class="text-muted small" style="font-size: 0.78rem;">ID: <?= esc($cert['patient_code']) ?></span>
                        </td>
                        <td class="small"><?= esc($cert['diagnosis']) ?></td>
                        <td class="small"><?= esc($cert['start_date']) ?></td>
                        <td class="small"><?= esc($cert['end_date']) ?></td>
                        <td class="small text-muted text-truncate" style="max-width: 150px;"><?= esc($cert['reason']) ?></td>
                        <td class="text-end">
                            <a href="<?= site_url('/admin/doctor/certificates/print/' . $cert['id']) ?>" class="btn btn-sm btn-outline-primary px-3 py-1 shadow-sm">
                                <i class="bi bi-printer me-1"></i> Print
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include VIEWS_PATH . '/layout/doctor_footer.php'; ?>
