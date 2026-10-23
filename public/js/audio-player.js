(() => {
    const player = document.querySelector('[data-music-player]');
    if (!player || player.dataset.initialized === 'true') return;
    player.dataset.initialized = 'true';

    const audio = player.querySelector('[data-music-audio]');
    const toggle = player.querySelector('[data-music-toggle]');
    const panelPlay = player.querySelector('[data-music-panel-play]');
    const panel = player.querySelector('.music-panel');
    const panelToggle = player.querySelector('[data-music-settings-toggle]');
    const volume = player.querySelector('[data-music-volume]');
    const volumeLabel = player.querySelector('[data-music-volume-label]');
    const mute = player.querySelector('[data-music-mute]');
    const status = player.querySelector('[data-music-status]');
    const storageKey = 'pokepartner.audio.v1';
    const clampVolume = (value) => Number.isFinite(value) ? Math.min(1, Math.max(0, value)) : 0.2;
    // Try autoplay for first-time visitors; an explicitly saved off setting wins.
    let state = { enabled: true, volume: 0.2, muted: false, position: 0 };
    let storageAvailable = true;
    let waitingForGesture = false;
    let loading = false;
    let loadError = false;
    let restoredPosition = false;
    let leaving = false;
    let attempt = 0;
    let lastSaved = 0;

    try {
        const saved = JSON.parse(localStorage.getItem(storageKey) || 'null');
        if (saved && typeof saved === 'object') {
            state = {
                enabled: saved.enabled === true,
                volume: clampVolume(saved.volume),
                muted: saved.muted === true,
                position: Number.isFinite(saved.position) ? Math.max(0, saved.position) : 0,
            };
        }
    } catch { storageAvailable = false; }

    audio.loop = true;
    audio.volume = state.volume;
    audio.muted = state.muted;
    player.hidden = false;

    const persist = () => {
        if (restoredPosition && Number.isFinite(audio.currentTime)) state.position = audio.currentTime;
        try { localStorage.setItem(storageKey, JSON.stringify(state)); }
        catch { storageAvailable = false; }
        lastSaved = performance.now();
    };

    const render = () => {
        const playing = state.enabled && !audio.paused && !audio.ended;
        const silent = state.muted || state.volume === 0;
        const resumable = state.enabled && waitingForGesture && audio.paused;
        const action = resumable ? 'Lanjutkan musik' : (state.enabled ? 'Matikan musik' : 'Nyalakan musik');
        player.dataset.state = loadError ? 'error' : (loading ? 'loading' : (playing ? (silent ? 'muted' : 'playing') : (resumable ? 'waiting' : 'off')));
        toggle.setAttribute('aria-label', action);
        toggle.setAttribute('title', action);
        toggle.setAttribute('aria-pressed', String(state.enabled && !resumable));
        panelPlay.textContent = action;
        player.querySelector('[data-music-play-icon]').toggleAttribute('hidden', state.enabled && !resumable);
        player.querySelector('[data-music-pause-icon]').toggleAttribute('hidden', !state.enabled || resumable);
        const statusText = loadError ? 'Gagal dimuat' : (loading ? 'Memuat…' : (resumable ? 'Ketuk untuk lanjut' : (playing ? (silent ? 'Tanpa suara' : 'Aktif · loop') : 'Mati')));
        if (status.textContent !== statusText) status.textContent = statusText;
        const percentage = Math.round(state.volume * 100);
        volume.value = String(percentage);
        volume.style.setProperty('--music-volume', `${percentage}%`);
        volume.setAttribute('aria-valuetext', `${percentage} persen`);
        volumeLabel.textContent = `${percentage}%`;
        mute.setAttribute('aria-pressed', String(silent));
        mute.setAttribute('aria-label', silent ? 'Aktifkan suara musik' : 'Bisukan musik');
        player.querySelector('[data-music-mute-label]').textContent = silent ? 'Bunyikan' : 'Bisukan';
        player.querySelector('[data-music-sound-icon]').toggleAttribute('hidden', silent);
        player.querySelector('[data-music-muted-icon]').toggleAttribute('hidden', !silent);
        player.querySelector('[data-music-preference-note]').textContent = storageAvailable
            ? 'Pengaturan disimpan di perangkat ini.' : 'Pengaturan berlaku selama halaman ini terbuka.';
    };

    const closePanel = (restoreFocus = false) => {
        panel.hidden = true;
        panelToggle.setAttribute('aria-expanded', 'false');
        panelToggle.setAttribute('aria-label', 'Buka pengaturan musik');
        if (restoreFocus) panelToggle.focus();
    };
    panelToggle.addEventListener('click', () => {
        if (!panel.hidden) { closePanel(); return; }
        panel.hidden = false;
        panelToggle.setAttribute('aria-expanded', 'true');
        panelToggle.setAttribute('aria-label', 'Tutup pengaturan musik');
        volume.focus({ preventScroll: true });
    });
    player.querySelector('[data-music-close]').addEventListener('click', () => closePanel(true));
    document.addEventListener('click', (event) => { if (!player.contains(event.target)) closePanel(); });
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape' && !panel.hidden) closePanel(true); });

    const disarmResume = () => {
        document.removeEventListener('pointerdown', resumeAfterInteraction, true);
        document.removeEventListener('click', resumeAfterInteraction, true);
        document.removeEventListener('keydown', resumeAfterInteraction, true);
    };
    const armResume = () => {
        document.addEventListener('pointerdown', resumeAfterInteraction, true);
        document.addEventListener('click', resumeAfterInteraction, true);
        document.addEventListener('keydown', resumeAfterInteraction, true);
    };
    const play = async (userInitiated = false) => {
        if (loading) return;
        const request = ++attempt;
        if (userInitiated) state.enabled = true;
        if (!state.enabled) return;
        if (loadError || audio.error) { restoredPosition = false; audio.load(); }
        loadError = false;
        loading = true;
        persist();
        render();
        try {
            await audio.play();
            if (request !== attempt || !state.enabled) return;
            waitingForGesture = false;
            disarmResume();
            persist();
        } catch (error) {
            if (request !== attempt || !state.enabled) return;
            if (error.name === 'NotAllowedError' || error.name === 'AbortError') {
                waitingForGesture = true;
                armResume();
            } else {
                loadError = true;
                state.enabled = false;
                disarmResume();
                persist();
                if (userInitiated) window.PokePartner?.notify({ type: 'error', title: 'Backsound belum dapat diputar', message: 'Periksa koneksi lalu coba nyalakan musik kembali.', key: 'music-playback-error' });
            }
        } finally {
            if (request === attempt) { loading = false; render(); }
        }
    };
    function resumeAfterInteraction(event) {
        if (player.contains(event.target) || !state.enabled || !waitingForGesture) return;
        if (event.type === 'keydown' && !['Enter', ' '].includes(event.key)) return;
        void play();
    }
    const stop = () => {
        attempt++;
        state.enabled = false;
        waitingForGesture = false;
        loading = false;
        audio.pause();
        disarmResume();
        persist();
        render();
    };
    const togglePlayback = () => {
        if (state.enabled && !waitingForGesture) stop();
        else void play(true);
    };
    toggle.addEventListener('click', togglePlayback);
    panelPlay.addEventListener('click', togglePlayback);

    volume.addEventListener('input', () => {
        state.volume = clampVolume(Number(volume.value) / 100);
        state.muted = false;
        audio.volume = state.volume;
        audio.muted = false;
        persist();
        render();
    });
    mute.addEventListener('click', () => {
        const silent = state.muted || state.volume === 0;
        if (silent && state.volume === 0) state.volume = 0.2;
        state.muted = !silent;
        audio.volume = state.volume;
        audio.muted = state.muted;
        persist();
        render();
    });

    audio.addEventListener('loadedmetadata', () => {
        if (!restoredPosition && Number.isFinite(audio.duration) && audio.duration > 0) {
            if (state.position > 0) audio.currentTime = state.position % audio.duration;
            restoredPosition = true;
        }
    });
    ['playing', 'pause', 'volumechange'].forEach((event) => audio.addEventListener(event, render));
    audio.addEventListener('timeupdate', () => {
        if (state.enabled && !audio.paused && performance.now() - lastSaved > 4000) persist();
    });
    audio.addEventListener('ended', () => {
        if (state.enabled && !leaving) { audio.currentTime = 0; void play(); }
    });
    window.addEventListener('pagehide', () => { leaving = true; persist(); });
    window.addEventListener('pageshow', (event) => {
        leaving = false;
        if (!event.persisted) return;
        try {
            const saved = JSON.parse(localStorage.getItem(storageKey) || 'null');
            if (saved) {
                state.enabled = saved.enabled === true;
                state.volume = clampVolume(saved.volume);
                state.muted = saved.muted === true;
                audio.volume = state.volume;
                audio.muted = state.muted;
                if (Number.isFinite(saved.position) && Number.isFinite(audio.duration) && audio.duration > 0) {
                    audio.currentTime = Math.max(0, saved.position) % audio.duration;
                }
            }
        } catch { /* In-memory preferences remain usable without storage. */ }
        if (!state.enabled) { audio.pause(); render(); }
        else if (audio.paused) void play();
    });
    document.addEventListener('visibilitychange', () => { if (document.hidden) persist(); });

    // Cross-tab changes to volume/mute/off are applied without starting a second soundtrack.
    window.addEventListener('storage', (event) => {
        if (event.key !== storageKey || !event.newValue) return;
        try {
            const saved = JSON.parse(event.newValue);
            state.volume = clampVolume(saved.volume);
            state.muted = saved.muted === true;
            audio.volume = state.volume;
            audio.muted = state.muted;
            if (saved.enabled === false) {
                attempt++;
                loading = false;
                state.enabled = false;
                waitingForGesture = false;
                audio.pause();
                disarmResume();
            }
            render();
        } catch { /* A malformed preference must not interrupt the page. */ }
    });

    render();
    if (state.enabled) void play();
})();
