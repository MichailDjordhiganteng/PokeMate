<section class="lp-container lp-hero-wrap" aria-labelledby="hero-title">
    <div class="lp-hero" data-hero-surface>
        <span class="lp-hero-orb lp-hero-orb--blue" aria-hidden="true"></span>
        <span class="lp-hero-orb lp-hero-orb--yellow" aria-hidden="true"></span>
        <div class="lp-hero-copy" data-reveal>
            <span class="lp-eyebrow lp-eyebrow--light"><span class="lp-live-dot" aria-hidden="true"></span>Pokémon Adventure Companion</span>
            <h1 id="hero-title">Your perfect <span class="lp-highlight">partner</span> for every adventure<span class="lp-yellow">.</span></h1>
            <p class="lp-hero-description">Temukan Pokémon companion yang sesuai dengan medan, kebutuhan, dan gaya petualanganmu. Partner yang tepat, untuk cerita yang lebih hebat.</p>
            <div class="lp-hero-actions">
                <a class="lp-button lp-button--primary" href="{{ $adventureUrl }}">Mulai Petualangan <x-icon name="arrow" /></a>
                <a class="lp-button lp-button--glass" href="#explore"><x-icon name="pokemon" />Lihat Pokémon</a>
            </div>
            <div class="lp-hero-trust"><span><x-icon name="shield" />Kode unit unik</span><span><x-icon name="calendar" />Rental 1–5 hari</span><span><x-icon name="users" />2 partner per trainer</span></div>
        </div>
        <div class="lp-hero-visual" data-reveal style="--reveal-x:28px;--reveal-y:0px;--reveal-delay:120ms">
            <div class="lp-orbit lp-orbit--outer" aria-hidden="true"></div>
            <div class="lp-orbit lp-orbit--inner" aria-hidden="true"></div>
            <span class="lp-orbit-dot" aria-hidden="true"></span>
            <div class="lp-floating-badge lp-floating-badge--top"><span class="lp-floating-icon"><x-icon name="shield" /></span><div><strong>{{ $heroPartner?->status === 'available' ? 'Adventure ready' : 'Companion preview' }}</strong><small>Choose. Connect. Explore.</small></div></div>
            <article class="lp-showcase" aria-label="Preview Pokémon companion">
                <div class="lp-showcase-top"><span><x-icon name="pokemon" />COMPANION PREVIEW</span><span class="lp-showcase-code">{{ $heroPartner?->pokemon_code ?? 'POKEPARTNER' }}</span></div>
                <div class="lp-showcase-art">
                    <span class="lp-art-aura" aria-hidden="true"></span>
                    <span class="lp-showcase-watermark" aria-hidden="true"></span>
                    @if($heroPartner)
                        <img class="lp-hero-pokemon" src="{{ $heroPartner->imageUrl() ?? asset('images/pokeball.png') }}" alt="{{ $heroPartner->imageUrl() ? $heroPartner->pokemon_name.' — Pokémon adventure companion' : 'Poké Ball untuk partner '.$heroPartner->pokemon_name }}" width="360" height="320" fetchpriority="high" decoding="async">
                        <span class="lp-level-pill">Lv. {{ $heroPartner->pokemon_level }}</span>
                    @else
                        <img class="lp-hero-pokemon lp-hero-pokemon--empty" src="{{ asset('images/pokeball.png') }}" alt="Poké Ball PokePartner" width="360" height="320" fetchpriority="high" decoding="async">
                    @endif
                </div>
                <div class="lp-showcase-details">
                    <div class="lp-showcase-identity"><div><span class="lp-overline">Your next companion</span><h2>{{ $heroPartner?->pokemon_name ?? 'Your next partner' }}</h2></div>@if($heroPartner)<span class="lp-status lp-status--{{ $heroPartner->status }}"><i aria-hidden="true"></i>{{ __('status.'.$heroPartner->status) }}</span>@endif</div>
                    @if($heroPartner)
                        <div class="lp-type-list">@foreach($heroPartner->types as $type)<span class="lp-type lp-type--{{ strtolower($type->type) }}">{{ $type->type }}</span>@endforeach</div>
                        <div class="lp-showcase-rate"><span>Adventure rental</span><strong>Rp {{ number_format($heroPartner->rental_price, 0, ',', '.') }}<small>/ hari</small></strong></div>
                    @else
                        <p class="lp-preview-empty">Partner baru akan tampil di sini setelah ditambahkan ke Pokédex.</p>
                    @endif
                </div>
            </article>
            <div class="lp-floating-badge lp-floating-badge--bottom"><span class="lp-floating-icon lp-floating-icon--yellow"><x-icon name="compass" /></span><div><strong>Your journey. Your pace.</strong><small>1–5 days of adventure</small></div><span class="lp-floating-arrow" aria-hidden="true">↗</span></div>
            <div class="lp-visual-caption"><span aria-hidden="true"></span>A little companion. A bigger adventure.</div>
        </div>
        <div class="lp-hero-bottom" aria-hidden="true"><span>BUILT FOR THE ADVENTURE AHEAD</span><span>EST. 2026 <i>✦</i> POKEPARTNER</span></div>
    </div>
</section>
