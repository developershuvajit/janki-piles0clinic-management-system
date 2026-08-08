<?php 
$activePage = 'doctor_prescriptions';
include VIEWS_PATH . '/layout/doctor_header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-slate mb-1">Physician Prescriptions Directory</h4>
        <p class="text-muted small mb-0">Review and print all outpatient prescriptions issued by you</p>
    </div>
    <a href="<?= site_url('/doctor/prescriptions/create') ?>" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> New Prescription
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Prescription #</th>
                        <th>Patient Name</th>
                        <th>Diagnosis</th>
                        <th>Follow-up Date</th>
                        <th>Date Written</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($prescriptions)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No prescriptions recorded yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($prescriptions as $p): ?>
                            <tr>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary">#RX-<?= esc((string)$p['id']) ?></span></td>
                                <td>
                                    <div class="fw-bold text-slate"><?= esc($p['patient_name']) ?></div>
                                    <div class="small text-muted">Code: <?= esc($p['patient_code'] ?? 'N/A') ?></div>
                                </td>
                                <td><?= esc($p['diagnosis']) ?></td>
                                <td><?= $p['follow_up_date'] ? date('d M Y', strtotime($p['follow_up_date'])) : 'None' ?></td>
                                <td class="small text-muted"><?= date('d M Y, h:i A', strtotime($p['created_at'])) ?></td>
                                <td class="text-end">
                                    <a href="<?= site_url('/doctor/prescriptions/print/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="bi bi-printer me-1"></i> Print RX
                                    </a>
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
