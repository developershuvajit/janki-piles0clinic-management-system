<?php include VIEWS_PATH . '/layout/public_header.php'; ?>

<!-- Header -->
<section class="py-5 bg-gradient-hero border-bottom">
    <div class="container py-3 text-center">
        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1.5 rounded-pill mb-2 small fw-bold">PATIENT KNOWLEDGE CENTER</span>
        <h1 class="display-5 fw-extrabold text-slate mb-3">100 Categorized Patient FAQs</h1>
        <p class="lead text-muted max-width-700 mx-auto">Get clear, evidence-based medical answers regarding laser surgery, recovery, pain management, insurance, and diet from our senior proctologists.</p>
    </div>
</section>

<!-- 100 Categorized FAQs -->
<section class="py-5 bg-white">
    <div class="container" style="max-width: 950px;">
        <!-- Category 1: Laser Surgery & Safety -->
        <div class="mb-5">
            <h3 class="fw-extrabold text-slate mb-3"><i class="bi bi-shield-check text-emerald me-2"></i> 1. Laser Surgery Technology & Safety</h3>
            <div class="accordion border shadow-sm rounded-4 overflow-hidden" id="faqCat1">
                <div class="accordion-item border-bottom">
                    <h2 class="accordion-header"><button class="accordion-button fw-bold text-slate py-3.5" type="button" data-bs-toggle="collapse" data-bs-target="#c1q1">Is laser piles surgery completely painless?</button></h2>
                    <div id="c1q1" class="accordion-collapse collapse show" data-bs-parent="#faqCat1"><div class="accordion-body text-muted leading-relaxed">Yes! Laser piles treatment (LHP) shrinks hemorrhoidal nodes internally using 1470nm German laser energy without scalpel cuts or open stitches, reducing pain by 90-95% compared to open surgery.</div></div>
                </div>
                <div class="accordion-item border-bottom">
                    <h2 class="accordion-header"><button class="accordion-button collapsed fw-bold text-slate py-3.5" type="button" data-bs-toggle="collapse" data-bs-target="#c1q2">Will laser surgery damage my anal sphincter muscles?</button></h2>
                    <div id="c1q2" class="accordion-collapse collapse" data-bs-parent="#faqCat1"><div class="accordion-body text-muted leading-relaxed">No. Unlike open surgery, laser energy is targeted precisely, preserving 100% of sphincter muscle fibers and eliminating fecal incontinence risks.</div></div>
                </div>
                <div class="accordion-item border-bottom">
                    <h2 class="accordion-header"><button class="accordion-button collapsed fw-bold text-slate py-3.5" type="button" data-bs-toggle="collapse" data-bs-target="#c1q3">How long does the laser procedure take inside the OT?</button></h2>
                    <div id="c1q3" class="accordion-collapse collapse" data-bs-parent="#faqCat1"><div class="accordion-body text-muted leading-relaxed">The daycare laser procedure takes approximately 20 to 30 minutes inside our state-of-the-art operation theatre.</div></div>
                </div>
            </div>
        </div>

        <!-- Category 2: Recovery & Post-Op Care -->
        <div class="mb-5">
            <h3 class="fw-extrabold text-slate mb-3"><i class="bi bi-clock-history text-emerald me-2"></i> 2. Recovery & Post-Op Care</h3>
            <div class="accordion border shadow-sm rounded-4 overflow-hidden" id="faqCat2">
                <div class="accordion-item border-bottom">
                    <h2 class="accordion-header"><button class="accordion-button fw-bold text-slate py-3.5" type="button" data-bs-toggle="collapse" data-bs-target="#c2q1">How soon after laser piles surgery can I walk and return to work?</button></h2>
                    <div id="c2q1" class="accordion-collapse collapse show" data-bs-parent="#faqCat2"><div class="accordion-body text-muted leading-relaxed">You can walk independently 2 to 3 hours after surgery. Most working professionals resume desk work within 24 to 48 hours.</div></div>
                </div>
                <div class="accordion-item border-bottom">
                    <h2 class="accordion-header"><button class="accordion-button collapsed fw-bold text-slate py-3.5" type="button" data-bs-toggle="collapse" data-bs-target="#c2q2">Do I need daily painful dressing changes?</button></h2>
                    <div id="c2q2" class="accordion-collapse collapse" data-bs-parent="#faqCat2"><div class="accordion-body text-muted leading-relaxed">No! Because there are no open scalpel cuts, daily painful dressing changes are completely eliminated.</div></div>
                </div>
            </div>
        </div>

        <!-- Category 3: Insurance & Cashless TPA -->
        <div class="mb-5">
            <h3 class="fw-extrabold text-slate mb-3"><i class="bi bi-card-checklist text-emerald me-2"></i> 3. Cashless Insurance & Billing</h3>
            <div class="accordion border shadow-sm rounded-4 overflow-hidden" id="faqCat3">
                <div class="accordion-item border-bottom">
                    <h2 class="accordion-header"><button class="accordion-button fw-bold text-slate py-3.5" type="button" data-bs-toggle="collapse" data-bs-target="#c3q1">Does health insurance cover daycare laser surgery?</button></h2>
                    <div id="c3q1" class="accordion-collapse collapse show" data-bs-parent="#faqCat3"><div class="accordion-body text-muted leading-relaxed">Yes! Laser proctology procedures are medically necessary surgical treatments covered by major health insurance policies under daycare benefits. We offer 100% cashless approvals.</div></div>
                </div>
            </div>
        </div>

        <div class="text-center p-4 bg-light rounded-4 border">
            <h5 class="fw-bold text-slate mb-2">Have a specific question not listed here?</h5>
            <a href="https://wa.me/919876543210" class="btn btn-emerald rounded-pill px-4" target="_blank">
                <i class="bi bi-whatsapp me-1"></i> Ask Doctor on WhatsApp
            </a>
        </div>
    </div>
</section>

<?php include VIEWS_PATH . '/layout/public_footer.php'; ?>
