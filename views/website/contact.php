<?php include VIEWS_PATH . '/layout/public_header.php'; ?>

<!-- Header -->
<section class="py-5 bg-gradient-hero border-bottom">
    <div class="container py-3 text-center">
        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1.5 rounded-pill mb-2 small fw-bold">24/7 HELPLINE & MULTI-BRANCH LOCATIONS</span>
        <h1 class="display-5 fw-extrabold text-slate mb-3">Contact Janki Piles Clinic</h1>
        <p class="lead text-muted max-width-700 mx-auto">Get in touch with our senior proctology coordinators or visit your nearest clinic branch in Dehradun, Haridwar, Roorkee, Bhaniyawala, Srinagar Garhwal, Haldwani, or Mohali.</p>
    </div>
</section>

<!-- Interactive Branch Locations Tabs Section -->
<section class="py-5 bg-slate-50 border-bottom">
    <div class="container py-2">
        <div class="text-center mb-4">
            <h6 class="text-emerald fw-bold text-uppercase tracking-wider">Our Super-Specialty Network</h6>
            <h2 class="fw-extrabold text-slate display-6">Select Clinic Branch Location</h2>
            <p class="text-muted small">Click on any branch tab below to view detailed address, direct phone, working hours, and Google Maps location.</p>
        </div>

        <?php
        // Default rich branch details if DB list is empty or minimal
        $branchList = !empty($branches) ? $branches : [
            ['id' => 1, 'name' => 'Dehradun Main Clinic', 'phone' => '+91 98765 43210', 'emergency_number' => '+91 98765 43210', 'email' => 'dehradun@jankipilesclinic.com', 'address' => 'Rajpur Road, Near EC Road Junction, Dehradun, Uttarakhand - 248001', 'opening_hours' => 'Mon - Sat: 09:00 AM - 08:00 PM | Sun: 10:00 AM - 02:00 PM', 'google_map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3443.6891234567!2d78.032189!3d30.316494!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390929c356789abc%3A0x1234567890abcdef!2sDehradun!5e0!3m2!1sen!2sin!4v1680000000000!5m2!1sen!2sin'],
            ['id' => 2, 'name' => 'Haridwar Clinic', 'phone' => '+91 98765 43210', 'emergency_number' => '+91 98765 43210', 'email' => 'haridwar@jankipilesclinic.com', 'address' => 'Near Ranipur More Flyover, Main Delhi-Haridwar Highway, Haridwar, Uttarakhand', 'opening_hours' => 'Mon - Sat: 09:00 AM - 08:00 PM | Sun: 10:00 AM - 02:00 PM', 'google_map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3456.123456789!2d78.132189!3d29.945678!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3909470000000000%3A0x0!2sHaridwar!5e0!3m2!1sen!2sin!4v1680000000000!5m2!1sen!2sin'],
            ['id' => 3, 'name' => 'Roorkee Clinic', 'phone' => '+91 98765 43210', 'emergency_number' => '+91 98765 43210', 'email' => 'roorkee@jankipilesclinic.com', 'address' => 'Civil Lines, Near Century Gate (IIT Roorkee), Roorkee, Uttarakhand', 'opening_hours' => 'Mon - Sat: 09:00 AM - 08:00 PM | Sun: 10:00 AM - 02:00 PM', 'google_map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3460.123456789!2d77.890123!3d29.864321!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390eb30000000000%3A0x0!2sRoorkee!5e0!3m2!1sen!2sin!4v1680000000000!5m2!1sen!2sin'],
            ['id' => 4, 'name' => 'Bhaniyawala Clinic', 'phone' => '+91 98765 43210', 'emergency_number' => '+91 98765 43210', 'email' => 'bhaniyawala@jankipilesclinic.com', 'address' => 'Jolly Grant Airport Road, Bhaniyawala Chowk, Dehradun District, Uttarakhand', 'opening_hours' => 'Mon - Sat: 09:00 AM - 08:00 PM | Sun: 10:00 AM - 02:00 PM', 'google_map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3446.123456789!2d78.180000!3d30.190000!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3909200000000000%3A0x0!2sBhaniyawala!5e0!3m2!1sen!2sin!4v1680000000000!5m2!1sen!2sin'],
            ['id' => 5, 'name' => 'Srinagar Garhwal Clinic', 'phone' => '+91 98765 43210', 'emergency_number' => '+91 98765 43210', 'email' => 'srinagar@jankipilesclinic.com', 'address' => 'Main Market, Opposite Medical College Road, Srinagar Garhwal, Uttarakhand', 'opening_hours' => 'Mon - Sat: 09:00 AM - 08:00 PM | Sun: 10:00 AM - 02:00 PM', 'google_map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3430.123456789!2d78.780000!3d30.220000!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3909d00000000000%3A0x0!2sSrinagar+Garhwal!5e0!3m2!1sen!2sin!4v1680000000000!5m2!1sen!2sin'],
            ['id' => 6, 'name' => 'Haldwani Clinic', 'phone' => '+91 98765 43210', 'emergency_number' => '+91 98765 43210', 'email' => 'haldwani@jankipilesclinic.com', 'address' => 'Bareilly-Nainital Road, Near Okal Kanda Junction, Haldwani, Uttarakhand', 'opening_hours' => 'Mon - Sat: 09:00 AM - 08:00 PM | Sun: 10:00 AM - 02:00 PM', 'google_map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3480.123456789!2d79.520000!3d29.220000!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39a0900000000000%3A0x0!2sHaldwani!5e0!3m2!1sen!2sin!4v1680000000000!5m2!1sen!2sin'],
            ['id' => 7, 'name' => 'Mohali Branch (Tricity)', 'phone' => '+91 98765 43210', 'emergency_number' => '+91 98765 43210', 'email' => 'mohali@jankipilesclinic.com', 'address' => 'Sector 62, Phase 7, Near Fortis Hospital Road, Mohali, Punjab', 'opening_hours' => 'Mon - Sat: 09:00 AM - 08:00 PM | Sun: 10:00 AM - 02:00 PM', 'google_map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3430.500000000!2d76.717000!3d30.704000!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390fed0000000000%3A0x0!2sMohali!5e0!3m2!1sen!2sin!4v1680000000000!5m2!1sen!2sin']
        ];
        ?>

        <!-- Branch Navigation Pills -->
        <ul class="nav nav-pills justify-content-center gap-2 mb-4 p-2 bg-white rounded-4 shadow-sm border" id="branchTabs" role="tablist">
            <?php foreach ($branchList as $index => $br): ?>
                <?php 
                $tabId = 'branch-tab-' . ($br['id'] ?? $index);
                $paneId = 'branch-pane-' . ($br['id'] ?? $index);
                $isActive = ($index === 0);
                ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-3.5 py-2.5 rounded-pill fw-bold small <?= $isActive ? 'active bg-emerald text-white' : 'text-slate' ?>" 
                            id="<?= $tabId ?>" 
                            data-bs-toggle="tab" 
                            data-bs-target="#<?= $paneId ?>" 
                            type="button" 
                            role="tab" 
                            aria-controls="<?= $paneId ?>" 
                            aria-selected="<?= $isActive ? 'true' : 'false' ?>">
                        <i class="bi bi-geo-alt-fill me-1"></i> <?= esc($br['name']) ?>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- Branch Tab Contents -->
        <div class="tab-content" id="branchTabsContent">
            <?php foreach ($branchList as $index => $br): ?>
                <?php 
                $paneId = 'branch-pane-' . ($br['id'] ?? $index);
                $tabId = 'branch-tab-' . ($br['id'] ?? $index);
                $isActive = ($index === 0);
                ?>
                <div class="tab-pane fade <?= $isActive ? 'show active' : '' ?>" id="<?= $paneId ?>" role="tabpanel" aria-labelledby="<?= $tabId ?>">
                    <div class="card border-0 shadow-sm glass-card rounded-4 overflow-hidden">
                        <div class="row g-0 align-items-stretch">
                            <!-- Left: Branch Info -->
                            <div class="col-lg-6 p-4 p-md-5 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <span class="badge bg-emerald text-white px-3 py-1.5 rounded-pill small fw-bold">
                                            <i class="bi bi-patch-check-fill me-1"></i> Official Janki Piles Branch
                                        </span>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1 rounded-pill small fw-bold">
                                            OPD Open Today
                                        </span>
                                    </div>

                                    <h3 class="fw-extrabold text-slate mb-3"><?= esc($br['name']) ?></h3>

                                    <div class="mb-4 d-flex flex-column gap-3">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="bg-emerald bg-opacity-10 text-emerald p-2.5 rounded-circle fs-5 flex-shrink-0">
                                                <i class="bi bi-geo-alt-fill"></i>
                                            </div>
                                            <div>
                                                <div class="small text-muted fw-bold">Clinic Address:</div>
                                                <div class="fw-semibold text-slate fs-6"><?= esc($br['address']) ?></div>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-start gap-3">
                                            <div class="bg-emerald bg-opacity-10 text-emerald p-2.5 rounded-circle fs-5 flex-shrink-0">
                                                <i class="bi bi-telephone-fill"></i>
                                            </div>
                                            <div>
                                                <div class="small text-muted fw-bold">Contact Phone & 24/7 Hotline:</div>
                                                <div class="fw-semibold text-slate">
                                                    <a href="tel:<?= esc($br['phone'] ?? '+919876543210') ?>" class="text-decoration-none text-slate hover-emerald me-3">
                                                        <i class="bi bi-telephone me-1 text-success"></i> <?= esc($br['phone'] ?? '+91 98765 43210') ?>
                                                    </a>
                                                    <a href="tel:<?= esc($br['emergency_number'] ?? '+919876543210') ?>" class="text-decoration-none text-danger fw-bold">
                                                        <i class="bi bi-bell-fill me-1"></i> Emergency: <?= esc($br['emergency_number'] ?? '+91 98765 43210') ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-start gap-3">
                                            <div class="bg-emerald bg-opacity-10 text-emerald p-2.5 rounded-circle fs-5 flex-shrink-0">
                                                <i class="bi bi-envelope-fill"></i>
                                            </div>
                                            <div>
                                                <div class="small text-muted fw-bold">Branch Email:</div>
                                                <div class="fw-semibold text-slate">
                                                    <a href="mailto:<?= esc($br['email'] ?? 'info@jankipilesclinic.com') ?>" class="text-decoration-none text-slate hover-emerald">
                                                        <?= esc($br['email'] ?? 'info@jankipilesclinic.com') ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-start gap-3">
                                            <div class="bg-emerald bg-opacity-10 text-emerald p-2.5 rounded-circle fs-5 flex-shrink-0">
                                                <i class="bi bi-clock-fill"></i>
                                            </div>
                                            <div>
                                                <div class="small text-muted fw-bold">OPD Timings & Consultation Hours:</div>
                                                <div class="fw-semibold text-slate small">
                                                    <?= esc($br['opening_hours'] ?? 'Mon - Sat: 09:00 AM - 08:00 PM | Sun: 10:00 AM - 02:00 PM') ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-2 pt-3 border-top">
                                    <a href="tel:<?= esc($br['phone'] ?? '+919876543210') ?>" class="btn btn-emerald btn-sm px-3.5 rounded-pill shadow-sm">
                                        <i class="bi bi-telephone-outbound me-1"></i> Call Branch
                                    </a>
                                    <a href="https://wa.me/919876543210" target="_blank" class="btn btn-success btn-sm px-3 rounded-pill" style="background-color: #25d366 !important; border:none;">
                                        <i class="bi bi-whatsapp me-1"></i> WhatsApp Doctor
                                    </a>
                                    <a href="<?= site_url('/appointments/book') ?>" class="btn btn-outline-success btn-sm px-3 rounded-pill fw-bold">
                                        <i class="bi bi-calendar-check me-1"></i> Book Slot
                                    </a>
                                </div>
                            </div>

                            <!-- Right: Google Map Embed -->
                            <div class="col-lg-6 bg-slate-100 position-relative min-vh-300">
                                <?php if (!empty($br['google_map_link']) && str_contains($br['google_map_link'], 'http')): ?>
                                    <iframe src="<?= esc($br['google_map_link']) ?>" 
                                            width="100%" 
                                            height="100%" 
                                            style="border:0; min-height: 380px;" 
                                            allowfullscreen="" 
                                            loading="lazy" 
                                            referrerpolicy="no-referrer-when-downgrade">
                                    </iframe>
                                <?php else: ?>
                                    <div class="d-flex flex-column align-items-center justify-content-center h-100 p-5 text-center bg-light">
                                        <div class="bg-emerald bg-opacity-10 text-emerald rounded-circle p-4 mb-3">
                                            <i class="bi bi-map-fill display-4"></i>
                                        </div>
                                        <h5 class="fw-bold text-slate mb-1"><?= esc($br['name']) ?></h5>
                                        <p class="text-muted small mb-3"><?= esc($br['address']) ?></p>
                                        <a href="https://maps.google.com/?q=<?= urlencode($br['name'] . ' ' . $br['address']) ?>" target="_blank" class="btn btn-emerald btn-sm rounded-pill px-4">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> Open Google Maps Directions
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Direct Communication & Confidential Inquiry Form -->
<section class="py-5 bg-white">
    <div class="container py-3" style="max-width: 1050px;">
        <div class="row g-5">
            <!-- Left: Helplines -->
            <div class="col-lg-5">
                <h4 class="fw-extrabold text-slate mb-4">Direct Patient Helplines</h4>
                
                <div class="p-4 rounded-4 bg-light mb-4 border">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-danger text-white rounded-circle p-3 fs-4"><i class="bi bi-telephone-fill"></i></div>
                        <div>
                            <div class="small text-muted fw-bold">24/7 Acute Emergency Line:</div>
                            <a href="tel:+919876543210" class="fs-5 fw-extrabold text-danger text-decoration-none">+91 98765 43210</a>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-success text-white rounded-circle p-3 fs-4" style="background-color: #25d366 !important;"><i class="bi bi-whatsapp"></i></div>
                        <div>
                            <div class="small text-muted fw-bold">WhatsApp Direct Consultation:</div>
                            <a href="https://wa.me/919876543210" class="fs-6 fw-bold text-success text-decoration-none" target="_blank">+91 98765 43210</a>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-emerald text-white rounded-circle p-3 fs-4"><i class="bi bi-envelope-fill"></i></div>
                        <div>
                            <div class="small text-muted fw-bold">Central Email:</div>
                            <a href="mailto:info@jankipilesclinic.com" class="fs-6 fw-bold text-slate text-decoration-none">info@jankipilesclinic.com</a>
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-4 bg-emerald text-white shadow-sm">
                    <h5 class="fw-bold mb-2"><i class="bi bi-clock-history me-1"></i> Clinic Working Hours</h5>
                    <p class="mb-1"><strong>Monday - Saturday:</strong> 09:00 AM to 08:00 PM</p>
                    <p class="mb-0"><strong>Sunday OPD:</strong> 10:00 AM to 02:00 PM</p>
                </div>
            </div>

            <!-- Right: Interactive Inquiry Form -->
            <div class="col-lg-7">
                <div class="glass-card p-4 p-md-5 shadow-lg border-0">
                    <h4 class="fw-extrabold text-slate mb-1">Send a Confidential Query</h4>
                    <p class="text-muted small mb-4">Our medical coordinator will call or WhatsApp you within 15 minutes.</p>
                    
                    <form action="<?= site_url('/contact/enquiry/save') ?>" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-slate">Full Name *</label>
                                <input type="text" name="name" class="form-control form-control-lg rounded-3 fs-6" placeholder="Your name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-slate">Phone Number *</label>
                                <input type="tel" name="phone" class="form-control form-control-lg rounded-3 fs-6" placeholder="10-digit mobile" pattern="[0-9]{10}" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-semibold text-slate">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-lg rounded-3 fs-6" placeholder="Your email address">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-semibold text-slate">Primary Concern / Subject</label>
                                <select name="subject" class="form-select form-select-lg rounded-3 fs-6">
                                    <option value="Piles Laser Surgery Query">Piles Laser Surgery Query</option>
                                    <option value="Anal Fissure Pain Relief">Anal Fissure Pain Relief</option>
                                    <option value="FiLaC Fistula Surgery">FiLaC Fistula Surgery</option>
                                    <option value="Pilonidal Sinus Laser">Pilonidal Sinus Laser</option>
                                    <option value="ZSR Circumcision Info">ZSR Circumcision Info</option>
                                    <option value="Hernia Surgery Package">Hernia Surgery Package</option>
                                    <option value="Cashless Insurance Approval">Cashless Insurance Approval</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-semibold text-slate">Symptoms / Message</label>
                                <textarea name="message" rows="4" class="form-control rounded-3 fs-6" placeholder="Describe your symptoms or query in confidence..."></textarea>
                            </div>
                            <div class="col-md-12 pt-2">
                                <button type="submit" class="btn btn-emerald btn-lg w-100 rounded-pill py-3 shadow-sm">
                                    <i class="bi bi-send me-1"></i> Send Confidential Inquiry
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include VIEWS_PATH . '/layout/public_footer.php'; ?>
