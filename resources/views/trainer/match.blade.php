@extends('layouts.app')
@section('title', 'Adventure Match')
@section('content')
<section class="hero match-hero"><span class="heading-tag">✧ Fitur Khas · Algoritma Elemen</span><h1>Adventure Match — Temukan Partner Petualanganmu!</h1><p>Pilih jenis ekspedisi hari ini, dan biarkan sistem mencocokkan tipe Pokémon yang siap menemanimu di alam liar.</p><span class="badge" style="background:#ffffff10;color:#c5d5ef">Rekomendasi berbasis kecocokan tipe Pokémon dan medan (rule-based).</span></section>
<div class="section-title"><div><span class="eyebrow">Langkah 1</span><h2>Pilih Medan Petualangan</h2></div><small>5 medan ekspedisi pilihan</small></div>
<form method="GET">
    <div class="terrain-grid">@foreach($terrains as $key => $terrain)@continue($key === 'city')
        @php $visual = ['forest' => ['forest', 'mint', 'Eksplorasi Hutan', 'Menjelajahi hutan lebat dan jalur pepohonan.'], 'water' => ['water', 'blue', 'Petualangan Perairan', 'Menyusuri tepi sungai dan jalur perairan.'], 'mountain' => ['mountain', 'yellow', 'Penjelajahan Gunung', 'Melintasi perbukitan dan jalur berbatu.'], 'cave' => ['compass', 'purple', 'Eksplorasi Gua', 'Menjelajahi gua dan lorong bebatuan.'], 'night' => ['moon', 'purple', 'Petualangan Malam', 'Menemani perjalanan di malam hari.']][$key]; @endphp
        <label class="terrain-option"><input type="radio" name="terrain" value="{{ $key }}" @checked(request('terrain') === $key) aria-label="{{ $visual[2] }}"><span class="terrain-icon tone-{{ $visual[1] }}"><x-icon :name="$visual[0]" /></span><span class="badge">{{ implode(' / ', $terrain['types']) }}</span><strong>{{ $visual[2] }}</strong><small>{{ $visual[3] }}</small></label>
    @endforeach</div>
    <div class="match-filter-actions"><div><label for="budget">Anggaran Maksimum / Hari (Rp)</label><input id="budget" type="number" name="budget" min="0" max="9999999999" value="{{ request('budget') }}" placeholder="Contoh: 100000"></div><button>Temukan Partner <x-icon name="sparkle" /></button><a class="button secondary" href="{{ route('trainer.match') }}">Reset</a></div>
</form>
<div class="result-ribbon"><div><strong>Hasil Rekomendasi Partner: {{ $terrains[request('terrain')]['label'] ?? 'Semua Medan' }}</strong><small>Hanya menampilkan Pokémon yang tersedia dan sesuai filter anggaran.</small></div><span class="badge available">{{ $pokemons->total() }} Partner Siap Bertugas</span></div>
<div class="match-results">@include('partials.pokemon-cards')</div><div class="pagination">{{ $pokemons->links() }}</div>
<div class="quota-banner"><x-icon name="users" /><div><strong>Kapasitas Rental Trainer: {{ $slots }}/2 Slot</strong><p>{{ $slots < 2 ? 'Masih ada slot untuk partner petualanganmu berikutnya.' : 'Kembalikan partner untuk membuka slot petualangan baru.' }}</p></div><a class="button white small" href="{{ route('trainer.catalog.index') }}">Lihat Semua di Pokédex</a></div>
@endsection
