<?php 
if (!defined('ROOT_PATH')) {
    exit('No direct script access allowed');
}
$activePage = 'settings';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<div class="card border-0 shadow-sm p-4">
    <form action="<?= site_url('/admin/settings/save') ?>" method="POST" autocomplete="off">
        <?= csrf_field() ?>

        <!-- SMTP Email Configurations -->
        <h5 class="fw-bold text-slate mb-3"><i class="bi bi-envelope-fill text-success me-2"></i>SMTP Configuration (Email Dispatcher)</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="smtp_host" class="form-label small fw-semibold">SMTP Host Address</label>
                <input type="text" class="form-control form-control-sm" id="smtp_host" name="smtp_host" value="<?= esc($settings['smtp_host'] ?? '') ?>" placeholder="e.g. smtp.mailtrap.io">
            </div>
            
            <div class="col-md-3">
                <label for="smtp_port" class="form-label small fw-semibold">SMTP Port Number</label>
                <input type="text" class="form-control form-control-sm" id="smtp_port" name="smtp_port" value="<?= esc($settings['smtp_port'] ?? '') ?>" placeholder="e.g. 587 or 2525">
            </div>
            
            <div class="col-md-3">
                <label for="smtp_secure" class="form-label small fw-semibold">Connection Protocol</label>
                <select class="form-control form-control-sm form-select" id="smtp_secure" name="smtp_secure">
                    <option value="none" <?= ($settings['smtp_secure'] ?? '') === 'none' ? 'selected' : '' ?>>None (Plain Text)</option>
                    <option value="tls" <?= ($settings['smtp_secure'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS (Recommended)</option>
                    <option value="ssl" <?= ($settings['smtp_secure'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                </select>
            </div>
            
            <div class="col-md-6">
                <label for="smtp_user" class="form-label small fw-semibold">SMTP Username</label>
                <input type="text" class="form-control form-control-sm" id="smtp_user" name="smtp_user" value="<?= esc($settings['smtp_user'] ?? '') ?>" placeholder="e.g. smtp_username_token">
            </div>
            
            <div class="col-md-6">
                <label for="smtp_pass" class="form-label small fw-semibold">SMTP Password</label>
                <input type="password" class="form-control form-control-sm" id="smtp_pass" name="smtp_pass" value="<?= esc($settings['smtp_pass'] ?? '') ?>" placeholder="SMTP Password Key">
            </div>
            
            <div class="col-md-6">
                <label for="smtp_from_email" class="form-label small fw-semibold">Sender Email Address</label>
                <input type="email" class="form-control form-control-sm" id="smtp_from_email" name="smtp_from_email" value="<?= esc($settings['smtp_from_email'] ?? '') ?>" placeholder="e.g. no-reply@clinic.com">
            </div>
            
            <div class="col-md-6">
                <label for="smtp_from_name" class="form-label small fw-semibold">Sender Display Name</label>
                <input type="text" class="form-control form-control-sm" id="smtp_from_name" name="smtp_from_name" value="<?= esc($settings['smtp_from_name'] ?? '') ?>" placeholder="e.g. MedClinic Dispatcher">
            </div>
        </div>

        <hr class="my-4">

        <!-- WhatsApp SMS Configurations -->
        <h5 class="fw-bold text-slate mb-3"><i class="bi bi-whatsapp text-success me-2"></i>WhatsApp Integration Gateway</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="whatsapp_api_url" class="form-label small fw-semibold">API Gateway URL Endpoint</label>
                <input type="text" class="form-control form-control-sm" id="whatsapp_api_url" name="whatsapp_api_url" value="<?= esc($settings['whatsapp_api_url'] ?? '') ?>" placeholder="e.g. https://api.ultramsg.com/v1/messages/chat">
            </div>
            
            <div class="col-md-6">
                <label for="whatsapp_api_key" class="form-label small fw-semibold">API Secret Token Key</label>
                <input type="password" class="form-control form-control-sm" id="whatsapp_api_key" name="whatsapp_api_key" value="<?= esc($settings['whatsapp_api_key'] ?? '') ?>" placeholder="Authorization Bearer Token or Instance Key">
            </div>
            
            <div class="col-md-6">
                <label for="whatsapp_sender_number" class="form-label small fw-semibold">Sender Phone Number / Instance ID</label>
                <input type="text" class="form-control form-control-sm" id="whatsapp_sender_number" name="whatsapp_sender_number" value="<?= esc($settings['whatsapp_sender_number'] ?? '') ?>" placeholder="e.g. +919876543210 or instance9948">
            </div>
        </div>

        <!-- Submit Button -->
        <div class="text-end pt-3">
            <button type="submit" class="btn btn-primary px-4 shadow-sm py-2">
                <i class="bi bi-save-fill me-1"></i> Save Configuration Settings
            </button>
        </div>
    </form>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
