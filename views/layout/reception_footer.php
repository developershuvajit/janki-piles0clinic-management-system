            <footer class="mt-5 pt-3 border-top text-center" style="font-size:0.78rem; color:var(--text-muted);">
                &copy; <?= date('Y') ?> Janki Piles Clinic &mdash; Reception &amp; OPD Operations Module &mdash;
                <span class="text-success fw-500">Branch Console Active</span>
            </footer>
        </main>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Application JS -->
    <script src="<?= asset('js/app.js') ?>"></script>

    <!-- Global Search & Keyboard Shortcuts -->
    <script>
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
