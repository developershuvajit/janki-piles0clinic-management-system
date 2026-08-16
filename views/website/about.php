<?php include VIEWS_PATH . '/layout/public_header.php'; ?>

<style>
    /* ============================================
       ABOUT PAGE - CLEAN MODERN DESIGN
       ============================================ */
    
    /* ----- Page Header ----- */
    .jpk-about-header {
        background: linear-gradient(145deg, #f8fafc, #ecfdf5);
        padding: 3.5rem 0 2.5rem;
        border-bottom: 1px solid #eef2f6;
        position: relative;
        overflow: hidden;
    }
    .jpk-about-header::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(5,150,105,0.04), transparent 70%);
        border-radius: 50%;
    }
    .jpk-about-header .container { position: relative; z-index: 1; }
    .jpk-about-header .badge {
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
    .jpk-about-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        color: #0b1a2b;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
    }
    .jpk-about-header p {
        font-size: 1.05rem;
        color: #475569;
        max-width: 650px;
        margin: 0 auto;
        line-height: 1.7;
    }

    /* ----- Story Section ----- */
    .jpk-story {
        padding: 3.5rem 0;
        background: #fff;
    }
    .jpk-story .tag {
        color: #059669;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.7rem;
        display: block;
        margin-bottom: 0.2rem;
    }
    .jpk-story h2 {
        font-size: 2.2rem;
        font-weight: 800;
        color: #0b1a2b;
        letter-spacing: -0.5px;
        margin-bottom: 1rem;
    }
    .jpk-story .text {
        color: #475569;
        font-size: 0.95rem;
        line-height: 1.8;
        margin-bottom: 1rem;
    }
    .jpk-story .highlight-box {
        background: #f8fafc;
        border-left: 4px solid #059669;
        padding: 1rem 1.2rem;
        border-radius: 8px;
        margin-top: 1.2rem;
    }
    .jpk-story .highlight-box p {
        font-style: italic;
        color: #0b1a2b;
        font-weight: 500;
        margin: 0;
        font-size: 0.95rem;
    }

    /* ----- Core Pillars ----- */
    .jpk-pillars {
        padding: 3rem 0;
        background: #f8fafc;
        border-top: 1px solid #eef2f6;
        border-bottom: 1px solid #eef2f6;
    }
    .jpk-pillars .pillar {
        display: flex;
        align-items: flex-start;
        gap: 0.8rem;
        background: #fff;
        padding: 1.2rem 1.2rem;
        border-radius: 12px;
        border: 1px solid #eef2f6;
        transition: all 0.3s;
        height: 100%;
    }
    .jpk-pillars .pillar:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.04);
        border-color: #b8e0cf;
    }
    .jpk-pillars .pillar .icon {
        width: 44px;
        height: 44px;
        background: #e6f5ed;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: #059669;
        flex-shrink: 0;
        transition: all 0.3s;
    }
    .jpk-pillars .pillar:hover .icon {
        background: #059669;
        color: #fff;
    }
    .jpk-pillars .pillar h6 {
        font-weight: 700;
        color: #0b1a2b;
        font-size: 0.9rem;
        margin-bottom: 0.2rem;
    }
    .jpk-pillars .pillar p {
        color: #64748b;
        font-size: 0.8rem;
        line-height: 1.6;
        margin: 0;
    }

    /* ----- Vision & Mission ----- */
    .jpk-vm {
        padding: 3.5rem 0;
        background: #fff;
    }
    .jpk-vm .card {
        background: #fff;
        border: 1px solid #eef2f6;
        border-radius: 14px;
        padding: 1.8rem;
        height: 100%;
        transition: all 0.3s;
    }
    .jpk-vm .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.04);
        border-color: #b8e0cf;
    }
    .jpk-vm .card .icon {
        font-size: 2rem;
        color: #059669;
        margin-bottom: 0.5rem;
        display: block;
    }
    .jpk-vm .card h4 {
        font-weight: 700;
        color: #0b1a2b;
        font-size: 1.1rem;
        margin-bottom: 0.4rem;
    }
    .jpk-vm .card p {
        color: #475569;
        font-size: 0.9rem;
        line-height: 1.7;
        margin: 0;
    }

    /* ----- Branches ----- */
    .jpk-branches {
        padding: 3.5rem 0;
        background: #f8fafc;
        border-top: 1px solid #eef2f6;
    }
    .jpk-branches .head {
        text-align: center;
        max-width: 600px;
        margin: 0 auto 2.5rem;
    }
    .jpk-branches .head .tag {
        color: #059669;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.7rem;
        display: block;
    }
    .jpk-branches .head h2 {
        font-size: 2rem;
        font-weight: 800;
        color: #0b1a2b;
        margin: 0.2rem 0;
        letter-spacing: -0.5px;
    }
    .jpk-branches .head p {
        color: #64748b;
        font-size: 0.95rem;
        margin: 0;
    }
    .jpk-branches .branch-card {
        background: #fff;
        border: 1px solid #eef2f6;
        border-radius: 12px;
        padding: 1.2rem 1.2rem;
        height: 100%;
        transition: all 0.3s;
    }
    .jpk-branches .branch-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.04);
        border-color: #b8e0cf;
    }
    .jpk-branches .branch-card h5 {
        font-weight: 700;
        color: #0b1a2b;
        font-size: 0.95rem;
        margin-bottom: 0.2rem;
    }
    .jpk-branches .branch-card h5 i {
        color: #059669;
        margin-right: 0.3rem;
    }
    .jpk-branches .branch-card .address {
        color: #64748b;
        font-size: 0.78rem;
        margin-bottom: 0.3rem;
        line-height: 1.5;
    }
    .jpk-branches .branch-card .phone {
        color: #059669;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .jpk-branches .branch-card .phone i {
        margin-right: 0.2rem;
    }

    /* ----- Responsive ----- */
    @media (max-width: 992px) {
        .jpk-about-header h1 { font-size: 2rem; }
        .jpk-story h2 { font-size: 1.8rem; }
        .jpk-branches .head h2 { font-size: 1.6rem; }
        .jpk-vm .card { padding: 1.2rem; }
    }
    @media (max-width: 576px) {
        .jpk-about-header { padding: 2rem 0 1.5rem; }
        .jpk-about-header h1 { font-size: 1.5rem; }
        .jpk-about-header p { font-size: 0.9rem; }
        .jpk-story { padding: 2rem 0; }
        .jpk-story h2 { font-size: 1.4rem; }
        .jpk-story .text { font-size: 0.85rem; }
        .jpk-pillars { padding: 2rem 0; }
        .jpk-pillars .pillar { padding: 0.8rem; }
        .jpk-vm { padding: 2rem 0; }
        .jpk-vm .card { padding: 1rem; }
        .jpk-branches { padding: 2rem 0; }
        .jpk-branches .head h2 { font-size: 1.3rem; }
        .jpk-branches .branch-card { padding: 0.8rem; }
    }
