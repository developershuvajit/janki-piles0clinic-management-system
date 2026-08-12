<?php 
$activePage = 'attendance';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Register Header -->
<div class="d-flex justify-content-between align-items-center mb-4 text-slate">
    <div>
        <h5 class="fw-bold mb-1"><i class="bi bi-calendar-check text-success me-2"></i>Daily Attendance Register</h5>
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

<form action="<?= site_url('/admin/employees/attendance/save') ?>" method="POST">
    <?= csrf_field() ?>
    <input type="hidden" name="date" value="<?= esc($date) ?>">

    <div class="card border-0 shadow-sm p-4 mb-3">
        <div class="table-responsive border-0 shadow-none">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                <thead>
                    <tr>
                        <th>Employee Details</th>
                        <th>Role</th>
                        <th>Attendance Status</th>
                        <th>Clock In Time</th>
                        <th>Clock Out Time</th>
                        <th>Notes / Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($roster)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No active employees found to log attendance.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($roster as $row): ?>
                            <tr>
                                <td class="fw-semibold text-slate">
                                    <?= esc($row['employee_name']) ?>
                                    <?php if (!empty($row['check_in'])): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 ms-1" style="font-size:0.55rem;">
                                            <i class="bi bi-qr-code me-1"></i> QR
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted"><?= esc($row['role_name']) ?></td>
                                <td>
                                    <select class="form-control form-control-sm form-select" name="attendance[<?= $row['employee_id'] ?>][status]" style="max-width: 150px;">
                                        <option value="present" <?= $row['status'] === 'present' ? 'selected' : '' ?>>Present</option>
                                        <option value="absent" <?= $row['status'] === 'absent' ? 'selected' : '' ?>>Absent</option>
                                        <option value="leave" <?= $row['status'] === 'leave' ? 'selected' : '' ?>>Leave Approved</option>
                                        <option value="late" <?= $row['status'] === 'late' ? 'selected' : '' ?>>Late Arrival</option>
                                        <option value="holiday" <?= $row['status'] === 'holiday' ? 'selected' : '' ?>>Holiday</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="time" class="form-control form-control-sm" name="attendance[<?= $row['employee_id'] ?>][check_in]" value="<?= esc($row['check_in'] ? substr($row['check_in'], 0, 5) : '') ?>" style="max-width: 120px;">
                                </td>
                                <td>
                                    <input type="time" class="form-control form-control-sm" name="attendance[<?= $row['employee_id'] ?>][check_out]" value="<?= esc($row['check_out'] ? substr($row['check_out'], 0, 5) : '') ?>" style="max-width: 120px;">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="attendance[<?= $row['employee_id'] ?>][notes]" value="<?= esc($row['notes'] ?? '') ?>" placeholder="e.g. Late due to transit delay">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-end">
        <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
            <i class="bi bi-save me-1"></i> Save Daily Attendance
        </button>
    </div>
</form>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>