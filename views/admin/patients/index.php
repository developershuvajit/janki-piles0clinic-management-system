<?php 
$activePage = 'patients';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Search & Actions Row -->
<div class="card border-0 shadow-sm p-3 mb-4">
    <form action="<?= site_url('/admin/patients') ?>" method="GET" class="row g-2 align-items-center">
        <div class="col-md-8">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" class="form-control" placeholder="Search by Patient ID, Name, Phone, or Email..." value="<?= esc($query ?? '') ?>">
            </div>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary btn-sm w-100 shadow-sm">
                <i class="bi bi-filter"></i> Search Directory
            </button>
        </div>
        <div class="col-md-2">
            <a href="<?= site_url('/admin/patients/create') ?>" class="btn btn-success btn-sm w-100 shadow-sm">
                <i class="bi bi-person-plus-fill"></i> Register Patient
            </a>
        </div>
    </form>
</div>

<!-- Table Directory -->
<div class="table-responsive border-0 shadow-sm rounded-3">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light text-slate">
            <tr>
                <th>Patient ID</th>
                <th>QR</th>
                <th>Name / Demographics</th>
                <th>Contact Info</th>
                <th>Vitals Metadata</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($patients)): ?>
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-person-fill-exclamation fs-3 d-block mb-2"></i>
                        No patient records located. Try adjusting your search query.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($patients as $pat): ?>
                    <tr>
                        <td class="fw-bold text-slate"><?= esc($pat['patient_id']) ?></td>
                        <td>
                            <?php if ($pat['qr_code_url']): ?>
                                <a href="<?= site_url($pat['qr_code_url']) ?>" target="_blank" title="View Patient ID QR">
                                    <img src="<?= site_url($pat['qr_code_url']) ?>" class="rounded border p-0.5" style="width: 38px; height: 38px; object-fit: cover;">
                                </a>
                            <?php else: ?>
                                <i class="bi bi-qr-code text-muted fs-4"></i>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="fw-bold text-slate"><?= esc($pat['name']) ?></div>
                            <span class="text-muted" style="font-size: 0.78rem;"><?= esc(ucfirst($pat['gender'])) ?> &bull; DOB: <?= esc($pat['dob']) ?></span>
                        </td>
                        <td>
                            <div class="small"><i class="bi bi-telephone text-muted me-1"></i> <?= esc($pat['phone']) ?></div>
                            <div class="small text-muted" style="font-size: 0.78rem;"><i class="bi bi-envelope me-1"></i> <?= esc($pat['email']) ?></div>
                        </td>
                        <td class="small text-slate">
                            <strong>Blood:</strong> <?= esc($pat['blood_group'] ?: 'N/A') ?><br>
                            <span class="text-danger" style="font-size: 0.75rem;"><i class="bi bi-exclamation-triangle"></i> Allergies: <?= esc($pat['allergies'] ? substr($pat['allergies'], 0, 20) . '...' : 'None') ?></span>
                        </td>
                        <td>
                            <span class="badge <?= $pat['status'] === 'active' ? 'badge-active' : 'badge-inactive' ?> rounded px-2.5 py-1.5">
                                <?= esc(ucfirst($pat['status'])) ?>
                            </span>
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="<?= site_url('/admin/patients/history/' . $pat['patient_id']) ?>" class="btn btn-sm btn-outline-primary me-1 px-2.5 py-1" title="View Patient Visit Timeline History">
                                <i class="bi bi-clock-history me-1"></i> Timeline
                            </a>
                            <a href="<?= site_url('/admin/patients/edit/' . $pat['id']) ?>" class="btn btn-sm btn-light border me-1 px-2 py-1" title="Edit">
                                <i class="bi bi-pencil-fill text-secondary"></i>
                            </a>
                            <a href="<?= site_url('/admin/patients/delete/' . $pat['id']) ?>" class="btn btn-sm btn-light border px-2 py-1" onclick="return confirm('Are you sure you want to delete this patient profile? This action deletes all prescriptions, admissions, and bills associated with them.');" title="Delete">
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
