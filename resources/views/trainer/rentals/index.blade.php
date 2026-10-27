@extends('layouts.app')

@section('title', $history ? 'Riwayat Rental' : 'Rental Saya')

@section('content')

<div class="breadcrumb">
    <a href="{{ route('trainer.dashboard') }}">Beranda</a>
    <x-icon name="chevron" />
    <strong>{{ $history ? 'Riwayat Rental' : 'Rental Saya' }}</strong>
</div>

{{-- Judul halaman mengikuti kuota maksimal dari Admin --}}
<x-page-heading
    :eyebrow="$history ? 'Log Trainer' : 'Partner Ekspedisi'"
    :title="$history ? 'Riwayat Rental Partner' : 'Rental Saya — Tim Petualanganku'"
    :description="$history
        ? 'Catatan ekspedisi yang sudah selesai dan transaksi yang dibatalkan milik akun Anda.'
        : 'Kelola partner Pokémon yang sedang menemanimu berpetualang. Maksimal '.$maxActivePokemon.' partner sekaligus.'"
>
    <a class="button" href="{{ route('trainer.catalog.index') }}">
        <x-icon name="plus" />
        {{ $history ? 'Sewa Baru' : 'Cari Partner Baru' }}
    </a>

    <a
        class="button white"
        href="{{ route($history ? 'trainer.rentals.index' : 'trainer.rentals.history') }}"
    >
        {{ $history ? 'Rental Aktif' : 'Riwayat Rental' }}
    </a>
</x-page-heading>

@if($history)

    {{-- Statistik Riwayat Rental --}}
    <div class="metrics-grid">

        <x-metric
            label="Total Transaksi"
            :value="$allCount"
            suffix="Transaksi"
            icon="report"
        />

        <x-metric
            label="Rental Selesai"
            :value="$completedCount"
            suffix="Selesai"
            icon="check"
            tone="mint"
        />

        {{-- DIUBAH: Mengikuti batas rental dari Admin --}}
        <x-metric
            label="Slot Rental Terpakai"
            :value="$slots.'/'.$maxActivePokemon"
            suffix="Partner"
            icon="compass"
            tone="blue"
        />

        <x-metric
            label="Total Denda Final"
            :value="'Rp '.number_format($totalFees, 0, ',', '.')"
            icon="coins"
            tone="yellow"
            class="metric-money"
        />

    </div>

    <form class="filters">
        <div>
            <label for="search">Cari Riwayat Partner</label>

            <div class="catalog-search">
                <x-icon name="search" />

                <input
                    id="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari berdasarkan nama Pokémon..."
                >
            </div>
        </div>

        <button>Cari Riwayat</button>

        <a
            class="button secondary"
            href="{{ route('trainer.rentals.history') }}"
        >
            Reset
        </a>
    </form>

    @include('partials.rental-table', ['admin' => false])

@else

    {{-- Statistik Rental Aktif --}}
    <div class="metrics-grid three">

        {{-- DIUBAH: Kapasitas mengikuti pengaturan Admin --}}
        <x-metric
            label="Kapasitas Squad"
            :value="$slots.'/'.$maxActivePokemon"
            suffix="Slot Terpakai"
            tone="mint"
            icon="users"
            :hint="$slots < $maxActivePokemon
                ? 'Masih tersedia slot partner'
                : 'Kapasitas tim penuh'"
        />

        <x-metric
            label="Jatuh Tempo Terdekat"
            :value="$nearestDue
                ? \Carbon\Carbon::parse($nearestDue)->format('d M Y')
                : 'Belum Ada'"
            tone="blue"
            icon="clock"
            hint="Jaga jadwal kepulangan partner"
        />

        <x-metric
            label="Total Denda Final"
            :value="'Rp '.number_format($totalFees, 0, ',', '.')"
            tone="purple"
            icon="shield"
            class="metric-money"
            hint="Dari transaksi yang sudah diproses"
        />

    </div>

    <div class="section-title">
        <h2>
            <x-icon name="pokemon" />
            Partner Ekspedisi Berjalan
        </h2>

        <small>
            {{ $rentals->total() }} transaksi belum selesai
        </small>
    </div>

    <div class="active-rentals">

        @forelse($rentals as $rental)

            @include('partials.adventure-card')

        @empty

            <div class="panel" style="grid-column:1/-1">
                <x-empty-state
                    title="Belum ada partner dalam petualangan"
                    description="Pilih partner dari katalog untuk memulai ekspedisi pertamamu."
                >
                    <a
                        class="button"
                        href="{{ route('trainer.catalog.index') }}"
                    >
                        Jelajahi Pokémon
                    </a>
                </x-empty-state>
            </div>

        @endforelse

    </div>

@endif

<div class="pagination">
    {{ $rentals->links() }}
</div>

<div class="regulation-banner">
    <x-icon name="shield" />

    <div>
        <strong>PEMBERITAHUAN POKÉCENTER</strong>

        <p>
            {{ $history
                ? 'Riwayat ini hanya menampilkan transaksi milik akun yang sedang masuk.'
                : 'Ajukan pengembalian melalui sistem lalu serahkan partner kepada admin untuk menyelesaikan transaksi.' }}
        </p>
    </div>

    <a class="button ghost" href="{{ route('trainer.match') }}">
        Cek Adventure Match
    </a>
</div>

@endsection
