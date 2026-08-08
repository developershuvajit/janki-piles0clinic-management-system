/**
 * MedClinic Admin — Enhanced Application Scripts v2.0
 */

document.addEventListener('DOMContentLoaded', () => {

    // ----------------------------------------------------------------
    // AJAX Login Handler
    // ----------------------------------------------------------------
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const submitBtn      = loginForm.querySelector('button[type="submit"]');
            const alertContainer = document.getElementById('alert-container');
            const originalHtml   = submitBtn.innerHTML;

            alertContainer.innerHTML = '';
            alertContainer.className = 'alert d-none';

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner"></span> Authenticating...';

            try {
                const fd = new FormData(loginForm);
                fd.append('ajax', '1');

                const res  = await fetch(loginForm.action, {
                    method: 'POST',
                    body: fd,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                if (res.ok && data.success) {
                    alertContainer.className  = 'alert alert-success mt-3';
                    alertContainer.innerHTML  = '&#10004; ' + data.message;
                    alertContainer.classList.remove('d-none');
                    showLoader();
                    setTimeout(() => { window.location.href = data.redirect; }, 1000);
                } else {
                    alertContainer.className  = 'alert alert-danger mt-3';
                    alertContainer.innerHTML  = '&#9888; ' + (data.message || 'Login failed. Please check your credentials.');
                    alertContainer.classList.remove('d-none');
                    submitBtn.disabled  = false;
                    submitBtn.innerHTML = originalHtml;
                    // Shake effect
                    loginForm.classList.add('shake');
                    setTimeout(() => loginForm.classList.remove('shake'), 500);
                }
            } catch (err) {
                console.error('Login AJAX error:', err);
                alertContainer.className  = 'alert alert-danger mt-3';
                alertContainer.innerHTML  = '&#9888; Connection failure. Please check your network connection.';
                alertContainer.classList.remove('d-none');
                submitBtn.disabled  = false;
                submitBtn.innerHTML = originalHtml;
            }
        });
    }

    // ----------------------------------------------------------------
    // Auto-Dismiss Flash Alerts (5 seconds)
    // ----------------------------------------------------------------
    document.querySelectorAll('.alert-dismiss-flash').forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            el.style.opacity    = '0';
            el.style.transform  = 'translateY(-8px)';
            setTimeout(() => el.remove(), 500);
        }, 5000);
    });

    // ----------------------------------------------------------------
    // Global Page Loader (on link/form navigation)
    // ----------------------------------------------------------------
    document.querySelectorAll('a[data-loader]').forEach(el => {
        el.addEventListener('click', () => showLoader());
    });

    // ----------------------------------------------------------------
    // Confirm Dialog (data-confirm attribute)
    // ----------------------------------------------------------------
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', (e) => {
            const msg = el.dataset.confirm || 'Are you sure you want to perform this action?';
            if (!confirm(msg)) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });

    // ----------------------------------------------------------------
    // Real-time Form Validation Enhancement
    // ----------------------------------------------------------------
    document.querySelectorAll('.needs-validation').forEach(form => {
        form.addEventListener('submit', (e) => {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // ----------------------------------------------------------------
    // Print Trigger
    // ----------------------------------------------------------------
    document.querySelectorAll('[data-print]').forEach(btn => {
        btn.addEventListener('click', () => window.print());
    });

    // ----------------------------------------------------------------
    // Sidebar Active State Highlight
    // ----------------------------------------------------------------
    const currentPath = window.location.pathname;
    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
        const href = link.getAttribute('href');
        if (href && href !== '#' && currentPath.startsWith(href.replace(/\/clinic\/public/, ''))) {
            link.classList.add('active');
        }
    });

    // ----------------------------------------------------------------
    // Toast Notifications (triggered by data attribute)
    // ----------------------------------------------------------------
    document.querySelectorAll('[data-toast]').forEach(el => {
        const type = el.dataset.toastType || 'info';
        const msg  = el.dataset.toast;
        if (msg) showToast(msg, type);
    });

    // ----------------------------------------------------------------
    // Number Counters for Stat Cards (animated)
    // ----------------------------------------------------------------
    document.querySelectorAll('[data-count]').forEach(el => {
        const target  = parseInt(el.dataset.count, 10) || 0;
        const prefix  = el.dataset.prefix  || '';
        const suffix  = el.dataset.suffix  || '';
        const dur     = 1200;
        const start   = performance.now();

        const step = (now) => {
            const pct = Math.min((now - start) / dur, 1);
            const val = Math.round(easeOut(pct) * target);
            el.textContent = prefix + val.toLocaleString('en-IN') + suffix;
            if (pct < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    });

    // ----------------------------------------------------------------
    // Date/Time Display
    // ----------------------------------------------------------------
    const clockEl = document.getElementById('live-clock');
    if (clockEl) {
        const tick = () => {
            const now = new Date();
            clockEl.textContent = now.toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit' });
        };
        tick();
        setInterval(tick, 60000);
    }

    // ----------------------------------------------------------------
    // Mobile Sidebar Toggle
    // ----------------------------------------------------------------
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar       = document.querySelector('.sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-open');
        });
    }

}); // DOMContentLoaded

// ----------------------------------------------------------------
// Utilities
// ----------------------------------------------------------------
function easeOut(t) { return t * (2 - t); }

function showLoader() {
    const loader = document.getElementById('page-loader');
    if (loader) loader.classList.add('active');
}

function hideLoader() {
    const loader = document.getElementById('page-loader');
    if (loader) loader.classList.remove('active');
}

function showToast(message, type = 'info', duration = 4000) {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast-msg ${type}`;
    toast.textContent = message;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
        toast.style.opacity    = '0';
        toast.style.transform  = 'translateX(30px)';
        setTimeout(() => toast.remove(), 400);
    }, duration);
}

// Expose globally
window.showToast  = showToast;
window.showLoader = showLoader;
window.hideLoader = hideLoader;
