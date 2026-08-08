<?php 
$activePage = 'doctor_dashboard';
include VIEWS_PATH . '/layout/doctor_header.php'; 
?>

<!-- Action Row -->
<div class="row mb-4">
    <!-- Active Patients Today -->
    <div class="col-md-4 mb-3">
        <div class="card p-3 border-0 shadow-sm bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <h6 class="text-muted text-uppercase mb-1 small fw-bold">Queue Waiting</h6>
                <h3 class="mb-0 fw-bold text-slate"><?= esc((string)$pending_today) ?></h3>
            </div>
            <div class="bg-warning bg-opacity-10 p-3 rounded text-warning fs-4">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
    </div>

    <!-- Completed Today -->
    <div class="col-md-4 mb-3">
        <div class="card p-3 border-0 shadow-sm bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <h6 class="text-muted text-uppercase mb-1 small fw-bold">Completed Consultations</h6>
                <h3 class="mb-0 fw-bold text-slate"><?= esc((string)$completed_today) ?></h3>
            </div>
            <div class="bg-success bg-opacity-10 p-3 rounded text-success fs-4">
                <i class="bi bi-check-circle-fill"></i>
            </div>
        </div>
    </div>

    <!-- Certificates Issued Shortcut -->
    <div class="col-md-4 mb-3">
        <div class="card p-3 border-0 shadow-sm bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <h6 class="text-muted text-uppercase mb-1 small fw-bold">Leave Certificates</h6>
                <a href="<?= site_url('/admin/doctor/certificates') ?>" class="btn btn-outline-primary btn-sm mt-1">
                    <i class="bi bi-file-earmark-medical me-1"></i> View Issued Certificates
                </a>
            </div>
            <div class="bg-primary bg-opacity-10 p-3 rounded text-primary fs-4">
                <i class="bi bi-file-earmark-medical-fill"></i>
            </div>
        </div>
    </div>
</div>

<!-- Queue List Directory -->
<div class="card border-0 shadow-sm p-4">
    <h5 class="fw-bold text-slate mb-3"><i class="bi bi-play-circle text-success me-2"></i>My Consultation Queue Board</h5>
    
    <div class="table-responsive border-0 shadow-none">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Token</th>
                    <th>Patient Name</th>
                    <th>Gender & Age</th>
                    <th>Scheduled Slot</th>
                    <th>Queue Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($queue)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-person-fill-slash d-block fs-3 mb-1"></i>
                            Your consultation queue is empty for today.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($queue as $q): ?>
                        <tr>
                            <td class="fw-bold text-success fs-4">#<?= esc((string)$q['token_number']) ?></td>
                            <td>
                                <div class="fw-bold text-slate"><?= esc($q['patient_name']) ?></div>
                                <span class="text-muted small" style="font-size: 0.78rem;">ID: <?= esc($q['patient_code']) ?></span>
                            </td>
                            <td class="small">
                                <?= esc(ucfirst($q['gender'])) ?> &bull; 
                                <?= esc(date('Y') - date('Y', strtotime($q['dob']))) ?> Yrs
                            </td>
                            <td class="small text-muted"><i class="bi bi-clock me-1"></i> <?= esc(date('h:i A', strtotime($q['time_slot']))) ?></td>
                            <td>
                                <?php if ($q['queue_status'] === 'in_consultation'): ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1.5 rounded">Active In Office</span>
                                <?php elseif ($q['queue_status'] === 'completed'): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1.5 rounded">Completed</span>
                                <?php else: ?>
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-1.5 rounded">Waiting</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if ($q['queue_status'] !== 'completed'): ?>
                                    <a href="<?= site_url('/admin/doctor/consult/' . $q['id']) ?>" class="btn btn-sm btn-primary px-3 py-1 shadow-sm">
                                        <i class="bi bi-activity me-1"></i> Consult Patient
                                    </a>
                                <?php else: ?>
                                    <span class="text-success small fw-semibold"><i class="bi bi-check-lg"></i> Done</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/doctor_footer.php'; ?>
