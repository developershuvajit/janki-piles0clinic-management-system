<?php 
$activePage = 'branches';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Form Card -->
<div class="card border-0 shadow-sm p-4">
    <form action="<?= site_url('/admin/branches/update/' . $branch['id']) ?>" method="POST" enctype="multipart/form-data" autocomplete="off">
        <?= csrf_field() ?>

        <div class="d-flex align-items-center mb-3">
            <h5 class="fw-bold text-slate mb-0"><i class="bi bi-pencil-square text-success me-2"></i>Update Branch Location Profiles</h5>
            <?php if ($branch['logo']): ?>
                <img src="<?= site_url($branch['logo']) ?>" alt="Branch Logo" class="ms-auto rounded border" style="width: 45px; height: 45px; object-fit: cover;">
            <?php endif; ?>
        </div>
        
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="name" class="form-label small fw-semibold">Branch Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm" id="name" name="name" value="<?= esc($branch['name']) ?>" required placeholder="e.g. City Dental Branch">
            </div>
            
            <div class="col-md-6">
                <label for="logo" class="form-label small fw-semibold">Change Logo Image</label>
                <input type="file" class="form-control form-control-sm" id="logo" name="logo" accept="image/*">
                <div class="form-text x-small text-muted" style="font-size: 0.75rem;">Supported formats: JPEG, PNG. Uploading replaces the current logo.</div>
            </div>
            
            <div class="col-md-12">
                <label for="address" class="form-label small fw-semibold">Physical Address <span class="text-danger">*</span></label>
                <textarea class="form-control" id="address" name="address" rows="2" required placeholder="Enter full address of the branch"><?= esc($branch['address']) ?></textarea>
            </div>
            
            <div class="col-md-4">
                <label for="phone" class="form-label small fw-semibold">Phone Number <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm" id="phone" name="phone" value="<?= esc($branch['phone']) ?>" required placeholder="e.g. +91 98765 43210">
            </div>
            
            <div class="col-md-4">
                <label for="emergency_number" class="form-label small fw-semibold">Emergency Helpline <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm" id="emergency_number" name="emergency_number" value="<?= esc($branch['emergency_number']) ?>" required placeholder="e.g. +91 99999 88888">
            </div>
            
            <div class="col-md-4">
                <label for="email" class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                <input type="email" class="form-control form-control-sm" id="email" name="email" value="<?= esc($branch['email']) ?>" required placeholder="e.g. citybranch@clinic.com">
            </div>
            
            <div class="col-md-6">
                <label for="opening_hours" class="form-label small fw-semibold">Opening Hours <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm" id="opening_hours" name="opening_hours" value="<?= esc($branch['opening_hours']) ?>" required placeholder="e.g. Mon-Sat: 09:00 AM - 08:00 PM">
            </div>
            
            <div class="col-md-6">
                <label for="status" class="form-label small fw-semibold">Operating Status</label>
                <select class="form-control form-control-sm form-select" id="status" name="status">
                    <option value="active" <?= $branch['status'] === 'active' ? 'selected' : '' ?>>Active (Open to registrations)</option>
                    <option value="inactive" <?= $branch['status'] === 'inactive' ? 'selected' : '' ?>>Inactive (Temporarily suspended)</option>
                </select>
            </div>

            <div class="col-md-12">
                <label for="google_map_link" class="form-label small fw-semibold">Google Maps Link (Embed Iframe / URL)</label>
                <textarea class="form-control" id="google_map_link" name="google_map_link" rows="2" placeholder="Paste iframe source link or standard maps URL location"><?= esc($branch['google_map_link']) ?></textarea>
            </div>
        </div>

        <div class="text-end pt-3">
            <a href="<?= site_url('/admin/branches') ?>" class="btn btn-outline-secondary btn-sm px-3 me-2">Cancel</a>
            <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
                <i class="bi bi-save me-1"></i> Update Branch Location
            </button>
        </div>
    </form>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
