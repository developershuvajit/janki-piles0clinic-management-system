<?php include VIEWS_PATH . '/layout/public_header.php'; ?>

<!-- Header -->
<section class="py-5 bg-gradient-hero border-bottom">
    <div class="container py-3 text-center">
        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1.5 rounded-pill mb-2 small fw-bold">GERMAN LASER SURGERY CENTER</span>
        <h1 class="display-5 fw-extrabold text-slate mb-3">Painless Proctology & Daycare Surgeries</h1>
        <p class="lead text-muted max-width-700 mx-auto">Explore our specialized minimally invasive daycare laser procedures for Piles, Fissure, Fistula, Pilonidal Sinus, Circumcision & Hernia.</p>
    </div>
</section>

<!-- Treatments Catalog -->
<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="row g-4">
            <?php 
            $iconMap = [
                'piles-treatment' => 'bi-activity',
                'fissure-treatment' => 'bi-lightning-charge-fill',
                'fistula-treatment' => 'bi-shield-plus',
                'pilonidal-sinus-treatment' => 'bi-bandaid-fill',
                'circumcision' => 'bi-scissors',
                'hydrocele-treatment' => 'bi-droplet-fill',
                'hernia-surgery' => 'bi-capsule-fill',
                'constipation-treatment' => 'bi-clipboard2-pulse-fill'
            ];
            foreach ($treatments as $tr): 
                $iconClass = $iconMap[$tr['slug']] ?? 'bi-heart-pulse-fill';
            ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 treatment-card border-0 shadow-sm glass-card rounded-4 overflow-hidden position-relative">
                        <div class="treatment-card-top-accent"></div>
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="treatment-icon-box">
                                    <i class="bi <?= $iconClass ?>"></i>
                                </div>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1.5 rounded-pill small fw-bold">
                                    <i class="bi bi-lightning-charge-fill me-1"></i> 30-Min Daycare
                                </span>
                            </div>

                            <h5 class="card-title fw-extrabold text-slate mb-2" style="font-size: 1.15rem; line-height: 1.3;">
                                <?= esc($tr['title']) ?>
                            </h5>

                            <p class="card-text text-muted small mb-4 flex-grow-1 leading-relaxed">
                                <?= esc($tr['content']) ?>
                            </p>

                            <div class="treatment-card-highlights mb-4 p-2.5 rounded-3 bg-slate-50 border border-slate-100">
                                <div class="d-flex flex-wrap gap-2 text-muted small" style="font-size: 0.78rem;">
                                    <span><i class="bi bi-check-circle-fill text-success me-1"></i> Zero Cuts</span>
                                    <span><i class="bi bi-check-circle-fill text-success me-1"></i> 24h Recovery</span>
                                    <span><i class="bi bi-check-circle-fill text-success me-1"></i> Cashless TPA</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between pt-3 border-top border-slate-100 mt-auto">
                                <a href="<?= site_url('/treatments/' . $tr['slug']) ?>" class="btn btn-emerald btn-sm px-3.5 rounded-pill shadow-sm">
                                    Learn Procedure <i class="bi bi-arrow-right-short fs-6"></i>
                                </a>
                                <a href="<?= site_url('/appointments/book') ?>" class="btn btn-outline-success btn-sm px-3 rounded-pill fw-bold">
                                    Book Slot
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include VIEWS_PATH . '/layout/public_footer.php'; ?>
