<?php 
$activePage = 'cms_blogs';
include VIEWS_PATH . '/layout/admin_header.php'; 

// Fetch categories to populate dropdown
$categories = \App\Models\Blog::getCategories();
?>

<div class="row text-slate">
    <!-- Left: Create/Edit Blog Form -->
    <div class="col-lg-5 mb-4">
        <div class="card border-0 shadow-sm p-4">
            <h6 class="fw-bold mb-3" id="form-title"><i class="bi bi-pencil-square text-success me-2"></i>Publish New Article</h6>
            
            <form action="<?= site_url('/admin/cms/blogs/save') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="blog-id" value="0">
                <input type="hidden" name="existing_image" id="blog-existing-image" value="">

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Article Title *</label>
                    <input type="text" class="form-control form-control-sm" name="title" id="blog-title" required placeholder="e.g. 5 Tips to Maintain Heart Health">
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Category</label>
                        <select class="form-control form-control-sm form-select" name="category_id" id="blog-category">
                            <option value="">None / Uncategorized</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= esc($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Status *</label>
                        <select class="form-control form-control-sm form-select" name="status" id="blog-status" required>
                            <option value="draft">Draft / Hidden</option>
                            <option value="published">Published / Public</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Tags (comma separated)</label>
                    <input type="text" class="form-control form-control-sm" name="tags" id="blog-tags" placeholder="e.g. heart, wellness, diet">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Upload Image Cover</label>
                    <input type="file" class="form-control form-control-sm" name="image" accept="image/*">
                    <div id="blog-image-preview-container" class="mt-2 d-none">
                        <span class="x-small text-muted d-block mb-1">Current Cover Image:</span>
                        <img src="" id="blog-image-preview" style="max-height: 80px; border-radius: 4px;">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Article Content *</label>
                    <textarea class="form-control form-control-sm" name="content" id="blog-content" rows="6" required placeholder="Write body text here..."></textarea>
                </div>

                <hr class="border-light my-3">
                
                <h6 class="fw-bold small mb-2 text-muted">SEO Optimizations</h6>
                <div class="mb-2">
                    <label class="form-label x-small fw-semibold">SEO Title</label>
                    <input type="text" class="form-control form-control-sm" name="seo_title" id="blog-seo-title" placeholder="Search listing title...">
                </div>
                <div class="mb-4">
                    <label class="form-label x-small fw-semibold">SEO Meta Description</label>
                    <textarea class="form-control form-control-sm" name="seo_description" id="blog-seo-desc" rows="2" placeholder="Search listing snippet..."></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 d-none" id="btn-cancel-edit">Cancel Edit</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm ms-auto">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Save Post
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right: Blogs Directory -->
    <div class="col-lg-7 mb-4">
        <div class="card border-0 shadow-sm p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-list-columns text-success me-2"></i>Published Health Articles</h6>
            
            <div class="table-responsive border-0 shadow-none">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.82rem;">
                    <thead>
                        <tr>
                            <th>Blog Details</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($blogs)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No blog articles published.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($blogs as $b): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-slate"><?= esc($b['title']) ?></div>
                                        <span class="text-muted small">Slug: /blog/<?= esc($b['slug']) ?></span>
                                    </td>
                                    <td><span class="badge bg-light text-slate border"><?= esc($b['category_name'] ?: 'Uncategorized') ?></span></td>
                                    <td>
                                        <?php if ($b['status'] === 'published'): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Published</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">Draft</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted"><?= esc(date('Y-m-d', strtotime($b['created_at']))) ?></td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary py-0.5 px-2 btn-edit" 
                                                data-id="<?= $b['id'] ?>"
                                                data-title="<?= esc($b['title']) ?>"
                                                data-cat="<?= esc($b['category_id'] ?? '') ?>"
                                                data-tags="<?= esc($b['tags'] ?? '') ?>"
                                                data-status="<?= esc($b['status']) ?>"
                                                data-img="<?= esc($b['image_url'] ?? '') ?>"
                                                data-content="<?= esc($b['content']) ?>"
                                                data-seotitle="<?= esc($b['seo_title'] ?? '') ?>"
                                                data-seodesc="<?= esc($b['seo_description'] ?? '') ?>">
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
            document.getElementById('form-title').innerHTML = '<i class="bi bi-pencil-square text-primary me-2"></i>Edit Article';
            document.getElementById('blog-id').value = this.dataset.id;
            document.getElementById('blog-title').value = this.dataset.title;
            document.getElementById('blog-category').value = this.dataset.cat;
            document.getElementById('blog-status').value = this.dataset.status;
            document.getElementById('blog-tags').value = this.dataset.tags;
            document.getElementById('blog-content').value = this.dataset.content;
            document.getElementById('blog-seo-title').value = this.dataset.seotitle;
            document.getElementById('blog-seo-desc').value = this.dataset.seodesc;
            document.getElementById('blog-existing-image').value = this.dataset.img;

            const previewContainer = document.getElementById('blog-image-preview-container');
            const previewImg = document.getElementById('blog-image-preview');
            if (this.dataset.img) {
                previewImg.src = '<?= site_url() ?>' + this.dataset.img;
                previewContainer.classList.remove('d-none');
            } else {
                previewContainer.classList.add('d-none');
            }

            cancelBtn.classList.remove('d-none');
        });
    });

    cancelBtn.addEventListener('click', function() {
        document.getElementById('form-title').innerHTML = '<i class="bi bi-pencil-square text-success me-2"></i>Publish New Article';
        document.getElementById('blog-id').value = '0';
        document.getElementById('blog-title').value = '';
        document.getElementById('blog-category').value = '';
        document.getElementById('blog-status').value = 'draft';
        document.getElementById('blog-tags').value = '';
        document.getElementById('blog-content').value = '';
        document.getElementById('blog-seo-title').value = '';
        document.getElementById('blog-seo-desc').value = '';
        document.getElementById('blog-existing-image').value = '';
        document.getElementById('blog-image-preview-container').classList.add('d-none');
        cancelBtn.classList.add('d-none');
    });
});
</script>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
