@extends('layouts.app')

@section('title', 'Konfirmasi Rental')

@section('content')

<div class="confirmation-page">

    <div class="breadcrumb">
        <a href="{{ route('trainer.catalog.index') }}">
            Jelajahi Pokémon
        </a>

        <x-icon name="chevron" />

        <a href="{{ route('trainer.rentals.create', ['pokemon' => $pokemon] + $data) }}">
            Form Sewa
        </a>

        <x-icon name="chevron" />

        <strong>Konfirmasi Rental</strong>
    </div>

    <x-steps :active="3" />

    <x-page-heading
        eyebrow="Tinjau & Konfirmasi Sewa Partner"
        title="Konfirmasi Petualanganmu"
        description="Pastikan data partner, jadwal ekspedisi, dan ketentuan rental sudah sesuai sebelum menyelesaikan pemesanan."
    />

    <section class="panel confirmation-partner">

        <x-pokemon-art :pokemon="$pokemon" />

        <div>
            <h2>
                {{ $pokemon->pokemon_name }}
                <span class="badge">
                    Lv. {{ $pokemon->pokemon_level }}
                </span>
            </h2>

            @foreach($pokemon->types as $type)
                <x-type-badge :name="$type->type" />
            @endforeach

            <p>{{ $pokemon->description }}</p>

            <div class="note-box" style="margin-bottom:0">
                <x-icon name="map" />

                <p>
                    <strong>Tujuan Petualangan</strong>
                    {{ $data['destination'] }}
                </p>
            </div>
        </div>

    </section>

    <section class="panel">

        <div class="panel-heading">
            <h2>
                <x-icon name="calendar" />
                Rincian Jadwal & Biaya Sewa
            </h2>

            <span class="badge">Ekspedisi Partner</span>
        </div>

        <div class="schedule-grid">

            <div>
                <small>Tanggal Mulai</small>
                <strong>
                    {{ \Carbon\Carbon::parse($data['start_date'])->format('d M Y') }}
                </strong>
            </div>

            <div>
                <small>Jatuh Tempo</small>
                <strong>
                    {{ \Carbon\Carbon::parse($data['start_date'])->addDays((int) $data['duration'])->format('d M Y') }}
                </strong>
            </div>

            <div>
                <small>Durasi Ekspedisi</small>
                <strong>{{ $data['duration'] }} Hari</strong>
            </div>

            <div class="schedule-total">
                <small>Total Estimasi</small>
                <strong>
                    Rp {{ number_format($pokemon->rental_price * $data['duration'], 0, ',', '.') }}
                </strong>
            </div>

        </div>

        @if(!empty($data['notes']))
            <div class="note-box">
                <x-icon name="report" />

                <p>
                    <strong>Catatan Khusus Trainer</strong>
                    {{ $data['notes'] }}
                </p>
            </div>
        @endif

    </section>

    <section class="panel">

        <div class="panel-heading">
            <h2>
                <x-icon name="shield" />
                Peringatan & Regulasi Wajib Trainer
            </h2>
        </div>

        <div class="rule-grid">

            <div>
                <x-icon name="users" />

                <h3>Batas Kuota Partner</h3>

                <p>
                    Maksimal {{ $maxActivePokemon }} Pokémon
                    dalam rental yang belum selesai.
                    Ketersediaan dan kuota diperiksa ulang
                    saat pemesanan disimpan.
                </p>
            </div>

            <div>
                <x-icon name="clock" />

                <h3>Batas Pengembalian</h3>

                <p>
                    Durasi maksimal 5 hari.
                    Denda setelah tanggal jatuh tempo sebesar
                    Rp 100.000 per hari kalender.
                </p>
            </div>

            <div>
                <x-icon name="shield" />

                <h3>Verifikasi Admin</h3>

                <p>
                    Pengembalian partner diselesaikan oleh admin
                    setelah pemeriksaan kondisi dan serah terima.
                </p>
            </div>

        </div>

    </section>

    <form
        class="panel"
        method="POST"
        action="{{ route('trainer.rentals.store') }}"
    >
        @csrf

        @foreach($data as $key => $value)
            <input
                type="hidden"
                name="{{ $key }}"
                value="{{ $value }}"
            >
        @endforeach

        <div class="row">

            <a
                class="button secondary"
                href="{{ route('trainer.rentals.create', ['pokemon' => $pokemon] + $data) }}"
            >
                <x-icon name="back" />
                Ubah Rincian Sewa
            </a>

            <button type="submit">
                <x-icon name="bolt" />
                Konfirmasi & Sewa Partner

                <span>
                    (Rp {{ number_format($pokemon->rental_price * $data['duration'], 0, ',', '.') }})
                </span>
            </button>

        </div>

        <p
            class="field-help"
            style="text-align:center;margin-top:16px"
        >
            Harga dihitung oleh sistem dari tarif partner
            saat pemesanan disimpan.
        </p>

    </form>

</div>

@endsection
