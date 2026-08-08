<?php 
$activePage = 'branches';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Action Row -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0 small">Create, edit, delete, and manage clinic branch systems.</p>
    <a href="<?= site_url('/admin/branches/create') ?>" class="btn btn-primary btn-sm px-3 shadow-sm">
        <i class="bi bi-plus-circle me-1"></i> Add Branch
    </a>
</div>

<!-- Table list -->
<div class="table-responsive border-0 shadow-sm rounded-3">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light text-slate">
            <tr>
                <th>Logo</th>
                <th>Branch Details</th>
                <th>Contact Info</th>
                <th>Hours</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($branches)): ?>
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-building fs-3 d-block mb-2"></i>
                        No branches configured. Click "Add Branch" to create one.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($branches as $branch): ?>
                    <tr>
                        <td>
                            <?php if ($branch['logo']): ?>
                                <img src="<?= site_url($branch['logo']) ?>" alt="Logo" class="rounded border" style="width: 50px; height: 50px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light rounded border d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-building text-secondary fs-4"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="fw-bold text-slate"><?= esc($branch['name']) ?></div>
                            <span class="text-muted small" style="font-size: 0.8rem;"><?= esc($branch['address']) ?></span>
                        </td>
                        <td>
                            <div class="small"><i class="bi bi-telephone text-muted me-1"></i> <?= esc($branch['phone']) ?></div>
                            <div class="small text-danger" style="font-size: 0.8rem;"><i class="bi bi-exclamation-circle me-1"></i> Emer: <?= esc($branch['emergency_number']) ?></div>
                            <div class="small text-muted" style="font-size: 0.8rem;"><i class="bi bi-envelope me-1"></i> <?= esc($branch['email']) ?></div>
                        </td>
                        <td class="small text-muted"><?= esc($branch['opening_hours']) ?></td>
                        <td>
                            <?php if ($branch['status'] === 'active'): ?>
                                <span class="badge badge-active px-2.5 py-1.5 rounded">Active</span>
                            <?php else: ?>
                                <span class="badge badge-inactive px-2.5 py-1.5 rounded">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end text-nowrap">
                            <?php if (\App\Helpers\Permission::has('view_branch_dashboard')): ?>
                                <a href="<?= site_url('/admin/branches/dashboard/' . $branch['id']) ?>" class="btn btn-sm btn-outline-success me-1 px-2.5 py-1" title="View Branch Dashboard">
                                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                                </a>
                            <?php endif; ?>
                            
                            <a href="<?= site_url('/admin/branches/edit/' . $branch['id']) ?>" class="btn btn-sm btn-light border me-1 px-2 py-1" title="Edit">
                                <i class="bi bi-pencil-fill text-secondary"></i>
                            </a>
                            
                            <a href="<?= site_url('/admin/branches/delete/' . $branch['id']) ?>" class="btn btn-sm btn-light border px-2 py-1" onclick="return confirm('Are you sure you want to delete this branch? This action is irreversible.');" title="Delete">
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
