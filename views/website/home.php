<?php include VIEWS_PATH . '/layout/public_header.php'; ?>

<style>
    /* ============================================
       JANKI PILES CLINIC - MINIMAL MODERN DESIGN
       ============================================ */
    
    /* ----- Global ----- */
    .text-emerald { color: #059669; }
    .bg-emerald { background: #059669; }
    
    /* ----- Hero ----- */
    .jpk-hero-new {
        padding: 3rem 0 4rem;
        position: relative;
        overflow: hidden;
        min-height: 500px;
    }
    .jpk-hero-new .bg-animated {
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse at 70% 30%, #dcfce7, #f0fdf4, #f8fafc);
        z-index: 0;
        animation: jpk-hero-pulse 8s ease-in-out infinite alternate;
    }
    @keyframes jpk-hero-pulse {
        0% { opacity: 0.6; transform: scale(1); }
        100% { opacity: 1; transform: scale(1.02); }
    }
    .jpk-hero-new .container { position: relative; z-index: 1; }
    
    .jpk-hero-badge {
        display: inline-block;
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(8px);
        padding: 0.2rem 1rem;
        border-radius: 50px;
        font-size: 0.65rem;
        font-weight: 600;
        color: #059669;
        border: 1px solid rgba(5,150,105,0.15);
        margin-bottom: 0.8rem;
    }
    .jpk-hero-title {
        font-size: 2.6rem;
        font-weight: 800;
        color: #0b1a2b;
        line-height: 1.1;
        letter-spacing: -0.5px;
    }
    .jpk-hero-title .highlight { color: #059669; }
    .jpk-hero-sub {
        font-size: 1rem;
        color: #475569;
        margin: 0.5rem 0 1.2rem;
        max-width: 500px;
    }
    .jpk-btn-primary {
        background: linear-gradient(135deg, #059669, #047857);
        color: #fff;
        padding: 0.6rem 1.8rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.25s;
        box-shadow: 0 4px 16px rgba(5,150,105,0.2);
    }
    .jpk-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(5,150,105,0.3);
        color: #fff;
    }
    .jpk-btn-outline {
        border: 2px solid #e2e8f0;
        color: #1e293b;
        padding: 0.6rem 1.8rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: rgba(255,255,255,0.6);
        backdrop-filter: blur(8px);
        transition: all 0.25s;
    }
    .jpk-btn-outline:hover {
        border-color: #059669;
        color: #059669;
        transform: translateY(-2px);
    }
    .jpk-hero-features {
        display: flex;
        flex-wrap: wrap;
        gap: 0.8rem 1.2rem;
        margin-top: 1rem;
        padding-top: 0.8rem;
        border-top: 1px solid rgba(0,0,0,0.05);
    }
    .jpk-hero-features span {
        font-size: 0.75rem;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    .jpk-hero-features span i { color: #059669; }

    /* ----- Hero Image ----- */
    .jpk-hero-img-box {
        background: linear-gradient(145deg, #e6f5ed, #d1f0e3);
        border-radius: 20px;
        padding: 0.8rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(5,150,105,0.08);
    }
    .jpk-hero-img-box img {
        width: 100%;
        min-height: 320px;
        object-fit: cover;
        border-radius: 14px;
    }
    .jpk-hero-stats {
        position: absolute;
        bottom: 1.2rem;
        left: 1.2rem;
        right: 1.2rem;
        display: flex;
        justify-content: space-around;
        background: rgba(255,255,255,0.92);
        backdrop-filter: blur(12px);
        border-radius: 12px;
        padding: 0.6rem 0.3rem;
        border: 1px solid rgba(255,255,255,0.3);
    }
    .jpk-hero-stats .stat { text-align: center; }
    .jpk-hero-stats .stat .num {
        display: block;
        font-size: 1rem;
        font-weight: 800;
        color: #059669;
    }
    .jpk-hero-stats .stat .lbl {
        font-size: 0.5rem;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .jpk-hero-badge-float {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(8px);
        padding: 0.2rem 0.7rem;
        border-radius: 40px;
        font-size: 0.55rem;
        font-weight: 600;
        color: #0b1a2b;
        border: 1px solid rgba(255,255,255,0.3);
        display: flex;
        align-items: center;
        gap: 0.3rem;
        animation: jpk-float 4s ease-in-out infinite;
    }
    .jpk-hero-badge-float i { color: #059669; }
    @keyframes jpk-float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }

    /* ----- Trust Bar ----- */
    .jpk-trust {
        background: #fff;
        padding: 2rem 0;
        border-bottom: 1px solid #eef2f6;
    }
    .jpk-trust .item { text-align: center; }
    .jpk-trust .item .num {
        font-size: 2rem;
        font-weight: 800;
        color: #059669;
        display: block;
    }
    .jpk-trust .item .lbl {
        font-size: 0.7rem;
        color: #94a3b8;
        font-weight: 500;
        letter-spacing: 0.3px;
        margin-top: 0.1rem;
    }
    .jpk-trust .item .ico {
        font-size: 1.6rem;
        color: #059669;
        display: block;
        margin-bottom: 0.2rem;
        opacity: 0.5;
    }

    /* ----- Section Headers ----- */
    .jpk-section {
        padding: 3.5rem 0;
    }
    .jpk-section .head {
        text-align: center;
        max-width: 600px;
        margin: 0 auto 2.5rem;
    }
    .jpk-section .head .tag {
        color: #059669;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.7rem;
        display: block;
    }
    .jpk-section .head h2 {
        font-size: 2rem;
        font-weight: 800;
        color: #0b1a2b;
        margin: 0.2rem 0;
    }
    .jpk-section .head p {
        color: #64748b;
        font-size: 0.95rem;
        margin: 0;
    }

    /* ----- Features ----- */
    .jpk-features { background: #f8fafc; }
    .jpk-feature-card {
        background: #fff;
        border-radius: 14px;
        padding: 1.5rem;
        height: 100%;
        border: 1px solid #eef2f6;
        text-align: center;
        transition: all 0.3s;
    }
    .jpk-feature-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.05);
        border-color: #b8e0cf;
    }
    .jpk-feature-card .ico {
        width: 52px;
        height: 52px;
        background: #e6f5ed;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: #059669;
        margin: 0 auto 0.8rem;
        transition: all 0.3s;
    }
    .jpk-feature-card:hover .ico {
        background: #059669;
        color: #fff;
    }
    .jpk-feature-card h5 {
        font-weight: 700;
        color: #0b1a2b;
        font-size: 1rem;
        margin-bottom: 0.3rem;
    }
    .jpk-feature-card p {
        color: #64748b;
        font-size: 0.85rem;
        line-height: 1.6;
        margin: 0;
    }

    /* ----- Treatments ----- */
    .jpk-treatments { background: #fff; border-top: 1px solid #eef2f6; }
    .jpk-treatment-card {
        background: #fff;
        border-radius: 14px;
        padding: 1.2rem;
        border: 1px solid #eef2f6;
        height: 100%;
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }
    .jpk-treatment-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #059669, #0f7b4a);
        opacity: 0;
        transition: opacity 0.3s;
    }
    .jpk-treatment-card:hover::before { opacity: 1; }
    .jpk-treatment-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.05);
    }
    .jpk-treatment-card .top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.6rem;
    }
    .jpk-treatment-card .top .ico {
        width: 40px;
        height: 40px;
        background: #e6f5ed;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        color: #059669;
        transition: all 0.3s;
    }
    .jpk-treatment-card:hover .top .ico {
        background: #059669;
        color: #fff;
    }
    .jpk-treatment-card .top .badge {
        background: rgba(5,150,105,0.08);
        color: #059669;
        border: 1px solid rgba(5,150,105,0.1);
        padding: 0.1rem 0.6rem;
        border-radius: 40px;
        font-size: 0.5rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    .jpk-treatment-card h5 {
        font-weight: 700;
        color: #0b1a2b;
        font-size: 0.95rem;
        margin-bottom: 0.3rem;
    }
    .jpk-treatment-card .desc {
        color: #64748b;
        font-size: 0.8rem;
        line-height: 1.6;
        margin-bottom: 0.6rem;
    }
    .jpk-treatment-card .actions {
        display: flex;
        gap: 0.4rem;
        padding-top: 0.6rem;
        border-top: 1px solid #eef2f6;
    }
    .jpk-treatment-card .actions .btn-sm {
        background: #059669;
        color: #fff;
        padding: 0.2rem 1rem;
        border-radius: 40px;
        font-size: 0.7rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }
    .jpk-treatment-card .actions .btn-sm:hover { background: #047857; }
    .jpk-treatment-card .actions .btn-sm-out {
        border: 1px solid #e2e8f0;
        color: #1e293b;
        padding: 0.2rem 1rem;
        border-radius: 40px;
        font-size: 0.7rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        background: transparent;
    }
    .jpk-treatment-card .actions .btn-sm-out:hover {
        border-color: #059669;
        color: #059669;
    }

    /* ----- Insurance ----- */
    .jpk-insurance {
        background: linear-gradient(135deg, #0b1a2b, #1a365d);
        padding: 3rem 0;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .jpk-insurance::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.03), transparent 70%);
        border-radius: 50%;
    }
    .jpk-insurance .container { position: relative; z-index: 1; }
    .jpk-insurance .badge {
        background: rgba(255,255,255,0.1);
        color: #fff;
        padding: 0.2rem 1rem;
        border-radius: 40px;
        font-size: 0.6rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
        margin-bottom: 0.5rem;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .jpk-insurance h2 {
        font-weight: 800;
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    .jpk-insurance p {
        font-size: 1rem;
        opacity: 0.8;
        line-height: 1.6;
        margin: 0;
    }
    .jpk-btn-light {
        background: #fff;
        color: #0b1a2b;
        padding: 0.6rem 1.8rem;
        border-radius: 50px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.3s;
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }
    .jpk-btn-light:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    }

    /* ----- Testimonials ----- */
    .jpk-testimonials { background: #f8fafc; border-top: 1px solid #eef2f6; }
    .jpk-testimonial-card {
        background: #fff;
        border-radius: 14px;
        padding: 1.5rem;
        border: 1px solid #eef2f6;
        height: 100%;
        transition: all 0.3s;
        position: relative;
    }
    .jpk-testimonial-card::before {
        content: '"';
        position: absolute;
        top: 5px;
        left: 15px;
        font-size: 3rem;
        color: #059669;
        opacity: 0.06;
        font-family: Georgia, serif;
    }
    .jpk-testimonial-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.05);
    }
    .jpk-testimonial-card .stars {
        color: #f59e0b;
        font-size: 0.85rem;
        letter-spacing: 2px;
        margin-bottom: 0.5rem;
    }
    .jpk-testimonial-card .quote {
        color: #1e293b;
        font-size: 0.9rem;
        line-height: 1.7;
        font-style: italic;
        position: relative;
        z-index: 1;
    }
    .jpk-testimonial-card .author {
        color: #059669;
        font-weight: 700;
        font-size: 0.8rem;
        margin-top: 0.8rem;
        text-align: right;
    }

    /* ----- FAQ ----- */
    .jpk-faq { background: #fff; border-top: 1px solid #eef2f6; padding: 3.5rem 0; }
    .jpk-faq .faq-item {
        background: #fff;
        border-radius: 10px;
        border: 1px solid #eef2f6;
        margin-bottom: 0.6rem;
        overflow: hidden;
        transition: all 0.3s;
    }
    .jpk-faq .faq-item:hover { border-color: #b8e0cf; }
    .jpk-faq .faq-item .q {
        padding: 0.8rem 1.2rem;
        background: #fafcff;
        font-weight: 600;
        color: #0b1a2b;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: none;
        width: 100%;
        text-align: left;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    .jpk-faq .faq-item .q:hover { background: #f5f9fc; }
    .jpk-faq .faq-item .q .icon {
        color: #94a3b8;
        font-size: 0.9rem;
        transition: transform 0.3s;
        flex-shrink: 0;
    }
    .jpk-faq .faq-item .q.active .icon {
        transform: rotate(180deg);
        color: #059669;
    }
    .jpk-faq .faq-item .a {
        padding: 0 1.2rem 1rem;
        color: #475569;
        font-size: 0.85rem;
        line-height: 1.7;
        display: none;
    }
    .jpk-faq .faq-item .a.open { display: block; animation: jpk-fade 0.3s ease; }
    @keyframes jpk-fade {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ----- Responsive ----- */
    @media (max-width: 992px) {
        .jpk-hero-new { padding: 2.5rem 0; min-height: auto; }
        .jpk-hero-title { font-size: 2rem; }
        .jpk-hero-img-box img { min-height: 240px; }
        .jpk-section .head h2 { font-size: 1.6rem; }
        .jpk-insurance h2 { font-size: 1.6rem; }
        .jpk-trust .item .num { font-size: 1.6rem; }
    }
    @media (max-width: 576px) {
        .jpk-hero-new { padding: 1.5rem 0; }
        .jpk-hero-title { font-size: 1.5rem; }
        .jpk-hero-sub { font-size: 0.85rem; }
        .jpk-hero-buttons .jpk-btn-primary,
        .jpk-hero-buttons .jpk-btn-outline {
            padding: 0.4rem 1.2rem;
            font-size: 0.75rem;
            width: 100%;
            justify-content: center;
        }
        .jpk-hero-features { gap: 0.4rem 0.8rem; }
        .jpk-hero-features span { font-size: 0.65rem; }
        .jpk-hero-img-box img { min-height: 160px; }
        .jpk-hero-stats { padding: 0.4rem; bottom: 0.8rem; left: 0.8rem; right: 0.8rem; }
        .jpk-hero-stats .stat .num { font-size: 0.8rem; }
        .jpk-hero-stats .stat .lbl { font-size: 0.4rem; }
        .jpk-hero-badge-float { display: none; }
        .jpk-trust .item .num { font-size: 1.2rem; }
        .jpk-trust .item .lbl { font-size: 0.6rem; }
        .jpk-section .head h2 { font-size: 1.3rem; }
        .jpk-feature-card { padding: 1rem; }
        .jpk-treatment-card { padding: 1rem; }
        .jpk-testimonial-card { padding: 1rem; }
        .jpk-insurance h2 { font-size: 1.3rem; }
        .jpk-insurance p { font-size: 0.85rem; }
        .jpk-insurance { padding: 2rem 0; }
        .jpk-treatment-card .actions .btn-sm,
        .jpk-treatment-card .actions .btn-sm-out { font-size: 0.6rem; padding: 0.15rem 0.6rem; }
        .jpk-faq .faq-item .q { font-size: 0.8rem; padding: 0.6rem 1rem; }
    }
</style>

<!-- ============================================
     HERO SECTION
     ============================================ -->
<section class="jpk-hero-new">
    <div class="bg-animated"></div>
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <div class="jpk-hero-badge">
                    <i class="bi bi-shield-check"></i> Premier Laser Proctology Center
                </div>
                <h1 class="jpk-hero-title">
                    Painless <span class="highlight">German Laser</span> Surgery
                </h1>
                <p class="jpk-hero-sub">
                    Zero cuts. No stitches. Same-day discharge.
                </p>
                <div class="jpk-hero-buttons">
                    <a href="<?= site_url('/appointments/book') ?>" class="jpk-btn-primary">
                        <i class="bi bi-calendar-plus"></i> Book Consultation
                    </a>
                    <a href="tel:+919876543210" class="jpk-btn-outline">
                        <i class="bi bi-telephone-fill"></i> Call Now
                    </a>
                </div>
                <div class="jpk-hero-features">
                    <span><i class="bi bi-check-circle-fill"></i> Cashless TPA</span>
                    <span><i class="bi bi-check-circle-fill"></i> Female Chaperones</span>
                    <span><i class="bi bi-check-circle-fill"></i> 100% Confidential</span>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="jpk-hero-img-box">
                    <img src="https://images.unsplash.com/photo-1631815588090-d4bfec5b1ccb?w=600&h=420&fit=crop&crop=center&q=80" 
                         alt="Laser Surgery">
                    <div class="jpk-hero-stats">
                        <div class="stat"><span class="num">15K+</span><span class="lbl">Surgeries</span></div>
                        <div class="stat"><span class="num">7+</span><span class="lbl">Branches</span></div>
                        <div class="stat"><span class="num">100%</span><span class="lbl">Cashless</span></div>
                    </div>
                    <div class="jpk-hero-badge-float">
                        <i class="bi bi-clock"></i> Same-Day Discharge
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     TRUST BAR
     ============================================ -->
<section class="jpk-trust">
    <div class="container">
        <div class="row g-2">
            <div class="col-6 col-md-3">
                <div class="item">
                    <span class="ico"><i class="bi bi-heart-pulse-fill"></i></span>
                    <span class="num">15,000+</span>
                    <span class="lbl">Successful Surgeries</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="item">
                    <span class="ico"><i class="bi bi-building"></i></span>
                    <span class="num">7+</span>
                    <span class="lbl">Clinical Branches</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="item">
                    <span class="ico"><i class="bi bi-clock"></i></span>
                    <span class="num">30 Min</span>
                    <span class="lbl">Daycare Procedure</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="item">
                    <span class="ico"><i class="bi bi-shield-check"></i></span>
                    <span class="num">100%</span>
                    <span class="lbl">Cashless TPA</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     WHY CHOOSE US
     ============================================ -->
<section class="jpk-section jpk-features">
    <div class="container">
        <div class="head">
            <span class="tag">The Janki Advantage</span>
            <h2>Why Patients Trust Us</h2>
            <p>World-class German laser technology with compassionate care.</p>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="jpk-feature-card">
                    <div class="ico"><i class="bi bi-shield-lock-fill"></i></div>
                    <h5>Zero Cuts &amp; Stitches</h5>
                    <p>Laser energy shrinks hemorrhoid nodes internally without scalpel cuts.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="jpk-feature-card">
                    <div class="ico"><i class="bi bi-clock-history"></i></div>
                    <h5>Same-Day Discharge</h5>
                    <p>Walk in for daycare surgery and return home comfortably by afternoon.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="jpk-feature-card">
                    <div class="ico"><i class="bi bi-heart-pulse-fill"></i></div>
                    <h5>Sphincter Preserving</h5>
                    <p>High-precision laser preserves 100% of anal sphincter control.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     TREATMENTS
     ============================================ -->
<section class="jpk-section jpk-treatments">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div class="head text-start" style="max-width:100%;margin:0;">
                <span class="tag">Our Specialties</span>
                <h2 style="font-size:1.8rem;">Advanced Laser Procedures</h2>
            </div>
            <a href="<?= site_url('/treatments') ?>" class="jpk-btn-outline" style="padding:0.3rem 1.2rem;font-size:0.75rem;">
                View All <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="row g-3">
            <?php if (empty($treatments)): ?>
                <div class="text-center py-4 text-muted col-12">No treatments available.</div>
            <?php else: ?>
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
                foreach (array_slice($treatments, 0, 6) as $tr): 
                    $iconClass = $iconMap[$tr['slug']] ?? 'bi-heart-pulse-fill';
                ?>
                    <div class="col-md-4">
                        <div class="jpk-treatment-card">
                            <div class="top">
                                <div class="ico"><i class="bi <?= $iconClass ?>"></i></div>
                                <span class="badge"><i class="bi bi-lightning-charge-fill me-1"></i> Laser</span>
                            </div>
                            <h5><?= esc($tr['title']) ?></h5>
                            <p class="desc"><?= esc($tr['content']) ?></p>
                            <div class="actions">
                                <a href="<?= site_url('/treatments/' . $tr['slug']) ?>" class="btn-sm">Learn More</a>
                                <a href="<?= site_url('/appointments/book') ?>" class="btn-sm-out">Book Slot</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ============================================
     INSURANCE BANNER
     ============================================ -->
<section class="jpk-insurance">
    <div class="container">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <span class="badge"><i class="bi bi-shield-check me-1"></i> 100% Cashless TPA</span>
                <h2>Covered Under Major Health Insurance</h2>
                <p>Empaneled with Star Health, HDFC ERGO, ICICI Lombard, Niva Bupa, Care Health &amp; all major PSU insurers.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?= site_url('/insurance') ?>" class="jpk-btn-light">
                    <i class="bi bi-shield-check me-1"></i> Check Eligibility
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     TESTIMONIALS
     ============================================ -->
<section class="jpk-section jpk-testimonials">
    <div class="container">
        <div class="head">
            <span class="tag">Patient Feedback</span>
            <h2>Real Recovery Stories</h2>
        </div>
        <div class="row g-3">
            <?php foreach ($testimonials as $t): ?>
                <div class="col-md-6">
                    <div class="jpk-testimonial-card">
                        <div class="stars">
                            <?php for($i=1; $i<=5; $i++): ?>
                                <i class="bi bi-star<?= $i <= $t['rating'] ? '-fill' : '' ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="quote">"<?= esc($t['review_text']) ?>"</p>
                        <div class="author">- <?= esc($t['author']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================
     FAQS
     ============================================ -->
<section class="jpk-faq">
    <div class="container" style="max-width:820px;">
        <div class="head">
            <span class="tag">Got Questions?</span>
            <h2>Frequently Asked Questions</h2>
        </div>
        <div class="faq-list">
            <?php foreach ($faqs as $index => $faq): ?>
                <div class="faq-item">
                    <button class="q" onclick="toggleFaq(this)">
                        <span><i class="bi bi-question-circle text-emerald me-2"></i> <?= esc($faq['q']) ?></span>
                        <span class="icon"><i class="bi bi-chevron-down"></i></span>
                    </button>
                    <div class="a <?= $index === 0 ? 'open' : '' ?>">
                        <?= esc($faq['a']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-3">
            <a href="<?= site_url('/faqs') ?>" class="jpk-btn-outline" style="padding:0.3rem 1.2rem;font-size:0.75rem;">
                Read All FAQs <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</section>

<script>
function toggleFaq(btn) {
    const answer = btn.nextElementSibling;
    const isOpen = answer.classList.contains('open');
    document.querySelectorAll('.faq-a').forEach(el => el.classList.remove('open'));
    document.querySelectorAll('.faq-q').forEach(el => el.classList.remove('active'));
    if (!isOpen) {
        answer.classList.add('open');
        btn.classList.add('active');
    }
}
document.addEventListener('DOMContentLoaded', function() {
    const first = document.querySelector('.faq-q');
    if (first) { first.classList.add('active'); }
});
</script>

<?php include VIEWS_PATH . '/layout/public_footer.php'; ?>