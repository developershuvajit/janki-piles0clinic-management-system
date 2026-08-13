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
    }
    .permission-item label {
        cursor: pointer;
        color: #1e293b;
        margin: 0;
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
    }
    .slug-display {
        font-size: 0.85rem;
        color: #94a3b8;
        background: #f1f4f8;
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        display: inline-block;
    }
</style>

<div class="datatable-wrapper mt-4">
    <div class="datatable-header">
        <h5><i class="bi bi-pencil-square me-2"></i>Edit Role: <?= esc($role['name']) ?></h5>
        <a href="<?= site_url('/admin/roles') ?>" class="btn-back">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="form-section">
        <form action="<?= site_url("/admin/roles/update/{$role['id']}") ?>" method="POST">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required 
                               value="<?= esc($role['name']) ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <div class="slug-display">
                            <i class="bi bi-tag me-1"></i> <?= esc($role['slug']) ?>
                        </div>
                        <small class="text-muted">Slug cannot be changed</small>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="2"><?= esc($role['description'] ?? '') ?></textarea>
            </div>

            <hr>

            <div class="mb-3">
                <label class="form-label fw-semibold">Assign Permissions</label>
                <small class="text-muted d-block mb-2">Select the permissions this role should have</small>
                
                <?php 
                $currentModule = '';
                foreach ($permissions as $perm): 
                    $checked = in_array($perm['id'], $rolePermIds) ? 'checked' : '';
                ?>
                    <?php if ($currentModule !== $perm['module']): ?>
                        <?php $currentModule = $perm['module']; ?>
                        <div class="permission-module">
                            <i class="bi bi-folder me-1"></i> <?= esc($currentModule) ?>
                        </div>
                        <div class="permission-grid">
                    <?php endif; ?>
                        <div class="permission-item">
                            <input type="checkbox" class="form-check-input" 
                                   name="permissions[]" value="<?= $perm['id'] ?>" 
                                   id="perm_<?= $perm['id'] ?>" <?= $checked ?>>
                            <label for="perm_<?= $perm['id'] ?>"><?= esc($perm['name']) ?></label>
                        </div>
                    <?php if ($currentModule !== $perm['module']): ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php if (!empty($permissions)): ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn-register">
                    <i class="bi bi-save"></i> Update Role
                </button>
                <a href="<?= site_url('/admin/roles') ?>" class="btn-back ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>