<?php include VIEWS_PATH . '/layout/public_header.php'; ?>

<style>
    /* ============================================
       DOCTORS PAGE - CLEAN MODERN DESIGN
       ============================================ */
    
    /* ----- Page Header ----- */
    .jpk-doctors-header {
        background: linear-gradient(145deg, #f8fafc, #ecfdf5);
        padding: 3.5rem 0 2.5rem;
        border-bottom: 1px solid #eef2f6;
        position: relative;
        overflow: hidden;
    }
    .jpk-doctors-header::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(5,150,105,0.04), transparent 70%);
        border-radius: 50%;
    }
    .jpk-doctors-header .container { position: relative; z-index: 1; }
    .jpk-doctors-header .badge {
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
    .jpk-doctors-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        color: #0b1a2b;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
    }
    .jpk-doctors-header p {
        font-size: 1.05rem;
        color: #475569;
        max-width: 650px;
        margin: 0 auto;
        line-height: 1.7;
    }

    /* ----- Doctor Profile Card ----- */
    .jpk-doctor-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #eef2f6;
        transition: all 0.3s ease;
        margin-bottom: 2rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.03);
    }
    .jpk-doctor-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.06);
        border-color: #b8e0cf;
    }
    .jpk-doctor-card .doctor-image {
        background: linear-gradient(145deg, #e6f5ed, #d1f0e3);
        min-height: 280px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }
    .jpk-doctor-card .doctor-image .avatar-icon {
        width: 120px;
        height: 120px;
        background: rgba(255,255,255,0.8);
        backdrop-filter: blur(8px);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        color: #059669;
        border: 3px solid rgba(255,255,255,0.5);
        box-shadow: 0 8px 30px rgba(5,150,105,0.08);
    }
    .jpk-doctor-card .doctor-image .experience-badge {
        position: absolute;
        bottom: 1rem;
        left: 1rem;
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(8px);
        padding: 0.3rem 1rem;
        border-radius: 40px;
        font-size: 0.65rem;
        font-weight: 600;
        color: #0b1a2b;
        border: 1px solid rgba(255,255,255,0.3);
    }
    .jpk-doctor-card .doctor-image .expertise-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(8px);
        padding: 0.3rem 0.8rem;
        border-radius: 40px;
        font-size: 0.55rem;
        font-weight: 600;
        color: #059669;
        border: 1px solid rgba(255,255,255,0.3);
    }
    .jpk-doctor-card .doctor-info {
        padding: 1.5rem 1.8rem;
    }
    .jpk-doctor-card .doctor-info .title-badge {
        background: rgba(5,150,105,0.08);
        color: #059669;
        padding: 0.1rem 0.8rem;
        border-radius: 40px;
        font-size: 0.6rem;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 0.3rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .jpk-doctor-card .doctor-info h3 {
        font-weight: 700;
        color: #0b1a2b;
        font-size: 1.2rem;
        margin-bottom: 0.2rem;
    }
    .jpk-doctor-card .doctor-info .qualifications {
        color: #64748b;
        font-size: 0.82rem;
        margin-bottom: 0.8rem;
    }
    .jpk-doctor-card .doctor-info .specialties {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        margin-bottom: 0.8rem;
    }
    .jpk-doctor-card .doctor-info .specialties span {
        background: #f1f5f9;
        color: #1e293b;
        padding: 0.15rem 0.8rem;
        border-radius: 40px;
        font-size: 0.65rem;
        font-weight: 500;
        border: 1px solid #e2e8f0;
    }
    .jpk-doctor-card .doctor-info .philosophy {
        background: #f8fafc;
        padding: 0.8rem 1rem;
        border-radius: 10px;
        border-left: 3px solid #059669;
        margin-bottom: 0.8rem;
    }
    .jpk-doctor-card .doctor-info .philosophy p {
        font-style: italic;
        color: #475569;
        font-size: 0.82rem;
        margin: 0;
        line-height: 1.6;
    }
    .jpk-doctor-card .doctor-info .actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6rem;
    }
    .jpk-doctor-card .doctor-info .actions .btn-primary {
        background: linear-gradient(135deg, #059669, #047857);
        color: #fff;
        padding: 0.4rem 1.4rem;
        border-radius: 40px;
        font-size: 0.78rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.25s;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        border: none;
    }
    .jpk-doctor-card .doctor-info .actions .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(5,150,105,0.25);
        color: #fff;
    }
    .jpk-doctor-card .doctor-info .actions .btn-outline {
        border: 1px solid #e2e8f0;
        color: #1e293b;
        padding: 0.4rem 1.4rem;
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
    .jpk-doctor-card .doctor-info .actions .btn-outline:hover {
        border-color: #059669;
        color: #059669;
    }

    /* ----- Schedule Table ----- */
    .jpk-schedule-section {
        background: #f8fafc;
        border-radius: 16px;
        padding: 2rem;
        border: 1px solid #eef2f6;
    }
    .jpk-schedule-section h4 {
        font-weight: 700;
        color: #0b1a2b;
        font-size: 1.1rem;
        margin-bottom: 1.2rem;
    }
    .jpk-schedule-section h4 i {
        color: #059669;
        margin-right: 0.4rem;
    }
    .jpk-schedule-section .table {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,0.03);
    }
    .jpk-schedule-section .table thead {
        background: #0b1a2b;
        color: #fff;
    }
    .jpk-schedule-section .table thead th {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.8rem 1rem;
        border: none;
    }
    .jpk-schedule-section .table tbody td {
        padding: 0.7rem 1rem;
        font-size: 0.82rem;
        vertical-align: middle;
        border-bottom: 1px solid #eef2f6;
    }
    .jpk-schedule-section .table tbody tr:last-child td {
        border-bottom: none;
    }
    .jpk-schedule-section .table tbody tr:hover {
        background: #f8fafc;
    }
    .jpk-schedule-section .table .branch-name {
        font-weight: 600;
        color: #0b1a2b;
    }
    .jpk-schedule-section .table .branch-name i {
        color: #059669;
        margin-right: 0.3rem;
    }
    .jpk-schedule-section .table .btn-book {
        background: #059669;
        color: #fff;
        padding: 0.15rem 1rem;
        border-radius: 40px;
        font-size: 0.7rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.25s;
        display: inline-block;
    }
    .jpk-schedule-section .table .btn-book:hover {
        background: #047857;
        transform: translateY(-1px);
        color: #fff;
    }

    /* ----- Responsive ----- */
    @media (max-width: 992px) {
        .jpk-doctors-header h1 { font-size: 2rem; }
        .jpk-doctor-card .doctor-image { min-height: 200px; }
        .jpk-doctor-card .doctor-image .avatar-icon { width: 80px; height: 80px; font-size: 2.5rem; }
        .jpk-schedule-section { padding: 1.2rem; }
    }
    @media (max-width: 576px) {
        .jpk-doctors-header { padding: 2rem 0 1.5rem; }
        .jpk-doctors-header h1 { font-size: 1.5rem; }
        .jpk-doctors-header p { font-size: 0.9rem; }
        .jpk-doctor-card .doctor-info { padding: 1rem; }
        .jpk-doctor-card .doctor-info h3 { font-size: 1rem; }
        .jpk-doctor-card .doctor-info .actions .btn-primary,
        .jpk-doctor-card .doctor-info .actions .btn-outline {
            padding: 0.3rem 1rem;
            font-size: 0.7rem;
            width: 100%;
            justify-content: center;
        }
        .jpk-schedule-section .table tbody td { font-size: 0.7rem; padding: 0.4rem 0.6rem; }
        .jpk-schedule-section .table thead th { font-size: 0.55rem; padding: 0.4rem 0.6rem; }
        .jpk-schedule-section .table .btn-book { font-size: 0.6rem; padding: 0.1rem 0.6rem; }
    }
