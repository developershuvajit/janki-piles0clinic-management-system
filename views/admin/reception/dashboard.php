<?php
$activePage = 'reception_dashboard';
include VIEWS_PATH . '/layout/reception_header.php';
?>

<!-- ============================================
     PAGE CSS
     ============================================ -->
<style>
/* Quick Actions Bar */
.border-emerald { border-color: #0f7b4a !important; }
.text-emerald { color: #0f7b4a !important; }
.btn-emerald { background: #0f7b4a; border-color: #0f7b4a; color: #fff; }
.btn-emerald:hover { background: #0a5d38; border-color: #0a5d38; color: #fff; }
.shadow-xs { box-shadow: 0 1px 3px rgba(0,0,0,.06); }
.text-slate { color: #0b1a2b; }
.rounded-4 { border-radius: 16px !important; }

/* Stat Cards */
.stat-card {
    background: #fff;
    border-radius: 14px;
    padding: 1rem 1.2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
    display: flex;
    align-items: center;
    gap: 1rem;
    border: 1px solid #f0f2f5;
    transition: .15s;
    text-decoration: none;
    color: inherit;
}
.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,.06);
    border-color: #2563eb;
}
.stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}
.stat-icon.success { background: #e6f5ed; color: #0f7b4a; }
.stat-icon.primary { background: #e6f0ff; color: #1a6bc4; }
.stat-icon.warning { background: #fef7e8; color: #c5711e; }
.stat-icon.info { background: #e6f7fe; color: #0e7c9e; }
.stat-label { font-size: .65rem; font-weight: 600; text-transform: uppercase; letter-spacing: .03em; color: #6b7a8f; }
.stat-value { font-size: 1.7rem; font-weight: 700; line-height: 1.1; color: #0b1a2b; }
.stat-sub { font-size: .65rem; color: #94a3b8; }

/* Table */
.table-clean {
    font-size: .82rem;
    margin-bottom: 0;
}
.table-clean th {
    font-size: .6rem;
    text-transform: uppercase;
    color: #6b7a8f;
    font-weight: 600;
    padding: .4rem .6rem;
    border-bottom: 1px solid #edf2f7;
    background: #f8fafc;
}
.table-clean td {
    padding: .4rem .6rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}
.table-clean tbody tr:hover td { background: #fafcfe; }

/* Badges */
.badge-soft {
    padding: .15rem .8rem;
    border-radius: 40px;
    font-size: .7rem;
    font-weight: 500;
}
.badge-soft-waiting { background: #fef7e8; color: #c5711e; }
.badge-soft-consult { background: #fde8e8; color: #b13b3b; }
.badge-soft-completed { background: #e6f5ed; color: #0f7b4a; }

/* Empty State */
.empty-clean {
    padding: 2rem 1rem;
    text-align: center;
    color: #94a3b8;
}
.empty-clean i {
    font-size: 2.2rem;
    opacity: .3;
    display: block;
    margin-bottom: .3rem;
}

/* Responsive */
@media(max-width:768px) {
    .stat-value { font-size: 1.3rem; }
    .stat-card { padding: .8rem 1rem; gap: .6rem; }
    .stat-icon { width: 38px; height: 38px; font-size: 1.2rem; }
}
@media(max-width:576px) {
    .stat-card { flex-direction: column; text-align: center; gap: .2rem; }
    .stat-value { font-size: 1.2rem; }
    .table-clean { font-size: .72rem; }
    .table-clean th, .table-clean td { padding: .3rem .4rem; }
}
</style>

<!-- ============================================
     QUICK ACTIONS BAR
     ============================================ -->
<div class="card border-0 shadow-sm p-3 mt-4 rounded-4 bg-white border-start border-4 border-emerald">
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
                <i class="bi bi-person-walking me-1"></i> Walk-In
            </a>
            <a href="<?= site_url('/reception/billing') ?>" class="btn btn-outline-success btn-sm rounded-pill px-3">
                <i class="bi bi-receipt me-1"></i> Billing
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

<!-- ============================================
     KEY PERFORMANCE METRICS
     ============================================ -->
<div class="row g-3 mt-3">
    <div class="col-md-3">
        <a href="<?= site_url('/reception/patients') ?>" class="stat-card">
            <div class="stat-icon success"><i class="bi bi-person-fill-check"></i></div>
            <div>
                <div class="stat-label">Today's Patients</div>
                <div class="stat-value"><?= esc((string)($patients_today ?? 0)) ?></div>
                <div class="stat-sub">Registered</div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="<?= site_url('/reception/queues') ?>" class="stat-card">
            <div class="stat-icon primary"><i class="bi bi-calendar2-check-fill"></i></div>
            <div>
                <div class="stat-label">Appointments</div>
                <div class="stat-value"><?= esc((string)($appointments_today ?? 0)) ?></div>
                <div class="stat-sub">Today</div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="<?= site_url('/reception/medicine-issue') ?>" class="stat-card">
            <div class="stat-icon warning"><i class="bi bi-capsule"></i></div>
            <div>
                <div class="stat-label">Pending Meds</div>
                <div class="stat-value"><?= esc((string)($pending_dispatches ?? 0)) ?></div>
                <div class="stat-sub">To Dispatch</div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="<?= site_url('/reception/billing') ?>" class="stat-card">
            <div class="stat-icon info"><i class="bi bi-wallet2"></i></div>
            <div>
                <div class="stat-label">Today's Revenue</div>
                <div class="stat-value">₹<?= esc(number_format($revenue_today ?? 0, 2)) ?></div>
                <div class="stat-sub">Collected</div>
            </div>
        </a>
    </div>
</div>

<!-- ============================================
     ACTIVE OPD QUEUE BOARD
     ============================================ -->
<div class="card border-0 shadow-sm p-4 rounded-4 mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-slate mb-0">
            <i class="bi bi-play-circle-fill text-success me-2"></i>Active OPD Consultation Queue
        </h5>
        <a href="<?= site_url('/reception/queues') ?>" class="btn btn-outline-success btn-sm rounded-pill px-3">
            <i class="bi bi-list-ol me-1"></i> Full Queue
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-clean">
            <thead>
                <tr>
                    <th style="width:60px;">Token</th>
                    <th>Patient Name</th>
                    <th>Doctor</th>
                    <th style="width:110px;">Status</th>
                    <th style="width:130px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($active_queue)): ?>
                    <tr>
                        <td colspan="5" class="empty-clean">
                            <i class="bi bi-person-fill-exclamation"></i>
                            <div>No active tokens</div>
                            <div style="font-size:.75rem">Queue is empty</div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($active_queue as $q): ?>
                        <tr>
                            <td class="fw-bold" style="font-size:.9rem;color:#0f7b4a;">#<?= esc((string)($q['token_number'] ?? 0)) ?></td>
                            <td class="fw-semibold text-slate"><?= esc($q['patient_name'] ?? 'Unknown') ?></td>
                            <td style="font-size:.78rem;">Dr. <?= esc($q['doctor_name'] ?? 'N/A') ?></td>
                            <td>
                                <?php if (($q['queue_status'] ?? 'waiting') === 'in_consultation'): ?>
                                    <span class="badge-soft badge-soft-consult">In Consultation</span>
                                <?php else: ?>
                                    <span class="badge-soft badge-soft-waiting">Waiting</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= site_url('/reception/queues/update/' . ($q['id'] ?? 0) . '?status=in_consultation') ?>" class="btn btn-sm btn-outline-primary px-2 py-1" style="font-size:0.7rem;border-radius:40px;">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Call In
                                </a>
                                <a href="<?= site_url('/reception/queues/update/' . ($q['id'] ?? 0) . '?status=completed') ?>" class="btn btn-sm btn-outline-success px-2 py-1" style="font-size:0.7rem;border-radius:40px;">
                                    <i class="bi bi-check-circle me-1"></i> Done
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