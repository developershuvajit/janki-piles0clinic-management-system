<?php 
$activePage = 'roles';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<style>
    .form-section {
        background: #fff;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        margin-bottom: 1.5rem;
    }
    .permission-module {
        font-weight: 600;
        color: #2563eb;
        font-size: 0.85rem;
        margin-top: 0.8rem;
        margin-bottom: 0.5rem;
        padding: 0.3rem 0.5rem;
        background: #f8fafc;
        border-radius: 6px;
        border-left: 3px solid #2563eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .permission-module .badge-soft {
        font-size: 0.65rem;
        font-weight: 400;
        background: #e2e8f0;
        padding: 0.1rem 0.6rem;
        border-radius: 40px;
        color: #475569;
    }
    .permission-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 0.3rem 0.8rem;
        padding-left: 0.5rem;
    }
    .permission-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.78rem;
        padding: 0.15rem 0;
    }
    .permission-item input[type="checkbox"] {
        width: 14px;
        height: 14px;
        accent-color: #2563eb;
        cursor: pointer;
        flex-shrink: 0;
        margin: 0;
    }
    .permission-item label {
        cursor: pointer;
        color: #1e293b;
        margin: 0;
        font-weight: 400;
    }
    .btn-back {
        border-radius: 40px;
        padding: 0.3rem 1.2rem;
        font-size: 0.78rem;
        border: 1px solid #e2e8f0;
        background: transparent;
        color: #1e293b;
        transition: all 0.15s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }
    .btn-back:hover {
        background: #f5f7fa;
        border-color: #cbd5e1;
        color: #1e293b;
    }
    .btn-register {
        border-radius: 40px;
        padding: 0.4rem 1.5rem;
        font-size: 0.78rem;
        background: #2563eb;
        border: none;
        color: #fff;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        text-decoration: none;
        cursor: pointer;
    }
    .btn-register:hover {
        background: #1d4ed8;
        color: #fff;
    }
    .btn-danger-clean {
        border-radius: 40px;
        padding: 0.3rem 1.2rem;
        font-size: 0.78rem;
        background: #ef4444;
        border: none;
        color: #fff;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        text-decoration: none;
        cursor: pointer;
    }
    .btn-danger-clean:hover {
        background: #dc2626;
        color: #fff;
    }
    .slug-display {
        font-size: 0.85rem;
        color: #475569;
        background: #f1f4f8;
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        display: inline-block;
        border: 1px solid #e2e8f0;
        font-weight: 500;
    }
    .form-label {
        font-weight: 600;
        font-size: 0.8rem;
        color: #0b1a2b;
    }
    .datatable-wrapper {
        max-width: 100%;
        overflow-x: hidden;
    }
    .datatable-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    .datatable-header h5 {
        font-weight: 700;
        color: #0b1a2b;
        margin: 0;
        font-size: 1.05rem;
    }
    .text-danger {
        color: #ef4444;
    }
    hr {
        border-color: #eef2f6;
        margin: 1.5rem 0;
    }
    .badge-soft {
        background: #f1f4f8;
        color: #1e293b;
        padding: 0.1rem 0.6rem;
        border-radius: 40px;
        font-size: 0.65rem;
    }
    .select-all-container {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.3rem 0.8rem;
        background: #f8fafc;
        border-radius: 6px;
        border: 1px solid #eef2f6;
    }
    .select-all-container input[type="checkbox"] {
        width: 14px;
        height: 14px;
        accent-color: #2563eb;
        cursor: pointer;
        margin: 0;
    }
    .select-all-container label {
        font-size: 0.78rem;
        font-weight: 600;
        color: #0b1a2b;
        margin: 0;
        cursor: pointer;
    }
    .form-control-sm {
        font-size: 0.85rem;
        padding: 0.4rem 0.75rem;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }
    .form-control-sm:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
    }
</style>

