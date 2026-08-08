<?php 
$activePage = 'reception_leads';
include VIEWS_PATH . '/layout/reception_header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-slate mb-1"><i class="bi bi-funnel-fill text-success me-2"></i>CRM Lead Management & Inquiries</h4>
        <p class="text-muted small mb-0">Track website, Google, WhatsApp, Facebook, and walk-in patient inquiries.</p>
    </div>
    <button type="button" class="btn btn-emerald btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#newLeadModal">
        <i class="bi bi-plus-lg me-1"></i> Register New Lead
    </button>
</div>

<!-- Lead Pipeline Status Badges -->
<div class="row g-2 mb-4">
    <?php 
    $statuses = [
        'all' => ['label' => 'All Inquiries', 'bg' => 'bg-secondary'],
        'new' => ['label' => 'New', 'bg' => 'bg-primary'],
        'contacted' => ['label' => 'Contacted', 'bg' => 'bg-info'],
        'interested' => ['label' => 'Interested', 'bg' => 'bg-warning'],
        'appointment_booked' => ['label' => 'Booked', 'bg' => 'bg-emerald'],
        'converted' => ['label' => 'Converted', 'bg' => 'bg-success'],
        'lost' => ['label' => 'Lost', 'bg' => 'bg-dark']
    ];
    ?>
    <?php foreach ($statuses as $k => $info): ?>
        <div class="col">
            <a href="<?= site_url('/reception/leads?status=' . $k) ?>" class="text-decoration-none">
                <div class="p-2.5 rounded-3 border bg-white text-center shadow-xs <?= ($active_status === $k) ? 'border-success border-2 bg-success bg-opacity-10' : '' ?>">
                    <div class="small text-muted text-uppercase fw-bold" style="font-size:0.68rem;"><?= esc($info['label']) ?></div>
                    <div class="fw-bold text-slate fs-5 mb-0"><?= esc((string)($counts[$k] ?? 0)) ?></div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<!-- Leads List Table -->
<div class="card border-0 shadow-sm p-4 rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:0.88rem;">
            <thead class="bg-light">
                <tr>
                    <th>Lead Name</th>
                    <th>Mobile / Email</th>
                    <th>Lead Source</th>
                    <th>Status</th>
                    <th>Follow-up Date</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($leads)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox display-6 d-block mb-2 text-muted"></i>
                            No lead inquiries found for selected status filter.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($leads as $l): ?>
                        <?php 
                            $waMsg = "Namaste " . $l['name'] . ", thank you for inquiring about Janki Piles Clinic. Our medical team is available for expert consultation. Helpline: +91 98765 43210.";
                            $waLink = \App\Models\Communication::getWhatsAppLink($l['phone'], $waMsg);
                        ?>
                        <tr>
                            <td class="fw-bold text-dark"><?= esc($l['name']) ?></td>
                            <td>
                                <div><i class="bi bi-telephone text-muted me-1"></i><?= esc($l['phone']) ?></div>
                                <?php if (!empty($l['email'])): ?>
                                    <div class="small text-muted"><i class="bi bi-envelope me-1"></i><?= esc($l['email']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge bg-light text-slate border"><?= esc($l['source']) ?></span></td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 text-uppercase">
                                    <?= esc(str_replace('_', ' ', $l['status'])) ?>
                                </span>
                            </td>
                            <td class="small"><?= esc($l['follow_up_date'] ? date('d M Y', strtotime($l['follow_up_date'])) : 'Not Set') ?></td>
                            <td class="small text-muted" style="max-width:220px;"><?= esc($l['notes'] ?: 'Inquiry registered') ?></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="<?= $waLink ?>" target="_blank" class="btn btn-sm btn-success px-2 py-1" style="font-size:0.75rem;">
                                        <i class="bi bi-whatsapp me-1"></i> Chat
                                    </a>
                                    <a href="<?= site_url('/reception/walk-in?name=' . urlencode($l['name']) . '&phone=' . urlencode($l['phone'])) ?>" class="btn btn-sm btn-outline-primary px-2 py-1" style="font-size:0.75rem;">
                                        <i class="bi bi-person-check me-1"></i> Convert
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Register New Lead Modal -->
<div class="modal fade" id="newLeadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-slate"><i class="bi bi-person-plus-fill text-success me-2"></i>Register Lead Inquiry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url('/reception/leads/save') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="lead_name" class="form-label small fw-bold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="lead_name" name="name" required placeholder="Enter lead name">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label for="lead_phone" class="form-label small fw-bold">Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="lead_phone" name="phone" required placeholder="+91 98765 43210">
                        </div>
                        <div class="col-md-6">
                            <label for="lead_source" class="form-label small fw-bold">Lead Source</label>
                            <select class="form-select" id="lead_source" name="source">
                                <option value="Walk-In">Walk-In</option>
                                <option value="Website">Website</option>
                                <option value="Google">Google</option>
                                <option value="Facebook">Facebook</option>
                                <option value="WhatsApp">WhatsApp</option>
                                <option value="Existing Patient">Existing Patient</option>
                                <option value="Doctor Referral">Doctor Referral</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="lead_email" class="form-label small fw-bold">Email Address</label>
                        <input type="email" class="form-control" id="lead_email" name="email" placeholder="email@example.com">
                    </div>
                    <div class="mb-3">
                        <label for="lead_follow_up" class="form-label small fw-bold">Follow-Up Date</label>
                        <input type="date" class="form-control" id="lead_follow_up" name="follow_up_date" min="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="lead_notes" class="form-label small fw-bold">Inquiry Notes</label>
                        <textarea class="form-control" id="lead_notes" name="notes" rows="2" placeholder="Patient complaints, treatment cost inquiry, requested callback time"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-emerald btn-sm px-4 fw-bold shadow-sm">
                        <i class="bi bi-cloud-upload-fill me-1"></i> Save Lead Inquiry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/reception_footer.php'; ?>
