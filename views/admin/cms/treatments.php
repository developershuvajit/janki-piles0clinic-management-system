<?php 
$activePage = 'cms_treatments';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<div class="row text-slate">
    <!-- Left: Create/Edit Specialty Form -->
    <div class="col-lg-5 mb-4">
        <div class="card border-0 shadow-sm p-4">
            <h6 class="fw-bold mb-3" id="form-title"><i class="bi bi-pencil-square text-success me-2"></i>Publish Treatment Specialty</h6>
            
            <form action="<?= site_url('/admin/cms/treatments/save') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="treat-id" value="0">
                <input type="hidden" name="existing_image" id="treat-existing-image" value="">

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Treatment Title *</label>
                    <input type="text" class="form-control form-control-sm" name="title" id="treat-title" required placeholder="e.g. Root Canal Therapy">
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Consultation Fee (INR) *</label>
                        <input type="number" class="form-control form-control-sm" name="price" id="treat-price" required step="10.00" min="0.00" placeholder="0.00">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Status *</label>
                        <select class="form-control form-control-sm form-select" name="status" id="treat-status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive / Hidden</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Video Walkthrough URL</label>
                    <input type="text" class="form-control form-control-sm" name="video_url" id="treat-video-url" placeholder="https://www.youtube.com/embed/...">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Upload Image Cover</label>
                    <input type="file" class="form-control form-control-sm" name="image" accept="image/*">
                    <div id="treat-image-preview-container" class="mt-2 d-none">
                        <span class="x-small text-muted d-block mb-1">Current Cover:</span>
                        <img src="" id="treat-image-preview" style="max-height: 80px; border-radius: 4px;">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Attending Physicians Checklist</label>
                    <div class="border rounded p-2 bg-light d-flex flex-column gap-1.5" style="max-height: 120px; overflow-y: auto; font-size: 0.8rem;">
                        <?php foreach ($doctors as $doc): ?>
                            <div class="form-check">
                                <input class="form-check-input check-doc" type="checkbox" name="doctor_ids[]" value="<?= $doc['id'] ?>" id="doc-chk-<?= $doc['id'] ?>">
                                <label class="form-check-label" for="doc-chk-<?= $doc['id'] ?>">
                                    Dr. <?= esc($doc['username']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Treatment Content Details *</label>
                    <textarea class="form-control form-control-sm" name="content" id="treat-content" rows="4" required placeholder="Describe treatment services here..."></textarea>
                </div>

                <hr class="border-light my-3">
                
                <h6 class="fw-bold small mb-2 text-muted">SEO Optimizations</h6>
                <div class="mb-2">
                    <label class="form-label x-small fw-semibold">SEO Title</label>
                    <input type="text" class="form-control form-control-sm" name="seo_title" id="treat-seo-title" placeholder="Search listing title...">
                </div>
                <div class="mb-4">
                    <label class="form-label x-small fw-semibold">SEO Meta Description</label>
                    <textarea class="form-control form-control-sm" name="seo_description" id="treat-seo-desc" rows="2" placeholder="Search listing snippet..."></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 d-none" id="btn-cancel-edit">Cancel Edit</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm ms-auto">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Save Specialty
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right: Specialty Catalog -->
    <div class="col-lg-7 mb-4">
        <div class="card border-0 shadow-sm p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-list-stars text-success me-2"></i>Attending Specialties Catalog</h6>
            
            <div class="table-responsive border-0 shadow-none">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.82rem;">
                    <thead>
                        <tr>
                            <th>Specialty details</th>
                            <th>Consultation Fee</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($treatments)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No specialty treatments listed.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($treatments as $tr): ?>
                                <?php 
                                // Fetch doctor IDs assigned
                                $assigned = \App\Models\Treatment::getDoctors((int)$tr['id']);
                                $assignedIds = array_column($assigned, 'id');
                                ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-slate"><?= esc($tr['title']) ?></div>
                                        <span class="text-muted small">Slug: /treatments/<?= esc($tr['slug']) ?></span>
                                    </td>
                                    <td class="fw-bold">₹<?= esc(number_format((float)$tr['price'], 2)) ?></td>
                                    <td>
                                        <?php if ($tr['status'] === 'active'): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">Hidden</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary py-0.5 px-2 btn-edit" 
                                                data-id="<?= $tr['id'] ?>"
                                                data-title="<?= esc($tr['title']) ?>"
                                                data-price="<?= esc($tr['price']) ?>"
                                                data-status="<?= esc($tr['status']) ?>"
                                                data-vid="<?= esc($tr['video_url'] ?? '') ?>"
                                                data-img="<?= esc($tr['image_url'] ?? '') ?>"
                                                data-content="<?= esc($tr['content']) ?>"
                                                data-docs="<?= esc(json_encode($assignedIds)) ?>"
                                                data-seotitle="<?= esc($tr['seo_title'] ?? '') ?>"
                                                data-seodesc="<?= esc($tr['seo_description'] ?? '') ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editBtns = document.querySelectorAll('.btn-edit');
    const cancelBtn = document.getElementById('btn-cancel-edit');
    
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('form-title').innerHTML = '<i class="bi bi-pencil-square text-primary me-2"></i>Edit Specialty';
            document.getElementById('treat-id').value = this.dataset.id;
            document.getElementById('treat-title').value = this.dataset.title;
            document.getElementById('treat-price').value = this.dataset.price;
            document.getElementById('treat-status').value = this.dataset.status;
            document.getElementById('treat-video-url').value = this.dataset.vid;
            document.getElementById('treat-content').value = this.dataset.content;
            document.getElementById('treat-seo-title').value = this.dataset.seotitle;
            document.getElementById('treat-seo-desc').value = this.dataset.seodesc;
            document.getElementById('treat-existing-image').value = this.dataset.img;

            // Preview Image
            const previewContainer = document.getElementById('treat-image-preview-container');
            const previewImg = document.getElementById('treat-image-preview');
            if (this.dataset.img) {
                previewImg.src = '<?= site_url() ?>' + this.dataset.img;
                previewContainer.classList.remove('d-none');
            } else {
                previewContainer.classList.add('d-none');
            }

            // Checklist doctors
            const docsChecked = JSON.parse(this.dataset.docs);
            const checkboxes = document.querySelectorAll('.check-doc');
            checkboxes.forEach(chk => {
                chk.checked = docsChecked.includes(parseInt(chk.value));
            });

            cancelBtn.classList.remove('d-none');
        });
    });

    cancelBtn.addEventListener('click', function() {
        document.getElementById('form-title').innerHTML = '<i class="bi bi-pencil-square text-success me-2"></i>Publish Treatment Specialty';
        document.getElementById('treat-id').value = '0';
        document.getElementById('treat-title').value = '';
        document.getElementById('treat-price').value = '';
        document.getElementById('treat-status').value = 'active';
        document.getElementById('treat-video-url').value = '';
        document.getElementById('treat-content').value = '';
        document.getElementById('treat-seo-title').value = '';
        document.getElementById('treat-seo-desc').value = '';
        document.getElementById('treat-existing-image').value = '';
        document.getElementById('treat-image-preview-container').classList.add('d-none');
        
        const checkboxes = document.querySelectorAll('.check-doc');
        checkboxes.forEach(chk => chk.checked = false);

        cancelBtn.classList.add('d-none');
    });
});
</script>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
