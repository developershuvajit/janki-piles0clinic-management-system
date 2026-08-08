<?php include VIEWS_PATH . '/layout/public_header.php'; ?>

<section class="py-5 bg-gradient-hero">
    <div class="container py-4 text-center">
        <h1 class="display-5 fw-bold text-slate mb-2">MedClinic Health Blogs</h1>
        <p class="lead text-muted max-width-600 mx-auto">Read health recommendations, cardiology advices, and general wellness tips.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <?php if (empty($blogs)): ?>
                <div class="text-center py-5 text-muted col-12">
                    <i class="bi bi-journal-medical fs-1 d-block mb-3"></i>
                    No health articles published yet.
                </div>
            <?php else: ?>
                <?php foreach ($blogs as $post): ?>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm overflow-hidden glass-card">
                            <?php if ($post['image_url']): ?>
                                <img src="<?= site_url($post['image_url']) ?>" class="card-img-top" alt="<?= esc($post['title']) ?>" style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="bi bi-journal-text text-success fs-1"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body p-4 d-flex flex-column">
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 align-self-start mb-2 small">
                                    <?= esc($post['category_name'] ?: 'Uncategorized') ?>
                                </span>
                                <h5 class="card-title fw-bold text-slate"><?= esc($post['title']) ?></h5>
                                <p class="card-text text-muted small text-truncate-3"><?= esc(strip_tags($post['content'])) ?></p>
                                <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top border-light">
                                    <span class="text-muted x-small"><i class="bi bi-clock me-1"></i> <?= esc(date('M d, Y', strtotime($post['created_at']))) ?></span>
                                    <a href="<?= site_url('/blog/' . $post['slug']) ?>" class="btn btn-outline-success btn-sm px-3 rounded-pill">Read Article &rarr;</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include VIEWS_PATH . '/layout/public_footer.php'; ?>
