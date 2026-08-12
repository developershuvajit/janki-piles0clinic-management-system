<?php 
$activePage = 'branches';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- ============================================
     PAGE CSS
     ============================================ -->
<link rel="stylesheet" href="<?= asset('css/datatable.css') ?>">

<style>
    /* Branch specific styles */
    .branch-logo {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 3px;
        background: #fff;
    }
    .branch-logo-placeholder {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: #f1f4f8;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 1.5rem;
    }
    .branch-name {
        font-weight: 600;
        color: #0b1a2b;
        font-size: 0.85rem;
    }
    .branch-address {
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 1px;
    }
    .branch-phone {
        font-size: 0.78rem;
        color: #1e293b;
    }
    .branch-emergency {
        font-size: 0.7rem;
        color: #b33c3c;
    }
    .branch-email {
        font-size: 0.7rem;
        color: #6b7a8f;
    }
    .branch-hours {
        font-size: 0.75rem;
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
        <h5>Branch Management <small><?= count($branches ?? []) ?> branches</small></h5>
        <a href="<?= site_url('/admin/branches/create') ?>" class="btn-register">
            <i class="bi bi-plus-circle-fill"></i> Add Branch
        </a>
    </div>

    <div class="table-responsive">
        <table id="branchesTable" class="table-custom" style="width:100%">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th style="width:60px;">Logo</th>
                    <th>Branch Details</th>
                    <th>Contact Info</th>
                    <th>Hours</th>
                    <th style="width:80px;">Status</th>
                    <th style="width:130px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($branches)): ?>
                    <?php foreach ($branches as $branch): ?>
                        <tr>
                            <td></td> <!-- Empty - DataTable will fill this -->
                            <td>
                                <?php if (!empty($branch['logo'])): ?>
                                    <img src="<?= site_url($branch['logo']) ?>" alt="Logo" class="branch-logo">
                                <?php else: ?>
                                    <div class="branch-logo-placeholder">
                                        <i class="bi bi-building"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="branch-name"><?= esc($branch['name']) ?></div>
                                <div class="branch-address"><?= esc($branch['address']) ?></div>
                            </td>
                            <td>
                                <div class="branch-phone"><i class="bi bi-telephone me-1"></i> <?= esc($branch['phone']) ?></div>
                                <div class="branch-emergency"><i class="bi bi-exclamation-circle me-1"></i> <?= esc($branch['emergency_number']) ?></div>
                                <div class="branch-email"><i class="bi bi-envelope me-1"></i> <?= esc($branch['email']) ?></div>
                            </td>
                            <td class="branch-hours"><?= esc($branch['opening_hours']) ?></td>
                            <td>
                                <span class="badge-status <?= ($branch['status'] ?? 'active') === 'active' ? 'active' : 'inactive' ?>">
                                    <?= esc(ucfirst($branch['status'] ?? 'active')) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-group">
                                    <?php if (\App\Helpers\Permission::has('view_branch_dashboard')): ?>
                                    <a href="<?= site_url('/admin/branches/dashboard/' . $branch['id']) ?>" class="btn-action dashboard" title="Dashboard">
                                        <i class="bi bi-speedometer2"></i>
                                    </a>
                                    <?php endif; ?>
                                    <a href="<?= site_url('/admin/branches/edit/' . $branch['id']) ?>" class="btn-action" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <a href="<?= site_url('/admin/branches/delete/' . $branch['id']) ?>" class="btn-action delete" onclick="return confirm('Delete this branch?')" title="Delete">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center;padding:2.5rem 1rem;color:#94a3b8;">
                            No branches configured yet. Click "Add Branch" to create one.
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
    if ($('#branchesTable').length) {
        var table = $('#branchesTable').DataTable({
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
                { orderable: false, targets: [1, 6] },
                { searchable: false, targets: [1, 6] }
            ],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_",
                info: "_START_ – _END_ of _TOTAL_",
                infoEmpty: "No branches found",
                infoFiltered: "(filtered from _MAX_ total)",
                zeroRecords: "No matching branches found"
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