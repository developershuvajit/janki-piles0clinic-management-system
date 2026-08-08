<?php 
$activePage = 'reception_communication';
include VIEWS_PATH . '/layout/reception_header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-slate mb-1"><i class="bi bi-whatsapp text-success me-2"></i>Communication Center</h4>
        <p class="text-muted small mb-0">Pre-configured WhatsApp and SMS message templates for patient reminders & notifications.</p>
    </div>
    <a href="https://web.whatsapp.com" target="_blank" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm">
        <i class="bi bi-whatsapp me-1"></i> Open WhatsApp Web
    </a>
</div>

<div class="row g-4 mb-4">
    <!-- Message Templates Gallery -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm p-4 rounded-4">
            <h5 class="fw-bold text-slate mb-3"><i class="bi bi-chat-quote-fill text-success me-2"></i>WhatsApp / SMS Templates</h5>
            
            <div class="accordion accordion-flush" id="templatesAccordion">
                <?php $i = 0; foreach ($templates as $key => $tmpl): $i++; ?>
                    <div class="accordion-item border rounded-3 mb-2 overflow-hidden">
                        <h2 class="accordion-header" id="heading<?= $i ?>">
                            <button class="accordion-button collapsed fw-bold text-slate py-2.5" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $i ?>">
                                <i class="bi bi-chat-left-text-fill text-success me-2"></i>
                                <?= esc($tmpl['name']) ?>
                                <span class="badge bg-light text-secondary border ms-auto me-2"><?= esc($tmpl['category']) ?></span>
                            </button>
                        </h2>
                        <div id="collapse<?= $i ?>" class="accordion-collapse collapse" data-bs-parent="#templatesAccordion">
                            <div class="accordion-body bg-light text-dark font-monospace small" style="white-space:pre-line;">
                                <?= esc($tmpl['body']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Quick Message Dispatcher Form -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <h5 class="fw-bold text-slate mb-3"><i class="bi bi-send-fill text-success me-2"></i>Send Quick WhatsApp Message</h5>
            
            <form id="quick-wa-form" onsubmit="event.preventDefault(); sendQuickWhatsApp();">
                <div class="mb-3">
                    <label for="wa_phone" class="form-label small fw-bold">Recipient Phone Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="wa_phone" required placeholder="+91 98765 43210">
                </div>

                <div class="mb-3">
                    <label for="wa_template" class="form-label small fw-bold">Select Template</label>
                    <select class="form-select" id="wa_template" onchange="populateTemplate(this.value)">
                        <option value="">-- Custom Message --</option>
                        <?php foreach ($templates as $key => $tmpl): ?>
                            <option value="<?= esc($tmpl['body']) ?>"><?= esc($tmpl['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="wa_message" class="form-label small fw-bold">Message Content <span class="text-danger">*</span></label>
                    <textarea class="form-control font-monospace" id="wa_message" rows="5" required placeholder="Type message or select a template above..."></textarea>
                </div>

                <button type="submit" class="btn btn-success w-100 py-2.5 fw-bold rounded-pill shadow-sm">
                    <i class="bi bi-whatsapp me-1"></i> Open 1-Click WhatsApp Chat
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Communication History Log -->
<div class="card border-0 shadow-sm p-4 rounded-4">
    <h5 class="fw-bold text-slate mb-3"><i class="bi bi-clock-history text-success me-2"></i>Recent Sent Logs</h5>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:0.85rem;">
            <thead class="bg-light">
                <tr>
                    <th>Log ID</th>
                    <th>Recipient</th>
                    <th>Template</th>
                    <th>Channel</th>
                    <th>Sent At</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No communication logs recorded yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $l): ?>
                        <tr>
                            <td class="fw-bold text-slate">#<?= esc((string)$l['id']) ?></td>
                            <td><?= esc($l['recipient_phone']) ?> <span class="text-muted small">(<?= esc($l['patient_name'] ?? 'Guest') ?>)</span></td>
                            <td><span class="badge bg-light text-dark border"><?= esc($l['template_name']) ?></span></td>
                            <td><span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25"><?= esc(strtoupper($l['channel'])) ?></span></td>
                            <td><?= esc(date('d M Y, h:i A', strtotime($l['sent_at']))) ?></td>
                            <td><span class="badge bg-success">SENT</span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function populateTemplate(text) {
    if (text) {
        document.getElementById('wa_message').value = text;
    }
}

function sendQuickWhatsApp() {
    const phone = document.getElementById('wa_phone').value.trim();
    const msg = document.getElementById('wa_message').value.trim();
    if (!phone || !msg) {
        alert('Please provide phone number and message text.');
        return;
    }
    let cleanPhone = phone.replace(/[^0-9]/g, '');
    if (!cleanPhone.startsWith('91') && cleanPhone.length === 10) {
        cleanPhone = '91' + cleanPhone;
    }
    const url = 'https://wa.me/' + cleanPhone + '?text=' + encodeURIComponent(msg);
    window.open(url, '_blank');
}
</script>

<?php include VIEWS_PATH . '/layout/reception_footer.php'; ?>
