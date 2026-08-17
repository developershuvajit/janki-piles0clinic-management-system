/**
 * MedClinic Admin — Enhanced Application Scripts v3.0
 *
 * Features:
 * ------------------------------------------------------------
 * ✓ AJAX Login Handler
 * ✓ Toast Notifications + Smart Sounds
 * ✓ Flash Alert Auto Dismiss
 * ✓ Global Page Loader
 * ✓ Confirm Dialog + Warning Sound
 * ✓ Form Validation + Warning Sound
 * ✓ Print Trigger + Click Sound
 * ✓ Sidebar Toggle
 * ✓ Number Counters
 * ✓ Live Clock
 * ✓ Smart Interactive Sound System
 * ✓ data-sound support
 * ✓ data-sound-type support
 * ✓ Custom notification events
 *
 * Notification sound engine:
 * notification.js
 *
 * IMPORTANT:
 * notification.js should be loaded before app.js
 */


/* ============================================================
   DOM READY
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {


    /* ========================================================
       HELPER — PLAY SOUND SAFELY
       ======================================================== */

    const playSound = (type = 'info') => {

        try {

            if (
                typeof window.playNotificationSound ===
                'function'
            ) {

                window.playNotificationSound(type);

            }

        } catch (error) {

            console.log(
                'Notification sound unavailable:',
                error
            );
        }
    };


    /* ========================================================
       HELPER — NORMALIZE SOUND TYPE
       ======================================================== */

    const getSoundType = (type = 'info') => {

        type = String(type || 'info').toLowerCase();

        switch (type) {

            case 'success':
                return 'success';

            case 'danger':
            case 'error':
                return 'error';

            case 'warning':
            case 'warn':
                return 'warning';

            case 'info':
            default:
                return 'info';
        }
    };


    /* ========================================================
       AJAX LOGIN HANDLER
       ======================================================== */

    const loginForm =
        document.getElementById('login-form');


    if (loginForm) {

        loginForm.addEventListener(
            'submit',
            async (e) => {

                e.preventDefault();


                const submitBtn =
                    loginForm.querySelector(
                        'button[type="submit"]'
                    );

                const alertContainer =
                    document.getElementById(
                        'alert-container'
                    );


                if (!submitBtn || !alertContainer) {
                    return;
                }


                const originalHtml =
                    submitBtn.innerHTML;


                alertContainer.innerHTML = '';

                alertContainer.className =
                    'alert d-none';


                submitBtn.disabled = true;

                submitBtn.innerHTML =
                    '<span class="spinner"></span> Authenticating...';


                try {

                    const fd =
                        new FormData(loginForm);

                    fd.append('ajax', '1');


                    const res =
                        await fetch(
                            loginForm.action,
                            {
                                method: 'POST',
                                body: fd,
                                headers: {
                                    'X-Requested-With':
                                        'XMLHttpRequest'
                                }
                            }
                        );


                    const data =
                        await res.json();


                    /* ----------------------------------------
                       LOGIN SUCCESS
                    ---------------------------------------- */

                    if (
                        res.ok &&
                        data.success
                    ) {

                        alertContainer.className =
                            'alert alert-success mt-3';

                        alertContainer.innerHTML =
                            '&#10004; ' +
                            data.message;

                        alertContainer.classList.remove(
                            'd-none'
                        );


                        // 🔊 Success sound
                        playSound('success');


                        showLoader();


                        setTimeout(
                            () => {

                                window.location.href =
                                    data.redirect;

                            },
                            1000
                        );


                    }


                    /* ----------------------------------------
                       LOGIN FAILED
                    ---------------------------------------- */

                    else {

                        alertContainer.className =
                            'alert alert-danger mt-3';

                        alertContainer.innerHTML =
                            '&#9888; ' +
                            (
                                data.message ||
                                'Login failed. Please check your credentials.'
                            );

                        alertContainer.classList.remove(
                            'd-none'
                        );


                        // 🔊 Error sound
                        playSound('error');


                        submitBtn.disabled = false;

                        submitBtn.innerHTML =
                            originalHtml;


                        // Shake effect
                        loginForm.classList.add(
                            'shake'
                        );


                        setTimeout(
                            () => {

                                loginForm.classList.remove(
                                    'shake'
                                );

                            },
                            500
                        );
                    }


                } catch (err) {

                    console.error(
                        'Login AJAX error:',
                        err
                    );


                    alertContainer.className =
                        'alert alert-danger mt-3';

                    alertContainer.innerHTML =
                        '&#9888; Connection failure. Please check your network connection.';

                    alertContainer.classList.remove(
                        'd-none'
                    );


                    // 🔊 Connection error sound
                    playSound('error');


                    submitBtn.disabled = false;

                    submitBtn.innerHTML =
                        originalHtml;
                }

            }
        );
    }



    /* ========================================================
       AUTO DISMISS FLASH ALERTS
       ======================================================== */

    document
        .querySelectorAll('.alert-dismiss-flash')
        .forEach(el => {

            setTimeout(
                () => {

                    el.style.transition =
                        'opacity 0.5s ease, transform 0.5s ease';

                    el.style.opacity = '0';

                    el.style.transform =
                        'translateY(-8px)';


                    setTimeout(
                        () => {

                            el.remove();

                        },
                        500
                    );

                },
                5000
            );

        });



    /* ========================================================
       GLOBAL PAGE LOADER
       ======================================================== */

    document
        .querySelectorAll('a[data-loader]')
        .forEach(el => {

            el.addEventListener(
                'click',
                () => {

                    showLoader();

                }
            );

        });



    /* ========================================================
       CONFIRM DIALOG
       ======================================================== */

    document
        .querySelectorAll('[data-confirm]')
        .forEach(el => {

            el.addEventListener(
                'click',
                (e) => {

                    const msg =
                        el.dataset.confirm ||
                        'Are you sure you want to perform this action?';


                    // 🔊 Warning sound before confirmation
                    playSound('warning');


                    if (!confirm(msg)) {

                        e.preventDefault();

                        e.stopPropagation();

                    }

                }
            );

        });



    /* ========================================================
       REAL-TIME FORM VALIDATION
       ======================================================== */

    document
        .querySelectorAll('.needs-validation')
        .forEach(form => {

            form.addEventListener(
                'submit',
                (e) => {

                    if (!form.checkValidity()) {

                        e.preventDefault();

                        e.stopPropagation();


                        // 🔊 Validation warning
                        playSound('warning');


                        // Focus first invalid field
                        const firstInvalid =
                            form.querySelector(
                                ':invalid'
                            );


                        if (firstInvalid) {

                            setTimeout(
                                () => {

                                    firstInvalid.focus();

                                },
                                50
                            );
                        }

                    }


                    form.classList.add(
                        'was-validated'
                    );

                }
            );

        });



    /* ========================================================
       PRINT TRIGGER
       ======================================================== */

    document
        .querySelectorAll('[data-print]')
        .forEach(btn => {

            btn.addEventListener(
                'click',
                () => {

                    // 🔊 Small info sound
                    playSound('info');


                    setTimeout(
                        () => {

                            window.print();

                        },
                        100
                    );

                }
            );

        });



    /* ========================================================
       SIDEBAR ACTIVE STATE
       ======================================================== */

    const currentPath =
        window.location.pathname;


    document
        .querySelectorAll(
            '.sidebar .nav-link'
        )
        .forEach(link => {

            const href =
                link.getAttribute('href');


            if (
                href &&
                href !== '#' &&
                currentPath.startsWith(
                    href.replace(
                        /\/clinic\/public/,
                        ''
                    )
                )
            ) {

                link.classList.add(
                    'active'
                );

            }

        });



    /* ========================================================
       TOAST NOTIFICATIONS
       ======================================================== */

    document
        .querySelectorAll('[data-toast]')
        .forEach(el => {

            const type =
                el.dataset.toastType ||
                'info';

            const msg =
                el.dataset.toast;


            if (msg) {

                showToast(
                    msg,
                    type
                );

            }

        });



    /* ========================================================
       NUMBER COUNTERS
       ======================================================== */

    document
        .querySelectorAll('[data-count]')
        .forEach(el => {

            const target =
                parseInt(
                    el.dataset.count,
                    10
                ) || 0;

            const prefix =
                el.dataset.prefix ||
                '';

            const suffix =
                el.dataset.suffix ||
                '';

            const dur = 1200;

            const start =
                performance.now();


            const step = (now) => {

                const pct =
                    Math.min(
                        (now - start) /
                            dur,
                        1
                    );


                const val =
                    Math.round(
                        easeOut(pct) *
                        target
                    );


                el.textContent =
                    prefix +
                    val.toLocaleString(
                        'en-IN'
                    ) +
                    suffix;


                if (pct < 1) {

                    requestAnimationFrame(
                        step
                    );

                }

            };


            requestAnimationFrame(
                step
            );

        });



    /* ========================================================
       LIVE DATE / TIME
       ======================================================== */

    const clockEl =
        document.getElementById(
            'live-clock'
        );


    if (clockEl) {

        const tick = () => {

            const now =
                new Date();


            clockEl.textContent =
                now.toLocaleTimeString(
                    'en-IN',
                    {
                        hour: '2-digit',
                        minute: '2-digit'
                    }
                );

        };


        tick();


        setInterval(
            tick,
            60000
        );

    }



    /* ========================================================
       MOBILE SIDEBAR TOGGLE
       ======================================================== */

    const sidebarToggle =
        document.getElementById(
            'sidebar-toggle'
        );

    const sidebar =
        document.querySelector(
            '.sidebar'
        );


    if (
        sidebarToggle &&
        sidebar
    ) {

        sidebarToggle.addEventListener(
            'click',
            () => {

                sidebar.classList.toggle(
                    'sidebar-open'
                );

            }
        );

    }



    /* ========================================================
       DATA-SOUND ELEMENTS
       ========================================================

       Example:

       <button
           data-sound
           data-sound-type="success">
           Save
       </button>

    */

    document
        .querySelectorAll('[data-sound]')
        .forEach(el => {

            el.addEventListener(
                'click',
                () => {

                    const type =
                        el.dataset.soundType ||
                        'info';


                    playSound(
                        getSoundType(type)
                    );

                }
            );

        });



    /* ========================================================
       SUCCESS ACTIONS
       ========================================================

       Example:

       <button
           data-action-success>
           Save
       </button>

    */

    document
        .querySelectorAll(
            '[data-action-success]'
        )
        .forEach(el => {

            el.addEventListener(
                'click',
                () => {

                    playSound(
                        'success'
                    );

                }
            );

        });



    /* ========================================================
       ERROR ACTIONS
       ========================================================

       Example:

       <button
           data-action-error>
           Error
       </button>

    */

    document
        .querySelectorAll(
            '[data-action-error]'
        )
        .forEach(el => {

            el.addEventListener(
                'click',
                () => {

                    playSound(
                        'error'
                    );

                }
            );

        });



    /* ========================================================
       CUSTOM SOUND EVENTS
       ========================================================

       From anywhere:

       window.dispatchEvent(
           new CustomEvent(
               'medclinic:notification',
               {
                   detail: {
                       type: 'success'
                   }
               }
           )
       );

    */

    window.addEventListener(
        'medclinic:notification',
        event => {

            const type =
                event.detail?.type ||
                'info';


            playSound(
                getSoundType(type)
            );

        }
    );



    /* ========================================================
       PAGE READY
       ======================================================== */

    console.log(
        'MedClinic Admin app.js initialized successfully.'
    );

});



