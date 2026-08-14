<?php 
$activePage = 'ipd';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Flash Messages -->
<?php if ($success = \App\Helpers\Session::getFlash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= esc($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($error = \App\Helpers\Session::getFlash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= esc($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4><i class="bi bi-hospital me-2"></i>Inpatient Department (IPD)</h4>
    <a href="<?= site_url('/admin/ipd/admit') ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Admit Patient
    </a>
</div>

<!-- Active Admissions -->
<div class="card border-0 shadow-sm p-4 mb-4">
    <h6 class="fw-bold text-slate mb-3"><i class="bi bi-person-lines-fill text-success me-2"></i>Active Admissions</h6>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Admission Date</th>
                    <th>Diagnosis</th>
                    <th>Branch</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($admissions)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No active admissions.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($admissions as $adm): ?>
                        <tr>
                            <td><?= $adm['id'] ?></td>
                            <td>
                                <strong><?= esc($adm['patient_name']) ?></strong>
                                <div class="small text-muted"><?= esc($adm['patient_code']) ?></div>
                            </td>
                            <td>Dr. <?= esc($adm['doctor_name']) ?></td>
                            <td><?= date('d M, Y h:i A', strtotime($adm['admission_date'])) ?></td>
                            <td><?= esc(substr($adm['diagnosis'], 0, 30)) ?>...</td>
                            <td><?= esc($adm['branch_name'] ?? 'Main') ?></td>
                            <td>
                                <span class="badge bg-success">Admitted</span>
                            </td>
                            <td>
                                <a href="<?= site_url('/admin/ipd/nursing-logs/' . $adm['id']) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-clipboard2-pulse"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Discharged History -->
<div class="card border-0 shadow-sm p-4">
    <h6 class="fw-bold text-slate mb-3"><i class="bi bi-clock-history text-muted me-2"></i>Discharged History</h6>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Admission Date</th>
                    <th>Discharge Date</th>
                    <th>Branch</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($discharged)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No discharge history.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($discharged as $dis): ?>
                        <tr>
                            <td><?= $dis['id'] ?></td>
                            <td>
                                <strong><?= esc($dis['patient_name']) ?></strong>
                                <div class="small text-muted"><?= esc($dis['patient_code']) ?></div>
                            </td>
                            <td>Dr. <?= esc($dis['doctor_name']) ?></td>
                            <td><?= date('d M, Y', strtotime($dis['admission_date'])) ?></td>
                            <td><?= $dis['discharge_date'] ? date('d M, Y', strtotime($dis['discharge_date'])) : '-' ?></td>
                            <td><?= esc($dis['branch_name'] ?? 'Main') ?></td>
                            <td>
                                <span class="badge bg-secondary">Discharged</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>