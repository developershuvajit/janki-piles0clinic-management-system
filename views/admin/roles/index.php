<?php 
$activePage = 'roles';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- ============================================
     PAGE CSS
     ============================================ -->
<link rel="stylesheet" href="<?= asset('css/datatable.css') ?>">

<style>
    .role-name {
        font-weight: 600;
        color: #0b1a2b;
        font-size: 0.9rem;
    }
    .role-slug {
        font-size: 0.75rem;
        color: #94a3b8;
        background: #f1f4f8;
        padding: 0.15rem 0.6rem;
        border-radius: 20px;
        display: inline-block;
    }
    .role-description {
        font-size: 0.78rem;
        color: #475569;
    }
    .badge-system {
        background: #e6f0ff;
        color: #1a6bc4;
        font-size: 0.6rem;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-weight: 600;
    }
    .badge-custom {
        background: #e6f5ed;
        color: #0b6e44;
        font-size: 0.6rem;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-weight: 600;
    }
    .badge-users {
        background: #2563eb;
        color: #fff;
        font-size: 0.7rem;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-weight: 600;
    }
    .badge-permissions {
        background: #0f7b4a;
        color: #fff;
        font-size: 0.7rem;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-weight: 600;
    }
    .action-group {
        display: flex;
        gap: 0.3rem;
        flex-wrap: wrap;
    }
    .btn-action {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #1e293b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
        text-decoration: none;
        font-size: 0.8rem;
    }
    .btn-action:hover {
        background: #f5f7fa;
        border-color: #cbd5e1;
    }
    .btn-action.edit:hover {
        background: #e6f0ff;
        border-color: #2563eb;
        color: #2563eb;
    }
    .btn-action.delete:hover {
        background: #fee2e2;
        border-color: #ef4444;
        color: #ef4444;
    }
    .btn-action.permissions:hover {
        background: #e6f5ed;
        border-color: #0f7b4a;
        color: #0f7b4a;
    }
</style>

<!-- ============================================
     PAGE HTML
     ============================================ -->
<div class="datatable-wrapper mt-4">
    <div class="datatable-header">
        <h5>Role Management <small><?= count($roles ?? []) ?> roles</small></h5>
        <a href="<?= site_url('/admin/roles/create') ?>" class="btn-register">
            <i class="bi bi-shield-plus"></i> Create Role
        </a>
    </div>

    <div class="table-responsive">
        <table id="rolesTable" class="table-custom" style="width:100%">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Role Name</th>
                    <th>Slug</th>
                    <th>Description</th>
                    <th style="width:100px;">Users</th>
                    <th style="width:100px;">Permissions</th>
                    <th style="width:80px;">Status</th>
                    <th style="width:150px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($roles)): ?>
                    <?php foreach ($roles as $role): 
                        $isSystem = in_array($role['id'], [1, 2, 3, 4]);
                    ?>
                        <tr>
                            <td></td>
                            <td>
                                <div class="role-name"><?= esc($role['name']) ?></div>
                                <?php if ($isSystem): ?>
                                    <span class="badge-system"><i class="bi bi-shield-fill-check me-1"></i>System</span>
                                <?php else: ?>
                                    <span class="badge-custom"><i class="bi bi-plus-circle me-1"></i>Custom</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="role-slug"><?= esc($role['slug']) ?></span>
                            </td>
                            <td>
                                <div class="role-description"><?= esc($role['description'] ?? 'No description') ?></div>
                            </td>
                            <td>
                                <span class="badge-users"><?= $role['user_count'] ?? 0 ?></span>
                            </td>
                            <td>
                                <span class="badge-permissions"><?= $role['permission_count'] ?? 0 ?></span>
                            </td>
                            <td>
                                <span class="badge-status <?= ($role['status'] ?? 'active') === 'active' ? 'active' : 'inactive' ?>">
                                    <?= esc(ucfirst($role['status'] ?? 'active')) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-group">
                                    <a href="<?= site_url('/admin/roles/edit/' . $role['id']) ?>" class="btn-action edit" title="Edit Role">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <a href="<?= site_url('/admin/roles/permissions/' . $role['id']) ?>" class="btn-action permissions" title="Manage Permissions">
                                        <i class="bi bi-list-check"></i>
                                    </a>
                                    <?php if (!$isSystem): ?>
                                        <a href="<?= site_url('/admin/roles/delete/' . $role['id']) ?>" class="btn-action delete" onclick="return confirm('Delete this role? This cannot be undone.')" title="Delete Role">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center;padding:2.5rem 1rem;color:#94a3b8;">
                            No roles found. Click "Create Role" to add one.
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
    if ($('#rolesTable').length) {
        var table = $('#rolesTable').DataTable({
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
            order: [[1, 'asc']],
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    searchable: false,
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).addClass('sno');
                    }
                },
                { orderable: false, targets: [7] },
                { searchable: false, targets: [7] }
            ],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_",
                info: "_START_ – _END_ of _TOTAL_",
                infoEmpty: "No roles found",
                infoFiltered: "(filtered from _MAX_ total)",
                zeroRecords: "No matching roles found"
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