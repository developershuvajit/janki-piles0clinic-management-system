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

    <style>
        /* ===== HEADER STYLES - MATCHING ADMIN & RECEPTION ===== */
        .admin-topbar-clean {
            background: #fff;
            padding: 0.8rem 1.8rem;
            border-bottom: 1px solid #eef2f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.8rem;
        }
        .admin-topbar-clean .page-title h5 {
            font-weight: 700;
            color: #0b1a2b;
            margin: 0;
            font-size: 1.1rem;
        }
        .admin-topbar-clean .page-title .breadcrumb {
            font-size: 0.75rem;
            margin: 0;
            padding: 0;
            background: transparent;
        }
        .admin-topbar-clean .page-title .breadcrumb-item a {
            color: #6b7a8f;
            text-decoration: none;
        }
        .admin-topbar-clean .page-title .breadcrumb-item.active {
            color: #0b1a2b;
        }
        .admin-topbar-clean .topbar-actions {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            flex-wrap: wrap;
        }
        .btn-soft-clean {
            border-radius: 40px;
            padding: 0.3rem 1rem;
            font-size: 0.78rem;
            border: 1px solid #e2e8f0;
            background: transparent;
            color: #1e293b;
            transition: all 0.15s;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            text-decoration: none;
        }
        .btn-soft-clean:hover {
            background: #f5f7fa;
            border-color: #cbd5e1;
        }
        .btn-primary-clean {
            border-radius: 40px;
            padding: 0.3rem 1.2rem;
            font-size: 0.78rem;
            background: #2563eb;
            border: none;
            color: #fff;
            transition: all 0.15s;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            text-decoration: none;
        }
        .btn-primary-clean:hover {
            background: #1d4ed8;
            color: #fff;
        }
        .btn-success-clean {
            border-radius: 40px;
            padding: 0.3rem 1.2rem;
            font-size: 0.78rem;
            background: #0f7b4a;
            border: none;
            color: #fff;
            transition: all 0.15s;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            text-decoration: none;
        }
        .btn-success-clean:hover {
            background: #0a5d38;
            color: #fff;
        }
        .badge-clean {
            padding: 0.3rem 1rem;
            border-radius: 40px;
            font-size: 0.65rem;
            font-weight: 600;
            background: #f1f4f8;
            color: #1e293b;
            border: 1px solid #e2e8f0;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }
        .badge-clean-primary {
            background: #e6f0ff;
            color: #1a6bc4;
            border-color: #d0e2ff;
        }
        .badge-clean-success {
            background: #e6f5ed;
            color: #0b6e44;
            border-color: #b8e0cf;
        }
        .badge-clean-warning {
            background: #fef7e8;
            color: #c5711e;
            border-color: #fde8b3;
        }
        .dropdown-menu-clean {
            border-radius: 12px;
            border: none;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            padding: 0.4rem;
            min-width: 200px;
        }
        .dropdown-menu-clean .dropdown-item {
            font-size: 0.8rem;
            border-radius: 8px;
            padding: 0.4rem 0.8rem;
            color: #1e293b;
        }
        .dropdown-menu-clean .dropdown-item:hover {
            background: #f5f7fa;
        }
        .dropdown-menu-clean .dropdown-item i {
            width: 1.2rem;
            text-align: center;
            margin-right: 0.3rem;
        }
        .user-avatar-sm {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.7rem;
            background: #2563eb;
            color: #fff;
        }

        /* ===== SIDEBAR - MATCHING ADMIN STYLE ===== */
        .sidebar-doctor {
            background: #0b1a2b;
            width: 280px;
            min-height: 100vh;
            padding: 1.2rem 1rem;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow: hidden;
            border-right: 1px solid rgba(255,255,255,0.06);
            flex-shrink: 0;
        }
        .sidebar-doctor .sidebar-scroll {
            flex: 1;
            overflow-y: auto;
            padding-right: 2px;
        }
        .sidebar-doctor .sidebar-scroll::-webkit-scrollbar {
            width: 3px;
        }
        .sidebar-doctor .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-doctor .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.15);
            border-radius: 10px;
        }
        .sidebar-doctor .sidebar-footer {
            flex-shrink: 0;
            padding-top: 0.8rem;
            border-top: 1px solid rgba(255,255,255,0.06);
            margin-top: 0.5rem;
        }
        .sidebar-doctor .nav-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.78rem;
            transition: all 0.1s;
            background: transparent;
            font-weight: 400;
        }
        .sidebar-doctor .nav-link:hover {
            background: rgba(59,130,246,0.08);
            color: #e2e8f0;
        }
        .sidebar-doctor .nav-link.active {
            background: rgba(59,130,246,0.12);
            color: #60a5fa;
            font-weight: 500;
        }
        .sidebar-doctor .nav-link i {
            font-size: 1rem;
            width: 1.2rem;
            text-align: center;
        }
        .sidebar-doctor .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            text-decoration: none;
            margin-bottom: 1.2rem;
            padding: 0.2rem 0.3rem;
        }
        .sidebar-doctor .sidebar-brand-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: #fff;
            flex-shrink: 0;
        }
        .sidebar-doctor .sidebar-brand-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.3px;
        }
        .sidebar-doctor .sidebar-brand-name span {
            color: #3b82f6;
        }
        .sidebar-doctor .sidebar-brand-sub {
            font-size: 0.6rem;
            color: #94a3b8;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: -2px;
        }
        .sidebar-doctor .sidebar-divider {
            border-color: rgba(255,255,255,0.06);
            margin: 0.8rem 0 1rem 0;
        }
        .sidebar-doctor .sidebar-accordion-header {
            width: 100%;
            background: transparent;
            border: none;
            color: #94a3b8;
            padding: 0.6rem 0.8rem;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
        }
        .sidebar-doctor .sidebar-accordion-header.active-header {
            background: rgba(59,130,246,0.15);
            color: #60a5fa;
        }
        .sidebar-doctor .sidebar-accordion-header .chevron-icon {
            font-size: 0.7rem;
            transition: transform 0.2s;
        }
        .sidebar-doctor .sidebar-accordion-header[aria-expanded="true"] .chevron-icon {
            transform: rotate(180deg);
        }
        .sidebar-doctor .sidebar-accordion-body {
            padding: 0.2rem 0.5rem 0.2rem 0;
            display: flex;
            flex-direction: column;
            gap: 0.1rem;
        }

        /* ===== FIX: Footer stays at bottom ===== */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .admin-wrapper {
            display: flex;
            flex: 1;
            min-height: 100vh;
        }
        .admin-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .admin-content-inner {
            flex: 1;
            padding: 0 0.5rem 1.5rem 0.5rem;
        }
        .admin-footer {
            margin-top: auto;
            flex-shrink: 0;
        }

        @media (max-width: 768px) {
            .admin-topbar-clean {
                padding: 0.6rem 1rem;
            }
            .admin-topbar-clean .topbar-actions .d-none-mobile {
                display: none;
            }
            .sidebar-doctor {
                width: 100%;
                height: auto;
                min-height: auto;
                position: relative;
                overflow: visible;
            }
            .sidebar-doctor .sidebar-scroll {
                overflow-y: visible;
                height: auto;
            }
        }
        @media (max-width: 576px) {
            .admin-topbar-clean .page-title h5 {
                font-size: 0.95rem;
            }
            .btn-primary-clean, .btn-success-clean {
                font-size: 0.7rem;
                padding: 0.2rem 0.8rem;
            }
            .btn-soft-clean {
                font-size: 0.7rem;
                padding: 0.2rem 0.6rem;
            }
            .badge-clean {
                font-size: 0.55rem;
                padding: 0.15rem 0.6rem;
            }
        }
    </style>
