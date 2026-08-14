<?php 
$activePage = 'doctor_ipd';
include VIEWS_PATH . '/layout/doctor_header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-slate mb-1">Admitted IPD Patients</h4>
        <p class="text-muted small mb-0">Inpatient ward admissions under your clinical supervision</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Admission ID</th>
                        <th>Patient Name</th>
                        <th>Branch</th>
                        <th>Admission Date</th>
                        <th>Diagnosis</th>
                        <th>Status</th>
                        <th class="text-end">Clinical Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($admissions)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No admitted patients under your supervision.</td>
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
                                        <div class="small text-muted text-truncate" style="max-width:200px;"><?= esc($adm['symptoms']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($adm['discharge_approval'] === 'approved'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success">Approved</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning">Admitted</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?= site_url('/doctor/ipd/visit-notes/' . $adm['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill me-1" title="Daily Visit Notes & Vitals">
                                        <i class="bi bi-journal-medical"></i> Visit Notes
                                    </a>
                                    <a href="<?= site_url('/doctor/ipd/procedure-notes/' . $adm['id']) ?>" class="btn btn-sm btn-outline-info rounded-pill me-1" title="Procedure & Surgery Notes">
                                        <i class="bi bi-tools"></i> Procedures
                                    </a>
                                    <a href="<?= site_url('/doctor/discharge/summary/' . $adm['id']) ?>" class="btn btn-sm btn-success rounded-pill" title="Approve & Generate Discharge Summary">
                                        <i class="bi bi-box-arrow-right"></i> Discharge
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