<?php 
if (!defined('ROOT_PATH')) {
    exit('No direct script access allowed');
}
$activePage = 'logs';
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
        <h5>Audit Logs <small><?= count($logs ?? []) ?> entries</small></h5>
    </div>

    <div class="table-responsive">
        <table id="logsTable" class="table-custom" style="width:100%">
            <thead>
                <tr>
                    <th class="sno">#</th>
                    <th style="min-width:160px;">Timestamp</th>
                    <th style="min-width:140px;">Administrator</th>
                    <th style="min-width:160px;">Action Triggered</th>
                    <th style="min-width:200px;">Audit Details</th>
                    <th style="width:130px;">Client IP</th>
                    <th style="min-width:180px;">User Agent</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($logs)):
                    $sn = 1;
                    foreach ($logs as $log): ?>
                        <tr>
                            <td class="sno"><?= $sn++ ?></td>
                            <td class="small fw-semibold text-nowrap">
                                <div><?= esc(date('d M, Y', strtotime($log['created_at']))) ?></div>
                                <span class="text-muted small" style="font-size: 0.7rem;">
                                    <i class="bi bi-clock me-1"></i><?= esc(date('h:i A', strtotime($log['created_at']))) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($log['username'])): ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-dark px-2.5 py-1.5">
                                        <i class="bi bi-person-fill me-1"></i><?= esc($log['username']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted px-2.5 py-1.5">System/Anon</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge-status active" style="background: rgba(99, 102, 241, 0.1); color: #6366f1; border-color: rgba(99, 102, 241, 0.25);">
                                    <?= esc($log['action']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="text-muted small" style="font-size: 0.82rem;">
                                    <?= esc($log['details']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2.5 py-1.5" style="font-size: 0.75rem;">
                                    <i class="bi bi-hdd-network me-1"></i><?= esc($log['ip_address']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="text-muted small" style="font-size: 0.75rem;" title="<?= esc($log['user_agent']) ?>">
                                    <?= esc(substr($log['user_agent'], 0, 40)) ?><?= strlen($log['user_agent']) > 40 ? '...' : '' ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center;padding:2.5rem 1rem;color:#94a3b8;">
                            <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
                            No audit logs captured in the system database yet.
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
    $('#logsTable').DataTable({
        pageLength: 25,
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        order: [[0, 'asc']],
        columnDefs: [
            { 
                targets: 0, 
                orderable: true 
            }
        ]
    });
});
</script>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>