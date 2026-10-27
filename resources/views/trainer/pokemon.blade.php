@extends('layouts.app')

@section('title', $pokemon->pokemon_name)

@section('content')

<div class="breadcrumb">
    <a href="{{ route('trainer.catalog.index') }}">
        <x-icon name="back" />
        Kembali ke Katalog
    </a>
    <x-icon name="chevron" />
    <strong>Detail Partner · {{ $pokemon->pokemon_code }}</strong>
</div>

<div class="detail-grid">

    <aside>
        <x-pokemon-art :pokemon="$pokemon" class="detail-art">
            <div class="type-list">
                @foreach($pokemon->types as $type)
                    <x-type-badge :name="$type->type" />
                @endforeach

                <div class="field-help">
                    Partner pendamping PokePartner
                </div>
            </div>
        </x-pokemon-art>

        <div class="note-box">
            <x-icon name="shield" />
            <div>
                <strong>Partner dalam pengawasan PokéCenter</strong>
                <p>Pengembalian dan ketersediaan diverifikasi oleh admin.</p>
            </div>
        </div>
    </aside>

    <section class="detail-copy">

        <span class="eyebrow">
            Pokédex · {{ $pokemon->pokemon_code }}
        </span>

        <h1>{{ $pokemon->pokemon_name }}</h1>

        <x-status :status="$pokemon->status" />

        <p>{{ $pokemon->description }}</p>

        <div class="detail-features">
            <div>
                <strong>
                    <x-icon name="types" />
                    Klasifikasi Elemen
                </strong>

                <p>{{ $pokemon->types->pluck('type')->join(' / ') }}</p>
            </div>

            <div>
                <strong>
                    <x-icon name="compass" />
                    Level Partner
                </strong>

                <p>
                    Level {{ $pokemon->pokemon_level }}
                    · Siap mendampingi petualangan sesuai ketersediaan.
                </p>
            </div>
        </div>

        <form
            class="panel"
            action="{{ route('trainer.rentals.create', $pokemon) }}"
            method="GET"
            id="detail-rental-form"
        >

            <div class="rental-rate">
                <div>
                    <span class="eyebrow">Tarif Sewa Harian</span>

                    <div class="price">
                        Rp {{ number_format($pokemon->rental_price, 0, ',', '.') }}
                        <small>/ hari</small>
                    </div>
                </div>

                {{-- DINAMIS: Mengikuti pengaturan Admin --}}
                <span class="badge">
                    {{ $slots }}/{{ $maxActivePokemon }} Slot Terpakai
                </span>
            </div>

            <label for="detail-duration">
                Pilih Durasi Petualangan
            </label>

            @include('partials.duration-picker', [
                'inputId' => 'detail-duration',
                'selectedDuration' => 3
            ])

            <dl class="summary-list">
                <div>
                    <dt>Durasi sewa</dt>
                    <dd><span id="detail-days">3</span> hari</dd>
                </div>

                <div>
                    <dt>Tarif harian</dt>
                    <dd>
                        Rp {{ number_format($pokemon->rental_price, 0, ',', '.') }}
                    </dd>
                </div>

                <div>
                    <dt>Biaya tambahan</dt>
                    <dd>Tidak ada</dd>
                </div>
            </dl>

            <div class="total">
                <div>
                    <small>Total Estimasi Biaya</small>
                    <strong class="price" id="detail-total"></strong>
                </div>
            </div>

            <div class="note-box">
                <x-icon name="info" />

                <p>
                    Maksimal 5 hari dan
                    {{ $maxActivePokemon }} partner
                    dalam rental belum selesai.
                    Denda keterlambatan Rp 100.000/hari kalender.
                </p>
            </div>

            {{-- DINAMIS: Tombol mengikuti kapasitas terbaru --}}
            @if($pokemon->status === 'available')

                <button
                    class="full-width"
                    @disabled($slots >= $maxActivePokemon)
                >
                    Sewa Sekarang (Lanjut ke Form Sewa)
                    <x-icon name="arrow" />
                </button>

            @else

                <button class="full-width secondary" disabled>
                    Partner Belum Tersedia
                </button>

            @endif

            @if($slots >= $maxActivePokemon)
                <span class="field-help">
                    Kuota partner penuh. Selesaikan pengembalian
                    untuk membuka slot baru.
                </span>
            @endif

        </form>

    </section>

</div>

<section class="panel section">
    <div class="panel-heading">
        <h2>
            <x-icon name="report" />
            Protokol Petualangan & Panduan Pendamping
        </h2>
    </div>

    <div class="rule-grid">

        <div>
            <x-icon name="calendar" />
            <h3>Rencanakan Jadwal</h3>
            <p>
                Pilih tanggal mulai dan durasi sewa
                sesuai rencana ekspedisi.
            </p>
        </div>

        <div>
            <x-icon name="shield" />
            <h3>Jaga Kondisi Partner</h3>
            <p>
                Pastikan partner terawat dan siap diperiksa
                saat kembali ke PokéCenter.
            </p>
        </div>

        <div>
            <x-icon name="return" />
            <h3>Kembalikan Tepat Waktu</h3>
            <p>
                Ajukan pengembalian melalui Rental Saya
                lalu serahkan partner kepada admin.
            </p>
        </div>

    </div>
</section>

@endsection

@push('scripts')
<script>
(() => {
    const input = document.getElementById('detail-duration');

    const update = () => {
        document.getElementById('detail-days').textContent = input.value;

        document.getElementById('detail-total').textContent =
            new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(
                Number(input.value) * {{ (float) $pokemon->rental_price }}
            );
    };

    input.addEventListener('change', update);
    update();
})();
</script>
@endpush
