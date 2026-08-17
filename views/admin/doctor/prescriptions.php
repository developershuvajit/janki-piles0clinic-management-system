<?php 
$activePage = 'doctor_prescriptions';
include VIEWS_PATH . '/layout/doctor_header.php'; 
?>

<!-- ============================================
     PAGE CSS
     ============================================ -->
<link rel="stylesheet" href="<?= asset('css/datatable.css') ?>">

<style>
.text-slate {
    color: #0b1a2b;
}

/* Prescription Badge */
.rx-badge {
    background: #dbeafe;
    color: #1d4ed8;
    font-weight: 700;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.8rem;
}

/* Diagnosis Column */
.diagnosis-cell {
    max-width: 180px;
    position: relative;
}
.diagnosis-text {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    font-size: 0.85rem;
    line-height: 1.4;
}
.diagnosis-text.expanded {
    -webkit-line-clamp: unset;
    display: block;
}
.diagnosis-toggle {
    display: inline-block;
    font-size: 0.7rem;
    color: #6366f1;
    cursor: pointer;
    font-weight: 600;
    margin-top: 2px;
    padding: 2px 8px;
    border-radius: 4px;
    background: #eef2ff;
    border: 1px solid #c7d2fe;
    transition: all 0.2s ease;
}
.diagnosis-toggle:hover {
    background: #4f46e5;
    color: #fff;
    border-color: #4f46e5;
}

/* ===== ACTION BUTTONS ===== */
.action-group {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.btn-action-print {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 0.4rem 1rem;
    border-radius: 8px;
    font-size: 0.78rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
    background: #eef2ff;
    color: #4f46e5;
    border: 1px solid #c7d2fe;
}
.btn-action-print:hover {
    background: #4f46e5;
    color: #fff;
    border-color: #4f46e5;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}
.btn-action-print i {
    font-size: 0.9rem;
}

.btn-action-edit {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 0.4rem 1rem;
    border-radius: 8px;
    font-size: 0.78rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
    background: #fef3c7;
    color: #d97706;
    border: 1px solid #fde68a;
}
.btn-action-edit:hover {
    background: #d97706;
    color: #fff;
    border-color: #d97706;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(217, 119, 6, 0.3);
}
.btn-action-edit i {
    font-size: 0.9rem;
}

.btn-action-delete {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 0.4rem 1rem;
    border-radius: 8px;
    font-size: 0.78rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fca5a5;
}
.btn-action-delete:hover {
    background: #dc2626;
    color: #fff;
    border-color: #dc2626;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}
.btn-action-delete i {
    font-size: 0.9rem;
}

/* Follow-up badge */
.followup-badge {
    background: #fef3c7;
    color: #d97706;
    padding: 0.2rem 0.6rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 600;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 576px) {
    .action-group {
        flex-direction: column;
        gap: 4px;
        align-items: stretch;
    }
    .btn-action-print,
    .btn-action-edit,
    .btn-action-delete {
        justify-content: center;
        padding: 0.3rem 0.7rem;
        font-size: 0.7rem;
    }
}
</style>

<!-- ============================================
     HEADER
     ============================================ -->
<div class="d-flex justify-content-between align-items-center mb-4 mt-4 mx-4">
    <div>
        <h4 class="fw-bold text-slate mb-1"><i class="bi bi-prescription2 text-success me-2"></i>Physician Prescriptions Directory</h4>
        <p class="text-muted small mb-0">Review and print all outpatient prescriptions issued by you</p>
    </div>
    <a href="<?= site_url('/doctor/prescriptions/create') ?>" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> New Prescription
    </a>
</div>

<!-- ============================================
     PRESCRIPTIONS TABLE
     ============================================ -->
<div class="datatable-wrapper">
    <div class="datatable-header">
        <h5>Prescriptions <small><?= count($prescriptions ?? []) ?> prescriptions issued</small></h5>
    </div>

    <div class="table-responsive">
        <table id="prescriptionTable" class="table-custom" style="width:100%">
            <thead>
                <tr>
                    <th class="sno">#</th>
                    <th style="width:120px;">Prescription #</th>
                    <th style="min-width:180px;">Patient Name</th>
                    <th style="min-width:200px;">Diagnosis</th>
                    <th style="width:130px;">Follow-up Date</th>
                    <th style="width:150px;">Date Written</th>
                    <th style="width:220px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($prescriptions)):
                    $sn = 1;
                    foreach ($prescriptions as $p): ?>
                        <tr>
                            <td class="sno"><?= $sn++ ?></td>
                            <td>
                                <span class="rx-badge">#RX-<?= esc((string)$p['id']) ?></span>
                            </td>
                            <td>
                                <div class="fw-bold text-slate"><?= esc($p['patient_name']) ?></div>
                                <span class="text-muted small" style="font-size: 0.75rem;">Code: <?= esc($p['patient_code'] ?? 'N/A') ?></span>
                            </td>
                            <td>
                                <div class="diagnosis-cell">
                                    <div class="diagnosis-text" id="diag-<?= $p['id'] ?>">
                                        <?= esc($p['diagnosis']) ?>
                                    </div>
                                    <?php if (strlen($p['diagnosis']) > 60): ?>
                                        <span class="diagnosis-toggle" onclick="toggleDiagnosis(<?= $p['id'] ?>, this)">
                                            <i class="bi bi-eye"></i> See More
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php if ($p['follow_up_date']): ?>
                                    <span class="followup-badge">
                                        <i class="bi bi-calendar3 me-1"></i><?= date('d M Y', strtotime($p['follow_up_date'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted small">None</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div><?= date('d M Y', strtotime($p['created_at'])) ?></div>
                                <span class="text-muted small" style="font-size: 0.7rem;">
                                    <i class="bi bi-clock me-1"></i><?= date('h:i A', strtotime($p['created_at'])) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-group">
                                    <a href="<?= site_url('/doctor/prescriptions/print/' . $p['id']) ?>" 
                                       class="btn-action-print">
                                        <i class="bi bi-printer"></i> Print RX
                                    </a>
                                    <a href="<?= site_url('/doctor/prescriptions/edit/' . $p['id']) ?>" 
                                       class="btn-action-edit">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="<?= site_url('/doctor/prescriptions/delete/' . $p['id']) ?>" 
                                       class="btn-action-delete" 
                                       onclick="return confirm('Delete this prescription?')">
                                        <i class="bi bi-trash3"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center;padding:2.5rem 1rem;color:#94a3b8;">
                            <i class="bi bi-prescription2 fs-3 d-block mb-2"></i>
                            No prescriptions recorded yet.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ============================================
     JAVASCRIPT - Diagnosis Toggle
     ============================================ -->
<script>
function toggleDiagnosis(id, element) {
    const diagElement = document.getElementById('diag-' + id);
    if (diagElement.classList.contains('expanded')) {
        diagElement.classList.remove('expanded');
        element.innerHTML = '<i class="bi bi-eye"></i> See More';
    } else {
        diagElement.classList.add('expanded');
        element.innerHTML = '<i class="bi bi-eye-slash"></i> See Less';
    }
}
</script>

<!-- ============================================
     DATATABLES LIBS + INIT
     ============================================ -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
    $('#prescriptionTable').DataTable({
        pageLength: 25,
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        order: [[0, 'asc']]
    });
});
</script>

<?php include VIEWS_PATH . '/layout/doctor_footer.php'; ?>