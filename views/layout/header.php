<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Clinic Management System') ?></title>
    
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

    <!-- Site Navbar -->
    

    <!-- Main Container -->
    <main class="flex-grow-1 py-4">
        <div class="container">
            
            <!-- Global Dismissible Alerts -->
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
