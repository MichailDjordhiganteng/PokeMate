<div class="music-player" data-music-player data-state="off" hidden>
    <audio id="pokepartner-backsound" data-music-audio loop preload="none" playsinline aria-hidden="true">
        <source src="{{ asset('audio/pokemon-redblue-opening.mp3') }}" type="audio/mpeg">
    </audio>

    <section class="music-panel" id="music-settings" aria-label="Pengaturan backsound" hidden>
        <div class="music-panel-heading">
            <div><img src="{{ asset('images/pokeball.png') }}" alt="" width="30" height="30"><div><strong>Adventure soundtrack</strong><span>Pokémon Red/Blue · Opening</span></div></div>
            <button class="music-control music-close" type="button" data-music-close aria-label="Tutup pengaturan musik">×</button>
        </div>
        <p class="music-loop-info"><span aria-hidden="true">↻</span>Loop aktif · berulang sampai dimatikan</p>
        <div class="music-volume-heading"><label for="music-volume">Volume musik</label><output for="music-volume" data-music-volume-label>20%</output></div>
        <input class="music-volume" id="music-volume" data-music-volume type="range" min="0" max="100" step="1" value="20" aria-valuetext="20 persen">
        <div class="music-panel-actions">
            <button class="music-control music-action" type="button" data-music-panel-play>Nyalakan musik</button>
            <button class="music-control music-mute" type="button" data-music-mute aria-pressed="false" aria-label="Bisukan musik">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m11 4-6 5H2v6h3l6 5V4Z"/><g data-music-sound-icon><path d="M15 8a6 6 0 0 1 0 8m3-11a10 10 0 0 1 0 14"/></g><g data-music-muted-icon hidden><path d="m16 9 6 6m-6 0 6-6"/></g></svg>
                <span data-music-mute-label>Bisukan</span>
            </button>
        </div>
        <span class="music-preference-note" data-music-preference-note>Pengaturan disimpan di perangkat ini.</span>
    </section>

    <div class="music-dock">
        <button class="music-control music-switch" type="button" data-music-toggle aria-label="Nyalakan musik" aria-pressed="false">
            <svg data-music-play-icon viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 4.5a1 1 0 0 1 1.5-.86l11 6.5a1 1 0 0 1 0 1.72l-11 6.5A1 1 0 0 1 8 17.5v-13Z"/></svg>
            <svg data-music-pause-icon viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" hidden><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/></svg>
        </button>
        <div class="music-dock-copy"><strong>Backsound<span class="music-bars" aria-hidden="true"><i></i><i></i><i></i></span></strong><span data-music-status role="status" aria-live="polite" aria-atomic="true">Mati</span></div>
        <button class="music-control music-settings-toggle" type="button" data-music-settings-toggle aria-controls="music-settings" aria-expanded="false" aria-label="Buka pengaturan musik">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M4 17h16"/><circle cx="9" cy="7" r="3" fill="currentColor"/><circle cx="15" cy="17" r="3" fill="currentColor"/></svg>
        </button>
    </div>
</div>
