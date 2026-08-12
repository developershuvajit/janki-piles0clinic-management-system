<?php include VIEWS_PATH . '/layout/public_header.php'; ?>

<!-- Hero Section -->
<section class="jpk-hero-section">
    <div class="jpk-hero-bg"></div>
    <div class="container jpk-hero-container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <div class="jpk-badge-pill">
                    <i class="bi bi-shield-check"></i> North India's Premier German Laser Proctology Center
                </div>
                <h1 class="jpk-hero-title">
                    Say Goodbye to Painful Piles & Fissures with <span class="jpk-text-emerald">German Laser Surgery</span>
                </h1>
                <p class="jpk-hero-subtitle">
                    <strong>100% Painless | Zero Scalpel Cuts | No Stitches | 30-Minute Daycare Surgery | Same-Day Discharge</strong>
                </p>
                <div class="jpk-hero-buttons">
                    <a href="<?= site_url('/appointments/book') ?>" class="jpk-btn-primary">
                        <i class="bi bi-calendar-plus me-2"></i> Book Laser Consultation
                    </a>
                    <a href="tel:+919876543210" class="jpk-btn-outline-danger">
                        <i class="bi bi-telephone-fill me-2"></i> Call Emergency (+91 98765 43210)
                    </a>
                </div>
                <div class="jpk-hero-features">
                    <span><i class="bi bi-check-circle-fill"></i> Cashless TPA Empaneled</span>
                    <span><i class="bi bi-check-circle-fill"></i> Female Chaperones Available</span>
                    <span><i class="bi bi-check-circle-fill"></i> 100% Confidential</span>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="jpk-glass-card">
                    <div class="jpk-card-header">
                        <span class="jpk-badge-danger">QUICK APPOINTMENT</span>
                        <h4>Book Confidential Slot</h4>
                        <p>Consult Senior Proctologists Today</p>
                    </div>
                    <form action="<?= site_url('/appointments/book/otp') ?>" method="POST">
                        <div class="jpk-form-group">
                            <label>Full Patient Name</label>
                            <input type="text" name="patient_name" placeholder="Enter full name" required>
                        </div>
                        <div class="jpk-form-group">
                            <label>Mobile Number</label>
                            <div class="jpk-input-group">
                                <span class="jpk-input-prefix">+91</span>
                                <input type="tel" name="mobile" placeholder="10-digit phone number" pattern="[0-9]{10}" required>
                            </div>
                        </div>
                        <div class="jpk-form-group">
                            <label>Select Preferred Branch</label>
                            <select name="branch">
                                <option value="Dehradun Main Clinic">Dehradun Main Clinic (Rajpur Road)</option>
                                <option value="Haridwar Clinic">Haridwar Clinic (Ranipur More)</option>
                                <option value="Roorkee Clinic">Roorkee Clinic (Civil Lines)</option>
                                <option value="Bhaniyawala Clinic">Bhaniyawala Clinic (Jolly Grant)</option>
                                <option value="Srinagar Garhwal">Srinagar Garhwal Clinic</option>
                                <option value="Haldwani Clinic">Haldwani Clinic (Kumaon)</option>
                                <option value="Mohali Branch">Mohali Branch (Tricity)</option>
                            </select>
                        </div>
                        <button type="submit" class="jpk-btn-submit">
                            <i class="bi bi-arrow-right-circle me-1"></i> Proceed to Choose Slot
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trust Counters -->
<section class="jpk-counters-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <h2 class="jpk-counter-number">15,000+</h2>
                <div class="jpk-counter-label">Successful Laser Surgeries</div>
            </div>
            <div class="col-6 col-md-3">
                <h2 class="jpk-counter-number">7+</h2>
                <div class="jpk-counter-label">Advanced Clinic Branches</div>
            </div>
            <div class="col-6 col-md-3">
                <h2 class="jpk-counter-number">30 Min</h2>
                <div class="jpk-counter-label">Daycare Laser Procedure</div>
            </div>
            <div class="col-6 col-md-3">
                <h2 class="jpk-counter-number">100%</h2>
                <div class="jpk-counter-label">Cashless TPA Coverage</div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose -->
