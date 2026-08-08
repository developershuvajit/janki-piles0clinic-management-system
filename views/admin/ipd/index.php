<?php 
$activePage = 'ipd';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Actions Row -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0 small">Overview of inpatient ward stays, bed allocations, and clinical discharges.</p>
    <a href="<?= site_url('/admin/ipd/admit') ?>" class="btn btn-primary btn-sm px-3 shadow-sm">
        <i class="bi bi-plus-circle me-1"></i> Admit Inpatient
    </a>
</div>

<!-- Tabs Navigation -->
<ul class="nav nav-tabs border-bottom mb-4" id="ipdTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-semibold" id="active-tab" data-bs-toggle="tab" data-bs-target="#active-admissions" type="button" role="tab">
            <i class="bi bi-heart-pulse text-danger me-1"></i> Active Admissions
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" id="history-tab" data-bs-toggle="tab" data-bs-target="#discharge-history" type="button" role="tab">
            <i class="bi bi-clock-history text-secondary me-1"></i> Discharge History
        </button>
    </li>
</ul>

<!-- Tabs Content -->
<div class="tab-content" id="ipdTabContent">
    <!-- Active Admissions Tab -->
    <div class="tab-pane fade show active" id="active-admissions" role="tabpanel">
        <div class="table-responsive border-0 shadow-sm rounded-3 bg-white">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-slate">
                    <tr>
                        <th>Patient Details</th>
                        <th>Attending Doctor</th>
                        <th>Room / Bed Mapped</th>
                        <th>Admission Date</th>
                        <th>Symptoms & Diagnosis</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($admissions)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-heart-pulse-fill fs-3 d-block mb-2"></i>
                                No patients currently admitted to IPD.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($admissions as $adm): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-slate"><?= esc($adm['patient_name']) ?></div>
                                    <span class="text-muted small" style="font-size: 0.78rem;">ID: <?= esc($adm['patient_code']) ?></span>
                                </td>
                                <td class="fw-semibold text-slate">Dr. <?= esc($adm['doctor_name']) ?></td>
                                <td>
                                    <span class="badge bg-light text-slate border fw-semibold">
                                        <i class="bi bi-hospital me-1"></i> <?= esc($adm['room_number']) ?> (<?= esc(ucfirst($adm['room_type'])) ?>)
                                    </span>
                                    <div class="small mt-1 text-muted">Bed: <strong><?= esc($adm['bed_number']) ?></strong></div>
                                </td>
                                <td class="small"><?= esc(date('Y-m-d h:i A', strtotime($adm['admission_date']))) ?></td>
                                <td class="small text-slate">
                                    <strong>Diagnosis:</strong> <?= esc($adm['diagnosis']) ?><br>
                                    <span class="text-muted" style="font-size: 0.78rem;">Symp: <?= esc(substr($adm['symptoms'], 0, 30)) ?>...</span>
                                </td>
                                <td class="text-end">
                                    <a href="<?= site_url('/admin/ipd/nursing-logs/' . $adm['id']) ?>" class="btn btn-sm btn-outline-primary px-3 py-1 shadow-sm">
                                        <i class="bi bi-activity me-1"></i> Charts & Logs
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Discharge History Tab -->
    <div class="tab-pane fade" id="discharge-history" role="tabpanel">
        <div class="table-responsive border-0 shadow-sm rounded-3 bg-white">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-slate">
                    <tr>
                        <th>Patient Details</th>
                        <th>Attending Doctor</th>
                        <th>Room / Bed Mapped</th>
                        <th>Stay Duration</th>
                        <th>Discharge Date</th>
                        <th class="text-end">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($discharged)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-archive-fill fs-3 d-block mb-2"></i>
                                No discharge records located.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($discharged as $dis): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-slate"><?= esc($dis['patient_name']) ?></div>
                                    <span class="text-muted small" style="font-size: 0.78rem;">ID: <?= esc($dis['patient_code']) ?></span>
                                </td>
                                <td class="fw-semibold text-slate">Dr. <?= esc($dis['doctor_name']) ?></td>
                                <td class="small text-muted"><?= esc($dis['room_number']) ?> &bull; Bed: <?= esc($dis['bed_number']) ?></td>
                                <td class="small">
                                    <?php 
                                    $admit = strtotime($dis['admission_date']);
                                    $disDate = strtotime($dis['discharge_date']);
                                    $days = ceil(($disDate - $admit) / 86400);
                                    echo $days < 1 ? '1 Day' : $days . ' Days';
                                    ?>
                                </td>
                                <td class="small text-muted"><?= esc(date('Y-m-d h:i A', strtotime($dis['discharge_date']))) ?></td>
                                <td class="text-end">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2.5 py-1.5 rounded">Discharged</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
