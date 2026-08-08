<?php 
$activePage = 'attendance';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Filter & Date Row -->
<div class="card border-0 shadow-sm p-3 mb-4">
    <form action="<?= site_url('/admin/employees/attendance') ?>" method="GET" class="row g-2 align-items-center">
        <div class="col-auto">
            <label for="date" class="col-form-label small fw-semibold">Attendance Date:</label>
        </div>
        <div class="col-auto">
            <input type="date" id="date" name="date" class="form-control form-control-sm" value="<?= esc($date) ?>" required max="<?= date('Y-m-d') ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary btn-sm px-3 shadow-sm">
                <i class="bi bi-funnel-fill me-1"></i> Retrieve Sheet
            </button>
        </div>
    </form>
</div>

<!-- Attendance Form -->
<form action="<?= site_url('/admin/employees/attendance/save') ?>" method="POST">
    <?= csrf_field() ?>
    <input type="hidden" name="date" value="<?= esc($date) ?>">

    <div class="table-responsive border-0 shadow-sm rounded-3">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-slate">
                <tr>
                    <th>Employee Name</th>
                    <th>Branch Mapped</th>
                    <th>Designation</th>
                    <th>Attendance Status</th>
                    <th>Check In Time</th>
                    <th>Check Out Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                            No employees enrolled in the system to log attendance.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($records as $row): ?>
                        <?php $empId = (int)$row['employee_id']; ?>
                        <tr>
                            <td class="fw-semibold text-slate"><?= esc($row['username']) ?></td>
                            <td class="small text-muted"><?= esc($row['branch_name'] ?? 'General') ?></td>
                            <td><span class="badge bg-secondary"><?= esc($row['role_name']) ?></span></td>
                            <td>
                                <select name="status[<?= $empId ?>]" class="form-control form-control-sm form-select" style="max-width: 150px;" required>
                                    <option value="present" <?= $row['status'] === 'present' ? 'selected' : '' ?>>Present</option>
                                    <option value="absent" <?= $row['status'] === 'absent' ? 'selected' : '' ?>>Absent</option>
                                    <option value="late" <?= $row['status'] === 'late' ? 'selected' : '' ?>>Late</option>
                                    <option value="leave" <?= $row['status'] === 'leave' ? 'selected' : '' ?>>Leave</option>
                                </select>
                            </td>
                            <td>
                                <input type="time" name="check_in[<?= $empId ?>]" class="form-control form-control-sm" style="max-width: 130px;" value="<?= esc($row['check_in_time'] ? date('H:i', strtotime($row['check_in_time'])) : '') ?>">
                            </td>
                            <td>
                                <input type="time" name="check_out[<?= $empId ?>]" class="form-control form-control-sm" style="max-width: 130px;" value="<?= esc($row['check_out_time'] ? date('H:i', strtotime($row['check_out_time'])) : '') ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (!empty($records)): ?>
        <div class="text-end mt-4">
            <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm">
                <i class="bi bi-cloud-check-fill me-1"></i> Save Daily Attendance Sheet
            </button>
        </div>
    <?php endif; ?>
</form>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
