<?php include VIEWS_PATH . '/layout/public_header.php'; ?>

<!-- Breadcrumb Header -->
<section class="py-4 bg-gradient-hero border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="<?= site_url('/') ?>" class="text-emerald text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('/treatments') ?>" class="text-emerald text-decoration-none">Treatments</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= esc($treatment['title']) ?></li>
            </ol>
        </nav>
        <h1 class="display-6 fw-extrabold text-slate mb-2"><?= esc($treatment['title']) ?></h1>
        <div class="d-flex flex-wrap gap-3 align-items-center text-muted small">
            <div><i class="bi bi-shield-check text-success me-1"></i> German 1470nm Laser Technology</div>
            <div><i class="bi bi-clock-history text-success me-1"></i> 30-Minute Daycare Surgery</div>
            <div><i class="bi bi-hospital text-success me-1"></i> Same-Day Discharge</div>
        </div>
    </div>
</section>

<!-- Clinical Content -->
<section class="py-5 bg-white">
    <div class="container" style="max-width: 1000px;">
        <div class="row g-5">
            <div class="col-lg-8">
                <!-- Overview -->
                <div class="mb-5">
                    <h3 class="fw-bold text-slate mb-3">Clinical Overview & Procedure Description</h3>
                    <p class="text-muted leading-relaxed fs-6 mb-4"><?= esc($treatment['content']) ?></p>
                    
                    <div class="p-4 rounded-4 bg-light border-start border-4 border-emerald mb-4">
                        <h5 class="fw-bold text-slate mb-2"><i class="bi bi-star-fill text-warning me-2"></i> Why Choose Laser Surgery at Janki Piles Clinic?</h5>
                        <ul class="list-unstyled text-muted small mb-0 d-flex flex-column gap-2">
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> <strong>100% Painless:</strong> 90-95% reduction in post-operative pain compared to open surgery.</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> <strong>Zero Cuts or Stitches:</strong> Laser energy photo-coagulates diseased tissue without open scalpel cuts.</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> <strong>Preserves Sphincter Control:</strong> Preserves anal sphincter muscles to eliminate fecal incontinence risk.</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> <strong>Same-Day Discharge:</strong> Discharged within 4 hours; resume normal work in 24-48 hours.</li>
                        </ul>
                    </div>
                </div>

                <!-- Step-by-Step Patient Recovery Timeline -->
                <div class="mb-5">
                    <h4 class="fw-bold text-slate mb-3">Patient Recovery Timeline</h4>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 text-center h-100 border">
                                <span class="badge bg-emerald mb-2">DAY 1</span>
                                <h6 class="fw-bold text-slate">Surgery & Discharge</h6>
                                <p class="text-muted small mb-0">Walk home 3-4 hours after daycare procedure. Light sitting and walking encouraged.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 text-center h-100 border">
                                <span class="badge bg-emerald mb-2">DAY 2</span>
                                <h6 class="fw-bold text-slate">Painless Bowel Movement</h6>
                                <p class="text-muted small mb-0">Smooth stool evacuation with softeners. Warm sitz bath twice daily.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 text-center h-100 border">
                                <span class="badge bg-emerald mb-2">DAY 3 - 5</span>
                                <h6 class="fw-bold text-slate">Return to Work</h6>
                                <p class="text-muted small mb-0">Full return to office desk duties, walking, and daily routines.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Post-Op Diet Chart -->
                <div class="mb-5 p-4 rounded-4 bg-light">
                    <h4 class="fw-bold text-slate mb-3"><i class="bi bi-egg-fried text-emerald me-2"></i> Recommended Post-Op Diet & Lifestyle Guidelines</h4>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-success"><i class="bi bi-check-lg me-1"></i> Foods to Eat (High Fiber):</h6>
                            <ul class="text-muted small">
                                <li>Whole wheat roti with bran, oats, brown rice</li>
                                <li>Fresh fruits (papaya, apples, prunes, oranges)</li>
                                <li>Green leafy vegetables, dal, salads</li>
                                <li>Drink 3 to 4 liters of water daily</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-danger"><i class="bi bi-x-lg me-1"></i> Foods to Avoid:</h6>
                            <ul class="text-muted small">
                                <li>Spicy foods, red chilli, hot sauces</li>
                                <li>Refined flour (maida), junk fast foods</li>
                                <li>Alcohol, excessive tea or coffee</li>
                                <li>Avoid sitting on commode > 5 minutes</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Sidebar -->
            <div class="col-lg-4">
                <div class="glass-card p-4 shadow-lg sticky-top" style="top: 100px;">
                    <span class="badge bg-danger px-3 py-1.5 rounded-pill mb-2">CASHLESS TPA AVAILABLE</span>
                    <h4 class="fw-bold text-slate mb-1">Book Consultation</h4>
                    <p class="text-muted small mb-3">Get 100% Confidential Guidance</p>
                    
                    <a href="<?= site_url('/appointments/book') ?>" class="btn btn-emerald btn-lg w-100 rounded-pill py-3 mb-3 shadow-sm">
                        <i class="bi bi-calendar-check me-1"></i> Book OPD Slot Now
                    </a>

                    <a href="tel:+919876543210" class="btn btn-outline-danger btn-lg w-100 rounded-pill py-3 mb-3 fw-bold">
                        <i class="bi bi-telephone-fill me-1"></i> Call +91 98765 43210
                    </a>

                    <a href="https://wa.me/919876543210" class="btn btn-success btn-lg w-100 rounded-pill py-3 fw-bold" target="_blank" style="background-color: #25d366; border: none;">
                        <i class="bi bi-whatsapp me-1"></i> WhatsApp Doctor
                    </a>

                    <hr class="my-4">

                    <div class="small text-muted">
                        <div class="mb-2"><i class="bi bi-shield-check text-success me-1"></i> 100% Insurance Coverage</div>
                        <div class="mb-2"><i class="bi bi-clock me-1"></i> Mon - Sat (9:00 AM - 8:00 PM)</div>
                        <div><i class="bi bi-geo-alt me-1"></i> 7 Branches in Uttrakhand & Punjab</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include VIEWS_PATH . '/layout/public_footer.php'; ?>
