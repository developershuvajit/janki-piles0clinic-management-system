<?php 
$activePage = 'leaves';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- ============================================
     PAGE CSS
     ============================================ -->
<link rel="stylesheet" href="<?= asset('css/datatable.css') ?>">

<style>
/* Custom styling for leave page */
.leave-form-card {
    border-radius: 12px;
    background: #ffffff;
}

.leave-form-card .form-label {
    font-weight: 600;
    font-size: 0.8rem;
    color: #475569;
    margin-bottom: 0.25rem;
}

.leave-form-card .form-control,
.leave-form-card .form-select {
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.leave-form-card .form-control:focus,
.leave-form-card .form-select:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.leave-form-card textarea.form-control {
    resize: vertical;
    min-height: 80px;
}

.datatable-wrapper .datatable-header {
    padding: 0 0 1rem 0;
}

/* Table styling */
.table-custom td {
    vertical-align: middle;
    padding: 0.75rem 0.5rem;
}

.table-custom .sno {
    width: 40px;
    text-align: center;
    color: #94a3b8;
    font-weight: 500;
    font-size: 0.85rem;
}

/* Status badge styling */
.badge-status-pending {
    background: rgba(251, 191, 36, 0.1);
    color: #d97706;
    border: 1px solid rgba(251, 191, 36, 0.25);
    padding: 0.35rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-status-approved {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
    border: 1px solid rgba(16, 185, 129, 0.25);
    padding: 0.35rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-status-rejected {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
    border: 1px solid rgba(239, 68, 68, 0.25);
    padding: 0.35rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Action buttons */
.action-group .btn-action {
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #64748b;
    transition: all 0.2s ease;
    text-decoration: none;
    font-size: 0.9rem;
    margin: 0 2px;
}

.action-group .btn-action:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    transform: translateY(-1px);
}

.action-group .btn-action.approve {
    color: #10b981;
    border-color: #d1fae5;
}

.action-group .btn-action.approve:hover {
    background: #d1fae5;
    border-color: #10b981;
}

.action-group .btn-action.reject {
    color: #ef4444;
    border-color: #fee2e2;
}

.action-group .btn-action.reject:hover {
    background: #fee2e2;
    border-color: #ef4444;
}

/* Leave type badge */
.leave-type-badge {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .leave-form-card {
        margin-bottom: 1.5rem;
    }
}
</style>

<!-- ============================================
     PAGE CONTENT
     ============================================ -->
<div class="row g-4">
    <!-- Left Column: Apply for Leave Form -->
    <div class="col-lg-4">
        <div class="leave-form-card card border-0 shadow-sm p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-calendar-minus text-success me-2"></i>Apply for Leave</h6>
            
            <form action="<?= site_url('/admin/attendance/leaves/apply') ?>" method="POST">
                <?= csrf_field() ?>

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
    <div class="col-lg-8">
        <div class="datatable-wrapper">
            <div class="datatable-header">
                <h5>Leaves Application Directory <small><?= count($leaves ?? []) ?> applications</small></h5>
            </div>

            <div class="table-responsive">
                <table id="leavesTable" class="table-custom" style="width:100%">
                    <thead>
                        <tr>
                            <th class="sno">#</th>
                            <th style="min-width:160px;">Employee Details</th>
                            <th style="width:120px;">Leave Type</th>
                            <th style="min-width:150px;">Timeframe</th>
                            <th style="min-width:120px;">Reason</th>
                            <th style="width:110px;">Status</th>
                            <th style="width:110px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($leaves)):
                            $sn = 1;
                            foreach ($leaves as $lv): ?>
                                <tr>
                                    <td class="sno"><?= $sn++ ?></td>
                                    <td>
                                        <div class="fw-bold text-slate"><?= esc($lv['employee_name']) ?></div>
                                        <span class="text-muted small" style="font-size: 0.78rem;"><?= esc($lv['role_name']) ?></span>
                                    </td>
                                    <td>
                                        <span class="leave-type-badge"><?= esc(ucfirst($lv['leave_type'])) ?></span>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.85rem;">
                                            <strong>From:</strong> <?= esc($lv['start_date']) ?><br>
                                            <strong>To:</strong> <?= esc($lv['end_date']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted small" style="font-size: 0.8rem;" title="<?= esc($lv['reason']) ?>">
                                            <?= esc(substr($lv['reason'], 0, 50)) ?><?= strlen($lv['reason']) > 50 ? '...' : '' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($lv['status'] === 'approved'): ?>
                                            <span class="badge-status-approved">Approved</span>
                                        <?php elseif ($lv['status'] === 'rejected'): ?>
                                            <span class="badge-status-rejected">Rejected</span>
                                        <?php else: ?>
                                            <span class="badge-status-pending">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-group">
                                            <?php if ($lv['status'] === 'pending'): ?>
                                                <a href="<?= site_url('/admin/attendance/leaves/approve/' . $lv['id']) ?>" 
                                                   class="btn-action approve" 
                                                   title="Approve">
                                                    <i class="bi bi-check-lg"></i>
                                                </a>
                                                <a href="<?= site_url('/admin/attendance/leaves/reject/' . $lv['id']) ?>" 
                                                   class="btn-action reject" 
                                                   title="Reject">
                                                    <i class="bi bi-x-lg"></i>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td colspan="7" style="text-align:center;padding:2.5rem 1rem;color:#94a3b8;">
                                    <i class="bi bi-calendar-check fs-3 d-block mb-2"></i>
                                    No leave applications lodged.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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

<script>
$(document).ready(function() {
    $('#leavesTable').DataTable({
        pageLength: 25,
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        order: [[0, 'asc']]
    });
});
</script>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>