<?php include VIEWS_PATH . '/layout/public_header.php'; ?>

<section class="py-5 bg-gradient-hero">
    <div class="container py-4 text-center">
        <h1 class="display-5 fw-bold text-slate mb-2">Media & Clinic Gallery</h1>
        <p class="lead text-muted max-width-600 mx-auto">Explore snapshots of our clinical rooms, laboratories, and patient lounges.</p>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <!-- Loop Albums -->
        <?php if (empty($albums)): ?>
            <div class="text-center py-5 text-muted">No media albums configured in CMS.</div>
        <?php else: ?>
            <?php foreach ($albums as $al): ?>
                <div class="mb-5">
                    <h4 class="fw-bold text-slate mb-1"><?= esc($al['name']) ?></h4>
                    <p class="text-muted small mb-4"><?= esc($al['description']) ?></p>

                    <div class="row g-4">
                        <?php 
                        $items = $albumMedia[$al['id']] ?? [];
                        if (empty($items)):
                        ?>
                            <div class="col-12 text-muted small">No photos or videos added to this album.</div>
                        <?php else: ?>
                            <?php foreach ($items as $item): ?>
                                <div class="col-md-4 col-sm-6">
                                    <div class="card border-0 shadow-sm overflow-hidden h-100 glass-card">
                                        <?php if ($item['type'] === 'photo'): ?>
                                            <img src="<?= site_url($item['url']) ?>" class="img-fluid" alt="<?= esc($item['caption']) ?>" style="height: 230px; width: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="position-relative" style="height: 230px; background: #000;">
                                                <div class="d-flex align-items-center justify-content-center h-100">
                                                    <i class="bi bi-play-btn-fill text-danger fs-1"></i>
                                                </div>
                                                <a href="<?= esc($item['url']) ?>" class="stretched-link" target="_blank" title="Play Video"></a>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($item['caption']): ?>
                                            <div class="card-body py-2 px-3 bg-light">
                                                <span class="small text-slate fw-semibold"><?= esc($item['caption']) ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <hr class="border-light my-5">
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php include VIEWS_PATH . '/layout/public_footer.php'; ?>
