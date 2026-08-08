<?php 
$activePage = 'cms_testimonials';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<div class="row text-slate">
    <!-- Left Column: Add Review Form -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-star-fill text-success me-2"></i>Log Patient Review</h6>
            
            <form action="<?= site_url('/admin/cms/testimonials/save') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Review Channel Type</label>
                    <select class="form-control form-control-sm form-select" name="type" required>
                        <option value="patient">Direct Patient Review</option>
                        <option value="google">Google Review Screenshot</option>
                        <option value="video">Patient Video Testimonial</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Patient Name *</label>
                    <input type="text" class="form-control form-control-sm" name="author" required placeholder="e.g. Pooja Sharma">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Rating Rating (1 - 5 stars) *</label>
                    <select class="form-control form-control-sm form-select text-warning" name="rating" required>
                        <option value="5">★★★★★ (5 Stars)</option>
                        <option value="4">★★★★☆ (4 Stars)</option>
                        <option value="3">★★★☆☆ (3 Stars)</option>
                        <option value="2">★★☆☆☆ (2 Stars)</option>
                        <option value="1">★☆☆☆☆ (1 Star)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Video URL link (if video testimonial)</label>
                    <input type="text" class="form-control form-control-sm" name="video_url" placeholder="https://www.youtube.com/embed/...">
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold">Patient Evaluation message *</label>
                    <textarea class="form-control form-control-sm" name="review_text" rows="4" required placeholder="Write review description here..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-sm w-100 shadow-sm">
                    <i class="bi bi-send-fill me-1"></i> Settle Review
                </button>
            </form>
        </div>
    </div>

    <!-- Right Column: Reviews Ledger -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-list-check text-success me-2"></i>Patient Evaluations Ledger</h6>
            
            <div class="table-responsive border-0 shadow-none">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.82rem;">
                    <thead>
                        <tr>
                            <th>Patient details</th>
                            <th>Rating</th>
                            <th>Review Message</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($testimonials)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No testimonials registered.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($testimonials as $t): ?>
                                <tr>
                                    <td class="fw-bold"><?= esc($t['author']) ?></td>
                                    <td class="text-warning">
                                        <?php for($i=1; $i<=5; $i++): ?>
                                            <i class="bi bi-star<?= $i <= $t['rating'] ? '-fill' : '' ?>"></i>
                                        <?php endfor; ?>
                                    </td>
                                    <td style="max-width: 300px; white-space: pre-wrap;" class="small text-muted"><?= esc($t['review_text']) ?></td>
                                    <td>
                                        <span class="badge bg-light text-slate border"><?= esc(ucfirst($t['type'])) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