</style>

<!-- ============================================
     PAGE HEADER
     ============================================ -->
<section class="jpk-about-header">
    <div class="container text-center">
        <span class="badge">15+ YEARS OF CLINICAL EXCELLENCE</span>
        <h1>About Janki Piles Clinic</h1>
        <p>North India's premier multi-branch laser proctology center, dedicated to 100% painless, stitchless daycare laser surgery for Piles, Fissure, Fistula &amp; Hernia.</p>
    </div>
</section>

<!-- ============================================
     OUR STORY
     ============================================ -->
<section class="jpk-story">
    <div class="container" style="max-width:1000px;">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <span class="tag">Our Clinical Journey</span>
                <h2>Pioneering Painless Laser Proctology Care</h2>
                <p class="text">
                    Founded over 15 years ago, <strong>Janki Piles Clinic</strong> was established with a singular mission: To eliminate the fear, embarrassment, and severe physical pain associated with anorectal disorders through advanced surgical innovation and ethical clinical care.
                </p>
                <p class="text">
                    Before laser proctology, patients suffering from Grade 3/4 piles, chronic fissures, or complex fistulas endured painful open scalpel cuts, weeks of strict bed rest, and high recurrence rates. Realizing the severe physical and emotional distress caused by traditional surgery, Janki Piles Clinic introduced <strong>German 1470nm Diode Laser Technology</strong> across regional centers in Uttarakhand and Punjab.
                </p>
                <div class="highlight-box">
                    <p>"Today, across our 7 clinic branches in Dehradun, Haridwar, Roorkee, Bhaniyawala, Srinagar Garhwal, Haldwani, and Mohali, we have successfully treated over 15,000+ patients with zero scalpel cuts and same-day discharge."</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div style="background:linear-gradient(145deg,#e6f5ed,#d1f0e3);border-radius:16px;padding:1.5rem;min-height:300px;display:flex;align-items:center;justify-content:center;font-size:4rem;color:#059669;box-shadow:0 20px 50px rgba(5,150,105,0.06);">
                    <div class="text-center">
                        <i class="bi bi-hospital" style="font-size:4rem;display:block;margin-bottom:0.5rem;opacity:0.6;"></i>
                        <span style="font-size:1.2rem;font-weight:600;color:#0b1a2b;display:block;background:rgba(255,255,255,0.6);padding:0.5rem 1.5rem;border-radius:12px;backdrop-filter:blur(8px);">15+ Years of Excellence</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     CORE PILLARS
     ============================================ -->