/* ============================================================
   UTILITIES
   ============================================================ */


/**
 * Ease-out animation
 */
function easeOut(t) {

    return t * (2 - t);

}



/* ============================================================
   PAGE LOADER
   ============================================================ */

function showLoader() {

    const loader =
        document.getElementById(
            'page-loader'
        );


    if (loader) {

        loader.classList.add(
            'active'
        );

    }

}


function hideLoader() {

    const loader =
        document.getElementById(
            'page-loader'
        );


    if (loader) {

        loader.classList.remove(
            'active'
        );

    }

}



/* ============================================================
   TOAST SYSTEM
   ============================================================ */

function showToast(
    message,
    type = 'info',
    duration = 4000
) {

    let container =
        document.querySelector(
            '.toast-container'
        );


    if (!container) {

        container =
            document.createElement(
                'div'
            );

        container.className =
            'toast-container';

        document.body.appendChild(
            container
        );

    }



    /* --------------------------------------------------------
       Create Toast
    -------------------------------------------------------- */

    const toast =
        document.createElement(
            'div'
        );


    toast.className =
        `toast-msg ${type}`;


    toast.textContent =
        message;


    container.appendChild(
        toast
    );



    /* --------------------------------------------------------
       Notification Sound
    -------------------------------------------------------- */

    if (
        typeof window.playNotificationSound ===
        'function'
    ) {

        let soundType = 'info';


        if (type === 'success') {

            soundType = 'success';

        }

        else if (
            type === 'danger' ||
            type === 'error'
        ) {

            soundType = 'error';

        }

        else if (
            type === 'warning'
        ) {

            soundType = 'warning';

        }


        window.playNotificationSound(
            soundType
        );

    }



    /* --------------------------------------------------------
       Auto Remove
    -------------------------------------------------------- */

    setTimeout(
        () => {

            toast.style.transition =
                'opacity 0.4s ease, transform 0.4s ease';

            toast.style.opacity =
                '0';

            toast.style.transform =
                'translateX(30px)';


            setTimeout(
                () => {

                    toast.remove();

                },
                400
            );

        },
        duration
    );

}



/* ============================================================
   GLOBAL CUSTOM FUNCTIONS
   ============================================================ */

window.showToast =
    showToast;


window.showLoader =
    showLoader;


window.hideLoader =
    hideLoader;



/* ============================================================
   GLOBAL SOUND FUNCTION
   ============================================================

   You can call from anywhere:

   playAppSound('success');

   playAppSound('error');

   playAppSound('warning');

   playAppSound('info');

*/

window.playAppSound =
    function (type = 'info') {

        if (
            typeof window.playNotificationSound ===
            'function'
        ) {

            window.playNotificationSound(
                type
            );

        }

    };