<section class="jpk-why-section">
    <div class="jpk-why-bg-decor"></div>
    <div class="container">
        <div class="jpk-section-header">
            <span class="jpk-section-tag">The Janki Advantage</span>
            <h2>Why Patients Trust Janki Piles Clinic</h2>
            <p>We combine world-class German laser technology with compassionate, patient-first proctology care.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="jpk-feature-card">
                    <div class="jpk-feature-icon">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <h5>Zero Cuts & Stitches</h5>
                    <p>Laser energy shrink hemorrhoid nodes internally without scalpel cuts, eliminating open painful wounds and stitches.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="jpk-feature-card">
                    <div class="jpk-feature-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <h5>Same-Day Discharge</h5>
                    <p>Walk in for daycare surgery in the morning and return home comfortably by afternoon. Resume office within 24-48 hours.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="jpk-feature-card">
                    <div class="jpk-feature-icon">
                        <i class="bi bi-heart-pulse-fill"></i>
                    </div>
                    <h5>Sphincter Preserving</h5>
                    <p>High-precision laser targets only diseased tissue, preserving 100% of anal sphincter control with zero incontinence risk.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Treatments -->
<section class="jpk-treatments-section">
    <div class="container">
        <div class="jpk-section-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div>
                <span class="jpk-section-tag">Our Clinical Specialties</span>
                <h2>Advanced Laser Procedures</h2>
            </div>
            <a href="<?= site_url('/treatments') ?>" class="jpk-btn-outline">
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
                        <div class="jpk-treatment-card">
                            <div class="jpk-treatment-accent"></div>
                            <div class="jpk-treatment-body">
                                <div class="jpk-treatment-top">
                                    <div class="jpk-treatment-icon">
                                        <i class="bi <?= $iconClass ?>"></i>
                                    </div>
                                    <span class="jpk-treatment-badge">
                                        <i class="bi bi-lightning-charge-fill me-1"></i> Daycare Laser
                                    </span>
                                </div>
                                <h5 class="jpk-treatment-title"><?= esc($tr['title']) ?></h5>
                                <p class="jpk-treatment-desc"><?= esc($tr['content']) ?></p>
                                <div class="jpk-treatment-highlights">
                                    <span><i class="bi bi-check-circle-fill"></i> Zero Cuts</span>
                                    <span><i class="bi bi-check-circle-fill"></i> 24h Recovery</span>
                                    <span><i class="bi bi-check-circle-fill"></i> Cashless TPA</span>
                                </div>
                                <div class="jpk-treatment-actions">
                                    <a href="<?= site_url('/treatments/' . $tr['slug']) ?>" class="jpk-btn-sm-primary">
                                        Learn Procedure <i class="bi bi-arrow-right-short"></i>
                                    </a>
                                    <a href="<?= site_url('/appointments/book') ?>" class="jpk-btn-sm-outline">
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

