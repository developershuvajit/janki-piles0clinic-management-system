<?php 
$userRole = \App\Helpers\Session::get('role_slug');
if ($userRole === 'receptionist') {
    $activePage = 'reception_ipd_beds';
    include VIEWS_PATH . '/layout/reception_header.php'; 
} else {
    $activePage = 'ipd_beds';
    include VIEWS_PATH . '/layout/admin_header.php'; 
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-slate mb-1">Ward Room & Bed Allocation Matrix</h4>
        <p class="text-muted small mb-0">Live bed availability status across inpatient wards and private rooms</p>
    </div>
    <a href="<?= site_url(($userRole === 'receptionist' ? '/reception' : '/admin') . '/ipd/admit') ?>" class="btn btn-emerald btn-sm rounded-pill px-3 shadow-sm">
        <i class="bi bi-person-plus-fill me-1"></i> Admit Patient to Bed
    </a>
</div>

<!-- Rooms Breakdown Cards -->
<div class="row mb-4">
    <?php foreach ($rooms as $room): ?>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold"><?= esc($room['room_number']) ?></span>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary text-uppercase small"><?= esc($room['type']) ?></span>
                </div>
                <h5 class="fw-bold text-slate mb-1">₹<?= number_format((float)$room['price_per_day'], 2) ?> <span class="fs-6 text-muted fw-normal">/ day</span></h5>
                <div class="small text-muted">
                    Occupied: <strong><?= (int)$room['occupied_beds'] ?></strong> / <?= (int)$room['total_beds'] ?> beds
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Beds Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-light py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-diagram-3-fill me-2"></i> All Bed Allocation Statuses</h6>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Room Number</th>
                        <th>Room Type</th>
                        <th>Bed Number</th>
                        <th>Rate / Day (₹)</th>
                        <th>Current Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($beds)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">No beds allocated on system.</td></tr>
                    <?php else: ?>
                        <?php foreach ($beds as $b): ?>
                            <tr>
                                <td><span class="fw-bold text-slate"><?= esc($b['room_number']) ?></span></td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-secondary text-uppercase"><?= esc($b['room_type']) ?></span></td>
                                <td><span class="badge bg-info bg-opacity-10 text-info fw-bold"><?= esc($b['bed_number']) ?></span></td>
                                <td class="fw-bold text-slate">₹<?= number_format((float)$b['price_per_day'], 2) ?></td>
                                <td>
                                    <?php if ($b['status'] === 'occupied'): ?>
                                        <span class="badge bg-danger bg-opacity-20 text-danger border border-danger px-2.5 py-1">Occupied</span>
                                    <?php else: ?>
                                        <span class="badge bg-success bg-opacity-20 text-success border border-success px-2.5 py-1">Available</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($b['status'] === 'available'): ?>
                                        <a href="<?= site_url(($userRole === 'receptionist' ? '/reception' : '/admin') . '/ipd/admit') ?>" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                            <i class="bi bi-journal-plus me-1"></i> Admit to Bed
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small"><i class="bi bi-lock-fill text-danger me-1"></i> Locked</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
if ($userRole === 'receptionist') {
    include VIEWS_PATH . '/layout/reception_footer.php'; 
} else {
    include VIEWS_PATH . '/layout/admin_footer.php'; 
}
?>
