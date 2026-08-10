<?php 
$activePage = 'attendance_report';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<style>
    .stat-card-mini {
        background: #fff;
        border-radius: 12px;
        padding: 0.8rem 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,.04);
        border: 1px solid #f0f2f5;
        text-align: center;
        transition: 0.15s;
    }
    .stat-card-mini:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,.06);
    }
    .stat-card-mini .number {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0b1a2b;
    }
    .stat-card-mini .label {
        font-size: 0.65rem;
        color: #94a3b8;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .stat-card-mini .icon {
        font-size: 1.8rem;
        margin-bottom: 0.2rem;
    }
    .badge-soft {
        background: #f1f4f8;
        color: #1e293b;
        padding: .15rem .7rem;
        border-radius: 40px;
        font-size: .7rem;
        display: inline-block;
    }
    .card-clean {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(0,0,0,.04);
        border: 1px solid #f0f2f5;
        padding: 1.2rem;
    }
    .table-clean {
        font-size: .82rem;
        width: 100%;
        border-collapse: collapse;
    }
    .table-clean th {
        font-size: .6rem;
        text-transform: uppercase;
        color: #6b7a8f;
        font-weight: 600;
        padding: .4rem .8rem;
        border-bottom: 1px solid #edf2f7;
        text-align: left;
    }
    .table-clean td {
        padding: .4rem .8rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .btn-primary-clean {
        border-radius: 40px;
        padding: 0.3rem 1.2rem;
        font-size: 0.78rem;
        background: #2563eb;
        border: none;
        color: #fff;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        text-decoration: none;
    }
    .btn-primary-clean:hover {
        background: #1d4ed8;
        color: #fff;
    }
    .btn-soft-clean {
        border-radius: 40px;
        padding: 0.3rem 1rem;
        font-size: 0.78rem;
        border: 1px solid #e2e8f0;
        background: transparent;
        color: #1e293b;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        text-decoration: none;
    }
    .btn-soft-clean:hover {
        background: #f5f7fa;
        border-color: #cbd5e1;
    }
    @media print {
        .no-print { display: none !important; }
    }
</style>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h5 class="fw-bold text-slate mb-0" style="font-size:1rem;">
            <i class="bi bi-graph-up-arrow text-success"></i> Attendance Report
        </h5>
        <span style="font-size:0.72rem;color:#94a3b8;">Complete attendance history and statistics</span>
    </div>
    <div class="d-flex gap-2 flex-wrap no-print">
        <form method="GET" class="d-flex gap-2 align-items-center">
            <input type="date" name="date" value="<?= $_GET['date'] ?? date('Y-m-d') ?>" class="form-control form-control-sm" style="width:auto;border-radius:40px;border:1px solid #e2e8f0;padding:0.2rem 0.8rem;">
            <button type="submit" class="btn-primary-clean" style="font-size:0.7rem;padding:0.2rem 1rem;">
                <i class="bi bi-filter"></i> Filter
            </button>
            <a href="<?= site_url('/admin/attendance/report') ?>" class="btn-soft-clean" style="font-size:0.7rem;padding:0.2rem 1rem;">Today</a>
        </form>
        <button onclick="window.print()" class="btn-soft-clean" style="font-size:0.7rem;padding:0.2rem 1rem;">
            <i class="bi bi-printer"></i> Print
        </button>
        <a href="<?= site_url('/admin/employees/attendance') ?>" class="btn-primary-clean" style="font-size:0.7rem;padding:0.2rem 1rem;">
            <i class="bi bi-pencil"></i> Manual Entry
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="stat-card-mini">
            <div class="icon text-primary">👥</div>
            <div class="number"><?= $totalStaff ?? 0 ?></div>
            <div class="label">Total Staff</div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="stat-card-mini">
            <div class="icon text-success">✅</div>
            <div class="number"><?= $present ?? 0 ?></div>
            <div class="label">Present</div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="stat-card-mini">
            <div class="icon text-danger">❌</div>
            <div class="number"><?= $absent ?? 0 ?></div>
            <div class="label">Absent</div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="stat-card-mini">
            <div class="icon text-warning">⏰</div>
            <div class="number"><?= $late ?? 0 ?></div>
            <div class="label">Late</div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="stat-card-mini">
            <div class="icon text-info">📋</div>
            <div class="number"><?= $leave ?? 0 ?></div>
            <div class="label">On Leave</div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="stat-card-mini">
            <div class="icon text-secondary">📊</div>
            <div class="number"><?= $totalStaff > 0 ? round(($present / $totalStaff) * 100, 1) : 0 ?>%</div>
            <div class="label">Attendance %</div>
        </div>
    </div>
</div>

<!-- Attendance Table -->
<div class="card-clean">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span style="font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#6b7a8f;">
            <i class="bi bi-table me-1"></i> Detailed Attendance Log
        </span>
        <span style="font-size:0.7rem;color:#94a3b8;">Date: <?= date('d M, Y', strtotime($date ?? date('Y-m-d'))) ?></span>
    </div>
    <div class="table-responsive">
        <table class="table table-clean">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Employee</th>
                    <th>Role</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th style="width:100px;">Status</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($attendanceData)): ?>
                    <tr><td colspan="7" class="text-center text-muted" style="padding:2rem;">No attendance records found for this date.</td></tr>
                <?php else: ?>
                    <?php $sn = 1; foreach ($attendanceData as $row): ?>
                    <tr>
                        <td><?= $sn++ ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <?php if (!empty($row['photo'])): ?>
                                    <img src="<?= site_url($row['photo']) ?>" style="width:30px;height:30px;border-radius:50%;object-fit:cover;border:1px solid #e2e8f0;">
                                <?php else: ?>
                                    <div style="width:30px;height:30px;border-radius:50%;background:#f1f4f8;display:flex;align-items:center;justify-content:center;">
                                        <i class="bi bi-person text-secondary" style="font-size:0.8rem;"></i>
                                    </div>
                                <?php endif; ?>
                                <span style="font-weight:500;font-size:0.82rem;"><?= esc($row['username'] ?? 'Unknown') ?></span>
                            </div>
                        </td>
                        <td style="font-size:0.78rem;"><?= esc($row['role_name'] ?? 'Staff') ?></td>
                        <td style="font-size:0.78rem;"><?= $row['check_in'] ?? '-' ?></td>
                        <td style="font-size:0.78rem;"><?= $row['check_out'] ?? '-' ?></td>
                        <td>
                            <?php 
                            $status = $row['status'] ?? 'not_marked';
                            $colors = [
                                'present' => 'background:#e6f5ed;color:#0f7b4a;',
                                'absent' => 'background:#ffe9e9;color:#b33c3c;',
                                'late' => 'background:#fef7e8;color:#c5711e;',
                                'leave' => 'background:#e6f0ff;color:#1a6bc4;',
                                'half_day' => 'background:#f0ebff;color:#7c3aed;',
                                'not_marked' => 'background:#f1f4f8;color:#94a3b8;'
                            ];
                            $labels = [
                                'present' => 'Present',
                                'absent' => 'Absent',
                                'late' => 'Late',
                                'leave' => 'On Leave',
                                'half_day' => 'Half Day',
                                'not_marked' => 'Not Marked'
                            ];
                            ?>
                            <span class="badge-soft" style="<?= $colors[$status] ?? $colors['not_marked'] ?>;padding:0.15rem 0.7rem;font-size:0.65rem;">
                                <?= $labels[$status] ?? ucfirst($status) ?>
                            </span>
                        </td>
                        <td style="font-size:0.7rem;color:#94a3b8;"><?= esc($row['notes'] ?? '') ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>