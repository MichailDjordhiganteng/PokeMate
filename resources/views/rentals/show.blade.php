@extends('layouts.app')
@section('title', 'Detail Rental')
@section('content')
@php
    $listRoute = $adminView ? 'admin.rentals.index' : 'trainer.rentals.index';
    $homeRoute = $adminView ? 'admin.dashboard' : 'trainer.dashboard';
@endphp
<div class="breadcrumb"><a href="{{ route($listRoute) }}"><x-icon name="back" />{{ $adminView ? 'Monitoring Rental' : 'Rental Saya' }}</a><x-icon name="chevron" /><strong>Detail Rental #{{ $rental->id }}</strong></div>
@if(!$adminView && session('success') && $rental->rental_status === 'active')
    <div class="success-heading"><span><x-icon name="check" />TRANSAKSI RENTAL BERHASIL</span><h1>Petualangan Dimulai! Partner Siap Menemanimu</h1><p>Pokémon berhasil dipesan. Simpan ringkasan transaksi dan pastikan jadwal pengembalian sesuai rencana.</p></div>
@else
    <x-page-heading eyebrow="Tiket Ekspedisi Resmi" :title="'Rental #RNT-'.str_pad($rental->id, 5, '0', STR_PAD_LEFT)" description="Ringkasan partner, trainer penyewa, jadwal petualangan, dan status pengembalian rental." />
@endif
<div class="checkout-grid ticket-grid">
    <aside>
        @foreach($rental->rentalDetails as $detail)
            <section class="panel rental-summary">
                <x-pokemon-art :pokemon="$detail->pokemon" />
                <div class="summary-body">
                    <span class="eyebrow">{{ $detail->pokemon->pokemon_code }} · Level {{ $detail->pokemon->pokemon_level }}</span>
                    <h2>{{ $detail->pokemon->pokemon_name }}</h2>
                    @foreach($detail->pokemon->types as $type)
                        <x-type-badge :name="$type->type" />
                    @endforeach
                    <p class="muted" style="font-size:12px;margin-top:14px">{{ $detail->pokemon->description }}</p>
                    <div class="rental-rate"><span>Tarif Saat Sewa</span><strong>Rp {{ number_format($detail->rental_price, 0, ',', '.') }} <small>/ hari</small></strong></div>
                </div>
            </section>
        @endforeach
    </aside>
    <section class="panel rental-ticket">
        <div class="ticket-heading"><x-icon name="report" /><div><small>TIKET EKSPEDISI RESMI</small><h2>Ringkasan Konfirmasi Sewa</h2></div></div>
        <div class="ticket-body">
            <div class="schedule-grid">
                <div><small>Kode Transaksi Rental</small><strong>#RNT-{{ str_pad($rental->id, 5, '0', STR_PAD_LEFT) }}</strong></div>
                <div><small>Trainer Penyewa</small><strong>{{ $rental->trainer->full_name }}</strong></div>
            </div>
            <dl class="summary-list">
                <div><dt>Periode Rental</dt><dd>{{ $rental->start_date->format('d M Y') }} – {{ $rental->due_date->format('d M Y') }}</dd></div>
                <div><dt>Durasi Ekspedisi</dt><dd>{{ (int) $rental->start_date->diffInDays($rental->due_date) }} Hari</dd></div>
                <div><dt>Tujuan & Medan</dt><dd>{{ $rental->destination ?? '—' }}</dd></div>
                <div><dt>Status Rental</dt><dd><x-status :status="$rental->rental_status" /></dd></div>
                @if($rental->return_requested_at)
                    <div><dt>Pengembalian Diajukan</dt><dd>{{ $rental->return_requested_at->format('d M Y H:i') }}</dd></div>
                @endif
                @if($rental->returned_at)
                    <div><dt>Diselesaikan Admin</dt><dd>{{ $rental->processedByAdmin?->full_name }}<br>{{ $rental->returned_at->format('d M Y H:i') }}</dd></div>
                @endif
            </dl>
            @if($rental->notes)
                <div class="note-box"><x-icon name="report" /><p><strong>Catatan Trainer</strong>{{ $rental->notes }}</p></div>
            @endif
            <div class="rental-rate"><span>Biaya Sewa</span><strong>Rp {{ number_format($rental->total_price, 0, ',', '.') }}</strong></div>
            <dl class="summary-list"><div><dt>Denda Final</dt><dd>Rp {{ number_format($rental->late_fee, 0, ',', '.') }}</dd></div></dl>
            <div class="total"><small>Total Biaya Rental</small><strong class="price">Rp {{ number_format($rental->total_price + $rental->late_fee, 0, ',', '.') }}</strong></div>
            @if($rental->rental_status === 'return_requested')
                <div class="note-box blue"><x-icon name="shield" /><p>{{ $adminView ? 'Pengembalian menunggu pemeriksaan. Cocokkan identitas trainer dan kondisi partner sebelum memproses inspeksi.' : 'Menunggu pemeriksaan admin. Silakan serahkan partner di PokéCenter.' }}</p></div>
            @endif
            <div class="actions">
                @if($adminView)
                    @if($rental->rental_status === 'return_requested')
                        <a class="button" href="{{ route('admin.returns.index') }}#return-{{ $rental->id }}"><x-icon name="shield" />Proses Inspeksi</a>
                    @endif
                    <a class="button secondary" href="{{ route($listRoute) }}">Kembali ke Monitoring</a>
                @else
                    @if(in_array($rental->rental_status, ['active', 'late']))
                        <a class="button" href="{{ route('trainer.rentals.return', $rental) }}">Ajukan Pengembalian <x-icon name="return" /></a>
                    @else
                        <a class="button" href="{{ route('trainer.rentals.index') }}">Lihat Tim Petualanganku <x-icon name="arrow" /></a>
                    @endif
                @endif
                <a class="button navy" href="{{ route($homeRoute) }}">Kembali ke Beranda</a>
            </div>
        </div>
    </section>
</div>
<div class="note-box blue"><x-icon name="info" /><div><strong>Pengembalian Partner</strong><p>{{ $adminView ? 'Trainer mengajukan pengembalian melalui sistem. Admin menyelesaikan transaksi setelah pemeriksaan dan serah terima partner.' : 'Ajukan pengembalian melalui sistem kemudian serahkan partner kepada admin.' }} Denda dihitung Rp 100.000/hari kalender hingga tanggal pengajuan pengembalian.</p></div></div>
@endsection
