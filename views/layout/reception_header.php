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
        /* ===== HEADER STYLES - MATCHING ADMIN ===== */
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
            background: #e6f7ef;
            color: #0b6e44;
            border-color: #b8e0cf;
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

        /* ===== SIDEBAR ===== */
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
            border-right: 1px solid rgba(255,255,255,0.06);
            flex-shrink: 0;
        }
        .sidebar-reception .sidebar-scroll {
            flex: 1;
            overflow-y: auto;
            padding-right: 2px;
        }
        .sidebar-reception .sidebar-scroll::-webkit-scrollbar {
            width: 3px;
        }
        .sidebar-reception .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-reception .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.15);
            border-radius: 10px;
        }
        .sidebar-reception .sidebar-footer {
            flex-shrink: 0;
            padding-top: 0.8rem;
            border-top: 1px solid rgba(255,255,255,0.06);
            margin-top: 0.5rem;
        }
        .sidebar-reception .nav-link {
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
        .sidebar-reception .nav-link:hover {
            background: rgba(59,130,246,0.08);
            color: #e2e8f0;
        }
        .sidebar-reception .nav-link.active {
            background: rgba(59,130,246,0.12);
            color: #60a5fa;
            font-weight: 500;
        }
        .sidebar-reception .nav-link i {
            font-size: 1rem;
            width: 1.2rem;
            text-align: center;
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

        @media (max-width: 768px) {
            .admin-topbar-clean {
                padding: 0.6rem 1rem;
            }
            .admin-topbar-clean .topbar-actions .d-none-mobile {
                display: none;
            }
            .sidebar-reception {
                width: 100%;
                height: auto;
                min-height: auto;
                position: relative;
                overflow: visible;
            }
            .sidebar-reception .sidebar-scroll {
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

<!-- Page Loading Overlay -->
<div id="page-loader">
    <div class="loader-ring"></div>
</div>

<div class="admin-wrapper">
    <!-- ========== RECEPTION SIDEBAR ========== -->
    <aside class="sidebar-reception">
        <div class="sidebar-scroll">
            <!-- Brand -->
            <a class="sidebar-brand mb-1" href="<?= site_url('/reception') ?>" style="display:flex;align-items:center;gap:0.8rem;text-decoration:none;margin-bottom:1.2rem;padding:0.2rem 0.3rem;">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,#2563eb,#1d4ed8);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:#fff;flex-shrink:0;">
                    <i class="bi bi-person-workspace"></i>
                </div>
                <div>
                    <span style="font-size:1.2rem;font-weight:700;color:#fff;letter-spacing:-0.3px;">Reception<span style="color:#3b82f6;">Desk</span></span>
                    <div style="font-size:0.6rem;color:#94a3b8;letter-spacing:0.5px;text-transform:uppercase;margin-top:-2px;">OPD Operations Module</div>
                </div>
            </a>

            <!-- Branch Badge -->
            <div style="padding:0.6rem 0.8rem;border-radius:10px;background:rgba(59,130,246,0.12);border:1px solid rgba(59,130,246,0.2);margin-bottom:1rem;">
                <div style="font-size:0.6rem;color:#60a5fa;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Assigned Branch:</div>
                <div style="font-weight:600;color:#fff;font-size:0.78rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    <i class="bi bi-geo-alt-fill me-1" style="color:#3b82f6;"></i> <?= esc($branchName) ?>
                </div>
            </div>

            <hr style="border-color:rgba(255,255,255,0.06);margin:0.8rem 0 1rem 0;">

            <?php
            $currentPage = $activePage ?? '';
            $isOpdActive = in_array($currentPage, ['reception_dashboard', 'reception_queue', 'walk_in'], true);
            $isPatientActive = in_array($currentPage, ['patients', 'patients_create', 'reception_followups'], true);
            $isIpdActive = in_array($currentPage, ['reception_ipd', 'reception_ipd_admit', 'reception_ipd_beds'], true);
            $isBillingActive = in_array($currentPage, ['billing', 'discharge', 'medicine_issue', 'medicines_stock'], true);
            $isDeskActive = in_array($currentPage, ['reception_leads', 'reception_communication', 'reception_attendance'], true);
            $isAccountActive = in_array($currentPage, ['reception_reports', 'reception_profile'], true);
            ?>

            <!-- 1. OPD Operations -->
            <div style="margin-bottom:0.3rem;">
                <button style="width:100%;background:<?= $isOpdActive ? 'rgba(59,130,246,0.15)' : 'transparent'; ?>;border:none;color:<?= $isOpdActive ? '#60a5fa' : '#94a3b8'; ?>;padding:0.6rem 0.8rem;border-radius:10px;display:flex;align-items:center;justify-content:space-between;font-size:0.78rem;font-weight:600;cursor:pointer;transition:all 0.15s;" 
                        type="button" data-bs-toggle="collapse" data-bs-target="#rec-opd" aria-expanded="<?= $isOpdActive ? 'true' : 'false' ?>">
                    <span><i class="bi bi-speedometer2" style="color:#22c55e;margin-right:0.6rem;"></i> OPD Operations</span>
                    <i class="bi bi-chevron-down" style="font-size:0.7rem;transition:transform 0.2s;transform:<?= $isOpdActive ? 'rotate(180deg)' : 'rotate(0)'; ?>;"></i>
                </button>
                <div class="collapse <?= $isOpdActive ? 'show' : '' ?>" id="rec-opd" style="padding:0.2rem 0.5rem 0.2rem 0;">
                    <div style="display:flex;flex-direction:column;gap:0.1rem;">
                        <a class="nav-link <?= $currentPage === 'reception_dashboard' ? 'active' : '' ?>" href="<?= site_url('/reception') ?>"><i class="bi bi-grid-1x2"></i> Desk Dashboard</a>
                        <a class="nav-link <?= $currentPage === 'reception_queue' ? 'active' : '' ?>" href="<?= site_url('/reception/queues') ?>"><i class="bi bi-list-ol"></i> Token Queue & Appointments</a>
                        <a class="nav-link <?= $currentPage === 'walk_in' ? 'active' : '' ?>" href="<?= site_url('/reception/walk-in') ?>"><i class="bi bi-person-walking"></i> Register Walk-In</a>
                    </div>
                </div>
            </div>

            <!-- 2. Patient Management -->
            <div style="margin-bottom:0.3rem;">
                <button style="width:100%;background:<?= $isPatientActive ? 'rgba(59,130,246,0.15)' : 'transparent'; ?>;border:none;color:<?= $isPatientActive ? '#60a5fa' : '#94a3b8'; ?>;padding:0.6rem 0.8rem;border-radius:10px;display:flex;align-items:center;justify-content:space-between;font-size:0.78rem;font-weight:600;cursor:pointer;transition:all 0.15s;" 
                        type="button" data-bs-toggle="collapse" data-bs-target="#rec-patients" aria-expanded="<?= $isPatientActive ? 'true' : 'false' ?>">
                    <span><i class="bi bi-person-lines-fill" style="color:#06b6d4;margin-right:0.6rem;"></i> Patient Management</span>
                    <i class="bi bi-chevron-down" style="font-size:0.7rem;transition:transform 0.2s;transform:<?= $isPatientActive ? 'rotate(180deg)' : 'rotate(0)'; ?>;"></i>
                </button>
                <div class="collapse <?= $isPatientActive ? 'show' : '' ?>" id="rec-patients" style="padding:0.2rem 0.5rem 0.2rem 0;">
                    <div style="display:flex;flex-direction:column;gap:0.1rem;">
                        <a class="nav-link <?= $currentPage === 'patients' ? 'active' : '' ?>" href="<?= site_url('/reception/patients') ?>"><i class="bi bi-folder2-open"></i> Patient Directory</a>
                        <a class="nav-link <?= $currentPage === 'patients_create' ? 'active' : '' ?>" href="<?= site_url('/reception/patients/create') ?>"><i class="bi bi-person-plus-fill"></i> Register New Patient</a>
                        <a class="nav-link <?= $currentPage === 'reception_followups' ? 'active' : '' ?>" href="<?= site_url('/reception/followups') ?>"><i class="bi bi-calendar2-check-fill"></i> Follow-up Tracker</a>
                    </div>
                </div>
            </div>

            <!-- 3. IPD & Admissions -->
            <div style="margin-bottom:0.3rem;">
                <button style="width:100%;background:<?= $isIpdActive ? 'rgba(59,130,246,0.15)' : 'transparent'; ?>;border:none;color:<?= $isIpdActive ? '#60a5fa' : '#94a3b8'; ?>;padding:0.6rem 0.8rem;border-radius:10px;display:flex;align-items:center;justify-content:space-between;font-size:0.78rem;font-weight:600;cursor:pointer;transition:all 0.15s;" 
                        type="button" data-bs-toggle="collapse" data-bs-target="#rec-ipd" aria-expanded="<?= $isIpdActive ? 'true' : 'false' ?>">
                    <span><i class="bi bi-hospital-fill" style="color:#3b82f6;margin-right:0.6rem;"></i> IPD & Ward Admissions</span>
                    <i class="bi bi-chevron-down" style="font-size:0.7rem;transition:transform 0.2s;transform:<?= $isIpdActive ? 'rotate(180deg)' : 'rotate(0)'; ?>;"></i>
                </button>
                <div class="collapse <?= $isIpdActive ? 'show' : '' ?>" id="rec-ipd" style="padding:0.2rem 0.5rem 0.2rem 0;">
                    <div style="display:flex;flex-direction:column;gap:0.1rem;">
                        <a class="nav-link <?= $currentPage === 'reception_ipd' ? 'active' : '' ?>" href="<?= site_url('/reception/ipd') ?>"><i class="bi bi-building-fill-add"></i> Inpatient Admissions</a>
                        <a class="nav-link <?= $currentPage === 'reception_ipd_admit' ? 'active' : '' ?>" href="<?= site_url('/reception/ipd/admit') ?>"><i class="bi bi-journal-plus"></i> Patient Bed Admission</a>
                        <a class="nav-link <?= $currentPage === 'reception_ipd_beds' ? 'active' : '' ?>" href="<?= site_url('/reception/ipd/beds') ?>"><i class="bi bi-diagram-3-fill"></i> Bed / Room Allocation</a>
                    </div>
                </div>
            </div>

            <!-- 4. Billing & Pharmacy -->
            <div style="margin-bottom:0.3rem;">
                <button style="width:100%;background:<?= $isBillingActive ? 'rgba(59,130,246,0.15)' : 'transparent'; ?>;border:none;color:<?= $isBillingActive ? '#60a5fa' : '#94a3b8'; ?>;padding:0.6rem 0.8rem;border-radius:10px;display:flex;align-items:center;justify-content:space-between;font-size:0.78rem;font-weight:600;cursor:pointer;transition:all 0.15s;" 
                        type="button" data-bs-toggle="collapse" data-bs-target="#rec-billing" aria-expanded="<?= $isBillingActive ? 'true' : 'false' ?>">
                    <span><i class="bi bi-receipt" style="color:#f59e0b;margin-right:0.6rem;"></i> Billing & Pharmacy</span>
                    <i class="bi bi-chevron-down" style="font-size:0.7rem;transition:transform 0.2s;transform:<?= $isBillingActive ? 'rotate(180deg)' : 'rotate(0)'; ?>;"></i>
                </button>
                <div class="collapse <?= $isBillingActive ? 'show' : '' ?>" id="rec-billing" style="padding:0.2rem 0.5rem 0.2rem 0;">
                    <div style="display:flex;flex-direction:column;gap:0.1rem;">
                        <a class="nav-link <?= $currentPage === 'billing' ? 'active' : '' ?>" href="<?= site_url('/reception/billing') ?>"><i class="bi bi-cash-register"></i> Cashier Billing & Receipts</a>
                        <a class="nav-link <?= $currentPage === 'discharge' ? 'active' : '' ?>" href="<?= site_url('/reception/discharge') ?>"><i class="bi bi-box-arrow-right"></i> Discharge Checkout</a>
                        <a class="nav-link <?= $currentPage === 'medicine_issue' ? 'active' : '' ?>" href="<?= site_url('/reception/medicine-issue') ?>"><i class="bi bi-capsule"></i> Issue Prescribed Meds</a>
                        <a class="nav-link <?= $currentPage === 'medicines_stock' ? 'active' : '' ?>" href="<?= site_url('/reception/medicines') ?>"><i class="bi bi-prescription2"></i> Medicine Stock View</a>
                    </div>
                </div>
            </div>

            <!-- 5. CRM Leads & Desk -->
            <div style="margin-bottom:0.3rem;">
                <button style="width:100%;background:<?= $isDeskActive ? 'rgba(59,130,246,0.15)' : 'transparent'; ?>;border:none;color:<?= $isDeskActive ? '#60a5fa' : '#94a3b8'; ?>;padding:0.6rem 0.8rem;border-radius:10px;display:flex;align-items:center;justify-content:space-between;font-size:0.78rem;font-weight:600;cursor:pointer;transition:all 0.15s;" 
                        type="button" data-bs-toggle="collapse" data-bs-target="#rec-desk" aria-expanded="<?= $isDeskActive ? 'true' : 'false' ?>">
                    <span><i class="bi bi-funnel-fill" style="color:#a855f7;margin-right:0.6rem;"></i> CRM Leads & Desk</span>
                    <i class="bi bi-chevron-down" style="font-size:0.7rem;transition:transform 0.2s;transform:<?= $isDeskActive ? 'rotate(180deg)' : 'rotate(0)'; ?>;"></i>
                </button>
                <div class="collapse <?= $isDeskActive ? 'show' : '' ?>" id="rec-desk" style="padding:0.2rem 0.5rem 0.2rem 0;">
                    <div style="display:flex;flex-direction:column;gap:0.1rem;">
                        <a class="nav-link <?= $currentPage === 'reception_leads' ? 'active' : '' ?>" href="<?= site_url('/reception/leads') ?>"><i class="bi bi-funnel"></i> Lead Management CRM</a>
                        <a class="nav-link <?= $currentPage === 'reception_communication' ? 'active' : '' ?>" href="<?= site_url('/reception/communication') ?>"><i class="bi bi-whatsapp"></i> Communication Center</a>
                        <a class="nav-link <?= $currentPage === 'reception_attendance' ? 'active' : '' ?>" href="<?= site_url('/reception/attendance') ?>"><i class="bi bi-clock-history"></i> Staff Attendance Roster</a>
                    </div>
                </div>
            </div>

            <!-- 6. Reports & Account -->
            <div style="margin-bottom:0.3rem;">
                <button style="width:100%;background:<?= $isAccountActive ? 'rgba(59,130,246,0.15)' : 'transparent'; ?>;border:none;color:<?= $isAccountActive ? '#60a5fa' : '#94a3b8'; ?>;padding:0.6rem 0.8rem;border-radius:10px;display:flex;align-items:center;justify-content:space-between;font-size:0.78rem;font-weight:600;cursor:pointer;transition:all 0.15s;" 
                        type="button" data-bs-toggle="collapse" data-bs-target="#rec-account" aria-expanded="<?= $isAccountActive ? 'true' : 'false' ?>">
                    <span><i class="bi bi-pie-chart-fill" style="color:#6b7a8f;margin-right:0.6rem;"></i> Reports & Account</span>
                    <i class="bi bi-chevron-down" style="font-size:0.7rem;transition:transform 0.2s;transform:<?= $isAccountActive ? 'rotate(180deg)' : 'rotate(0)'; ?>;"></i>
                </button>
                <div class="collapse <?= $isAccountActive ? 'show' : '' ?>" id="rec-account" style="padding:0.2rem 0.5rem 0.2rem 0;">
                    <div style="display:flex;flex-direction:column;gap:0.1rem;">
                        <a class="nav-link <?= $currentPage === 'reception_reports' ? 'active' : '' ?>" href="<?= site_url('/reception/reports') ?>"><i class="bi bi-graph-up"></i> Daily Branch Reports</a>
                        <a class="nav-link <?= $currentPage === 'reception_profile' ? 'active' : '' ?>" href="<?= site_url('/reception/profile') ?>"><i class="bi bi-person-gear"></i> My Profile & Security</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <div style="display:flex;align-items:center;gap:0.6rem;padding:0.3rem 0.3rem 0.3rem 0;margin-bottom:0.8rem;">
                <div style="width:38px;height:38px;background:linear-gradient(135deg,#3b82f6,#2563eb);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.75rem;flex-shrink:0;">
                    <?= substr(esc($user['username'] ?? 'R'), 0, 1) ?>
                </div>
                <div style="flex:1;overflow:hidden;">
                    <div style="font-weight:600;color:#fff;font-size:0.8rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= esc($user['username'] ?? 'Receptionist') ?></div>
                    <div style="display:flex;align-items:center;gap:0.3rem;font-size:0.65rem;color:#60a5fa;">
                        <span style="display:inline-block;width:6px;height:6px;background:#22c55e;border-radius:50%;"></span>
                        Reception Staff
                    </div>
                </div>
            </div>
            <a class="btn btn-sm btn-outline-danger w-100" href="<?= site_url('/reception/logout') ?>" style="border-radius:10px;font-size:0.78rem;padding:0.5rem;border-color:rgba(239,68,68,0.3);color:#ef4444;background:rgba(239,68,68,0.05);transition:all 0.15s;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:0.4rem;" 
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

            <div class="topbar-actions">
                <!-- Role Badge -->
                <span class="badge-clean badge-clean-primary d-none-mobile">
                    <i class="bi bi-shield-check"></i> <?= esc(strtoupper($user['role'] ?? 'RECEPTION')) ?>
                </span>

                <!-- Branch Badge -->
                <span class="badge-clean badge-clean-success d-none-mobile">
                    <i class="bi bi-building"></i> <?= esc($branchName) ?>
                </span>

                <!-- Quick Actions Dropdown -->
                <div class="dropdown">
                    <button class="btn-primary-clean dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-plus-circle"></i> Quick Action
                    </button>
                    <ul class="dropdown-menu dropdown-menu-clean">
                        <li><a class="dropdown-item" href="<?= site_url('/reception/patients/create') ?>"><i class="bi bi-person-plus text-success"></i> New Patient</a></li>
                        <li><a class="dropdown-item" href="<?= site_url('/reception/walk-in') ?>"><i class="bi bi-person-walking text-primary"></i> Walk-in OPD</a></li>
                        <li><a class="dropdown-item" href="<?= site_url('/reception/ipd/admit') ?>"><i class="bi bi-hospital text-warning"></i> Admit IPD</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= site_url('/reception/billing') ?>"><i class="bi bi-receipt text-info"></i> Create Bill</a></li>
                        <li><a class="dropdown-item" href="<?= site_url('/reception/medicine-issue') ?>"><i class="bi bi-capsule text-danger"></i> Issue Medicine</a></li>
                    </ul>
                </div>

                <!-- User Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn-soft-clean d-flex align-items-center gap-2 dropdown-toggle" type="button" data-bs-toggle="dropdown" style="padding:0.15rem 0.8rem 0.15rem 0.15rem;">
                        <span class="user-avatar-sm"><?= substr(esc($user['username'] ?? 'R'), 0, 1) ?></span>
                        <span class="d-none d-sm-inline" style="font-weight:500;font-size:0.8rem;"><?= esc($user['username'] ?? 'Reception') ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-clean">
                        <li><a class="dropdown-item" href="<?= site_url('/reception/profile') ?>"><i class="bi bi-person-circle"></i> My Profile</a></li>
                        <li><a class="dropdown-item" href="<?= site_url('/reception/reports') ?>"><i class="bi bi-graph-up"></i> Reports</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= site_url('/reception/logout') ?>"><i class="bi bi-box-arrow-right"></i> Sign Out</a></li>
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
        <div class="alert alert-danger alert-dismissible fade show alert-dismiss-flash mb-4" role="alert" style="border-radius:12px;border-left:4px solid #dc3545;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= esc($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if ($success = \App\Helpers\Session::getFlash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show alert-dismiss-flash mb-4" role="alert" style="border-radius:12px;border-left:4px solid #198754;">
            <i class="bi bi-check-circle-fill me-2"></i> <?= esc($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if ($warning = \App\Helpers\Session::getFlash('warning')): ?>
        <div class="alert alert-warning alert-dismissible fade show alert-dismiss-flash mb-4" role="alert" style="border-radius:12px;border-left:4px solid #ffc107;">
            <i class="bi bi-exclamation-circle-fill me-2"></i> <?= esc($warning) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Page Content starts below -->
        <div class="admin-content-inner">