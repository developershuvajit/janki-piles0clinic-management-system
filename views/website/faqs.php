<?php include VIEWS_PATH . '/layout/public_header.php'; ?>

<style>
    /* ============================================
       FAQS PAGE - CLEAN MODERN DESIGN
       ============================================ */
    
    /* ----- Page Header ----- */
    .jpk-faqs-header {
        background: linear-gradient(145deg, #f8fafc, #ecfdf5);
        padding: 3.5rem 0 2.5rem;
        border-bottom: 1px solid #eef2f6;
        position: relative;
        overflow: hidden;
    }
    .jpk-faqs-header::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(5,150,105,0.04), transparent 70%);
        border-radius: 50%;
    }
    .jpk-faqs-header .container { position: relative; z-index: 1; }
    .jpk-faqs-header .badge {
        background: rgba(5,150,105,0.08);
        color: #059669;
        border: 1px solid rgba(5,150,105,0.1);
        padding: 0.15rem 1rem;
        border-radius: 40px;
        font-size: 0.65rem;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 0.5rem;
    }
    .jpk-faqs-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        color: #0b1a2b;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
    }
    .jpk-faqs-header p {
        font-size: 1.05rem;
        color: #475569;
        max-width: 650px;
        margin: 0 auto;
        line-height: 1.7;
    }

    /* ----- Category Section ----- */
    .jpk-faq-category {
        margin-bottom: 2.5rem;
    }
    .jpk-faq-category .cat-title {
        font-weight: 700;
        color: #0b1a2b;
        font-size: 1.1rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .jpk-faq-category .cat-title i {
        color: #059669;
        font-size: 1.3rem;
    }

    /* ----- Accordion ----- */
    .jpk-accordion {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #eef2f6;
        box-shadow: 0 2px 12px rgba(0,0,0,0.03);
    }
    .jpk-accordion-item {
        background: #fff;
        border-bottom: 1px solid #eef2f6;
    }
    .jpk-accordion-item:last-child {
        border-bottom: none;
    }
    .jpk-accordion-item .q {
        padding: 0.9rem 1.2rem;
        background: #fafcff;
        font-weight: 600;
        color: #0b1a2b;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: none;
        width: 100%;
        text-align: left;
        font-size: 0.92rem;
        transition: all 0.2s;
        border-radius: 0;
    }
    .jpk-accordion-item .q:hover {
        background: #f5f9fc;
    }
    .jpk-accordion-item .q .icon {
        color: #94a3b8;
        font-size: 1rem;
        transition: transform 0.3s;
        flex-shrink: 0;
        margin-left: 0.5rem;
    }
    .jpk-accordion-item .q.active .icon {
        transform: rotate(180deg);
        color: #059669;
    }
    .jpk-accordion-item .a {
        padding: 0 1.2rem 1.2rem;
        color: #475569;
        font-size: 0.88rem;
        line-height: 1.7;
        display: none;
    }
    .jpk-accordion-item .a.open {
        display: block;
        animation: jpk-faq-fade 0.3s ease;
    }
    @keyframes jpk-faq-fade {
        from { opacity: 0; transform: translateY(-6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ----- Bottom CTA ----- */
    .jpk-faq-cta {
        text-align: center;
        background: #f8fafc;
        border-radius: 14px;
        padding: 1.8rem 1.5rem;
        border: 1px solid #eef2f6;
        margin-top: 2rem;
    }
    .jpk-faq-cta h5 {
        font-weight: 700;
        color: #0b1a2b;
        font-size: 1.05rem;
        margin-bottom: 0.3rem;
    }
    .jpk-faq-cta p {
        color: #64748b;
        font-size: 0.9rem;
        margin-bottom: 0.8rem;
    }
    .jpk-faq-cta .btn-whatsapp {
        background: #25d366;
        color: #fff;
        padding: 0.5rem 1.8rem;
        border-radius: 40px;
        font-weight: 600;
        font-size: 0.85rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.25s;
        border: none;
    }
    .jpk-faq-cta .btn-whatsapp:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(37, 211, 102, 0.3);
        color: #fff;
    }

    /* ----- Responsive ----- */
    @media (max-width: 992px) {
        .jpk-faqs-header h1 { font-size: 2rem; }
    }
    @media (max-width: 576px) {
        .jpk-faqs-header { padding: 2rem 0 1.5rem; }
        .jpk-faqs-header h1 { font-size: 1.5rem; }
        .jpk-faqs-header p { font-size: 0.9rem; }
        .jpk-faq-category .cat-title { font-size: 0.95rem; }
        .jpk-accordion-item .q { font-size: 0.82rem; padding: 0.6rem 0.8rem; }
        .jpk-accordion-item .a { font-size: 0.8rem; padding: 0 0.8rem 0.8rem; }
        .jpk-faq-cta { padding: 1rem; }
        .jpk-faq-cta h5 { font-size: 0.9rem; }
        .jpk-faq-cta .btn-whatsapp { font-size: 0.78rem; padding: 0.4rem 1.2rem; }
    }
</style>

<!-- ============================================
     PAGE HEADER
     ============================================ -->
<section class="jpk-faqs-header">
    <div class="container text-center">
        <span class="badge">PATIENT KNOWLEDGE CENTER</span>
        <h1>100 Categorized Patient FAQs</h1>
        <p>Get clear, evidence-based medical answers regarding laser surgery, recovery, pain management, insurance, and diet from our senior proctologists.</p>
    </div>
</section>

<!-- ============================================
     FAQS CATEGORIES
     ============================================ -->
<section class="py-4 bg-white">
    <div class="container" style="max-width: 920px;">

        <?php if (!empty($faqCategories) && is_array($faqCategories)): ?>
            <?php foreach ($faqCategories as $category): ?>
                <div class="jpk-faq-category">
                    <div class="cat-title">
                        <i class="bi <?= $category['icon'] ?? 'bi-question-circle' ?>"></i>
                        <?= esc($category['title']) ?>
                    </div>
                    <div class="jpk-accordion">
                        <?php foreach ($category['items'] as $index => $item): ?>
                            <div class="jpk-accordion-item">
                                <button class="q <?= $index === 0 ? 'active' : '' ?>" onclick="toggleFaq(this)">
                                    <span><?= esc($item['question']) ?></span>
                                    <span class="icon"><i class="bi bi-chevron-down"></i></span>
                                </button>
                                <div class="a <?= $index === 0 ? 'open' : '' ?>">
                                    <?= esc($item['answer']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Default/Static FAQs -->
            <div class="jpk-faq-category">
                <div class="cat-title">
                    <i class="bi bi-shield-check"></i> 1. Laser Surgery Technology &amp; Safety
                </div>
                <div class="jpk-accordion">
                    <div class="jpk-accordion-item">
                        <button class="q active" onclick="toggleFaq(this)">
                            <span>Is laser piles surgery completely painless?</span>
                            <span class="icon"><i class="bi bi-chevron-down"></i></span>
                        </button>
                        <div class="a open">
                            Yes! Laser piles treatment (LHP) shrinks hemorrhoidal nodes internally using 1470nm German laser energy without scalpel cuts or open stitches, reducing pain by 90-95% compared to open surgery.
                        </div>
                    </div>
                    <div class="jpk-accordion-item">
                        <button class="q" onclick="toggleFaq(this)">
                            <span>Will laser surgery damage my anal sphincter muscles?</span>
                            <span class="icon"><i class="bi bi-chevron-down"></i></span>
                        </button>
                        <div class="a">
                            No. Unlike open surgery, laser energy is targeted precisely, preserving 100% of sphincter muscle fibers and eliminating fecal incontinence risks.
                        </div>
                    </div>
                    <div class="jpk-accordion-item">
                        <button class="q" onclick="toggleFaq(this)">
                            <span>How long does the laser procedure take inside the OT?</span>
                            <span class="icon"><i class="bi bi-chevron-down"></i></span>
                        </button>
                        <div class="a">
                            The daycare laser procedure takes approximately 20 to 30 minutes inside our state-of-the-art operation theatre.
                        </div>
                    </div>
                </div>
            </div>

            <div class="jpk-faq-category">
                <div class="cat-title">
                    <i class="bi bi-clock-history"></i> 2. Recovery &amp; Post-Op Care
                </div>
                <div class="jpk-accordion">
                    <div class="jpk-accordion-item">
                        <button class="q active" onclick="toggleFaq(this)">
                            <span>How soon after laser piles surgery can I walk and return to work?</span>
                            <span class="icon"><i class="bi bi-chevron-down"></i></span>
                        </button>
                        <div class="a open">
                            You can walk independently 2 to 3 hours after surgery. Most working professionals resume desk work within 24 to 48 hours.
                        </div>
                    </div>
                    <div class="jpk-accordion-item">
                        <button class="q" onclick="toggleFaq(this)">
                            <span>Do I need daily painful dressing changes?</span>
                            <span class="icon"><i class="bi bi-chevron-down"></i></span>
                        </button>
                        <div class="a">
                            No! Because there are no open scalpel cuts, daily painful dressing changes are completely eliminated.
                        </div>
                    </div>
                </div>
            </div>

            <div class="jpk-faq-category">
                <div class="cat-title">
                    <i class="bi bi-card-checklist"></i> 3. Cashless Insurance &amp; Billing
                </div>
                <div class="jpk-accordion">
                    <div class="jpk-accordion-item">
                        <button class="q active" onclick="toggleFaq(this)">
                            <span>Does health insurance cover daycare laser surgery?</span>
                            <span class="icon"><i class="bi bi-chevron-down"></i></span>
                        </button>
                        <div class="a open">
                            Yes! Laser proctology procedures are medically necessary surgical treatments covered by major health insurance policies under daycare benefits. We offer 100% cashless approvals.
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- ============================================
             BOTTOM CTA
             ============================================ -->
        <div class="jpk-faq-cta">
            <h5>Have a specific question not listed here?</h5>
            <p>Get instant answers from our senior proctologists on WhatsApp</p>
            <a href="https://wa.me/919876543210" class="btn-whatsapp" target="_blank">
                <i class="bi bi-whatsapp"></i> Ask Doctor on WhatsApp
            </a>
        </div>

    </div>
</section>

<script>
function toggleFaq(btn) {
    const answer = btn.nextElementSibling;
    const isOpen = answer.classList.contains('open');
    
    // Close all answers in same accordion
    const parent = btn.closest('.jpk-accordion');
    if (parent) {
        parent.querySelectorAll('.a').forEach(el => el.classList.remove('open'));
        parent.querySelectorAll('.q').forEach(el => el.classList.remove('active'));
    }
    
    if (!isOpen) {
        answer.classList.add('open');
        btn.classList.add('active');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto-open first FAQ in each category
    document.querySelectorAll('.jpk-accordion').forEach(accordion => {
        const firstQ = accordion.querySelector('.q');
        const firstA = accordion.querySelector('.a');
        if (firstQ && firstA && !firstQ.classList.contains('active')) {
            firstQ.classList.add('active');
            firstA.classList.add('open');
        }
    });
});
</script>

<?php include VIEWS_PATH . '/layout/public_footer.php'; ?>