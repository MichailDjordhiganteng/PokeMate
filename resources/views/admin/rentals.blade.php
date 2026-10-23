@extends('layouts.app')
@section('title', $returns ? 'Pengembalian Partner' : 'Monitoring Rental')
@section('content')
@if($returns)
<section class="admin-hero watermark-surface"><x-page-heading eyebrow="Administrator · Checkpoint PokéCenter" title="Pusat Verifikasi & Finalisasi Pengembalian" description="Periksa kondisi partner, verifikasi serah terima, dan selesaikan transaksi pengembalian Pokémon."><span class="badge">{{ $waitingCount }} Pengajuan Menunggu</span></x-page-heading></section>
<div class="note-box blue"><x-icon name="shield" /><div><strong>Otoritas finalisasi berada di tangan admin</strong><p>Status partner berubah setelah pemeriksaan selesai. Pastikan identitas trainer sesuai dengan pemilik rental sebelum menyelesaikan transaksi.</p></div></div>
@else
<x-page-heading eyebrow="Pusat Kendali Lapangan" title="Monitoring Rental Partner Pokémon" description="Pantau seluruh transaksi ekspedisi aktif, jadwal pengembalian, serta keterlambatan partner."><a class="button secondary" href="{{ route('admin.reports.export', request()->only(['search', 'status', 'from', 'to'])) }}"><x-icon name="download" />Ekspor CSV</a><a class="button" href="{{ route('admin.rentals.index', request()->query()) }}"><x-icon name="refresh" />Segarkan Data</a></x-page-heading>
@if($lateCount)<div class="note-box red"><x-icon name="warning" /><div><strong>{{ $lateCount }} rental melewati jatuh tempo</strong><p>Gunakan filter Terlambat untuk meninjau partner yang belum kembali tepat waktu.</p></div><a class="button white small" href="{{ route('admin.rentals.index', ['status' => 'late']) }}">Lihat Rental</a></div>@endif
@endif
<div class="metrics-grid"><x-metric label="{{ $returns ? 'Menunggu Inspeksi' : 'Total Transaksi Aktif' }}" :value="$returns ? $waitingCount : $activeCount" suffix="Transaksi" icon="compass" tone="blue" /><x-metric label="Jatuh Tempo Hari Ini" :value="$dueTodayCount" suffix="Rental" icon="calendar" /><x-metric label="Keterlambatan" :value="$lateCount" suffix="Rental" icon="warning" tone="red" /><x-metric label="Selesai Hari Ini" :value="$completedToday" suffix="Transaksi" icon="check" tone="mint" /></div>
@include('partials.report-filters')
<div class="section-title"><h2><x-icon :name="$returns ? 'return' : 'report'" />{{ $returns ? 'Daftar Pengajuan Pengembalian' : 'Daftar Transaksi Monitoring' }}</h2><small>{{ $rentals->total() }} transaksi sesuai filter</small></div>
@include('partials.rental-table', ['admin' => true])
<div class="pagination">{{ $rentals->links() }}</div>
@foreach($rentals as $rental)
@if($rental->rental_status === 'return_requested')
<section class="panel returns-form" id="return-{{ $rental->id }}"><div class="panel-heading"><div><h2>Inspeksi Rental #{{ $rental->id }}</h2><p class="muted">{{ $rental->trainer->full_name }} · {{ $rental->rentalDetails->pluck('pokemon.pokemon_name')->join(', ') }}</p></div><x-status status="return_requested" /></div>
    <form method="POST" action="{{ route('admin.returns.complete', $rental) }}">@csrf
        <div class="field-grid"><div class="note-box"><x-icon name="clock" /><div><strong>Jadwal Pengembalian</strong><p>Jatuh tempo {{ $rental->due_date->format('d M Y') }}<br>Diajukan {{ $rental->return_requested_at->format('d M Y, H:i') }}</p></div></div><div class="note-box yellow"><x-icon name="coins" /><div><strong>Estimasi Denda</strong><p>Rp {{ number_format(max(0, (int) $rental->due_date->diffInDays($rental->return_requested_at->copy()->startOfDay(), false)) * 100000, 0, ',', '.') }}<br>Dihitung sampai pengajuan trainer.</p></div></div></div>
        <label for="condition-{{ $rental->id }}">Kondisi Partner Setelah Pemeriksaan</label><select name="condition" id="condition-{{ $rental->id }}" required><option value="available">Sehat — Tersedia untuk Disewa</option><option value="resting">Perlu Istirahat / Pemulihan</option><option value="unavailable">Tidak Tersedia / Perawatan</option></select>
        <div class="form-actions"><button><x-icon name="check" />Selesaikan Pengembalian</button></div>
    </form>
</section>
@endif
@endforeach
<div class="note-box"><x-icon name="info" /><p>Denda keterlambatan Rp 100.000 per hari kalender. Waktu tunggu pemeriksaan admin tidak menambah denda trainer.</p></div>
@endsection
