<?php 
$activePage = 'employees';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- ============================================
     PAGE CSS
     ============================================ -->
<link rel="stylesheet" href="<?= asset('css/datatable.css') ?>">

<style>
    /* Employee specific styles */
    .employee-photo {
        width: 45px;
        height: 45px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #e2e8f0;
        padding: 2px;
    }
    .employee-photo-placeholder {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        border: 2px solid #e2e8f0;
        background: #f1f4f8;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 1.2rem;
    }
    .employee-name {
        font-weight: 600;
        color: #0b1a2b;
        font-size: 0.85rem;
    }
    .employee-id {
        font-size: 0.7rem;
        color: #94a3b8;
    }
    .employee-email {
        font-size: 0.78rem;
        color: #1e293b;
    }
    .employee-role {
        font-size: 0.78rem;
        font-weight: 500;
        color: #0b1a2b;
    }
    .employee-branch {
        font-size: 0.7rem;
        color: #6b7a8f;
    }
    .employee-salary {
        font-size: 0.78rem;
        font-weight: 600;
        color: #0b1a2b;
    }
    .employee-shift {
        font-size: 0.7rem;
        color: #475569;
    }
    .btn-action.dashboard {
        border-color: #d0e2ff;
        color: #1a6bc4;
    }
    .btn-action.dashboard:hover {
        background: #e6f0ff;
    }
</style>

<!-- ============================================
     PAGE HTML
     ============================================ -->
<div class="datatable-wrapper mt-4">
    <div class="datatable-header">
        <h5>Employee List <small><?= count($employees ?? []) ?> enrolled</small></h5>
        <a href="<?= site_url('/admin/employees/create') ?>" class="btn-register">
            <i class="bi bi-person-plus-fill"></i> Enroll Employee
        </a>
    </div>

    <div class="table-responsive">
        <table id="employeesTable" class="table-custom" style="width:100%">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th style="width:60px;">Photo</th>
                    <th>Employee Details</th>
                    <th>Contact Info</th>
                    <th>Role & Branch</th>
                    <th>Salary & Shift</th>
                    <th style="width:80px;">Status</th>
                    <th style="width:130px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($employees)): ?>
                    <?php foreach ($employees as $emp): ?>
                        <tr>
                            <td></td> <!-- Empty - DataTable will fill this -->
                            <td>
                                <?php if (!empty($emp['photo'])): ?>
                                    <img src="<?= site_url($emp['photo']) ?>" alt="Photo" class="employee-photo">
                                <?php else: ?>
                                    <div class="employee-photo-placeholder">
                                        <i class="bi bi-person"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="employee-name"><?= esc($emp['username']) ?></div>
                                <div class="employee-id">ID: EMP-<?= esc((string)$emp['id']) ?></div>
                            </td>
                            <td>
                                <div class="employee-email"><i class="bi bi-envelope me-1"></i> <?= esc($emp['email']) ?></div>
                                <?php if (!empty($emp['phone'])): ?>
                                    <div class="employee-email"><i class="bi bi-telephone me-1"></i> <?= esc($emp['phone']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="employee-role"><?= esc($emp['role_name']) ?></div>
                                <div class="employee-branch"><i class="bi bi-building me-1"></i> <?= $emp['branch_name'] ? esc($emp['branch_name']) : 'Multi-Branch' ?></div>
                            </td>
                            <td>
                                <div class="employee-salary">₹<?= esc(number_format((float)$emp['salary'], 2)) ?></div>
                                <div class="employee-shift"><i class="bi bi-clock me-1"></i> <?= esc(date('h:i A', strtotime($emp['shift_start']))) ?> - <?= esc(date('h:i A', strtotime($emp['shift_end']))) ?></div>
                            </td>
                            <td>
                                <span class="badge-status <?= ($emp['user_status'] ?? 'inactive') === 'active' ? 'active' : 'inactive' ?>">
                                    <?= esc(ucfirst($emp['user_status'] ?? 'inactive')) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-group">
                                    <a href="<?= site_url('/admin/employees/edit/' . $emp['id']) ?>" class="btn-action" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <a href="<?= site_url('/admin/employees/delete/' . $emp['id']) ?>" class="btn-action delete" onclick="return confirm('Delete this employee?')" title="Delete">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center;padding:2.5rem 1rem;color:#94a3b8;">
                            No employees enrolled yet. Click "Enroll Employee" to add one.
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

<script src="<?= asset('js/datatable.js') ?>"></script>

<script>
$(document).ready(function() {
    if ($('#employeesTable').length) {
        var table = $('#employeesTable').DataTable({
            dom: '<"d-flex flex-wrap align-items-center justify-content-between gap-2 p-2"lBf>t<"d-flex flex-wrap align-items-center justify-content-between gap-2 p-2"ip>',
            buttons: [
                { extend: 'copy', text: '<i class="bi bi-copy"></i> Copy', className: 'btn btn-sm' },
                { extend: 'csv', text: '<i class="bi bi-file-earmark-spreadsheet"></i> CSV', className: 'btn btn-sm' },
                { extend: 'excel', text: '<i class="bi bi-file-earmark-excel"></i> Excel', className: 'btn btn-sm' },
                { extend: 'pdf', text: '<i class="bi bi-file-earmark-pdf"></i> PDF', className: 'btn btn-sm' },
                { extend: 'print', text: '<i class="bi bi-printer"></i> Print', className: 'btn btn-sm' }
            ],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            order: [[2, 'asc']],
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    searchable: false,
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).addClass('sno');
                    }
                },
                { orderable: false, targets: [1, 7] },
                { searchable: false, targets: [1, 7] }
            ],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_",
                info: "_START_ – _END_ of _TOTAL_",
                infoEmpty: "No employees found",
                infoFiltered: "(filtered from _MAX_ total)",
                zeroRecords: "No matching employees found"
            },
            drawCallback: function(settings) {
                // Update SNO after each draw
                var api = this.api();
                var rows = api.rows({ page: 'current' }).nodes();
                var last = null;
                
                api.column(0, { page: 'current' }).data().each(function(data, index) {
                    var row = rows[index];
                    var cell = $(row).find('td:first-child');
                    var pageInfo = api.page.info();
                    var displayIndex = pageInfo.start + index + 1;
                    cell.text(displayIndex);
                });
            }
        });
    }
});
</script>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>