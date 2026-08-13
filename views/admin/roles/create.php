 <?php 
$activePage = 'roles';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<div class="card border-0 shadow-sm p-4 mt-5">
    <form action="<?= site_url('/admin/roles/save') ?>" method="POST">
        <?= csrf_field() ?>
        
        <h5 class="fw-bold text-slate mb-3"><i class="bi bi-shield-plus text-primary me-2"></i>Create New Role</h5>
        
        <div class="row g-3">
            <!-- Left Column - Form Fields -->
            <div class="col-md-6">
                <!-- Role Name -->
                <div class="mb-3">
                    <label for="roleName" class="form-label small fw-semibold">Role Name <span class="text-danger">*</span></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="bi bi-tag text-muted"></i></span>
                        <input type="text" class="form-control" id="roleName" name="name" required 
                               placeholder="e.g., Nurse, Lab Technician">
                    </div>
                </div>
                
                <!-- Slug -->
                <div class="mb-3">
                    <label for="roleSlug" class="form-label small fw-semibold">Slug <span class="text-danger">*</span></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="bi bi-link-45deg text-muted"></i></span>
                        <input type="text" class="form-control" id="roleSlug" name="slug" required 
                               placeholder="e.g., nurse, lab_technician">
                    </div>
                    <div class="form-text x-small text-muted" style="font-size: 0.7rem;">Unique identifier (no spaces, lowercase, underscores)</div>
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label for="description" class="form-label small fw-semibold">Description</label>
                    <textarea class="form-control form-control-sm" id="description" name="description" rows="3" 
                              placeholder="Brief description of this role"></textarea>
                </div>
            </div>

            <!-- Right Column - Permissions -->
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-slate mb-0"><i class="bi bi-lock text-primary me-2"></i>Assign Permissions</h6>
                    <span class="badge bg-light text-muted small" id="selectedCount">0 selected</span>
                </div>
                
                <div class="bg-light p-3 rounded" style="height: 280px; overflow-y: auto;">
                    <?php 
                    $currentModule = '';
                    foreach ($permissions as $perm): 
                    ?>
                        <?php if ($currentModule !== $perm['module']): ?>
                            <?php if ($currentModule !== ''): ?>
                                </div> <!-- Close previous module grid -->
                            <?php endif; ?>
                            <?php $currentModule = $perm['module']; ?>
                            <div class="fw-semibold text-primary small mt-2 mb-1">
                                <i class="bi bi-folder me-1"></i> <?= esc($currentModule) ?>
                            </div>
                            <div class="row g-1 mb-2">
                        <?php endif; ?>
                        
                        <div class="col-6">
                            <div class="form-check form-check-sm">
                                <input type="checkbox" class="form-check-input permission-checkbox" 
                                       name="permissions[]" value="<?= $perm['id'] ?>" 
                                       id="perm_<?= $perm['id'] ?>">
                                <label class="form-check-label small" for="perm_<?= $perm['id'] ?>">
                                    <?= esc($perm['name']) ?>
                                </label>
                            </div>
                        </div>
                        
                    <?php endforeach; ?>
                    <?php if (!empty($permissions)): ?>
                            </div> <!-- Close last module grid -->
                        </div> <!-- Close bg-light -->
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="text-end pt-3 mt-2 border-top">
            <a href="<?= site_url('/admin/roles') ?>" class="btn btn-outline-secondary btn-sm px-3 me-2">
                <i class="bi bi-x-circle me-1"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
                <i class="bi bi-save me-1"></i> Create Role
            </button>
        </div>
    </form>
</div>

<script>
// Auto-generate slug from role name
document.getElementById('roleName').addEventListener('keyup', function() {
    const slug = document.getElementById('roleSlug');
    if (!slug.value || slug.dataset.autogenerated === 'true') {
        slug.value = this.value.toLowerCase()
            .replace(/[^a-z0-9]+/g, '_')
            .replace(/^_|_$/g, '');
        slug.dataset.autogenerated = 'true';
    }
});

document.getElementById('roleSlug').addEventListener('input', function() {
    this.dataset.autogenerated = 'false';
});

// Update selected count
document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const total = document.querySelectorAll('.permission-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = total + ' selected';
    });
});
</script>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>