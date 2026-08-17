<?php
// ============================================
// RECEPTION SIDEBAR - FINAL VERSION
// ============================================
$user = \App\Helpers\Session::user();
$roleSlug = $user['role_slug'] ?? $user['role'] ?? '';
$branchId = $user['branch_id'] ?? null;
$branchName = 'Main Branch';

// Branch Name বের করা
if ($branchId) {
    $b = \App\Models\Branch::find((int)$branchId);
    if ($b) {
        $branchName = $b['name'];
    }
}

// যদি রিসেপশনিস্ট না হয়, তাহলে অন্য হেডার দেখান
if ($roleSlug !== 'receptionist') {
    if ($roleSlug === 'doctor') {
        include __DIR__ . '/doctor_header.php';
        return;
    }
    include __DIR__ . '/admin_header.php';
    return;
}

// Active Page এবং HR Active চেক করা (এই লাইনগুলো খুব গুরুত্বপূর্ণ)
$currentPage = $activePage ?? '';
$isHrActive = in_array($currentPage, ['reception_attendance_scan', 'reception_attendance_report', 'reception_id_cards'], true);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Reception Desk') ?> — Janki Piles Clinic</title>
    <meta name="robots" content="noindex, nofollow">

    <?php
    $sound = \App\Helpers\Session::getSoundNotification();
    if ($sound):
    ?>
        <meta name="sound-notification" content="<?= $sound['type'] ?>">
    <?php endif; ?>

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
        /* ============================================
           RECEPTION LAYOUT - FIXED & RESPONSIVE
           ============================================ */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            font-family: 'Inter', 'Plus Jakarta Sans', sans-serif;
            background: #f5f9fc;
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

        /* Sidebar */
        .sidebar-reception {
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
            border-right: 1px solid rgba(255, 255, 255, 0.06);
            flex-shrink: 0;
            transition: transform 0.3s ease;
            z-index: 1040;
        }

        .sidebar-reception .sidebar-scroll {
            flex: 1;
            overflow-y: auto;
            padding-right: 2px;
        }

        .sidebar-reception .sidebar-scroll::-webkit-scrollbar {
            width: 3px;
        }

        .sidebar-reception .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
        }

        .sidebar-reception .sidebar-footer {
            flex-shrink: 0;
            padding-top: 0.8rem;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            margin-top: 0.5rem;
        }

        /* Sidebar Brand */
        .sidebar-reception .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            text-decoration: none;
            margin-bottom: 1.2rem;
            padding: 0.2rem 0.3rem;
        }

        .sidebar-reception .sidebar-brand-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: #fff;
            flex-shrink: 0;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
        }

        .sidebar-reception .sidebar-brand-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.3px;
        }

        .sidebar-reception .sidebar-brand-name span {
            color: #3b82f6;
        }

        .sidebar-reception .sidebar-brand-sub {
            font-size: 0.55rem;
            color: #94a3b8;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: -2px;
        }

        /* Sidebar Divider */
        .sidebar-reception .sidebar-divider {
            border-color: rgba(255, 255, 255, 0.06);
            margin: 0.6rem 0 0.8rem 0;
        }

        /* Branch Badge */
        .sidebar-branch-badge {
            padding: 0.5rem 0.8rem;
            border-radius: 10px;
            background: rgba(59, 130, 246, 0.12);
            border: 1px solid rgba(59, 130, 246, 0.2);
            margin-bottom: 1rem;
        }

        .sidebar-branch-badge .label {
            font-size: 0.6rem;
            color: #60a5fa;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sidebar-branch-badge .branch {
            font-weight: 600;
            color: #fff;
            font-size: 0.78rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Accordion */
        .sidebar-reception .sidebar-accordion-header {
            width: 100%;
            background: transparent;
            border: none;
            color: #94a3b8;
            padding: 0.5rem 0.8rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
        }

        .sidebar-reception .sidebar-accordion-header:hover {
            background: rgba(255, 255, 255, 0.04);
            color: #e2e8f0;
        }

        .sidebar-reception .sidebar-accordion-header.active-header {
            background: rgba(59, 130, 246, 0.12);
            color: #60a5fa;
        }

        .sidebar-reception .sidebar-accordion-header .chevron-icon {
            font-size: 0.7rem;
            transition: transform 0.2s;
        }

        .sidebar-reception .sidebar-accordion-header[aria-expanded="true"] .chevron-icon {
            transform: rotate(180deg);
        }

        .sidebar-reception .sidebar-accordion-body {
            padding: 0.2rem 0.5rem 0.2rem 0;
            display: flex;
            flex-direction: column;
            gap: 0.05rem;
        }

        /* Nav Links */
        .sidebar-reception .nav-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.8rem;
            border-radius: 8px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.75rem;
            transition: all 0.1s;
            background: transparent;
            font-weight: 400;
        }

        .sidebar-reception .nav-link:hover {
            background: rgba(59, 130, 246, 0.08);
            color: #e2e8f0;
        }

        .sidebar-reception .nav-link.active {
            background: rgba(59, 130, 246, 0.12);
            color: #60a5fa;
            font-weight: 500;
        }

        .sidebar-reception .nav-link i {
            font-size: 0.95rem;
            width: 1.2rem;
            text-align: center;
            flex-shrink: 0;
        }

        /* Sidebar User */
        .sidebar-reception .sidebar-user {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.3rem 0.3rem 0.3rem 0;
            margin-bottom: 0.8rem;
        }

        .sidebar-reception .sidebar-user .avatar {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 0.75rem;
            flex-shrink: 0;
        }

        .sidebar-reception .sidebar-user .name {
            font-weight: 600;
            color: #fff;
            font-size: 0.8rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-reception .sidebar-user .role {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.6rem;
            color: #60a5fa;
        }

        .sidebar-reception .sidebar-user .role .dot {
            display: inline-block;
            width: 6px;
            height: 6px;
            background: #22c55e;
            border-radius: 50%;
        }

        .sidebar-reception .sidebar-user .branch-label {
            font-size: 0.55rem;
            color: #94a3b8;
            margin-top: 1px;
        }

        /* Logout Button */
        .sidebar-reception .btn-logout {
            border-radius: 10px;
            font-size: 0.75rem;
            padding: 0.4rem;
            border-color: rgba(239, 68, 68, 0.25);
            color: #ef4444;
            background: rgba(239, 68, 68, 0.05);
            transition: all 0.15s;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            width: 100%;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .sidebar-reception .btn-logout:hover {
            background: rgba(239, 68, 68, 0.12);
            border-color: #ef4444;
        }

        /* Top Bar */
        .admin-topbar-clean {
            background: #fff;
            padding: 0.7rem 1.5rem;
            border-bottom: 1px solid #eef2f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.6rem;
        }

        .admin-topbar-clean .page-title h5 {
            font-weight: 700;
            color: #0b1a2b;
            margin: 0;
            font-size: 1.05rem;
        }

        .admin-topbar-clean .page-title .breadcrumb {
            font-size: 0.7rem;
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
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        /* Buttons */
        .btn-soft-clean {
            border-radius: 40px;
            padding: 0.25rem 0.9rem;
            font-size: 0.75rem;
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
            padding: 0.25rem 1rem;
            font-size: 0.75rem;
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

        /* Badges */
        .badge-clean {
            padding: 0.2rem 0.8rem;
            border-radius: 40px;
            font-size: 0.6rem;
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

        /* User Avatar */
        .user-avatar-sm {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.65rem;
            background: #2563eb;
            color: #fff;
        }

        /* Dropdown */
        .dropdown-menu-clean {
            border-radius: 12px;
            border: none;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            padding: 0.3rem;
            min-width: 190px;
        }

        .dropdown-menu-clean .dropdown-item {
            font-size: 0.78rem;
            border-radius: 8px;
            padding: 0.35rem 0.7rem;
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

        /* Main Content */
        .admin-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .admin-content-inner {
            flex: 1;
            padding: 0 0.5rem 1.5rem 0.5rem;
        }

        /* Footer */
        .admin-footer {
            font-size: 0.75rem;
            color: #6b7a8f;
            padding: 0.6rem 1.5rem;
            background: #fff;
            border-top: 1px solid #eef2f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.4rem;
            margin-top: auto;
            flex-shrink: 0;
        }

        /* Mobile Responsive */
        .sidebar-toggle-btn {
            display: none;
            background: transparent;
            border: none;
            font-size: 1.5rem;
            color: #0b1a2b;
            padding: 0.2rem 0.5rem;
            cursor: pointer;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1039;
        }

        @media (max-width: 992px) {
            .sidebar-toggle-btn {
                display: block;
            }

            .sidebar-reception {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                transform: translateX(-100%);
                width: 280px;
                height: 100vh;
                z-index: 1041;
                transition: transform 0.3s ease;
                border-radius: 0;
            }

            .sidebar-reception.open {
                transform: translateX(0);
            }

            .sidebar-overlay.active {
                display: block;
            }

            .admin-topbar-clean {
                padding: 0.5rem 1rem;
            }

            .admin-topbar-clean .page-title h5 {
                font-size: 0.95rem;
            }

            .admin-topbar-clean .topbar-actions .d-none-mobile {
                display: none !important;
            }

            .admin-footer {
                padding: 0.5rem 1rem;
                flex-direction: column;
                text-align: center;
            }

            .admin-footer>div:last-child {
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .admin-topbar-clean .page-title h5 {
                font-size: 0.85rem;
            }

            .btn-primary-clean {
                font-size: 0.65rem;
                padding: 0.15rem 0.6rem;
            }

            .btn-soft-clean {
                font-size: 0.65rem;
                padding: 0.15rem 0.6rem;
            }

            .badge-clean {
                font-size: 0.5rem;
                padding: 0.1rem 0.5rem;
            }

            .user-avatar-sm {
                width: 24px;
                height: 24px;
                font-size: 0.55rem;
            }

            .admin-content-inner {
                padding: 0 0.3rem 1rem 0.3rem;
            }

            .admin-footer {
                font-size: 0.65rem;
                padding: 0.4rem 0.8rem;
            }
        }

        /* Page Loader */
        #page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #f5f9fc;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.3s ease;
        }

        #page-loader.hide {
            opacity: 0;
            pointer-events: none;
        }

        .loader-ring {
            width: 48px;
            height: 48px;
            border: 4px solid #e2e8f0;
            border-top-color: #2563eb;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>

    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <div class="admin-wrapper">

        <!-- ========== SIDEBAR ========== -->
        <aside class="sidebar-reception" id="sidebar">
            <div class="sidebar-scroll">
                <!-- Brand -->
                <a class="sidebar-brand" href="<?= site_url('/reception') ?>">
                    <div class="sidebar-brand-icon">
                        <i class="bi bi-person-workspace"></i>
                    </div>
                    <div>
                        <span class="sidebar-brand-name">Reception<span>Desk</span></span>
                        <div class="sidebar-brand-sub">OPD Operations Module</div>
                    </div>
                </a>

                <!-- Branch Badge -->
                <div class="sidebar-branch-badge">
                    <div class="label"><i class="bi bi-geo-alt-fill me-1"></i> Assigned Branch:</div>
                    <div class="branch"><?= esc($branchName) ?></div>
                </div>

                <hr class="sidebar-divider">

                <?php
                // Active Page & Menu Logic
                $isOpdActive = in_array($currentPage, ['reception_dashboard', 'reception_queue', 'walk_in'], true);
                $isPatientActive = in_array($currentPage, ['patients', 'patients_create', 'reception_followups'], true);
                $isIpdActive = in_array($currentPage, ['reception_ipd', 'reception_ipd_admit', 'reception_ipd_beds'], true);
                $isBillingActive = in_array($currentPage, ['billing', 'discharge', 'medicine_issue', 'medicines_stock'], true);
                $isDeskActive = in_array($currentPage, ['reception_leads', 'reception_communication', 'reception_attendance'], true);
                $isAccountActive = in_array($currentPage, ['reception_reports', 'reception_profile'], true);
                ?>

                <!-- 1. OPD Operations -->
                <div style="margin-bottom:0.2rem;">
                    <button class="sidebar-accordion-header <?= $isOpdActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#rec-opd" aria-expanded="<?= $isOpdActive ? 'true' : 'false' ?>">
                        <span><i class="bi bi-speedometer2" style="color:#22c55e;margin-right:0.5rem;"></i> OPD Operations</span>
                        <i class="bi bi-chevron-down chevron-icon"></i>
                    </button>
                    <div class="collapse <?= $isOpdActive ? 'show' : '' ?>" id="rec-opd">
                        <div class="sidebar-accordion-body">
                            <a class="nav-link <?= $currentPage === 'reception_dashboard' ? 'active' : '' ?>" href="<?= site_url('/reception') ?>">
                                <i class="bi bi-grid-1x2"></i> Desk Dashboard
                            </a>
                            <a class="nav-link <?= $currentPage === 'appointments' ? 'active' : '' ?>" href="<?= site_url('/reception/appointments') ?>">
                                <i class="bi bi-calendar-check" style="color:#8b5cf6;"></i> All Appointments
                            </a>
                            <a class="nav-link <?= $currentPage === 'appointments_schedule' ? 'active' : '' ?>" href="<?= site_url('/reception/appointments/schedule') ?>">
    <i class="bi bi-calendar-event" style="color:#10b981;"></i> Doctor Schedule
</a>
                            <a class="nav-link <?= $currentPage === 'reception_queue' ? 'active' : '' ?>" href="<?= site_url('/reception/queues') ?>">
                                <i class="bi bi-list-ol"></i> Token Queue & Appointments
                            </a>
                            <a class="nav-link <?= $currentPage === 'walk_in' ? 'active' : '' ?>" href="<?= site_url('/reception/walk-in') ?>">
                                <i class="bi bi-person-walking" style="color:#f59e0b;"></i> Register Walk-In
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 1. OPD Operations -->


                <!-- 2. Patient Management -->
                <div style="margin-bottom:0.2rem;">
                    <button class="sidebar-accordion-header <?= $isPatientActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#rec-patients" aria-expanded="<?= $isPatientActive ? 'true' : 'false' ?>">
                        <span><i class="bi bi-person-lines-fill" style="color:#06b6d4;margin-right:0.5rem;"></i> Patient Management</span>
                        <i class="bi bi-chevron-down chevron-icon"></i>
                    </button>
                    <div class="collapse <?= $isPatientActive ? 'show' : '' ?>" id="rec-patients">
                        <div class="sidebar-accordion-body">
                            <a class="nav-link <?= $currentPage === 'patients' ? 'active' : '' ?>" href="<?= site_url('/reception/patients') ?>">
                                <i class="bi bi-folder2-open"></i> Patient Directory
                            </a>
                            <a class="nav-link <?= $currentPage === 'patients_create' ? 'active' : '' ?>" href="<?= site_url('/reception/patients/create') ?>">
                                <i class="bi bi-person-plus-fill" style="color:#22c55e;"></i> Register New Patient
                            </a>
                            <a class="nav-link <?= $currentPage === 'reception_followups' ? 'active' : '' ?>" href="<?= site_url('/reception/followups') ?>">
                                <i class="bi bi-calendar2-check-fill" style="color:#8b5cf6;"></i> Follow-up Tracker
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 3. IPD & Admissions -->
                <div style="margin-bottom:0.2rem;">
                    <button class="sidebar-accordion-header <?= $isIpdActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#rec-ipd" aria-expanded="<?= $isIpdActive ? 'true' : 'false' ?>">
                        <span><i class="bi bi-hospital-fill" style="color:#3b82f6;margin-right:0.5rem;"></i> IPD & Ward Admissions</span>
                        <i class="bi bi-chevron-down chevron-icon"></i>
                    </button>
                    <div class="collapse <?= $isIpdActive ? 'show' : '' ?>" id="rec-ipd">
                        <div class="sidebar-accordion-body">
                            <a class="nav-link <?= $currentPage === 'reception_ipd' ? 'active' : '' ?>" href="<?= site_url('/reception/ipd') ?>">
                                <i class="bi bi-building-fill-add"></i> Inpatient Admissions
                            </a>

                        </div>
                    </div>
                </div>

                <!-- 4. Billing & Pharmacy -->
                <div style="margin-bottom:0.2rem;">
                    <button class="sidebar-accordion-header <?= $isBillingActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#rec-billing" aria-expanded="<?= $isBillingActive ? 'true' : 'false' ?>">
                        <span><i class="bi bi-receipt" style="color:#f59e0b;margin-right:0.5rem;"></i> Billing & Pharmacy</span>
                        <i class="bi bi-chevron-down chevron-icon"></i>
                    </button>
                    <div class="collapse <?= $isBillingActive ? 'show' : '' ?>" id="rec-billing">
                        <div class="sidebar-accordion-body">
                            <a class="nav-link <?= $currentPage === 'billing' ? 'active' : '' ?>" href="<?= site_url('/reception/billing') ?>">
                                <i class="bi bi-receipt"></i> Cashier Billing & Receipts
                            </a>
                            <a class="nav-link <?= $currentPage === 'discharge' ? 'active' : '' ?>" href="<?= site_url('/reception/discharge') ?>">
                                <i class="bi bi-box-arrow-right" style="color:#ef4444;"></i> Discharge Checkout
                            </a>
                            <a class="nav-link <?= $currentPage === 'medicine_issue' ? 'active' : '' ?>" href="<?= site_url('/reception/medicine-issue') ?>">
                                <i class="bi bi-capsule" style="color:#8b5cf6;"></i> Issue Prescribed Meds
                            </a>
                            <a class="nav-link <?= $currentPage === 'medicines_stock' ? 'active' : '' ?>" href="<?= site_url('/reception/medicines') ?>">
                                <i class="bi bi-prescription2" style="color:#22c55e;"></i> Medicine Stock View
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 🟢 HR & Operations (Reception) - FINAL FIXED VERSION -->
                <div style="margin-bottom:0.2rem;">
                    <button class="sidebar-accordion-header <?= $isHrActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#rec-hr" aria-expanded="<?= $isHrActive ? 'true' : 'false' ?>">
                        <span><i class="bi bi-people-fill" style="color:#3b82f6;margin-right:0.5rem;"></i> HR & Operations</span>
                        <i class="bi bi-chevron-down chevron-icon"></i>
                    </button>
                    <div class="collapse <?= $isHrActive ? 'show' : '' ?>" id="rec-hr">
                        <div class="sidebar-accordion-body">
                            <!-- 1. QR Attendance Scan -->
                            <a class="nav-link <?= $currentPage === 'reception_attendance_scan' ? 'active' : '' ?>" href="<?= site_url('/reception/attendance/scan') ?>">
                                <i class="bi bi-qr-code-scan" style="color:#f59e0b;"></i> QR Attendance Scan
                            </a>
                            <!-- 2. Attendance Report -->
                            <a class="nav-link <?= $currentPage === 'reception_attendance_report' ? 'active' : '' ?>" href="<?= site_url('/reception/attendance/report') ?>">
                                <i class="bi bi-graph-up-arrow" style="color:#22c55e;"></i> Attendance Report
                            </a>
                            <!-- 3. ID Card Generate -->
                            <a class="nav-link <?= $currentPage === 'reception_id_cards' ? 'active' : '' ?>" href="<?= site_url('/reception/id-cards') ?>">
                                <i class="bi bi-card-heading" style="color:#8b5cf6;"></i> ID Card Generate
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 5. CRM Leads & Desk -->
                <div style="margin-bottom:0.2rem;">
                    <button class="sidebar-accordion-header <?= $isDeskActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#rec-desk" aria-expanded="<?= $isDeskActive ? 'true' : 'false' ?>">
                        <span><i class="bi bi-funnel-fill" style="color:#a855f7;margin-right:0.5rem;"></i> CRM Leads & Desk</span>
                        <i class="bi bi-chevron-down chevron-icon"></i>
                    </button>
                    <div class="collapse <?= $isDeskActive ? 'show' : '' ?>" id="rec-desk">
                        <div class="sidebar-accordion-body">
                            <a class="nav-link <?= $currentPage === 'reception_leads' ? 'active' : '' ?>" href="<?= site_url('/reception/leads') ?>">
                                <i class="bi bi-funnel"></i> Lead Management CRM
                            </a>
                            <a class="nav-link <?= $currentPage === 'reception_communication' ? 'active' : '' ?>" href="<?= site_url('/reception/communication') ?>">
                                <i class="bi bi-whatsapp" style="color:#25D366;"></i> Communication Center
                            </a>

                        </div>
                    </div>
                </div>

                <!-- 6. Reports & Account -->
                <div style="margin-bottom:0.2rem;">
                    <button class="sidebar-accordion-header <?= $isAccountActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#rec-account" aria-expanded="<?= $isAccountActive ? 'true' : 'false' ?>">
                        <span><i class="bi bi-pie-chart-fill" style="color:#6b7a8f;margin-right:0.5rem;"></i> Reports & Account</span>
                        <i class="bi bi-chevron-down chevron-icon"></i>
                    </button>
                    <div class="collapse <?= $isAccountActive ? 'show' : '' ?>" id="rec-account">
                        <div class="sidebar-accordion-body">
                            <a class="nav-link <?= $currentPage === 'reception_reports' ? 'active' : '' ?>" href="<?= site_url('/reception/reports') ?>">
                                <i class="bi bi-graph-up" style="color:#8b5cf6;"></i> Daily Branch Reports
                            </a>
                            <a class="nav-link <?= $currentPage === 'reception_profile' ? 'active' : '' ?>" href="<?= site_url('/reception/profile') ?>">
                                <i class="bi bi-person-gear" style="color:#f59e0b;"></i> My Profile & Security
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Sidebar Footer -->
            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <div class="avatar"><?= substr(esc($user['username'] ?? 'R'), 0, 1) ?></div>
                    <div style="flex:1;overflow:hidden;">
                        <div class="name"><?= esc($user['username'] ?? 'Receptionist') ?></div>
                        <div class="role">
                            <span class="dot"></span>
                            Reception Staff
                        </div>
                        <div class="branch-label"><i class="bi bi-building"></i> <?= esc($branchName) ?></div>
                    </div>
                </div>
                <a class="btn-logout" href="<?= site_url('/logout') ?>" data-confirm="Are you sure you want to log out?">
                    <i class="bi bi-box-arrow-left"></i> Sign Out
                </a>
            </div>
        </aside>

        <!-- ========== MAIN CONTENT ========== -->
        <main class="admin-content">
            <!-- Top Bar -->
            <div class="admin-topbar-clean">
                <div class="page-title">
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        <button class="sidebar-toggle-btn" id="sidebarToggleBtn" onclick="toggleSidebar()">
                            <i class="bi bi-list"></i>
                        </button>
                        <div>
                            <h5><?= esc($title ?? 'Reception Desk') ?></h5>
                            <?php if (!empty($breadcrumb)): ?>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="<?= site_url('/reception') ?>">Home</a></li>
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
                    </div>
                </div>

                <div class="topbar-actions">
                    <span class="badge-clean badge-clean-primary d-none-mobile">
                        <i class="bi bi-shield-check"></i> RECEPTION
                    </span>
                    <span class="badge-clean badge-clean-success d-none-mobile">
                        <i class="bi bi-building"></i> <?= esc($branchName) ?>
                    </span>

                    <!-- Quick Actions Dropdown -->
                    <div class="dropdown">
                        <button class="btn-primary-clean dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-plus-circle"></i> <span class="d-none d-sm-inline">Quick Action</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-clean">
                            <li><a class="dropdown-item" href="<?= site_url('/reception/patients/create') ?>"><i class="bi bi-person-plus text-success"></i> New Patient</a></li>
                            <li><a class="dropdown-item" href="<?= site_url('/reception/walk-in') ?>"><i class="bi bi-person-walking text-primary"></i> Walk-in OPD</a></li>
                            <li><a class="dropdown-item" href="<?= site_url('/reception/ipd/admit') ?>"><i class="bi bi-hospital text-warning"></i> Admit IPD</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="<?= site_url('/reception/billing') ?>"><i class="bi bi-receipt text-info"></i> Create Bill</a></li>
                            <li><a class="dropdown-item" href="<?= site_url('/reception/medicine-issue') ?>"><i class="bi bi-capsule text-danger"></i> Issue Medicine</a></li>
                        </ul>
                    </div>

                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <button class="btn-soft-clean d-flex align-items-center gap-2 dropdown-toggle" type="button" data-bs-toggle="dropdown" style="padding:0.1rem 0.6rem 0.1rem 0.1rem;">
                            <span class="user-avatar-sm"><?= substr(esc($user['username'] ?? 'R'), 0, 1) ?></span>
                            <span class="d-none d-sm-inline" style="font-weight:500;font-size:0.75rem;"><?= esc($user['username'] ?? 'Reception') ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-clean">
                            <li><a class="dropdown-item" href="<?= site_url('/reception/profile') ?>"><i class="bi bi-person-circle"></i> My Profile</a></li>
                            <li><a class="dropdown-item" href="<?= site_url('/reception/reports') ?>"><i class="bi bi-graph-up"></i> Reports</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="<?= site_url('/logout') ?>"><i class="bi bi-box-arrow-right"></i> Sign Out</a></li>
                        </ul>
                    </div>

                    <a href="<?= site_url() ?>" class="btn-soft-clean d-none-mobile" target="_blank">
                        <i class="bi bi-globe"></i> View Site
                    </a>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if ($error = \App\Helpers\Session::getFlash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show alert-dismiss-flash mb-3" role="alert" style="border-radius:10px;border-left:4px solid #dc3545;margin:0 1.2rem;padding:0.5rem 1rem;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= esc($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" style="padding:0.3rem;"></button>
                </div>
            <?php endif; ?>
            <?php if ($success = \App\Helpers\Session::getFlash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show alert-dismiss-flash mb-3" role="alert" style="border-radius:10px;border-left:4px solid #198754;margin:0 1.2rem;padding:0.5rem 1rem;">
                    <i class="bi bi-check-circle-fill me-2"></i> <?= esc($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" style="padding:0.3rem;"></button>
                </div>
            <?php endif; ?>
            <?php if ($warning = \App\Helpers\Session::getFlash('warning')): ?>
                <div class="alert alert-warning alert-dismissible fade show alert-dismiss-flash mb-3" role="alert" style="border-radius:10px;border-left:4px solid #ffc107;margin:0 1.2rem;padding:0.5rem 1rem;">
                    <i class="bi bi-exclamation-circle-fill me-2"></i> <?= esc($warning) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" style="padding:0.3rem;"></button>
                </div>
            <?php endif; ?>

            <!-- Page Content -->
            <div class="admin-content-inner">