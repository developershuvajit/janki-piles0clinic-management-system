<?php include VIEWS_PATH . '/layout/public_header.php'; ?>

<!-- Page Header -->
<section class="py-5 bg-gradient-hero border-bottom">
    <div class="container py-3 text-center">
        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1.5 rounded-pill mb-2 small fw-bold">EXPERIENCED SURGICAL FACULTY</span>
        <h1 class="display-5 fw-extrabold text-slate mb-3">Meet Our Expert Proctologists & Laser Surgeons</h1>
        <p class="lead text-muted max-width-700 mx-auto">Veteran MS General Surgeons and International Fellows in German Laser Proctology committed to compassionate, 100% confidential patient care.</p>
    </div>
</section>

<!-- Doctor Profiles List -->
<section class="py-5 bg-white">
    <div class="container py-3" style="max-width: 1000px;">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-5">
            <div class="row g-0 align-items-center">
                <div class="col-md-5 bg-gradient-hero text-center p-5">
                    <div class="bg-white rounded-circle shadow-md p-4 mx-auto mb-3" style="width: 160px; height: 160px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-person-badge-fill text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h4 class="fw-bold text-slate mb-1">Chief Laser Proctologist</h4>
                    <p class="badge bg-emerald text-white px-3 py-1.5 rounded-pill mb-0">15+ Years Clinical Experience</p>
                </div>
                <div class="col-md-7 p-4 p-lg-5">
                    <div class="badge bg-success bg-opacity-10 text-success px-3 py-1 rounded-pill mb-2 small fw-bold">SENIOR SURGICAL SPECIALIST</div>
                    <h3 class="fw-extrabold text-slate mb-2">Dr. Senior Proctologist (MS, FMAS, FIAGES)</h3>
                    <p class="text-muted small mb-4">M.B.B.S. (Gold Medalist), M.S. (General Surgery), Fellowship in Minimal Access Surgery (FMAS), Certified in German Laser Proctology (LHP, FiLaC, SiLaC).</p>
                    
                    <h6 class="fw-bold text-slate mb-2 text-uppercase small tracking-wider">Surgical Expertise:</h6>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <span class="badge bg-light text-slate border px-3 py-2">German Laser Piles (LHP)</span>
                        <span class="badge bg-light text-slate border px-3 py-2">FiLaC Fistula Repair</span>
                        <span class="badge bg-light text-slate border px-3 py-2">Laser Fissure Sphincterotomy</span>
                        <span class="badge bg-light text-slate border px-3 py-2">ZSR Stapler Circumcision</span>
                        <span class="badge bg-light text-slate border px-3 py-2">Laparoscopic Hernia</span>
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-4">
                        <div class="fw-bold text-slate small mb-1"><i class="bi bi-quote text-success me-1"></i> Patient Care Philosophy:</div>
                        <p class="fst-italic text-muted small mb-0">"Anorectal disorders cause immense physical agony and mental distress mainly because patients feel embarrassed to seek help early. My clinical goal is to provide a non-judgmental, warm environment where patients feel fully heard, respected, and cured with zero scalpel cuts."</p>
                    </div>

                    <div class="d-flex gap-3">
                        <a href="<?= site_url('/appointments/book') ?>" class="btn btn-emerald rounded-pill px-4 shadow-sm">
                            <i class="bi bi-calendar-check me-1"></i> Book OPD Consultation
                        </a>
                        <a href="https://wa.me/919876543210" class="btn btn-outline-success rounded-pill px-4" target="_blank">
                            <i class="bi bi-whatsapp me-1"></i> WhatsApp Query
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Consultation Schedule Across Branches -->
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-light">
            <h4 class="fw-bold text-slate mb-3"><i class="bi bi-clock-history text-emerald me-2"></i> OPD Consultation Schedule Across Branches</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle bg-white rounded-3 overflow-hidden shadow-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>Branch Location</th>
                            <th>Consultation Days</th>
                            <th>OPD Timings</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold text-slate"><i class="bi bi-geo-alt-fill text-success me-1"></i> Dehradun Main Clinic</td>
                            <td>Mon, Wed, Fri</td>
                            <td>10:00 AM - 02:00 PM & 05:00 PM - 08:00 PM</td>
                            <td><a href="<?= site_url('/appointments/book') ?>" class="btn btn-sm btn-emerald rounded-pill px-3">Book Slot</a></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-slate"><i class="bi bi-geo-alt-fill text-success me-1"></i> Haridwar Clinic</td>
                            <td>Tue, Thu</td>
                            <td>10:30 AM - 03:00 PM</td>
                            <td><a href="<?= site_url('/appointments/book') ?>" class="btn btn-sm btn-emerald rounded-pill px-3">Book Slot</a></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-slate"><i class="bi bi-geo-alt-fill text-success me-1"></i> Roorkee Clinic</td>
                            <td>Tue, Sat</td>
                            <td>04:00 PM - 07:30 PM</td>
                            <td><a href="<?= site_url('/appointments/book') ?>" class="btn btn-sm btn-emerald rounded-pill px-3">Book Slot</a></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-slate"><i class="bi bi-geo-alt-fill text-success me-1"></i> Haldwani Clinic</td>
                            <td>Every 2nd & 4th Sunday</td>
                            <td>11:00 AM - 04:00 PM</td>
                            <td><a href="<?= site_url('/appointments/book') ?>" class="btn btn-sm btn-emerald rounded-pill px-3">Book Slot</a></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-slate"><i class="bi bi-geo-alt-fill text-success me-1"></i> Mohali Branch</td>
                            <td>Mon, Sat</td>
                            <td>11:00 AM - 03:00 PM</td>
                            <td><a href="<?= site_url('/appointments/book') ?>" class="btn btn-sm btn-emerald rounded-pill px-3">Book Slot</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php include VIEWS_PATH . '/layout/public_footer.php'; ?>
