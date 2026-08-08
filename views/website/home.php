<?php include VIEWS_PATH . '/layout/public_header.php'; ?>

<!-- Hero Section -->
<section class="bg-gradient-hero py-5">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <div class="d-inline-flex align-items-center gap-2 bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1.5 rounded-pill mb-3 small fw-bold">
                    <i class="bi bi-shield-check fs-6"></i> North India's Premier German Laser Proctology Center
                </div>
                <h1 class="display-5 fw-extrabold text-slate mb-3" style="line-height: 1.15; letter-spacing: -0.5px;">
                    Say Goodbye to Painful Piles & Fissures with <span class="text-emerald">German Laser Surgery</span>
                </h1>
                <p class="lead text-secondary mb-4">
                    <strong>100% Painless | Zero Scalpel Cuts | No Stitches | 30-Minute Daycare Surgery | Same-Day Discharge</strong>
                </p>
                <div class="d-flex flex-wrap gap-3 mb-4">
                    <a href="<?= site_url('/appointments/book') ?>" class="btn btn-emerald btn-lg px-4 py-3 shadow-sm rounded-pill">
                        <i class="bi bi-calendar-plus me-2"></i> Book Laser Consultation
                    </a>
                    <a href="tel:+919876543210" class="btn btn-outline-danger btn-lg px-4 py-3 rounded-pill fw-bold">
                        <i class="bi bi-telephone-fill me-2"></i> Call Emergency (+91 98765 43210)
                    </a>
                </div>
                <div class="d-flex align-items-center gap-4 text-muted small border-top pt-3">
                    <div><i class="bi bi-check-circle-fill text-success me-1"></i> Cashless TPA Empaneled</div>
                    <div><i class="bi bi-check-circle-fill text-success me-1"></i> Female Chaperones Available</div>
                    <div><i class="bi bi-check-circle-fill text-success me-1"></i> 100% Confidential</div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="glass-card p-4 shadow-lg border-0">
                    <div class="text-center mb-3">
                        <span class="badge bg-danger px-3 py-2 rounded-pill mb-2">QUICK APPOINTMENT</span>
                        <h4 class="fw-bold text-slate mb-1">Book Confidential Slot</h4>
                        <p class="text-muted small">Consult Senior Proctologists Today</p>
                    </div>
                    <form action="<?= site_url('/appointments/book/otp') ?>" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-slate">Full Patient Name</label>
                            <input type="text" name="patient_name" class="form-control form-control-lg rounded-3 fs-6" placeholder="Enter full name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-slate">Mobile Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted fw-bold">+91</span>
                                <input type="tel" name="mobile" class="form-control form-control-lg rounded-end-3 fs-6" placeholder="10-digit phone number" pattern="[0-9]{10}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-slate">Select Preferred Branch</label>
                            <select name="branch" class="form-select form-select-lg rounded-3 fs-6">
                                <option value="Dehradun Main Clinic">Dehradun Main Clinic (Rajpur Road)</option>
                                <option value="Haridwar Clinic">Haridwar Clinic (Ranipur More)</option>
                                <option value="Roorkee Clinic">Roorkee Clinic (Civil Lines)</option>
                                <option value="Bhaniyawala Clinic">Bhaniyawala Clinic (Jolly Grant)</option>
                                <option value="Srinagar Garhwal">Srinagar Garhwal Clinic</option>
                                <option value="Haldwani Clinic">Haldwani Clinic (Kumaon)</option>
                                <option value="Mohali Branch">Mohali Branch (Tricity)</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-emerald btn-lg w-100 rounded-pill py-3 shadow-sm">
                            <i class="bi bi-arrow-right-circle me-1"></i> Proceed to Choose Slot
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trust Counters Bar -->
<section class="py-4 bg-white border-bottom shadow-sm">
    <div class="container text-center">
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <h2 class="display-6 fw-extrabold text-emerald mb-0">15,000+</h2>
                <div class="text-muted small fw-semibold">Successful Laser Surgeries</div>
            </div>
            <div class="col-6 col-md-3">
                <h2 class="display-6 fw-extrabold text-emerald mb-0">7+</h2>
                <div class="text-muted small fw-semibold">Advanced Clinic Branches</div>
            </div>
            <div class="col-6 col-md-3">
                <h2 class="display-6 fw-extrabold text-emerald mb-0">30 Min</h2>
                <div class="text-muted small fw-semibold">Daycare Laser Procedure</div>
            </div>
            <div class="col-6 col-md-3">
                <h2 class="display-6 fw-extrabold text-emerald mb-0">100%</h2>
                <div class="text-muted small fw-semibold">Cashless TPA Coverage</div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Janki Piles Clinic -->
