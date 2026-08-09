<?php 
$activePage = 'reception_dashboard';
include VIEWS_PATH . '/layout/reception_header.php'; 
?>

<style>
.stat-card{background:#fff;border-radius:14px;padding:1rem 1.2rem;box-shadow:0 2px 8px rgba(0,0,0,.04);display:flex;align-items:center;gap:1rem;border:1px solid #f0f2f5;transition:.15s;cursor:pointer;text-decoration:none;color:inherit}
.stat-card:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(0,0,0,.06);border-color:#2563eb}
.stat-card:active{transform:scale(0.97)}
.stat-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0}
.stat-icon.green{background:#e6f5ed;color:#0f7b4a}
.stat-icon.blue{background:#e6f0ff;color:#1a6bc4}
.stat-icon.orange{background:#fff1e0;color:#c5711e}
.stat-icon.emerald{background:#e6f5ed;color:#059669}
.stat-icon.purple{background:#f0edff;color:#6d4fc9}
.stat-label{font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.03em;color:#6b7a8f}
.stat-value{font-size:1.7rem;font-weight:700;line-height:1.1;color:#0b1a2b}
.stat-sub{font-size:.65rem;color:#94a3b8;margin-top:1px}
.card-clean{background:#fff;border-radius:14px;box-shadow:0 2px 8px rgba(0,0,0,.04);border:1px solid #f0f2f5;padding:1.2rem}
.btn-soft{border-radius:40px;padding:.3rem 1rem;font-size:.78rem;border:1px solid #e8ecf0;background:transparent;color:#1e293b;display:inline-flex;align-items:center;gap:6px;transition:.1s;text-decoration:none}
.btn-soft:hover{background:#f5f7fa;border-color:#d0d5dd;color:#0b1a2b}
.btn-soft-primary{border-color:#d0e2ff;color:#1a6bc4}
.btn-soft-primary:hover{background:#e6f0ff;border-color:#93b9f8}
.btn-soft-success{border-color:#b8e0cf;color:#059669}
.btn-soft-success:hover{background:#e6f5ed;border-color:#6bc2a3}
.btn-soft-danger{border-color:#fad5d5;color:#b13b3b}
.btn-soft-danger:hover{background:#fef0f0;border-color:#f5baba}
.badge-soft{background:#f1f4f8;color:#1e293b;padding:.15rem .8rem;border-radius:40px;font-size:.7rem}
.badge-soft-waiting{background:#fef7e8;color:#c5711e}
.badge-soft-consult{background:#fde8e8;color:#b13b3b}
.badge-soft-completed{background:#e6f5ed;color:#0f7b4a}
.empty-clean{padding:2rem 1rem;text-align:center;color:#94a3b8}
.empty-clean i{font-size:2.2rem;opacity:.3;display:block;margin-bottom:.3rem}
.table-clean{font-size:.82rem;margin-bottom:0}
.table-clean th{font-size:.6rem;text-transform:uppercase;color:#6b7a8f;font-weight:600;padding:.4rem .6rem;border-bottom:1px solid #edf2f7;background:transparent}
.table-clean td{padding:.4rem .6rem;border-bottom:1px solid #f1f5f9;vertical-align:middle}
.table-clean tbody tr:last-child td{border-bottom:none}
.table-clean tbody tr:hover td{background:#fafcfe}
.activity-item{display:flex;align-items:flex-start;gap:.8rem;padding:.6rem 0;border-bottom:1px solid #f1f5f9}
.activity-item:last-child{border-bottom:none}
.activity-icon{width:32px;height:32px;border-radius:50%;background:#f1f4f8;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.9rem;color:#6b7a8f}
.activity-content{flex:1;min-width:0}
.activity-content .title{font-size:.8rem;color:#0b1a2b;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.activity-content .time{font-size:.65rem;color:#94a3b8}
.activity-badge{font-size:.6rem;padding:.05rem .6rem;border-radius:40px;background:#f1f4f8;color:#6b7a8f;white-space:nowrap}
.activity-badge.patient{background:#e6f5ed;color:#0f7b4a}
.activity-badge.appointment{background:#e6f0ff;color:#1a6bc4}
.activity-badge.billing{background:#fff1e0;color:#c5711e}
.activity-badge.lead{background:#f0edff;color:#6d4fc9}
.quick-link-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:.5rem}
.quick-link-item{display:flex;align-items:center;gap:.5rem;padding:.4rem .8rem;border-radius:10px;border:1px solid #e8ecf0;text-decoration:none;color:#1e293b;font-size:.78rem;transition:.15s}
.quick-link-item:hover{background:#f8fafc;border-color:#cbd5e1;color:#0b1a2b;transform:translateY(-1px)}
.quick-link-item i{font-size:1rem;width:1.2rem;text-align:center}

/* Responsive */
@media(max-width:768px){
.stat-value{font-size:1.3rem}
.stat-card{padding:.8rem 1rem;gap:.6rem}
.stat-icon{width:38px;height:38px;font-size:1.2rem}
.card-clean{padding:.8rem}
.quick-link-grid{grid-template-columns:1fr 1fr}
}
@media(max-width:576px){
.stat-card{flex-direction:column;text-align:center;gap:.2rem}
.stat-value{font-size:1.2rem}
.quick-link-grid{grid-template-columns:1fr}
.btn-soft{padding:.2rem .7rem;font-size:.7rem}
.table-clean{font-size:.72rem}
.table-clean th,.table-clean td{padding:.3rem .4rem}
}
</style>

<!-- ===== QUICK ACTIONS BAR ===== -->
<div class="card-clean mb-4" style="padding:1rem 1.2rem;">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-lightning-charge-fill text-primary fs-4"></i>
            <div>
                <h6 class="fw-bold mb-0" style="color:#0b1a2b;font-size:.95rem;">Reception Quick Actions</h6>
                <span class="text-muted" style="font-size:.7rem;">Fast minimal-click desk operations</span>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= site_url('/reception/patients/create') ?>" class="btn-soft btn-soft-success"><i class="bi bi-person-plus-fill"></i> New Patient</a>
            <a href="<?= site_url('/reception/walk-in') ?>" class="btn-soft"><i class="bi bi-person-walking"></i> Walk-In</a>
            <a href="<?= site_url('/reception/billing') ?>" class="btn-soft btn-soft-primary"><i class="bi bi-receipt"></i> Billing</a>
            <a href="<?= site_url('/reception/followups') ?>" class="btn-soft"><i class="bi bi-calendar-check"></i> Follow-ups</a>
        </div>
    </div>
</div>

<!-- ===== STATS - 4 CARDS ===== -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6">
        <a href="<?= site_url('/reception/patients') ?>" class="stat-card">
            <div class="stat-icon emerald"><i class="bi bi-person-fill-check"></i></div>
            <div>
                <div class="stat-label">Today's Patients</div>
                <div class="stat-value"><?= esc((string)($patients_today ?? 0)) ?></div>
                <div class="stat-sub">Registered</div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6">
        <a href="<?= site_url('/reception/queues') ?>" class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-calendar2-check-fill"></i></div>
            <div>
                <div class="stat-label">Appointments</div>
                <div class="stat-value"><?= esc((string)($appointments_today ?? 0)) ?></div>
                <div class="stat-sub">Today</div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6">
        <a href="<?= site_url('/reception/medicine-issue') ?>" class="stat-card">
            <div class="stat-icon orange"><i class="bi bi-capsule"></i></div>
            <div>
                <div class="stat-label">Pending Meds</div>
                <div class="stat-value"><?= esc((string)($pending_dispatches ?? 0)) ?></div>
                <div class="stat-sub">To Dispatch</div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6">
        <a href="<?= site_url('/reception/billing') ?>" class="stat-card">
            <div class="stat-icon green"><i class="bi bi-wallet2"></i></div>
            <div>
                <div class="stat-label">Today's Revenue</div>
                <div class="stat-value">₹<?= esc(number_format($revenue_today ?? 0)) ?></div>
                <div class="stat-sub">Collected</div>
            </div>
        </a>
    </div>
</div>

<!-- ===== ACTIVE OPD QUEUE + RECENT ACTIVITY ===== -->
<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="card-clean">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <span style="font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#6b7a8f;">
                        <i class="bi bi-play-circle-fill text-success me-1"></i> Active OPD Queue
                    </span>
                    <div style="font-size:.7rem;color:#94a3b8;"><?= !empty($active_queue) ? count($active_queue) . ' patients waiting' : 'Queue is empty' ?></div>
                </div>
                <a href="<?= site_url('/reception/queues') ?>" class="btn-soft" style="font-size:.7rem;">View All <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            <?php if (empty($active_queue)): ?>
                <div class="empty-clean"><i class="bi bi-person-fill-exclamation"></i><div>No active tokens</div><div style="font-size:.75rem">Queue is empty</div></div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-clean">
                    <thead><tr><th style="width:60px;">Token</th><th>Patient</th><th>Doctor</th><th style="width:100px;">Status</th><th style="width:130px;">Actions</th></tr></thead>
                    <tbody>
                        <?php foreach ($active_queue as $q): ?>
                        <tr>
                            <td class="fw-bold" style="font-size:.9rem;color:#0f7b4a;">#<?= esc((string)($q['token_number'] ?? 0)) ?></td>
                            <td><?= esc($q['patient_name'] ?? 'Unknown') ?></td>
                            <td style="font-size:.78rem;">Dr. <?= esc($q['doctor_name'] ?? 'N/A') ?></td>
                            <td><span class="badge-soft <?= ($q['queue_status'] ?? 'waiting') === 'in_consultation' ? 'badge-soft-consult' : 'badge-soft-waiting' ?>"><?= ($q['queue_status'] ?? 'waiting') === 'in_consultation' ? 'In Consultation' : 'Waiting' ?></span></td>
                            <td>
                                <a href="<?= site_url('/reception/queues/update/' . ($q['id'] ?? 0) . '?status=in_consultation') ?>" class="btn-soft btn-soft-primary" style="font-size:.65rem;padding:.1rem .6rem;">Call In</a>
                                <a href="<?= site_url('/reception/queues/update/' . ($q['id'] ?? 0) . '?status=completed') ?>" class="btn-soft btn-soft-success" style="font-size:.65rem;padding:.1rem .6rem;">Done</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card-clean">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span style="font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#6b7a8f;">
                    <i class="bi bi-activity text-success me-1"></i> Recent Activity
                </span>
                <span class="badge-soft"><i class="bi bi-dot" style="color:#22c55e"></i> live</span>
            </div>
            <?php if (!empty($recent_activity)): ?>
            <div style="max-height:300px;overflow-y:auto;">
                <?php 
                $iconMap = [
                    'patient' => 'person-plus',
                    'appointment' => 'calendar-plus',
                    'billing' => 'receipt',
                    'lead' => 'funnel',
                    'medicine' => 'capsule',
                    'ipd' => 'hospital',
                    'default' => 'clock-history'
                ];
                foreach ($recent_activity as $act): 
                    $type = $act['type'] ?? 'default';
                    $icon = $iconMap[$type] ?? $iconMap['default'];
                ?>
                <div class="activity-item">
                    <div class="activity-icon"><i class="bi bi-<?= $icon ?>"></i></div>
                    <div class="activity-content">
                        <div class="title"><?= esc($act['description'] ?? 'Activity recorded') ?></div>
                        <div class="time"><?= isset($act['created_at']) ? date('d M, h:i A', strtotime($act['created_at'])) : date('d M, h:i A') ?></div>
                    </div>
                    <span class="activity-badge <?= $type ?>"><?= esc(ucfirst($type)) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <div style="padding-top:.5rem;border-top:1px solid #edf2f7;margin-top:.2rem">
                <a href="<?= site_url('/reception/reports') ?>" class="btn-soft" style="font-size:.7rem;">View All <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            <?php else: ?>
            <div class="empty-clean"><i class="bi bi-journal-x"></i><div>No recent activity</div><div style="font-size:.75rem">Events will appear here</div></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ===== QUICK LINKS ===== -->
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card-clean">
            <div style="font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#6b7a8f;margin-bottom:.8rem;">
                <i class="bi bi-grid-3x3-gap-fill text-primary me-1"></i> Quick Access
            </div>
            <div class="quick-link-grid">
                <a href="<?= site_url('/reception/patients') ?>" class="quick-link-item"><i class="bi bi-folder2-open text-primary"></i> Directory</a>
                <a href="<?= site_url('/reception/ipd') ?>" class="quick-link-item"><i class="bi bi-hospital text-danger"></i> IPD Ward</a>
                <a href="<?= site_url('/reception/billing') ?>" class="quick-link-item"><i class="bi bi-cash-register text-success"></i> Billing</a>
                <a href="<?= site_url('/reception/medicine-issue') ?>" class="quick-link-item"><i class="bi bi-capsule text-warning"></i> Meds Issue</a>
                <a href="<?= site_url('/reception/communication') ?>" class="quick-link-item"><i class="bi bi-whatsapp text-success"></i> WhatsApp</a>
                <a href="<?= site_url('/reception/leads') ?>" class="quick-link-item"><i class="bi bi-funnel text-purple"></i> Leads</a>
                <a href="<?= site_url('/reception/followups') ?>" class="quick-link-item"><i class="bi bi-calendar-check text-info"></i> Follow-ups</a>
                <a href="<?= site_url('/reception/reports') ?>" class="quick-link-item"><i class="bi bi-graph-up text-secondary"></i> Reports</a>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-clean">
            <div style="font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#6b7a8f;margin-bottom:.8rem;">
                <i class="bi bi-info-circle-fill text-info me-1"></i> Branch Status
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;">
                <div style="padding:.4rem .6rem;background:#f8fafc;border-radius:8px;">
                    <div style="font-size:.6rem;color:#94a3b8;text-transform:uppercase;">Branch</div>
                    <div style="font-size:.8rem;font-weight:600;color:#0b1a2b;"><?= esc($branchName ?? 'Dehradun Main Clinic') ?></div>
                </div>
                <div style="padding:.4rem .6rem;background:#f8fafc;border-radius:8px;">
                    <div style="font-size:.6rem;color:#94a3b8;text-transform:uppercase;">Staff</div>
                    <div style="font-size:.8rem;font-weight:600;color:#0b1a2b;"><?= esc($user['username'] ?? 'Reception') ?></div>
                </div>
                <div style="padding:.4rem .6rem;background:#f8fafc;border-radius:8px;">
                    <div style="font-size:.6rem;color:#94a3b8;text-transform:uppercase;">Time</div>
                    <div style="font-size:.8rem;font-weight:600;color:#0b1a2b;"><?= date('h:i A') ?></div>
                </div>
                <div style="padding:.4rem .6rem;background:#f8fafc;border-radius:8px;">
                    <div style="font-size:.6rem;color:#94a3b8;text-transform:uppercase;">Date</div>
                    <div style="font-size:.8rem;font-weight:600;color:#0b1a2b;"><?= date('d M, Y') ?></div>
                </div>
                <div style="padding:.4rem .6rem;background:#f8fafc;border-radius:8px;grid-column:span 2;">
                    <div style="font-size:.6rem;color:#94a3b8;text-transform:uppercase;">Status</div>
                    <div style="font-size:.8rem;font-weight:600;color:#059669;display:flex;align-items:center;gap:.3rem;">
                        <span style="display:inline-block;width:8px;height:8px;background:#22c55e;border-radius:50%;animation:pulse 2s infinite;"></span>
                        Active - OPD Running
                    </div>
                </div>
            </div>
            <style>
            @keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
            </style>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/reception_footer.php'; ?>