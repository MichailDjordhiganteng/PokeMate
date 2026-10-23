<section class="lp-container lp-section lp-about" id="about" aria-labelledby="benefits-title">
    <div class="lp-section-heading" data-reveal><div><span class="lp-eyebrow">Adventure, with a little more confidence.</span><h2 id="benefits-title">Why trainers choose <span>PokePartner.</span></h2></div><p>Dirancang untuk perjalanan yang menyenangkan, dengan proses rental yang jelas dari awal sampai kembali.</p></div>
    <div class="lp-benefits-grid">
        @foreach([
            ['icon' => 'shield', 'title' => 'Verified Partners', 'description' => 'Setiap Pokémon tercatat dengan kode unit unik. Kenali identitas, tipe, dan level partner sebelum berangkat.', 'tag' => 'A partner you can get to know'],
            ['icon' => 'calendar', 'title' => 'Flexible Adventure', 'description' => 'Dari perjalanan singkat sampai eksplorasi lebih jauh, pilih durasi sewa antara 1 hingga 5 hari.', 'tag' => 'Make room for your own pace'],
            ['icon' => 'return', 'title' => 'Easy Return', 'description' => 'Ajukan pengembalian melalui akunmu. Admin memeriksa partner dan menyelesaikan transaksi secara terstruktur.', 'tag' => 'A clear way back home'],
            ['icon' => 'report', 'title' => 'Adventure History', 'description' => 'Lihat kembali partner, jadwal, biaya, dan status setiap petualangan dalam riwayat rental milikmu.', 'tag' => 'Every journey has a story'],
        ] as $benefit)
            <article class="lp-benefit" data-reveal style="--reveal-delay:{{ ($loop->index % 2) * 90 }}ms"><span class="lp-feature-icon"><x-icon :name="$benefit['icon']" /></span><div><span class="lp-overline">{{ $benefit['tag'] }}</span><h3>{{ $benefit['title'] }}</h3><p>{{ $benefit['description'] }}</p></div></article>
        @endforeach
    </div>
</section>
