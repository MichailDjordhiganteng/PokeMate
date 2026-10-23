<section class="lp-container lp-types-section" aria-labelledby="types-title" data-reveal>
    <div class="lp-types-copy"><span class="lp-eyebrow">A different element. A new possibility.</span><h2 id="types-title">Find your <span>type.</span></h2><p>Dari jalur hutan sampai tepian air, setiap elemen punya ceritanya sendiri. Temukan yang cocok untukmu.</p></div>
    <div class="lp-types-grid">
        @foreach(['fire' => ['Fire', 'fire'], 'water' => ['Water', 'water'], 'grass' => ['Grass', 'leaf'], 'electric' => ['Electric', 'bolt'], 'flying' => ['Flying', 'compass'], 'psychic' => ['Psychic', 'sparkle'], 'normal' => ['Normal', 'circle'], 'poison' => ['Poison', 'types']] as $key => [$label, $icon])
            <a class="lp-type-chip lp-type-chip--{{ $key }}" href="{{ isset($explorerTypes[$key]) ? $catalogUrl.'?'.http_build_query(['type_id' => $explorerTypes[$key]->id]) : $catalogUrl }}" aria-label="Jelajahi partner tipe {{ $label }}"><span><x-icon :name="$icon" /></span>{{ $label }}<x-icon class="lp-chip-arrow" name="chevron" /></a>
        @endforeach
    </div>
</section>