</head>
<body>

<!-- Page Loading Overlay -->
<div id="page-loader">
    <div class="loader-ring"></div>
</div>

<div class="admin-wrapper">
    <!-- ========== DOCTOR SIDEBAR ========== -->
    <aside class="sidebar-doctor">
        <div class="sidebar-scroll">
            <!-- Brand -->
            <a class="sidebar-brand" href="<?= site_url('/doctor') ?>">
                <div class="sidebar-brand-icon" style="background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                    <i class="bi bi-activity"></i>
                </div>
                <div>
                    <span class="sidebar-brand-name">Doctor<span>Console</span></span>
                    <div class="sidebar-brand-sub">Physician Portal</div>
                </div>
            </a>

            <!-- Branch Badge -->
            <div style="padding:0.6rem 0.8rem;border-radius:10px;background:rgba(59,130,246,0.12);border:1px solid rgba(59,130,246,0.2);margin-bottom:1rem;">
                <div style="font-size:0.6rem;color:#60a5fa;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Attending Branch:</div>
                <div style="font-weight:600;color:#fff;font-size:0.78rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    <i class="bi bi-hospital-fill me-1" style="color:#3b82f6;"></i> <?= esc($branchName) ?>
                </div>
            </div>

            <hr class="sidebar-divider">

            <?php
            $currentPage = $activePage ?? '';
            $isOpdActive = in_array($currentPage, ['doctor_dashboard', 'doctor_opd', 'doctor_patients', 'doctor_prescriptions'], true);
            $isIpdActive = in_array($currentPage, ['doctor_ipd', 'doctor_discharge'], true);
            $isUtilActive = in_array($currentPage, ['doctor_medicines', 'doctor_billing', 'ai_assist'], true);
            $isReportActive = in_array($currentPage, ['doctor_reports', 'doctor_profile'], true);
            ?>

            <!-- 1. OPD & Consultations -->
            <div style="margin-bottom:0.3rem;">
                <button class="sidebar-accordion-header <?= $isOpdActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#doc-opd" aria-expanded="<?= $isOpdActive ? 'true' : 'false' ?>">
                    <span><i class="bi bi-stethoscope me-2" style="color:#22c55e;"></i> OPD & Consultations</span>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </button>
                <div class="collapse <?= $isOpdActive ? 'show' : '' ?>" id="doc-opd">
                    <div class="sidebar-accordion-body">
                        <a class="nav-link <?= $currentPage === 'doctor_dashboard' ? 'active' : '' ?>" href="<?= site_url('/doctor') ?>"><i class="bi bi-speedometer2"></i> Doctor Dashboard</a>
                        <a class="nav-link <?= $currentPage === 'doctor_opd' ? 'active' : '' ?>" href="<?= site_url('/doctor/opd') ?>"><i class="bi bi-activity"></i> OPD Patient Queue</a>
                        <a class="nav-link <?= $currentPage === 'doctor_patients' ? 'active' : '' ?>" href="<?= site_url('/doctor/patients') ?>"><i class="bi bi-person-bounding-box"></i> Patient Search & History</a>
                        <a class="nav-link <?= $currentPage === 'doctor_prescriptions' ? 'active' : '' ?>" href="<?= site_url('/doctor/prescriptions') ?>"><i class="bi bi-file-earmark-medical"></i> Prescriptions Directory</a>
                    </div>
                </div>
            </div>

            <!-- 2. IPD & Discharges -->
            <div style="margin-bottom:0.3rem;">
                <button class="sidebar-accordion-header <?= $isIpdActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#doc-ipd" aria-expanded="<?= $isIpdActive ? 'true' : 'false' ?>">
                    <span><i class="bi bi-hospital-fill me-2" style="color:#06b6d4;"></i> IPD & Ward Care</span>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </button>
                <div class="collapse <?= $isIpdActive ? 'show' : '' ?>" id="doc-ipd">
                    <div class="sidebar-accordion-body">
                        <a class="nav-link <?= $currentPage === 'doctor_ipd' ? 'active' : '' ?>" href="<?= site_url('/doctor/ipd') ?>"><i class="bi bi-building-fill-add"></i> Admitted IPD Patients</a>
                        <a class="nav-link <?= $currentPage === 'doctor_discharge' ? 'active' : '' ?>" href="<?= site_url('/doctor/discharge') ?>"><i class="bi bi-check2-square"></i> Approve Discharge</a>
                    </div>
                </div>
            </div>

            <!-- 3. Clinical Utilities -->
            <div style="margin-bottom:0.3rem;">
                <button class="sidebar-accordion-header <?= $isUtilActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#doc-util" aria-expanded="<?= $isUtilActive ? 'true' : 'false' ?>">
                    <span><i class="bi bi-cpu-fill me-2" style="color:#f59e0b;"></i> Clinical Utilities & AI</span>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </button>
                <div class="collapse <?= $isUtilActive ? 'show' : '' ?>" id="doc-util">
                    <div class="sidebar-accordion-body">
                        <a class="nav-link <?= $currentPage === 'doctor_medicines' ? 'active' : '' ?>" href="<?= site_url('/doctor/medicines') ?>"><i class="bi bi-prescription2"></i> Medicine List & Stock</a>
                        <a class="nav-link <?= $currentPage === 'doctor_billing' ? 'active' : '' ?>" href="<?= site_url('/doctor/billing-summary') ?>"><i class="bi bi-receipt-cutoff"></i> Patient Bill Summaries</a>
                        <a class="nav-link <?= $currentPage === 'ai_assist' ? 'active' : '' ?>" href="<?= site_url('/doctor/ai-assist') ?>"><i class="bi bi-robot"></i> AI Symptom Assistant</a>
                    </div>
                </div>
            </div>

            <!-- 4. Reports & Profile -->
            <div style="margin-bottom:0.3rem;">
                <button class="sidebar-accordion-header <?= $isReportActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#doc-reports" aria-expanded="<?= $isReportActive ? 'true' : 'false' ?>">
                    <span><i class="bi bi-person-gear me-2" style="color:#8b5cf6;"></i> Reports & Profile</span>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </button>
                <div class="collapse <?= $isReportActive ? 'show' : '' ?>" id="doc-reports">
                    <div class="sidebar-accordion-body">
                        <a class="nav-link <?= $currentPage === 'doctor_reports' ? 'active' : '' ?>" href="<?= site_url('/doctor/reports') ?>"><i class="bi bi-graph-up-arrow"></i> Clinical Reports</a>
                        <a class="nav-link <?= $currentPage === 'doctor_profile' ? 'active' : '' ?>" href="<?= site_url('/doctor/profile') ?>"><i class="bi bi-person-circle"></i> My Profile & Availability</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <div style="display:flex;align-items:center;gap:0.6rem;padding:0.3rem 0.3rem 0.3rem 0;margin-bottom:0.8rem;">
                <div style="width:38px;height:38px;background:linear-gradient(135deg,#3b82f6,#2563eb);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.75rem;flex-shrink:0;">
                    <?= substr(esc($user['username'] ?? 'D'), 0, 1) ?>
                </div>
                <div style="flex:1;overflow:hidden;">
                    <div style="font-weight:600;color:#fff;font-size:0.8rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Dr. <?= esc($user['username'] ?? 'Doctor') ?></div>
                    <div style="display:flex;align-items:center;gap:0.3rem;font-size:0.65rem;color:#60a5fa;">
                        <span style="display:inline-block;width:6px;height:6px;background:#22c55e;border-radius:50%;"></span>
                        Medical Officer
                    </div>
                </div>
            </div>
            <a class="btn btn-sm btn-outline-danger w-100" href="<?= site_url('/doctor/logout') ?>" style="border-radius:10px;font-size:0.78rem;padding:0.5rem;border-color:rgba(239,68,68,0.3);color:#ef4444;background:rgba(239,68,68,0.05);transition:all 0.15s;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:0.4rem;" 
               data-confirm="Are you sure you want to log out?">
                <i class="bi bi-box-arrow-left"></i> Sign Out
            </a>
        </div>
    </aside>

    <!-- ========== MAIN CONTENT ========== -->
    <main class="admin-content">
        <!-- Top Bar - Matching Admin Style -->
        <div class="admin-topbar-clean">
            <div class="page-title">
                <h5><?= esc($title ?? 'Doctor Console') ?></h5>
                <?php if (!empty($breadcrumb)): ?>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= site_url('/doctor') ?>">Home</a></li>
                        <?php foreach ($breadcrumb as $label => $url): ?>
                        <?php if ($url): ?>
                        <li class="breadcrumb-item"><a href="<?= esc($url) ?>"><?= esc($label) ?></a></li>
                        <?php else: ?>
                        <li class="breadcrumb-item active"><?= esc($label) ?></li>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </ol>
                </nav>
                <?php endif; ?>
            </div>

            <div class="topbar-actions">
                <!-- Role Badge -->
                <span class="badge-clean badge-clean-primary d-none-mobile">
                    <i class="bi bi-shield-check"></i> <?= esc(strtoupper($user['role'] ?? 'DOCTOR')) ?>
                </span>

                <!-- Branch Badge -->
                <span class="badge-clean badge-clean-success d-none-mobile">
                    <i class="bi bi-building"></i> <?= esc($branchName) ?>
                </span>

                <!-- Status Badge -->
                <span class="badge-clean badge-clean-warning d-none-mobile">
                    <i class="bi bi-dot" style="color:#22c55e;"></i> On Duty
                </span>

                <!-- Quick Actions Dropdown -->
                <div class="dropdown">
                    <button class="btn-success-clean dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-plus-circle"></i> Quick Action
                    </button>
                    <ul class="dropdown-menu dropdown-menu-clean">
                        <li><a class="dropdown-item" href="<?= site_url('/doctor/opd') ?>"><i class="bi bi-activity text-primary"></i> OPD Queue</a></li>
                        <li><a class="dropdown-item" href="<?= site_url('/doctor/prescriptions') ?>"><i class="bi bi-file-earmark-medical text-success"></i> New Prescription</a></li>
                        <li><a class="dropdown-item" href="<?= site_url('/doctor/ipd') ?>"><i class="bi bi-hospital text-warning"></i> IPD Rounds</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= site_url('/doctor/patients') ?>"><i class="bi bi-person-bounding-box text-info"></i> Patient History</a></li>
                        <li><a class="dropdown-item" href="<?= site_url('/doctor/ai-assist') ?>"><i class="bi bi-robot text-secondary"></i> AI Symptom Checker</a></li>
                    </ul>
                </div>

                <!-- User Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn-soft-clean d-flex align-items-center gap-2 dropdown-toggle" type="button" data-bs-toggle="dropdown" style="padding:0.15rem 0.8rem 0.15rem 0.15rem;">
                        <span class="user-avatar-sm"><?= substr(esc($user['username'] ?? 'D'), 0, 1) ?></span>
                        <span class="d-none d-sm-inline" style="font-weight:500;font-size:0.8rem;">Dr. <?= esc($user['username'] ?? 'Doctor') ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-clean">
                        <li><a class="dropdown-item" href="<?= site_url('/doctor/profile') ?>"><i class="bi bi-person-circle"></i> My Profile</a></li>
                        <li><a class="dropdown-item" href="<?= site_url('/doctor/reports') ?>"><i class="bi bi-graph-up"></i> Clinical Reports</a></li>
                        <li><a class="dropdown-item" href="<?= site_url('/doctor/schedule') ?>"><i class="bi bi-calendar3"></i> Schedule</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= site_url('/doctor/logout') ?>"><i class="bi bi-box-arrow-right"></i> Sign Out</a></li>
                    </ul>
                </div>

                <!-- View Site -->
                <a href="<?= site_url() ?>" class="btn-soft-clean d-none-mobile" target="_blank">
                    <i class="bi bi-globe"></i> View Site
                </a>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if ($error = \App\Helpers\Session::getFlash('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show alert-dismiss-flash mb-4" role="alert" style="border-radius:12px;border-left:4px solid #dc3545;margin:0 1.8rem;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= esc($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if ($success = \App\Helpers\Session::getFlash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show alert-dismiss-flash mb-4" role="alert" style="border-radius:12px;border-left:4px solid #198754;margin:0 1.8rem;">
            <i class="bi bi-check-circle-fill me-2"></i> <?= esc($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if ($warning = \App\Helpers\Session::getFlash('warning')): ?>
        <div class="alert alert-warning alert-dismissible fade show alert-dismiss-flash mb-4" role="alert" style="border-radius:12px;border-left:4px solid #ffc107;margin:0 1.8rem;">
            <i class="bi bi-exclamation-circle-fill me-2"></i> <?= esc($warning) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Page Content starts below -->
        <div class="admin-content-inner" style="padding: 0 0.5rem 1.5rem 0.5rem;">