<!-- Insurance Banner -->
<section class="jpk-insurance-section">
    <div class="jpk-insurance-pattern"></div>
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="jpk-insurance-badge">100% CASHLESS TPA SUPPORT</span>
                <h2>Covered Under Major Health Insurance Policies</h2>
                <p>We are empaneled with Star Health, HDFC ERGO, ICICI Lombard, Niva Bupa, Care Health, and all major PSU insurers. Free pre-authorization assistance.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?= site_url('/insurance') ?>" class="jpk-btn-light">
                    <i class="bi bi-shield-check me-1"></i> Check Cashless Eligibility
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Reviews -->
<section class="jpk-reviews-section">
    <div class="container">
        <div class="jpk-section-header">
            <span class="jpk-section-tag">Patient Feedback</span>
            <h2>Real Recovery Stories</h2>
        </div>
        <div class="row g-4 justify-content-center">
            <?php foreach ($testimonials as $t): ?>
                <div class="col-md-6">
                    <div class="jpk-review-card">
                        <div class="jpk-review-stars">
                            <?php for($i=1; $i<=5; $i++): ?>
                                <i class="bi bi-star<?= $i <= $t['rating'] ? '-fill' : '' ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="jpk-review-text">"<?= esc($t['review_text']) ?>"</p>
                        <div class="jpk-review-author">- <?= esc($t['author']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- FAQs -->
<section class="jpk-faq-section">
    <div class="container" style="max-width:850px;">
        <div class="jpk-section-header">
            <span class="jpk-section-tag">Got Questions?</span>
            <h2>Frequently Asked Questions</h2>
        </div>
        <div class="jpk-accordion" id="faqAccordion">
            <?php foreach ($faqs as $index => $faq): ?>
                <div class="jpk-accordion-item">
                    <div class="jpk-accordion-header" id="heading-<?= $index ?>">
                        <button class="jpk-accordion-btn <?= $index > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $index ?>">
                            <i class="bi bi-question-circle"></i> <?= esc($faq['q']) ?>
                        </button>
                    </div>
                    <div id="collapse-<?= $index ?>" class="jpk-accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" data-bs-parent="#faqAccordion">
                        <div class="jpk-accordion-body">
                            <?= esc($faq['a']) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?= site_url('/faqs') ?>" class="jpk-btn-outline">
                Read All 100 Patient FAQs <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</section>

<style>
/* ===== JANKI PILES CLINIC - UNIQUE STYLES ===== */
/* All classes prefixed with 'jpk-' to avoid conflicts */

/* ----- reset & base ----- */
.jpk-text-emerald { color: #0f7b6e; }

/* ----- hero section ----- */
.jpk-hero-section {
    background: linear-gradient(145deg, #f1f8f6 0%, #e6f2ef 100%);
    position: relative;
    overflow: hidden;
    padding: 4rem 0;
    min-height: 520px;
}
.jpk-hero-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('https://images.unsplash.com/photo-1584362917165-526a96857948?w=1200&q=80');
    background-size: cover;
    background-position: center 20%;
    opacity: 0.06;
    animation: jpk-zoom 18s infinite alternate ease-in-out;
    z-index: 0;
}
@keyframes jpk-zoom {
    0% { transform: scale(1); }
    100% { transform: scale(1.08); }
}
.jpk-hero-container {
    position: relative;
    z-index: 3;
}

.jpk-badge-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(15,123,110,0.1);
    color: #0f7b6e;
    border: 1px solid rgba(15,123,110,0.25);
    padding: 0.4rem 1.2rem;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 700;
    margin-bottom: 1.2rem;
    animation: jpk-pulse 2.2s infinite;
}
@keyframes jpk-pulse {
    0% { opacity: 0.8; transform: scale(1); }
    50% { opacity: 1; transform: scale(1.03); }
    100% { opacity: 0.8; transform: scale(1); }
}

.jpk-hero-title {
    font-size: 3rem;
    font-weight: 800;
    color: #1a2634;
    line-height: 1.15;
    letter-spacing: -0.5px;
    margin-bottom: 1rem;
}
.jpk-hero-subtitle {
    font-size: 1.25rem;
    color: #6c757d;
    margin-bottom: 1.5rem;
}
.jpk-hero-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.jpk-hero-features {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    color: #6c757d;
    font-size: 0.9rem;
    border-top: 1px solid #dee2e6;
    padding-top: 1rem;
}
.jpk-hero-features span i {
    color: #0f7b6e;
    margin-right: 0.3rem;
}

/* ----- buttons ----- */
.jpk-btn-primary {
    background: #0f7b6e;
    border: 1px solid #0f7b6e;
    color: #fff;
    padding: 0.8rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.25s;
    display: inline-flex;
    align-items: center;
}
.jpk-btn-primary:hover {
    background: #0d6b5f;
    border-color: #0d6b5f;
    color: #fff;
    transform: scale(1.02);
    box-shadow: 0 12px 24px -10px rgba(15,123,110,0.3);
}

.jpk-btn-outline-danger {
    border: 1px solid #dc3545;
    color: #dc3545;
    padding: 0.8rem 2rem;
    border-radius: 50px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.25s;
    display: inline-flex;
    align-items: center;
    background: transparent;
}
.jpk-btn-outline-danger:hover {
    background: #dc3545;
    color: #fff;
}

.jpk-btn-outline {
    border: 1px solid #0f7b6e;
    color: #0f7b6e;
    padding: 0.6rem 1.8rem;
    border-radius: 50px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.25s;
    display: inline-flex;
    align-items: center;
    background: transparent;
}
.jpk-btn-outline:hover {
    background: #0f7b6e;
    color: #fff;
}

.jpk-btn-light {
    background: #fff;
    color: #0f7b6e;
    padding: 0.8rem 2rem;
    border-radius: 50px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.25s;
    display: inline-flex;
    align-items: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.jpk-btn-light:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
}

.jpk-btn-sm-primary {
    background: #0f7b6e;
    color: #fff;
    padding: 0.4rem 1.2rem;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.25s;
    display: inline-flex;
    align-items: center;
}
.jpk-btn-sm-primary:hover {
    background: #0d6b5f;
    color: #fff;
}

.jpk-btn-sm-outline {
    border: 1px solid #0f7b6e;
    color: #0f7b6e;
    padding: 0.4rem 1.2rem;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.25s;
    display: inline-flex;
    align-items: center;
    background: transparent;
}
.jpk-btn-sm-outline:hover {
    background: #0f7b6e;
    color: #fff;
}

.jpk-btn-submit {
    background: #0f7b6e;
    border: none;
    color: #fff;
    padding: 0.9rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1.1rem;
    width: 100%;
    transition: all 0.25s;
}
.jpk-btn-submit:hover {
    background: #0d6b5f;
    transform: scale(1.01);
}

/* ----- glass card ----- */
.jpk-glass-card {
    background: rgba(255,255,255,0.82);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.5);
    border-radius: 1.25rem;
    padding: 1.8rem;
    box-shadow: 0 20px 40px -12px rgba(0,0,0,0.06);
    transition: all 0.3s;
}
.jpk-glass-card:hover {
    box-shadow: 0 28px 48px -16px rgba(0,0,0,0.1);
}
.jpk-card-header {
    text-align: center;
    margin-bottom: 1.5rem;
}
.jpk-card-header .jpk-badge-danger {
    background: #dc3545;
    color: #fff;
    padding: 0.3rem 1.2rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 700;
    display: inline-block;
    margin-bottom: 0.5rem;
}
.jpk-card-header h4 {
    font-weight: 700;
    color: #1a2634;
    margin-bottom: 0.2rem;
}
.jpk-card-header p {
    color: #6c757d;
    font-size: 0.9rem;
}

