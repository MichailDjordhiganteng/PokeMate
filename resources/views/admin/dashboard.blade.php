@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('content')
<section class="admin-hero watermark-surface">
    <x-page-heading eyebrow="Wilayah Kanto · Pusat Data Regional" title="Dashboard Manajemen PokePartner" description="Pusat kendali operasional rental partner Pokémon. Pantau ketersediaan, ekspedisi aktif, dan pengembalian dari satu tempat.">
        <a class="button ghost" href="{{ route('admin.reports.print') }}" target="_blank" rel="noopener"><x-icon name="print" />Cetak Ringkasan</a>
        <a class="button" href="{{ route('admin.pokemons.create') }}"><x-icon name="plus" />Tambah Pokémon Baru</a>
    </x-page-heading>
</section>
<div class="metrics-grid">
    <x-metric label="Total Pokémon" :value="$totalPokemon" suffix="Ekor" hint="Tercatat di Pokédex partner" tone="mint" />
    <x-metric label="Pokémon Tersedia" :value="$distribution['available'] ?? 0" suffix="Siap Sewa" hint="Partner siap menemani ekspedisi" tone="blue" icon="check" />
    <x-metric label="Sedang Berpetualang" :value="$distribution['on_adventure'] ?? 0" suffix="Aktif" hint="Sedang menemani trainer" tone="purple" icon="compass" />
    <x-metric label="Istirahat / Pemulihan" :value="($distribution['resting'] ?? 0) + ($distribution['unavailable'] ?? 0)" suffix="Partner" hint="Rehat atau belum tersedia" tone="peach" icon="shield" />
    <x-metric label="Trainer Terdaftar" :value="$trainerCount" suffix="Trainer" hint="Anggota komunitas PokePartner" icon="users" />
    <x-metric label="Rental Aktif" :value="$activeCount" suffix="Transaksi" hint="Petualangan yang masih berjalan" icon="report" />
    <x-metric label="Rental Terlambat" :value="$lateCount" suffix="Transaksi" hint="Melewati tanggal jatuh tempo" tone="red" icon="warning" />
    <x-metric label="Rental Selesai" :value="$completedCount" suffix="Transaksi" hint="Pengembalian telah diverifikasi" icon="check" />
</div>
<div class="admin-charts">
    <section class="panel"><div class="panel-heading"><div><h2>Distribusi Status Partner Pokémon</h2><p class="muted">Ketersediaan seluruh partner dalam inventaris.</p></div><x-icon name="pokemon" /></div>
        <div class="donut-layout"><div class="status-donut" style="--available: {{ $totalPokemon ? ($distribution['available'] ?? 0) / $totalPokemon * 100 : 0 }}%; --adventure: {{ $totalPokemon ? (($distribution['available'] ?? 0) + ($distribution['on_adventure'] ?? 0)) / $totalPokemon * 100 : 0 }}%" role="img" aria-label="{{ $distribution['available'] ?? 0 }} tersedia, {{ $distribution['on_adventure'] ?? 0 }} berpetualang, {{ ($distribution['resting'] ?? 0) + ($distribution['unavailable'] ?? 0) }} perawatan"><div><strong>{{ $totalPokemon }}</strong><small>Pokémon</small></div></div><div class="chart-legend"><div><i class="legend-dot"></i>Tersedia <b>{{ $distribution['available'] ?? 0 }}</b></div><div><i class="legend-dot yellow"></i>Berpetualang <b>{{ $distribution['on_adventure'] ?? 0 }}</b></div><div><i class="legend-dot purple"></i>Rehat / Perawatan <b>{{ ($distribution['resting'] ?? 0) + ($distribution['unavailable'] ?? 0) }}</b></div></div></div>
        <div class="note-box"><x-icon name="info" /><p>Data diambil dari status partner yang tersimpan saat halaman dimuat.</p></div>
    </section>
    <section class="panel"><div class="panel-heading"><div><h2>Aktivitas Rental & Pengembalian</h2><p class="muted">Perbandingan transaksi dalam 7 hari terakhir.</p></div></div><div class="row"><small><i class="legend-dot yellow"></i> Rental Baru</small><small><i class="legend-dot"></i> Pengembalian</small></div><div class="weekly-chart" role="img" aria-label="Grafik rental dan pengembalian tujuh hari terakhir">@foreach($week as $day)<div class="chart-day"><div class="chart-bar" style="height: {{ $day['new'] / $weekMax * 100 }}%" title="{{ $day['new'] }} rental baru"></div><div class="chart-bar returned" style="height: {{ $day['returned'] / $weekMax * 100 }}%" title="{{ $day['returned'] }} pengembalian"></div><small>{{ $day['label'] }}</small></div>@endforeach</div><div class="note-box"><x-icon name="calendar" /><p>{{ now()->subDays(6)->format('d M') }} – {{ now()->format('d M Y') }} · {{ $week->sum('new') }} rental baru dan {{ $week->sum('returned') }} pengembalian.</p></div></section>
</div>
<div class="section-title"><h2>Pintasan Cepat Manajemen</h2><small>Akses langsung ke operasional PokéCenter</small></div>
<div class="quick-links">
    <a class="quick-link" href="{{ route('admin.pokemons.index') }}"><span class="metric-icon"><x-icon name="pokemon" /></span><h3>Kelola Pokémon</h3><p>Katalog, tarif harian, level, dan ketersediaan partner.</p><small>{{ $totalPokemon }} UNIT TERDAFTAR →</small></a>
    <a class="quick-link" href="{{ route('admin.types.index') }}"><span class="metric-icon"><x-icon name="types" /></span><h3>Kelola Tipe</h3><p>Klasifikasi elemen untuk rekomendasi petualangan.</p><small>{{ $typeCount }} TIPE ELEMEN →</small></a>
    <a class="quick-link" href="{{ route('admin.trainers.index') }}"><span class="metric-icon"><x-icon name="users" /></span><h3>Kelola Trainer</h3><p>Informasi kontak, level, dan riwayat rental trainer.</p><small>{{ $trainerCount }} TRAINER →</small></a>
    <a class="quick-link" href="{{ route('admin.returns.index') }}"><span class="metric-icon"><x-icon name="return" /></span><h3>Verifikasi Pengembalian</h3><p>Pemeriksaan kondisi partner dan finalisasi rental.</p><small>{{ $waitingCount }} PERLU VERIFIKASI →</small></a>
</div>
<div class="section-title"><h2>Transaksi Rental Terbaru</h2><a href="{{ route('admin.rentals.index') }}">Lihat Semua Transaksi →</a></div>
@include('partials.rental-table', ['admin' => true])
@endsection
