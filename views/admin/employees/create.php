<?php 
$activePage = 'employees';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<div class="card border-0 shadow-sm p-4 mt-4">
    <form action="<?= site_url('/admin/employees/save') ?>" method="POST" enctype="multipart/form-data" autocomplete="off">
        <?= csrf_field() ?>

        <h5 class="fw-bold text-slate mb-3"><i class="bi bi-person-fill-add text-success me-2"></i>Employee Profile details</h5>
        
        <div class="row g-3 mb-4">
            <!-- Account Credentials -->
            <div class="col-md-4">
                <label for="username" class="form-label small fw-semibold">Console Username <span class="text-danger">*</span></label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-person text-muted"></i></span>
                    <input type="text" class="form-control" id="username" name="username" required placeholder="e.g. johndoe">
                </div>
            </div>
            
            <div class="col-md-4">
                <label for="email" class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-envelope text-muted"></i></span>
                    <input type="email" class="form-control" id="email" name="email" required placeholder="e.g. johndoe@clinic.com">
                </div>
            </div>
            
            <div class="col-md-4">
                <label for="password" class="form-label small fw-semibold">Console Password <span class="text-danger">*</span></label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-key text-muted"></i></span>
                    <input type="password" class="form-control" id="password" name="password" minlength="8" required placeholder="Enter password (min 8 chars)">
                </div>
            </div>

            <!-- Role & Assignments -->
            <div class="col-md-4">
                <label for="role_id" class="form-label small fw-semibold">Employee Designation / Role <span class="text-danger">*</span></label>
                <select class="form-control form-control-sm form-select" id="role_id" name="role_id" required>
                    <option value="" disabled selected>Select Designation</option>
                    <?php foreach ($roles as $role): ?>
                        <?php if ($role['slug'] !== 'super_admin'): // Prevent creating extra Super Admins from this UI ?>
                            <option value="<?= $role['id'] ?>"><?= esc($role['name']) ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-4">
                <label for="branch_id" class="form-label small fw-semibold">Assign Branch Office</label>
                <select class="form-control form-control-sm form-select" id="branch_id" name="branch_id">
                    <option value="">Headquarters / General Staff</option>
                    <?php foreach ($branches as $branch): ?>
                        <option value="<?= $branch['id'] ?>"><?= esc($branch['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-4">
                <label for="photo" class="form-label small fw-semibold">Profile Photo</label>
                <input type="file" class="form-control form-control-sm" id="photo" name="photo" accept="image/*">
            </div>

            <!-- Salary & Shifts -->
            <div class="col-md-4">
                <label for="salary" class="form-label small fw-semibold">Base Monthly Salary (INR) <span class="text-danger">*</span></label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light">₹</span>
                    <input type="number" class="form-control" id="salary" name="salary" step="100.00" required placeholder="0.00">
                </div>
            </div>
            
            <div class="col-md-4">
                <label for="shift_start" class="form-label small fw-semibold">Shift Start Time <span class="text-danger">*</span></label>
                <input type="time" class="form-control form-control-sm" id="shift_start" name="shift_start" value="09:00" required>
            </div>
            
            <div class="col-md-4">
                <label for="shift_end" class="form-label small fw-semibold">Shift End Time <span class="text-danger">*</span></label>
                <input type="time" class="form-control form-control-sm" id="shift_end" name="shift_end" value="17:00" required>
            </div>

            <!-- Documents Upload -->
            <div class="col-md-12">
                <label for="documents" class="form-label small fw-semibold">Upload Verification Documents (Degrees, Contracts, CV)</label>
                <input type="file" class="form-control form-control-sm" id="documents" name="documents[]" multiple accept=".pdf,.doc,.docx,image/*">
                <div class="form-text x-small text-muted" style="font-size: 0.75rem;">Supported formats: PDF, Word, JPEG, PNG. Max: 5MB per file. Hold Ctrl to upload multiple files.</div>
            </div>
        </div>

        <div class="text-end pt-3">
            <a href="<?= site_url('/admin/employees') ?>" class="btn btn-outline-secondary btn-sm px-3 me-2">Cancel</a>
            <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
                <i class="bi bi-save me-1"></i> Save Employee Profile
            </button>
        </div>
    </form>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
