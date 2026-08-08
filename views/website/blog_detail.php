<?php include VIEWS_PATH . '/layout/public_header.php'; ?>

<section class="py-5 bg-gradient-hero">
    <div class="container py-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="<?= site_url('/blog') ?>" class="text-success text-decoration-none">Blogs</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= esc($blog['title']) ?></li>
            </ol>
        </nav>
        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1 mb-2 small"><?= esc($blog['category_name'] ?: 'General') ?></span>
        <h1 class="fw-bold text-slate"><?= esc($blog['title']) ?></h1>
        <div class="text-muted small mt-2"><i class="bi bi-clock me-1"></i> Published: <?= esc(date('F d, Y', strtotime($blog['created_at']))) ?></div>
    </div>
</section>

<section class="py-5 bg-white text-slate">
    <div class="container">
        <div class="row g-4">
            <!-- Left Column: Article & Comments -->
            <div class="col-lg-8">
                <?php if ($blog['image_url']): ?>
                    <img src="<?= site_url($blog['image_url']) ?>" class="img-fluid rounded-4 mb-4 shadow-sm" alt="<?= esc($blog['title']) ?>" style="max-height: 400px; width: 100%; object-fit: cover;">
                <?php endif; ?>

                <div class="article-content mb-5" style="line-height: 1.7; font-size: 1.05rem;">
                    <?= $blog['content'] ?>
                </div>

                <!-- Tags -->
                <?php if ($blog['tags']): ?>
                    <div class="mb-4">
                        <?php foreach (explode(',', $blog['tags']) as $tag): ?>
                            <span class="badge bg-light text-slate border me-1">#<?= esc(trim($tag)) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <hr class="border-light my-5">

                <!-- Comments section -->
                <div class="mb-5">
                    <h5 class="fw-bold text-slate mb-4"><i class="bi bi-chat-left-text text-success me-2"></i>Patient Discussion (<?= count($comments) ?>)</h5>

                    <?php if (Session::getFlash('success')): ?>
                        <div class="alert alert-success small mb-4"><?= esc(Session::getFlash('success')) ?></div>
                    <?php endif; ?>

                    <div class="d-flex flex-column gap-3 mb-4">
                        <?php if (empty($comments)): ?>
                            <div class="small text-muted">No comments posted yet. Start the discussion below!</div>
                        <?php else: ?>
                            <?php foreach ($comments as $comment): ?>
                                <div class="bg-light p-3 rounded-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong class="small text-slate"><?= esc($comment['author_name']) ?></strong>
                                        <span class="text-muted x-small"><?= esc(date('M d, Y', strtotime($comment['created_at']))) ?></span>
                                    </div>
                                    <p class="mb-0 small text-muted"><?= esc($comment['comment_text']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Post Comment Form -->
                    <div class="card border-0 shadow-sm p-4">
                        <h6 class="fw-bold text-slate mb-3">Leave a Reply</h6>
                        
                        <form action="<?= site_url('/blog/comment/save') ?>" method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="blog_id" value="<?= $blog['id'] ?>">

                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Your Name *</label>
                                    <input type="text" class="form-control form-control-sm" name="author_name" required placeholder="e.g. John Doe">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Email Address *</label>
                                    <input type="email" class="form-control form-control-sm" name="author_email" required placeholder="e.g. john@email.com">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label small fw-semibold">Comment *</label>
                                <textarea class="form-control" name="comment_text" rows="3" required placeholder="Write your thoughts..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-success btn-sm px-4 rounded-pill">Post Comment</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Column: Sidebar -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4">
                    <h6 class="fw-bold text-slate mb-3"><i class="bi bi-bookmark-star text-success me-2"></i>Related Articles</h6>
                    
                    <?php if (empty($related)): ?>
                        <div class="small text-muted">No related posts found.</div>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($related as $rel): ?>
                                <div class="pb-2 border-bottom border-light">
                                    <a href="<?= site_url('/blog/' . $rel['slug']) ?>" class="fw-bold text-slate text-decoration-none small d-block mb-1 hover-success"><?= esc($rel['title']) ?></a>
                                    <span class="text-muted x-small"><?= esc(date('M d, Y', strtotime($rel['created_at']))) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include VIEWS_PATH . '/layout/public_footer.php'; ?>
