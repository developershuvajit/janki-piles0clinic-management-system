<?php 
if (!defined('ROOT_PATH')) {
    exit('No direct script access allowed');
}
$activePage = 'logs';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Audit Logs Table -->
<div class="table-responsive border-0">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
            <tr>
                <th>Timestamp</th>
                <th>Administrator</th>
                <th>Action Triggered</th>
                <th>Audit Details</th>
                <th>Client IP</th>
                <th>User Agent</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
                        No audit logs captured in the system database yet.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="small fw-semibold text-nowrap"><?= esc($log['created_at']) ?></td>
                        <td>
                            <?php if ($log['username']): ?>
                                <span class="badge bg-secondary px-2.5 py-1.5"><i class="bi bi-person-fill me-1"></i><?= esc($log['username']) ?></span>
                            <?php else: ?>
                                <span class="badge bg-light text-muted px-2.5 py-1.5">System/Anon</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="text-success fw-medium"><?= esc($log['action']) ?></span>
                        </td>
                        <td class="small text-wrap" style="max-width: 250px;"><?= esc($log['details']) ?></td>
                        <td class="small text-muted"><?= esc($log['ip_address']) ?></td>
                        <td class="small text-muted text-truncate" style="max-width: 180px;" title="<?= esc($log['user_agent']) ?>">
                            <?= esc($log['user_agent']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
