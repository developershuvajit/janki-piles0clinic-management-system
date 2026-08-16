<?php 
$activePage = 'leaves';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<div class="row">
    <!-- Left Column: Apply for Leave Form -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm p-4 text-slate">
            <h6 class="fw-bold mb-3"><i class="bi bi-calendar-minus text-success me-2"></i>Apply for Leave</h6>
            
            <form action="<?= site_url('/admin/attendance/leaves/apply') ?>" method="POST">
                <?= csrf_field() ?>

                <!-- ✅ NEW: Employee Select Dropdown -->
                <div class="mb-3">
                    <label for="employee_id" class="form-label small fw-semibold">Select Employee</label>
                    <select class="form-control form-control-sm form-select" id="employee_id" name="employee_id" required>
                        <option value="">-- Select Employee --</option>
                        <?php foreach ($employees as $emp): ?>
                            <option value="<?= $emp['id'] ?>">
                                <?= esc($emp['username'] ?? $emp['employee_name'] ?? 'Unknown') ?> 
                                (<?= esc($emp['role_name'] ?? 'Staff') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="leave_type" class="form-label small fw-semibold">Leave Type</label>
                    <select class="form-control form-control-sm form-select" id="leave_type" name="leave_type" required>
                        <option value="sick">Sick Leave</option>
                        <option value="casual">Casual Leave</option>
                        <option value="annual">Annual Leave</option>
                        <option value="unpaid">Unpaid Leave</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="start_date" class="form-label small fw-semibold">Start Date</label>
                    <input type="date" class="form-control form-control-sm" id="start_date" name="start_date" required min="<?= date('Y-m-d') ?>">
                </div>

                <div class="mb-3">
                    <label for="end_date" class="form-label small fw-semibold">End Date</label>
                    <input type="date" class="form-control form-control-sm" id="end_date" name="end_date" required min="<?= date('Y-m-d') ?>">
                </div>

                <div class="mb-4">
                    <label for="reason" class="form-label small fw-semibold">Reason</label>
                    <textarea class="form-control" id="reason" name="reason" rows="3" required placeholder="Describe reason for leave request..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-sm w-100 shadow-sm">
                    <i class="bi bi-send me-1"></i> Submit Leave Request
                </button>
            </form>
        </div>
    </div>

    <!-- Right Column: Leaves Directory List -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm p-4 text-slate">
            <h6 class="fw-bold mb-3"><i class="bi bi-list-check text-success me-2"></i>Leaves Application Directory</h6>
            
            <div class="table-responsive border-0 shadow-none">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.82rem;">
                    <thead>
                        <tr>
                            <th>Employee Details</th>
                            <th>Leave details</th>
                            <th>Timeframe</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($leaves)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No leave applications lodged.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($leaves as $lv): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-slate"><?= esc($lv['employee_name']) ?></div>
                                        <span class="text-muted small"><?= esc($lv['role_name']) ?></span>
                                    </td>
                                    <td><span class="badge bg-light text-slate border"><?= esc(ucfirst($lv['leave_type'])) ?></span></td>
                                    <td>
                                        <?= esc($lv['start_date']) ?> to<br>
                                        <?= esc($lv['end_date']) ?>
                                    </td>
                                    <td class="small text-muted text-truncate" style="max-width: 150px;" title="<?= esc($lv['reason']) ?>"><?= esc($lv['reason']) ?></td>
                                    <td>
                                        <?php if ($lv['status'] === 'approved'): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded">Approved</span>
                                        <?php elseif ($lv['status'] === 'rejected'): ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded">Rejected</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 rounded">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <?php if ($lv['status'] === 'pending'): ?>
                                            <a href="<?= site_url('/admin/attendance/leaves/approve/' . $lv['id']) ?>" class="btn btn-sm btn-success px-2 py-0.5 me-1 text-white shadow-sm" title="Approve">
                                                <i class="bi bi-check-lg"></i>
                                            </a>
                                            <a href="<?= site_url('/admin/attendance/leaves/reject/' . $lv['id']) ?>" class="btn btn-sm btn-outline-danger px-2 py-0.5" title="Reject">
                                                <i class="bi bi-x-lg"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
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
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>