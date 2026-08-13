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
    <title><?= esc($title ?? 'Admin Panel') ?> — janki piles</title>
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

    <style>
        /* Clean header styles - inline */
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

        @media (max-width: 768px) {
            .admin-topbar-clean {
                padding: 0.6rem 1rem;
            }

            .admin-topbar-clean .topbar-actions .d-none-mobile {
                display: none;
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
        <!-- ========== SIDEBAR ========== -->
        <aside class="sidebar" style="background: #0b1a2b; width: 280px; min-height: 100vh; padding: 1.2rem 1rem; display: flex; flex-direction: column; justify-content: space-between; position: sticky; top: 0; height: 100vh; overflow-y: auto; border-right: 1px solid rgba(255,255,255,0.06);">
            <div>
                <!-- Brand -->
                <a class="sidebar-brand" href="<?= site_url('/admin/dashboard') ?>" style="display: flex; align-items: center; gap: 0.8rem; text-decoration: none; margin-bottom: 1.2rem; padding: 0.2rem 0.3rem;">
                    <div style="width: 44px; height: 44px; background: linear-gradient(135deg, #2563eb, #1d4ed8); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: #fff; flex-shrink: 0;">
                        <i class="bi bi-hospital"></i>
                    </div>
                    <div>
                        <span style="font-size: 1.2rem; font-weight: 700; color: #fff; letter-spacing: -0.3px;">Janki<span style="color: #3b82f6;">Piles</span></span>
                        <div style="font-size: 0.6rem; color: #94a3b8; letter-spacing: 0.5px; text-transform: uppercase; margin-top: -2px;">Clinic Management</div>
                    </div>
                </a>

                <hr style="border-color: rgba(255,255,255,0.06); margin: 0.8rem 0 1rem 0;">

                <?php
                $currentPage = $activePage ?? '';
                $isDashboardsActive = in_array($currentPage, ['dashboard', 'reception_dashboard', 'doctor_dashboard'], true);
                $isClinicalActive   = in_array($currentPage, ['patients', 'appointments', 'appointments_pending', 'ipd'], true);
                $isBillingActive    = in_array($currentPage, ['billing', 'inventory'], true);
                $isHrActive         = in_array($currentPage, ['branches', 'employees', 'attendance', 'leaves', 'salary'], true);
                $isCmsActive        = str_starts_with($currentPage, 'cms_');
                $isSystemActive     = in_array($currentPage, ['reports', 'settings', 'logs'], true);
                ?>

                <!-- 1. Dashboards & Consoles -->
                <div style="margin-bottom: 0.3rem;">
                    <button style="width: 100%; background: <?= $isDashboardsActive ? 'rgba(59,130,246,0.15)' : 'transparent'; ?>; border: none; color: <?= $isDashboardsActive ? '#60a5fa' : '#94a3b8'; ?>; padding: 0.6rem 0.8rem; border-radius: 10px; display: flex; align-items: center; justify-content: space-between; font-size: 0.78rem; font-weight: 600; cursor: pointer; transition: all 0.15s;"
                        type="button" data-bs-toggle="collapse" data-bs-target="#menu-dashboards" aria-expanded="<?= $isDashboardsActive ? 'true' : 'false' ?>">
                        <span><i class="bi bi-grid-1x2-fill" style="color: #22c55e; margin-right: 0.6rem;"></i> Dashboards & Consoles</span>
                        <i class="bi bi-chevron-down" style="font-size: 0.7rem; transition: transform 0.2s; transform: <?= $isDashboardsActive ? 'rotate(180deg)' : 'rotate(0)'; ?>;"></i>
                    </button>
                    <div class="collapse <?= $isDashboardsActive ? 'show' : '' ?>" id="menu-dashboards" style="padding: 0.2rem 0.5rem 0.2rem 0;">
                        <div style="display: flex; flex-direction: column; gap: 0.1rem;">
                            <a class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="<?= site_url('/admin/dashboard') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; border-radius: 8px; color: <?= $currentPage === 'dashboard' ? '#60a5fa' : '#94a3b8'; ?>; text-decoration: none; font-size: 0.78rem; transition: all 0.1s; background: <?= $currentPage === 'dashboard' ? 'rgba(59,130,246,0.1)' : 'transparent'; ?>; font-weight: <?= $currentPage === 'dashboard' ? '500' : '400'; ?>;">
                                <i class="bi bi-speedometer2" style="font-size: 1rem;"></i> Admin Dashboard
                            </a>
                            <?php if (\App\Helpers\Permission::has('view_reception_dashboard')): ?>
                                <a class="nav-link <?= $currentPage === 'reception_dashboard' ? 'active' : '' ?>" href="<?= site_url('/reception') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; border-radius: 8px; color: <?= $currentPage === 'reception_dashboard' ? '#60a5fa' : '#94a3b8'; ?>; text-decoration: none; font-size: 0.78rem; transition: all 0.1s; background: <?= $currentPage === 'reception_dashboard' ? 'rgba(59,130,246,0.1)' : 'transparent'; ?>; font-weight: <?= $currentPage === 'reception_dashboard' ? '500' : '400'; ?>;">
                                    <i class="bi bi-person-workspace" style="font-size: 1rem;"></i> Reception Desk
                                </a>
                            <?php endif; ?>
                            <?php if (\App\Helpers\Permission::has('view_doctor_dashboard')): ?>
                                <a class="nav-link <?= $currentPage === 'doctor_dashboard' ? 'active' : '' ?>" href="<?= site_url('/doctor') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; border-radius: 8px; color: <?= $currentPage === 'doctor_dashboard' ? '#60a5fa' : '#94a3b8'; ?>; text-decoration: none; font-size: 0.78rem; transition: all 0.1s; background: <?= $currentPage === 'doctor_dashboard' ? 'rgba(59,130,246,0.1)' : 'transparent'; ?>; font-weight: <?= $currentPage === 'doctor_dashboard' ? '500' : '400'; ?>;">
                                    <i class="bi bi-activity" style="font-size: 1rem;"></i> Physician Console
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- 2. Clinical & Patients -->
                <div style="margin-bottom: 0.3rem;">
                    <button style="width: 100%; background: <?= $isClinicalActive ? 'rgba(59,130,246,0.15)' : 'transparent'; ?>; border: none; color: <?= $isClinicalActive ? '#60a5fa' : '#94a3b8'; ?>; padding: 0.6rem 0.8rem; border-radius: 10px; display: flex; align-items: center; justify-content: space-between; font-size: 0.78rem; font-weight: 600; cursor: pointer; transition: all 0.15s;"
                        type="button" data-bs-toggle="collapse" data-bs-target="#menu-clinical" aria-expanded="<?= $isClinicalActive ? 'true' : 'false' ?>">
                        <span><i class="bi bi-hospital-fill" style="color: #06b6d4; margin-right: 0.6rem;"></i> Clinical & Patients</span>
                        <i class="bi bi-chevron-down" style="font-size: 0.7rem; transition: transform 0.2s; transform: <?= $isClinicalActive ? 'rotate(180deg)' : 'rotate(0)'; ?>;"></i>
                    </button>
                    <div class="collapse <?= $isClinicalActive ? 'show' : '' ?>" id="menu-clinical" style="padding: 0.2rem 0.5rem 0.2rem 0;">
                        <div style="display: flex; flex-direction: column; gap: 0.1rem;">
                            <?php if (\App\Helpers\Permission::has('manage_patients')): ?>
                                <a class="nav-link <?= $currentPage === 'patients' ? 'active' : '' ?>" href="<?= site_url('/admin/patients') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; border-radius: 8px; color: <?= $currentPage === 'patients' ? '#60a5fa' : '#94a3b8'; ?>; text-decoration: none; font-size: 0.78rem; transition: all 0.1s; background: <?= $currentPage === 'patients' ? 'rgba(59,130,246,0.1)' : 'transparent'; ?>; font-weight: <?= $currentPage === 'patients' ? '500' : '400'; ?>;">
                                    <i class="bi bi-person-lines-fill" style="font-size: 1rem; color: #22c55e;"></i> Patients Directory
                                </a>
                            <?php endif; ?>
                            <?php if (\App\Helpers\Permission::has('manage_appointments')): ?>
                                <a class="nav-link <?= $currentPage === 'appointments' ? 'active' : '' ?>" href="<?= site_url('/admin/appointments') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; border-radius: 8px; color: <?= $currentPage === 'appointments' ? '#60a5fa' : '#94a3b8'; ?>; text-decoration: none; font-size: 0.78rem; transition: all 0.1s; background: <?= $currentPage === 'appointments' ? 'rgba(59,130,246,0.1)' : 'transparent'; ?>; font-weight: <?= $currentPage === 'appointments' ? '500' : '400'; ?>;">
                                    <i class="bi bi-calendar-check" style="font-size: 1rem; color: #8b5cf6;"></i> Appointments
                                </a>
                                <a class="nav-link <?= $currentPage === 'appointments_pending' ? 'active' : '' ?>" href="<?= site_url('/admin/appointments/pending') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; border-radius: 8px; color: <?= $currentPage === 'appointments_pending' ? '#60a5fa' : '#94a3b8'; ?>; text-decoration: none; font-size: 0.78rem; transition: all 0.1s; background: <?= $currentPage === 'appointments_pending' ? 'rgba(59,130,246,0.1)' : 'transparent'; ?>; font-weight: <?= $currentPage === 'appointments_pending' ? '500' : '400'; ?>;">
                                    <i class="bi bi-clock-history" style="font-size: 1rem; color: #f59e0b;"></i> Pending Approvals
                                </a>
                            <?php endif; ?>
                            <?php if (\App\Helpers\Permission::has('manage_ipd')): ?>
                                <a class="nav-link <?= $currentPage === 'ipd' ? 'active' : '' ?>" href="<?= site_url('/admin/ipd') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; border-radius: 8px; color: <?= $currentPage === 'ipd' ? '#60a5fa' : '#94a3b8'; ?>; text-decoration: none; font-size: 0.78rem; transition: all 0.1s; background: <?= $currentPage === 'ipd' ? 'rgba(59,130,246,0.1)' : 'transparent'; ?>; font-weight: <?= $currentPage === 'ipd' ? '500' : '400'; ?>;">
                                    <i class="bi bi-hospital" style="font-size: 1rem; color: #ec4899;"></i> IPD Ward Admissions
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- 3. Billing & Pharmacy -->
                <?php if (\App\Helpers\Permission::has('manage_reception_dashboard')): ?>
                    <div style="margin-bottom: 0.3rem;">
                        <button style="width: 100%; background: <?= $isBillingActive ? 'rgba(59,130,246,0.15)' : 'transparent'; ?>; border: none; color: <?= $isBillingActive ? '#60a5fa' : '#94a3b8'; ?>; padding: 0.6rem 0.8rem; border-radius: 10px; display: flex; align-items: center; justify-content: space-between; font-size: 0.78rem; font-weight: 600; cursor: pointer; transition: all 0.15s;"
                            type="button" data-bs-toggle="collapse" data-bs-target="#menu-billing" aria-expanded="<?= $isBillingActive ? 'true' : 'false' ?>">
                            <span><i class="bi bi-wallet2" style="color: #f59e0b; margin-right: 0.6rem;"></i> Billing & Pharmacy</span>
                            <i class="bi bi-chevron-down" style="font-size: 0.7rem; transition: transform 0.2s; transform: <?= $isBillingActive ? 'rotate(180deg)' : 'rotate(0)'; ?>;"></i>
                        </button>
                        <div class="collapse <?= $isBillingActive ? 'show' : '' ?>" id="menu-billing" style="padding: 0.2rem 0.5rem 0.2rem 0;">
                            <div style="display: flex; flex-direction: column; gap: 0.1rem;">
                                <a class="nav-link <?= $currentPage === 'billing' ? 'active' : '' ?>" href="<?= site_url('/admin/billing') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; border-radius: 8px; color: <?= $currentPage === 'billing' ? '#60a5fa' : '#94a3b8'; ?>; text-decoration: none; font-size: 0.78rem; transition: all 0.1s; background: <?= $currentPage === 'billing' ? 'rgba(59,130,246,0.1)' : 'transparent'; ?>; font-weight: <?= $currentPage === 'billing' ? '500' : '400'; ?>;">
                                    <i class="bi bi-receipt-cutoff" style="font-size: 1rem; color: #8b5cf6;"></i> Billing Ledger
                                </a>
                                <a class="nav-link <?= $currentPage === 'inventory' ? 'active' : '' ?>" href="<?= site_url('/admin/inventory') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; border-radius: 8px; color: <?= $currentPage === 'inventory' ? '#60a5fa' : '#94a3b8'; ?>; text-decoration: none; font-size: 0.78rem; transition: all 0.1s; background: <?= $currentPage === 'inventory' ? 'rgba(59,130,246,0.1)' : 'transparent'; ?>; font-weight: <?= $currentPage === 'inventory' ? '500' : '400'; ?>;">
                                    <i class="bi bi-capsule" style="font-size: 1rem; color: #ef4444;"></i> Medicine Inventory
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- 4. HR & Operations -->
                 <div style="margin-bottom: 0.3rem;">
    <button style="width: 100%; background: <?= $isHrActive ? 'rgba(59,130,246,0.15)' : 'transparent'; ?>; border: none; color: <?= $isHrActive ? '#60a5fa' : '#94a3b8'; ?>; padding: 0.6rem 0.8rem; border-radius: 10px; display: flex; align-items: center; justify-content: space-between; font-size: 0.78rem; font-weight: 600; cursor: pointer; transition: all 0.15s;"
            type="button" data-bs-toggle="collapse" data-bs-target="#menu-hr" aria-expanded="<?= $isHrActive ? 'true' : 'false' ?>">
            <span><i class="bi bi-people-fill" style="color: #3b82f6; margin-right: 0.6rem;"></i> HR & Operations</span>
            <i class="bi bi-chevron-down" style="font-size: 0.7rem; transition: transform 0.2s; transform: <?= $isHrActive ? 'rotate(180deg)' : 'rotate(0)'; ?>;"></i>
        </button>
        <div class="collapse <?= $isHrActive ? 'show' : '' ?>" id="menu-hr" style="padding: 0.2rem 0.5rem 0.2rem 0;">
            <div style="display: flex; flex-direction: column; gap: 0.1rem;">

                <!-- Employee Management -->
                <?php if (\App\Helpers\Permission::has('manage_employees')): ?>
                    <a class="nav-link <?= $currentPage === 'employees' ? 'active' : '' ?>" href="<?= site_url('/admin/employees') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; border-radius: 8px; color: <?= $currentPage === 'employees' ? '#60a5fa' : '#94a3b8'; ?>; text-decoration: none; font-size: 0.78rem; transition: all 0.1s; background: <?= $currentPage === 'employees' ? 'rgba(59,130,246,0.1)' : 'transparent'; ?>; font-weight: <?= $currentPage === 'employees' ? '500' : '400'; ?>;">
                        <i class="bi bi-person-gear" style="font-size: 1rem; color: #8b5cf6;"></i> Employee Directory
                    </a>
                <?php endif; ?>

                <!-- Employee ID Cards - Fixed Icon -->
                <?php if (\App\Helpers\Permission::has('manage_employees')): ?>
                    <a class="nav-link <?= $currentPage === 'id_cards' ? 'active' : '' ?>" href="<?= site_url('/admin/employees/id-cards') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; border-radius: 8px; color: <?= $currentPage === 'id_cards' ? '#60a5fa' : '#94a3b8'; ?>; text-decoration: none; font-size: 0.78rem; transition: all 0.1s; background: <?= $currentPage === 'id_cards' ? 'rgba(59,130,246,0.1)' : 'transparent'; ?>; font-weight: <?= $currentPage === 'id_cards' ? '500' : '400'; ?>;">
                        <i class="bi bi-credit-card-2-front" style="font-size: 1rem; color: #3b82f6;"></i> ID Cards Generator
                    </a>
                <?php endif; ?>

                <!-- Attendance Section -->
                <hr style="border-color: rgba(255,255,255,0.06); margin: 0.3rem 0.8rem;">

                <!-- Manual Attendance -->
               

                <!-- QR Attendance Scanner -->
                <?php if (\App\Helpers\Permission::has('record_attendance')): ?>
                    <a class="nav-link <?= $currentPage === 'attendance_scan' ? 'active' : '' ?>" href="<?= site_url('/admin/attendance/scan') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; border-radius: 8px; color: <?= $currentPage === 'attendance_scan' ? '#60a5fa' : '#94a3b8'; ?>; text-decoration: none; font-size: 0.78rem; transition: all 0.1s; background: <?= $currentPage === 'attendance_scan' ? 'rgba(59,130,246,0.1)' : 'transparent'; ?>; font-weight: <?= $currentPage === 'attendance_scan' ? '500' : '400'; ?>;">
                        <i class="bi bi-qr-code-scan" style="font-size: 1rem; color: #8b5cf6;"></i> QR Attendance Scanner
                    </a>
                <?php endif; ?>

                <!-- Attendance Report -->
                <?php if (\App\Helpers\Permission::has('view_logs')): ?>
                    <a class="nav-link <?= $currentPage === 'attendance_report' ? 'active' : '' ?>" href="<?= site_url('/admin/attendance/report') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; border-radius: 8px; color: <?= $currentPage === 'attendance_report' ? '#60a5fa' : '#94a3b8'; ?>; text-decoration: none; font-size: 0.78rem; transition: all 0.1s; background: <?= $currentPage === 'attendance_report' ? 'rgba(59,130,246,0.1)' : 'transparent'; ?>; font-weight: <?= $currentPage === 'attendance_report' ? '500' : '400'; ?>;">
                        <i class="bi bi-graph-up-arrow" style="font-size: 1rem; color: #22c55e;"></i> Attendance Report
                    </a>
                <?php endif; ?>

                <!-- Leaves Panel -->
                <?php if (\App\Helpers\Permission::has('record_attendance')): ?>
                    <a class="nav-link <?= $currentPage === 'leaves' ? 'active' : '' ?>" href="<?= site_url('/admin/employees/attendance/leaves') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; border-radius: 8px; color: <?= $currentPage === 'leaves' ? '#60a5fa' : '#94a3b8'; ?>; text-decoration: none; font-size: 0.78rem; transition: all 0.1s; background: <?= $currentPage === 'leaves' ? 'rgba(59,130,246,0.1)' : 'transparent'; ?>; font-weight: <?= $currentPage === 'leaves' ? '500' : '400'; ?>;">
                        <i class="bi bi-calendar-minus" style="font-size: 1rem; color: #f59e0b;"></i> Leaves Panel
                    </a>
                <?php endif; ?>

                <!-- Salary Payrolls -->
                <?php if (\App\Helpers\Permission::has('manage_employees')): ?>
                    <a class="nav-link <?= $currentPage === 'salary' ? 'active' : '' ?>" href="<?= site_url('/admin/salary') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; border-radius: 8px; color: <?= $currentPage === 'salary' ? '#60a5fa' : '#94a3b8'; ?>; text-decoration: none; font-size: 0.78rem; transition: all 0.1s; background: <?= $currentPage === 'salary' ? 'rgba(59,130,246,0.1)' : 'transparent'; ?>; font-weight: <?= $currentPage === 'salary' ? '500' : '400'; ?>;">
                        <i class="bi bi-cash-stack" style="font-size: 1rem; color: #22c55e;"></i> Salary Payrolls
                    </a>
                <?php endif; ?>

            </div>
        </div>
    </div>

                <!-- 5. Website CMS & CRM -->
                <?php if (\App\Helpers\Permission::has('manage_settings')): ?>
                    <div style="margin-bottom: 0.3rem;">
                        <button style="width: 100%; background: <?= $isCmsActive ? 'rgba(59,130,246,0.15)' : 'transparent'; ?>; border: none; color: <?= $isCmsActive ? '#60a5fa' : '#94a3b8'; ?>; padding: 0.6rem 0.8rem; border-radius: 10px; display: flex; align-items: center; justify-content: space-between; font-size: 0.78rem; font-weight: 600; cursor: pointer; transition: all 0.15s;"
                            type="button" data-bs-toggle="collapse" data-bs-target="#menu-cms" aria-expanded="<?= $isCmsActive ? 'true' : 'false' ?>">
                            <span><i class="bi bi-window-sidebar" style="color: #a855f7; margin-right: 0.6rem;"></i> Website CMS & CRM</span>
                            <i class="bi bi-chevron-down" style="font-size: 0.7rem; transition: transform 0.2s; transform: <?= $isCmsActive ? 'rotate(180deg)' : 'rotate(0)'; ?>;"></i>
                        </button>
                        <div class="collapse <?= $isCmsActive ? 'show' : '' ?>" id="menu-cms" style="padding: 0.2rem 0.5rem 0.2rem 0;">
                            <div style="display: flex; flex-direction: column; gap: 0.1rem;">
                                <a class="nav-link <?= $currentPage === 'cms_settings' ? 'active' : '' ?>" href="<?= site_url('/admin/cms/settings') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; border-radius: 8px; color: <?= $currentPage === 'cms_settings' ? '#60a5fa' : '#94a3b8'; ?>; text-decoration: none; font-size: 0.78rem; transition: all 0.1s; background: <?= $currentPage === 'cms_settings' ? 'rgba(59,130,246,0.1)' : 'transparent'; ?>; font-weight: <?= $currentPage === 'cms_settings' ? '500' : '400'; ?>;">
                                    <i class="bi bi-sliders" style="font-size: 1rem; color: #a855f7;"></i> Layout Settings
                                </a>
                                <a class="nav-link <?= $currentPage === 'cms_blogs' ? 'active' : '' ?>" href="<?= site_url('/admin/cms/blogs') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; border-radius: 8px; color: <?= $currentPage === 'cms_blogs' ? '#60a5fa' : '#94a3b8'; ?>; text-decoration: none; font-size: 0.78rem; transition: all 0.1s; background: <?= $currentPage === 'cms_blogs' ? 'rgba(59,130,246,0.1)' : 'transparent'; ?>; font-weight: <?= $currentPage === 'cms_blogs' ? '500' : '400'; ?>;">
                                    <i class="bi bi-newspaper" style="font-size: 1rem; color: #06b6d4;"></i> Health Blogs
                                </a>
                                <a class="nav-link <?= $currentPage === 'cms_comments' ? 'active' : '' ?>" href="<?= site_url('/admin/cms/comments') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; border-radius: 8px; color: <?= $currentPage === 'cms_comments' ? '#60a5fa' : '#94a3b8'; ?>; text-decoration: none; font-size: 0.78rem; transition: all 0.1s; background: <?= $currentPage === 'cms_comments' ? 'rgba(59,130,246,0.1)' : 'transparent'; ?>; font-weight: <?= $currentPage === 'cms_comments' ? '500' : '400'; ?>;">
                                    <i class="bi bi-chat-left-text" style="font-size: 1rem; color: #f59e0b;"></i> Comments Queue
                                </a>
                                <a class="nav-link <?= $currentPage === 'cms_treatments' ? 'active' : '' ?>" href="<?= site_url('/admin/cms/treatments') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; border-radius: 8px; color: <?= $currentPage === 'cms_treatments' ? '#60a5fa' : '#94a3b8'; ?>; text-decoration: none; font-size: 0.78rem; transition: all 0.1s; background: <?= $currentPage === 'cms_treatments' ? 'rgba(59,130,246,0.1)' : 'transparent'; ?>; font-weight: <?= $currentPage === 'cms_treatments' ? '500' : '400'; ?>;">
                                    <i class="bi bi-heart-pulse" style="font-size: 1rem; color: #ef4444;"></i> Specialty Catalog
                                </a>
                                <a class="nav-link <?= $currentPage === 'cms_gallery' ? 'active' : '' ?>" href="<?= site_url('/admin/cms/gallery') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; border-radius: 8px; color: <?= $currentPage === 'cms_gallery' ? '#60a5fa' : '#94a3b8'; ?>; text-decoration: none; font-size: 0.78rem; transition: all 0.1s; background: <?= $currentPage === 'cms_gallery' ? 'rgba(59,130,246,0.1)' : 'transparent'; ?>; font-weight: <?= $currentPage === 'cms_gallery' ? '500' : '400'; ?>;">
                                    <i class="bi bi-images" style="font-size: 1rem; color: #8b5cf6;"></i> Media Gallery
                                </a>
                                <a class="nav-link <?= $currentPage === 'cms_testimonials' ? 'active' : '' ?>" href="<?= site_url('/admin/cms/testimonials') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; border-radius: 8px; color: <?= $currentPage === 'cms_testimonials' ? '#60a5fa' : '#94a3b8'; ?>; text-decoration: none; font-size: 0.78rem; transition: all 0.1s; background: <?= $currentPage === 'cms_testimonials' ? 'rgba(59,130,246,0.1)' : 'transparent'; ?>; font-weight: <?= $currentPage === 'cms_testimonials' ? '500' : '400'; ?>;">
                                    <i class="bi bi-star-fill" style="font-size: 1rem; color: #f59e0b;"></i> Patient Reviews
                                </a>
                                <a class="nav-link <?= $currentPage === 'cms_enquiries' ? 'active' : '' ?>" href="<?= site_url('/admin/cms/enquiries') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; border-radius: 8px; color: <?= $currentPage === 'cms_enquiries' ? '#60a5fa' : '#94a3b8'; ?>; text-decoration: none; font-size: 0.78rem; transition: all 0.1s; background: <?= $currentPage === 'cms_enquiries' ? 'rgba(59,130,246,0.1)' : 'transparent'; ?>; font-weight: <?= $currentPage === 'cms_enquiries' ? '500' : '400'; ?>;">
                                    <i class="bi bi-envelope-paper" style="font-size: 1rem; color: #22c55e;"></i> CRM Lead Pipeline
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- 6. System & Analytics -->
                <div style="margin-bottom: 0.3rem;">
                    <button style="width: 100%; background: <?= $isSystemActive ? 'rgba(59,130,246,0.15)' : 'transparent'; ?>; border: none; color: <?= $isSystemActive ? '#60a5fa' : '#94a3b8'; ?>; padding: 0.6rem 0.8rem; border-radius: 10px; display: flex; align-items: center; justify-content: space-between; font-size: 0.78rem; font-weight: 600; cursor: pointer; transition: all 0.15s;"
                        type="button" data-bs-toggle="collapse" data-bs-target="#menu-system" aria-expanded="<?= $isSystemActive ? 'true' : 'false' ?>">
                        <span><i class="bi bi-gear-wide-connected" style="color: #ef4444; margin-right: 0.6rem;"></i> System & Analytics</span>
                        <i class="bi bi-chevron-down" style="font-size: 0.7rem; transition: transform 0.2s; transform: <?= $isSystemActive ? 'rotate(180deg)' : 'rotate(0)'; ?>;"></i>
                    </button>
                    <div class="collapse <?= $isSystemActive ? 'show' : '' ?>" id="menu-system" style="padding: 0.2rem 0.5rem 0.2rem 0;">
                        <div style="display: flex; flex-direction: column; gap: 0.1rem;">
                            <?php if (\App\Helpers\Permission::has('view_logs')): ?>
                                <a class="nav-link <?= $currentPage === 'reports' ? 'active' : '' ?>" href="<?= site_url('/admin/reports') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; border-radius: 8px; color: <?= $currentPage === 'reports' ? '#60a5fa' : '#94a3b8'; ?>; text-decoration: none; font-size: 0.78rem; transition: all 0.1s; background: <?= $currentPage === 'reports' ? 'rgba(59,130,246,0.1)' : 'transparent'; ?>; font-weight: <?= $currentPage === 'reports' ? '500' : '400'; ?>;">
                                    <i class="bi bi-graph-up-arrow" style="font-size: 1rem; color: #8b5cf6;"></i> Analytics Reports
                                </a>
                                <!-- Branch Management -->
                                <?php if (\App\Helpers\Permission::has('manage_branches')): ?>
                                    <a class="nav-link <?= $currentPage === 'branches' ? 'active' : '' ?>" href="<?= site_url('/admin/branches') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; border-radius: 8px; color: <?= $currentPage === 'branches' ? '#60a5fa' : '#94a3b8'; ?>; text-decoration: none; font-size: 0.78rem; transition: all 0.1s; background: <?= $currentPage === 'branches' ? 'rgba(59,130,246,0.1)' : 'transparent'; ?>; font-weight: <?= $currentPage === 'branches' ? '500' : '400'; ?>;">
                                        <i class="bi bi-building" style="font-size: 1rem; color: #22c55e;"></i> Branches
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if (\App\Helpers\Permission::has('manage_settings')): ?>
                                <a class="nav-link <?= $currentPage === 'settings' ? 'active' : '' ?>" href="<?= site_url('/admin/settings') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; border-radius: 8px; color: <?= $currentPage === 'settings' ? '#60a5fa' : '#94a3b8'; ?>; text-decoration: none; font-size: 0.78rem; transition: all 0.1s; background: <?= $currentPage === 'settings' ? 'rgba(59,130,246,0.1)' : 'transparent'; ?>; font-weight: <?= $currentPage === 'settings' ? '500' : '400'; ?>;">
                                    <i class="bi bi-gear" style="font-size: 1rem; color: #f59e0b;"></i> System Settings
                                </a>
                            <?php endif; ?>
                            <?php if (\App\Helpers\Permission::has('view_logs')): ?>
                                <a class="nav-link <?= $currentPage === 'logs' ? 'active' : '' ?>" href="<?= site_url('/admin/logs') ?>" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; border-radius: 8px; color: <?= $currentPage === 'logs' ? '#60a5fa' : '#94a3b8'; ?>; text-decoration: none; font-size: 0.78rem; transition: all 0.1s; background: <?= $currentPage === 'logs' ? 'rgba(59,130,246,0.1)' : 'transparent'; ?>; font-weight: <?= $currentPage === 'logs' ? '500' : '400'; ?>;">
                                    <i class="bi bi-journal-text" style="font-size: 1rem; color: #06b6d4;"></i> Activity Logs
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Footer -->
            <div style="margin-top: 1rem; padding-top: 0.8rem; border-top: 1px solid rgba(255,255,255,0.06);">
                <div style="display: flex; align-items: center; gap: 0.6rem; padding: 0.3rem 0.3rem 0.3rem 0; margin-bottom: 0.8rem;">
                    <div style="width: 38px; height: 38px; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 0.75rem; flex-shrink: 0;">
                        <?= substr(esc($user['username'] ?? 'A'), 0, 1) ?>
                    </div>
                    <div style="flex: 1; overflow: hidden;">
                        <div style="font-weight: 600; color: #fff; font-size: 0.8rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= esc($user['username'] ?? 'Admin') ?></div>
                        <div style="display: flex; align-items: center; gap: 0.3rem; font-size: 0.65rem; color: #60a5fa;">
                            <span style="display: inline-block; width: 6px; height: 6px; background: #22c55e; border-radius: 50%;"></span>
                            <?= esc(ucfirst($user['role'] ?? 'admin')) ?>
                        </div>
                    </div>
                </div>
                <a class="btn btn-sm btn-outline-danger w-100" href="<?= site_url('/logout') ?>" style="border-radius: 10px; font-size: 0.78rem; padding: 0.5rem; border-color: rgba(239,68,68,0.3); color: #ef4444; background: rgba(239,68,68,0.05); transition: all 0.15s; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 0.4rem;"
                    data-confirm="Are you sure you want to log out?">
                    <i class="bi bi-box-arrow-left"></i> Sign Out
                </a>
            </div>
        </aside>

        <!-- ========== MAIN CONTENT ========== -->
        <main class="admin-content">
            <!-- Top Bar - Clean & Modern with Dropdowns -->
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
                    <!-- Role Badge -->
                    <span class="badge-clean badge-clean-primary d-none-mobile">
                        <i class="bi bi-shield-check"></i> <?= esc(strtoupper($user['role'] ?? 'ADMIN')) ?>
                    </span>

                    <!-- Quick Actions Dropdown -->
                    <div class="dropdown">
                        <button class="btn-primary-clean dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-plus-circle"></i> Quick Action
                        </button>
                        <ul class="dropdown-menu dropdown-menu-clean">
                            <li><a class="dropdown-item" href="<?= site_url('/admin/patients/create') ?>"><i
                                        class="bi bi-person-plus text-success"></i> New Patient</a></li>
                            <li><a class="dropdown-item" href="<?= site_url('/admin/appointments/create') ?>"><i
                                        class="bi bi-calendar-plus text-primary"></i> New Appointment</a></li>
                            <li><a class="dropdown-item" href="<?= site_url('/admin/ipd/admit') ?>"><i
                                        class="bi bi-hospital text-warning"></i> Admit IPD</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="<?= site_url('/admin/billing/create') ?>"><i
                                        class="bi bi-receipt text-info"></i> Create Bill</a></li>
                            <li><a class="dropdown-item" href="<?= site_url('/admin/inventory/add') ?>"><i
                                        class="bi bi-capsule text-danger"></i> Add Medicine</a></li>
                        </ul>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div class="dropdown">
                        <button class="btn-soft-clean d-flex align-items-center gap-2 dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" style="padding: 0.15rem 0.8rem 0.15rem 0.15rem;">
                            <span class="user-avatar-sm"><?= substr(esc($user['username'] ?? 'A'), 0, 1) ?></span>
                            <span class="d-none d-sm-inline"
                                style="font-weight:500;font-size:0.8rem;"><?= esc($user['username'] ?? 'Admin') ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-clean">
                            <li><a class="dropdown-item" href="<?= site_url('/admin/profile') ?>"><i
                                        class="bi bi-person-circle"></i> My Profile</a></li>
                            <li><a class="dropdown-item" href="<?= site_url('/admin/settings') ?>"><i
                                        class="bi bi-gear"></i> Settings</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="<?= site_url('/logout') ?>"><i
                                        class="bi bi-box-arrow-right"></i> Sign Out</a></li>
                        </ul>
                    </div>

                    <!-- View Site -->
                    <a href="<?= site_url() ?>" class="btn-soft-clean d-none-mobile" target="_blank">
                        <i class="bi bi-globe"></i> View Site
                    </a>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if ($success = \App\Helpers\Session::getFlash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show alert-dismiss-flash mb-4" role="alert"
                    style="border-radius:12px;border-left:4px solid #198754;">
                    <i class="bi bi-check-circle-fill me-2"></i> <?= esc($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error = \App\Helpers\Session::getFlash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show alert-dismiss-flash mb-4" role="alert"
                    style="border-radius:12px;border-left:4px solid #dc3545;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= esc($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($warning = \App\Helpers\Session::getFlash('warning')): ?>
                <div class="alert alert-warning alert-dismissible fade show alert-dismiss-flash mb-4" role="alert"
                    style="border-radius:12px;border-left:4px solid #ffc107;">
                    <i class="bi bi-exclamation-circle-fill me-2"></i> <?= esc($warning) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Page Content starts below -->