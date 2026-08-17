<?php
$settings = \App\Models\Cms::getSettings();
$menuList = [];
if (!empty($settings['menus_json'])) {
    $menuList = json_decode($settings['menus_json'], true) ?: [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? ($settings['meta_title'] ?? 'Janki Piles Clinic - Advanced Laser Proctology Center')) ?></title>
    <meta name="description" content="<?= esc($settings['meta_description'] ?? '100% Painless Laser Piles, Fissure & Fistula Surgery at Janki Piles Clinic. Discharged same day with 100% cashless health insurance.') ?>">
    <meta name="keywords" content="Piles doctor, laser piles surgery, fissure treatment, fistula laser clinic, proctologist Dehradun, Haridwar, Haldwani, Mohali">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://www.jankipilesclinic.com<?= esc($_SERVER['REQUEST_URI'] ?? '') ?>">

    <!-- Open Graph Tags -->
    <meta property="og:title" content="<?= esc($title ?? 'Janki Piles Clinic - Advanced Laser Proctology Center') ?>">
    <meta property="og:description" content="Painless German Laser Surgery for Piles, Fissure, Fistula & Hernia. 15+ years of clinical excellence with same-day discharge.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.jankipilesclinic.com">
    <meta property="og:image" content="https://www.jankipilesclinic.com/images/hero-banner-janki-piles-clinic.jpg">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* ============================================
           GLOBAL DESIGN TOKENS + MOTION SYSTEM
           ============================================ */
        :root{
            --jpk-emerald:#059669;
            --jpk-emerald-deep:#047857;
            --jpk-navy:#0B1A2B;
            --jpk-slate:#475569;
            --jpk-bg-soft:#F8FAFC;
            --jpk-green-light:#E6F5ED;
            --jpk-border:#E2E8F0;
            --jpk-ease:cubic-bezier(0.22,1,0.36,1);
            --jpk-dur:0.75s;
        }
        html{ overflow-x:clip; scroll-behavior:smooth; }
        body{ overflow-x:clip; font-family:'Plus Jakarta Sans',sans-serif; }
        h1,h2,h3,h4,h5,h6{ font-family:'Outfit',sans-serif; }

        /* ----- Scroll progress bar ----- */
        .jpk-scroll-progress{
            position:fixed; top:0; left:0; height:3px; width:0%;
            background:linear-gradient(90deg,var(--jpk-emerald),var(--jpk-emerald-deep));
            z-index:2000; transition:width 0.1s linear;
        }

        /* ----- Cursor glow (desktop only) ----- */
        .jpk-cursor-glow{
            position:fixed; width:220px; height:220px; border-radius:50%;
            background:radial-gradient(circle, rgba(5,150,105,0.10), transparent 70%);
            pointer-events:none; z-index:1; transform:translate(-50%,-50%);
            top:0; left:0; opacity:0; transition:opacity 0.3s ease;
            will-change:transform;
        }
        @media (hover:none), (pointer:coarse){ .jpk-cursor-glow{ display:none !important; } }

        /* ----- Scroll reveal base classes ----- */
        .jpk-reveal{ opacity:0; transition:opacity var(--jpk-dur) var(--jpk-ease), transform var(--jpk-dur) var(--jpk-ease), filter var(--jpk-dur) var(--jpk-ease); will-change:transform,opacity; }
        .jpk-reveal.up{ transform:translateY(40px); }
        .jpk-reveal.down{ transform:translateY(-40px); }
        .jpk-reveal.left{ transform:translateX(-40px); }
        .jpk-reveal.right{ transform:translateX(40px); }
        .jpk-reveal.scale{ transform:scale(0.94); }
        .jpk-reveal.blur{ filter:blur(8px); transform:translateY(20px); }
        .jpk-reveal.in-view{ opacity:1; transform:translate(0,0) scale(1); filter:blur(0); }

        .jpk-stagger.in-view > *{ opacity:1; transform:translate(0,0) scale(1); }
        .jpk-stagger > *{ opacity:0; transform:translateY(30px); transition:opacity 0.6s var(--jpk-ease), transform 0.6s var(--jpk-ease); }
        .jpk-stagger > *:nth-child(1){ transition-delay:0ms; }
        .jpk-stagger > *:nth-child(2){ transition-delay:110ms; }
        .jpk-stagger > *:nth-child(3){ transition-delay:220ms; }
        .jpk-stagger > *:nth-child(4){ transition-delay:330ms; }
        .jpk-stagger > *:nth-child(5){ transition-delay:440ms; }
        .jpk-stagger > *:nth-child(6){ transition-delay:550ms; }
        .jpk-stagger > *:nth-child(7){ transition-delay:660ms; }
        .jpk-stagger > *:nth-child(8){ transition-delay:770ms; }

        /* ----- Subtle X parallax ----- */
        .jpk-parallax-x{ will-change:transform; transition:transform 0.1s linear; }

        /* ----- Button micro-interactions ----- */
        .jpk-btn-primary, .jpk-btn-outline, .jpk-btn-light, .btn-appointment, .btn-call, .btn-sm, .btn-sm-out{
            transition: transform 0.2s var(--jpk-ease), box-shadow 0.2s var(--jpk-ease), filter 0.2s ease, color 0.2s ease, border-color .2s ease, background .2s ease;
        }
        .jpk-btn-primary:active, .jpk-btn-outline:active, .jpk-btn-light:active,
        .btn-appointment:active, .btn-call:active, .btn-sm:active, .btn-sm-out:active{
            transform: scale(0.97);
        }

        @media (prefers-reduced-motion: reduce){
            .jpk-reveal, .jpk-stagger > *{ opacity:1 !important; transform:none !important; filter:none !important; transition:none !important; }
            .jpk-parallax-x{ transform:none !important; }
            .jpk-cursor-glow{ display:none !important; }
            html{ scroll-behavior:auto; }
        }

        /* ============================================
           HEADER STYLES - PREMIUM HEALTHCARE DESIGN
           ============================================ */

        /* ----- Top Bar ----- */
        .jpk-topbar {
            background: #0b1a2b;
            color: #94a3b8;
            font-size: 0.8rem;
            padding: 0.4rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .jpk-topbar a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.2s;
        }
        .jpk-topbar a:hover {
            color: #059669;
        }
        .jpk-topbar .topbar-divider {
            color: rgba(255,255,255,0.08);
            margin: 0 0.5rem;
        }
        .jpk-topbar .emergency-phone {
            color: #fff;
            font-weight: 700;
        }
        .jpk-topbar .emergency-phone:hover {
            color: #059669;
        }
        .jpk-topbar .emergency-phone i{
            display:inline-block;
            animation: jpk-phone-pulse 2.4s ease-in-out infinite;
        }
        @keyframes jpk-phone-pulse{
            0%,100%{ transform:scale(1); opacity:1; }
            50%{ transform:scale(1.15); opacity:0.75; }
        }
        .jpk-topbar .topbar-social a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            color: #94a3b8;
            transition: all 0.25s var(--jpk-ease);
            font-size: 0.75rem;
        }
        .jpk-topbar .topbar-social a:hover {
            background: rgba(5,150,105,0.2);
            color: #059669;
            transform: translateY(-2px) scale(1.08);
        }
        .jpk-topbar .topbar-text i{ transition: transform 0.3s var(--jpk-ease); }
        .jpk-topbar .topbar-text:hover i{ transform: translateY(-1px); }

        /* ----- Main Navigation ----- */
        .jpk-navbar {
            background: #fff;
            padding: 0.6rem 0;
            border-bottom: 1px solid #eef2f6;
            box-shadow: 0 2px 12px rgba(0,0,0,0.03);
            transition: padding 0.3s var(--jpk-ease), box-shadow 0.3s var(--jpk-ease), backdrop-filter 0.3s var(--jpk-ease), background 0.3s var(--jpk-ease);
        }
        .jpk-navbar.jpk-scrolled{
            padding: 0.3rem 0;
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        }
        .jpk-navbar .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
            opacity:0;
            animation: jpk-fade-in-up 0.7s var(--jpk-ease) forwards;
        }
        .jpk-navbar .brand-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #059669, #0f7b4a);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.4rem;
            flex-shrink: 0;
            transition: transform 0.3s var(--jpk-ease);
        }
        .jpk-navbar .navbar-brand:hover .brand-icon{ transform: rotate(-6deg) scale(1.05); }
        .jpk-navbar .brand-text {
            line-height: 1.1;
        }
        .jpk-navbar .brand-name {
            font-size: 1.1rem;
            font-weight: 800;
            color: #0b1a2b;
            letter-spacing: -0.5px;
        }
        .jpk-navbar .brand-sub {
            font-size: 0.55rem;
            color: #94a3b8;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            font-weight: 600;
        }
        .jpk-navbar .nav-link {
            font-weight: 600;
            font-size: 0.85rem;
            color: #1e293b !important;
            padding: 0.01rem 0.8rem !important;
            transition: all 0.2s;
            position: relative;
        }
        .jpk-navbar .navbar-nav.jpk-stagger > .nav-item{ transform:translateY(-14px); }
        .jpk-navbar .nav-link:hover,
        .jpk-navbar .nav-link.active {
            color: #059669 !important;
        }
        .jpk-navbar .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: #059669;
            transition: width 0.3s ease;
        }
        .jpk-navbar .nav-link:hover::after,
        .jpk-navbar .nav-link.active::after {
            width: 60%;
        }
        .jpk-navbar .btn-appointment {
            background: linear-gradient(135deg, #059669, #0f7b4a);
            color: #fff;
            border: none;
            padding: 0.45rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.8rem;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            box-shadow: 0 4px 16px rgba(5,150,105,0.2);
            opacity:0;
            animation: jpk-fade-in-up 0.7s var(--jpk-ease) forwards;
            animation-delay: .45s;
        }
        .jpk-navbar .btn-appointment:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(5,150,105,0.3);
            color: #fff;
        }
        .jpk-navbar .btn-appointment i {
            font-size: 0.9rem;
        }
        .jpk-navbar .btn-call {
            border: 1px solid #e2e8f0;
            color: #1e293b;
            padding: 0.45rem 1.2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.8rem;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: #fff;
        }
        .jpk-navbar .btn-call:hover {
            border-color: #059669;
            color: #059669;
        }
        .jpk-navbar .navbar-toggler {
            border: none;
            padding: 0.4rem;
        }
        .jpk-navbar .navbar-toggler:focus {
            box-shadow: none;
        }

        @keyframes jpk-fade-in-up{
            from{ opacity:0; transform:translateY(10px); }
            to{ opacity:1; transform:translateY(0); }
        }

        /* ----- Mobile Menu ----- */
        @media (max-width: 991px) {
            .jpk-navbar .navbar-collapse {
                padding-top: 0.8rem;
                border-top: 1px solid #eef2f6;
                margin-top: 0.6rem;
            }
            .jpk-navbar .nav-link {
                padding: 0.6rem 0 !important;
            }
            .jpk-navbar .nav-link::after {
                display: none;
            }
            .jpk-navbar .btn-call {
                display: none;
            }
            .jpk-topbar .topbar-text {
                font-size: 0.7rem;
            }
            .jpk-topbar .topbar-divider {
                margin: 0 0.3rem;
            }
        }

        @media (max-width: 576px) {
            .jpk-topbar {
                font-size: 0.65rem;
                padding: 0.3rem 0;
            }
            .jpk-topbar .topbar-social a {
                width: 26px;
                height: 26px;
                font-size: 0.65rem;
            }
            .jpk-navbar .brand-name {
                font-size: 0.95rem;
            }
            .jpk-navbar .brand-icon {
                width: 36px;
                height: 36px;
                font-size: 1.1rem;
            }
            .jpk-navbar .btn-appointment {
                padding: 0.35rem 1rem;
                font-size: 0.7rem;
            }
            .jpk-navbar .brand-sub {
                font-size: 0.45rem;
            }
        }
    </style>

    <!-- Master Schema Markup -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": ["MedicalBusiness", "LocalBusiness", "MedicalClinic"],
      "name": "Janki Piles Clinic",
      "image": "https://www.jankipilesclinic.com/images/logo.png",
      "@id": "https://www.jankipilesclinic.com/#organization",
      "url": "https://www.jankipilesclinic.com",
      "telephone": "+919876543210",
      "priceRange": "₹₹",
      "medicalSpecialty": ["Proctology", "Gastroenterology", "GeneralSurgery"],
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Rajpur Road",
        "addressLocality": "Dehradun",
        "addressRegion": "Uttarakhand",
        "postalCode": "248001",
        "addressCountry": "IN"
      }
    }
    </script>
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Scroll progress indicator -->
    <div class="jpk-scroll-progress" id="jpkScrollProgress"></div>
    <!-- Desktop cursor glow -->
    <div class="jpk-cursor-glow" id="jpkCursorGlow"></div>

    <!-- ============================================
         TOP EMERGENCY BAR
         ============================================ -->
    <div class="jpk-topbar">
        <div class="container">
            <div class="row align-items-center g-2">
                <div class="col-md-8">
                    <div class="d-flex flex-wrap align-items-center gap-2 gap-md-3">
                        <span class="topbar-text">
                            <i class="bi bi-geo-alt text-emerald me-1"></i> 
                            Dehradun | Haridwar | Roorkee | Haldwani | Mohali
                        </span>
                        <span class="topbar-divider">|</span>
                        <span class="topbar-text">
                            <i class="bi bi-clock text-emerald me-1"></i> 
                            Mon-Sat 9 AM - 8 PM
                        </span>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-flex flex-wrap align-items-center justify-content-md-end gap-2">
                        <a href="tel:+919876543210" class="emergency-phone">
                            <i class="bi bi-telephone-fill text-danger me-1"></i> 
                            24/7: +91 98765 43210
                        </a>
                        <span class="topbar-divider">|</span>
                        <div class="topbar-social d-flex gap-1">
                            <a href="#" target="_blank"><i class="bi bi-facebook"></i></a>
                            <a href="#" target="_blank"><i class="bi bi-instagram"></i></a>
                            <a href="#" target="_blank"><i class="bi bi-youtube"></i></a>
                            <a href="https://wa.me/919876543210" target="_blank"><i class="bi bi-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================
         MAIN NAVIGATION
         ============================================ -->
    <nav class="jpk-navbar navbar navbar-expand-lg sticky-top" id="jpkNavbar">
        <div class="container">
            <a class="navbar-brand" href="<?= site_url() ?>">
                <div class="brand-icon">
                    <i class="bi bi-hospital"></i>
                </div>
                <div class="brand-text">
                    <div class="brand-name">Janki Piles Clinic</div>
                    <div class="brand-sub">Laser Proctology Center</div>
                </div>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav mx-auto gap-1 gap-lg-2 jpk-stagger">
                    <li class="nav-item">
                        <a class="nav-link <?= ($activePage ?? '') === 'home' ? 'active' : '' ?>" href="<?= site_url('/') ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($activePage ?? '') === 'about' ? 'active' : '' ?>" href="<?= site_url('/about') ?>">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($activePage ?? '') === 'doctors' ? 'active' : '' ?>" href="<?= site_url('/doctors') ?>">Our Doctors</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($activePage ?? '') === 'treatments' ? 'active' : '' ?>" href="<?= site_url('/treatments') ?>">Treatments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($activePage ?? '') === 'faqs' ? 'active' : '' ?>" href="<?= site_url('/faqs') ?>">FAQs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($activePage ?? '') === 'blog' ? 'active' : '' ?>" href="<?= site_url('/blog') ?>">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($activePage ?? '') === 'contact' ? 'active' : '' ?>" href="<?= site_url('/contact') ?>">Contact</a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center gap-2">
                    <a href="tel:+919876543210" class="btn-call d-none d-lg-inline-flex">
                        <i class="bi bi-telephone-fill"></i> Call Now
                    </a>
                    <a href="<?= site_url('/appointments/book') ?>" class="btn-appointment">
                        <i class="bi bi-calendar-plus"></i> Book Appointment
                    </a>
                </div>
            </div>
        </div>
    </nav>