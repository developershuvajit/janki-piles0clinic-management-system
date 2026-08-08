<?php 
$activePage = 'cms_gallery';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Gallery Header Actions -->
<div class="row mb-4 align-items-center text-slate">
    <div class="col-md-6 mb-2">
        <h5 class="fw-bold mb-1"><i class="bi bi-images text-success me-2"></i>Media Gallery Catalog</h5>
        <p class="text-muted small mb-0">Construct photo/video albums representing clinic rooms, laboratories, and patient lounges.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <button type="button" class="btn btn-outline-success btn-sm px-3 me-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#albumModal">
            <i class="bi bi-folder-plus me-1"></i> Create Album
        </button>
        <?php if ($selectedAlbumId > 0): ?>
            <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#mediaModal">
                <i class="bi bi-plus-circle me-1"></i> Add Media Asset
            </button>
        <?php endif; ?>
    </div>
</div>

<div class="row text-slate">
    <!-- Left Column: Albums List -->
    <div class="col-lg-3 mb-4">
        <div class="card border-0 shadow-sm p-4">
            <h6 class="fw-bold mb-3">Gallery Albums</h6>
            <div class="list-group list-group-flush small">
                <?php if (empty($albums)): ?>
                    <span class="text-muted small">No albums created.</span>
                <?php else: ?>
                    <?php foreach ($albums as $al): ?>
                        <a href="<?= site_url('/admin/cms/gallery?album_id=' . $al['id']) ?>" class="list-group-item list-group-item-action border-0 px-2 py-2 rounded-2 mb-1 fw-semibold <?= $selectedAlbumId === (int)$al['id'] ? 'bg-success bg-opacity-10 text-success' : 'text-slate' ?>">
                            <i class="bi bi-folder2-open me-2"></i> <?= esc($al['name']) ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Column: Media Grid -->
    <div class="col-lg-9 mb-4">
        <div class="card border-0 shadow-sm p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-images text-success me-2"></i>Media Assets in Album</h6>
            
            <?php if ($selectedAlbumId === 0): ?>
                <div class="text-center py-5 text-muted">Please select or create an album to manage media files.</div>
            <?php elseif (empty($media)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-camera fs-2 d-block mb-2"></i>
                    No media items loaded in this album. Click "Add Media Asset" to begin.
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($media as $item): ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="card h-100 border overflow-hidden position-relative">
                                <?php if ($item['type'] === 'photo'): ?>
                                    <img src="<?= site_url($item['url']) ?>" alt="Photo" style="height: 160px; object-fit: cover; width: 100%;">
                                <?php else: ?>
                                    <div class="bg-dark d-flex align-items-center justify-content-center" style="height: 160px;">
                                        <i class="bi bi-play-btn-fill text-danger fs-1"></i>
                                    </div>
                                <?php endif; ?>
                                <?php if ($item['caption']): ?>
                                    <div class="card-body p-2 bg-light text-center small text-truncate">
                                        <?= esc($item['caption']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal: Create Album -->
<div class="modal fade" id="albumModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg text-slate">
            <form action="<?= site_url('/admin/cms/gallery/album/save') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-folder-plus me-2"></i>Create Media Album</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Album Name *</label>
                        <input type="text" class="form-control form-control-sm" name="name" required placeholder="e.g. Clinic Interior">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Description</label>
                        <textarea class="form-control form-control-sm" name="description" rows="3" placeholder="Describe album contents..."></textarea>
                    </div>
                </div>
                <div class="modal-footer pt-0 border-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-4">Create Album</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Add Media -->
<div class="modal fade" id="mediaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg text-slate">
            <form action="<?= site_url('/admin/cms/gallery/media/save') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="album_id" value="<?= $selectedAlbumId ?>">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Add Media Asset</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Media Type *</label>
                        <select class="form-control form-control-sm form-select" name="type" id="media-type" required>
                            <option value="photo">Photo Upload</option>
                            <option value="video">Video Link (YouTube Embed)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="photo-field-container">
                        <label class="form-label small fw-semibold">Upload Photo File *</label>
                        <input type="file" class="form-control form-control-sm" name="file" id="photo-file" accept="image/*">
                    </div>

                    <div class="mb-3 d-none" id="video-field-container">
                        <label class="form-label small fw-semibold">Video URL (Embed Code Link) *</label>
                        <input type="text" class="form-control form-control-sm" name="video_url" id="video-url" placeholder="https://www.youtube.com/embed/...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Short Caption / Label</label>
                        <input type="text" class="form-control form-control-sm" name="caption" placeholder="e.g. Diagnostic Lab Room">
                    </div>
                </div>
                <div class="modal-footer pt-0 border-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">Add Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('media-type');
    const photoContainer = document.getElementById('photo-field-container');
    const videoContainer = document.getElementById('video-field-container');
    const photoFile = document.getElementById('photo-file');
    const videoUrl = document.getElementById('video-url');

    typeSelect.addEventListener('change', function() {
        if (this.value === 'photo') {
            photoContainer.classList.remove('d-none');
            videoContainer.classList.add('d-none');
            photoFile.setAttribute('required', 'required');
            videoUrl.removeAttribute('required');
        } else {
            photoContainer.classList.add('d-none');
            videoContainer.classList.remove('d-none');
            photoFile.removeAttribute('required');
            videoUrl.setAttribute('required', 'required');
        }
    });
});
</script>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
