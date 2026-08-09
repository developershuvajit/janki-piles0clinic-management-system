        </div><!-- /.admin-content-inner -->

        <!-- ===== CLEAN DOCTOR FOOTER ===== -->
        <footer class="admin-footer mt-4 pt-3 border-top" style="font-size:0.78rem; color:#6b7a8f; padding: 0.8rem 1.8rem; background: #fff; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; margin-top: auto; flex-shrink: 0;">
            <div>
                &copy; <?= date('Y') ?> <span style="font-weight:600; color:#0b1a2b;">Janki Piles Clinic</span> 
                <span style="color:#94a3b8;">&mdash;</span> Doctor Console Module
                <span style="margin-left:0.5rem; background:#e6f0ff; color:#1a6bc4; padding:0.1rem 0.6rem; border-radius:40px; font-size:0.6rem; font-weight:600; display:inline-flex; align-items:center; gap:0.2rem;">
                    <i class="bi bi-check-circle-fill" style="font-size:0.5rem;"></i> Clinical Session Active
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
                    <span style="color:#475569;"><?= esc(strtoupper($user['role'] ?? 'DOCTOR')) ?></span>
                </span>
                <a href="<?= site_url('/doctor/profile') ?>" style="color:#6b7a8f; text-decoration:none; font-size:0.7rem; transition:0.15s;" onmouseover="this.style.color='#2563eb'" onmouseout="this.style.color='#6b7a8f'">
                    <i class="bi bi-person-circle"></i>
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

// Sidebar toggle for mobile
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('sidebar-toggle');
    const sidebar = document.querySelector('.sidebar-doctor');
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function() {
            if (sidebar.style.display === 'none' || sidebar.style.display === '') {
                sidebar.style.display = 'block';
            } else {
                sidebar.style.display = 'none';
            }
        });
    }
});
</script>
</body>
</html>