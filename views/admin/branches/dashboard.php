<?php 
$activePage = 'branches';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Branch Dashboard Layout -->
<div class="row">
    <!-- Main Statistics & Roster -->
    <div class="col-lg-8">
        <!-- Stats cards -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card p-4 border-0 shadow-sm d-flex flex-row align-items-center justify-content-between text-white" style="background: linear-gradient(135deg, #0d8a72 0%, #15803d 100%);">
                    <div>
                        <h6 class="text-white-50 text-uppercase mb-1 small fw-bold">Branch Revenue</h6>
                        <h3 class="mb-0 fw-bold">₹<?= esc(number_format($stats['total_revenue'], 2)) ?></h3>
                    </div>
                    <div class="bg-white bg-opacity-25 p-3 rounded-3 fs-3 text-white">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card p-4 border-0 shadow-sm d-flex flex-row align-items-center justify-content-between text-white" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%);">
                    <div>
                        <h6 class="text-white-50 text-uppercase mb-1 small fw-bold">Branch Patients</h6>
                        <h3 class="mb-0 fw-bold"><?= esc((string)$stats['patient_count']) ?></h3>
                    </div>
                    <div class="bg-white bg-opacity-25 p-3 rounded-3 fs-3 text-white">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Doctors Assigned list -->
        <div class="card border-0 shadow-sm p-4 mb-4">
            <h5 class="fw-bold text-slate mb-3"><i class="bi bi-heart-pulse-fill text-success me-2"></i>Assigned Medical Staff (Doctors)</h5>
            
            <div class="table-responsive border-0 p-0 shadow-none">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Email Address</th>
                            <th class="text-end">Shift Timing</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stats['doctors'])): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="bi bi-person-fill-slash d-block fs-4 mb-1"></i>
                                    No doctors assigned to this branch location yet.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($stats['doctors'] as $doc): ?>
                                <tr>
                                    <td>
                                        <?php if ($doc['photo']): ?>
                                            <img src="<?= site_url($doc['photo']) ?>" alt="Photo" class="rounded-circle border" style="width: 40px; height: 40px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded-circle border d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="bi bi-person-fill text-secondary fs-5"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-semibold text-slate"><?= esc($doc['username']) ?></td>
                                    <td class="small"><?= esc($doc['email']) ?></td>
                                    <td class="text-end">
                                        <span class="badge bg-light text-secondary border px-2.5 py-1.5 small">
                                            <?= esc($doc['shift_start'] ? date('h:i A', strtotime($doc['shift_start'])) : '09:00 AM') ?> - 
                                            <?= esc($doc['shift_end'] ? date('h:i A', strtotime($doc['shift_end'])) : '05:00 PM') ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Right Panel: Branch Metadata -->
    <div class="col-lg-4">
        <!-- Branch card detail -->
        <div class="card border-0 shadow-sm p-4 mb-4">
            <h5 class="fw-bold text-slate mb-3"><i class="bi bi-info-circle-fill text-success me-2"></i>Location Details</h5>
            
            <div class="mb-3 text-center">
                <?php if ($branch['logo']): ?>
                    <img src="<?= site_url($branch['logo']) ?>" alt="Logo" class="rounded border p-1" style="width: 80px; height: 80px; object-fit: cover;">
                <?php else: ?>
                    <div class="bg-light rounded border d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="bi bi-building text-secondary fs-2"></i>
                    </div>
                <?php endif; ?>
                <h4 class="fw-bold mt-2 text-slate"><?= esc($branch['name']) ?></h4>
                <span class="badge <?= $branch['status'] === 'active' ? 'badge-active' : 'badge-inactive' ?> px-3 py-1.5 rounded-pill"><?= esc(ucfirst($branch['status'])) ?></span>
            </div>
            
            <ul class="list-group list-group-flush mb-2" style="font-size: 0.85rem;">
                <li class="list-group-item px-0 py-2.5 text-slate">
                    <strong>Address:</strong><br><span class="text-muted"><?= esc($branch['address']) ?></span>
                </li>
                <li class="list-group-item px-0 py-2.5 text-slate">
                    <strong>Helpline Phone:</strong><br><span class="text-muted"><?= esc($branch['phone']) ?></span>
                </li>
                <li class="list-group-item px-0 py-2.5 text-danger fw-semibold">
                    <strong>Emergency Hotline:</strong><br><span><?= esc($branch['emergency_number']) ?></span>
                </li>
                <li class="list-group-item px-0 py-2.5 text-slate">
                    <strong>Email:</strong><br><span class="text-muted"><?= esc($branch['email']) ?></span>
                </li>
                <li class="list-group-item px-0 py-2.5 text-slate">
                    <strong>Opening Hours:</strong><br><span class="text-muted"><?= esc($branch['opening_hours']) ?></span>
                </li>
            </ul>
        </div>
        
        <!-- Google Map Iframe -->
        <?php if (!empty($branch['google_map_link'])): ?>
            <div class="card border-0 shadow-sm p-3">
                <h6 class="fw-bold text-slate mb-2">Location Map</h6>
                <?php if (strpos(trim($branch['google_map_link']), '<iframe') !== false): ?>
                    <div class="ratio ratio-16x9 rounded border overflow-hidden">
                        <?= $branch['google_map_link'] ?>
                    </div>
                <?php else: ?>
                    <a href="<?= esc($branch['google_map_link']) ?>" class="btn btn-outline-success btn-sm w-100" target="_blank">
                        <i class="bi bi-geo-alt-fill me-1"></i> Open Google Maps
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
