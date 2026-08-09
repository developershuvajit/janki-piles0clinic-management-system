<?php
if (!defined('ROOT_PATH')) { exit('No direct script access allowed'); }
?>
</div><!-- /.admin-content-inner -->

<!-- ===== CLEAN RECEPTION FOOTER ===== -->
<footer class="admin-footer mt-4 pt-3 border-top" style="font-size:0.78rem; color:#6b7a8f; padding: 0.8rem 1.8rem; background: #fff; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; margin-top: auto; flex-shrink: 0;">
    <div>
        &copy; <?= date('Y') ?> <span style="font-weight:600; color:#0b1a2b;">Janki Piles Clinic</span> 
        <span style="color:#94a3b8;">&mdash;</span> Reception &amp; OPD Operations Module
        <span style="margin-left:0.5rem; background:#e6f5ed; color:#0f7b4a; padding:0.1rem 0.6rem; border-radius:40px; font-size:0.6rem; font-weight:600; display:inline-flex; align-items:center; gap:0.2rem;">
            <i class="bi bi-check-circle-fill" style="font-size:0.5rem;"></i> Branch Console Active
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
            <span style="color:#475569;"><?= esc(strtoupper($user['role'] ?? 'RECEPTION')) ?></span>
        </span>
        <a href="<?= site_url('/reception/profile') ?>" style="color:#6b7a8f; text-decoration:none; font-size:0.7rem; transition:0.15s;" onmouseover="this.style.color='#2563eb'" onmouseout="this.style.color='#6b7a8f'">
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

// Global Search & Keyboard Shortcuts
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('global-search-input');
    const searchResults = document.getElementById('global-search-results');

    if (searchInput && searchResults) {
        let debounceTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const q = this.value.trim();
            if (q.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch('<?= site_url("/reception/search") ?>?q=' + encodeURIComponent(q))
                    .then(res => res.json())
                    .then(data => {
                        let html = '';
                        if (data.patients && data.patients.length > 0) {
                            html += '<div class="p-2 bg-light fw-bold small text-muted text-uppercase">Patients</div>';
                            data.patients.forEach(p => {
                                html += `<a href="<?= site_url('/reception/patients/history/') ?>${p.id}" class="text-decoration-none text-dark d-block global-search-item">
                                            <div class="fw-bold">${p.name} (${p.code})</div>
                                            <div class="small text-muted"><i class="bi bi-telephone me-1"></i>${p.phone}</div>
                                         </a>`;
                            });
                        }
                        if (data.appointments && data.appointments.length > 0) {
                            html += '<div class="p-2 bg-light fw-bold small text-muted text-uppercase">Appointments / Tokens</div>';
                            data.appointments.forEach(a => {
                                html += `<a href="<?= site_url('/reception/queues') ?>" class="text-decoration-none text-dark d-block global-search-item">
                                            <div class="fw-bold">Token #${a.token_number} - ${a.patient_name}</div>
                                            <div class="small text-muted">Doctor: Dr. ${a.doctor_name} | Date: ${a.date}</div>
                                         </a>`;
                            });
                        }
                        if (data.leads && data.leads.length > 0) {
                            html += '<div class="p-2 bg-light fw-bold small text-muted text-uppercase">CRM Leads</div>';
                            data.leads.forEach(l => {
                                html += `<a href="<?= site_url('/reception/leads') ?>" class="text-decoration-none text-dark d-block global-search-item">
                                            <div class="fw-bold">${l.name} (${l.phone})</div>
                                            <div class="small text-muted">Source: ${l.source} | Status: ${l.status}</div>
                                         </a>`;
                            });
                        }

                        if (!html) {
                            html = '<div class="p-3 text-center text-muted small">No matching patients or records found.</div>';
                        }

                        searchResults.innerHTML = html;
                        searchResults.style.display = 'block';
                    });
            }, 300);
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });
    }

    // Global Keyboard Shortcuts (Alt+S for Search, Alt+N for New Patient)
    document.addEventListener('keydown', function(e) {
        if (e.altKey && e.key.toLowerCase() === 's') {
            e.preventDefault();
            if (searchInput) searchInput.focus();
        }
        if (e.altKey && e.key.toLowerCase() === 'n') {
            e.preventDefault();
            window.location.href = '<?= site_url("/reception/patients/create") ?>';
        }
    });
});
</script>
</body>
</html>