.jpk-form-group {
    margin-bottom: 1.2rem;
}
.jpk-form-group label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #1a2634;
    display: block;
    margin-bottom: 0.3rem;
}
.jpk-form-group input,
.jpk-form-group select {
    width: 100%;
    padding: 0.7rem 1rem;
    border: 1px solid #dee2e6;
    border-radius: 0.75rem;
    font-size: 1rem;
    transition: border 0.2s;
    background: #fff;
}
.jpk-form-group input:focus,
.jpk-form-group select:focus {
    border-color: #0f7b6e;
    outline: none;
    box-shadow: 0 0 0 3px rgba(15,123,110,0.15);
}
.jpk-input-group {
    display: flex;
    align-items: stretch;
}
.jpk-input-prefix {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-right: none;
    padding: 0.7rem 1rem;
    border-radius: 0.75rem 0 0 0.75rem;
    font-weight: 700;
    color: #6c757d;
    display: flex;
    align-items: center;
}
.jpk-input-group input {
    border-radius: 0 0.75rem 0.75rem 0;
    border-left: none;
}

/* ----- counters ----- */
.jpk-counters-section {
    padding: 3rem 0;
    background: #fff;
    border-bottom: 1px solid #e9edf2;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
}
.jpk-counter-number {
    font-size: 2.8rem;
    font-weight: 800;
    color: #0f7b6e;
    margin-bottom: 0.2rem;
}
.jpk-counter-label {
    color: #6c757d;
    font-size: 0.9rem;
    font-weight: 600;
}