<section class="jpk-pillars">
    <div class="container">
        <div class="row g-3">
            <div class="col-md-6 col-lg-3">
                <div class="pillar">
                    <div class="icon"><i class="bi bi-shield-check"></i></div>
                    <div>
                        <h6>Zero-Cut Laser Ablation</h6>
                        <p>No scalpel cuts or open stitches, reducing post-operative pain by 90-95%.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="pillar">
                    <div class="icon"><i class="bi bi-heart-pulse"></i></div>
                    <div>
                        <h6>100% Sphincter Preservation</h6>
                        <p>High-precision laser preserves anal sphincter muscles, ensuring zero incontinence risk.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="pillar">
                    <div class="icon"><i class="bi bi-gender-female"></i></div>
                    <div>
                        <h6>Female Patient Dignity</h6>
                        <p>Dedicated female chaperones and private consultation suites for complete comfort.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="pillar">
                    <div class="icon"><i class="bi bi-credit-card-2-front"></i></div>
                    <div>
                        <h6>100% Cashless TPA</h6>
                        <p>Direct tie-ups with Star Health, HDFC ERGO, Care, Max Bupa, ICICI Lombard &amp; more.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     VISION & MISSION
     ============================================ -->
<section class="jpk-vm">
    <div class="container" style="max-width:950px;">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card">
                    <span class="icon"><i class="bi bi-eye-fill"></i></span>
                    <h4>Our Vision</h4>
                    <p>To be India's most trusted, patient-centric center of excellence in Laser Proctology and Minimally Invasive General Surgery, recognized for zero-pain clinical outcomes, surgical innovation, and absolute ethical transparency.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <span class="icon"><i class="bi bi-rocket-takeoff-fill"></i></span>
                    <h4>Our Mission</h4>
                    <p>To deliver 100% painless daycare laser solutions that return patients to normal daily life within 24-48 hours, break societal stigmas around proctological diseases, and make advanced surgical technology affordable through cashless insurance support.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     BRANCHES
     ============================================ -->
<section class="jpk-branches">
    <div class="container">
        <div class="head">
            <span class="tag">Regional Healthcare Footprint</span>
            <h2>Our 7 Clinic Branches</h2>
            <p>Conveniently accessible across major cities in Uttarakhand and Punjab.</p>
        </div>
        <div class="row g-3">
            <div class="col-md-4 col-sm-6">
                <div class="branch-card">
                    <h5><i class="bi bi-geo-alt-fill"></i> Dehradun (Main)</h5>
                    <div class="address">Rajpur Road, Near EC Road Junction, Dehradun</div>
                    <div class="phone"><i class="bi bi-telephone"></i> +91 98765 43210</div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="branch-card">
                    <h5><i class="bi bi-geo-alt-fill"></i> Haridwar Clinic</h5>
                    <div class="address">Near Ranipur More Flyover, Main Highway, Haridwar</div>
                    <div class="phone"><i class="bi bi-telephone"></i> +91 98765 43210</div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="branch-card">
                    <h5><i class="bi bi-geo-alt-fill"></i> Roorkee Clinic</h5>
                    <div class="address">Civil Lines, Near IIT Roorkee, Roorkee</div>
                    <div class="phone"><i class="bi bi-telephone"></i> +91 98765 43210</div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="branch-card">
                    <h5><i class="bi bi-geo-alt-fill"></i> Bhaniyawala Clinic</h5>
                    <div class="address">Jolly Grant Airport Road, Bhaniyawala / Doiwala</div>
                    <div class="phone"><i class="bi bi-telephone"></i> +91 98765 43210</div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="branch-card">
                    <h5><i class="bi bi-geo-alt-fill"></i> Srinagar Garhwal</h5>
                    <div class="address">Main Market, Medical College Road, Srinagar</div>
                    <div class="phone"><i class="bi bi-telephone"></i> +91 98765 43210</div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="branch-card">
                    <h5><i class="bi bi-geo-alt-fill"></i> Haldwani (Kumaon)</h5>
                    <div class="address">Bareilly-Nainital Road, Haldwani</div>
                    <div class="phone"><i class="bi bi-telephone"></i> +91 98765 43210</div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include VIEWS_PATH . '/layout/public_footer.php'; ?>