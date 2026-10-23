@extends('layouts.app')
@section('title', 'Dashboard Trainer')
@section('content')
<section class="hero trainer-hero watermark-surface">
    <div>
        <span class="eyebrow">Pokémon Adventure Companion</span>
        <h1>Selamat Datang, Trainer {{ auth()->user()->full_name }}! ⚡</h1>
        <p>Siap untuk petualangan baru hari ini? Sewa partner Pokémon untuk menjelajahi rute, pegunungan, dan hutan dengan aman dan menyenangkan.</p>
        <div class="actions"><a class="button" href="{{ route('trainer.catalog.index') }}">Jelajahi Pokémon →</a><a class="button secondary" href="{{ route('trainer.match') }}">✧ Adventure Match</a></div>
    </div>
    <aside class="trainer-license">
        <span class="eyebrow">Identitas Trainer</span>
        @if(auth()->user()->trainerProfile?->avatar)
            <img class="trainer-avatar" src="{{ asset('storage/'.auth()->user()->trainerProfile->avatar) }}" alt="Avatar {{ auth()->user()->full_name }}">
        @endif
        <strong>#TRN-{{ str_pad(auth()->id(), 5, '0', STR_PAD_LEFT) }}</strong>
        <p>Level {{ auth()->user()->trainerProfile?->trainer_level ?? 1 }}<br>{{ auth()->user()->trainerProfile?->address ?? 'Lengkapi profil trainermu' }}</p>
        <a href="{{ route('trainer.profile.edit') }}">Lihat Profil →</a>
    </aside>
</section>

<div class="grid adventure-stats">
    <section class="panel"><span class="eyebrow">Status Tim & Slot</span><strong>{{ $slots }} <small>/ 2 Slot Terpakai</small></strong><progress value="{{ $slots }}" max="2" aria-label="Slot partner terpakai"></progress><p class="muted">{{ 2 - $slots }} slot partner tersedia</p></section>
    <section class="panel"><span class="eyebrow">Jatuh Tempo Terdekat</span><strong>{{ $nearestDue?->format('d M Y') ?? 'Belum Ada' }}</strong><p class="muted">{{ $nearestDue ? 'Pastikan partner kembali sesuai jadwal.' : 'Mulai perjalanan dengan memilih partner.' }}</p></section>
    <section class="panel"><span class="eyebrow">Status Pengembalian</span><strong>{{ $lateCount > 0 ? $lateCount.' Rental Terlambat' : 'Tepat Waktu' }}</strong><p class="muted">{{ $lateCount > 0 ? 'Segera ajukan pengembalian partnermu.' : 'Tidak ada rental aktif melewati jatuh tempo.' }}</p></section>
</div>

<div class="row section"><div><h2>Tim Petualanganku <span class="badge">Slot Aktif: {{ $slots }}/2</span></h2><p class="muted">Pokémon yang menemanimu dalam penjelajahan.</p></div><a href="{{ route('trainer.rentals.index') }}">Lihat Semua →</a></div>
<div class="team-grid">
    @foreach($rentals as $rental)
        @foreach($rental->rentalDetails as $detail)
            <article class="panel team-card">
                <div class="team-partner">
                    <div class="art">@if($detail->pokemon->imageUrl())<img src="{{ $detail->pokemon->imageUrl() }}" alt="{{ $detail->pokemon->pokemon_name }}">@else<span class="placeholder" aria-hidden="true">◉</span>@endif</div>
                    <div>
                        <small>{{ $detail->pokemon->pokemon_code }} · Lv. {{ $detail->pokemon->pokemon_level }}</small>
                        <h3>{{ $detail->pokemon->pokemon_name }}</h3>
                        <span class="badge {{ $rental->rental_status }}">{{ __('status.'.$rental->rental_status) }}</span>
                        <p><small>Masa Sewa</small><br>{{ $rental->start_date->format('d M') }} – {{ $rental->due_date->format('d M Y') }}</p>
                    </div>
                </div>
                <div class="row"><small>{{ $rental->destination }}</small><a class="button secondary small" href="{{ route('trainer.rentals.show', $rental) }}">Detail & Pengembalian</a></div>
            </article>
        @endforeach
    @endforeach
    @if($slots < 2)
        <section class="panel open-slot"><div class="slot-icon" aria-hidden="true">⊕</div><span class="badge">{{ 2 - $slots }} Slot Tersedia</span><h3>Slot Partner Masih Kosong</h3><p class="muted">Lengkapi tim ekspedisimu dengan Pokémon pendamping pilihan.</p><a class="button" href="{{ route('trainer.catalog.index') }}">+ Pilih Partner Sekarang</a></section>
    @endif
</div>

<div class="row section"><div><h2>Rekomendasi Partner Tersedia</h2><p class="muted">Partner tangguh yang siap berangkat menemanimu.</p></div><a href="{{ route('trainer.catalog.index') }}">Lihat Semua Katalog →</a></div>
<div class="dashboard-partners">@include('partials.pokemon-cards')</div>
<section class="hero expedition-tip"><div><span class="eyebrow">Tips Ekspedisi</span><h2>Sesuaikan tipe partner dengan medan tujuanmu.</h2><p>Tipe Water cocok untuk jalur perairan, sedangkan Grass dan Flying dapat menemani eksplorasi hutan.</p></div><a class="button" href="{{ route('trainer.match') }}">✧ Cek Adventure Match</a></section>
@endsection
