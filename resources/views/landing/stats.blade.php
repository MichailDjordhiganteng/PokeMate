<section class="lp-container lp-stats" aria-label="Sekilas PokePartner" data-reveal>
    @foreach([
        ['value' => $partnerCount, 'label' => 'Pokémon partners', 'hint' => 'Terdaftar di Pokédex', 'icon' => 'pokemon'],
        ['value' => $typeCount, 'label' => 'Pokémon types', 'hint' => 'Temukan elemenmu', 'icon' => 'types'],
        ['value' => '1–5', 'label' => 'Days of adventure', 'hint' => 'Durasi sewa fleksibel', 'icon' => 'calendar'],
        ['value' => '2', 'label' => 'Companions per trainer', 'hint' => 'Batas partner rental aktif', 'icon' => 'users'],
    ] as $stat)
        <div class="lp-stat"><span class="lp-stat-icon"><x-icon :name="$stat['icon']" /></span><div><strong>{{ $stat['value'] }}</strong><h2>{{ $stat['label'] }}</h2><p>{{ $stat['hint'] }}</p></div></div>
    @endforeach
</section>
