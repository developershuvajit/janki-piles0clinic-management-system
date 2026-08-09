<?php
if (!defined('ROOT_PATH')) { exit('No direct script access allowed'); }

$user = \App\Helpers\Session::user();
$roleSlug = $user['role_slug'] ?? '';
$userId = (int)($user['id'] ?? 0);

if (empty($roleSlug) && $userId > 0) {
    $r = \App\Helpers\Database::row(
        "SELECT r.slug FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.id = :id",
        ['id' => $userId]
    );
    $roleSlug = $r['slug'] ?? '';
}

if ($roleSlug === 'receptionist') {
    include __DIR__ . '/reception_footer.php';
    return;
}
if ($roleSlug === 'doctor') {
    include __DIR__ . '/doctor_footer.php';
    return;
}
?>
            </div><!-- /.admin-content-inner -->

            <!-- ===== CLEAN ADMIN FOOTER - STICKY BOTTOM ===== -->
            <footer class="admin-footer" style="font-size:0.78rem; color:#6b7a8f; padding: 0.8rem 1.8rem; background: #fff; border-top: 1px solid #eef2f6; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; margin-top: auto; flex-shrink: 0;">
                <div>
                    &copy; <?= date('Y') ?> <span style="font-weight:600; color:#0b1a2b;">Janki Piles Clinic</span> 
                    <span style="color:#94a3b8;">&mdash;</span> Advanced Proctology Management System
                    <span style="margin-left:0.5rem; background:#e6f5ed; color:#0f7b4a; padding:0.1rem 0.6rem; border-radius:40px; font-size:0.6rem; font-weight:600; display:inline-flex; align-items:center; gap:0.2rem;">
                        <i class="bi bi-check-circle-fill" style="font-size:0.5rem;"></i> System Online
                    </span>
                </div>
                <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                    <span style="display: flex; align-items: center; gap: 0.3rem; font-size:0.7rem;">
                        <i class="bi bi-hdd-stack text-muted"></i> 
                        <span class="text-muted">v2.0.1</span>
                    </span>
                    <span style="display: flex; align-items: center; gap: 0.3rem; font-size:0.7rem;">
                        <i class="bi bi-clock text-muted"></i> 
                        <span id="footer-clock" style="color:#475569;"><?= date('h:i A') ?></span>
                    </span>
                    <span style="display: flex; align-items: center; gap: 0.3rem; font-size:0.7rem;">
                        <span style="display:inline-block; width:6px; height:6px; background:#22c55e; border-radius:50%;"></span>
                        <span style="color:#475569;"><?= esc(strtoupper($user['role'] ?? 'ADMIN')) ?></span>
                    </span>
                    <a href="<?= site_url('/admin/settings') ?>" style="color:#6b7a8f; text-decoration:none; font-size:0.7rem; transition:0.15s;" onmouseover="this.style.color='#2563eb'" onmouseout="this.style.color='#6b7a8f'">
                        <i class="bi bi-gear"></i>
                    </a>
                    <a href="<?= site_url() ?>" target="_blank" style="color:#6b7a8f; text-decoration:none; font-size:0.7rem; transition:0.15s;" onmouseover="this.style.color='#2563eb'" onmouseout="this.style.color='#6b7a8f'">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                </div>
            </footer>

        </main>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Application JS -->
    <script src="<?= asset('js/app.js') ?>"></script>

    <!-- Footer Clock Script -->
    <script>
        (function() {
            function updateClock() {
                var now = new Date();
                var hours = String(now.getHours()).padStart(2, '0');
                var minutes = String(now.getMinutes()).padStart(2, '0');
                var ampm = now.getHours() >= 12 ? 'PM' : 'AM';
                var hour12 = now.getHours() % 12 || 12;
                var clockEl = document.getElementById('footer-clock');
                if (clockEl) {
                    clockEl.textContent = String(hour12).padStart(2, '0') + ':' + minutes + ' ' + ampm;
                }
            }
            updateClock();
            setInterval(updateClock, 10000);
        })();
    </script>
</body>
</html>