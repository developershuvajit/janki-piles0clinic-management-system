<?php 
$activePage = 'reception_dashboard';
include VIEWS_PATH . '/layout/reception_header.php'; 
?>

<!-- Reception Quick Actions Command Center Bar -->
<div class="card border-0 shadow-sm p-3 mb-4 rounded-4 bg-white border-start border-4 border-emerald">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-lightning-charge-fill text-emerald fs-4"></i>
            <div>
                <h6 class="fw-bold text-slate mb-0">Reception Quick Actions</h6>
                <span class="text-muted small">Fast minimal-click desk operations</span>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= site_url('/reception/patients/create') ?>" class="btn btn-emerald btn-sm rounded-pill px-3 shadow-xs">
                <i class="bi bi-person-plus-fill me-1"></i> New Patient
            </a>
            <a href="<?= site_url('/reception/walk-in') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="bi bi-person-walking me-1"></i> Register Walk-In
            </a>
            <a href="<?= site_url('/reception/billing') ?>" class="btn btn-outline-success btn-sm rounded-pill px-3">
                <i class="bi bi-receipt me-1"></i> Cashier Billing
            </a>
            <a href="<?= site_url('/reception/followups') ?>" class="btn btn-outline-warning btn-sm rounded-pill px-3 text-dark">
                <i class="bi bi-calendar-check me-1"></i> Follow-ups
            </a>
            <a href="<?= site_url('/reception/communication') ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                <i class="bi bi-whatsapp me-1"></i> WhatsApp
            </a>
        </div>
    </div>
</div>

<!-- Key Performance Metrics Grid -->
<div class="row g-3 mb-4">
    <!-- Patients Registered Today -->
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm rounded-4 bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <h6 class="text-muted text-uppercase mb-1 small fw-bold">Today's Patients</h6>
                <h3 class="mb-0 fw-bold text-slate"><?= esc((string)$patients_today) ?></h3>
            </div>
            <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success fs-4">
                <i class="bi bi-person-fill-check"></i>
            </div>
        </div>
    </div>

    <!-- Appointments Today -->
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm rounded-4 bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <h6 class="text-muted text-uppercase mb-1 small fw-bold">Appointments</h6>
                <h3 class="mb-0 fw-bold text-slate"><?= esc((string)$appointments_today) ?></h3>
            </div>
            <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary fs-4">
                <i class="bi bi-calendar2-check-fill"></i>
            </div>
        </div>
    </div>

    <!-- Pending Medicines -->
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm rounded-4 bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <h6 class="text-muted text-uppercase mb-1 small fw-bold">Pending Meds</h6>
                <h3 class="mb-0 fw-bold text-slate"><?= esc((string)$pending_dispatches) ?></h3>
            </div>
            <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning fs-4">
                <i class="bi bi-capsule"></i>
            </div>
        </div>
    </div>

    <!-- Today's Collection -->
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm rounded-4 bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <h6 class="text-muted text-uppercase mb-1 small fw-bold">Today's Revenue</h6>
                <h3 class="mb-0 fw-bold text-success">₹<?= esc(number_format($revenue_today, 2)) ?></h3>
            </div>
            <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info fs-4">
                <i class="bi bi-wallet2"></i>
            </div>
        </div>
    </div>
</div>

<!-- Active OPD Token Queue Board -->
<div class="card border-0 shadow-sm p-4 rounded-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-slate mb-0">
            <i class="bi bi-play-circle-fill text-success me-2"></i>Active Patient OPD Consultation Queue
        </h5>
        <a href="<?= site_url('/reception/queues') ?>" class="btn btn-outline-success btn-sm rounded-pill px-3">
            <i class="bi bi-list-ol me-1"></i> Full Queue Board
        </a>
    </div>
    
    <div class="table-responsive border-0 shadow-none">
        <table class="table table-hover align-middle mb-0" style="font-size:0.88rem;">
            <thead class="bg-light">
                <tr>
                    <th>Token</th>
                    <th>Patient Name</th>
                    <th>Doctor</th>
                    <th>Queue Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($active_queue)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No active OPD consultation tokens at this time.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($active_queue as $q): ?>
                        <tr>
                            <td class="fw-bold text-success fs-5">#<?= esc((string)$q['token_number']) ?></td>
                            <td class="fw-semibold text-slate"><?= esc($q['patient_name']) ?></td>
                            <td>Dr. <?= esc($q['doctor_name']) ?></td>
                            <td>
                                <?php if ($q['queue_status'] === 'in_consultation'): ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded">In consultation room</span>
                                <?php else: ?>
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 rounded">Waiting</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= site_url('/reception/queues/update/' . $q['id'] . '?status=in_consultation') ?>" class="btn btn-sm btn-outline-primary px-2 py-1" style="font-size:0.75rem;">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Call In
                                </a>
                                <a href="<?= site_url('/reception/queues/update/' . $q['id'] . '?status=completed') ?>" class="btn btn-sm btn-outline-success px-2 py-1" style="font-size:0.75rem;">
                                    <i class="bi bi-check-circle me-1"></i> Complete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/reception_footer.php'; ?>
