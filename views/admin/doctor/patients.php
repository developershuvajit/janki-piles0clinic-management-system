<?php 
$activePage = 'doctor_patients';
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

/* Patient Code Badge */
.patient-code {
    background: #dbeafe;
    color: #1d4ed8;
    font-weight: 700;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.8rem;
}

/* Blood Group Badge */
.blood-badge {
    background: #fee2e2;
    color: #dc2626;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* ===== ACTION BUTTONS - IMPROVED ===== */
.action-group {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

.btn-action-history {
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

.btn-action-history:hover {
    background: #4f46e5;
    color: #fff;
    border-color: #4f46e5;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

.btn-action-history i {
    font-size: 0.9rem;
}

/* ===== TABLE ROW HOVER ===== */
.table-custom tbody tr:hover {
    background-color: #f8fafc;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 576px) {
    .action-group {
        flex-direction: column;
        gap: 4px;
    }
    .btn-action-history {
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
        <h4 class="fw-bold text-slate mb-1"><i class="bi bi-person-lines-fill text-success me-2"></i>Patient Medical Records Directory</h4>
        <p class="text-muted small mb-0">Search and review patient medical histories and previous prescriptions</p>
    </div>
    <div>
        <span class="badge bg-light text-dark border px-3 py-2">
            <i class="bi bi-people me-1"></i> <?= count($patients ?? []) ?> Patients
        </span>
    </div>
</div>

<!-- ============================================
     PATIENTS TABLE
     ============================================ -->
<div class="datatable-wrapper">
    <div class="datatable-header">
        <h5>Patient Records <small><?= count($patients ?? []) ?> patients registered</small></h5>
    </div>

    <div class="table-responsive">
        <table id="patientsTable" class="table-custom" style="width:100%">
            <thead>
                <tr>
                    <th class="sno">#</th>
                    <th style="width:120px;">Patient Code</th>
                    <th style="min-width:180px;">Name</th>
                    <th style="width:140px;">Phone</th>
                    <th style="width:130px;">Gender / Age</th>
                    <th style="width:110px;">Blood Group</th>
                    <th style="width:130px;">Registered Date</th>
                    <th style="width:160px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($patients)):
                    $sn = 1;
                    foreach ($patients as $p): 
                        $age = date_diff(date_create($p['dob']), date_create('today'))->y;
                    ?>
                        <tr>
                            <td class="sno"><?= $sn++ ?></td>
                            <td>
                                <span class="patient-code"><?= esc($p['patient_id']) ?></span>
                            </td>
                            <td>
                                <div class="fw-bold text-slate"><?= esc($p['name']) ?></div>
                                <span class="text-muted small" style="font-size: 0.75rem;"><?= esc($p['email'] ?? 'No email') ?></span>
                            </td>
                            <td>
                                <a href="tel:<?= esc($p['phone']) ?>" class="text-decoration-none text-slate">
                                    <i class="bi bi-telephone me-1 text-muted"></i><?= esc($p['phone']) ?>
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <?= ucfirst(esc($p['gender'])) ?>
                                </span>
                                <span class="text-muted small d-block" style="font-size: 0.75rem;">
                                    <i class="bi bi-calendar3 me-1"></i><?= $age ?> years
                                </span>
                            </td>
                            <td>
                                <span class="blood-badge"><?= esc($p['blood_group'] ?: 'N/A') ?></span>
                            </td>
                            <td>
                                <div><?= date('d M Y', strtotime($p['created_at'])) ?></div>
                                <span class="text-muted small" style="font-size: 0.7rem;">
                                    <i class="bi bi-clock me-1"></i><?= date('h:i A', strtotime($p['created_at'])) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-group">
                                    <a href="<?= site_url('/doctor/patients/history/' . $p['id']) ?>" 
                                       class="btn-action-history">
                                        <i class="bi bi-clock-history"></i> History
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center;padding:2.5rem 1rem;color:#94a3b8;">
                            <i class="bi bi-person-fill-exclamation fs-3 d-block mb-2"></i>
                            No patient records found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

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
    $('#patientsTable').DataTable({
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