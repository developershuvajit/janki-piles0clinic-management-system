<?php 
$activePage = 'reception_leads';
include VIEWS_PATH . '/layout/reception_header.php'; 
?>

<!-- ============================================
     PAGE CSS
     ============================================ -->
<link rel="stylesheet" href="<?= asset('css/datatable.css') ?>">

<style>
/* Custom styles for leads page */
.border-emerald { border-color: #0f7b4a !important; }
.text-emerald { color: #0f7b4a !important; }
.btn-emerald { background: #0f7b4a; border-color: #0f7b4a; color: #fff; }
.btn-emerald:hover { background: #0a5d38; border-color: #0a5d38; color: #fff; }
.shadow-xs { box-shadow: 0 1px 3px rgba(0,0,0,.06); }
.text-slate { color: #0b1a2b; }

/* Status filter badges */
.status-filter {
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: #fff;
    text-align: center;
    transition: all 0.2s ease;
}
.status-filter:hover {
    border-color: #0f7b4a;
    transform: translateY(-1px);
}
.status-filter.active {
    border-color: #0f7b4a;
    border-width: 2px;
    background: #f0fdf4;
}
.status-filter .label {
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: #6b7a8f;
}
.status-filter .count {
    font-size: 1.3rem;
    font-weight: 700;
    color: #0b1a2b;
    margin-top: 2px;
}
</style>

<!-- ============================================
     HEADER
     ============================================ -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-slate mb-1"><i class="bi bi-funnel-fill text-success me-2"></i>CRM Lead Management & Inquiries</h4>
        <p class="text-muted small mb-0">Track website, Google, WhatsApp, Facebook, and walk-in patient inquiries.</p>
    </div>
    <button type="button" class="btn btn-emerald btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#newLeadModal">
        <i class="bi bi-plus-lg me-1"></i> Register New Lead
    </button>
</div>

<!-- ============================================
     LEAD PIPELINE STATUS BADGES
     ============================================ -->
<div class="row g-2 mb-4">
    <?php 
    $statuses = [
        'all' => ['label' => 'All Inquiries', 'icon' => 'bi-grid-3x3-gap-fill'],
        'new' => ['label' => 'New', 'icon' => 'bi-star-fill'],
        'contacted' => ['label' => 'Contacted', 'icon' => 'bi-chat-fill'],
        'interested' => ['label' => 'Interested', 'icon' => 'bi-hand-thumbs-up-fill'],
        'appointment_booked' => ['label' => 'Booked', 'icon' => 'bi-calendar-check-fill'],
        'converted' => ['label' => 'Converted', 'icon' => 'bi-person-check-fill'],
        'lost' => ['label' => 'Lost', 'icon' => 'bi-x-circle-fill']
    ];
    ?>
    <?php foreach ($statuses as $k => $info): ?>
        <div class="col">
            <a href="<?= site_url('/reception/leads?status=' . $k) ?>" class="text-decoration-none">
                <div class="status-filter <?= ($active_status === $k) ? 'active' : '' ?>">
                    <div class="label"><?= esc($info['label']) ?></div>
                    <div class="count"><?= esc((string)($counts[$k] ?? 0)) ?></div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<!-- ============================================
     LEADS LIST TABLE
     ============================================ -->
<div class="datatable-wrapper mt-4">
    <div class="datatable-header">
        <h5>Lead Inquiries <small><?= count($leads ?? []) ?> records</small></h5>
    </div>

    <div class="table-responsive">
        <table id="leadsTable" class="table-custom" style="width:100%">
            <thead>
                <tr>
                    <th class="sno">#</th>
                    <th style="min-width:150px;">Lead Name</th>
                    <th style="min-width:160px;">Mobile / Email</th>
                    <th style="width:130px;">Lead Source</th>
                    <th style="width:130px;">Status</th>
                    <th style="width:130px;">Follow-up Date</th>
                    <th style="min-width:160px;">Notes</th>
                    <th style="width:150px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($leads)):
                    $sn = 1;
                    foreach ($leads as $l): 
                        $waMsg = "Namaste " . $l['name'] . ", thank you for inquiring about Janki Piles Clinic. Our medical team is available for expert consultation. Helpline: +91 98765 43210.";
                        $waLink = \App\Models\Communication::getWhatsAppLink($l['phone'], $waMsg);
                    ?>
                        <tr>
                            <td class="sno"><?= $sn++ ?></td>
                            <td class="fw-bold text-slate"><?= esc($l['name']) ?></td>
                            <td>
                                <div><i class="bi bi-telephone text-muted me-1"></i><?= esc($l['phone']) ?></div>
                                <?php if (!empty($l['email'])): ?>
                                    <div class="text-muted small" style="font-size: 0.75rem;">
                                        <i class="bi bi-envelope me-1"></i><?= esc($l['email']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= esc($l['source']) ?></span>
                            </td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 text-uppercase" style="font-size: 0.7rem; font-weight: 600;">
                                    <?= esc(str_replace('_', ' ', $l['status'])) ?>
                                </span>
                            </td>
                            <td class="small">
                                <?= esc($l['follow_up_date'] ? date('d M Y', strtotime($l['follow_up_date'])) : 'Not Set') ?>
                            </td>
                            <td>
                                <span class="text-muted small" style="font-size: 0.82rem;">
                                    <?= esc($l['notes'] ?: 'Inquiry registered') ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-group">
                                    <a href="<?= $waLink ?>" target="_blank" class="btn-action" title="WhatsApp Chat" style="color: #25D366;">
                                        <i class="bi bi-whatsapp"></i>
                                    </a>
                                    <a href="<?= site_url('/reception/walk-in?name=' . urlencode($l['name']) . '&phone=' . urlencode($l['phone'])) ?>" 
                                       class="btn-action" 
                                       title="Convert to Patient" 
                                       style="color: #6366f1;">
                                        <i class="bi bi-person-check"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center;padding:2.5rem 1rem;color:#94a3b8;">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            No lead inquiries found for selected status filter.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ============================================
     REGISTER NEW LEAD MODAL
     ============================================ -->
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
                        <input type="text" class="form-control form-control-sm" id="lead_name" name="name" required placeholder="Enter lead name">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label for="lead_phone" class="form-label small fw-bold">Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="lead_phone" name="phone" required placeholder="+91 98765 43210">
                        </div>
                        <div class="col-md-6">
                            <label for="lead_source" class="form-label small fw-bold">Lead Source</label>
                            <select class="form-select form-select-sm" id="lead_source" name="source">
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
                        <input type="email" class="form-control form-control-sm" id="lead_email" name="email" placeholder="email@example.com">
                    </div>
                    <div class="mb-3">
                        <label for="lead_follow_up" class="form-label small fw-bold">Follow-Up Date</label>
                        <input type="date" class="form-control form-control-sm" id="lead_follow_up" name="follow_up_date" min="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="lead_notes" class="form-label small fw-bold">Inquiry Notes</label>
                        <textarea class="form-control form-control-sm" id="lead_notes" name="notes" rows="2" placeholder="Patient complaints, treatment cost inquiry, requested callback time"></textarea>
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

<!-- ============================================
     DATATABLES LIBS + INIT
     ============================================ -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
    $('#leadsTable').DataTable({
        pageLength: 25,
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        order: [[0, 'asc']]
    });
});
</script>

<?php include VIEWS_PATH . '/layout/reception_footer.php'; ?>