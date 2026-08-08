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

if ($roleSlug === 'receptionist') {
    include __DIR__ . '/reception_header.php';
    return;
}
if ($roleSlug === 'doctor') {
    include __DIR__ . '/doctor_header.php';
    return;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Admin Panel') ?> — MedClinic</title>
    <meta name="robots" content="noindex, nofollow">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
    <!-- ========== SIDEBAR ========== -->
    <aside class="sidebar">
        <div>
            <!-- Brand -->
            <a class="sidebar-brand" href="<?= site_url('/admin/dashboard') ?>">
                <div class="sidebar-brand-icon">
                    <i class="bi bi-hospital"></i>
                </div>
                <span class="sidebar-brand-name">Janki<span>Piles</span></span>
            </a>

            <hr class="sidebar-divider">

            <?php
            $currentPage = $activePage ?? '';
            $isDashboardsActive = in_array($currentPage, ['dashboard', 'reception_dashboard', 'doctor_dashboard'], true);
            $isClinicalActive   = in_array($currentPage, ['patients', 'appointments', 'appointments_pending', 'ipd'], true);
            $isBillingActive    = in_array($currentPage, ['billing', 'inventory'], true);
            $isHrActive         = in_array($currentPage, ['branches', 'employees', 'attendance', 'leaves', 'salary'], true);
            $isCmsActive        = str_starts_with($currentPage, 'cms_');
            $isSystemActive     = in_array($currentPage, ['reports', 'settings', 'logs'], true);
            ?>

            <!-- 1. Dashboards & Consoles Topic -->
            <div class="sidebar-accordion mb-1">
                <button class="sidebar-accordion-header <?= $isDashboardsActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#menu-dashboards" aria-expanded="<?= $isDashboardsActive ? 'true' : 'false' ?>">
                    <span><i class="bi bi-grid-1x2-fill me-2 text-success"></i> Dashboards & Consoles</span>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </button>
                <div class="collapse <?= $isDashboardsActive ? 'show' : '' ?>" id="menu-dashboards">
                    <div class="sidebar-accordion-body">
                        <a class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="<?= site_url('/admin/dashboard') ?>">
                            <i class="bi bi-speedometer2 me-1"></i> Admin Dashboard
                        </a>
                        <?php if (\App\Helpers\Permission::has('view_reception_dashboard')): ?>
                            <a class="nav-link <?= $currentPage === 'reception_dashboard' ? 'active' : '' ?>" href="<?= site_url('/reception') ?>">
                                <i class="bi bi-person-workspace me-1"></i> Reception Desk
                            </a>
                        <?php endif; ?>
                        <?php if (\App\Helpers\Permission::has('view_doctor_dashboard')): ?>
                            <a class="nav-link <?= $currentPage === 'doctor_dashboard' ? 'active' : '' ?>" href="<?= site_url('/doctor') ?>">
                                <i class="bi bi-activity me-1"></i> Physician Console
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- 2. Clinical Operations Topic -->
            <div class="sidebar-accordion mb-1">
                <button class="sidebar-accordion-header <?= $isClinicalActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#menu-clinical" aria-expanded="<?= $isClinicalActive ? 'true' : 'false' ?>">
                    <span><i class="bi bi-hospital-fill me-2 text-info"></i> Clinical & Patients</span>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </button>
                <div class="collapse <?= $isClinicalActive ? 'show' : '' ?>" id="menu-clinical">
                    <div class="sidebar-accordion-body">
                        <?php if (\App\Helpers\Permission::has('manage_patients')): ?>
                            <a class="nav-link <?= $currentPage === 'patients' ? 'active' : '' ?>" href="<?= site_url('/admin/patients') ?>">
                                <i class="bi bi-person-lines-fill me-1"></i> Patients Directory
                            </a>
                        <?php endif; ?>
                        <?php if (\App\Helpers\Permission::has('manage_appointments')): ?>
                            <a class="nav-link <?= $currentPage === 'appointments' ? 'active' : '' ?>" href="<?= site_url('/admin/appointments') ?>">
                                <i class="bi bi-calendar-check me-1"></i> Appointments
                            </a>
                            <a class="nav-link <?= $currentPage === 'appointments_pending' ? 'active' : '' ?>" href="<?= site_url('/admin/appointments/pending') ?>">
                                <i class="bi bi-clock-history me-1"></i> Pending Approvals
                            </a>
                        <?php endif; ?>
                        <?php if (\App\Helpers\Permission::has('manage_ipd')): ?>
                            <a class="nav-link <?= $currentPage === 'ipd' ? 'active' : '' ?>" href="<?= site_url('/admin/ipd') ?>">
                                <i class="bi bi-hospital me-1"></i> IPD Ward Admissions
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- 3. Billing & Inventory Topic -->
            <?php if (\App\Helpers\Permission::has('manage_reception_dashboard')): ?>
                <div class="sidebar-accordion mb-1">
                    <button class="sidebar-accordion-header <?= $isBillingActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#menu-billing" aria-expanded="<?= $isBillingActive ? 'true' : 'false' ?>">
                        <span><i class="bi bi-wallet2 me-2 text-warning"></i> Billing & Pharmacy</span>
                        <i class="bi bi-chevron-down chevron-icon"></i>
                    </button>
                    <div class="collapse <?= $isBillingActive ? 'show' : '' ?>" id="menu-billing">
                        <div class="sidebar-accordion-body">
                            <a class="nav-link <?= $currentPage === 'billing' ? 'active' : '' ?>" href="<?= site_url('/admin/billing') ?>">
                                <i class="bi bi-receipt-cutoff me-1"></i> Billing Ledger
                            </a>
                            <a class="nav-link <?= $currentPage === 'inventory' ? 'active' : '' ?>" href="<?= site_url('/admin/inventory') ?>">
                                <i class="bi bi-capsule me-1"></i> Medicine Inventory
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- 4. HR & Administration Topic -->
            <div class="sidebar-accordion mb-1">
                <button class="sidebar-accordion-header <?= $isHrActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#menu-hr" aria-expanded="<?= $isHrActive ? 'true' : 'false' ?>">
                    <span><i class="bi bi-people-fill me-2 text-primary"></i> HR & Operations</span>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </button>
                <div class="collapse <?= $isHrActive ? 'show' : '' ?>" id="menu-hr">
                    <div class="sidebar-accordion-body">
                        <?php if (\App\Helpers\Permission::has('manage_branches')): ?>
                            <a class="nav-link <?= $currentPage === 'branches' ? 'active' : '' ?>" href="<?= site_url('/admin/branches') ?>">
                                <i class="bi bi-building me-1"></i> Branches
                            </a>
                        <?php endif; ?>
                        <?php if (\App\Helpers\Permission::has('manage_employees')): ?>
                            <a class="nav-link <?= $currentPage === 'employees' ? 'active' : '' ?>" href="<?= site_url('/admin/employees') ?>">
                                <i class="bi bi-person-gear me-1"></i> Employee Directory
                            </a>
                        <?php endif; ?>
                        <?php if (\App\Helpers\Permission::has('record_attendance')): ?>
                            <a class="nav-link <?= $currentPage === 'attendance' ? 'active' : '' ?>" href="<?= site_url('/admin/employees/attendance') ?>">
                                <i class="bi bi-calendar-check me-1"></i> Attendance Roster
                            </a>
                            <a class="nav-link <?= $currentPage === 'leaves' ? 'active' : '' ?>" href="<?= site_url('/admin/employees/attendance/leaves') ?>">
                                <i class="bi bi-calendar-minus me-1"></i> Leaves Panel
                            </a>
                        <?php endif; ?>
                        <?php if (\App\Helpers\Permission::has('manage_employees')): ?>
                            <a class="nav-link <?= $currentPage === 'salary' ? 'active' : '' ?>" href="<?= site_url('/admin/salary') ?>">
                                <i class="bi bi-cash-stack me-1"></i> Salary Payrolls
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- 5. Website CMS & CRM Topic -->
            <?php if (\App\Helpers\Permission::has('manage_settings')): ?>
                <div class="sidebar-accordion mb-1">
                    <button class="sidebar-accordion-header <?= $isCmsActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#menu-cms" aria-expanded="<?= $isCmsActive ? 'true' : 'false' ?>">
                        <span><i class="bi bi-window-sidebar me-2 text-purple"></i> Website CMS & CRM</span>
                        <i class="bi bi-chevron-down chevron-icon"></i>
                    </button>
                    <div class="collapse <?= $isCmsActive ? 'show' : '' ?>" id="menu-cms">
                        <div class="sidebar-accordion-body">
                            <a class="nav-link <?= $currentPage === 'cms_settings' ? 'active' : '' ?>" href="<?= site_url('/admin/cms/settings') ?>">
                                <i class="bi bi-sliders me-1"></i> Layout Settings
                            </a>
                            <a class="nav-link <?= $currentPage === 'cms_blogs' ? 'active' : '' ?>" href="<?= site_url('/admin/cms/blogs') ?>">
                                <i class="bi bi-newspaper me-1"></i> Health Blogs
                            </a>
                            <a class="nav-link <?= $currentPage === 'cms_comments' ? 'active' : '' ?>" href="<?= site_url('/admin/cms/comments') ?>">
                                <i class="bi bi-chat-left-text me-1"></i> Comments Queue
                            </a>
                            <a class="nav-link <?= $currentPage === 'cms_treatments' ? 'active' : '' ?>" href="<?= site_url('/admin/cms/treatments') ?>">
                                <i class="bi bi-heart-pulse me-1"></i> Specialty Catalog
                            </a>
                            <a class="nav-link <?= $currentPage === 'cms_gallery' ? 'active' : '' ?>" href="<?= site_url('/admin/cms/gallery') ?>">
                                <i class="bi bi-images me-1"></i> Media Gallery
                            </a>
                            <a class="nav-link <?= $currentPage === 'cms_testimonials' ? 'active' : '' ?>" href="<?= site_url('/admin/cms/testimonials') ?>">
                                <i class="bi bi-star-fill me-1"></i> Patient Reviews
                            </a>
                            <a class="nav-link <?= $currentPage === 'cms_enquiries' ? 'active' : '' ?>" href="<?= site_url('/admin/cms/enquiries') ?>">
                                <i class="bi bi-envelope-paper me-1"></i> CRM Lead Pipeline
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- 6. System & Analytics Topic -->
            <div class="sidebar-accordion mb-1">
                <button class="sidebar-accordion-header <?= $isSystemActive ? 'active-header' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#menu-system" aria-expanded="<?= $isSystemActive ? 'true' : 'false' ?>">
                    <span><i class="bi bi-gear-wide-connected me-2 text-danger"></i> System & Analytics</span>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </button>
                <div class="collapse <?= $isSystemActive ? 'show' : '' ?>" id="menu-system">
                    <div class="sidebar-accordion-body">
                        <?php if (\App\Helpers\Permission::has('view_logs')): ?>
                            <a class="nav-link <?= $currentPage === 'reports' ? 'active' : '' ?>" href="<?= site_url('/admin/reports') ?>">
                                <i class="bi bi-graph-up-arrow me-1"></i> Analytics Reports
                            </a>
                        <?php endif; ?>
                        <?php if (\App\Helpers\Permission::has('manage_settings')): ?>
                            <a class="nav-link <?= $currentPage === 'settings' ? 'active' : '' ?>" href="<?= site_url('/admin/settings') ?>">
                                <i class="bi bi-gear me-1"></i> System Settings
                            </a>
                        <?php endif; ?>
                        <?php if (\App\Helpers\Permission::has('view_logs')): ?>
                            <a class="nav-link <?= $currentPage === 'logs' ? 'active' : '' ?>" href="<?= site_url('/admin/logs') ?>">
                                <i class="bi bi-journal-text me-1"></i> Activity Logs
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Footer -->
        <div>
            <hr class="sidebar-divider">
            <div class="sidebar-user">
                <div class="sidebar-user-avatar">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <div class="sidebar-user-name"><?= esc($user['username'] ?? 'Admin') ?></div>
                    <div class="sidebar-user-role">
                        <span class="online-dot me-1"></span><?= esc(ucfirst($user['role'] ?? 'admin')) ?>
                    </div>
                </div>
            </div>
            <a class="btn btn-sm btn-outline-danger w-100" href="<?= site_url('/logout') ?>"
               data-confirm="Are you sure you want to log out?">
                <i class="bi bi-box-arrow-left me-1"></i> Logout
            </a>
        </div>
    </aside>

    <!-- ========== MAIN CONTENT ========== -->
    <main class="admin-content">
        <!-- Top Bar -->
        <div class="admin-topbar">
            <div>
                <h1><?= esc($title ?? 'Dashboard') ?></h1>
                <?php if (!empty($breadcrumb)): ?>
                    <nav aria-label="breadcrumb" class="mt-1">
                        <ol class="breadcrumb mb-0" style="font-size:0.8rem;">
                            <li class="breadcrumb-item"><a href="<?= site_url('/admin/dashboard') ?>" class="text-decoration-none">Home</a></li>
                            <?php foreach ($breadcrumb as $label => $url): ?>
                                <?php if ($url): ?>
                                    <li class="breadcrumb-item"><a href="<?= esc($url) ?>" class="text-decoration-none"><?= esc($label) ?></a></li>
                                <?php else: ?>
                                    <li class="breadcrumb-item active"><?= esc($label) ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ol>
                    </nav>
                <?php endif; ?>
            </div>
            <div class="topbar-actions">
                <span class="badge bg-white text-secondary border px-3 py-2" style="font-size:0.75rem;">
                    <i class="bi bi-shield-check text-success me-1"></i><?= esc(strtoupper($user['role'] ?? 'ADMIN')) ?>
                </span>
                <span class="badge bg-white text-secondary border px-3 py-2" style="font-size:0.75rem;" id="live-clock">
                    <i class="bi bi-clock me-1"></i>--:--
                </span>
                <a href="<?= site_url() ?>" class="btn btn-sm btn-outline-secondary px-3" target="_blank">
                    <i class="bi bi-globe me-1"></i> View Site
                </a>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if ($success = \App\Helpers\Session::getFlash('success')): ?>
            <div class="alert alert-success alert-dismissible fade show alert-dismiss-flash mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= esc($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error = \App\Helpers\Session::getFlash('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show alert-dismiss-flash mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= esc($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($warning = \App\Helpers\Session::getFlash('warning')): ?>
            <div class="alert alert-warning alert-dismissible fade show alert-dismiss-flash mb-4" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i> <?= esc($warning) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Page Content starts below -->