/* ----- section headers ----- */
.jpk-section-header {
    text-align: center;
    max-width: 700px;
    margin: 0 auto 3rem;
}
.jpk-section-header .jpk-section-tag {
    color: #0f7b6e;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-size: 0.85rem;
    display: block;
}
.jpk-section-header h2 {
    font-weight: 800;
    color: #1a2634;
    font-size: 2.5rem;
    margin: 0.3rem 0;
}
.jpk-section-header p {
    color: #6c757d;
}

/* ----- why section ----- */
.jpk-why-section {
    padding: 4rem 0;
    background: #f8faf9;
    position: relative;
    overflow: hidden;
}
.jpk-why-bg-decor {
    position: absolute;
    top: -30%;
    right: -5%;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(15,123,110,0.03) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}
.jpk-feature-card {
    background: #fff;
    border-radius: 1.25rem;
    padding: 2rem;
    height: 100%;
    box-shadow: 0 8px 24px rgba(0,0,0,0.04);
    transition: all 0.3s;
    border: none;
}
.jpk-feature-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 40px -14px rgba(0,0,0,0.08);
}
.jpk-feature-icon {
    width: 58px;
    height: 58px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 16px;
    background: rgba(15,123,110,0.08);
    color: #0f7b6e;
    font-size: 1.8rem;
    margin-bottom: 1rem;
}
.jpk-feature-card h5 {
    font-weight: 700;
    color: #1a2634;
    margin-bottom: 0.5rem;
}
.jpk-feature-card p {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 0;
    line-height: 1.6;
}

/* ----- treatments ----- */
.jpk-treatments-section {
    padding: 4rem 0;
    background: #fff;
    border-top: 1px solid #e9edf2;
}
.jpk-treatment-card {
    background: #fff;
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0,0,0,0.04);
    transition: all 0.3s;
    height: 100%;
    position: relative;
    border: none;
}
.jpk-treatment-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 24px 48px -16px rgba(0,0,0,0.1);
}
.jpk-treatment-accent {
    height: 4px;
    background: linear-gradient(90deg, #0f7b6e, #3aa395);
    width: 100%;
    position: absolute;
    top: 0;
    left: 0;
}
.jpk-treatment-body {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    height: 100%;
}
.jpk-treatment-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}
.jpk-treatment-icon {
    width: 48px;
    height: 48px;
    background: rgba(15,123,110,0.06);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    color: #0f7b6e;
}
.jpk-treatment-badge {
    background: rgba(15,123,110,0.08);
    color: #0f7b6e;
    border: 1px solid rgba(15,123,110,0.15);
    padding: 0.3rem 1rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 700;
}
.jpk-treatment-title {
    font-weight: 800;
    color: #1a2634;
    font-size: 1.15rem;
    margin-bottom: 0.5rem;
}
.jpk-treatment-desc {
    color: #6c757d;
    font-size: 0.9rem;
    line-height: 1.6;
    flex-grow: 1;
    margin-bottom: 1rem;
}
.jpk-treatment-highlights {
    display: flex;
    flex-wrap: wrap;
    gap: 0.8rem;
    background: #f6f8fa;
    padding: 0.6rem 1rem;
    border-radius: 0.75rem;
    margin-bottom: 1.2rem;
    font-size: 0.8rem;
    color: #6c757d;
}
.jpk-treatment-highlights span i {
    color: #0f7b6e;
    margin-right: 0.3rem;
}
.jpk-treatment-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 0.8rem;
    border-top: 1px solid #e9edf2;
}

