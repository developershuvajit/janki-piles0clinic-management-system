<?php
$user = \App\Helpers\Session::user();
$roleSlug = $user['role_slug'] ?? '';
$userId = (int)($user['id'] ?? 0);

if (empty($roleSlug) && $userId > 0) {
    $r = \App\Helpers\Database::row(
        "SELECT r.slug FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.id = :id",
        ['id' => $userId]
    );
    $roleSlug = $r['slug'] ?? '';
}

// If receptionist, use reception header
if ($roleSlug === 'receptionist') {
    include __DIR__ . '/reception_header.php';
    return;
}

// If doctor, use doctor header
if ($roleSlug === 'doctor') {
    include __DIR__ . '/doctor_header.php';
    return;
}

// SUPER ADMIN or BRANCH ADMIN - Same menu, different data filter
$isSuperAdmin = ($roleSlug === 'super_admin' || $roleSlug === 'admin');
$isBranchAdmin = ($roleSlug === 'branch_admin');
$isAdmin = ($isSuperAdmin || $isBranchAdmin);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Admin Panel') ?> — Janki Piles</title>
    <meta name="robots" content="noindex, nofollow">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">

    <style>
        /* ===== TOP BAR STYLES ===== */
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
        .badge-clean-danger {
            background: #fee2e2;
            color: #991b1b;
            border-color: #fecaca;
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

        /* ===== SIDEBAR STYLES ===== */
        .sidebar-super {
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
        }
        .sidebar-super .sidebar-scroll {
            flex: 1;
            overflow-y: auto;
            padding-right: 2px;
        }
        .sidebar-super .sidebar-scroll::-webkit-scrollbar {
            width: 3px;
        }
        .sidebar-super .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-super .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
        }
        .sidebar-super .sidebar-footer {
            flex-shrink: 0;
            padding-top: 0.8rem;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            margin-top: 0.5rem;
        }
        .sidebar-super .nav-link {
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
        .sidebar-super .nav-link:hover {
            background: rgba(59, 130, 246, 0.08);
            color: #e2e8f0;
        }
        .sidebar-super .nav-link.active {
            background: rgba(59, 130, 246, 0.12);
            color: #60a5fa;
            font-weight: 500;
        }
        .sidebar-super .nav-link i {
            font-size: 1rem;
            width: 1.2rem;
            text-align: center;
        }
        .sidebar-super .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            text-decoration: none;
            margin-bottom: 1.2rem;
            padding: 0.2rem 0.3rem;
        }
        .sidebar-super .sidebar-brand-icon {
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
        .sidebar-super .sidebar-brand-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.3px;
        }
        .sidebar-super .sidebar-brand-name span {
            color: #3b82f6;
        }
        .sidebar-super .sidebar-brand-sub {
            font-size: 0.6rem;
            color: #94a3b8;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: -2px;
        }
        .sidebar-super .sidebar-divider {
            border-color: rgba(255, 255, 255, 0.06);
            margin: 0.8rem 0 1rem 0;
        }
        .sidebar-super .sidebar-accordion-header {
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
        .sidebar-super .sidebar-accordion-header.active-header {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
        }
        .sidebar-super .sidebar-accordion-header .chevron-icon {
            font-size: 0.7rem;
            transition: transform 0.2s;
        }
        .sidebar-super .sidebar-accordion-header[aria-expanded="true"] .chevron-icon {
            transform: rotate(180deg);
        }
        .sidebar-super .sidebar-accordion-body {
            padding: 0.2rem 0.5rem 0.2rem 0;
            display: flex;
            flex-direction: column;
            gap: 0.1rem;
        }
        .sidebar-role-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 8px;
            background: rgba(59, 130, 246, 0.15);
            border: 1px solid rgba(59, 130, 246, 0.2);
            margin-bottom: 1rem;
            font-size: 0.6rem;
            color: #60a5fa;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .sidebar-role-badge i {
            font-size: 0.8rem;
        }

        /* ===== LAYOUT FIXES ===== */
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

        @media (max-width: 768px) {
            .admin-topbar-clean {
                padding: 0.6rem 1rem;
            }
            .admin-topbar-clean .topbar-actions .d-none-mobile {
                display: none;
            }
            .sidebar-super {
                width: 100%;
                height: auto;
                min-height: auto;
                position: relative;
                overflow: visible;
            }
            .sidebar-super .sidebar-scroll {
                overflow-y: visible;
                height: auto;
            }
        }
        @media (max-width: 576px) {
            .admin-topbar-clean .page-title h5 {
                font-size: 0.95rem;
            }
            .btn-primary-clean {
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

    <div id="page-loader">
        <div class="loader-ring"></div>
    </div>

    <div class="admin-wrapper">

        <!-- ========== SIDEBAR ========== -->
        <aside class="sidebar-super">
            <div class="sidebar-scroll">
                <!-- Brand -->
                <a class="sidebar-brand" href="<?= site_url('/admin/dashboard') ?>">
                    <div class="sidebar-brand-icon" style="background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                        <i class="bi bi-hospital"></i>
                    </div>
                    <div>
                        <span class="sidebar-brand-name">Janki<span>Piles</span></span>
                        <div class="sidebar-brand-sub"><?= $isBranchAdmin ? 'Branch Admin Console' : 'Super Admin Console' ?></div>
                    </div>
                </a>

                <!-- Role Badge -->
                <div class="sidebar-role-badge">
                    <i class="bi bi-shield-fill-check"></i>
                    <?= $isBranchAdmin ? 'BRANCH ADMIN' : 'SUPER ADMIN' ?>
                    <?php if ($isBranchAdmin && isset($_SESSION['branch_name'])): ?>
                        <span style="color:#94a3b8;font-weight:400;text-transform:none;font-size:0.7rem;">
                            | <?= esc($_SESSION['branch_name']) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <hr class="sidebar-divider">

                <?php
                $currentPage = $activePage ?? '';
                $branchId = $_SESSION['branch_id'] ?? 0;
                
                // Section active states
                $isAdminActive = in_array($currentPage, ['dashboard', 'patients', 'appointments', 'appointments_pending', 'ipd', 'billing', 'inventory'], true);
                $isHrActive = in_array($currentPage, ['branches', 'employees', 'id_cards', 'attendance', 'attendance_scan', 'attendance_report', 'leaves', 'salary', 'users', 'roles'], true);
                $isCmsActive = str_starts_with($currentPage, 'cms_');
                $isSystemActive = in_array($currentPage, ['reports', 'settings', 'logs'], true);
                ?>

                <!-- ======================================== -->
                <!-- 1. DASHBOARD & CLINICAL (Both Roles - Data filtered by branch) -->
                <!-- ======================================== -->
                <div style="margin-bottom:0.3rem;">
                    <button class="sidebar-accordion-header <?= $isAdminActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#menu-admin" aria-expanded="<?= $isAdminActive ? 'true' : 'false' ?>">
                        <span><i class="bi bi-speedometer2" style="color:#22c55e;margin-right:0.6rem;"></i> Dashboard & Clinical</span>
                        <i class="bi bi-chevron-down chevron-icon"></i>
                    </button>
                    <div class="collapse <?= $isAdminActive ? 'show' : '' ?>" id="menu-admin">
                        <div class="sidebar-accordion-body">
                            <a class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="<?= site_url('/admin/dashboard') ?>">
                                <i class="bi bi-grid-1x2"></i> Dashboard
                            </a>
                            <a class="nav-link <?= $currentPage === 'patients' ? 'active' : '' ?>" href="<?= site_url('/admin/patients') ?>">
                                <i class="bi bi-person-lines-fill" style="color:#22c55e;"></i> Patients
                            </a>
                            <a class="nav-link <?= $currentPage === 'appointments' ? 'active' : '' ?>" href="<?= site_url('/admin/appointments') ?>">
                                <i class="bi bi-calendar-check" style="color:#8b5cf6;"></i> Appointments
                            </a>
                            <a class="nav-link <?= $currentPage === 'appointments_pending' ? 'active' : '' ?>" href="<?= site_url('/admin/appointments/pending') ?>">
                                <i class="bi bi-clock-history" style="color:#f59e0b;"></i> Pending Approvals
                            </a>
                            <a class="nav-link <?= $currentPage === 'ipd' ? 'active' : '' ?>" href="<?= site_url('/admin/ipd') ?>">
                                <i class="bi bi-hospital" style="color:#ec4899;"></i> IPD Admissions
                            </a>
                            <a class="nav-link <?= $currentPage === 'billing' ? 'active' : '' ?>" href="<?= site_url('/admin/billing') ?>">
                                <i class="bi bi-receipt-cutoff" style="color:#8b5cf6;"></i> Billing
                            </a>
                            <a class="nav-link <?= $currentPage === 'inventory' ? 'active' : '' ?>" href="<?= site_url('/admin/inventory') ?>">
                                <i class="bi bi-capsule" style="color:#ef4444;"></i> Inventory
                            </a>
                        </div>
                    </div>
                </div>

                <!-- ======================================== -->
                <!-- 2. HR & OPERATIONS (Both Roles - Data filtered by branch) -->
                <!-- ======================================== -->
                <div style="margin-bottom:0.3rem;">
                    <button class="sidebar-accordion-header <?= $isHrActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#menu-hr" aria-expanded="<?= $isHrActive ? 'true' : 'false' ?>">
                        <span><i class="bi bi-people-fill" style="color:#3b82f6;margin-right:0.6rem;"></i> HR & Operations</span>
                        <i class="bi bi-chevron-down chevron-icon"></i>
                    </button>
                    <div class="collapse <?= $isHrActive ? 'show' : '' ?>" id="menu-hr">
                        <div class="sidebar-accordion-body">
                            <!-- Branches - শুধু Super Admin -->
                            <?php if ($isSuperAdmin): ?>
                                <a class="nav-link <?= $currentPage === 'branches' ? 'active' : '' ?>" href="<?= site_url('/admin/branches') ?>">
                                    <i class="bi bi-building" style="color:#22c55e;"></i> Branches
                                </a>
                                <a class="nav-link <?= $currentPage === 'roles' ? 'active' : '' ?>" href="<?= site_url('/admin/roles') ?>">
                                    <i class="bi bi-shield-lock" style="color:#a855f7;"></i> Roles & Permissions
                                </a>
                               
                            <?php endif; ?>
                            
                            <!-- Employees - Both Roles (Branch Admin sees only his branch employees) -->
                            <a class="nav-link <?= $currentPage === 'employees' ? 'active' : '' ?>" href="<?= site_url('/admin/employees') ?>">
                                <i class="bi bi-person-lines-fill" style="color:#3b82f6;"></i> Employees
                            </a>
                            
                            <!-- ID Cards - Both Roles -->
                            <a class="nav-link <?= $currentPage === 'id_cards' ? 'active' : '' ?>" href="<?= site_url('/admin/employees/id-cards') ?>">
                                <i class="bi bi-credit-card-2-front" style="color:#8b5cf6;"></i> ID Cards
                            </a>
                            
                            <!-- Attendance - Both Roles -->
                            <a class="nav-link <?= $currentPage === 'attendance_scan' ? 'active' : '' ?>" href="<?= site_url('/admin/attendance/scan') ?>">
                                <i class="bi bi-qr-code-scan" style="color:#f59e0b;"></i> QR Attendance
                            </a>
                            <a class="nav-link <?= $currentPage === 'attendance_report' ? 'active' : '' ?>" href="<?= site_url('/admin/attendance/report') ?>">
                                <i class="bi bi-graph-up-arrow" style="color:#22c55e;"></i> Attendance Report
                            </a>
                            <a class="nav-link <?= $currentPage === 'leaves' ? 'active' : '' ?>" href="<?= site_url('/admin/employees/attendance/leaves') ?>">
                                <i class="bi bi-calendar-minus" style="color:#ec4899;"></i> Leaves
                            </a>
                            
                            <!-- Salary - Both Roles (Branch Admin sees only his branch employees) -->
                            <a class="nav-link <?= $currentPage === 'salary' ? 'active' : '' ?>" href="<?= site_url('/admin/salary') ?>">
                                <i class="bi bi-cash-stack" style="color:#0f7b4a;"></i> Salary
                            </a>
                        </div>
                    </div>
                </div>

                <!-- ======================================== -->
                <!-- 3. WEBSITE CMS & CRM (Only Super Admin) -->
                <!-- ======================================== -->
                <?php if ($isSuperAdmin): ?>
                <div style="margin-bottom:0.3rem;">
                    <button class="sidebar-accordion-header <?= $isCmsActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#menu-cms" aria-expanded="<?= $isCmsActive ? 'true' : 'false' ?>">
                        <span><i class="bi bi-window-sidebar" style="color:#a855f7;margin-right:0.6rem;"></i> Website CMS & CRM</span>
                        <i class="bi bi-chevron-down chevron-icon"></i>
                    </button>
                    <div class="collapse <?= $isCmsActive ? 'show' : '' ?>" id="menu-cms">
                        <div class="sidebar-accordion-body">
                            <a class="nav-link <?= $currentPage === 'cms_settings' ? 'active' : '' ?>" href="<?= site_url('/admin/cms/settings') ?>">
                                <i class="bi bi-sliders" style="color:#a855f7;"></i> Layout Settings
                            </a>
                            <a class="nav-link <?= $currentPage === 'cms_blogs' ? 'active' : '' ?>" href="<?= site_url('/admin/cms/blogs') ?>">
                                <i class="bi bi-newspaper" style="color:#06b6d4;"></i> Health Blogs
                            </a>
                            <a class="nav-link <?= $currentPage === 'cms_comments' ? 'active' : '' ?>" href="<?= site_url('/admin/cms/comments') ?>">
                                <i class="bi bi-chat-left-text" style="color:#f59e0b;"></i> Comments
                            </a>
                            <a class="nav-link <?= $currentPage === 'cms_treatments' ? 'active' : '' ?>" href="<?= site_url('/admin/cms/treatments') ?>">
                                <i class="bi bi-heart-pulse" style="color:#ef4444;"></i> Treatments
                            </a>
                            <a class="nav-link <?= $currentPage === 'cms_gallery' ? 'active' : '' ?>" href="<?= site_url('/admin/cms/gallery') ?>">
                                <i class="bi bi-images" style="color:#8b5cf6;"></i> Gallery
                            </a>
                            <a class="nav-link <?= $currentPage === 'cms_testimonials' ? 'active' : '' ?>" href="<?= site_url('/admin/cms/testimonials') ?>">
                                <i class="bi bi-star-fill" style="color:#f59e0b;"></i> Reviews
                            </a>
                            <a class="nav-link <?= $currentPage === 'cms_enquiries' ? 'active' : '' ?>" href="<?= site_url('/admin/cms/enquiries') ?>">
                                <i class="bi bi-envelope-paper" style="color:#22c55e;"></i> CRM Leads
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- ======================================== -->
                <!-- 4. SYSTEM & ANALYTICS (Only Super Admin) -->
                <!-- ======================================== -->
                <?php if ($isSuperAdmin): ?>
                <div style="margin-bottom:0.3rem;">
                    <button class="sidebar-accordion-header <?= $isSystemActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#menu-system" aria-expanded="<?= $isSystemActive ? 'true' : 'false' ?>">
                        <span><i class="bi bi-gear-wide-connected" style="color:#ef4444;margin-right:0.6rem;"></i> System & Analytics</span>
                        <i class="bi bi-chevron-down chevron-icon"></i>
                    </button>
                    <div class="collapse <?= $isSystemActive ? 'show' : '' ?>" id="menu-system">
                        <div class="sidebar-accordion-body">
                            <a class="nav-link <?= $currentPage === 'reports' ? 'active' : '' ?>" href="<?= site_url('/admin/reports') ?>">
                                <i class="bi bi-graph-up-arrow" style="color:#8b5cf6;"></i> Reports
                            </a>
                            <a class="nav-link <?= $currentPage === 'settings' ? 'active' : '' ?>" href="<?= site_url('/admin/settings') ?>">
                                <i class="bi bi-gear" style="color:#f59e0b;"></i> Settings
                            </a>
                            <a class="nav-link <?= $currentPage === 'logs' ? 'active' : '' ?>" href="<?= site_url('/admin/logs') ?>">
                                <i class="bi bi-journal-text" style="color:#06b6d4;"></i> Activity Logs
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- Sidebar Footer -->
            <div class="sidebar-footer">
                <div style="display:flex;align-items:center;gap:0.6rem;padding:0.3rem 0.3rem 0.3rem 0;margin-bottom:0.8rem;">
                    <div style="width:38px;height:38px;background:linear-gradient(135deg,#3b82f6,#2563eb);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.75rem;flex-shrink:0;">
                        <?= substr(esc($user['username'] ?? 'A'), 0, 1) ?>
                    </div>
                    <div style="flex:1;overflow:hidden;">
                        <div style="font-weight:600;color:#fff;font-size:0.8rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            <?= esc($user['username'] ?? 'Admin') ?>
                        </div>
                        <div style="display:flex;align-items:center;gap:0.3rem;font-size:0.65rem;color:#60a5fa;">
                            <span style="display:inline-block;width:6px;height:6px;background:#22c55e;border-radius:50%;"></span>
                            <?= $isBranchAdmin ? 'Branch Admin' : 'Super Admin' ?>
                        </div>
                        <?php if ($isBranchAdmin && isset($_SESSION['branch_name'])): ?>
                            <div style="font-size:0.6rem;color:#94a3b8;margin-top:2px;">
                                <i class="bi bi-building"></i> <?= esc($_SESSION['branch_name']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <a class="btn btn-sm btn-outline-danger w-100" href="<?= site_url('/logout') ?>" style="border-radius:10px;font-size:0.78rem;padding:0.5rem;border-color:rgba(239,68,68,0.3);color:#ef4444;background:rgba(239,68,68,0.05);transition:all 0.15s;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:0.4rem;"
                    data-confirm="Are you sure you want to log out?">
                    <i class="bi bi-box-arrow-left"></i> Sign Out
                </a>
            </div>
        </aside>

        <!-- ========== MAIN CONTENT ========== -->
        <main class="admin-content">
            <!-- Top Bar -->
            <div class="admin-topbar-clean">
                <div class="page-title">
                    <h5><?= esc($title ?? 'Dashboard') ?></h5>
                    <?php if (!empty($breadcrumb)): ?>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= site_url('/admin/dashboard') ?>">Home</a></li>
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
                    <span class="badge-clean badge-clean-primary d-none-mobile">
                        <i class="bi bi-shield-check"></i> <?= $isBranchAdmin ? 'BRANCH ADMIN' : 'SUPER ADMIN' ?>
                    </span>
                    <?php if ($isBranchAdmin && isset($_SESSION['branch_name'])): ?>
                        <span class="badge-clean badge-clean-success d-none-mobile">
                            <i class="bi bi-building"></i> <?= esc($_SESSION['branch_name']) ?>
                        </span>
                    <?php else: ?>
                        <span class="badge-clean badge-clean-success d-none-mobile">
                            <i class="bi bi-building"></i> All Branches
                        </span>
                    <?php endif; ?>

                    <div class="dropdown">
                        <button class="btn-primary-clean dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-plus-circle"></i> Quick Action
                        </button>
                        <ul class="dropdown-menu dropdown-menu-clean">
                            <li><a class="dropdown-item" href="<?= site_url('/admin/patients/create') ?>"><i class="bi bi-person-plus text-success"></i> New Patient</a></li>
                            <li><a class="dropdown-item" href="<?= site_url('/admin/appointments/create') ?>"><i class="bi bi-calendar-plus text-primary"></i> New Appointment</a></li>
                            <?php if ($isSuperAdmin): ?>
                                <li><a class="dropdown-item" href="<?= site_url('/admin/ipd/admit') ?>"><i class="bi bi-hospital text-warning"></i> Admit IPD</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= site_url('/admin/branches/create') ?>"><i class="bi bi-building-add text-info"></i> New Branch</a></li>
                                <li><a class="dropdown-item" href="<?= site_url('/admin/users/create') ?>"><i class="bi bi-person-plus text-primary"></i> New User</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= site_url('/admin/employees/create') ?>"><i class="bi bi-person-plus text-primary"></i> New Employee</a></li>
                        </ul>
                    </div>

                    <div class="dropdown">
                        <button class="btn-soft-clean d-flex align-items-center gap-2 dropdown-toggle" type="button" data-bs-toggle="dropdown" style="padding:0.15rem 0.8rem 0.15rem 0.15rem;">
                            <span class="user-avatar-sm"><?= substr(esc($user['username'] ?? 'A'), 0, 1) ?></span>
                            <span class="d-none d-sm-inline" style="font-weight:500;font-size:0.8rem;"><?= esc($user['username'] ?? 'Admin') ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-clean">
                            <li><a class="dropdown-item" href="<?= site_url('/admin/profile') ?>"><i class="bi bi-person-circle"></i> My Profile</a></li>
                            <?php if ($isSuperAdmin): ?>
                                <li><a class="dropdown-item" href="<?= site_url('/admin/settings') ?>"><i class="bi bi-gear"></i> Settings</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
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

            <!-- Page Content -->
            <div class="admin-content-inner">