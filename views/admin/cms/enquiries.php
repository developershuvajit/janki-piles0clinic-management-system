<?php 
$activePage = 'cms_enquiries';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Lead CRM Dashboard -->
<div class="card border-0 shadow-sm p-4 text-slate">
    <h5 class="fw-bold text-slate mb-3"><i class="bi bi-envelope-paper text-success me-2"></i>CRM Lead Enquiries Pipeline</h5>
    <p class="text-muted small">Monitor, track, and follow up with patients submitting inquiries from public contact portals.</p>

    <div class="table-responsive border-0 shadow-none">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
            <thead>
                <tr>
                    <th>Prospect Patient</th>
                    <th>Inquiry Details</th>
                    <th>Message</th>
                    <th>CRM Status</th>
                    <th>Follow-Up Notes</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($enquiries)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No lead inquiries captured yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($enquiries as $row): ?>
                        <tr>
                            <td>
                                <div class="fw-bold"><?= esc($row['name']) ?></div>
                                <span class="text-muted small">Email: <?= esc($row['email']) ?></span><br>
                                <span class="text-muted small">Phone: <?= esc($row['phone']) ?></span>
                            </td>
                            <td>
                                <div class="fw-semibold text-slate"><?= esc($row['subject']) ?></div>
                                <span class="text-muted small">Date: <?= esc(date('Y-m-d H:i', strtotime($row['created_at']))) ?></span>
                            </td>
                            <td style="max-width: 250px; white-space: pre-wrap;" class="small text-muted"><?= esc($row['message']) ?></td>
                            <td>
                                <?php if ($row['status'] === 'resolved'): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1.5 rounded">Resolved</span>
                                <?php elseif ($row['status'] === 'contacted'): ?>
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2.5 py-1.5 rounded">Contacted / Active</span>
                                <?php else: ?>
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-1.5 rounded">New Lead</span>
                                <?php endif; ?>
                            </td>
                            <td style="max-width: 200px;" class="small text-truncate" title="<?= esc($row['notes']) ?>"><?= esc($row['notes'] ?: '-') ?></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-primary px-3 py-1 shadow-sm btn-follow-modal" 
                                        data-id="<?= $row['id'] ?>"
                                        data-name="<?= esc($row['name']) ?>"
                                        data-status="<?= esc($row['status']) ?>"
                                        data-notes="<?= esc($row['notes'] ?? '') ?>"
                                        data-bs-toggle="modal" data-bs-target="#followModal">
                                    <i class="bi bi-chat-dots me-1"></i> Follow Up
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Update Follow Up -->
<div class="modal fade" id="followModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg text-slate">
            <form action="<?= site_url('/admin/cms/enquiries/update') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="modal-lead-id">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-chat-dots me-2"></i>CRM Lead Follow-Up</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <div class="bg-light p-3 rounded-3 mb-3 small">
                        <strong>Prospect Name:</strong> <span id="modal-lead-name"></span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Lead Pipeline Status</label>
                        <select class="form-control form-control-sm form-select" name="status" id="modal-lead-status" required>
                            <option value="new">New Lead</option>
                            <option value="contacted">Contacted / Active Dialogue</option>
                            <option value="resolved">Resolved / Settle Inquiry</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Follow-Up Comments / Action Notes</label>
                        <textarea class="form-control form-control-sm" name="notes" id="modal-lead-notes" rows="4" placeholder="Log comments on phone call, email, or meeting details..."></textarea>
                    </div>
                </div>

                <div class="modal-footer pt-0 border-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">Save Lead Log</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const followButtons = document.querySelectorAll('.btn-follow-modal');
    
    followButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('modal-lead-id').value = this.dataset.id;
            document.getElementById('modal-lead-name').innerText = this.dataset.name;
            document.getElementById('modal-lead-status').value = this.dataset.status;
            document.getElementById('modal-lead-notes').value = this.dataset.notes;
        });
    });
});
</script>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
