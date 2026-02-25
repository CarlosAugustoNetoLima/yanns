document.addEventListener('DOMContentLoaded', () => {
    const heroVideo = document.getElementById('heroVideo');
    if (!heroVideo) return;

    const videoBtn = document.querySelector('.hero-control-players .video-control');
    const soundBtn = document.querySelector('.hero-control-players .sound-control');

    // Toggle functions
    window.toggleVideo = function () {
        if (!heroVideo) return;
        if (heroVideo.paused) {
            heroVideo.play();
        } else {
            heroVideo.pause();
        }
    };

    window.toggleSound = function () {
        if (!heroVideo) return;
        heroVideo.muted = !heroVideo.muted;
    };

    // UI Sync Functions
    const updatePlayState = () => {
        if (!videoBtn) return;
        if (heroVideo.paused) {
            videoBtn.dataset.state = 'play';
            videoBtn.classList.remove('state-pause');
            videoBtn.classList.add('state-play');
            videoBtn.setAttribute('aria-label', 'Play Video');
            const iconPlay = videoBtn.querySelector('.icon-play');
            const iconPause = videoBtn.querySelector('.icon-pause');
            if (iconPlay) iconPlay.style.display = '';
            if (iconPause) iconPause.style.display = 'none';
        } else {
            videoBtn.dataset.state = 'pause';
            videoBtn.classList.remove('state-play');
            videoBtn.classList.add('state-pause');
            videoBtn.setAttribute('aria-label', 'Pause Video');
            const iconPlay = videoBtn.querySelector('.icon-play');
            const iconPause = videoBtn.querySelector('.icon-pause');
            if (iconPlay) iconPlay.style.display = 'none';
            if (iconPause) iconPause.style.display = '';
        }
    };

    const updateSoundState = () => {
        if (!soundBtn) return;
        if (heroVideo.muted) {
            soundBtn.dataset.state = 'muted';
            soundBtn.classList.remove('state-unmuted');
            soundBtn.classList.add('state-muted');
            soundBtn.setAttribute('aria-label', 'Unmute');
            const iconMuted = soundBtn.querySelector('.icon-muted');
            const iconUnmuted = soundBtn.querySelector('.icon-unmuted');
            if (iconMuted) iconMuted.style.display = '';
            if (iconUnmuted) iconUnmuted.style.display = 'none';
        } else {
            soundBtn.dataset.state = 'unmuted';
            soundBtn.classList.remove('state-muted');
            soundBtn.classList.add('state-unmuted');
            soundBtn.setAttribute('aria-label', 'Mute');
            const iconMuted = soundBtn.querySelector('.icon-muted');
            const iconUnmuted = soundBtn.querySelector('.icon-unmuted');
            if (iconMuted) iconMuted.style.display = 'none';
            if (iconUnmuted) iconUnmuted.style.display = '';
        }
    };

    // Listeners for native events
    heroVideo.addEventListener('play', updatePlayState);
    heroVideo.addEventListener('pause', updatePlayState);
    heroVideo.addEventListener('volumechange', updateSoundState);

    // Initial strict configuration requested by user
    heroVideo.muted = false;
    heroVideo.volume = 1;

    // Forceful attempt to play using muted first so it strictly autoplays visually
    heroVideo.muted = true;

    const playPromise = heroVideo.play();
    if (playPromise !== undefined) {
        playPromise.then(() => {
            // It is playing (muted). Let's attempt to boldly unmute it now.
            heroVideo.muted = false;

            // If the browser blocks unmuting without interaction, it will forcefully PAUSE the video.
            // We set up a quick trap to catch this auto-pause and fallback to playing muted visually.
            const catchAutoPause = () => {
                heroVideo.muted = true;
                heroVideo.play().catch(() => { }); // Keep the video moving

                // Wait for the actual first user interaction to release the sound
                const unlockSound = (evt) => {
                    if (evt && evt.target && evt.target.closest && evt.target.closest('.hero-control-players')) {
                        // User clicked our controls, let them handle it. Remove these listeners.
                        ['click', 'touchstart', 'scroll', 'keydown'].forEach(e => document.removeEventListener(e, unlockSound));
                        return;
                    }
                    heroVideo.muted = false;
                    ['click', 'touchstart', 'scroll', 'keydown'].forEach(e => document.removeEventListener(e, unlockSound));
                    updateSoundState();
                };

                ['click', 'touchstart', 'scroll', 'keydown'].forEach(e => document.addEventListener(e, unlockSound));
                heroVideo.removeEventListener('pause', catchAutoPause);
            };

            heroVideo.addEventListener('pause', catchAutoPause);

            // If it survives 500ms without pausing, it means the browser allowed the audio!
            setTimeout(() => {
                heroVideo.removeEventListener('pause', catchAutoPause);
                updateSoundState();
            }, 500);

            updatePlayState();
            updateSoundState();

        }).catch(error => {
            console.warn("Autoplay was completely blocked:", error);
        });
    }

    setTimeout(() => {
        updatePlayState();
        updateSoundState();
    }, 150);
});
