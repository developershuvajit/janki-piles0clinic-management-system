 <?php 
$activePage = 'attendance_report';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- ============================================
     PAGE CSS
     ============================================ -->
<link rel="stylesheet" href="<?= asset('css/datatable.css') ?>">

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
    .btn-manual {
        background: #0f7b4a;
        color: #fff;
        border: none;
        border-radius: 40px;
        padding: 0.3rem 1.2rem;
        font-size: 0.78rem;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        text-decoration: none;
    }
    .btn-manual:hover {
        background: #0b6e44;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(15,123,74,0.2);
    }
    .employee-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid #e2e8f0;
    }
    .employee-avatar-placeholder {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #f1f4f8;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 0.8rem;
    }
    .filter-section {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.8rem;
        padding: 0.8rem 1.2rem;
        background: #fafcff;
        border-radius: 12px;
        border: 1px solid #eef2f6;
    }
    .filter-section .form-control-sm {
        border-radius: 40px;
        border: 1px solid #e2e8f0;
        padding: 0.2rem 0.8rem;
        font-size: 0.75rem;
    }
    .badge-no-record {
        font-size: 0.55rem;
        color: #94a3b8;
        display: block;
        margin-top: 0.1rem;
    }
    .shift-time {
        font-size: 0.7rem;
        color: #6b7a8f;
    }
    @media print {
        .no-print { display: none !important; }
    }
</style>

<!-- ============================================
     PAGE HTML
     ============================================ -->
<div class="datatable-wrapper mt-4">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h5 class="fw-bold text-slate mb-0" style="font-size:1rem;">
                <i class="bi bi-graph-up-arrow text-success"></i> Attendance Report
            </h5>
            <span style="font-size:0.72rem;color:#94a3b8;">Complete attendance history with filter options</span>
        </div>
        <div class="d-flex gap-2 flex-wrap no-print">
            <a href="<?= site_url('/admin/employees/attendance') ?>" class="btn-manual" style="font-size:0.7rem;padding:0.2rem 1rem;">
                <i class="bi bi-pencil"></i> Manual Entry
            </a>
            <button onclick="window.print()" class="btn-soft-clean" style="font-size:0.7rem;padding:0.2rem 1rem;">
                <i class="bi bi-printer"></i> Print
            </button>
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

    <!-- Filter Section -->
    <div class="filter-section no-print mb-4">
        <span class="text-muted small fw-semibold"><i class="bi bi-funnel"></i> Filters:</span>
        <form method="GET" class="d-flex flex-wrap align-items-center gap-2">
            <input type="date" name="date" value="<?= $_GET['date'] ?? date('Y-m-d') ?>" class="form-control form-control-sm" style="width:160px;">
            <input type="month" name="month" value="<?= $_GET['month'] ?? '' ?>" class="form-control form-control-sm" style="width:160px;" placeholder="Select Month">
            <select name="status" class="form-control form-control-sm" style="width:140px;">
                <option value="">All Status</option>
                <option value="present" <?= ($_GET['status'] ?? '') === 'present' ? 'selected' : '' ?>>Present</option>
                <option value="absent" <?= ($_GET['status'] ?? '') === 'absent' ? 'selected' : '' ?>>Absent</option>
                <option value="late" <?= ($_GET['status'] ?? '') === 'late' ? 'selected' : '' ?>>Late</option>
                <option value="leave" <?= ($_GET['status'] ?? '') === 'leave' ? 'selected' : '' ?>>On Leave</option>
                <option value="half_day" <?= ($_GET['status'] ?? '') === 'half_day' ? 'selected' : '' ?>>Half Day</option>
            </select>
            <button type="submit" class="btn-primary-clean" style="font-size:0.7rem;padding:0.2rem 1rem;">
                <i class="bi bi-filter"></i> Apply
            </button>
            <a href="<?= site_url('/admin/attendance/report') ?>" class="btn-soft-clean" style="font-size:0.7rem;padding:0.2rem 1rem;">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </a>
        </form>
    </div>

    <!-- Attendance Table -->
    <div class="card-clean">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span style="font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#6b7a8f;">
                <i class="bi bi-table me-1"></i> Detailed Attendance Log
            </span>
            <span style="font-size:0.7rem;color:#94a3b8;" id="reportDate">
                <?php if (!empty($_GET['month'])): ?>
                    Month: <?= date('F Y', strtotime($_GET['month'] . '-01')) ?>
                <?php else: ?>
                    Date: <?= date('d M, Y', strtotime($date ?? date('Y-m-d'))) ?>
                <?php endif; ?>
            </span>
        </div>
        <div class="table-responsive">
            <table id="attendanceReportTable" class="table-custom" style="width:100%">
                <thead>
                    <tr>
                        <th class="sno">#</th>
                        <th>Employee</th>
                        <th>Role</th>
                        <th>Shift</th>
                        <th>Date</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th style="width:100px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($attendanceData)): ?>
                        <tr>
                            <td colspan="8" style="text-align:center;padding:2.5rem 1rem;color:#94a3b8;">
                                No attendance records found for the selected date/month.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $selectedDate = $_GET['date'] ?? date('Y-m-d');
                        $sn = 1;
                        foreach ($attendanceData as $row): 
                            $hasAttendance = !empty($row['check_in']) || !empty($row['check_out']);
                            // Show the actual date from the record if it exists, otherwise show selected date
                            $displayDate = $hasAttendance && !empty($row['date']) 
                                ? date('d M Y', strtotime($row['date'])) 
                                : date('d M Y', strtotime($selectedDate));
                            
                            // Get shift from employee table
                            $shiftStart = !empty($row['shift_start']) ? date('h:i A', strtotime($row['shift_start'])) : '09:00 AM';
                            $shiftEnd = !empty($row['shift_end']) ? date('h:i A', strtotime($row['shift_end'])) : '05:00 PM';
                            
                            // Calculate late minutes if status is late
                            $lateMin = 0;
                            if (($row['status'] ?? '') === 'late' && !empty($row['check_in']) && !empty($row['shift_start'])) {
                                $lateMin = round((strtotime($row['check_in']) - strtotime($row['shift_start'])) / 60);
                            }
                        ?>
                        <tr>
                            <td class="sno"><?= $sn++ ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <?php if (!empty($row['photo'])): ?>
                                        <img src="<?= site_url($row['photo']) ?>" class="employee-avatar">
                                    <?php else: ?>
                                        <div class="employee-avatar-placeholder">
                                            <i class="bi bi-person"></i>
                                        </div>
                                    <?php endif; ?>
                                    <span style="font-weight:500;font-size:0.82rem;"><?= esc($row['username'] ?? 'Unknown') ?></span>
                                </div>
                            </td>
                            <td style="font-size:0.78rem;"><?= esc($row['role_name'] ?? 'Staff') ?></td>
                            <td style="font-size:0.7rem;color:#6b7a8f;">
                                <?= $shiftStart ?> - <?= $shiftEnd ?>
                            </td>
                            <td style="font-size:0.78rem;">
                                <?= $displayDate ?>
                                <?php if (!$hasAttendance): ?>
                                    <span class="badge-no-record">(No record)</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size:0.78rem;"><?= !empty($row['check_in']) ? date('h:i A', strtotime($row['check_in'])) : '-' ?></td>
                            <td style="font-size:0.78rem;"><?= !empty($row['check_out']) ? date('h:i A', strtotime($row['check_out'])) : '-' ?></td>
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
                                    'late' => 'Late ⏰',
                                    'leave' => 'On Leave',
                                    'half_day' => 'Half Day',
                                    'not_marked' => 'Not Marked'
                                ];
                                ?>
                                <span class="badge-soft" style="<?= $colors[$status] ?? $colors['not_marked'] ?>;padding:0.15rem 0.7rem;font-size:0.65rem;">
                                    <?= $labels[$status] ?? ucfirst($status) ?>
                                </span>
                                <?php if ($status === 'late' && $lateMin > 0): ?>
                                    <div style="font-size:0.55rem;color:#c5711e;margin-top:0.1rem;">
                                        <?= $lateMin ?> min late
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============================================
     DATATABLES LIBS + INIT
     ============================================ -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script src="<?= asset('js/datatable.js') ?>"></script>

