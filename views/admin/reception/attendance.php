<?php 
$activePage = 'reception_attendance';
include VIEWS_PATH . '/layout/reception_header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-slate mb-1"><i class="bi bi-clock-history text-success me-2"></i>Daily Staff Attendance Register</h4>
        <p class="text-muted small mb-0">Record staff check-ins, check-outs, shift attendance, and daily roster logs.</p>
    </div>
    <div>
        <form action="<?= site_url('/reception/attendance') ?>" method="GET" class="d-flex gap-2">
            <input type="date" name="date" class="form-control form-control-sm" value="<?= esc($date) ?>" onchange="this.form.submit()">
        </form>
    </div>
</div>

<!-- Today's Attendance Summary Widgets -->
<div class="row g-3 mb-4">
    <div class="col-md-2.4">
        <div class="card p-3 border-0 shadow-sm rounded-4 text-center">
            <div class="small text-muted text-uppercase fw-bold">Total Staff</div>
            <h3 class="fw-bold text-slate mb-0"><?= esc((string)($summary['total_staff'] ?? 0)) ?></h3>
        </div>
    </div>
    <div class="col-md-2.4">
        <div class="card p-3 border-0 shadow-sm rounded-4 text-center border-start border-4 border-success">
            <div class="small text-muted text-uppercase fw-bold">Present</div>
            <h3 class="fw-bold text-success mb-0"><?= esc((string)($summary['present'] ?? 0)) ?></h3>
        </div>
    </div>
    <div class="col-md-2.4">
        <div class="card p-3 border-0 shadow-sm rounded-4 text-center border-start border-4 border-warning">
            <div class="small text-muted text-uppercase fw-bold">Late Entry</div>
            <h3 class="fw-bold text-warning mb-0"><?= esc((string)($summary['late'] ?? 0)) ?></h3>
        </div>
    </div>
    <div class="col-md-2.4">
        <div class="card p-3 border-0 shadow-sm rounded-4 text-center border-start border-4 border-info">
            <div class="small text-muted text-uppercase fw-bold">On Leave</div>
            <h3 class="fw-bold text-info mb-0"><?= esc((string)($summary['leave'] ?? 0)) ?></h3>
        </div>
    </div>
    <div class="col-md-2.4">
        <div class="card p-3 border-0 shadow-sm rounded-4 text-center border-start border-4 border-danger">
            <div class="small text-muted text-uppercase fw-bold">Absent</div>
            <h3 class="fw-bold text-danger mb-0"><?= esc((string)($summary['absent'] ?? 0)) ?></h3>
        </div>
    </div>
</div>

<!-- Roster List -->
<div class="card border-0 shadow-sm p-4 rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:0.88rem;">
            <thead class="bg-light">
                <tr>
                    <th>Staff Name</th>
                    <th>Role</th>
                    <th>Date</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Status</th>
                    <th>Notes</th>
                    <th>Mark Attendance</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roster as $r): ?>
                    <tr>
                        <td class="fw-bold text-dark"><?= esc($r['username']) ?></td>
                        <td><span class="badge bg-light text-slate border"><?= esc($r['role_name']) ?></span></td>
                        <td><?= esc(date('d M Y', strtotime($date))) ?></td>
                        <td><span class="badge bg-light text-dark font-monospace"><?= esc($r['check_in'] ? date('h:i A', strtotime($r['check_in'])) : '--:--') ?></span></td>
                        <td><span class="badge bg-light text-dark font-monospace"><?= esc($r['check_out'] ? date('h:i A', strtotime($r['check_out'])) : '--:--') ?></span></td>
                        <td>
                            <?php 
                                $status = $r['status'] ?? 'not_marked';
                                $badgeClass = 'bg-secondary';
                                if ($status === 'present') $badgeClass = 'bg-success';
                                elseif ($status === 'late') $badgeClass = 'bg-warning text-dark';
                                elseif ($status === 'half_day') $badgeClass = 'bg-info text-dark';
                                elseif ($status === 'leave') $badgeClass = 'bg-primary';
                                elseif ($status === 'absent') $badgeClass = 'bg-danger';
                            ?>
                            <span class="badge <?= $badgeClass ?> text-uppercase"><?= esc(str_replace('_', ' ', $status)) ?></span>
                        </td>
                        <td class="small text-muted" style="max-width:180px;"><?= esc($r['notes'] ?: '-') ?></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1" style="font-size:0.75rem;" 
                                    onclick="openMarkModal('<?= $r['user_id'] ?>', '<?= esc($r['username']) ?>', '<?= esc($r['status'] ?? 'present') ?>', '<?= esc($r['check_in'] ?? '') ?>', '<?= esc($r['check_out'] ?? '') ?>', '<?= esc($r['notes'] ?? '') ?>')">
                                <i class="bi bi-pencil-square me-1"></i> Update
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Attendance Mark Modal -->
<div class="modal fade" id="markAttendanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-slate"><i class="bi bi-clock-history text-success me-2"></i>Update Staff Attendance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url('/reception/attendance/save') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="user_id" id="modal_user_id">
                <input type="hidden" name="date" value="<?= esc($date) ?>">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Staff Member</label>
                        <input type="text" class="form-control" id="modal_username" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="modal_status" class="form-label small fw-bold">Attendance Status</label>
                        <select class="form-select" id="modal_status" name="status">
                            <option value="present">Present</option>
                            <option value="late">Late Entry</option>
                            <option value="half_day">Half Day</option>
                            <option value="leave">On Leave</option>
                            <option value="absent">Absent</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label for="modal_check_in" class="form-label small fw-bold">Check-In Time</label>
                            <input type="time" class="form-control" id="modal_check_in" name="check_in">
                        </div>
                        <div class="col-md-6">
                            <label for="modal_check_out" class="form-label small fw-bold">Check-Out Time</label>
                            <input type="time" class="form-control" id="modal_check_out" name="check_out">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="modal_notes" class="form-label small fw-bold">Remarks / Shift Notes</label>
                        <input type="text" class="form-control" id="modal_notes" name="notes" placeholder="e.g. Traffic delay, approved leave request">
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-emerald btn-sm px-4 fw-bold shadow-sm">
                        <i class="bi bi-check-lg me-1"></i> Save Attendance
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openMarkModal(userId, username, status, checkIn, checkOut, notes) {
    document.getElementById('modal_user_id').value = userId;
    document.getElementById('modal_username').value = username;
    document.getElementById('modal_status').value = status || 'present';
    document.getElementById('modal_check_in').value = checkIn || '';
    document.getElementById('modal_check_out').value = checkOut || '';
    document.getElementById('modal_notes').value = notes || '';
    
    var modal = new bootstrap.Modal(document.getElementById('markAttendanceModal'));
    modal.show();
}
</script>

<?php include VIEWS_PATH . '/layout/reception_footer.php'; ?>