/* ----- insurance ----- */
.jpk-insurance-section {
    padding: 4rem 0;
    background: #0f7b6e;
    color: #fff;
    position: relative;
    overflow: hidden;
}
.jpk-insurance-pattern {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="%23ffffff" fill-opacity="0.04"%3E%3Cpath d="M30 0 L60 30 L30 60 L0 30 Z"/%3E%3C/g%3E%3C/svg%3E');
    pointer-events: none;
}
.jpk-insurance-section .container {
    position: relative;
    z-index: 2;
}
.jpk-insurance-badge {
    background: #fff;
    color: #1a2634;
    padding: 0.3rem 1.2rem;
    border-radius: 50px;
    font-weight: 700;
    font-size: 0.8rem;
    display: inline-block;
    margin-bottom: 0.8rem;
}
.jpk-insurance-section h2 {
    font-weight: 800;
    font-size: 2.5rem;
    margin-bottom: 0.8rem;
}
.jpk-insurance-section p {
    font-size: 1.2rem;
    opacity: 0.9;
    margin-bottom: 0;
}

/* ----- reviews ----- */
.jpk-reviews-section {
    padding: 4rem 0;
    background: #fff;
    border-top: 1px solid #e9edf2;
}
.jpk-review-card {
    background: #f8faf9;
    border-radius: 1.25rem;
    padding: 1.8rem;
    height: 100%;
    transition: all 0.3s;
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.02);
}
.jpk-review-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px -12px rgba(0,0,0,0.06);
}
.jpk-review-stars {
    color: #f59e0b;
    font-size: 1.1rem;
    margin-bottom: 0.8rem;
}
.jpk-review-text {
    color: #1a2634;
    font-size: 1.05rem;
    font-style: italic;
    margin-bottom: 0.8rem;
}
.jpk-review-author {
    color: #0f7b6e;
    font-weight: 700;
    text-align: right;
}

/* ----- faq ----- */
.jpk-faq-section {
    padding: 4rem 0;
    background: #f8faf9;
    border-top: 1px solid #e9edf2;
}
.jpk-accordion {
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0,0,0,0.03);
}
.jpk-accordion-item {
    background: #fff;
    border-bottom: 1px solid #e9edf2;
}
.jpk-accordion-item:last-child {
    border-bottom: none;
}
.jpk-accordion-btn {
    width: 100%;
    padding: 1.2rem 1.5rem;
    background: transparent;
    border: none;
    font-weight: 700;
    color: #1a2634;
    text-align: left;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    transition: all 0.2s;
    cursor: pointer;
}
.jpk-accordion-btn i {
    color: #0f7b6e;
    font-size: 1.2rem;
}
.jpk-accordion-btn:hover {
    background: #f8faf9;
}
.jpk-accordion-btn.collapsed .bi-question-circle {
    color: #0f7b6e;
}
.jpk-accordion-collapse {
    transition: all 0.25s;
}
.jpk-accordion-body {
    padding: 0 1.5rem 1.5rem 1.5rem;
    color: #6c757d;
    line-height: 1.7;
}

/* ----- responsive tweaks ----- */
@media (max-width: 768px) {
    .jpk-hero-title { font-size: 2.2rem; }
    .jpk-hero-section { padding: 2.5rem 0; min-height: auto; }
    .jpk-counter-number { font-size: 2.2rem; }
    .jpk-section-header h2 { font-size: 2rem; }
    .jpk-insurance-section h2 { font-size: 2rem; }
    .jpk-why-section, .jpk-treatments-section, .jpk-insurance-section,
    .jpk-reviews-section, .jpk-faq-section { padding: 2.5rem 0; }
}
</style>

<?php include VIEWS_PATH . '/layout/public_footer.php'; ?>