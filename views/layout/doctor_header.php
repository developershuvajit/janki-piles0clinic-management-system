<?php
$user = \App\Helpers\Session::user();
$branchId = $user['branch_id'] ?? null;
$branchName = 'Dehradun Main Clinic'; // Default fallback
if ($branchId) {
    $b = \App\Models\Branch::find((int)$branchId);
    if ($b) {
        $branchName = $b['name'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Doctor Console') ?> — Janki Piles Clinic</title>
    <meta name="robots" content="noindex, nofollow">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Premium Stylesheet -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>

<!-- Page Loading Overlay -->
<div id="page-loader">
    <div class="loader-ring"></div>
</div>

<div class="admin-wrapper">
    <!-- ========== DOCTOR SIDEBAR ========== -->
    <aside class="sidebar">
        <div>
            <!-- Brand & Doctor Badge -->
            <a class="sidebar-brand mb-1" href="<?= site_url('/doctor') ?>">
                <div class="sidebar-brand-icon" style="background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                    <i class="bi bi-activity"></i>
                </div>
                <div>
                    <span class="sidebar-brand-name">Doctor<span>Console</span></span>
                    <div class="text-primary small fw-bold" style="font-size:0.7rem; letter-spacing:0.5px;">PHYSICIAN PORTAL</div>
                </div>
            </a>

            <div class="p-2.5 rounded-3 bg-primary bg-opacity-15 border border-primary border-opacity-25 mb-3">
                <div class="small text-primary fw-bold text-uppercase" style="font-size:0.68rem; letter-spacing:0.5px;">Attending Branch:</div>
                <div class="fw-bold text-white small text-truncate"><i class="bi bi-hospital me-1 text-primary"></i> <?= esc($branchName) ?></div>
            </div>

            <hr class="sidebar-divider my-2">

            <?php
            $currentPage = $activePage ?? '';
            $isOpdActive      = in_array($currentPage, ['doctor_dashboard', 'doctor_opd', 'doctor_patients', 'doctor_prescriptions'], true);
            $isIpdActive      = in_array($currentPage, ['doctor_ipd', 'doctor_discharge'], true);
            $isUtilActive     = in_array($currentPage, ['doctor_medicines', 'doctor_billing', 'ai_assist'], true);
            $isReportActive   = in_array($currentPage, ['doctor_reports', 'doctor_profile'], true);
            ?>

            <!-- 1. OPD & Consultations Topic -->
            <div class="sidebar-accordion mb-1">
                <button class="sidebar-accordion-header <?= $isOpdActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#doc-opd" aria-expanded="<?= $isOpdActive ? 'true' : 'false' ?>">
                    <span><i class="bi bi-stethoscope me-2 text-primary"></i> OPD & Consultations</span>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </button>
                <div class="collapse <?= $isOpdActive ? 'show' : '' ?>" id="doc-opd">
                    <div class="sidebar-accordion-body">
                        <a class="nav-link <?= $currentPage === 'doctor_dashboard' ? 'active' : '' ?>" href="<?= site_url('/doctor') ?>">
                            <i class="bi bi-speedometer2 me-1"></i> Doctor Dashboard
                        </a>
                        <a class="nav-link <?= $currentPage === 'doctor_opd' ? 'active' : '' ?>" href="<?= site_url('/doctor/opd') ?>">
                            <i class="bi bi-activity me-1"></i> OPD Patient Queue
                        </a>
                        <a class="nav-link <?= $currentPage === 'doctor_patients' ? 'active' : '' ?>" href="<?= site_url('/doctor/patients') ?>">
                            <i class="bi bi-person-bounding-box me-1"></i> Patient Search & History
                        </a>
                        <a class="nav-link <?= $currentPage === 'doctor_prescriptions' ? 'active' : '' ?>" href="<?= site_url('/doctor/prescriptions') ?>">
                            <i class="bi bi-file-earmark-medical me-1"></i> Prescriptions Directory
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. IPD & Discharges Topic -->
            <div class="sidebar-accordion mb-1">
                <button class="sidebar-accordion-header <?= $isIpdActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#doc-ipd" aria-expanded="<?= $isIpdActive ? 'true' : 'false' ?>">
                    <span><i class="bi bi-hospital-fill me-2 text-info"></i> IPD & Ward Care</span>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </button>
                <div class="collapse <?= $isIpdActive ? 'show' : '' ?>" id="doc-ipd">
                    <div class="sidebar-accordion-body">
                        <a class="nav-link <?= $currentPage === 'doctor_ipd' ? 'active' : '' ?>" href="<?= site_url('/doctor/ipd') ?>">
                            <i class="bi bi-building-fill-add me-1"></i> Admitted IPD Patients
                        </a>
                        <a class="nav-link <?= $currentPage === 'doctor_discharge' ? 'active' : '' ?>" href="<?= site_url('/doctor/discharge') ?>">
                            <i class="bi bi-check2-square me-1"></i> Approve Discharge
                        </a>
                    </div>
                </div>
            </div>

            <!-- 3. Clinical Utilities Topic -->
            <div class="sidebar-accordion mb-1">
                <button class="sidebar-accordion-header <?= $isUtilActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#doc-util" aria-expanded="<?= $isUtilActive ? 'true' : 'false' ?>">
                    <span><i class="bi bi-cpu-fill me-2 text-warning"></i> Clinical Utilities & AI</span>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </button>
                <div class="collapse <?= $isUtilActive ? 'show' : '' ?>" id="doc-util">
                    <div class="sidebar-accordion-body">
                        <a class="nav-link <?= $currentPage === 'doctor_medicines' ? 'active' : '' ?>" href="<?= site_url('/doctor/medicines') ?>">
                            <i class="bi bi-prescription2 me-1"></i> Medicine List & Stock
                        </a>
                        <a class="nav-link <?= $currentPage === 'doctor_billing' ? 'active' : '' ?>" href="<?= site_url('/doctor/billing-summary') ?>">
                            <i class="bi bi-receipt-cutoff me-1"></i> Patient Bill Summaries
                        </a>
                        <a class="nav-link <?= $currentPage === 'ai_assist' ? 'active' : '' ?>" href="<?= site_url('/doctor/ai-assist') ?>">
                            <i class="bi bi-robot me-1"></i> AI Symptom Assistant
                        </a>
                    </div>
                </div>
            </div>

            <!-- 4. Reports & Profile Topic -->
            <div class="sidebar-accordion mb-1">
                <button class="sidebar-accordion-header <?= $isReportActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#doc-reports" aria-expanded="<?= $isReportActive ? 'true' : 'false' ?>">
                    <span><i class="bi bi-person-gear me-2 text-secondary"></i> Reports & Profile</span>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </button>
                <div class="collapse <?= $isReportActive ? 'show' : '' ?>" id="doc-reports">
                    <div class="sidebar-accordion-body">
                        <a class="nav-link <?= $currentPage === 'doctor_reports' ? 'active' : '' ?>" href="<?= site_url('/doctor/reports') ?>">
                            <i class="bi bi-graph-up-arrow me-1"></i> Clinical Reports
                        </a>
                        <a class="nav-link <?= $currentPage === 'doctor_profile' ? 'active' : '' ?>" href="<?= site_url('/doctor/profile') ?>">
                            <i class="bi bi-person-circle me-1"></i> My Profile & Availability
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Profile Widget -->
        <div class="sidebar-footer pt-3 border-top border-secondary border-opacity-25">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:36px; height:36px; font-size:0.9rem;">
                    Dr
                </div>
                <div class="overflow-hidden">
                    <div class="fw-bold text-white small text-truncate">Dr. <?= esc($user['username'] ?? 'Doctor') ?></div>
                    <div class="text-primary small" style="font-size:0.72rem;">Medical Officer</div>
                </div>
            </div>
            <a href="<?= site_url('/doctor/logout') ?>" class="btn btn-outline-danger btn-sm w-100 rounded-pill py-1.5" style="font-size:0.8rem;">
                <i class="bi bi-box-arrow-right me-1"></i> Sign Out
            </a>
        </div>
    </aside>

    <!-- ========== MAIN CONTENT AREA ========== -->
    <main class="admin-content">
        <!-- Top Bar -->
        <header class="admin-header-bar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-light d-lg-none" id="sidebar-toggle">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div>
                    <h4 class="fw-bold text-slate mb-0"><?= esc($title ?? 'Doctor Console') ?></h4>
                    <nav aria-label="breadcrumb" class="d-none d-sm-block">
                        <ol class="breadcrumb mb-0 small text-muted">
                            <li class="breadcrumb-item"><a href="<?= site_url('/doctor') ?>">Doctor Portal</a></li>
                            <li class="breadcrumb-item active"><?= esc($title ?? 'Consultations') ?></li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill small fw-bold">
                    <i class="bi bi-shield-plus me-1"></i> Clinical Active Duty
                </span>
                <a href="<?= site_url('/doctor/opd') ?>" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                    <i class="bi bi-play-circle-fill me-1"></i> OPD Queue
                </a>
            </div>
        </header>

        <!-- Flash Alert Messages -->
        <?php if ($flashError = \App\Helpers\Session::getFlash('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-octagon-fill me-2"></i> <?= esc($flashError) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($flashSuccess = \App\Helpers\Session::getFlash('success')): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= esc($flashSuccess) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
