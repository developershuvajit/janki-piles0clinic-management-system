/**
 * Notification Sound System
 * Works with sub-folder projects like:
 * /2026/clinic/public/
 *
 * notification.js:
 * /assets/js/notification.js
 *
 * sound files:
 * /assets/sound/
 */

(function () {
    'use strict';

    // =========================================================
    // SOUND PATH
    // =========================================================

    const SOUND_BASE = new URL(
        '../sound/',
        document.currentScript?.src || window.location.href
    );

    const soundMap = {
        success: 'success.mp3',
        error: 'error.mp3',
        danger: 'error.mp3',
        warning: 'warning.mp3',
        info: 'info.mp3'
    };


    // =========================================================
    // AUDIO STATE
    // =========================================================

    let audioUnlocked = false;
    let audioContext = null;
    let pendingSound = null;
    let notificationPlayed = false;


    // =========================================================
    // GET SOUND URL
    // =========================================================

    function getSoundUrl(type) {

        const file = soundMap[type] || soundMap.info;

        return new URL(file, SOUND_BASE).href;
    }


    // =========================================================
    // GET / CREATE AUDIO CONTEXT
    // =========================================================

    function getAudioContext() {

        if (audioContext) {
            return audioContext;
        }

        const AudioContext =
            window.AudioContext ||
            window.webkitAudioContext;

        if (!AudioContext) {
            return null;
        }

        try {

            audioContext = new AudioContext();

            return audioContext;

        } catch (error) {

            console.log('AudioContext creation error:', error);

            return null;
        }
    }


    // =========================================================
    // UNLOCK AUDIO
    // =========================================================

    function unlockAudio() {

        if (audioUnlocked) {
            return;
        }

        const context = getAudioContext();

        // Browser doesn't support Web Audio
        if (!context) {

            audioUnlocked = true;

            playPendingSound();

            return;
        }

        try {

            if (context.state === 'suspended') {

                context.resume()
                    .then(function () {

                        audioUnlocked = true;

                        playPendingSound();

                    })
                    .catch(function (error) {

                        console.log(
                            'AudioContext resume error:',
                            error
                        );

                    });

            } else {

                audioUnlocked = true;

                playPendingSound();
            }

        } catch (error) {

            console.log(
                'Audio unlock error:',
                error
            );

            audioUnlocked = true;

            playPendingSound();
        }
    }


    // =========================================================
    // PLAY PENDING SOUND
    // =========================================================

    function playPendingSound() {

        if (!pendingSound) {
            return;
        }

        const type = pendingSound;

        pendingSound = null;

        // No artificial delay
        playNotificationSound(type);
    }


    // =========================================================
    // MAIN NOTIFICATION SOUND
    // =========================================================

    window.playNotificationSound = function (type = 'info') {

        type = String(type || 'info').toLowerCase();

        // -----------------------------------------------------
        // Browser audio not unlocked yet
        // -----------------------------------------------------

        if (!audioUnlocked) {

            pendingSound = type;

            unlockAudio();

            return;
        }


        const soundFile = getSoundUrl(type);

        console.log(
            'Playing notification sound:',
            soundFile
        );


        // -----------------------------------------------------
        // Play MP3
        // -----------------------------------------------------

        try {

            const audio = new Audio();

            audio.src = soundFile;
            audio.preload = 'auto';
            audio.volume = 0.6;

            // Prevent unnecessary delay
            audio.currentTime = 0;

            const playPromise = audio.play();

            if (playPromise !== undefined) {

                playPromise
                    .then(function () {

                        console.log(
                            'Notification sound played:',
                            type
                        );

                    })
                    .catch(function (error) {

                        console.log(
                            'MP3 playback blocked:',
                            error
                        );

                        playWebAudio(type);
                    });

            }

        } catch (error) {

            console.log(
                'Audio playback error:',
                error
            );

            playWebAudio(type);
        }
    };


    // =========================================================
    // WEB AUDIO FALLBACK
    // =========================================================

    function playWebAudio(type) {

        const context = getAudioContext();

        if (!context) {
            return;
        }

        try {

            if (context.state === 'suspended') {

                context.resume().catch(function () {});

            }


            // =================================================
            // SUCCESS
            // =================================================

            if (type === 'success') {

                const notes = [
                    523.25,
                    659.25,
                    783.99
                ];

                notes.forEach(function (freq, i) {

                    const oscillator =
                        context.createOscillator();

                    const gainNode =
                        context.createGain();

                    oscillator.connect(gainNode);
                    gainNode.connect(context.destination);

                    const start =
                        context.currentTime +
                        (i * 0.15);

                    oscillator.frequency.value = freq;
                    oscillator.type = 'sine';

                    gainNode.gain.setValueAtTime(
                        0.2,
                        start
                    );

                    gainNode.gain.exponentialRampToValueAtTime(
                        0.01,
                        start + 0.2
                    );

                    oscillator.start(start);

                    oscillator.stop(
                        start + 0.2
                    );
                });

            }


            // =================================================
            // ERROR / DANGER
            // =================================================

            else if (
                type === 'error' ||
                type === 'danger'
            ) {

                const oscillator =
                    context.createOscillator();

                const gainNode =
                    context.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(context.destination);

                const now =
                    context.currentTime;

                oscillator.frequency.setValueAtTime(
                    400,
                    now
                );

                oscillator.frequency.exponentialRampToValueAtTime(
                    150,
                    now + 0.3
                );

                oscillator.type = 'sawtooth';

                gainNode.gain.setValueAtTime(
                    0.2,
                    now
                );

                gainNode.gain.exponentialRampToValueAtTime(
                    0.01,
                    now + 0.3
                );

                oscillator.start(now);

                oscillator.stop(
                    now + 0.3
                );
            }


            // =================================================
            // WARNING
            // =================================================

            else if (type === 'warning') {

                [0, 0.2].forEach(function (delay) {

                    const oscillator =
                        context.createOscillator();

                    const gainNode =
                        context.createGain();

                    oscillator.connect(gainNode);
                    gainNode.connect(context.destination);

                    const start =
                        context.currentTime +
                        delay;

                    oscillator.frequency.value = 800;

                    oscillator.type = 'square';

                    gainNode.gain.setValueAtTime(
                        0.15,
                        start
                    );

                    gainNode.gain.setValueAtTime(
                        0,
                        start + 0.15
                    );

                    oscillator.start(start);

                    oscillator.stop(
                        start + 0.15
                    );
                });

            }


            // =================================================
            // INFO
            // =================================================

            else {

                const oscillator =
                    context.createOscillator();

                const gainNode =
                    context.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(context.destination);

                const now =
                    context.currentTime;

                oscillator.frequency.value = 600;

                oscillator.type = 'sine';

                gainNode.gain.setValueAtTime(
                    0.15,
                    now
                );

                gainNode.gain.exponentialRampToValueAtTime(
                    0.01,
                    now + 0.15
                );

                oscillator.start(now);

                oscillator.stop(
                    now + 0.15
                );
            }

        } catch (error) {

            console.log(
                'Web Audio error:',
                error
            );
        }
    }


    // =========================================================
    // DETECT NOTIFICATION
    // =========================================================

    function autoPlayNotificationSound() {

        console.log(
            'Checking notification...'
        );


        // =====================================================
        // META NOTIFICATION
        // =====================================================

        const soundMeta =
            document.querySelector(
                'meta[name="sound-notification"]'
            );

        if (soundMeta) {

            const type =
                soundMeta.getAttribute('content') ||
                'info';

            console.log(
                'Sound notification:',
                type
            );

            triggerNotificationSound(type);

            return;
        }


        // =====================================================
        // FLASH MESSAGE
        // =====================================================

        const flashContainer =
            document.querySelector(
                '.flash-messages'
            );

        if (!flashContainer) {

            console.log(
                'No flash message found.'
            );

            return;
        }


        const alerts =
            flashContainer.querySelectorAll(
                '.alert'
            );

        if (!alerts.length) {

            console.log(
                'No alerts found.'
            );

            return;
        }


        const firstAlert = alerts[0];

        let type = 'info';


        if (
            firstAlert.classList.contains(
                'alert-success'
            )
        ) {

            type = 'success';

        }

        else if (
            firstAlert.classList.contains(
                'alert-danger'
            ) ||
            firstAlert.classList.contains(
                'alert-error'
            )
        ) {

            type = 'error';

        }

        else if (
            firstAlert.classList.contains(
                'alert-warning'
            )
        ) {

            type = 'warning';
        }


        console.log(
            'Detected notification:',
            type
        );


        triggerNotificationSound(type);
    }


    // =========================================================
    // TRIGGER SOUND
    // =========================================================

    function triggerNotificationSound(type) {

        // Prevent duplicate sound
        if (notificationPlayed) {
            return;
        }

        notificationPlayed = true;

        pendingSound = type;

        // Play immediately
        playNotificationSound(type);
    }


    // =========================================================
    // BROWSER AUDIO UNLOCK
    // =========================================================

    function setupAudioUnlock() {

        const events = [
            'click',
            'touchstart',
            'keydown'
        ];


        events.forEach(function (eventName) {

            document.addEventListener(
                eventName,
                function () {

                    unlockAudio();

                },
                {
                    once: true,
                    passive: true
                }
            );

        });
    }


    // =========================================================
    // INITIALIZE
    // =========================================================

    document.addEventListener(
        'DOMContentLoaded',
        function () {

            console.log(
                'Notification script loaded!'
            );

            // Setup browser audio permission
            setupAudioUnlock();

            // Detect and play notification immediately
            autoPlayNotificationSound();

        }
    );

})();