<?php include VIEWS_PATH . '/layout/public_header.php'; ?>

<style>
    /* ============================================
       TREATMENTS PAGE - CLEAN MODERN DESIGN
       ============================================ */
    
    /* ----- Page Header ----- */
    .jpk-treatments-header {
        background: linear-gradient(145deg, #f8fafc, #ecfdf5);
        padding: 3.5rem 0 2.5rem;
        border-bottom: 1px solid #eef2f6;
        position: relative;
        overflow: hidden;
    }
    .jpk-treatments-header::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(5,150,105,0.04), transparent 70%);
        border-radius: 50%;
    }
    .jpk-treatments-header .container { position: relative; z-index: 1; }
    .jpk-treatments-header .badge {
        background: rgba(5,150,105,0.08);
        color: #059669;
        border: 1px solid rgba(5,150,105,0.1);
        padding: 0.15rem 1rem;
        border-radius: 40px;
        font-size: 0.65rem;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 0.5rem;
    }
    .jpk-treatments-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        color: #0b1a2b;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
    }
    .jpk-treatments-header p {
        font-size: 1.05rem;
        color: #475569;
        max-width: 650px;
        margin: 0 auto;
        line-height: 1.7;
    }

    /* ----- Treatment Cards ----- */
    .jpk-treatment-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #eef2f6;
        transition: all 0.3s ease;
        height: 100%;
        box-shadow: 0 2px 12px rgba(0,0,0,0.03);
    }
    .jpk-treatment-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 48px rgba(0,0,0,0.06);
        border-color: #b8e0cf;
    }
    .jpk-treatment-card .card-image {
        height: 180px;
        background: linear-gradient(145deg, #e6f5ed, #d1f0e3);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    .jpk-treatment-card .card-image .icon-large {
        font-size: 4rem;
        color: #059669;
        opacity: 0.3;
        position: absolute;
        right: -20px;
        bottom: -20px;
        transform: rotate(-10deg);
    }
    .jpk-treatment-card .card-image .treatment-icon {
        width: 70px;
        height: 70px;
        background: rgba(255,255,255,0.85);
        backdrop-filter: blur(8px);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: #059669;
        border: 1px solid rgba(255,255,255,0.3);
        box-shadow: 0 4px 16px rgba(5,150,105,0.06);
        z-index: 1;
    }
    .jpk-treatment-card .card-image .badge-laser {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(8px);
        padding: 0.2rem 0.8rem;
        border-radius: 40px;
        font-size: 0.55rem;
        font-weight: 600;
        color: #059669;
        border: 1px solid rgba(255,255,255,0.3);
    }
    .jpk-treatment-card .card-image .badge-laser i {
        margin-right: 0.2rem;
    }
    .jpk-treatment-card .card-body {
        padding: 1.2rem 1.5rem 1.5rem;
    }
    .jpk-treatment-card .card-body h5 {
        font-weight: 700;
        color: #0b1a2b;
        font-size: 1.05rem;
        margin-bottom: 0.3rem;
    }
    .jpk-treatment-card .card-body .desc {
        color: #64748b;
        font-size: 0.85rem;
        line-height: 1.6;
        margin-bottom: 0.8rem;
    }
    .jpk-treatment-card .card-body .highlights {
        display: flex;
        flex-wrap: wrap;
        gap: 0.3rem 0.8rem;
        background: #f8fafc;
        padding: 0.4rem 0.8rem;
        border-radius: 10px;
        margin-bottom: 0.8rem;
        font-size: 0.7rem;
        color: #475569;
    }
    .jpk-treatment-card .card-body .highlights span i {
        color: #059669;
        margin-right: 0.2rem;
    }
    .jpk-treatment-card .card-body .actions {
        display: flex;
        gap: 0.6rem;
        padding-top: 0.6rem;
        border-top: 1px solid #eef2f6;
    }
    .jpk-treatment-card .card-body .actions .btn-primary {
        background: linear-gradient(135deg, #059669, #047857);
        color: #fff;
        padding: 0.3rem 1.2rem;
        border-radius: 40px;
        font-size: 0.78rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.25s;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        border: none;
        flex: 1;
        justify-content: center;
    }
    .jpk-treatment-card .card-body .actions .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(5,150,105,0.2);
        color: #fff;
    }
    .jpk-treatment-card .card-body .actions .btn-outline {
        border: 1px solid #e2e8f0;
        color: #1e293b;
        padding: 0.3rem 1.2rem;
        border-radius: 40px;
        font-size: 0.78rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.25s;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        background: #fff;
    }
    .jpk-treatment-card .card-body .actions .btn-outline:hover {
        border-color: #059669;
        color: #059669;
    }

    /* ----- Responsive ----- */
    @media (max-width: 992px) {
        .jpk-treatments-header h1 { font-size: 2rem; }
        .jpk-treatment-card .card-image { height: 140px; }
        .jpk-treatment-card .card-image .icon-large { font-size: 3rem; }
        .jpk-treatment-card .card-image .treatment-icon { width: 55px; height: 55px; font-size: 1.5rem; }
    }
    @media (max-width: 576px) {
        .jpk-treatments-header { padding: 2rem 0 1.5rem; }
        .jpk-treatments-header h1 { font-size: 1.5rem; }
        .jpk-treatments-header p { font-size: 0.9rem; }
        .jpk-treatment-card .card-body { padding: 1rem; }
        .jpk-treatment-card .card-body h5 { font-size: 0.95rem; }
        .jpk-treatment-card .card-body .desc { font-size: 0.8rem; }
        .jpk-treatment-card .card-body .actions { flex-direction: column; }
        .jpk-treatment-card .card-body .actions .btn-primary,
        .jpk-treatment-card .card-body .actions .btn-outline {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<!-- ============================================
     PAGE HEADER
     ============================================ -->
<section class="jpk-treatments-header">
    <div class="container text-center">
        <span class="badge">GERMAN LASER SURGERY CENTER</span>
        <h1>Painless Proctology &amp; Daycare Surgeries</h1>
        <p>Explore our specialized minimally invasive daycare laser procedures for Piles, Fissure, Fistula, Pilonidal Sinus, Circumcision &amp; Hernia.</p>
    </div>
</section>

<!-- ============================================
     TREATMENTS CATALOG
     ============================================ -->
<section class="py-4 bg-white">
    <div class="container">
        <div class="row g-4">
            <?php if (!empty($treatments)): ?>
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
                $imageMap = [
                    'piles-treatment' => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=400&h=200&fit=crop',
                    'fissure-treatment' => 'https://images.unsplash.com/photo-1584362917165-526a96857948?w=400&h=200&fit=crop',
                    'fistula-treatment' => 'https://images.unsplash.com/photo-1581591524425-c7e10ed20f44?w=400&h=200&fit=crop',
                    'pilonidal-sinus-treatment' => 'https://images.unsplash.com/photo-1516549655169-df83a0774514?w=400&h=200&fit=crop',
                    'circumcision' => 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=400&h=200&fit=crop',
                    'hydrocele-treatment' => 'https://images.unsplash.com/photo-1582750433449-648ed127bb54?w=400&h=200&fit=crop',
                    'hernia-surgery' => 'https://images.unsplash.com/photo-1516549655169-df83a0774514?w=400&h=200&fit=crop',
                    'constipation-treatment' => 'https://images.unsplash.com/photo-1582750433449-648ed127bb54?w=400&h=200&fit=crop'
                ];
                foreach ($treatments as $tr): 
                    $iconClass = $iconMap[$tr['slug']] ?? 'bi-heart-pulse-fill';
                    $imageUrl = $imageMap[$tr['slug']] ?? 'https://images.unsplash.com/photo-1584362917165-526a96857948?w=400&h=200&fit=crop';
                ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="jpk-treatment-card">
                            <div class="card-image">
                                <img src="<?= $imageUrl ?>" alt="<?= esc($tr['title']) ?>" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;opacity:0.15;">
                                <span class="icon-large"><i class="bi <?= $iconClass ?>"></i></span>
                                <div class="treatment-icon">
                                    <i class="bi <?= $iconClass ?>"></i>
                                </div>
                                <span class="badge-laser"><i class="bi bi-lightning-charge-fill"></i> 30-Min Daycare</span>
                            </div>
                            <div class="card-body">
                                <h5><?= esc($tr['title']) ?></h5>
                                <p class="desc"><?= esc($tr['content']) ?></p>
                                <div class="highlights">
                                    <span><i class="bi bi-check-circle-fill"></i> Zero Cuts</span>
                                    <span><i class="bi bi-check-circle-fill"></i> 24h Recovery</span>
                                    <span><i class="bi bi-check-circle-fill"></i> Cashless TPA</span>
                                </div>
                                <div class="actions">
                                    <a href="<?= site_url('/treatments/' . $tr['slug']) ?>" class="btn-primary">
                                        Learn More <i class="bi bi-arrow-right-short"></i>
                                    </a>
                                    <a href="<?= site_url('/appointments/book') ?>" class="btn-outline">
                                        Book Slot
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback Static Treatments -->
                <?php 
                $fallbackTreatments = [
                    ['slug' => 'piles-treatment', 'title' => 'Laser Piles Surgery (LHP)', 'content' => 'Painless laser treatment for Grade 1-4 hemorrhoids with zero cuts and same-day discharge.', 'icon' => 'bi-activity'],
                    ['slug' => 'fissure-treatment', 'title' => 'Laser Fissure Surgery', 'content' => 'Advanced laser sphincterotomy for chronic anal fissures with minimal pain and quick recovery.', 'icon' => 'bi-lightning-charge-fill'],
                    ['slug' => 'fistula-treatment', 'title' => 'FiLaC Laser Fistula Surgery', 'content' => 'Minimally invasive laser treatment for anal fistulas with sphincter preservation.', 'icon' => 'bi-shield-plus'],
                    ['slug' => 'pilonidal-sinus-treatment', 'title' => 'SiLaC Pilonidal Sinus Laser', 'content' => 'Laser ablation for pilonidal sinus with minimal scarring and fast return to daily activities.', 'icon' => 'bi-bandaid-fill'],
                    ['slug' => 'circumcision', 'title' => 'ZSR Stapler Circumcision', 'content' => 'Painless, bloodless circumcision with ZSR stapler technique, completed in 15 minutes.', 'icon' => 'bi-scissors'],
                    ['slug' => 'hernia-surgery', 'title' => 'Laparoscopic Hernia Repair', 'content' => 'Minimally invasive hernia repair with laparoscopic mesh technique and same-day discharge.', 'icon' => 'bi-capsule-fill']
                ];
                foreach ($fallbackTreatments as $tr): 
                ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="jpk-treatment-card">
                            <div class="card-image">
                                <img src="https://images.unsplash.com/photo-1584362917165-526a96857948?w=400&h=200&fit=crop" alt="<?= esc($tr['title']) ?>" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;opacity:0.15;">
                                <span class="icon-large"><i class="bi <?= $tr['icon'] ?>"></i></span>
                                <div class="treatment-icon">
                                    <i class="bi <?= $tr['icon'] ?>"></i>
                                </div>
                                <span class="badge-laser"><i class="bi bi-lightning-charge-fill"></i> 30-Min Daycare</span>
                            </div>
                            <div class="card-body">
                                <h5><?= esc($tr['title']) ?></h5>
                                <p class="desc"><?= esc($tr['content']) ?></p>
                                <div class="highlights">
                                    <span><i class="bi bi-check-circle-fill"></i> Zero Cuts</span>
                                    <span><i class="bi bi-check-circle-fill"></i> 24h Recovery</span>
                                    <span><i class="bi bi-check-circle-fill"></i> Cashless TPA</span>
                                </div>
                                <div class="actions">
                                    <a href="<?= site_url('/treatments/' . $tr['slug']) ?>" class="btn-primary">
                                        Learn More <i class="bi bi-arrow-right-short"></i>
                                    </a>
                                    <a href="<?= site_url('/appointments/book') ?>" class="btn-outline">
                                        Book Slot
                                    </a>
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