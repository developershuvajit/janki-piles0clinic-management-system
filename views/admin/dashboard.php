<?php
if (!defined('ROOT_PATH')) { exit('No direct script access allowed'); }
$activePage = 'dashboard';
include VIEWS_PATH . '/layout/admin_header.php';
?>

<!-- ===== CLEAN DASHBOARD - 3 CARDS PER ROW ===== -->
<style>
    /* minimal inline styles */
    .stat-card{background:#fff;border-radius:14px;padding:1rem 1.2rem;box-shadow:0 2px 8px rgba(0,0,0,.04);display:flex;align-items:center;gap:1rem;border:1px solid #f0f2f5;transition:.2s;cursor:pointer;text-decoration:none;color:inherit}
    .stat-card:hover{transform:translateY(-4px);box-shadow:0 12px 28px rgba(0,0,0,.08);border-color:#2563eb}
    .stat-card:active{transform:scale(0.97)}
    .stat-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0}
    .stat-icon.green{background:#e6f5ed;color:#0f7b4a}
    .stat-icon.blue{background:#e6f0ff;color:#1a6bc4}
    .stat-icon.orange{background:#fff1e0;color:#c5711e}
    .stat-icon.red{background:#ffe9e9;color:#b33c3c}
    .stat-icon.teal{background:#e3f7f5;color:#1b7a72}
    .stat-label{font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.03em;color:#6b7a8f}
    .stat-value{font-size:1.7rem;font-weight:700;line-height:1.1;color:#0b1a2b}
    .stat-sub{font-size:.65rem;color:#94a3b8;margin-top:1px}
    .card-clean{background:#fff;border-radius:14px;box-shadow:0 2px 8px rgba(0,0,0,.04);border:1px solid #f0f2f5;padding:1.2rem}
    .btn-soft{border-radius:40px;padding:.3rem 1rem;font-size:.78rem;border:1px solid #e8ecf0;background:transparent;color:#1e293b;display:flex;align-items:center;gap:6px;transition:.1s;text-decoration:none}
    .btn-soft:hover{background:#f5f7fa;border-color:#d0d5dd}
    .badge-soft{background:#f1f4f8;color:#1e293b;padding:.15rem .8rem;border-radius:40px;font-size:.7rem}
    .empty-clean{padding:2rem 1rem;text-align:center;color:#94a3b8}
    .empty-clean i{font-size:2.2rem;opacity:.3;display:block;margin-bottom:.3rem}
    .table-clean{font-size:.82rem}
    .table-clean th{font-size:.6rem;text-transform:uppercase;color:#6b7a8f;font-weight:600;padding:.4rem .8rem;border-bottom:1px solid #edf2f7}
    .table-clean td{padding:.4rem .8rem;border:none;background:#fafcfe;border-radius:8px}
    .upload-area{border:1.5px dashed #e2e8f0;border-radius:16px;padding:1rem;background:#fafdff}
</style>

<!-- ===== STATS - 3 CARDS PER ROW ===== -->
<div class="row g-3 mb-4">
    <!-- Total Patients -->
    <div class="col-xl-4 col-lg-4 col-md-6">
        <a href="<?= site_url('/admin/patients') ?>" class="stat-card">
            <div class="stat-icon green"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="stat-label">Total Patients</div>
                <div class="stat-value"><?= esc((string)($totalPatients ?? 0)) ?></div>
                <div class="stat-sub">Registered</div>
            </div>
        </a>
    </div>
    
    <!-- Today OPD -->
    <div class="col-xl-4 col-lg-4 col-md-6">
        <a href="<?= site_url('/admin/appointments') ?>" class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-calendar-check-fill"></i></div>
            <div>
                <div class="stat-label">Today OPD</div>
                <div class="stat-value"><?= esc((string)($todayOpd ?? 0)) ?></div>
                <div class="stat-sub">Consultations</div>
            </div>
        </a>
    </div>
    
    <!-- IPD Active -->
    <div class="col-xl-4 col-lg-4 col-md-6">
        <a href="<?= site_url('/admin/ipd') ?>" class="stat-card">
            <div class="stat-icon orange"><i class="bi bi-hospital-fill"></i></div>
            <div>
                <div class="stat-label">IPD Active</div>
                <div class="stat-value"><?= esc((string)($activeIpd ?? 0)) ?></div>
                <div class="stat-sub">Admitted</div>
            </div>
        </a>
    </div>
    
    <!-- Today Revenue -->
    <div class="col-xl-4 col-lg-4 col-md-6">
        <a href="<?= site_url('/admin/billing') ?>" class="stat-card">
            <div class="stat-icon green"><i class="bi bi-currency-rupee"></i></div>
            <div>
                <div class="stat-label">Today Revenue</div>
                <div class="stat-value">₹<?= number_format($todayRevenue ?? 0) ?></div>
                <div class="stat-sub">Collected</div>
            </div>
        </a>
    </div>
    
    <!-- Low Stock -->
    <div class="col-xl-4 col-lg-4 col-md-6">
        <a href="<?= site_url('/admin/inventory/low-stock') ?>" class="stat-card">
            <div class="stat-icon red"><i class="bi bi-capsule-pill"></i></div>
            <div>
                <div class="stat-label">Low Stock</div>
                <div class="stat-value"><?= esc((string)($lowStockCount ?? 0)) ?></div>
                <div class="stat-sub">Medicines</div>
            </div>
        </a>
    </div>
    
    <!-- Audit Logs -->
    <div class="col-xl-4 col-lg-4 col-md-6">
        <a href="<?= site_url('/admin/logs') ?>" class="stat-card">
            <div class="stat-icon teal"><i class="bi bi-journal-text"></i></div>
            <div>
                <div class="stat-label">Audit Logs</div>
                <div class="stat-value"><?= esc((string)($logCount ?? 0)) ?></div>
                <div class="stat-sub">Events today</div>
            </div>
        </a>
    </div>
</div>

<!-- ===== LOW STOCK ALERT ===== -->
<?php if (!empty($lowStockItems)): ?>
<div class="alert alert-warning alert-dismissible fade show mb-4" style="border-radius:12px;background:#fef7e8;border:1px solid #fae6c3;color:#7a5d2b;padding:.6rem 1rem">
    <i class="bi bi-exclamation-triangle-fill me-1"></i>
    <strong><?= count($lowStockItems) ?> medicines</strong> low stock.
    <a href="<?= site_url('/admin/inventory/low-stock') ?>" style="color:#8b5f1e;font-weight:600">View &rarr;</a>
    <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size:.7rem"></button>
</div>
<?php endif; ?>

<!-- ===== QUICK ACTIONS + RECENT ===== -->
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-clean">
            <div style="font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#6b7a8f;margin-bottom:.6rem">
                <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Quick Actions
            </div>
            <div class="d-grid gap-2">
                <a href="<?= site_url('/admin/patients/create') ?>" class="btn-soft"><i class="bi bi-person-plus"></i> Register Patient</a>
                <a href="<?= site_url('/admin/reception/walk-in') ?>" class="btn-soft"><i class="bi bi-person-walking"></i> Walk-in OPD</a>
                <a href="<?= site_url('/admin/ipd/admit') ?>" class="btn-soft"><i class="bi bi-hospital"></i> Admit IPD</a>
                <a href="<?= site_url('/admin/appointments/pending') ?>" class="btn-soft"><i class="bi bi-clock-history"></i> Approve Appointments</a>
                <a href="<?= site_url('/admin/reports') ?>" class="btn-soft"><i class="bi bi-graph-up-arrow"></i> Reports</a>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-clean">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem">
                <span style="font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#6b7a8f"><i class="bi bi-activity text-success me-1"></i> Recent Activity</span>
                <span class="badge-soft"><i class="bi bi-dot" style="color:#22c55e"></i> live</span>
            </div>
            <?php if (!empty($recentLogs)): ?>
            <div class="table-responsive">
                <table class="table table-clean">
                    <thead><tr><th>Action</th><th>User</th><th>Time</th></tr></thead>
                    <tbody>
                        <?php foreach ($recentLogs as $log): ?>
                        <tr><td><?= esc($log['action']) ?></td><td><span class="badge-soft"><?= esc($log['username'] ?? 'System') ?></span></td><td style="font-size:.7rem;color:#6b7a8f"><?= date('d M, h:i A', strtotime($log['created_at'])) ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div style="padding-top:.5rem;border-top:1px solid #edf2f7;margin-top:.2rem">
                <a href="<?= site_url('/admin/logs') ?>" class="btn-soft" style="font-size:.7rem">View All <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            <?php else: ?>
            <div class="empty-clean"><i class="bi bi-journal-x"></i><div>No recent activity</div><div style="font-size:.75rem">Events will appear here</div></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ===== UTILITIES ===== -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card-clean">
            <div style="font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#6b7a8f;margin-bottom:.5rem">
                <i class="bi bi-cloud-arrow-up-fill text-success me-1"></i> Secure Upload Test
            </div>
            <p style="font-size:.78rem;color:#6b7a8f;margin-bottom:.8rem">MIME validation, whitelist, 2MB limit, double-extension blocking.</p>
            <form action="<?= site_url('/admin/upload-test') ?>" method="POST" enctype="multipart/form-data" class="upload-area">
                <?= csrf_field() ?>
                <div class="mb-2">
                    <label style="font-size:.8rem;font-weight:500">Choose File (PDF, Word, Images)</label>
                    <input class="form-control form-control-sm" type="file" name="test_file" required style="border-radius:40px;border:1px solid #e2e8f0;font-size:.8rem;padding:.3rem .8rem">
                </div>
                <button type="submit" class="btn btn-sm btn-primary" style="border-radius:40px;background:#1a6bc4;border:none;padding:.25rem 1.5rem;font-size:.8rem"><i class="bi bi-upload me-1"></i> Test</button>
            </form>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-clean">
            <div style="font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#6b7a8f;margin-bottom:.5rem">
                <i class="bi bi-patch-check-fill text-success me-1"></i> Library Integrations
            </div>
            <p style="font-size:.78rem;color:#6b7a8f;margin-bottom:.8rem">FPDF PDF engine & PHPQRCode library with live output.</p>
            <div class="d-flex gap-2 flex-wrap mb-2">
                <a href="<?= site_url('/admin/pdf-test') ?>" class="btn-soft" target="_blank" style="border-color:#f1d7d7;color:#b33c3c"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                <a href="<?= site_url('/admin/qr-test') ?>" class="btn-soft" style="border-color:#d1e8df;color:#0f7b4a"><i class="bi bi-qr-code"></i> QR</a>
                <a href="<?= site_url('/admin/reports') ?>" class="btn-soft" style="border-color:#d0e2ff;color:#1a6bc4"><i class="bi bi-bar-chart-line"></i> Analytics</a>
            </div>
            <div style="padding:.5rem 1rem;border-radius:12px;background:#f6faff;font-size:.72rem;border:1px solid #eaf0f8">
                <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>PHP <?= phpversion() ?></span>
                &bull; <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Env: <?= esc(ucfirst($_ENV['APP_ENV'] ?? 'development')) ?></span>
                &bull; <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Session Active</span>
            </div>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>