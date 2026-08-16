<?php
// doctor_footer.php
$user = \App\Helpers\Session::user();
$roleSlug = $user['role_slug'] ?? $user['role'] ?? '';

if ($roleSlug !== 'doctor') {
    if ($roleSlug === 'receptionist') {
        include __DIR__ . '/reception_footer.php';
        return;
    }
    include __DIR__ . '/admin_footer.php';
    return;
}
?>
            </div><!-- /.admin-content-inner -->

            <!-- ===== DOCTOR FOOTER ===== -->
            <footer class="admin-footer">
                <div>
                    &copy; <?= date('Y') ?> <span style="font-weight:600; color:#0b1a2b;">Janki Piles Clinic</span> 
                    <span style="color:#94a3b8;">&mdash;</span> Doctor Console Module
                    <span style="margin-left:0.4rem; background:#e6f0ff; color:#1a6bc4; padding:0.05rem 0.5rem; border-radius:40px; font-size:0.55rem; font-weight:600; display:inline-flex; align-items:center; gap:0.2rem;">
                        <i class="bi bi-check-circle-fill" style="font-size:0.4rem;"></i> Clinical Session Active
                    </span>
                </div>
                <div style="display:flex;align-items:center;gap:0.6rem;flex-wrap:wrap;">
                    <span style="display:flex;align-items:center;gap:0.2rem;font-size:0.65rem;color:#94a3b8;">
                        <i class="bi bi-hdd-stack"></i> v2.0.1
                    </span>
                    <span style="display:flex;align-items:center;gap:0.2rem;font-size:0.65rem;color:#94a3b8;">
                        <i class="bi bi-clock"></i> 
                        <span id="footer-clock" style="color:#475569;"><?= date('h:i A') ?></span>
                    </span>
                    <span style="display:flex;align-items:center;gap:0.2rem;font-size:0.65rem;color:#94a3b8;">
                        <span style="display:inline-block;width:5px;height:5px;background:#22c55e;border-radius:50%;"></span>
                        <?= esc(strtoupper($user['role'] ?? 'DOCTOR')) ?>
                    </span>
                    <a href="<?= site_url('/doctor/profile') ?>" style="color:#94a3b8;text-decoration:none;font-size:0.65rem;transition:0.15s;" onmouseover="this.style.color='#2563eb'" onmouseout="this.style.color='#94a3b8'">
                        <i class="bi bi-person-circle"></i>
                    </a>
                    <a href="<?= site_url() ?>" target="_blank" style="color:#94a3b8;text-decoration:none;font-size:0.65rem;transition:0.15s;" onmouseover="this.style.color='#2563eb'" onmouseout="this.style.color='#94a3b8'">
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

        // ===== SIDEBAR TOGGLE =====
        function toggleSidebar() {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        }

        function closeSidebar() {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        }

        // Close sidebar on window resize (if becoming desktop)
        window.addEventListener('resize', function() {
            if (window.innerWidth > 992) {
                closeSidebar();
            }
        });

        // Close sidebar on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSidebar();
            }
        });

        // ===== PAGE LOADER =====
        window.addEventListener('load', function() {
            var loader = document.getElementById('page-loader');
            if (loader) {
                loader.classList.add('hide');
                setTimeout(function() {
                    loader.style.display = 'none';
                }, 400);
            }
        });
    </script>
</body>
</html>