 <?php 
$activePage = 'permissions';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- ============================================
     PAGE CSS
     ============================================ -->
<link rel="stylesheet" href="<?= asset('css/datatable.css') ?>">

<style>
    .permission-module-badge {
        background: #2563eb;
        color: #fff;
        font-size: 0.65rem;
        padding: 0.15rem 0.6rem;
        border-radius: 20px;
        font-weight: 600;
    }
    .permission-name {
        font-weight: 500;
        color: #0b1a2b;
        font-size: 0.85rem;
    }
    .permission-slug {
        font-size: 0.7rem;
        color: #94a3b8;
        background: #f1f4f8;
        padding: 0.15rem 0.6rem;
        border-radius: 20px;
        display: inline-block;
    }
    .permission-desc {
        font-size: 0.78rem;
        color: #475569;
    }
</style>

<!-- ============================================
     PAGE HTML
     ============================================ -->
<div class="datatable-wrapper mt-4">
    <div class="datatable-header">
        <h5>Permissions Management <small><?= count($permissions ?? []) ?> permissions</small></h5>
        <div>
            <span class="badge-status active me-2">Total: <?= count($permissions ?? []) ?></span>
            <a href="<?= site_url('/admin/roles') ?>" class="btn-back">
                <i class="bi bi-arrow-left"></i> Back to Roles
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table id="permissionsTable" class="table-custom" style="width:100%">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Permission Name</th>
                    <th>Slug</th>
                    <th>Module</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($permissions)): ?>
                    <?php foreach ($permissions as $perm): ?>
                        <tr>
                            <td></td>
                            <td>
                                <div class="permission-name"><?= esc($perm['name']) ?></div>
                            </td>
                            <td>
                                <span class="permission-slug"><?= esc($perm['slug']) ?></span>
                            </td>
                            <td>
                                <span class="permission-module-badge"><?= esc($perm['module']) ?></span>
                            </td>
                            <td>
                                <div class="permission-desc"><?= esc($perm['description'] ?? '') ?></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center;padding:2.5rem 1rem;color:#94a3b8;">
                            No permissions found.
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
    if ($('#permissionsTable').length) {
        var table = $('#permissionsTable').DataTable({
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
            order: [[3, 'asc'], [1, 'asc']],
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    searchable: false,
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).addClass('sno');
                    }
                }
            ],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_",
                info: "_START_ – _END_ of _TOTAL_",
                infoEmpty: "No permissions found",
                infoFiltered: "(filtered from _MAX_ total)",
                zeroRecords: "No matching permissions found"
            },
            drawCallback: function(settings) {
                var api = this.api();
                var rows = api.rows({ page: 'current' }).nodes();
                
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