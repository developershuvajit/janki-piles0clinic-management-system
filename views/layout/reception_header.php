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
    <title><?= esc($title ?? 'Reception Desk') ?> — Janki Piles Clinic</title>
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
    <style>
        .global-search-container { position: relative; width: 320px; }
        .global-search-results { 
            position: absolute; top: 100%; left: 0; right: 0; 
            background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); 
            z-index: 1050; display: none; max-height: 400px; overflow-y: auto; margin-top: 6px;
        }
        .global-search-item { padding: 10px 14px; border-bottom: 1px solid #f1f5f9; cursor: pointer; }
        .global-search-item:hover { background: #f8fafc; }
    </style>
</head>
<body>

<!-- Page Loading Overlay -->
<div id="page-loader">
    <div class="loader-ring"></div>
</div>

<div class="admin-wrapper">
    <!-- ========== RECEPTION SIDEBAR ========== -->
    <aside class="sidebar">
        <div>
            <!-- Brand & Branch Badge -->
            <a class="sidebar-brand mb-1" href="<?= site_url('/reception') ?>">
                <div class="sidebar-brand-icon" style="background: linear-gradient(135deg, #059669, #047857);">
                    <i class="bi bi-person-workspace"></i>
                </div>
                <div>
                    <span class="sidebar-brand-name">Reception<span>Desk</span></span>
                    <div class="text-emerald small fw-bold" style="font-size:0.7rem; letter-spacing:0.5px;">OPD OPERATIONS MODULE</div>
                </div>
            </a>

            <div class="p-2.5 rounded-3 bg-emerald bg-opacity-15 border border-emerald border-opacity-25 mb-3">
                <div class="small text-emerald fw-bold text-uppercase" style="font-size:0.68rem; letter-spacing:0.5px;">Assigned Branch:</div>
                <div class="fw-bold text-white small text-truncate"><i class="bi bi-geo-alt-fill me-1 text-emerald"></i> <?= esc($branchName) ?></div>
            </div>

            <hr class="sidebar-divider my-2">

            <?php
            $currentPage = $activePage ?? '';
            $isOpdActive       = in_array($currentPage, ['reception_dashboard', 'reception_queue', 'walk_in'], true);
            $isPatientActive   = in_array($currentPage, ['patients', 'patients_create', 'reception_followups'], true);
            $isIpdActive       = in_array($currentPage, ['reception_ipd', 'reception_ipd_admit', 'reception_ipd_beds'], true);
            $isBillingActive   = in_array($currentPage, ['billing', 'discharge', 'medicine_issue', 'medicines_stock'], true);
            $isDeskActive      = in_array($currentPage, ['reception_leads', 'reception_communication', 'reception_attendance'], true);
            $isAccountActive   = in_array($currentPage, ['reception_reports', 'reception_profile'], true);
            ?>

            <!-- 1. OPD Operations Topic -->
            <div class="sidebar-accordion mb-1">
                <button class="sidebar-accordion-header <?= $isOpdActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#rec-opd" aria-expanded="<?= $isOpdActive ? 'true' : 'false' ?>">
                    <span><i class="bi bi-speedometer2 me-2 text-emerald"></i> OPD Operations</span>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </button>
                <div class="collapse <?= $isOpdActive ? 'show' : '' ?>" id="rec-opd">
                    <div class="sidebar-accordion-body">
                        <a class="nav-link <?= $currentPage === 'reception_dashboard' ? 'active' : '' ?>" href="<?= site_url('/reception') ?>">
                            <i class="bi bi-grid-1x2 me-1"></i> Desk Dashboard
                        </a>
                        <a class="nav-link <?= $currentPage === 'reception_queue' ? 'active' : '' ?>" href="<?= site_url('/reception/queues') ?>">
                            <i class="bi bi-list-ol me-1"></i> Token Queue & Appointments
                        </a>
                        <a class="nav-link <?= $currentPage === 'walk_in' ? 'active' : '' ?>" href="<?= site_url('/reception/walk-in') ?>">
                            <i class="bi bi-person-walking me-1"></i> Register Walk-In
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. Patient Management & Follow-ups Topic -->
            <div class="sidebar-accordion mb-1">
                <button class="sidebar-accordion-header <?= $isPatientActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#rec-patients" aria-expanded="<?= $isPatientActive ? 'true' : 'false' ?>">
                    <span><i class="bi bi-person-lines-fill me-2 text-info"></i> Patient Management</span>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </button>
                <div class="collapse <?= $isPatientActive ? 'show' : '' ?>" id="rec-patients">
                    <div class="sidebar-accordion-body">
                        <a class="nav-link <?= $currentPage === 'patients' ? 'active' : '' ?>" href="<?= site_url('/reception/patients') ?>">
                            <i class="bi bi-folder2-open me-1"></i> Patient Directory
                        </a>
                        <a class="nav-link <?= $currentPage === 'patients_create' ? 'active' : '' ?>" href="<?= site_url('/reception/patients/create') ?>">
                            <i class="bi bi-person-plus-fill me-1"></i> Register New Patient
                        </a>
                        <a class="nav-link <?= $currentPage === 'reception_followups' ? 'active' : '' ?>" href="<?= site_url('/reception/followups') ?>">
                            <i class="bi bi-calendar2-check-fill me-1"></i> Follow-up Tracker
                        </a>
                    </div>
                </div>
            </div>

            <!-- 3. IPD & Admissions Topic -->
            <div class="sidebar-accordion mb-1">
                <button class="sidebar-accordion-header <?= $isIpdActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#rec-ipd" aria-expanded="<?= $isIpdActive ? 'true' : 'false' ?>">
                    <span><i class="bi bi-hospital-fill me-2 text-primary"></i> IPD & Ward Admissions</span>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </button>
                <div class="collapse <?= $isIpdActive ? 'show' : '' ?>" id="rec-ipd">
                    <div class="sidebar-accordion-body">
                        <a class="nav-link <?= $currentPage === 'reception_ipd' ? 'active' : '' ?>" href="<?= site_url('/reception/ipd') ?>">
                            <i class="bi bi-building-fill-add me-1"></i> Inpatient Admissions
                        </a>
                        <a class="nav-link <?= $currentPage === 'reception_ipd_admit' ? 'active' : '' ?>" href="<?= site_url('/reception/ipd/admit') ?>">
                            <i class="bi bi-journal-plus me-1"></i> Patient Bed Admission
                        </a>
                        <a class="nav-link <?= $currentPage === 'reception_ipd_beds' ? 'active' : '' ?>" href="<?= site_url('/reception/ipd/beds') ?>">
                            <i class="bi bi-diagram-3-fill me-1"></i> Bed / Room Allocation
                        </a>
                    </div>
                </div>
            </div>

            <!-- 4. Billing & Pharmacy Topic -->
            <div class="sidebar-accordion mb-1">
                <button class="sidebar-accordion-header <?= $isBillingActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#rec-billing" aria-expanded="<?= $isBillingActive ? 'true' : 'false' ?>">
                    <span><i class="bi bi-receipt me-2 text-warning"></i> Billing & Pharmacy</span>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </button>
                <div class="collapse <?= $isBillingActive ? 'show' : '' ?>" id="rec-billing">
                    <div class="sidebar-accordion-body">
                        <a class="nav-link <?= $currentPage === 'billing' ? 'active' : '' ?>" href="<?= site_url('/reception/billing') ?>">
                            <i class="bi bi-cash-register me-1"></i> Cashier Billing & Receipts
                        </a>
                        <a class="nav-link <?= $currentPage === 'discharge' ? 'active' : '' ?>" href="<?= site_url('/reception/discharge') ?>">
                            <i class="bi bi-box-arrow-right me-1"></i> Discharge Checkout
                        </a>
                        <a class="nav-link <?= $currentPage === 'medicine_issue' ? 'active' : '' ?>" href="<?= site_url('/reception/medicine-issue') ?>">
                            <i class="bi bi-capsule me-1"></i> Issue Prescribed Meds
                        </a>
                        <a class="nav-link <?= $currentPage === 'medicines_stock' ? 'active' : '' ?>" href="<?= site_url('/reception/medicines') ?>">
                            <i class="bi bi-prescription2 me-1"></i> Medicine Stock View
                        </a>
                    </div>
                </div>
            </div>

            <!-- 5. CRM Leads & Desk Topic -->
            <div class="sidebar-accordion mb-1">
                <button class="sidebar-accordion-header <?= $isDeskActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#rec-desk" aria-expanded="<?= $isDeskActive ? 'true' : 'false' ?>">
                    <span><i class="bi bi-funnel-fill me-2 text-purple"></i> CRM Leads & Desk</span>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </button>
                <div class="collapse <?= $isDeskActive ? 'show' : '' ?>" id="rec-desk">
                    <div class="sidebar-accordion-body">
                        <a class="nav-link <?= $currentPage === 'reception_leads' ? 'active' : '' ?>" href="<?= site_url('/reception/leads') ?>">
                            <i class="bi bi-funnel me-1"></i> Lead Management CRM
                        </a>
                        <a class="nav-link <?= $currentPage === 'reception_communication' ? 'active' : '' ?>" href="<?= site_url('/reception/communication') ?>">
                            <i class="bi bi-whatsapp me-1"></i> Communication Center
                        </a>
                        <a class="nav-link <?= $currentPage === 'reception_attendance' ? 'active' : '' ?>" href="<?= site_url('/reception/attendance') ?>">
                            <i class="bi bi-clock-history me-1"></i> Staff Attendance Roster
                        </a>
                    </div>
                </div>
            </div>

            <!-- 6. Reports & Account Topic -->
            <div class="sidebar-accordion mb-1">
                <button class="sidebar-accordion-header <?= $isAccountActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#rec-account" aria-expanded="<?= $isAccountActive ? 'true' : 'false' ?>">
                    <span><i class="bi bi-pie-chart-fill me-2 text-secondary"></i> Reports & Account</span>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </button>
                <div class="collapse <?= $isAccountActive ? 'show' : '' ?>" id="rec-account">
                    <div class="sidebar-accordion-body">
                        <a class="nav-link <?= $currentPage === 'reception_reports' ? 'active' : '' ?>" href="<?= site_url('/reception/reports') ?>">
                            <i class="bi bi-graph-up me-1"></i> Daily Branch Reports
                        </a>
                        <a class="nav-link <?= $currentPage === 'reception_profile' ? 'active' : '' ?>" href="<?= site_url('/reception/profile') ?>">
                            <i class="bi bi-person-gear me-1"></i> My Profile & Security
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Profile Widget -->
        <div class="sidebar-footer pt-3 border-top border-secondary border-opacity-25">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div class="bg-emerald text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:36px; height:36px; font-size:0.9rem;">
                    <?= strtoupper(substr($user['username'] ?? 'R', 0, 1)) ?>
                </div>
                <div class="overflow-hidden">
                    <div class="fw-bold text-white small text-truncate"><?= esc($user['username'] ?? 'Receptionist') ?></div>
                    <div class="text-emerald small" style="font-size:0.72rem;">Reception Staff</div>
                </div>
            </div>
            <a href="<?= site_url('/reception/logout') ?>" class="btn btn-outline-danger btn-sm w-100 rounded-pill py-1.5" style="font-size:0.8rem;">
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
                    <h4 class="fw-bold text-slate mb-0"><?= esc($title ?? 'Reception Desk') ?></h4>
                    <nav aria-label="breadcrumb" class="d-none d-sm-block">
                        <ol class="breadcrumb mb-0 small text-muted">
                            <li class="breadcrumb-item"><a href="<?= site_url('/reception') ?>">Reception Portal</a></li>
                            <li class="breadcrumb-item active"><?= esc($title ?? 'Overview') ?></li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Global Search Input Bar -->
                <div class="global-search-container d-none d-md-block">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="global-search-input" class="form-control border-start-0 ps-0" placeholder="Search Patients, Mobile, Token... (Alt+S)">
                    </div>
                    <div id="global-search-results" class="global-search-results"></div>
                </div>

                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill small fw-bold">
                    <i class="bi bi-building me-1"></i> <?= esc($branchName) ?>
                </span>
                <a href="<?= site_url('/reception/patients/create') ?>" class="btn btn-emerald btn-sm rounded-pill px-3 shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> New Patient
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
