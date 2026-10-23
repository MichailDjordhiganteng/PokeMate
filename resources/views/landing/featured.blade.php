<section class="lp-featured-section" id="explore" aria-labelledby="featured-title">
    <div class="lp-container">
        <div class="lp-section-heading" data-reveal><div><span class="lp-eyebrow">Meet your next companion</span><h2 id="featured-title">Featured adventure <span>partners.</span></h2></div><a class="lp-text-link" href="{{ $catalogUrl }}">Explore Pokédex <x-icon name="arrow" /></a></div>
        <div class="lp-partners-grid">
            @forelse($featuredPartners as $partner)
                <article class="lp-partner" data-reveal style="--reveal-delay:{{ $loop->index * 80 }}ms">
                    <a class="lp-partner-card" href="{{ route($landingAdmin ? 'admin.pokemons.edit' : 'trainer.catalog.show', $partner) }}" aria-label="{{ $landingAdmin ? 'Kelola' : 'Lihat detail' }} {{ $partner->pokemon_name }}">
                        <div class="lp-partner-art lp-partner-art--{{ strtolower($partner->types->first()?->type ?? 'normal') }}"><span class="lp-partner-code">{{ $partner->pokemon_code }}</span><span class="lp-level-pill">Lv. {{ $partner->pokemon_level }}</span><span class="lp-card-watermark" aria-hidden="true"></span><img src="{{ $partner->imageUrl() ?? asset('images/pokeball.png') }}" alt="{{ $partner->imageUrl() ? $partner->pokemon_name : 'Poké Ball untuk '.$partner->pokemon_name }}" width="280" height="220" loading="lazy" decoding="async"></div>
                        <div class="lp-partner-body"><div class="lp-partner-heading"><h3>{{ $partner->pokemon_name }}</h3><span class="lp-status lp-status--{{ $partner->status }}"><i aria-hidden="true"></i>{{ __('status.'.$partner->status) }}</span></div><div class="lp-type-list">@foreach($partner->types as $type)<span class="lp-type lp-type--{{ strtolower($type->type) }}">{{ $type->type }}</span>@endforeach</div><div class="lp-card-bottom"><div><span>Rental starts at</span><strong>Rp {{ number_format($partner->rental_price, 0, ',', '.') }}<small>/ hari</small></strong></div></div><span class="lp-view-detail">{{ $landingAdmin ? 'Kelola Partner' : 'View Detail' }}<x-icon name="arrow" /></span></div>
                    </a>
                </article>
            @empty
                <div class="lp-empty" data-reveal><x-icon name="pokemon" /><h3>Petualangan baru sedang disiapkan.</h3><p>Pokémon partner akan tampil di sini setelah ditambahkan ke Pokédex.</p><a class="lp-button lp-button--primary" href="{{ $adventureUrl }}">Mulai Petualangan <x-icon name="arrow" /></a></div>
            @endforelse
        </div>
        <p class="lp-section-footnote" data-reveal><x-icon name="info" />@auth Status dan harga mengikuti inventaris partner saat halaman dimuat. @else Kenali partnermu di sini. Masuk sebagai trainer untuk melihat detail dan memulai rental. @endauth</p>
    </div>
</section>