</style>

<!-- ============================================
     PAGE HEADER
     ============================================ -->
<section class="jpk-doctors-header">
    <div class="container text-center">
        <span class="badge">EXPERIENCED SURGICAL FACULTY</span>
        <h1>Meet Our Expert Proctologists &amp; Laser Surgeons</h1>
        <p>Veteran MS General Surgeons and International Fellows in German Laser Proctology committed to compassionate, 100% confidential patient care.</p>
    </div>
</section>

<!-- ============================================
     DOCTOR PROFILES
     ============================================ -->
<section class="py-4 bg-white">
    <div class="container" style="max-width:1000px;">
        
        <?php if (!empty($doctors) && is_array($doctors)): ?>
            <?php foreach ($doctors as $doctor): ?>
                <div class="jpk-doctor-card">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <div class="doctor-image">
                                <?php if (!empty($doctor['photo'])): ?>
                                    <img src="<?= site_url($doctor['photo']) ?>" alt="<?= esc($doctor['username']) ?>" style="width:100%;height:100%;object-fit:cover;position:absolute;top:0;left:0;">
                                <?php endif; ?>
                                <div class="avatar-icon">
                                    <i class="bi bi-person-badge-fill"></i>
                                </div>
                                <?php if (!empty($doctor['experience'])): ?>
                                    <span class="experience-badge"><i class="bi bi-clock"></i> <?= esc($doctor['experience']) ?>+ Years</span>
                                <?php endif; ?>
                                <span class="expertise-badge"><i class="bi bi-star-fill"></i> Laser Specialist</span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="doctor-info">
                                <span class="title-badge">
                                    <?= !empty($doctor['title']) ? esc($doctor['title']) : 'Senior Proctologist' ?>
                                </span>
                                <h3>Dr. <?= esc($doctor['username'] ?? 'Senior Proctologist') ?></h3>
                                <div class="qualifications">
                                    <?= esc($doctor['qualifications'] ?? 'M.B.B.S., M.S. (General Surgery), FMAS, FIAGES') ?>
                                </div>
                                <div class="specialties">
                                    <?php 
                                    $specialties = !empty($doctor['specialties']) 
                                        ? explode(',', $doctor['specialties']) 
                                        : ['German Laser Piles (LHP)', 'FiLaC Fistula Repair', 'Laser Fissure Sphincterotomy', 'ZSR Stapler Circumcision', 'Laparoscopic Hernia'];
                                    foreach ($specialties as $spec): 
                                    ?>
                                        <span><?= esc(trim($spec)) ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <div class="philosophy">
                                    <p><i class="bi bi-quote text-success me-1"></i> <?= esc($doctor['philosophy'] ?? 'Anorectal disorders cause immense physical agony and mental distress. My clinical goal is to provide a non-judgmental, warm environment where patients feel fully heard, respected, and cured with zero scalpel cuts.') ?></p>
                                </div>
                                <div class="actions">
                                    <a href="<?= site_url('/appointments/book') ?>" class="btn-primary">
                                        <i class="bi bi-calendar-check"></i> Book Consultation
                                    </a>
                                    <a href="https://wa.me/919876543210" class="btn-outline" target="_blank">
                                        <i class="bi bi-whatsapp"></i> WhatsApp Query
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Default/Static Doctor Profile when no dynamic data -->
            <div class="jpk-doctor-card">
                <div class="row g-0">
                    <div class="col-md-4">
                        <div class="doctor-image">
                            <div class="avatar-icon">
                                <i class="bi bi-person-badge-fill"></i>
                            </div>
                            <span class="experience-badge"><i class="bi bi-clock"></i> 15+ Years</span>
                            <span class="expertise-badge"><i class="bi bi-star-fill"></i> Laser Specialist</span>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="doctor-info">
                            <span class="title-badge">Chief Laser Proctologist</span>
                            <h3>Dr. Senior Proctologist</h3>
                            <div class="qualifications">M.B.B.S. (Gold Medalist), M.S. (General Surgery), FMAS, FIAGES</div>
                            <div class="specialties">
                                <span>German Laser Piles (LHP)</span>
                                <span>FiLaC Fistula Repair</span>
                                <span>Laser Fissure Sphincterotomy</span>
                                <span>ZSR Stapler Circumcision</span>
                                <span>Laparoscopic Hernia</span>
                            </div>
                            <div class="philosophy">
                                <p><i class="bi bi-quote text-success me-1"></i> Anorectal disorders cause immense physical agony and mental distress mainly because patients feel embarrassed to seek help early. My clinical goal is to provide a non-judgmental, warm environment where patients feel fully heard, respected, and cured with zero scalpel cuts.</p>
                            </div>
                            <div class="actions">
                                <a href="<?= site_url('/appointments/book') ?>" class="btn-primary">
                                    <i class="bi bi-calendar-check"></i> Book Consultation
                                </a>
                                <a href="https://wa.me/919876543210" class="btn-outline" target="_blank">
                                    <i class="bi bi-whatsapp"></i> WhatsApp Query
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

       
    </div>
</section>

<?php include VIEWS_PATH . '/layout/public_footer.php'; ?>