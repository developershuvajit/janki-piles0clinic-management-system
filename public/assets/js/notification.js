/**
 * Notification Sound System
 * Works with sub-folder projects like:
 * /2026/clinic/public/
 */

(function () {
    'use strict';

    // notification.js is inside /assets/js/
    // So ../sound/ points to /assets/sound/
    const SOUND_BASE = new URL('../sound/', document.currentScript?.src || window.location.href);

    const soundMap = {
        success: 'success.mp3',
        error: 'error.mp3',
        danger: 'error.mp3',
        warning: 'warning.mp3',
        info: 'info.mp3'
    };

    let audioUnlocked = false;
    let pendingSound = null;


    /**
     * Get sound URL
     */
    function getSoundUrl(type) {
        const file = soundMap[type] || soundMap.info;
        return new URL(file, SOUND_BASE).href;
    }


    /**
     * Unlock browser audio after user interaction
     */
    function unlockAudio() {
        if (audioUnlocked) return;

        try {
            const AudioContext =
                window.AudioContext || window.webkitAudioContext;

            if (AudioContext) {
                const context = new AudioContext();

                if (context.state === 'suspended') {
                    context.resume().then(function () {
                        audioUnlocked = true;
                        playPendingSound();
                    });
                } else {
                    audioUnlocked = true;
                    playPendingSound();
                }
            } else {
                audioUnlocked = true;
                playPendingSound();
            }
        } catch (e) {
            console.log('Audio unlock error:', e);
            audioUnlocked = true;
            playPendingSound();
        }
    }


    /**
     * Play pending notification
     */
    function playPendingSound() {
        if (!pendingSound) return;

        const type = pendingSound;
        pendingSound = null;

        setTimeout(function () {
            playNotificationSound(type);
        }, 100);
    }


    /**
     * Play notification sound
     */
    window.playNotificationSound = function (type = 'info') {

        // Browser has not allowed audio yet
        if (!audioUnlocked) {
            pendingSound = type;
            unlockAudio();
            return;
        }

        const soundFile = getSoundUrl(type);

        console.log('Playing notification sound:', soundFile);

        try {
            const audio = new Audio(soundFile);

            audio.preload = 'auto';
            audio.volume = 0.6;

            audio.play()
                .then(function () {
                    console.log('Notification sound played:', type);
                })
                .catch(function (error) {
                    console.log('MP3 playback blocked:', error);
                    playWebAudio(type);
                });

        } catch (error) {
            console.log('Audio error:', error);
            playWebAudio(type);
        }
    };


    /**
     * Web Audio fallback
     */
    function playWebAudio(type) {

        try {
            const AudioContext =
                window.AudioContext || window.webkitAudioContext;

            if (!AudioContext) return;

            const context = new AudioContext();

            if (context.state === 'suspended') {
                context.resume();
            }

            if (type === 'success') {

                const notes = [523.25, 659.25, 783.99];

                notes.forEach(function (freq, i) {

                    const oscillator = context.createOscillator();
                    const gainNode = context.createGain();

                    oscillator.connect(gainNode);
                    gainNode.connect(context.destination);

                    const start = context.currentTime + (i * 0.15);

                    oscillator.frequency.value = freq;
                    oscillator.type = 'sine';

                    gainNode.gain.setValueAtTime(0.2, start);
                    gainNode.gain.exponentialRampToValueAtTime(
                        0.01,
                        start + 0.2
                    );

                    oscillator.start(start);
                    oscillator.stop(start + 0.2);
                });

            } else if (type === 'error' || type === 'danger') {

                const oscillator = context.createOscillator();
                const gainNode = context.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(context.destination);

                oscillator.frequency.setValueAtTime(
                    400,
                    context.currentTime
                );

                oscillator.frequency.exponentialRampToValueAtTime(
                    150,
                    context.currentTime + 0.3
                );

                oscillator.type = 'sawtooth';

                gainNode.gain.setValueAtTime(
                    0.2,
                    context.currentTime
                );

                gainNode.gain.exponentialRampToValueAtTime(
                    0.01,
                    context.currentTime + 0.3
                );

                oscillator.start(context.currentTime);
                oscillator.stop(context.currentTime + 0.3);

            } else if (type === 'warning') {

                [0, 0.2].forEach(function (delay) {

                    const oscillator = context.createOscillator();
                    const gainNode = context.createGain();

                    oscillator.connect(gainNode);
                    gainNode.connect(context.destination);

                    const start = context.currentTime + delay;

                    oscillator.frequency.value = 800;
                    oscillator.type = 'square';

                    gainNode.gain.setValueAtTime(0.15, start);
                    gainNode.gain.setValueAtTime(0, start + 0.15);

                    oscillator.start(start);
                    oscillator.stop(start + 0.15);
                });

            } else {

                const oscillator = context.createOscillator();
                const gainNode = context.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(context.destination);

                oscillator.frequency.value = 600;
                oscillator.type = 'sine';

                gainNode.gain.setValueAtTime(
                    0.15,
                    context.currentTime
                );

                gainNode.gain.exponentialRampToValueAtTime(
                    0.01,
                    context.currentTime + 0.15
                );

                oscillator.start(context.currentTime);
                oscillator.stop(context.currentTime + 0.15);
            }

        } catch (error) {
            console.log('Web Audio error:', error);
        }
    }


    /**
     * Detect flash notification
     */
    function autoPlayNotificationSound() {

        console.log('Checking notification...');

        // Meta notification
        const soundMeta =
            document.querySelector('meta[name="sound-notification"]');

        if (soundMeta) {

            const type =
                soundMeta.getAttribute('content') || 'info';

            console.log('Sound notification:', type);

            pendingSound = type;

            // Try immediately
            setTimeout(function () {
                playNotificationSound(type);
            }, 300);

            return;
        }


        // Flash messages
        const flashContainer =
            document.querySelector('.flash-messages');

        if (!flashContainer) {
            console.log('No flash message found.');
            return;
        }

        const alerts =
            flashContainer.querySelectorAll('.alert');

        if (!alerts.length) {
            console.log('No alerts found.');
            return;
        }

        const firstAlert = alerts[0];

        let type = 'info';

        if (firstAlert.classList.contains('alert-success')) {
            type = 'success';

        } else if (
            firstAlert.classList.contains('alert-danger') ||
            firstAlert.classList.contains('alert-error')
        ) {
            type = 'error';

        } else if (
            firstAlert.classList.contains('alert-warning')
        ) {
            type = 'warning';
        }

        console.log('Detected notification:', type);

        pendingSound = type;

        setTimeout(function () {
            playNotificationSound(type);
        }, 300);
    }


    /**
     * Browser audio unlock
     *
     * First click / touch / key press enables audio.
     */
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


    /**
     * Initialize
     */
    document.addEventListener('DOMContentLoaded', function () {

        console.log('Notification script loaded!');

        setupAudioUnlock();

        autoPlayNotificationSound();

    });

})();