<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="text-center max-width-700 mx-auto mb-5">
            <h6 class="text-emerald fw-bold text-uppercase tracking-wider">The Janki Advantage</h6>
            <h2 class="fw-extrabold text-slate display-6">Why Patients Trust Janki Piles Clinic</h2>
            <p class="text-muted">We combine world-class German laser technology with compassionate, patient-first proctology care.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4 glass-card">
                    <div class="feature-icon-box bg-success bg-opacity-10 text-success mb-3">
                        <i class="bi bi-shield-lock-fill fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-slate mb-2">Zero Cuts & Stitches</h5>
                    <p class="text-muted small mb-0">Laser energy shrink hemorrhoid nodes internally without scalpel cuts, eliminating open painful wounds and stitches.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4 glass-card">
                    <div class="feature-icon-box bg-success bg-opacity-10 text-success mb-3">
                        <i class="bi bi-clock-history fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-slate mb-2">Same-Day Discharge</h5>
                    <p class="text-muted small mb-0">Walk in for daycare surgery in the morning and return home comfortably by afternoon. Resume office within 24-48 hours.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4 glass-card">
                    <div class="feature-icon-box bg-success bg-opacity-10 text-success mb-3">
                        <i class="bi bi-heart-pulse-fill fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-slate mb-2">Sphincter Preserving</h5>
                    <p class="text-muted small mb-0">High-precision laser targets only diseased tissue, preserving 100% of anal sphincter control with zero incontinence risk.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Treatments -->
