<section class="lp-container lp-section" id="how-it-works" aria-labelledby="how-title">
    <div class="lp-section-heading" data-reveal><div><span class="lp-eyebrow">Less planning. More exploring.</span><h2 id="how-title">How PokePartner <span>works.</span></h2></div><p>Petualangan dimulai dalam 3 langkah sederhana. Dari memilih partner sampai menciptakan cerita baru.</p></div>
    <div class="lp-steps-grid">
        @foreach([
            ['icon' => 'search', 'title' => 'Temukan partnermu', 'description' => 'Jelajahi Pokédex dan kenali Pokémon berdasarkan tipe, level, dan ketersediaannya.', 'tag' => '01 — Temukan'],
            ['icon' => 'compass', 'title' => 'Pilih petualanganmu', 'description' => 'Sesuaikan partner dengan medan, rencana perjalanan, dan anggaran lewat Adventure Match.', 'tag' => '02 — Pilih'],
            ['icon' => 'map', 'title' => 'Let the adventure begin', 'description' => 'Sewa hingga 2 partner selama 1–5 hari. Nikmati perjalanan, lalu kembalikan melalui admin.', 'tag' => '03 — Adventure'],
        ] as $step)
            <article class="lp-step" data-reveal style="--reveal-delay:{{ $loop->index * 90 }}ms"><div class="lp-step-top"><span class="lp-feature-icon"><x-icon :name="$step['icon']" /></span><span class="lp-step-number">0{{ $loop->iteration }}</span></div><span class="lp-overline">{{ $step['tag'] }}</span><h3>{{ $step['title'] }}</h3><p>{{ $step['description'] }}</p><span class="lp-step-line" aria-hidden="true"></span></article>
        @endforeach
    </div>
</section>
