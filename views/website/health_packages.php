<?php include VIEWS_PATH . '/layout/public_header.php'; ?>

<!-- Header -->
<section class="py-5 bg-gradient-hero border-bottom">
    <div class="container py-3 text-center">
        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1.5 rounded-pill mb-2 small fw-bold">PREVENTIVE PROCTOLOGY SCREENING</span>
        <h1 class="display-5 fw-extrabold text-slate mb-3">Anorectal Health Checkup Packages</h1>
        <p class="lead text-muted max-width-700 mx-auto">Early diagnosis saves severe pain and cost. Choose from our curated proctology screening packages.</p>
    </div>
</section>

<!-- Packages Grid -->
<section class="py-5 bg-white">
    <div class="container" style="max-width: 1000px;">
        <div class="row g-4">
            <!-- Package 1 -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm glass-card rounded-4 p-4 d-flex flex-column text-center">
                    <span class="badge bg-emerald text-white w-fit mx-auto px-3 py-1 rounded-pill mb-3">BASIC CHECKUP</span>
                    <h3 class="fw-bold text-slate mb-1">₹999</h3>
                    <div class="text-muted small mb-4">Basic Anorectal Checkup</div>
                    <ul class="list-unstyled text-muted small text-start mb-4 flex-grow-1 d-flex flex-column gap-2">
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Senior Proctologist OPD Consult</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Digital Video Anoscopy</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Physical Examination</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Dietary Fiber Counseling</li>
                    </ul>
                    <a href="<?= site_url('/appointments/book') ?>" class="btn btn-outline-success rounded-pill w-100">Book Package</a>
                </div>
            </div>

            <!-- Package 2 -->
            <div class="col-md-4">
                <div class="card h-100 border-2 border-emerald shadow-lg glass-card rounded-4 p-4 d-flex flex-column text-center position-relative">
                    <span class="badge bg-danger text-white w-fit mx-auto px-3 py-1 rounded-pill mb-3">MOST POPULAR</span>
                    <h3 class="fw-bold text-emerald mb-1">₹2,499</h3>
                    <div class="text-muted small mb-4">Comprehensive Laser Package</div>
                    <ul class="list-unstyled text-muted small text-start mb-4 flex-grow-1 d-flex flex-column gap-2">
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Everything in Basic Checkup</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Complete Blood Count (CBC)</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Blood Sugar & Coagulation Test</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Pre-Laser Fitness Evaluation</li>
                    </ul>
                    <a href="<?= site_url('/appointments/book') ?>" class="btn btn-emerald rounded-pill w-100 py-2.5 shadow-sm">Book Package</a>
                </div>
            </div>

            <!-- Package 3 -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm glass-card rounded-4 p-4 d-flex flex-column text-center">
                    <span class="badge bg-emerald text-white w-fit mx-auto px-3 py-1 rounded-pill mb-3">SENIOR CITIZEN</span>
                    <h3 class="fw-bold text-slate mb-1">₹1,999</h3>
                    <div class="text-muted small mb-4">Senior Citizen Health</div>
                    <ul class="list-unstyled text-muted small text-start mb-4 flex-grow-1 d-flex flex-column gap-2">
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Proctologist & General Consult</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Rectal Bleeding Evaluation</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Bowel Habit & Incontinence Check</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Ergonomic Seating Guidance</li>
                    </ul>
                    <a href="<?= site_url('/appointments/book') ?>" class="btn btn-outline-success rounded-pill w-100">Book Package</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include VIEWS_PATH . '/layout/public_footer.php'; ?>
