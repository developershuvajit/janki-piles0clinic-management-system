<?php 
$activePage = 'employees';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Form Card -->
<div class="row">
    <!-- Main Form Column -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <form action="<?= site_url('/admin/employees/update/' . $employee['id']) ?>" method="POST" enctype="multipart/form-data" autocomplete="off">
                <?= csrf_field() ?>

                <div class="d-flex align-items-center mb-3">
                    <h5 class="fw-bold text-slate mb-0"><i class="bi bi-pencil-square text-success me-2"></i>Update Employee Profile</h5>
                    <?php if ($employee['photo']): ?>
                        <img src="<?= site_url($employee['photo']) ?>" alt="Photo" class="ms-auto rounded-circle border" style="width: 45px; height: 45px; object-fit: cover;">
                    <?php endif; ?>
                </div>
                
                <div class="row g-3 mb-4">
                    <!-- Account Credentials -->
                    <div class="col-md-6">
                        <label for="username" class="form-label small fw-semibold">Console Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="username" name="username" value="<?= esc($employee['username']) ?>" required placeholder="e.g. johndoe">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="email" class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control form-control-sm" id="email" name="email" value="<?= esc($employee['email']) ?>" required placeholder="e.g. johndoe@clinic.com">
                    </div>
                    
                    <div class="col-md-12">
                        <label for="password" class="form-label small fw-semibold">Console Password (Leave blank to keep current password)</label>
                        <input type="password" class="form-control form-control-sm" id="password" name="password" minlength="8" placeholder="Enter new password to overwrite">
                    </div>

                    <!-- Role & Assignments -->
                    <div class="col-md-6">
                        <label for="role_id" class="form-label small fw-semibold">Employee Designation / Role <span class="text-danger">*</span></label>
                        <select class="form-control form-control-sm form-select" id="role_id" name="role_id" required>
                            <?php foreach ($roles as $role): ?>
                                <?php if ($role['slug'] !== 'super_admin' || (int)$employee['role_id'] === 1): ?>
                                    <option value="<?= $role['id'] ?>" <?= (int)$employee['role_id'] === (int)$role['id'] ? 'selected' : '' ?>><?= esc($role['name']) ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="branch_id" class="form-label small fw-semibold">Assign Branch Office</label>
                        <select class="form-control form-control-sm form-select" id="branch_id" name="branch_id">
                            <option value="">Headquarters / General Staff</option>
                            <?php foreach ($branches as $branch): ?>
                                <option value="<?= $branch['id'] ?>" <?= (int)$employee['branch_id'] === (int)$branch['id'] ? 'selected' : '' ?>><?= esc($branch['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-12">
                        <label for="photo" class="form-label small fw-semibold">Update Profile Photo</label>
                        <input type="file" class="form-control form-control-sm" id="photo" name="photo" accept="image/*">
                    </div>

                    <!-- Salary & Shifts -->
                    <div class="col-md-4">
                        <label for="salary" class="form-label small fw-semibold">Base Monthly Salary (INR)</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light">₹</span>
                            <input type="number" class="form-control" id="salary" name="salary" step="100.00" value="<?= esc(number_format((float)$employee['salary'], 2, '.', '')) ?>" required placeholder="0.00">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="shift_start" class="form-label small fw-semibold">Shift Start Time</label>
                        <input type="time" class="form-control form-control-sm" id="shift_start" name="shift_start" value="<?= esc(date('H:i', strtotime($employee['shift_start']))) ?>" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="shift_end" class="form-label small fw-semibold">Shift End Time</label>
                        <input type="time" class="form-control form-control-sm" id="shift_end" name="shift_end" value="<?= esc(date('H:i', strtotime($employee['shift_end']))) ?>" required>
                    </div>

                    <!-- Add Documents Upload -->
                    <div class="col-md-12">
                        <label for="documents" class="form-label small fw-semibold">Upload More Verification Documents</label>
                        <input type="file" class="form-control form-control-sm" id="documents" name="documents[]" multiple accept=".pdf,.doc,.docx,image/*">
                    </div>
                </div>

                <div class="text-end pt-3">
                    <a href="<?= site_url('/admin/employees') ?>" class="btn btn-outline-secondary btn-sm px-3 me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
                        <i class="bi bi-save me-1"></i> Update Employee Profile
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Document Attachments List Column -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <h5 class="fw-bold text-slate mb-3"><i class="bi bi-file-earmark-text text-success me-2"></i>Verification Documents</h5>
            <p class="text-muted small">Access or delete academic, contract, and identity document credentials.</p>
            
            <div class="list-group list-group-flush mt-3" style="max-height: 400px; overflow-y: auto;">
                <?php if (empty($documents)): ?>
                    <div class="text-center py-4 text-muted small">
                        <i class="bi bi-file-earmark-slash d-block fs-3 mb-1"></i>
                        No files uploaded.
                    </div>
                <?php else: ?>
                    <?php foreach ($documents as $doc): ?>
                        <div class="list-group-item px-0 py-2.5 d-flex align-items-center justify-content-between">
                            <div class="text-truncate me-2" style="max-width: 180px;">
                                <a href="<?= site_url($doc['file_path']) ?>" class="small fw-semibold text-slate text-decoration-none" target="_blank" title="<?= esc($doc['document_name']) ?>">
                                    <i class="bi bi-file-earmark-arrow-down text-success me-1"></i> <?= esc($doc['document_name']) ?>
                                </a>
                                <div class="x-small text-muted" style="font-size: 0.7rem;"><?= esc(date('Y-m-d H:i', strtotime($doc['uploaded_at']))) ?></div>
                            </div>
                            <a href="<?= site_url('/admin/employees/delete-doc/' . $doc['id']) ?>" class="btn btn-sm btn-light border px-2 py-1" onclick="return confirm('Are you sure you want to delete this document?');">
                                <i class="bi bi-trash-fill text-danger"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
