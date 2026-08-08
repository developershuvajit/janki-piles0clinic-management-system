<?php include VIEWS_PATH . '/layout/header.php'; ?>

<!-- 404 Layout -->
<div class="row justify-content-center py-5 text-center">
    <div class="col-md-6 my-5">
        <i class="bi bi-exclamation-triangle text-warning" style="font-size: 5rem;"></i>
        <h1 class="fw-bold display-5 mt-3 text-slate">404 - Page Not Found</h1>
        <p class="lead text-muted mb-4">The requested page link is invalid, has expired, or was moved.</p>
        
        <a href="<?= site_url() ?>" class="btn btn-primary px-4 py-2.5 shadow-sm">
            <i class="bi bi-house-door-fill me-1"></i> Return Home
        </a>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/footer.php'; ?>