<script>
$(document).ready(function() {
    if ($('#attendanceReportTable').length) {
        var table = $('#attendanceReportTable').DataTable({
            dom: '<"d-flex flex-wrap align-items-center justify-content-between gap-2 p-2"lBf>t<"d-flex flex-wrap align-items-center justify-content-between gap-2 p-2"ip>',
            buttons: [
                { extend: 'copy', text: '<i class="bi bi-copy"></i> Copy', className: 'btn btn-sm' },
                { extend: 'csv', text: '<i class="bi bi-file-earmark-spreadsheet"></i> CSV', className: 'btn btn-sm' },
                { extend: 'excel', text: '<i class="bi bi-file-earmark-excel"></i> Excel', className: 'btn btn-sm' },
                { extend: 'pdf', text: '<i class="bi bi-file-earmark-pdf"></i> PDF', className: 'btn btn-sm' },
                { extend: 'print', text: '<i class: 'bi bi-printer"></i> Print', className: 'btn btn-sm' }
            ],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            order: [[4, 'desc']], // Sort by Date column (index 4)
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    searchable: false
                },
                { orderable: false, targets: [7] },
                { searchable: false, targets: [0, 7] }
            ],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_",
                info: "_START_ – _END_ of _TOTAL_",
                infoEmpty: "No attendance records found",
                infoFiltered: "(filtered from _MAX_ total)",
                zeroRecords: "No matching records found"
            },
            drawCallback: function(settings) {
                var api = this.api();
                var rows = api.rows({ page: 'current' }).nodes();
                var pageInfo = api.page.info();
                
                api.column(0, { page: 'current' }).data().each(function(data, index) {
                    var row = rows[index];
                    var cell = $(row).find('td:first-child');
                    cell.text(pageInfo.start + index + 1);
                });
            }
        });
    }
});
</script>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>