<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Rental — PokePartner</title>
    <link rel="stylesheet" href="{{ asset('css/pokepartner.css') }}">
    <link rel="stylesheet" href="{{ asset('css/report-print.css') }}">
</head>
<body>
<main>
    <div class="actions print-controls">
        <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
        <a href="{{ route('admin.reports.index', request()->only(['search', 'status', 'from', 'to'])) }}">Kembali ke Laporan</a>
    </div>
    <h1>Laporan Rental PokePartner</h1>
    <p>Dicetak: {{ now()->format('d M Y H:i') }} WIB · Oleh: {{ auth()->user()->full_name }}</p>
    <p>
        Periode tanggal mulai: {{ request('from') ?: 'Awal' }} s.d. {{ request('to') ?: 'Sekarang' }}<br>
        Status: {{ request('status') ? __('status.'.request('status')) : 'Semua Status' }}
        @if(request('search')) · Nama Trainer: {{ request('search') }} @endif
    </p>
    <div class="grid">
        <div class="panel">Jumlah Transaksi<strong>{{ $count }}</strong></div>
        <div class="panel">Pendapatan Rental Selesai<strong>Rp {{ number_format($income, 0, ',', '.') }}</strong></div>
        <div class="panel">Denda Rental Selesai<strong>Rp {{ number_format($fees, 0, ',', '.') }}</strong></div>
    </div>
    @include('partials.rental-table', ['admin' => true])
    <p class="muted">Ringkasan pendapatan dan denda hanya menghitung rental berstatus selesai. Daftar di atas memuat seluruh transaksi sesuai filter.</p>
</main>
</body>
</html>
