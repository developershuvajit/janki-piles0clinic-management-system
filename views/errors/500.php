<?php include VIEWS_PATH . '/layout/header.php'; ?>

<!-- 500 Layout -->
<div class="row justify-content-center py-5 text-center">
    <div class="col-md-6 my-5">
        <i class="bi bi-x-circle-fill text-danger" style="font-size: 5rem;"></i>
        <h1 class="fw-bold display-5 mt-3 text-slate">500 - Internal Error</h1>
        <p class="lead text-muted mb-4">An unexpected internal server error occurred. The details have been captured and written to the system error logs for support.</p>
        
        <a href="<?= site_url() ?>" class="btn btn-primary px-4 py-2.5 shadow-sm">
            <i class="bi bi-house-door-fill me-1"></i> Return Home
        </a>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/footer.php'; ?>