<div class="datatable-wrapper mt-4">
    <div class="datatable-header">
        <h5><i class="bi bi-pencil-square me-2"></i>Edit Role: <?= esc($role['name']) ?></h5>
        <a href="<?= site_url('/admin/roles') ?>" class="btn-back">
            <i class="bi bi-arrow-left"></i> Back to Roles
        </a>
    </div>

    <div class="form-section">
        <form action="<?= site_url("/admin/roles/update/{$role['id']}") ?>" method="POST">
            <?= csrf_field() ?>
            
            <!-- Role Name & Slug -->
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="name" required 
                               value="<?= esc($role['name']) ?>" placeholder="Enter role name">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Slug <span class="badge-soft ms-1">Read Only</span></label>
                        <div class="slug-display">
                            <i class="bi bi-tag me-1"></i> <?= esc($role['slug']) ?>
                        </div>
                        <small class="text-muted">Slug cannot be changed after creation</small>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control form-control-sm" name="description" rows="2" 
                          placeholder="Brief description of this role"><?= esc($role['description'] ?? '') ?></textarea>
            </div>

            <hr>

            <!-- Permissions Section -->
            <div class="mb-3">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div>
                        <label class="form-label fw-semibold mb-0">Assign Permissions</label>
                        <small class="text-muted d-block">Select the permissions this role should have</small>
                    </div>
                    <div class="select-all-container">
                        <input type="checkbox" id="select-all-permissions" onchange="toggleAllPermissions(this)">
                        <label for="select-all-permissions">Select All</label>
                    </div>
                </div>
                
                <?php 
                $currentModule = '';
                $modulePermissions = [];
                
                // Group permissions by module
                foreach ($permissions as $perm) {
                    $modulePermissions[$perm['module']][] = $perm;
                }
                
                foreach ($modulePermissions as $module => $perms): 
                ?>
                    <div class="permission-module">
                        <span><i class="bi bi-folder me-1"></i> <?= esc(ucfirst(str_replace('_', ' ', $module))) ?></span>
                        <span class="badge-soft" id="module-count-<?= md5($module) ?>">
                            <span class="selected-count">0</span> / <?= count($perms) ?> selected
                        </span>
                    </div>
                    <div class="permission-grid" data-module="<?= md5($module) ?>">
                        <?php foreach ($perms as $perm): 
                            $checked = in_array($perm['id'], $rolePermIds) ? 'checked' : '';
                        ?>
                            <div class="permission-item">
                                <input type="checkbox" class="form-check-input permission-checkbox" 
                                       name="permissions[]" value="<?= $perm['id'] ?>" 
                                       id="perm_<?= $perm['id'] ?>" <?= $checked ?>
                                       data-module="<?= md5($module) ?>"
                                       onchange="updateModuleCount(this)">
                                <label for="perm_<?= $perm['id'] ?>"><?= esc($perm['name']) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <hr>

            <!-- Action Buttons -->
            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="submit" class="btn-register">
                    <i class="bi bi-save"></i> Update Role
                </button>
                <a href="<?= site_url('/admin/roles') ?>" class="btn-back">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
                <?php 
                $defaultRoles = [1, 2, 3, 4];
                if (!in_array($role['id'], $defaultRoles)): 
                ?>
                <button type="button" class="btn-danger-clean ms-auto" 
                        onclick="confirmDelete(<?= $role['id'] ?>, '<?= esc($role['name']) ?>')">
                    <i class="bi bi-trash"></i> Delete Role
                </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript for Select All & Module Count -->
<script>
    // Update module count when checkbox changes
    function updateModuleCount(checkbox) {
        const moduleHash = checkbox.dataset.module;
        const grid = document.querySelector(`.permission-grid[data-module="${moduleHash}"]`);
        if (!grid) return;
        
        const checkboxes = grid.querySelectorAll('.permission-checkbox');
        const checked = grid.querySelectorAll('.permission-checkbox:checked');
        const countLabel = document.getElementById(`module-count-${moduleHash}`);
        
        if (countLabel) {
            countLabel.innerHTML = `<span class="selected-count">${checked.length}</span> / ${checkboxes.length} selected`;
        }
    }

    // Toggle all permissions
    function toggleAllPermissions(masterCheckbox) {
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = masterCheckbox.checked;
            updateModuleCount(cb);
        });
    }

    // Update all module counts on page load
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.permission-checkbox').forEach(cb => {
            updateModuleCount(cb);
        });
        
        // Update master checkbox state
        const allCheckboxes = document.querySelectorAll('.permission-checkbox');
        const selectAll = document.getElementById('select-all-permissions');
        if (selectAll && allCheckboxes.length > 0) {
            const allChecked = document.querySelectorAll('.permission-checkbox:checked');
            selectAll.checked = allChecked.length === allCheckboxes.length;
        }
    });

    // Confirm delete
    function confirmDelete(id, name) {
        if (confirm(`Are you sure you want to delete the role "${name}"? This action cannot be undone.`)) {
            window.location.href = '<?= site_url('/admin/roles/delete') ?>/' + id;
        }
    }

    // Update master checkbox when individual checkboxes change
    document.addEventListener('DOMContentLoaded', function() {
        const allCheckboxes = document.querySelectorAll('.permission-checkbox');
        const selectAll = document.getElementById('select-all-permissions');
        
        allCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                if (selectAll) {
                    const allChecked = document.querySelectorAll('.permission-checkbox:checked');
                    selectAll.checked = allChecked.length === allCheckboxes.length;
                }
            });
        });
    });
</script>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>