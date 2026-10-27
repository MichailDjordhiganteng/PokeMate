@extends('layouts.app')

@section('title', 'Katalog Pokémon')

@section('content')

<section class="catalog-console">

    <x-page-heading
        eyebrow="Pokédex Modul Ekspedisi"
        title="Katalog Pokédex Partner Petualangan"
        :description="'Temukan Pokémon setia yang siap mendampingi ekspedisi alam liar. Maksimal '.$maxActivePokemon.' partner dalam rental yang belum selesai.'"
    >
        {{-- Kuota mengikuti pengaturan Admin --}}
        <span class="system-status">
            <x-icon name="users" />
            {{ $slots }}/{{ $maxActivePokemon }} Slot Terpakai
        </span>
    </x-page-heading>

    {{-- Form pencarian dan filter --}}
    <form method="GET">

        <div class="catalog-search">
            <x-icon name="search" />

            <input
                id="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama Pokémon atau kode unit..."
                aria-label="Cari nama atau kode Pokémon"
            >
        </div>

        <div class="filters">

            <div>
                <label for="type_id">Elemen Tipe</label>

                <select id="type_id" name="type_id">
                    <option value="">Semua Tipe Elemen</option>

                    @foreach($types as $type)
                        <option
                            value="{{ $type->id }}"
                            @selected(request('type_id') == $type->id)
                        >
                            {{ $type->type }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status">Status Ketersediaan</label>

                <select id="status" name="status">
                    <option value="">Semua Status</option>

                    @foreach([
                        'available',
                        'on_adventure',
                        'resting',
                        'unavailable'
                    ] as $status)
                        <option
                            value="{{ $status }}"
                            @selected(request('status') === $status)
                        >
                            {{ __('status.'.$status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="sort">Urutkan Berdasarkan</label>

                <select id="sort" name="sort">

                    @foreach([
                        'code' => 'Kode Partner',
                        'price_low' => 'Tarif Terendah',
                        'price_high' => 'Tarif Tertinggi',
                        'level' => 'Level Tertinggi'
                    ] as $key => $label)

                        <option
                            value="{{ $key }}"
                            @selected(request('sort', 'code') === $key)
                        >
                            {{ $label }}
                        </option>

                    @endforeach

                </select>
            </div>

            <button>Cari Partner</button>
        </div>

        <div class="filter-summary">
            <span>
                Filter aktif:
                <b>
                    {{ request('status')
                        ? __('status.'.request('status'))
                        : 'Semua Status' }}
                </b>

                <a href="{{ route('trainer.catalog.index') }}">
                    Reset Filter
                </a>
            </span>

            <span>
                Menampilkan {{ $pokemons->count() }}
                dari {{ $pokemons->total() }} Pokémon
            </span>
        </div>

    </form>

</section>

{{-- Kartu Pokémon tetap menggunakan komponen aslinya --}}
@include('partials.pokemon-cards')

<div class="regulation-banner">
    <x-icon name="info" />

    <div>
        <strong>ATURAN RESMI POKÉDEX RENTAL</strong>

        <p>
            Durasi sewa maksimal 5 hari.
            Keterlambatan dikenakan denda
            Rp 100.000 per hari kalender.
        </p>
    </div>

    <a
        class="button ghost small"
        href="{{ route('trainer.match') }}"
    >
        Cari Partner Sesuai Medan
        <x-icon name="arrow" />
    </a>
</div>

<div class="pagination">
    {{ $pokemons->links() }}
</div>

@endsection
