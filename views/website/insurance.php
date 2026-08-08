<?php include VIEWS_PATH . '/layout/public_header.php'; ?>

<!-- Header -->
<section class="py-5 bg-gradient-hero border-bottom">
    <div class="container py-3 text-center">
        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1.5 rounded-pill mb-2 small fw-bold">100% CASHLESS HOSPITALIZATION</span>
        <h1 class="display-5 fw-extrabold text-slate mb-3">Cashless Insurance & TPA Facilities</h1>
        <p class="lead text-muted max-width-700 mx-auto">Get 0% hassle cashless approvals for laser piles, fissure, fistula & hernia surgery. Empaneled with leading private and public health insurance providers.</p>
    </div>
</section>

<!-- Insurance Content -->
<section class="py-5 bg-white">
    <div class="container" style="max-width: 1000px;">
        <div class="row g-4 align-items-center mb-5">
            <div class="col-lg-7">
                <h3 class="fw-extrabold text-slate mb-3">Zero-Hassle Insurance Pre-Authorization</h3>
                <p class="text-muted leading-relaxed mb-3">At Janki Piles Clinic, we believe quality healthcare should be accessible and stress-free. Our dedicated Insurance Helpdesk handles all documentation, TPA queries, pre-authorization, and claims settlement directly with your insurance provider.</p>
                
                <div class="row g-3 pt-2">
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <h5 class="fw-bold text-emerald mb-1">1 - 2 Hours</h5>
                            <div class="small text-muted">Pre-Auth Approval Time</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <h5 class="fw-bold text-emerald mb-1">100% Paperwork</h5>
                            <div class="small text-muted">Handled by Clinic Desk</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="glass-card p-4 shadow-lg border-0">
                    <h5 class="fw-bold text-slate mb-3">Check Cashless Approval Now</h5>
                    <form action="<?= site_url('/contact/enquiry/save') ?>" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-slate">Patient Name</label>
                            <input type="text" name="name" class="form-control rounded-3" placeholder="Full name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-slate">Mobile Number</label>
                            <input type="tel" name="phone" class="form-control rounded-3" placeholder="10-digit mobile" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-slate">Insurance Provider Name</label>
                            <input type="text" name="message" class="form-control rounded-3" placeholder="e.g. Star Health, HDFC ERGO" required>
                        </div>
                        <button type="submit" class="btn btn-emerald w-100 rounded-pill py-2.5 shadow-sm">
                            <i class="bi bi-shield-check me-1"></i> Verify Policy Coverage
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Empaneled Insurance Providers List -->
        <h4 class="fw-extrabold text-slate mb-4 text-center">Empaneled Insurance & TPA Partners</h4>
        <div class="row g-3 text-center">
            <div class="col-6 col-md-3"><div class="p-3 bg-light rounded-3 border font-weight-bold text-slate">Star Health Insurance</div></div>
            <div class="col-6 col-md-3"><div class="p-3 bg-light rounded-3 border font-weight-bold text-slate">HDFC ERGO Health</div></div>
            <div class="col-6 col-md-3"><div class="p-3 bg-light rounded-3 border font-weight-bold text-slate">ICICI Lombard</div></div>
            <div class="col-6 col-md-3"><div class="p-3 bg-light rounded-3 border font-weight-bold text-slate">Niva Bupa (Max Bupa)</div></div>
            <div class="col-6 col-md-3"><div class="p-3 bg-light rounded-3 border font-weight-bold text-slate">Care Health Insurance</div></div>
            <div class="col-6 col-md-3"><div class="p-3 bg-light rounded-3 border font-weight-bold text-slate">SBI General Insurance</div></div>
            <div class="col-6 col-md-3"><div class="p-3 bg-light rounded-3 border font-weight-bold text-slate">Bajaj Allianz</div></div>
            <div class="col-6 col-md-3"><div class="p-3 bg-light rounded-3 border font-weight-bold text-slate">PSU Insurers (CGHS/EHS)</div></div>
        </div>
    </div>
</section>

<?php include VIEWS_PATH . '/layout/public_footer.php'; ?>
