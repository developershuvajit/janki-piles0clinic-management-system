<?php 
$activePage = 'employees';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Action Row -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0 small">Enroll and manage employee files, salaries, shifts, and branches.</p>
    <a href="<?= site_url('/admin/employees/create') ?>" class="btn btn-primary btn-sm px-3 shadow-sm">
        <i class="bi bi-plus-circle me-1"></i> Enroll Employee
    </a>
</div>

<!-- Roster Table -->
<div class="table-responsive border-0 shadow-sm rounded-3">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light text-slate">
            <tr>
                <th>Employee</th>
                <th>Contact Details</th>
                <th>Role & Branch</th>
                <th>Salary & Shifts</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($employees)): ?>
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-people fs-3 d-block mb-2"></i>
                        No employees currently enrolled in the system.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($employees as $emp): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if ($emp['photo']): ?>
                                    <img src="<?= site_url($emp['photo']) ?>" alt="Photo" class="rounded-circle border me-2" style="width: 45px; height: 45px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light rounded-circle border d-flex align-items-center justify-content-center me-2" style="width: 45px; height: 45px;">
                                        <i class="bi bi-person text-secondary fs-4"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="fw-bold text-slate"><?= esc($emp['username']) ?></div>
                                    <span class="text-muted x-small" style="font-size: 0.75rem;">ID: EMP-<?= esc((string)$emp['id']) ?></span>
                                </div>
                            </div>
                        </td>
                        <td class="small"><?= esc($emp['email']) ?></td>
                        <td>
                            <div class="fw-semibold small text-slate"><?= esc($emp['role_name']) ?></div>
                            <span class="badge bg-light text-secondary border mt-1" style="font-size: 0.7rem;">
                                <?= $emp['branch_name'] ? esc($emp['branch_name']) : 'Multi-Branch' ?>
                            </span>
                        </td>
                        <td>
                            <div class="small fw-semibold">₹<?= esc(number_format((float)$emp['salary'], 2)) ?></div>
                            <span class="text-muted" style="font-size: 0.75rem;">
                                <?= esc(date('h:i A', strtotime($emp['shift_start']))) ?> - <?= esc(date('h:i A', strtotime($emp['shift_end']))) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($emp['user_status'] === 'active'): ?>
                                <span class="badge badge-active px-2 py-1 rounded">Active</span>
                            <?php else: ?>
                                <span class="badge badge-inactive px-2 py-1 rounded">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="<?= site_url('/admin/employees/edit/' . $emp['id']) ?>" class="btn btn-sm btn-light border me-1 px-2.5 py-1" title="Edit Profile & Docs">
                                <i class="bi bi-pencil-fill text-secondary me-1"></i> Edit
                            </a>
                            <a href="<?= site_url('/admin/employees/delete/' . $emp['id']) ?>" class="btn btn-sm btn-light border px-2.5 py-1" onclick="return confirm('Are you sure you want to terminate this employee? All related credentials documents will be deleted.');" title="Delete">
                                <i class="bi bi-trash-fill text-danger"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
