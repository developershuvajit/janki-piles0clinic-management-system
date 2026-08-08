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
        body {
            font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif;
            background-color: #f8fafc;
            color: #334155;
            overflow-x: hidden;
        }
        .bg-top-bar {
            background-color: #0f172a;
            color: #cbd5e1;
            font-size: 0.85rem;
        }
        .navbar-brand {
            font-weight: 800;
            letter-spacing: -0.5px;
            font-size: 1.4rem;
        }
        .nav-link {
            font-weight: 600;
            font-size: 0.9rem;
            color: #334155 !important;
            padding: 0.5rem 0.6rem !important;
            white-space: nowrap;
            transition: all 0.2s ease;
        }
        .nav-link:hover, .nav-link.active {
            color: #059669 !important;
        }
        .bg-gradient-hero {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 50%, #e0f2fe 100%);
        }
        .text-emerald {
            color: #059669;
        }
        .bg-emerald {
            background-color: #059669;
        }
        .btn-emerald {
            background-color: #059669;
            color: #ffffff;
            border: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-emerald:hover {
            background-color: #047857;
            color: #ffffff;
            transform: translateY(-1px);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 1rem;
        }
        .feature-icon-box {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
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

    <!-- Top Emergency & Info Bar -->
    <div class="bg-top-bar py-2 d-none d-md-block">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex gap-4">
                <span><i class="bi bi-geo-alt text-emerald me-1"></i> Multi-Branch Network: Dehradun | Haridwar | Roorkee | Haldwani | Mohali</span>
                <span><i class="bi bi-clock text-emerald me-1"></i> OPD: Mon-Sat 9 AM - 8 PM</span>
            </div>
            <div class="d-flex gap-3 align-items-center">
                <a href="tel:+919876543210" class="text-white text-decoration-none fw-bold"><i class="bi bi-telephone-fill text-danger me-1"></i> 24/7 Helpline: +91 98765 43210</a>
                <a href="https://wa.me/919876543210" class="text-success text-decoration-none fw-bold" target="_blank"><i class="bi bi-whatsapp me-1"></i> WhatsApp</a>
            </div>
        </div>
    </div>

    <!-- Sticky Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top py-2.5 shadow-sm">
        <div class="container">
            <a class="navbar-brand text-slate d-flex align-items-center" href="<?= site_url() ?>">
                <div class="bg-emerald text-white rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-hospital fs-5"></i>
                </div>
                <div>
                    <div class="lh-1 fw-extrabold text-slate" style="letter-spacing: -0.5px;">JANKI PILES CLINIC</div>
                    <div class="small text-muted fw-normal" style="font-size: 0.72rem; letter-spacing: 0.5px;">LASER PROCTOLOGY CENTER</div>
                </div>
            </a>
            
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1 gap-lg-2">
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('/') ?>">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('/about') ?>">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('/doctors') ?>">Our Doctors</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('/treatments') ?>">Treatments</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('/faqs') ?>">FAQs</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('/blog') ?>">Blogs</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('/contact') ?>">Contact</a></li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <a href="tel:+919876543210" class="btn btn-outline-danger btn-sm rounded-pill fw-bold d-none d-xl-inline-flex">
                        <i class="bi bi-telephone-outbound me-1"></i> Call Doctor
                    </a>
                    <a href="<?= site_url('/appointments/book') ?>" class="btn btn-emerald btn-sm px-3.5 rounded-pill shadow-sm">
                        <i class="bi bi-calendar-check me-1"></i> Book Appointment
                    </a>
                </div>
            </div>
        </div>
    </nav>
