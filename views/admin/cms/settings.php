<?php 
$activePage = 'cms_settings';
include VIEWS_PATH . '/layout/admin_header.php'; 

$faqs = [];
if (!empty($settings['faqs_json'])) {
    $faqs = json_decode($settings['faqs_json'], true) ?: [];
}
?>

<div class="card border-0 shadow-sm p-4 text-slate">
    <form action="<?= site_url('/admin/cms/settings/save') ?>" method="POST">
        <?= csrf_field() ?>

        <h5 class="fw-bold text-slate mb-4"><i class="bi bi-window-sidebar text-success me-2"></i>Website Layout & Configuration Settings</h5>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Website Brand Name</label>
                <input type="text" class="form-control form-control-sm" name="settings[site_name]" value="<?= esc($settings['site_name'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Helpline Contact Phone</label>
                <input type="text" class="form-control form-control-sm" name="settings[contact_phone]" value="<?= esc($settings['contact_phone'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Support Email Address</label>
                <input type="email" class="form-control form-control-sm" name="settings[contact_email]" value="<?= esc($settings['contact_email'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Clinic Physical Address</label>
                <input type="text" class="form-control form-control-sm" name="settings[contact_address]" value="<?= esc($settings['contact_address'] ?? '') ?>">
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label small fw-semibold">Google Maps Embed Link (iframe src)</label>
            <textarea class="form-control form-control-sm" name="settings[map_link]" rows="2" placeholder="https://www.google.com/maps/embed?..."><?= esc($settings['map_link'] ?? '') ?></textarea>
        </div>

        <hr class="border-light my-4">

        <h6 class="fw-bold mb-3"><i class="bi bi-search text-success me-2"></i>Global SEO Settings</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Default Meta Title</label>
                <input type="text" class="form-control form-control-sm" name="settings[meta_title]" value="<?= esc($settings['meta_title'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Default Meta Description</label>
                <input type="text" class="form-control form-control-sm" name="settings[meta_description]" value="<?= esc($settings['meta_description'] ?? '') ?>">
            </div>
        </div>

        <hr class="border-light my-4">

        <!-- FAQ Repeating Panel -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0"><i class="bi bi-question-circle text-success me-2"></i>Frequently Asked Questions (FAQ) Manager</h6>
            <button type="button" class="btn btn-outline-success btn-sm px-3" id="btn-add-faq">
                <i class="bi bi-plus-circle me-1"></i> Add FAQ Row
            </button>
        </div>

        <div id="faq-rows-container" class="mb-4 d-flex flex-column gap-3">
            <?php if (empty($faqs)): ?>
                <div class="text-muted small no-faq-msg">No FAQs defined. Click "Add FAQ Row" to start.</div>
            <?php else: ?>
                <?php foreach ($faqs as $index => $faq): ?>
                    <div class="faq-row-item card border p-3 bg-light position-relative">
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-2 btn-remove-faq" style="font-size: 0.75rem;"></button>
                        <div class="row g-2">
                            <div class="col-12 mb-2">
                                <label class="form-label small fw-semibold mb-1">Question</label>
                                <input type="text" class="form-control form-control-sm" name="faqs[<?= $index ?>][q]" value="<?= esc($faq['q']) ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold mb-1">Answer</label>
                                <textarea class="form-control form-control-sm" name="faqs[<?= $index ?>][a]" rows="2" required><?= esc($faq['a']) ?></textarea>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="text-end pt-3 border-top">
            <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
                <i class="bi bi-save me-1"></i> Save CMS Settings
            </button>
        </div>
    </form>
</div>

<!-- FAQ dynamic add scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('faq-rows-container');
    const addBtn = document.getElementById('btn-add-faq');
    let faqIndex = container.querySelectorAll('.faq-row-item').length;

    addBtn.addEventListener('click', function() {
        const noMsg = container.querySelector('.no-faq-msg');
        if (noMsg) noMsg.remove();

        const row = document.createElement('div');
        row.className = 'faq-row-item card border p-3 bg-light position-relative';
        row.innerHTML = `
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2 btn-remove-faq" style="font-size: 0.75rem;"></button>
            <div class="row g-2">
                <div class="col-12 mb-2">
                    <label class="form-label small fw-semibold mb-1">Question</label>
                    <input type="text" class="form-control form-control-sm" name="faqs[${faqIndex}][q]" required placeholder="Enter question...">
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold mb-1">Answer</label>
                    <textarea class="form-control form-control-sm" name="faqs[${faqIndex}][a]" rows="2" required placeholder="Enter answer..."></textarea>
                </div>
            </div>
        `;
        container.appendChild(row);
        faqIndex++;
    });

    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-remove-faq')) {
            e.target.closest('.faq-row-item').remove();
            if (container.querySelectorAll('.faq-row-item').length === 0) {
                container.innerHTML = '<div class="text-muted small no-faq-msg">No FAQs defined. Click "Add FAQ Row" to start.</div>';
            }
        }
    });
});
</script>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
