<?php 
if (!defined('ROOT_PATH')) {
    exit('No direct script access allowed');
}
$activePage = 'dashboard';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- QR Code Display Layout -->
<div class="row">
    <!-- QR Code Output -->
    <div class="col-md-6 mb-4">
        <div class="card p-4 border-0 shadow-sm text-center">
            <h5 class="fw-bold text-slate text-start mb-3">QR Code Visual Output</h5>
            
            <div class="my-3 p-3 bg-light d-inline-block rounded border">
                <img src="<?= esc($qrUrl) ?>" alt="QR Code" class="img-fluid" style="width: 250px; height: 250px;">
            </div>
            
            <div class="mt-2 text-muted small text-truncate">
                QR Payload Data: <code class="bg-light px-2 py-1 border rounded"><?= esc($data) ?></code>
            </div>
        </div>
    </div>
    
    <!-- QR Customizer Input -->
    <div class="col-md-6 mb-4">
        <div class="card p-4 border-0 shadow-sm h-100 d-flex flex-column justify-content-between">
            <div>
                <h5 class="fw-bold text-slate mb-3">Generate Custom QR Code</h5>
                <p class="text-muted small">Input any text string, email, telephone number, or URL to compile a custom QR code instantly. The backend dynamically switches between local PHPQRCode generation and secure API fallbacks depending on offline libraries configuration.</p>
                
                <form action="<?= site_url('/admin/qr-test') ?>" method="GET" class="mt-3">
                    <div class="mb-3">
                        <label for="data" class="form-label small fw-semibold">QR Code Payload Link / Text</label>
                        <input type="text" class="form-control" id="data" name="data" value="<?= esc($data) ?>" required placeholder="e.g. https://google.com">
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-sm px-4 py-2 shadow-sm">
                        <i class="bi bi-qr-code me-1"></i> Generate QR Code
                    </button>
                    
                    <a href="<?= site_url('/admin/dashboard') ?>" class="btn btn-outline-secondary btn-sm px-3 py-2 ms-2">
                        Dashboard
                    </a>
                </form>
            </div>
            
            <div class="text-muted small pt-3 border-top mt-4">
                <i class="bi bi-info-circle me-1"></i> QR outputs automatically get stored under <code>assets/uploads/qr_codes/</code> when using offline generation.
            </div>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
