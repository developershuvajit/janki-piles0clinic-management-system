<?php 
$activePage = 'doctor_patients';
include VIEWS_PATH . '/layout/doctor_header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-slate mb-1">Patient Medical Records Directory</h4>
        <p class="text-muted small mb-0">Search and review patient medical histories and previous prescriptions</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Patient Code</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Gender / Age</th>
                        <th>Blood Group</th>
                        <th>Registered Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($patients)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No patient records found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($patients as $p): ?>
                            <tr>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary fw-bold"><?= esc($p['patient_id']) ?></span></td>
                                <td>
                                    <div class="fw-bold text-slate"><?= esc($p['name']) ?></div>
                                    <div class="small text-muted"><?= esc($p['email'] ?? 'No email') ?></div>
                                </td>
                                <td><?= esc($p['phone']) ?></td>
                                <td><?= ucfirst(esc($p['gender'])) ?> (<?= date_diff(date_create($p['dob']), date_create('today'))->y ?> yrs)</td>
                                <td><span class="badge bg-danger bg-opacity-10 text-danger"><?= esc($p['blood_group'] ?: 'N/A') ?></span></td>
                                <td class="small text-muted"><?= date('d M Y', strtotime($p['created_at'])) ?></td>
                                <td class="text-end">
                                    <a href="<?= site_url('/doctor/patients/history/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="bi bi-clock-history me-1"></i> Medical History
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