<section class="py-5 bg-white border-top">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5">
            <div>
                <h6 class="text-emerald fw-bold text-uppercase">Our Clinical Specialties</h6>
                <h2 class="fw-extrabold text-slate display-6">Advanced Laser Procedures</h2>
            </div>
            <a href="<?= site_url('/treatments') ?>" class="btn btn-outline-success rounded-pill px-4 mt-3 mt-md-0">
                View All Treatments <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php if (empty($treatments)): ?>
                <div class="text-center py-5 text-muted col-12">No treatments available currently.</div>
            <?php else: ?>
                <?php 
                $iconMap = [
                    'piles-treatment' => 'bi-activity',
                    'fissure-treatment' => 'bi-lightning-charge-fill',
                    'fistula-treatment' => 'bi-shield-plus',
                    'pilonidal-sinus-treatment' => 'bi-bandaid-fill',
                    'circumcision' => 'bi-scissors',
                    'hydrocele-treatment' => 'bi-droplet-fill',
                    'hernia-surgery' => 'bi-capsule-fill',
                    'constipation-treatment' => 'bi-clipboard2-pulse-fill'
                ];
                foreach (array_slice($treatments, 0, 6) as $tr): 
                    $iconClass = $iconMap[$tr['slug']] ?? 'bi-heart-pulse-fill';
                ?>
                    <div class="col-md-4">
                        <div class="card h-100 treatment-card border-0 shadow-sm glass-card rounded-4 overflow-hidden position-relative">
                            <div class="treatment-card-top-accent"></div>
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="treatment-icon-box">
                                        <i class="bi <?= $iconClass ?>"></i>
                                    </div>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1.5 rounded-pill small fw-bold">
                                        <i class="bi bi-lightning-charge-fill me-1"></i> Daycare Laser
                                    </span>
                                </div>

                                <h5 class="card-title fw-extrabold text-slate mb-2" style="font-size: 1.15rem; line-height: 1.3;">
                                    <?= esc($tr['title']) ?>
                                </h5>

                                <p class="card-text text-muted small mb-4 flex-grow-1 leading-relaxed">
                                    <?= esc($tr['content']) ?>
                                </p>

                                <div class="treatment-card-highlights mb-4 p-2.5 rounded-3 bg-slate-50 border border-slate-100">
                                    <div class="d-flex flex-wrap gap-2 text-muted small" style="font-size: 0.78rem;">
                                        <span><i class="bi bi-check-circle-fill text-success me-1"></i> Zero Cuts</span>
                                        <span><i class="bi bi-check-circle-fill text-success me-1"></i> 24h Recovery</span>
                                        <span><i class="bi bi-check-circle-fill text-success me-1"></i> Cashless TPA</span>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between pt-3 border-top border-slate-100 mt-auto">
                                    <a href="<?= site_url('/treatments/' . $tr['slug']) ?>" class="btn btn-emerald btn-sm px-3.5 rounded-pill shadow-sm">
                                        Learn Procedure <i class="bi bi-arrow-right-short fs-6"></i>
                                    </a>
                                    <a href="<?= site_url('/appointments/book') ?>" class="btn btn-outline-success btn-sm px-3 rounded-pill fw-bold">
                                        Book Slot
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Cashless Insurance Banner -->
<section class="py-5 bg-emerald text-white">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="badge bg-white text-dark px-3 py-1.5 rounded-pill mb-2 fw-bold">100% CASHLESS TPA SUPPORT</span>
                <h2 class="fw-extrabold display-6 mb-2">Covered Under Major Health Insurance Policies</h2>
                <p class="lead mb-0 opacity-90">We are empaneled with Star Health, HDFC ERGO, ICICI Lombard, Niva Bupa, Care Health, and all major PSU insurers. Free pre-authorization assistance.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?= site_url('/insurance') ?>" class="btn btn-light btn-lg px-4 py-3 rounded-pill fw-bold text-success shadow">
                    <i class="bi bi-shield-check me-1"></i> Check Cashless Eligibility
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Verified Reviews -->
<section class="py-5 bg-white border-top">
    <div class="container py-3">
        <div class="text-center max-width-700 mx-auto mb-5">
            <h6 class="text-emerald fw-bold text-uppercase">Patient Feedback</h6>
            <h2 class="fw-extrabold text-slate display-6">Real Recovery Stories</h2>
        </div>

        <div class="row g-4 justify-content-center">
            <?php foreach ($testimonials as $t): ?>
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm p-4 bg-light rounded-4">
                        <div class="d-flex text-warning mb-3">
                            <?php for($i=1; $i<=5; $i++): ?>
                                <i class="bi bi-star<?= $i <= $t['rating'] ? '-fill' : '' ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="card-text text-slate fs-6 italic">"<?= esc($t['review_text']) ?>"</p>
                        <hr class="border-secondary border-opacity-25 my-3">
                        <div class="fw-bold text-emerald text-end">- <?= esc($t['author']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Homepage FAQs Section -->
<section class="py-5 bg-light border-top">
    <div class="container" style="max-width: 850px;">
        <div class="text-center mb-5">
            <h6 class="text-emerald fw-bold text-uppercase">Got Questions?</h6>
            <h2 class="fw-extrabold text-slate display-6">Frequently Asked Questions</h2>
        </div>

        <div class="accordion border-0 shadow-sm rounded-4 overflow-hidden" id="faqAccordion">
            <?php foreach ($faqs as $index => $faq): ?>
                <div class="accordion-item border-0 border-bottom">
                    <h2 class="accordion-header" id="heading-<?= $index ?>">
                        <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?> fw-bold text-slate py-3.5" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $index ?>">
                            <i class="bi bi-question-circle text-emerald me-2"></i> <?= esc($faq['q']) ?>
                        </button>
                    </h2>
                    <div id="collapse-<?= $index ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted leading-relaxed py-3.5">
                            <?= esc($faq['a']) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-4">
            <a href="<?= site_url('/faqs') ?>" class="btn btn-outline-success rounded-pill px-4 fw-bold">
                Read All 100 Patient FAQs <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</section>

<?php include VIEWS_PATH . '/layout/public_footer.php'; ?>
