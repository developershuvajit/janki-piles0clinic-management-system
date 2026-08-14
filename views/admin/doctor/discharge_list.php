<?php 
$activePage = 'doctor_discharge';
include VIEWS_PATH . '/layout/doctor_header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-slate mb-1">IPD Patient Discharge Approvals</h4>
        <p class="text-muted small mb-0">Approve patient discharges and generate final medical discharge summaries</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Admission #</th>
                        <th>Patient Name</th>
                        <th>Branch</th>
                        <th>Admit Date</th>
                        <th>Diagnosis</th>
                        <th>Discharge Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($admissions)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No admitted patients available for discharge approval.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($admissions as $adm): ?>
                            <tr>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary">#ADM-<?= esc((string)$adm['id']) ?></span></td>
                                <td>
                                    <div class="fw-bold text-slate"><?= esc($adm['patient_name']) ?></div>
                                    <div class="small text-muted">Code: <?= esc($adm['patient_code']) ?></div>
                                </td>
                                <td><?= esc($adm['branch_name'] ?? 'Main Branch') ?></td>
                                <td class="small text-muted"><?= date('d M Y, h:i A', strtotime($adm['admission_date'])) ?></td>
                                <td>
                                    <div class="small fw-bold"><?= esc($adm['diagnosis']) ?></div>
                                    <?php if (!empty($adm['symptoms'])): ?>
                                        <div class="small text-muted text-truncate" style="max-width:150px;"><?= esc($adm['symptoms']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($adm['discharge_approval'] === 'approved'): ?>
                                        <span class="badge bg-success px-2.5 py-1">Doctor Approved</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark px-2.5 py-1">Pending Approval</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($adm['discharge_approval'] !== 'approved'): ?>
                                        <a href="<?= site_url('/doctor/discharge/approve/' . $adm['id']) ?>" class="btn btn-sm btn-success rounded-pill px-3 me-1" onclick="return confirm('Approve discharge for this patient?');">
                                            <i class="bi bi-check-lg me-1"></i> Approve
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= site_url('/doctor/discharge/summary/' . $adm['id']) ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                                        <i class="bi bi-file-earmark-medical me-1"></i> Summary
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