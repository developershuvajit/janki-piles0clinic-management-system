<?php include VIEWS_PATH . '/layout/public_header.php'; ?>

<style>
    /* ============================================
       CONTACT PAGE - CLEAN MODERN DESIGN
       ============================================ */
    
    /* ----- Page Header ----- */
    .jpk-contact-header {
        background: linear-gradient(145deg, #f8fafc, #ecfdf5);
        padding: 3.5rem 0 2.5rem;
        border-bottom: 1px solid #eef2f6;
        position: relative;
        overflow: hidden;
    }
    .jpk-contact-header::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(5,150,105,0.04), transparent 70%);
        border-radius: 50%;
    }
    .jpk-contact-header .container { position: relative; z-index: 1; }
    .jpk-contact-header .badge {
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
    .jpk-contact-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        color: #0b1a2b;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
    }
    .jpk-contact-header p {
        font-size: 1.05rem;
        color: #475569;
        max-width: 650px;
        margin: 0 auto;
        line-height: 1.7;
    }

    /* ----- Branch Tabs ----- */
    .jpk-branch-tabs .nav-pills .nav-link {
        border-radius: 40px;
        font-weight: 600;
        font-size: 0.78rem;
        padding: 0.4rem 1.2rem;
        color: #1e293b;
        transition: all 0.2s;
        border: 1px solid transparent;
    }
    .jpk-branch-tabs .nav-pills .nav-link:hover {
        background: #f1f5f9;
        border-color: #e2e8f0;
    }
    .jpk-branch-tabs .nav-pills .nav-link.active {
        background: #059669;
        color: #fff;
        border-color: #059669;
        box-shadow: 0 4px 16px rgba(5,150,105,0.25);
    }
    .jpk-branch-tabs .nav-pills .nav-link i {
        margin-right: 0.3rem;
    }

    /* ----- Branch Card ----- */
    .jpk-branch-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #eef2f6;
        overflow: hidden;
        transition: all 0.3s;
        box-shadow: 0 2px 12px rgba(0,0,0,0.03);
    }
    .jpk-branch-card:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.06);
    }
    .jpk-branch-card .info-section {
        padding: 1.8rem;
    }
    .jpk-branch-card .info-section .badge-branch {
        background: rgba(5,150,105,0.08);
        color: #059669;
        padding: 0.15rem 0.8rem;
        border-radius: 40px;
        font-size: 0.6rem;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 0.3rem;
    }
    .jpk-branch-card .info-section .badge-open {
        background: #e6f5ed;
        color: #0f7b4a;
        border: 1px solid #b8e0cf;
        padding: 0.15rem 0.6rem;
        border-radius: 40px;
        font-size: 0.55rem;
        font-weight: 600;
        display: inline-block;
        margin-left: 0.3rem;
    }
    .jpk-branch-card .info-section h3 {
        font-weight: 700;
        color: #0b1a2b;
        font-size: 1.2rem;
        margin-bottom: 1rem;
    }
    .jpk-branch-card .info-section .detail-item {
        display: flex;
        align-items: flex-start;
        gap: 0.8rem;
        margin-bottom: 0.8rem;
    }
    .jpk-branch-card .info-section .detail-item .icon-box {
        width: 36px;
        height: 36px;
        background: #e6f5ed;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #059669;
        font-size: 0.9rem;
        flex-shrink: 0;
        margin-top: 0.1rem;
    }
    .jpk-branch-card .info-section .detail-item .content .label {
        font-size: 0.65rem;
        color: #94a3b8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .jpk-branch-card .info-section .detail-item .content .value {
        font-size: 0.85rem;
        color: #0b1a2b;
        font-weight: 500;
    }
    .jpk-branch-card .info-section .detail-item .content .value a {
        color: #0b1a2b;
        text-decoration: none;
        transition: color 0.2s;
    }
    .jpk-branch-card .info-section .detail-item .content .value a:hover {
        color: #059669;
    }
    .jpk-branch-card .info-section .actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        padding-top: 1rem;
        border-top: 1px solid #eef2f6;
        margin-top: 0.5rem;
    }
    .jpk-branch-card .info-section .actions .btn-call {
        background: #059669;
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
    }
    .jpk-branch-card .info-section .actions .btn-call:hover {
        background: #047857;
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(5,150,105,0.2);
    }
    .jpk-branch-card .info-section .actions .btn-wa {
        background: #25d366;
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
    }
    .jpk-branch-card .info-section .actions .btn-wa:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(37,211,102,0.25);
    }
    .jpk-branch-card .info-section .actions .btn-book {
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
    .jpk-branch-card .info-section .actions .btn-book:hover {
        border-color: #059669;
        color: #059669;
    }
    .jpk-branch-card .map-section {
        min-height: 300px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .jpk-branch-card .map-section iframe {
        width: 100%;
        height: 100%;
        min-height: 300px;
        border: none;
    }
    .jpk-branch-card .map-section .map-placeholder {
        text-align: center;
        padding: 2rem;
        color: #94a3b8;
    }
    .jpk-branch-card .map-section .map-placeholder i {
        font-size: 3rem;
        display: block;
        margin-bottom: 0.5rem;
        color: #cbd5e1;
    }

    /* ----- Helplines ----- */
    .jpk-helplines {
        background: #f8fafc;
        border-radius: 16px;
        padding: 1.8rem;
        border: 1px solid #eef2f6;
    }
    .jpk-helplines .item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.6rem 0;
        border-bottom: 1px solid #eef2f6;
    }
    .jpk-helplines .item:last-child {
        border-bottom: none;
    }
    .jpk-helplines .item .icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .jpk-helplines .item .icon.emergency { background: #fde8e8; color: #b33c3c; }
    .jpk-helplines .item .icon.whatsapp { background: #e6f5ed; color: #0f7b4a; }
    .jpk-helplines .item .icon.email { background: #e6f0ff; color: #1a6bc4; }
    .jpk-helplines .item .info .label {
        font-size: 0.65rem;
        color: #94a3b8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .jpk-helplines .item .info .value {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0b1a2b;
        text-decoration: none;
    }
    .jpk-helplines .item .info .value:hover { color: #059669; }

    .jpk-hours-box {
        background: #059669;
        color: #fff;
        border-radius: 14px;
        padding: 1.5rem;
        margin-top: 1.5rem;
    }
    .jpk-hours-box h5 {
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }
    .jpk-hours-box p {
        margin-bottom: 0.2rem;
        opacity: 0.9;
        font-size: 0.9rem;
    }

    /* ----- Enquiry Form ----- */
    .jpk-enquiry-form {
        background: #fff;
        border-radius: 16px;
        padding: 2rem;
        border: 1px solid #eef2f6;
        box-shadow: 0 2px 12px rgba(0,0,0,0.03);
    }
    .jpk-enquiry-form h4 {
        font-weight: 700;
        color: #0b1a2b;
        font-size: 1.1rem;
        margin-bottom: 0.2rem;
    }
    .jpk-enquiry-form .sub {
        color: #94a3b8;
        font-size: 0.82rem;
        margin-bottom: 1.2rem;
    }
    .jpk-enquiry-form .form-control,
    .jpk-enquiry-form .form-select {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    .jpk-enquiry-form .form-control:focus,
    .jpk-enquiry-form .form-select:focus {
        border-color: #059669;
        box-shadow: 0 0 0 3px rgba(5,150,105,0.08);
    }
    .jpk-enquiry-form .form-label {
        font-size: 0.78rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.2rem;
    }
    .jpk-enquiry-form .btn-submit {
        background: linear-gradient(135deg, #059669, #047857);
        color: #fff;
        padding: 0.7rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.95rem;
        border: none;
        width: 100%;
        transition: all 0.25s;
    }
    .jpk-enquiry-form .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(5,150,105,0.25);
    }

    /* ----- Responsive ----- */
    @media (max-width: 992px) {
        .jpk-contact-header h1 { font-size: 2rem; }
        .jpk-branch-card .map-section { min-height: 200px; }
        .jpk-branch-card .map-section iframe { min-height: 200px; }
        .jpk-branch-tabs .nav-pills .nav-link { font-size: 0.7rem; padding: 0.3rem 0.8rem; }
    }
    @media (max-width: 576px) {
        .jpk-contact-header { padding: 2rem 0 1.5rem; }
        .jpk-contact-header h1 { font-size: 1.5rem; }
        .jpk-contact-header p { font-size: 0.9rem; }
        .jpk-branch-card .info-section { padding: 1.2rem; }
        .jpk-branch-card .info-section h3 { font-size: 1rem; }
        .jpk-branch-card .info-section .detail-item .content .value { font-size: 0.78rem; }
        .jpk-enquiry-form { padding: 1.2rem; }
        .jpk-helplines { padding: 1.2rem; }
        .jpk-branch-tabs .nav-pills { gap: 0.3rem !important; }
        .jpk-branch-tabs .nav-pills .nav-link { font-size: 0.6rem; padding: 0.2rem 0.6rem; }
    }
</style>

<!-- ============================================
     PAGE HEADER
     ============================================ -->
<section class="jpk-contact-header">
    <div class="container text-center">
        <span class="badge">24/7 HELPLINE &amp; MULTI-BRANCH LOCATIONS</span>
        <h1>Contact Janki Piles Clinic</h1>
        <p>Get in touch with our senior proctology coordinators or visit your nearest clinic branch in Dehradun, Haridwar, Roorkee, Bhaniyawala, Srinagar Garhwal, Haldwani, or Mohali.</p>
    </div>
</section>

<!-- ============================================
     BRANCH LOCATIONS
     ============================================ -->
<section class="py-4 bg-white border-bottom">
    <div class="container">
        <div class="text-center mb-4">
            <span class="text-emerald fw-bold text-uppercase" style="font-size:0.7rem;letter-spacing:0.5px;">Our Super-Specialty Network</span>
            <h2 class="fw-extrabold text-slate" style="font-size:1.8rem;">Select Clinic Branch Location</h2>
            <p class="text-muted" style="font-size:0.9rem;">Click on any branch below to view details, address, and location map.</p>
        </div>

        <?php
        // Default branch data if empty
        $branchList = !empty($branches) ? $branches : [
            ['id' => 1, 'name' => 'Dehradun Main Clinic', 'phone' => '+91 98765 43210', 'emergency_number' => '+91 98765 43210', 'email' => 'dehradun@jankipilesclinic.com', 'address' => 'Rajpur Road, Near EC Road Junction, Dehradun, Uttarakhand - 248001', 'opening_hours' => 'Mon - Sat: 09:00 AM - 08:00 PM | Sun: 10:00 AM - 02:00 PM', 'google_map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3443.6891234567!2d78.032189!3d30.316494!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390929c356789abc%3A0x1234567890abcdef!2sDehradun!5e0!3m2!1sen!2sin!4v1680000000000!5m2!1sen!2sin'],
            ['id' => 2, 'name' => 'Haridwar Clinic', 'phone' => '+91 98765 43210', 'emergency_number' => '+91 98765 43210', 'email' => 'haridwar@jankipilesclinic.com', 'address' => 'Near Ranipur More Flyover, Main Delhi-Haridwar Highway, Haridwar, Uttarakhand', 'opening_hours' => 'Mon - Sat: 09:00 AM - 08:00 PM | Sun: 10:00 AM - 02:00 PM', 'google_map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3456.123456789!2d78.132189!3d29.945678!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3909470000000000%3A0x0!2sHaridwar!5e0!3m2!1sen!2sin!4v1680000000000!5m2!1sen!2sin'],
            ['id' => 3, 'name' => 'Roorkee Clinic', 'phone' => '+91 98765 43210', 'emergency_number' => '+91 98765 43210', 'email' => 'roorkee@jankipilesclinic.com', 'address' => 'Civil Lines, Near Century Gate (IIT Roorkee), Roorkee, Uttarakhand', 'opening_hours' => 'Mon - Sat: 09:00 AM - 08:00 PM | Sun: 10:00 AM - 02:00 PM', 'google_map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3460.123456789!2d77.890123!3d29.864321!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390eb30000000000%3A0x0!2sRoorkee!5e0!3m2!1sen!2sin!4v1680000000000!5m2!1sen!2sin'],
            ['id' => 4, 'name' => 'Bhaniyawala Clinic', 'phone' => '+91 98765 43210', 'emergency_number' => '+91 98765 43210', 'email' => 'bhaniyawala@jankipilesclinic.com', 'address' => 'Jolly Grant Airport Road, Bhaniyawala Chowk, Dehradun District, Uttarakhand', 'opening_hours' => 'Mon - Sat: 09:00 AM - 08:00 PM | Sun: 10:00 AM - 02:00 PM', 'google_map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3446.123456789!2d78.180000!3d30.190000!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3909200000000000%3A0x0!2sBhaniyawala!5e0!3m2!1sen!2sin!4v1680000000000!5m2!1sen!2sin'],
            ['id' => 5, 'name' => 'Srinagar Garhwal Clinic', 'phone' => '+91 98765 43210', 'emergency_number' => '+91 98765 43210', 'email' => 'srinagar@jankipilesclinic.com', 'address' => 'Main Market, Opposite Medical College Road, Srinagar Garhwal, Uttarakhand', 'opening_hours' => 'Mon - Sat: 09:00 AM - 08:00 PM | Sun: 10:00 AM - 02:00 PM', 'google_map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3430.123456789!2d78.780000!3d30.220000!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3909d00000000000%3A0x0!2sSrinagar+Garhwal!5e0!3m2!1sen!2sin!4v1680000000000!5m2!1sen!2sin'],
            ['id' => 6, 'name' => 'Haldwani Clinic', 'phone' => '+91 98765 43210', 'emergency_number' => '+91 98765 43210', 'email' => 'haldwani@jankipilesclinic.com', 'address' => 'Bareilly-Nainital Road, Near Okal Kanda Junction, Haldwani, Uttarakhand', 'opening_hours' => 'Mon - Sat: 09:00 AM - 08:00 PM | Sun: 10:00 AM - 02:00 PM', 'google_map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3480.123456789!2d79.520000!3d29.220000!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39a0900000000000%3A0x0!2sHaldwani!5e0!3m2!1sen!2sin!4v1680000000000!5m2!1sen!2sin'],
            ['id' => 7, 'name' => 'Mohali Branch (Tricity)', 'phone' => '+91 98765 43210', 'emergency_number' => '+91 98765 43210', 'email' => 'mohali@jankipilesclinic.com', 'address' => 'Sector 62, Phase 7, Near Fortis Hospital Road, Mohali, Punjab', 'opening_hours' => 'Mon - Sat: 09:00 AM - 08:00 PM | Sun: 10:00 AM - 02:00 PM', 'google_map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3430.500000000!2d76.717000!3d30.704000!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390fed0000000000%3A0x0!2sMohali!5e0!3m2!1sen!2sin!4v1680000000000!5m2!1sen!2sin']
        ];
        ?>

        <!-- Branch Navigation -->
        <div class="jpk-branch-tabs mb-4">
            <ul class="nav nav-pills justify-content-center gap-2 flex-wrap" id="branchTabs" role="tablist">
                <?php foreach ($branchList as $index => $br): ?>
                    <?php 
                    $tabId = 'branch-tab-' . ($br['id'] ?? $index);
                    $paneId = 'branch-pane-' . ($br['id'] ?? $index);
                    $isActive = ($index === 0);
                    ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $isActive ? 'active' : '' ?>" 
                                id="<?= $tabId ?>" 
                                data-bs-toggle="tab" 
                                data-bs-target="#<?= $paneId ?>" 
                                type="button" 
                                role="tab">
                            <i class="bi bi-geo-alt-fill"></i> <?= esc($br['name']) ?>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Branch Content -->
        <div class="tab-content" id="branchTabsContent">
            <?php foreach ($branchList as $index => $br): ?>
                <?php 
                $paneId = 'branch-pane-' . ($br['id'] ?? $index);
                $isActive = ($index === 0);
                ?>
                <div class="tab-pane fade <?= $isActive ? 'show active' : '' ?>" id="<?= $paneId ?>" role="tabpanel">
                    <div class="jpk-branch-card">
                        <div class="row g-0">
                            <div class="col-lg-6">
                                <div class="info-section">
                                    <div>
                                        <span class="badge-branch"><i class="bi bi-patch-check-fill me-1"></i> Official Janki Piles Branch</span>
                                        <span class="badge-open"><i class="bi bi-check-circle-fill me-1"></i> OPD Open Today</span>
                                    </div>
                                    <h3><?= esc($br['name']) ?></h3>

                                    <div class="detail-item">
                                        <div class="icon-box"><i class="bi bi-geo-alt-fill"></i></div>
                                        <div class="content">
                                            <div class="label">Address</div>
                                            <div class="value"><?= esc($br['address']) ?></div>
                                        </div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="icon-box"><i class="bi bi-telephone-fill"></i></div>
                                        <div class="content">
                                            <div class="label">Contact &amp; Emergency</div>
                                            <div class="value">
                                                <a href="tel:<?= esc($br['phone'] ?? '+919876543210') ?>"><?= esc($br['phone'] ?? '+91 98765 43210') ?></a>
                                                <span style="color:#94a3b8;margin:0 0.3rem;">|</span>
                                                <a href="tel:<?= esc($br['emergency_number'] ?? '+919876543210') ?>" style="color:#b33c3c;font-weight:600;">Emergency: <?= esc($br['emergency_number'] ?? '+91 98765 43210') ?></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="icon-box"><i class="bi bi-envelope-fill"></i></div>
                                        <div class="content">
                                            <div class="label">Email</div>
                                            <div class="value">
                                                <a href="mailto:<?= esc($br['email'] ?? 'info@jankipilesclinic.com') ?>"><?= esc($br['email'] ?? 'info@jankipilesclinic.com') ?></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="icon-box"><i class="bi bi-clock-fill"></i></div>
                                        <div class="content">
                                            <div class="label">OPD Timings</div>
                                            <div class="value" style="font-size:0.78rem;"><?= esc($br['opening_hours'] ?? 'Mon - Sat: 09:00 AM - 08:00 PM | Sun: 10:00 AM - 02:00 PM') ?></div>
                                        </div>
                                    </div>

                                    <div class="actions">
                                        <a href="tel:<?= esc($br['phone'] ?? '+919876543210') ?>" class="btn-call"><i class="bi bi-telephone-outbound"></i> Call Branch</a>
                                        <a href="https://wa.me/919876543210" target="_blank" class="btn-wa"><i class="bi bi-whatsapp"></i> WhatsApp</a>
                                        <a href="<?= site_url('/appointments/book') ?>" class="btn-book"><i class="bi bi-calendar-check"></i> Book Slot</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="map-section">
                                    <?php if (!empty($br['google_map_link']) && str_contains($br['google_map_link'], 'http')): ?>
                                        <iframe src="<?= esc($br['google_map_link']) ?>" allowfullscreen="" loading="lazy"></iframe>
                                    <?php else: ?>
                                        <div class="map-placeholder">
                                            <i class="bi bi-map-fill"></i>
                                            <h6><?= esc($br['name']) ?></h6>
                                            <p style="font-size:0.8rem;"><?= esc($br['address']) ?></p>
                                            <a href="https://maps.google.com/?q=<?= urlencode($br['name'] . ' ' . $br['address']) ?>" target="_blank" class="btn btn-emerald btn-sm rounded-pill px-4" style="background:#059669;color:#fff;border:none;padding:0.2rem 1.2rem;font-size:0.75rem;text-decoration:none;">
                                                <i class="bi bi-box-arrow-up-right me-1"></i> Open Google Maps
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================
     HELPLINES & ENQUIRY FORM
     ============================================ -->
<section class="py-4 bg-light">
    <div class="container" style="max-width:1050px;">
        <div class="row g-4">
            <!-- Helplines -->
            <div class="col-lg-5">
                <h4 class="fw-bold text-slate mb-3" style="font-size:1.1rem;">Direct Patient Helplines</h4>
                <div class="jpk-helplines">
                    <div class="item">
                        <div class="icon emergency"><i class="bi bi-telephone-fill"></i></div>
                        <div class="info">
                            <div class="label">24/7 Emergency Line</div>
                            <a href="tel:+919876543210" class="value" style="color:#b33c3c;">+91 98765 43210</a>
                        </div>
                    </div>
                    <div class="item">
                        <div class="icon whatsapp"><i class="bi bi-whatsapp"></i></div>
                        <div class="info">
                            <div class="label">WhatsApp Consultation</div>
                            <a href="https://wa.me/919876543210" target="_blank" class="value" style="color:#0f7b4a;">+91 98765 43210</a>
                        </div>
                    </div>
                    <div class="item">
                        <div class="icon email"><i class="bi bi-envelope-fill"></i></div>
                        <div class="info">
                            <div class="label">Central Email</div>
                            <a href="mailto:info@jankipilesclinic.com" class="value" style="color:#1a6bc4;">info@jankipilesclinic.com</a>
                        </div>
                    </div>
                </div>

                <div class="jpk-hours-box">
                    <h5><i class="bi bi-clock-history me-1"></i> Clinic Working Hours</h5>
                    <p><strong>Monday - Saturday:</strong> 09:00 AM to 08:00 PM</p>
                    <p><strong>Sunday OPD:</strong> 10:00 AM to 02:00 PM</p>
                </div>
            </div>

            <!-- Enquiry Form -->
            <div class="col-lg-7">
                <div class="jpk-enquiry-form">
                    <h4>Send a Confidential Query</h4>
                    <p class="sub">Our medical coordinator will call or WhatsApp you within 15 minutes.</p>
                    
                    <form action="<?= site_url('/contact/enquiry/save') ?>" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name <span style="color:#b33c3c;">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Your name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number <span style="color:#b33c3c;">*</span></label>
                                <input type="tel" name="phone" class="form-control" placeholder="10-digit mobile" pattern="[0-9]{10}" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="Your email address">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Primary Concern</label>
                                <select name="subject" class="form-select">
                                    <option value="Piles Laser Surgery Query">Piles Laser Surgery Query</option>
                                    <option value="Anal Fissure Pain Relief">Anal Fissure Pain Relief</option>
                                    <option value="FiLaC Fistula Surgery">FiLaC Fistula Surgery</option>
                                    <option value="Pilonidal Sinus Laser">Pilonidal Sinus Laser</option>
                                    <option value="ZSR Circumcision Info">ZSR Circumcision Info</option>
                                    <option value="Hernia Surgery Package">Hernia Surgery Package</option>
                                    <option value="Cashless Insurance Approval">Cashless Insurance Approval</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Symptoms / Message</label>
                                <textarea name="message" rows="3" class="form-control" placeholder="Describe your symptoms or query in confidence..."></textarea>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn-submit">
                                    <i class="bi bi-send me-1"></i> Send Confidential Inquiry
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include VIEWS_PATH . '/layout/public_footer.php'; ?>