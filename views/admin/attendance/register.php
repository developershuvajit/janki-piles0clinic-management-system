 <?php 
$activePage = 'attendance';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- ============================================
     PAGE CSS
     ============================================ -->
<link rel="stylesheet" href="<?= asset('css/datatable.css') ?>">

<style>
    .badge-qr {
        background: #e6f0ff;
        color: #1a6bc4;
        font-size: 0.55rem;
        padding: 0.1rem 0.5rem;
        border-radius: 40px;
        display: inline-block;
        margin-left: 0.3rem;
    }
    .badge-qr i {
        font-size: 0.5rem;
    }
    .employee-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid #e2e8f0;
    }
    .employee-avatar-placeholder {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #f1f4f8;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 0.7rem;
    }
    .shift-time {
        font-size: 0.7rem;
        color: #6b7a8f;
    }
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
        font-size: 1.3rem;
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
</style>

<!-- ============================================
     PAGE HEADER
     ============================================ -->
<div class="datatable-wrapper mt-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h5 class="fw-bold text-slate mb-0" style="font-size:1rem;">
                <i class="bi bi-calendar-check text-success me-2"></i>Daily Attendance Register
            </h5>
            <p class="text-muted small mb-0">
                Record manual clock-in times and statuses for employee shifts.
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 ms-2">
                    <i class="bi bi-qr-code me-1"></i> QR Attendance Auto-Synced
                </span>
            </p>
        </div>
        <div style="max-width: 250px;">
            <label class="form-label small fw-semibold">Select Register Date</label>
            <input type="date" class="form-control form-control-sm" value="<?= esc($date) ?>" onchange="window.location.href='<?= site_url('/admin/employees/attendance?date=') ?>' + this.value">
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card-mini">
                <div class="number"><?= count($roster ?? []) ?></div>
                <div class="label">Total Staff</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-mini">
                <div class="number" style="color:#0f7b4a;">
                    <?php 
                    $present = 0;
                    foreach ($roster as $r) {
                        if (($r['status'] ?? '') === 'present') $present++;
                    }
                    echo $present;
                    ?>
                </div>
                <div class="label">Present</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-mini">
                <div class="number" style="color:#c5711e;">
                    <?php 
                    $late = 0;
                    foreach ($roster as $r) {
                        if (($r['status'] ?? '') === 'late') $late++;
                    }
                    echo $late;
                    ?>
                </div>
                <div class="label">Late</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-mini">
                <div class="number" style="color:#b33c3c;">
                    <?php 
                    $absent = 0;
                    foreach ($roster as $r) {
                        if (($r['status'] ?? '') === 'absent' || empty($r['status'])) $absent++;
                    }
                    echo $absent;
                    ?>
                </div>
                <div class="label">Absent</div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="<?= site_url('/admin/employees/attendance/save') ?>" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="date" value="<?= esc($date) ?>">

        <div class="card-clean">
            <div class="table-responsive">
                <table id="attendanceTable" class="table-custom" style="width:100%">
                    <thead>
                        <tr>
                            <th class="sno">#</th>
                            <th>Employee</th>
                            <th>Role</th>
                            <th>Shift</th>
                            <th style="width:140px;">Status</th>
                            <th style="width:120px;">Check In</th>
                            <th style="width:120px;">Check Out</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($roster)): ?>
                            <tr>
                                <td colspan="8" style="text-align:center;padding:2.5rem 1rem;color:#94a3b8;">
                                    No active employees found to log attendance.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $sn = 1; foreach ($roster as $row): ?>
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
                                            <div>
                                                <span style="font-weight:500;font-size:0.82rem;"><?= esc($row['employee_name']) ?></span>
                                                <?php if (!empty($row['check_in'])): ?>
                                                    <span class="badge-qr">
                                                        <i class="bi bi-qr-code me-1"></i> QR
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="font-size:0.78rem;"><?= esc($row['role_name'] ?? 'Staff') ?></td>
                                    <td class="shift-time">
                                        <?php 
                                        $shiftStart = !empty($row['shift_start']) ? date('h:i A', strtotime($row['shift_start'])) : '09:00 AM';
                                        $shiftEnd = !empty($row['shift_end']) ? date('h:i A', strtotime($row['shift_end'])) : '05:00 PM';
                                        ?>
                                        <?= $shiftStart ?> - <?= $shiftEnd ?>
                                    </td>
                                    <td>
                                        <select class="form-control form-control-sm form-select" name="attendance[<?= $row['employee_id'] ?>][status]" style="min-width:120px;">
                                            <option value="present" <?= (isset($row['status']) && $row['status'] === 'present') ? 'selected' : '' ?>>Present</option>
                                            <option value="absent" <?= (isset($row['status']) && $row['status'] === 'absent') ? 'selected' : '' ?>>Absent</option>
                                            <option value="late" <?= (isset($row['status']) && $row['status'] === 'late') ? 'selected' : '' ?>>Late</option>
                                            <option value="leave" <?= (isset($row['status']) && $row['status'] === 'leave') ? 'selected' : '' ?>>Leave</option>
                                            <option value="half_day" <?= (isset($row['status']) && $row['status'] === 'half_day') ? 'selected' : '' ?>>Half Day</option>
                                            <option value="holiday" <?= (isset($row['status']) && $row['status'] === 'holiday') ? 'selected' : '' ?>>Holiday</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="time" class="form-control form-control-sm" name="attendance[<?= $row['employee_id'] ?>][check_in]" value="<?= esc(!empty($row['check_in']) ? substr($row['check_in'], 0, 5) : '') ?>" style="max-width:120px;">
                                    </td>
                                    <td>
                                        <input type="time" class="form-control form-control-sm" name="attendance[<?= $row['employee_id'] ?>][check_out]" value="<?= esc(!empty($row['check_out']) ? substr($row['check_out'], 0, 5) : '') ?>" style="max-width:120px;">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="attendance[<?= $row['employee_id'] ?>][notes]" value="<?= esc($row['notes'] ?? '') ?>" placeholder="Remarks">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-end mt-4">
            <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm" style="border-radius:40px;background:#2563eb;border:none;">
                <i class="bi bi-save me-1"></i> Save Daily Attendance
            </button>
        </div>
    </form>
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
    if ($('#attendanceTable').length) {
        var table = $('#attendanceTable').DataTable({
            dom: '<"d-flex flex-wrap align-items-center justify-content-between gap-2 p-2"lBf>t<"d-flex flex-wrap align-items-center justify-content-between gap-2 p-2"ip>',
            buttons: [
                { extend: 'copy', text: '<i class="bi bi-copy"></i> Copy', className: 'btn btn-sm' },
                { extend: 'csv', text: '<i class="bi bi-file-earmark-spreadsheet"></i> CSV', className: 'btn btn-sm' },
                { extend: 'excel', text: '<i class="bi bi-file-earmark-excel"></i> Excel', className: 'btn btn-sm' },
                { extend: 'pdf', text: '<i class="bi bi-file-earmark-pdf"></i> PDF', className: 'btn btn-sm' },
                { extend: 'print', text: '<i class="bi bi-printer"></i> Print', className: 'btn btn-sm' }
            ],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            order: [[1, 'asc']],
            columnDefs: [
                { orderable: false, targets: [0] },
                { searchable: false, targets: [0] }
            ],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_",
                info: "_START_ – _END_ of _TOTAL_",
                infoEmpty: "No employees found",
                infoFiltered: "(filtered from _MAX_ total)",
                zeroRecords: "No matching employees found"
            }
        });
    }
});
</script>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>