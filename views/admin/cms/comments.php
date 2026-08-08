<?php 
$activePage = 'cms_comments';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Comments Ledger -->
<div class="card border-0 shadow-sm p-4 text-slate">
    <h5 class="fw-bold text-slate mb-3"><i class="bi bi-chat-left-text text-success me-2"></i>Comments Moderation Ledger</h5>
    <p class="text-muted small">Moderate discussion comments posted by patients on health blog posts.</p>
    
    <div class="table-responsive border-0 shadow-none">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
            <thead>
                <tr>
                    <th>Blog Article</th>
                    <th>Author Details</th>
                    <th>Comment Text</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($comments)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No blog comments posted.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($comments as $row): ?>
                        <tr>
                            <td class="fw-bold" style="max-width: 180px;"><?= esc($row['blog_title']) ?></td>
                            <td>
                                <strong><?= esc($row['author_name']) ?></strong><br>
                                <span class="text-muted small"><?= esc($row['author_email']) ?></span>
                            </td>
                            <td style="max-width: 300px; white-space: pre-wrap;" class="small"><?= esc($row['comment_text']) ?></td>
                            <td>
                                <?php if ($row['status'] === 'approved'): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1.5 rounded">Approved</span>
                                <?php elseif ($row['status'] === 'rejected'): ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1.5 rounded">Rejected</span>
                                <?php else: ?>
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-1.5 rounded">Pending Approval</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end text-nowrap">
                                <?php if ($row['status'] === 'pending'): ?>
                                    <a href="<?= site_url('/admin/cms/comments/approve/' . $row['id']) ?>" class="btn btn-sm btn-success px-2 py-0.5 me-1 text-white shadow-sm" title="Approve">
                                        <i class="bi bi-check-lg"></i>
                                    </a>
                                    <a href="<?= site_url('/admin/cms/comments/reject/' . $row['id']) ?>" class="btn btn-sm btn-outline-danger px-2 py-0.5" title="Reject">
                                        <i class="bi bi-x-lg"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
