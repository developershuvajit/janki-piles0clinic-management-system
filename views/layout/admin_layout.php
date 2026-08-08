<?php
$user = \App\Helpers\Session::user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Admin Panel') ?> - MedClinic</title>
    
    <!-- Google Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom Style Sheet -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="sidebar d-flex flex-column justify-content-between">
            <div>
                <a class="navbar-brand d-flex align-items-center mb-4 text-white text-decoration-none" href="<?= site_url() ?>">
                    <i class="bi bi-heart-pulse-fill text-success fs-3 me-2"></i>
                    <span>Med</span>Clinic
                </a>
                <hr class="border-secondary">
                
                <div class="text-white-50 small mb-3">ADMIN NAVIGATION</div>
                <nav class="nav flex-column">
                    <a class="nav-link <?= ($viewPath ?? '') === 'admin.dashboard' ? 'active' : '' ?>" href="<?= site_url('/admin/dashboard') ?>">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                    <a class="nav-link <?= ($viewPath ?? '') === 'admin.settings' ? 'active' : '' ?>" href="<?= site_url('/admin/settings') ?>">
                        <i class="bi bi-gear me-2"></i> System Settings
                    </a>
                    <a class="nav-link <?= ($viewPath ?? '') === 'admin.logs' ? 'active' : '' ?>" href="<?= site_url('/admin/logs') ?>">
                        <i class="bi bi-journal-text me-2"></i> Activity Logs
                    </a>
                </nav>
            </div>
            
            <!-- User Footer Status inside Sidebar -->
            <div>
                <hr class="border-secondary">
                <div class="d-flex align-items-center text-white mb-3">
                    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
                        <i class="bi bi-person-fill text-white fs-5"></i>
                    </div>
                    <div>
                        <div class="small fw-semibold text-truncate" style="max-width: 150px;"><?= esc($user['username']) ?></div>
                        <div class="text-muted" style="font-size: 0.75rem;"><?= esc(ucfirst($user['role'])) ?></div>
                    </div>
                </div>
                <a class="btn btn-outline-danger btn-sm w-100" href="<?= site_url('/logout') ?>">
                    <i class="bi bi-box-arrow-left me-1"></i> Logout
                </a>
            </div>
        </aside>

        <!-- Dynamic Content Panel -->
        <main class="admin-content d-flex flex-column justify-content-between">
            <div class="container-fluid">
                <!-- Header Info Bar -->
                <header class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <h1 class="h3 mb-0 text-slate"><?= esc($title ?? 'Dashboard') ?></h1>
                    <div>
                        <span class="badge bg-white text-secondary border me-2 py-2 px-3">Role: <?= esc(strtoupper($user['role'])) ?></span>
                        <a href="<?= site_url() ?>" class="btn btn-sm btn-outline-secondary px-3" target="_blank">
                            <i class="bi bi-globe me-1"></i> View Site
                        </a>
                    </div>
                </header>

                <!-- Nested Flash System Alerts -->
                <?php if ($success = \App\Helpers\Session::getFlash('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show alert-dismiss-flash" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> <?= esc($success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error = \App\Helpers\Session::getFlash('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show alert-dismiss-flash" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= esc($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Inner View Slot Inclusion -->
                <?php include VIEWS_PATH . '/' . str_replace('.', '/', $viewPath) . '.php'; ?>
            </div>
            
            <footer class="mt-5 text-center text-muted small py-3 border-top">
                &copy; <?= date('Y') ?> MedClinic Portal. Secure Core PHP Platform.
            </footer>
        </main>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Application JS -->
    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
