<?php
$activePage = 'patients';
include VIEWS_PATH . '/layout/admin_header.php';
?>

<!-- ============================================
     PAGE CSS
     ============================================ -->
<link rel="stylesheet" href="<?= asset('css/datatable.css') ?>">

<!-- ============================================
     PAGE HTML
     ============================================ -->
<div class="datatable-wrapper mt-4">
    <div class="datatable-header">
        <h5>Patient List <small><?= count($patients ?? []) ?> registered</small></h5>
        <a href="<?= site_url('/admin/patients/create') ?>" class="btn-register">
            <i class="bi bi-person-plus-fill"></i> Register
        </a>
    </div>

    <div class="table-responsive">
        <table id="patientsTable" class="table-custom" style="width:100%">
            <thead>
                <tr>
                    <th class="sno">#</th>
                    <th>Patient ID</th>
                    <th>Name</th>
                    <th style="min-width:80px;">Gender</th>
                    <th>Phone</th>
                    <th style="min-width:120px;">Email</th>
                    <th style="width:60px;">Blood</th>
                    <th style="width:80px;">Status</th>
                    <th style="width:110px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($patients)):
                    $sn = 1;
                    foreach ($patients as $pat): ?>
                        <tr>
                            <td class="sno"><?= $sn++ ?></td>
                            <td class="patient-id"><?= esc($pat['patient_id']) ?></td>
                            <td class="name"><?= esc($pat['name']) ?></td>
                            <td class="gender">
                                <?= esc(ucfirst($pat['gender'] ?? 'N/A')) ?>
                                <?php if (!empty($pat['dob'])): ?>
                                    <span class="dob"><?= esc($pat['dob']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="phone"><?= esc($pat['phone']) ?></td>
                            <td class="email"><?= esc($pat['email']) ?></td>
                            <td><span class="blood"><?= esc($pat['blood_group'] ?: '—') ?></span></td>
                            <td>
                                <span class="badge-status <?= ($pat['status'] ?? 'inactive') === 'active' ? 'active' : 'inactive' ?>">
                                    <?= esc($pat['status'] ?? 'inactive') ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-group">
                                    <a href="<?= site_url('/admin/patients/history/' . $pat['patient_id']) ?>" class="btn-action history" title="History">
                                        <i class="bi bi-clock-history"></i>
                                    </a>
                                    <a href="<?= site_url('/admin/patients/edit/' . $pat['id']) ?>" class="btn-action" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <a href="<?= site_url('/admin/patients/delete/' . $pat['id']) ?>" class="btn-action delete" onclick="return confirm('Delete this patient?')" title="Delete">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="9" style="text-align:center;padding:2.5rem 1rem;color:#94a3b8;">
                            No patients registered yet.
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
        ]
    });
});
</script>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>