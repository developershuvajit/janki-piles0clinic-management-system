<?php
$settings = \App\Models\Cms::getSettings();
?>
<style>
    /* ============================================
       FOOTER - MODERN PROFESSIONAL DESIGN
       ============================================ */
    .jpk-footer {
        background: linear-gradient(180deg, #0f172a 0%, #080d1a 100%);
        color: #cbd5e1;
        padding-top: 3.5rem;
        padding-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .jpk-footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #059669, #0f7b4a, #059669);
        background-size: 200% auto;
        animation: jpk-footer-line 3s ease-in-out infinite;
    }
    @keyframes jpk-footer-line {
        0%, 100% { background-position: 0% center; }
        50% { background-position: 200% center; }
    }

    .jpk-footer .brand-icon {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, #059669, #0f7b4a);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.4rem;
        flex-shrink: 0;
    }
    .jpk-footer .brand-name {
        font-size: 1.1rem;
        font-weight: 800;
        color: #fff;
        letter-spacing: -0.3px;
        line-height: 1.2;
    }
    .jpk-footer .brand-tagline {
        font-size: 0.55rem;
        color: #059669;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
    }
    .jpk-footer .about-text {
        font-size: 0.82rem;
        color: #94a3b8;
        line-height: 1.7;
        margin-bottom: 1.2rem;
    }
    .jpk-footer .emergency-box {
        background: rgba(5,150,105,0.08);
        border: 1px solid rgba(5,150,105,0.15);
        border-radius: 12px;
        padding: 0.8rem 1.2rem;
        transition: all 0.3s ease;
    }
    .jpk-footer .emergency-box:hover {
        background: rgba(5,150,105,0.12);
        border-color: rgba(5,150,105,0.3);
        box-shadow: 0 0 0 3px rgba(5,150,105,0.06);
    }
    .jpk-footer .emergency-box .label {
        font-size: 0.6rem;
        color: #059669;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .jpk-footer .emergency-box .number {
        font-size: 1.3rem;
        font-weight: 800;
        color: #fff;
        text-decoration: none;
        transition: all 0.3s;
        display: block;
    }
    .jpk-footer .emergency-box .number:hover {
        color: #059669;
    }
    .jpk-footer .emergency-box .btn-call {
        background: linear-gradient(135deg, #059669, #0f7b4a);
        color: #fff;
        border: none;
        padding: 0.3rem 1.2rem;
        border-radius: 40px;
        font-size: 0.7rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s;
    }
    .jpk-footer .emergency-box .btn-call:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(5,150,105,0.3);
    }

    .jpk-footer .footer-heading {
        font-size: 0.75rem;
        font-weight: 700;
        color: #fff;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid rgba(255,255,255,0.06);
    }
    .jpk-footer .footer-heading i {
        color: #059669;
        margin-right: 0.4rem;
    }
    .jpk-footer .footer-link {
        color: #94a3b8;
        text-decoration: none;
        font-size: 0.8rem;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }
    .jpk-footer .footer-link:hover {
        color: #059669;
        transform: translateX(4px);
    }
    .jpk-footer .footer-link i {
        font-size: 0.6rem;
        color: #059669;
    }

    .jpk-footer .branch-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .jpk-footer .branch-list li {
        font-size: 0.78rem;
        color: #94a3b8;
        padding: 0.2rem 0;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.3s;
    }
    .jpk-footer .branch-list li:hover {
        color: #e2e8f0;
        transform: translateX(4px);
    }
    .jpk-footer .branch-list li i {
        color: #059669;
        font-size: 0.6rem;
    }

    .jpk-footer .social-icons {
        display: flex;
        gap: 0.4rem;
        margin-top: 0.8rem;
    }
    .jpk-footer .social-icons a {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 1px solid rgba(255,255,255,0.06);
        color: #94a3b8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        transition: all 0.3s;
        text-decoration: none;
    }
    .jpk-footer .social-icons a:hover {
        background: #059669;
        border-color: #059669;
        color: #fff;
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 4px 16px rgba(5,150,105,0.25);
    }

    .jpk-footer .footer-divider {
        border-color: rgba(255,255,255,0.06);
        margin: 1.5rem 0;
    }
    .jpk-footer .copyright {
        font-size: 0.72rem;
        color: #64748b;
    }
    .jpk-footer .footer-bottom-links a {
        font-size: 0.72rem;
        color: #64748b;
        text-decoration: none;
        transition: all 0.3s;
    }
    .jpk-footer .footer-bottom-links a:hover {
        color: #059669;
    }
    .jpk-footer .footer-bottom-links .sep {
        color: rgba(255,255,255,0.06);
    }

    /* ----- Floating WhatsApp ----- */
    .jpk-whatsapp-float {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 9999;
        width: 58px;
        height: 58px;
        border-radius: 50%;
        background: #25d366;
        color: #fff;
        border: none;
        box-shadow: 0 4px 20px rgba(37, 211, 102, 0.35);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        text-decoration: none;
        transition: all 0.3s ease;
        animation: jpk-wa-pulse 2s infinite;
    }
    .jpk-whatsapp-float:hover {
        transform: scale(1.1) translateY(-4px);
        box-shadow: 0 8px 30px rgba(37, 211, 102, 0.45);
        color: #fff;
        animation-play-state: paused;
    }
    @keyframes jpk-wa-pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    @media (max-width: 768px) {
        .jpk-footer { padding-top: 2.5rem; }
        .jpk-footer .emergency-box .number { font-size: 1.1rem; }
        .jpk-footer .brand-section { text-align: center; }
        .jpk-footer .brand-section .brand-icon { margin: 0 auto; }
        .jpk-footer .social-icons { justify-content: center; }
        .jpk-whatsapp-float { width: 50px; height: 50px; font-size: 1.5rem; bottom: 20px; right: 20px; }
    }

    @media (prefers-reduced-motion: reduce){
        .jpk-footer::before, .jpk-whatsapp-float{ animation: none !important; }
    }
</style>

<!-- ============================================
     PRE-FOOTER TRUST STRIP
     ============================================ -->
<section class="py-3 text-white jpk-reveal up" style="background: #0b1120; border-top: 1px solid rgba(255,255,255,0.05); border-bottom: 1px solid rgba(255,255,255,0.05);">
    <div class="container">
        <div class="row g-2 text-center text-md-start align-items-center jpk-stagger">
            <div class="col-md-3">
                <div class="d-flex align-items-center gap-3 justify-content-center justify-content-md-start">
                    <div style="width:36px;height:36px;border-radius:50%;background:rgba(5,150,105,0.15);display:flex;align-items:center;justify-content:center;color:#059669;font-size:1.1rem;">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:0.8rem;color:#fff;">100% Cashless TPA</div>
                        <div style="font-size:0.65rem;color:#94a3b8;">All Major Insurances</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-flex align-items-center gap-3 justify-content-center justify-content-md-start">
                    <div style="width:36px;height:36px;border-radius:50%;background:rgba(5,150,105,0.15);display:flex;align-items:center;justify-content:center;color:#059669;font-size:1.1rem;">
                        <i class="bi bi-lightning-charge"></i>
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:0.8rem;color:#fff;">German 1470nm Laser</div>
                        <div style="font-size:0.65rem;color:#94a3b8;">Painless Daycare Surgery</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-flex align-items-center gap-3 justify-content-center justify-content-md-start">
                    <div style="width:36px;height:36px;border-radius:50%;background:rgba(5,150,105,0.15);display:flex;align-items:center;justify-content:center;color:#059669;font-size:1.1rem;">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:0.8rem;color:#fff;">7 Clinical Branches</div>
                        <div style="font-size:0.65rem;color:#94a3b8;">Uttarakhand &amp; Punjab</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-flex align-items-center gap-3 justify-content-center justify-content-md-start">
                    <div style="width:36px;height:36px;border-radius:50%;background:rgba(5,150,105,0.15);display:flex;align-items:center;justify-content:center;color:#059669;font-size:1.1rem;">
                        <i class="bi bi-person-check"></i>
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:0.8rem;color:#fff;">Female Chaperones</div>
                        <div style="font-size:0.65rem;color:#94a3b8;">100% Privacy &amp; Dignity</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     MAIN FOOTER
     ============================================ -->
<footer class="jpk-footer">
    <div class="container">
        <div class="row g-4 jpk-stagger">
            <!-- Column 1: Brand & About -->
            <div class="col-lg-4 col-md-6">
                <div class="brand-section d-flex align-items-center gap-3 mb-3">
                    <div class="brand-icon"><i class="bi bi-hospital"></i></div>
                    <div>
                        <div class="brand-name">Janki Piles Clinic</div>
                        <div class="brand-tagline">Laser Proctology Center</div>
                    </div>
                </div>
                <p class="about-text">
                    North India's premier multi-branch daycare laser proctology &amp; general surgery institution. Providing scalpel-free, painless, stitchless treatments with same-day discharge.
                </p>
                <div class="emergency-box">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <div class="label"><i class="bi bi-telephone-fill me-1"></i> 24/7 Emergency Line</div>
                            <a href="tel:+919876543210" class="number">+91 98765 43210</a>
                        </div>
                        <a href="tel:+919876543210" class="btn-call">Call Now</a>
                    </div>
                </div>
                <div class="social-icons">
                    <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                    <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                    <a href="https://wa.me/919876543210" target="_blank" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>

            <!-- Column 2: Treatments -->
            <div class="col-lg-3 col-md-6">
                <h6 class="footer-heading"><i class="bi bi-activity"></i> Laser Procedures</h6>
                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0.2rem;">
                    <li><a href="<?= site_url('/treatments/piles-treatment') ?>" class="footer-link"><i class="bi bi-chevron-right"></i> Laser Piles Surgery</a></li>
                    <li><a href="<?= site_url('/treatments/fissure-treatment') ?>" class="footer-link"><i class="bi bi-chevron-right"></i> Laser Fissure Surgery</a></li>
                    <li><a href="<?= site_url('/treatments/fistula-treatment') ?>" class="footer-link"><i class="bi bi-chevron-right"></i> FiLaC Laser Fistula</a></li>
                    <li><a href="<?= site_url('/treatments/pilonidal-sinus-treatment') ?>" class="footer-link"><i class="bi bi-chevron-right"></i> SiLaC Pilonidal Laser</a></li>
                    <li><a href="<?= site_url('/treatments/circumcision') ?>" class="footer-link"><i class="bi bi-chevron-right"></i> ZSR Circumcision</a></li>
                    <li><a href="<?= site_url('/treatments/hernia-surgery') ?>" class="footer-link"><i class="bi bi-chevron-right"></i> Laparoscopic Hernia</a></li>
                    <li><a href="<?= site_url('/treatments/hydrocele-treatment') ?>" class="footer-link"><i class="bi bi-chevron-right"></i> Hydrocele Surgery</a></li>
                </ul>
            </div>

            <!-- Column 3: Branches -->
            <div class="col-lg-2 col-md-6">
                <h6 class="footer-heading"><i class="bi bi-geo-alt-fill"></i> Branch Network</h6>
                <ul class="branch-list">
                    <li><i class="bi bi-pin-map-fill"></i> Dehradun (Main)</li>
                    <li><i class="bi bi-pin-map-fill"></i> Haridwar</li>
                    <li><i class="bi bi-pin-map-fill"></i> Roorkee</li>
                    <li><i class="bi bi-pin-map-fill"></i> Bhaniyawala</li>
                    <li><i class="bi bi-pin-map-fill"></i> Srinagar Garhwal</li>
                    <li><i class="bi bi-pin-map-fill"></i> Haldwani</li>
                    <li><i class="bi bi-pin-map-fill"></i> Mohali (Tricity)</li>
                </ul>
            </div>

            <!-- Column 4: Quick Links -->
            <div class="col-lg-3 col-md-6">
                <h6 class="footer-heading"><i class="bi bi-info-circle-fill"></i> Patient Navigation</h6>
                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0.2rem;">
                    <li><a href="<?= site_url('/about') ?>" class="footer-link"><i class="bi bi-chevron-right"></i> About Our Center</a></li>
                    <li><a href="<?= site_url('/doctors') ?>" class="footer-link"><i class="bi bi-chevron-right"></i> Senior Proctologists</a></li>
                    <li><a href="<?= site_url('/faqs') ?>" class="footer-link"><i class="bi bi-chevron-right"></i> Patient FAQs &amp; Diet</a></li>
                    <li><a href="<?= site_url('/blog') ?>" class="footer-link"><i class="bi bi-chevron-right"></i> Medical Articles</a></li>
                    <li><a href="<?= site_url('/contact') ?>" class="footer-link"><i class="bi bi-chevron-right"></i> Contact &amp; Timings</a></li>
                    <li><a href="<?= site_url('/appointments/book') ?>" class="footer-link"><i class="bi bi-chevron-right"></i> Book Consultation</a></li>
                </ul>
            </div>
        </div>

        <!-- Divider -->
        <hr class="footer-divider">

        <!-- Bottom Bar -->
        <div class="row align-items-center g-2">
            <div class="col-md-7 text-center text-md-start">
                <span class="copyright">&copy; <?= date('Y') ?> Janki Piles Clinic. All Rights Reserved.</span>
            </div>
            <div class="col-md-5 text-center text-md-end">
                <div class="footer-bottom-links d-flex flex-wrap justify-content-center justify-content-md-end gap-2">
                    <a href="<?= site_url('/contact') ?>">Privacy Policy</a>
                    <span class="sep">|</span>
                    <a href="<?= site_url('/contact') ?>">Terms &amp; Conditions</a>
                    <span class="sep">|</span>
                    <a href="<?= site_url('/contact') ?>">Medical Disclaimer</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- ============================================
     FLOATING WHATSAPP BUTTON
     ============================================ -->
<a href="https://wa.me/919876543210" 
   class="jpk-whatsapp-float" 
   target="_blank" 
   aria-label="WhatsApp Doctor Assistance">
    <i class="bi bi-whatsapp"></i>
</a>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- ============================================
     JPK PREMIUM ANIMATION ENGINE (vanilla JS)
     Scroll reveal, stagger, X-parallax, counters,
     navbar compact state, scroll progress, cursor glow.
     Respects prefers-reduced-motion.
     ============================================ -->
<script>
(function(){
    "use strict";
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ---------- Scroll reveal + stagger ---------- */
    var revealEls = document.querySelectorAll('.jpk-reveal, .jpk-stagger, .jpk-trust, .jpk-testimonial-card');
    if (reduceMotion) {
        revealEls.forEach(function(el){ el.classList.add('in-view'); });
    } else if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function(entries){
            entries.forEach(function(entry){
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -60px 0px' });
        revealEls.forEach(function(el){ io.observe(el); });
    } else {
        revealEls.forEach(function(el){ el.classList.add('in-view'); });
    }

    /* ---------- Counter animation ---------- */
    var counters = document.querySelectorAll('[data-counter]');
    function animateCounter(el){
        var target = parseFloat(el.getAttribute('data-target')) || 0;
        var suffix = el.getAttribute('data-suffix') || '';
        var dur = 1400, start = null;
        function step(ts){
            if (!start) start = ts;
            var progress = Math.min((ts - start) / dur, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            var val = Math.floor(eased * target);
            el.textContent = val.toLocaleString('en-IN') + suffix;
            if (progress < 1) requestAnimationFrame(step);
            else el.textContent = target.toLocaleString('en-IN') + suffix;
        }
        requestAnimationFrame(step);
    }
    if (counters.length){
        if (reduceMotion || !('IntersectionObserver' in window)) {
            counters.forEach(function(el){
                el.textContent = (parseFloat(el.getAttribute('data-target'))||0).toLocaleString('en-IN') + (el.getAttribute('data-suffix')||'');
            });
        } else {
            var counterIO = new IntersectionObserver(function(entries){
                entries.forEach(function(entry){
                    if (entry.isIntersecting){
                        animateCounter(entry.target);
                        counterIO.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });
            counters.forEach(function(el){ counterIO.observe(el); });
        }
    }

    /* ---------- Navbar compact-on-scroll ---------- */
    var navbar = document.getElementById('jpkNavbar');
    var progressBar = document.getElementById('jpkScrollProgress');
    function onScroll(){
        if (navbar){
            if (window.scrollY > 40) navbar.classList.add('jpk-scrolled');
            else navbar.classList.remove('jpk-scrolled');
        }
        if (progressBar){
            var doc = document.documentElement;
            var max = (doc.scrollHeight - doc.clientHeight) || 1;
            var pct = (window.scrollY / max) * 100;
            progressBar.style.width = Math.min(pct, 100) + '%';
        }
    }
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });

    /* ---------- Subtle X-axis parallax (desktop only) ---------- */
    var parallaxEls = document.querySelectorAll('.jpk-parallax-x');
    if (!reduceMotion && parallaxEls.length && window.matchMedia('(min-width: 992px)').matches) {
        var ticking = false;
        function updateParallax(){
            var scrollY = window.scrollY;
            parallaxEls.forEach(function(el){
                var speed = parseFloat(el.getAttribute('data-parallax-speed')) || 0.05;
                var x = Math.max(-30, Math.min(30, scrollY * speed * -1));
                el.style.transform = 'translate3d(' + x.toFixed(1) + 'px, 0, 0)';
            });
            ticking = false;
        }
        window.addEventListener('scroll', function(){
            if (!ticking){
                requestAnimationFrame(updateParallax);
                ticking = true;
            }
        }, { passive: true });
        updateParallax();
    }

    /* ---------- Desktop cursor glow ---------- */
    var glow = document.getElementById('jpkCursorGlow');
    if (glow && !reduceMotion && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        var gx = 0, gy = 0, cx = 0, cy = 0, glowActive = false;
        document.addEventListener('mousemove', function(e){
            gx = e.clientX; gy = e.clientY;
            if (!glowActive){ glow.style.opacity = '1'; glowActive = true; }
        });
        document.addEventListener('mouseleave', function(){
            glow.style.opacity = '0'; glowActive = false;
        });
        function raf(){
            cx += (gx - cx) * 0.15;
            cy += (gy - cy) * 0.15;
            glow.style.transform = 'translate(' + cx + 'px,' + cy + 'px) translate(-50%,-50%)';
            requestAnimationFrame(raf);
        }
        requestAnimationFrame(raf);
    }
})();
</script>
</body>
</html>