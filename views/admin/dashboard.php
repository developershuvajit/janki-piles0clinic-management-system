<?php
if (!defined('ROOT_PATH')) { exit('No direct script access allowed'); }
$activePage = 'dashboard';
include VIEWS_PATH . '/layout/admin_header.php';
?>

<!-- ===== KPI STAT CARDS ===== -->
<div class="row g-3 mb-4">
    <div class="col-xl-2 col-lg-4 col-md-6 fade-in-up">
        <div class="stat-card green">
            <div class="stat-icon green"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="stat-label">Total Patients</div>
                <div class="stat-value" data-count="<?= $totalPatients ?? 0 ?>"><?= esc((string)($totalPatients ?? 0)) ?></div>
                <div class="stat-sub">Registered</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6 fade-in-up">
        <div class="stat-card blue">
            <div class="stat-icon blue"><i class="bi bi-calendar-check-fill"></i></div>
            <div>
                <div class="stat-label">Today OPD</div>
                <div class="stat-value" data-count="<?= $todayOpd ?? 0 ?>"><?= esc((string)($todayOpd ?? 0)) ?></div>
                <div class="stat-sub">Consultations</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6 fade-in-up">
        <div class="stat-card orange">
            <div class="stat-icon orange"><i class="bi bi-hospital-fill"></i></div>
            <div>
                <div class="stat-label">IPD Active</div>
                <div class="stat-value" data-count="<?= $activeIpd ?? 0 ?>"><?= esc((string)($activeIpd ?? 0)) ?></div>
                <div class="stat-sub">Admitted</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6 fade-in-up">
        <div class="stat-card green">
            <div class="stat-icon green"><i class="bi bi-currency-rupee"></i></div>
            <div>
                <div class="stat-label">Today Revenue</div>
                <div class="stat-value" data-count="<?= (int)($todayRevenue ?? 0) ?>" data-prefix="₹"><?= '₹' . number_format($todayRevenue ?? 0) ?></div>
                <div class="stat-sub">Collected</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6 fade-in-up">
        <div class="stat-card red">
            <div class="stat-icon red"><i class="bi bi-capsule-pill"></i></div>
            <div>
                <div class="stat-label">Low Stock</div>
                <div class="stat-value" data-count="<?= $lowStockCount ?? 0 ?>"><?= esc((string)($lowStockCount ?? 0)) ?></div>
                <div class="stat-sub">Medicines</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6 fade-in-up">
        <div class="stat-card teal">
            <div class="stat-icon teal"><i class="bi bi-journal-text"></i></div>
            <div>
                <div class="stat-label">Audit Logs</div>
                <div class="stat-value" data-count="<?= $logCount ?? 0 ?>"><?= esc((string)($logCount ?? 0)) ?></div>
                <div class="stat-sub">Events today</div>
            </div>
        </div>
    </div>
</div>

<!-- ===== LOW STOCK ALERT ===== -->
<?php if (!empty($lowStockItems)): ?>
<div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong><?= count($lowStockItems) ?> medicines</strong> are running low on stock.
    <a href="<?= site_url('/admin/inventory/low-stock') ?>" class="alert-link ms-1">View Inventory &rarr;</a>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- ===== QUICK ACTIONS + RECENT ACTIVITY ===== -->
<div class="row g-4 mb-4">
    <!-- Quick Actions -->
    <div class="col-lg-4">
        <div class="card h-100 p-4">
            <h6 class="fw-bold mb-3 text-muted" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.6px;">
                <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Quick Actions
            </h6>
            <div class="d-grid gap-2">
                <a href="<?= site_url('/admin/patients/create') ?>" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-person-plus me-1"></i> Register New Patient
                </a>
                <a href="<?= site_url('/admin/reception/walk-in') ?>" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-person-walking me-1"></i> Walk-in OPD
                </a>
                <a href="<?= site_url('/admin/ipd/admit') ?>" class="btn btn-sm btn-outline-warning">
                    <i class="bi bi-hospital me-1"></i> Admit IPD Patient
                </a>
                <a href="<?= site_url('/admin/appointments/pending') ?>" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-clock-history me-1"></i> Approve Appointments
                </a>
                <a href="<?= site_url('/admin/reports') ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-graph-up-arrow me-1"></i> View Reports
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity Log -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="p-4 pb-2">
                <h6 class="fw-bold mb-0 text-muted" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.6px;">
                    <i class="bi bi-activity me-1 text-success"></i> Recent Activity
                </h6>
            </div>
            <?php if (!empty($recentLogs)): ?>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>User</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentLogs as $log): ?>
                        <tr>
                            <td><?= esc($log['action']) ?></td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= esc($log['username'] ?? 'System') ?></span>
                            </td>
                            <td class="text-muted" style="font-size:0.78rem;">
                                <?= date('d M, h:i A', strtotime($log['created_at'])) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">
                <a href="<?= site_url('/admin/logs') ?>" class="btn btn-sm btn-outline-secondary">
                    View All Logs <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-journal-x empty-state-icon"></i>
                <div class="empty-state-title">No recent activity</div>
                <div class="empty-state-text">System events will appear here as users perform actions.</div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ===== SYSTEM UTILITIES ===== -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card p-4">
            <h6 class="fw-bold mb-3 text-muted" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.6px;">
                <i class="bi bi-cloud-arrow-up-fill text-success me-1"></i> Secure File Upload Test
            </h6>
            <p class="text-muted small mb-3">Verify file upload security: MIME validation, extension whitelist, size limit (2MB), and double-extension blocking.</p>
            <form action="<?= site_url('/admin/upload-test') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="test_file" class="form-label">Choose File (PDF, Word, Images)</label>
                    <input class="form-control form-control-sm" type="file" id="test_file" name="test_file" required>
                </div>
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-upload me-1"></i> Test Upload
                </button>
            </form>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card p-4">
            <h6 class="fw-bold mb-3 text-muted" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.6px;">
                <i class="bi bi-patch-check-fill text-success me-1"></i> Library Integrations
            </h6>
            <p class="text-muted small mb-3">Test FPDF PDF engine and PHPQRCode library integration with live output.</p>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= site_url('/admin/pdf-test') ?>" class="btn btn-sm btn-outline-danger" target="_blank">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Generate PDF
                </a>
                <a href="<?= site_url('/admin/qr-test') ?>" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-qr-code me-1"></i> Generate QR
                </a>
                <a href="<?= site_url('/admin/reports') ?>" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-bar-chart-line me-1"></i> Analytics
                </a>
            </div>
            <div class="mt-3 p-2 rounded" style="background:#f8fafc;font-size:0.78rem;">
                <span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i>PHP <?= phpversion() ?></span>
                &nbsp;&bull;&nbsp;
                <span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Env: <?= esc(ucfirst($_ENV['APP_ENV'] ?? 'development')) ?></span>
                &nbsp;&bull;&nbsp;
                <span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Session Active</span>
            </div>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
