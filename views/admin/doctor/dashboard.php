<?php
$activePage = 'doctor_dashboard';
include VIEWS_PATH . '/layout/doctor_header.php';
?>
<style>
/* ===== STAT CARDS ===== */
.stat-card{background:#fff;border-radius:14px;padding:1rem 1.2rem;box-shadow:0 2px 8px rgba(0,0,0,.04);display:flex;align-items:center;gap:1rem;border:1px solid #f0f2f5;transition:.15s;cursor:pointer;text-decoration:none;color:inherit}
.stat-card:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(0,0,0,.06);border-color:#2563eb}
.stat-card:active{transform:scale(0.97)}
.stat-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0}
.stat-icon.warning{background:#fef7e8;color:#c5711e}
.stat-icon.success{background:#e6f5ed;color:#0f7b4a}
.stat-icon.primary{background:#e6f0ff;color:#1a6bc4}
.stat-icon.purple{background:#f0edff;color:#6d4fc9}
.stat-icon.info{background:#e6f7fe;color:#0e7c9e}
.stat-icon.danger{background:#fde8e8;color:#b13b3b}
.stat-label{font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.03em;color:#6b7a8f}
.stat-value{font-size:1.7rem;font-weight:700;line-height:1.1;color:#0b1a2b}
.stat-sub{font-size:.65rem;color:#94a3b8;margin-top:1px}

/* ===== CLEAN CARDS ===== */
.card-clean{background:#fff;border-radius:14px;box-shadow:0 2px 8px rgba(0,0,0,.04);border:1px solid #f0f2f5;padding:1.2rem}

/* ===== SOFT BUTTONS ===== */
.btn-soft{border-radius:40px;padding:.3rem 1rem;font-size:.78rem;border:1px solid #e8ecf0;background:transparent;color:#1e293b;display:inline-flex;align-items:center;gap:6px;transition:.1s;text-decoration:none}
.btn-soft:hover{background:#f5f7fa;border-color:#d0d5dd;color:#0b1a2b}
.btn-soft-primary{border-color:#d0e2ff;color:#1a6bc4}
.btn-soft-primary:hover{background:#e6f0ff;border-color:#93b9f8}
.btn-soft-success{border-color:#b8e0cf;color:#059669}
.btn-soft-success:hover{background:#e6f5ed;border-color:#6bc2a3}
.btn-soft-danger{border-color:#fad5d5;color:#b13b3b}
.btn-soft-danger:hover{background:#fef0f0;border-color:#f5baba}
.btn-soft-warning{border-color:#fde8b3;color:#c5711e}
.btn-soft-warning:hover{background:#fef7e8;border-color:#fad48a}

/* ===== BADGES ===== */
.badge-soft{background:#f1f4f8;color:#1e293b;padding:.15rem .8rem;border-radius:40px;font-size:.7rem}
.badge-soft-waiting{background:#fef7e8;color:#c5711e}
.badge-soft-consult{background:#fde8e8;color:#b13b3b}
.badge-soft-completed{background:#e6f5ed;color:#0f7b4a}
.badge-soft-primary{background:#e6f0ff;color:#1a6bc4}
.badge-soft-success{background:#e6f5ed;color:#0f7b4a}
.badge-soft-danger{background:#fde8e8;color:#b13b3b}
.badge-soft-warning{background:#fef7e8;color:#c5711e}
.badge-soft-info{background:#e6f7fe;color:#0e7c9e}

/* ===== EMPTY STATE ===== */
.empty-clean{padding:2rem 1rem;text-align:center;color:#94a3b8}
.empty-clean i{font-size:2.2rem;opacity:.3;display:block;margin-bottom:.3rem}

/* ===== CLEAN TABLE ===== */
.table-clean{font-size:.82rem;margin-bottom:0}
.table-clean th{font-size:.6rem;text-transform:uppercase;color:#6b7a8f;font-weight:600;padding:.4rem .6rem;border-bottom:1px solid #edf2f7;background:transparent}
.table-clean td{padding:.4rem .6rem;border-bottom:1px solid #f1f5f9;vertical-align:middle}
.table-clean tbody tr:last-child td{border-bottom:none}
.table-clean tbody tr:hover td{background:#fafcfe}

/* ===== QUICK GRID ===== */
.doctor-quick-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:.5rem}
.doctor-quick-item{display:flex;align-items:center;gap:.5rem;padding:.4rem .8rem;border-radius:10px;border:1px solid #e8ecf0;text-decoration:none;color:#1e293b;font-size:.78rem;transition:.15s}
.doctor-quick-item:hover{background:#f8fafc;border-color:#cbd5e1;color:#0b1a2b;transform:translateY(-1px)}
.doctor-quick-item i{font-size:1rem;width:1.2rem;text-align:center}

/* ===== PULSE ANIMATION ===== */
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}

/* ===== RESPONSIVE ===== */
@media(max-width:768px){
.stat-value{font-size:1.3rem}
.stat-card{padding:.8rem 1rem;gap:.6rem}
.stat-icon{width:38px;height:38px;font-size:1.2rem}
.card-clean{padding:.8rem}
.doctor-quick-grid{grid-template-columns:1fr 1fr}
}
@media(max-width:576px){
.stat-card{flex-direction:column;text-align:center;gap:.2rem}
.stat-value{font-size:1.2rem}
.doctor-quick-grid{grid-template-columns:1fr}
.btn-soft{padding:.2rem .7rem;font-size:.7rem}
.table-clean{font-size:.72rem}
.table-clean th,.table-clean td{padding:.3rem .4rem}
.badge-soft{font-size:.6rem;padding:.05rem .6rem}
}
</style>
<!-- Action Row -->
<div class="row g-3 mt-4">
    <!-- Queue Waiting -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <a href="<?= site_url('/doctor/opd') ?>" class="stat-card">
            <div class="stat-icon warning"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="stat-label">Queue Waiting</div>
                <div class="stat-value"><?= esc((string)($pending_today ?? 0)) ?></div>
                <div class="stat-sub">Patients in queue</div>
            </div>
        </a>
    </div>

    <!-- Completed Consultations -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <a href="<?= site_url('/doctor/opd?status=completed') ?>" class="stat-card">
            <div class="stat-icon success"><i class="bi bi-check-circle-fill"></i></div>
            <div>
                <div class="stat-label">Completed</div>
                <div class="stat-value"><?= esc((string)($completed_today ?? 0)) ?></div>
                <div class="stat-sub">Consultations done</div>
            </div>
        </a>
    </div>

    <!-- Total Patients -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <a href="<?= site_url('/doctor/patients') ?>" class="stat-card">
            <div class="stat-icon primary"><i class="bi bi-person-bounding-box"></i></div>
            <div>
                <div class="stat-label">Total Patients</div>
                <div class="stat-value"><?= esc((string)($total_patients ?? 0)) ?></div>
                <div class="stat-sub">Registered under care</div>
            </div>
        </a>
    </div>

    <!-- IPD Admissions -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <a href="<?= site_url('/doctor/ipd') ?>" class="stat-card">
            <div class="stat-icon purple"><i class="bi bi-hospital-fill"></i></div>
            <div>
                <div class="stat-label">IPD Admissions</div>
                <div class="stat-value"><?= esc((string)($ipd_patients ?? 0)) ?></div>
                <div class="stat-sub">Under care</div>
            </div>
        </a>
    </div>
</div>

<!-- Queue Board -->
<div class="card-clean mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <span style="font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#6b7a8f;">
                <i class="bi bi-play-circle-fill text-success me-1"></i> My Consultation Queue Board
            </span>
            <div style="font-size:.7rem;color:#94a3b8;"><?= !empty($queue) ? count($queue) . ' patients waiting' : 'Queue is empty' ?></div>
        </div>
        <a href="<?= site_url('/doctor/opd') ?>" class="btn-soft" style="font-size:.7rem;">Manage Queue <i class="bi bi-arrow-right ms-1"></i></a>
    </div>

    <?php if (empty($queue)): ?>
        <div class="empty-clean"><i class="bi bi-person-fill-slash"></i><div>Your consultation queue is empty</div><div style="font-size:.75rem">Patients will appear here when assigned</div></div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-clean">
            <thead>
                <tr>
                    <th style="width:60px;">Token</th>
                    <th>Patient Name</th>
                    <th>Gender &amp; Age</th>
                    <th style="width:90px;">Slot</th>
                    <th style="width:110px;">Status</th>
                    <th style="width:120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($queue as $q): ?>
                    <tr>
                        <td class="fw-bold" style="font-size:.9rem;color:#0f7b4a;">#<?= esc((string)($q['token_number'] ?? 0)) ?></td>
                        <td>
                            <div class="fw-bold" style="color:#0b1a2b;font-size:.82rem;"><?= esc($q['patient_name'] ?? 'Unknown') ?></div>
                            <span class="text-muted" style="font-size:.65rem;">ID: <?= esc($q['patient_code'] ?? 'N/A') ?></span>
                        </td>
                        <td style="font-size:.75rem;color:#475569;">
                            <?= esc(ucfirst($q['gender'] ?? 'N/A')) ?> &bull; 
                            <?php 
                                $age = 'N/A';
                                if (!empty($q['dob'])) {
                                    try {
                                        $dob = new DateTime($q['dob']);
                                        $now = new DateTime();
                                        $age = $now->diff($dob)->y . ' Yrs';
                                    } catch (Exception $e) {
                                        $age = 'N/A';
                                    }
                                }
                                echo esc($age);
                            ?>
                        </td>
                        <td style="font-size:.7rem;color:#64748b;">
                            <i class="bi bi-clock me-1"></i> 
                            <?= !empty($q['time_slot']) ? esc(date('h:i A', strtotime($q['time_slot']))) : '—' ?>
                        </td>
                        <td>
                            <?php 
                                $status = $q['queue_status'] ?? 'waiting';
                                if ($status === 'in_consultation'): ?>
                                <span class="badge-soft badge-soft-consult">In Consultation</span>
                            <?php elseif ($status === 'completed'): ?>
                                <span class="badge-soft badge-soft-completed">Completed</span>
                            <?php else: ?>
                                <span class="badge-soft badge-soft-waiting">Waiting</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($status !== 'completed'): ?>
                                <a href="<?= site_url('/doctor/consult/' . ($q['id'] ?? 0)) ?>" class="btn-soft btn-soft-primary" style="font-size:.7rem;padding:.15rem .7rem;">
                                    <i class="bi bi-activity me-1"></i> Consult
                                </a>
                            <?php else: ?>
                                <span class="text-success" style="font-size:.7rem;font-weight:600;"><i class="bi bi-check-lg"></i> Done</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
 

<?php include VIEWS_PATH . '/layout/doctor_footer.php'; ?>