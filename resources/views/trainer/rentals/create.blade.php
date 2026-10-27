@extends('layouts.app')

@section('title', 'Form Sewa Partner')

@section('content')

<div class="breadcrumb">
    <a href="{{ route('trainer.catalog.index') }}">Jelajahi Pokémon</a>
    <x-icon name="chevron" />
    <a href="{{ route('trainer.catalog.show', $pokemon) }}">
        Detail {{ $pokemon->pokemon_name }}
    </a>
    <x-icon name="chevron" />
    <strong>Form Sewa Partner</strong>
</div>

{{-- Status kuota mengikuti pengaturan Admin --}}
<div class="quota-banner">
    <x-icon name="shield" />

    <div>
        <strong>
            Status Kuota Petualangan:
            {{ $slots }} dari {{ $maxActivePokemon }} Slot Terpakai
        </strong>

        <p>
            {{ $slots < $maxActivePokemon
                ? 'Masih tersedia slot untuk Pokémon pendamping tambahan.'
                : 'Kuota penuh. Selesaikan pengembalian partner sebelum menyewa lagi.' }}
        </p>
    </div>

    <span class="badge">
        {{ max(0, $maxActivePokemon - $slots) }} SLOT TERSEDIA
    </span>
</div>

<x-page-heading
    eyebrow="Registrasi Petualangan Lapangan"
    title="Form Pengajuan Sewa Pokémon"
    description="Lengkapi rencana ekspedisi sebelum mengonfirmasi ketersediaan partner petualangan."
/>

<div class="checkout-grid">

    <form
        class="panel"
        method="POST"
        action="{{ route('trainer.rentals.confirm') }}"
        id="rental-form"
        data-validate
        data-validation-title="Lengkapi rencana petualangan"
    >
        @csrf

        <input
            type="hidden"
            name="pokemon_id"
            value="{{ $pokemon->id }}"
        >

        <div class="panel-heading">
            <h2>
                <x-icon name="calendar" />
                Jadwal & Rencana Petualangan
            </h2>

            <span class="badge">Wajib Diisi</span>
        </div>

        <label for="start_date">
            Tanggal Mulai Petualangan
        </label>

        <input
            type="date"
            id="start_date"
            name="start_date"
            min="{{ today()->toDateString() }}"
            value="{{ old('start_date', request('start_date', today()->toDateString())) }}"
            required
            data-required-message="Pilih tanggal mulai petualangan."
            data-invalid-message="Tanggal mulai harus valid dan tidak boleh sebelum hari ini."
        >

        <label for="duration">
            Pilihan Durasi Petualangan
        </label>

        @include('partials.duration-picker', [
            'selectedDuration' => old('duration', request('duration', 3))
        ])

        <div class="note-box">
            <x-icon name="info" />
            <p>
                Durasi sewa maksimal
                <strong style="display:inline">5 hari</strong>
                agar jadwal istirahat partner tetap terjaga.
            </p>
        </div>

        <div class="due-date-box">
            <x-icon name="clock" />

            <div>
                <small>Jatuh Tempo Pengembalian</small>
                <strong id="due-date"></strong>
            </div>
        </div>

        <label for="destination">
            Tujuan & Medan Petualangan
        </label>

        <input
            id="destination"
            name="destination"
            maxlength="255"
            value="{{ old('destination', request('destination')) }}"
            placeholder="Contoh: Hutan Viridian"
            list="adventure-destinations"
            required
            data-required-message="Isi tujuan dan medan petualangan, misalnya Hutan Viridian."
        >

        <datalist id="adventure-destinations">
            <option value="Hutan Viridian">
            <option value="Gunung Moon">
            <option value="Sungai Cerulean">
            <option value="Taman Kota Saffron">
        </datalist>

        <label for="notes">
            Catatan Khusus Trainer <small>(Opsional)</small>
        </label>

        <textarea
            id="notes"
            name="notes"
            maxlength="2000"
            rows="4"
            placeholder="Rencana perjalanan atau kebutuhan khusus partner..."
        >{{ old('notes', request('notes')) }}</textarea>

        {{-- Persetujuan menggunakan kuota dinamis --}}
        <label class="check">
            <input
                id="agreement"
                type="checkbox"
                name="agreement"
                value="1"
                required
                @checked(old('agreement'))
                data-validation-label="Persetujuan sewa"
                data-required-message="Centang persetujuan aturan sewa sebelum melanjutkan."
            >

            Saya menyetujui durasi maksimal 5 hari,
            batas {{ $maxActivePokemon }} partner,
            dan denda keterlambatan Rp 100.000 per hari kalender.
        </label>

        <div class="form-actions">
            <a
                class="button secondary"
                href="{{ route('trainer.catalog.show', $pokemon) }}"
            >
                Batal & Kembali
            </a>

            {{-- Tombol hanya nonaktif jika kuota penuh atau Pokémon tidak tersedia --}}
            <button
                @disabled(
                    $slots >= $maxActivePokemon ||
                    $pokemon->status !== 'available'
                )
            >
                Lanjut ke Konfirmasi
                <x-icon name="arrow" />
            </button>
        </div>

        @if($slots >= $maxActivePokemon || $pokemon->status !== 'available')
            <p class="field-help">
                Kuota penuh atau partner sedang tidak tersedia.
            </p>
        @endif

    </form>

    {{-- Ringkasan Pokémon dan estimasi biaya --}}
    <aside>
        <section class="panel rental-summary">

            <x-pokemon-art :pokemon="$pokemon" :meta="false">
                <span class="art-status">
                    <x-status :status="$pokemon->status" />
                </span>
            </x-pokemon-art>

            <div class="summary-body">

                <div class="row">
                    <div>
                        <span class="eyebrow">
                            {{ $pokemon->pokemon_code }}
                            · Level {{ $pokemon->pokemon_level }}
                        </span>

                        <h2>{{ $pokemon->pokemon_name }}</h2>
                    </div>

                    <div>
                        @foreach($pokemon->types as $type)
                            <x-type-badge :name="$type->type" />
                        @endforeach
                    </div>
                </div>

                <p class="muted" style="font-size:12px">
                    {{ $pokemon->description }}
                </p>

                <div class="rental-rate">
                    <span>Tarif Sewa Harian</span>

                    <strong>
                        Rp {{ number_format($pokemon->rental_price, 0, ',', '.') }}
                        <small>/ hari</small>
                    </strong>
                </div>

                <span
                    class="eyebrow"
                    style="display:block;margin-top:22px"
                >
                    Rincian Perhitungan Biaya
                </span>

                <dl class="summary-list">
                    <div>
                        <dt>
                            Biaya sewa
                            (<span id="cost-days">3</span> hari)
                        </dt>
                        <dd id="subtotal-price"></dd>
                    </div>

                    <div>
                        <dt>Biaya tambahan</dt>
                        <dd>Tidak ada</dd>
                    </div>

                    <div>
                        <dt>Harga per hari</dt>
                        <dd>
                            Rp {{ number_format($pokemon->rental_price, 0, ',', '.') }}
                        </dd>
                    </div>
                </dl>

                <div class="total">
                    <small>Total Estimasi Biaya</small>
                    <strong class="price" id="total-price"></strong>
                </div>

            </div>
        </section>

        <div class="note-box">
            <x-icon name="shield" />

            <div>
                <strong>Kebijakan Pengembalian</strong>

                <p>
                    Denda Rp 100.000/hari kalender setelah jatuh tempo
                    hingga tanggal pengajuan pengembalian.
                    Harga dan ketersediaan diperiksa ulang saat konfirmasi.
                </p>
            </div>
        </div>
    </aside>

</div>

@endsection

@push('scripts')
<script>
(() => {
    const start = document.getElementById('start_date');
    const duration = document.getElementById('duration');

    function calculate() {
        const days = Number(duration.value);
        const date = new Date(start.value + 'T00:00:00');

        date.setDate(date.getDate() + days);

        document.getElementById('due-date').textContent = isNaN(date)
            ? 'Pilih tanggal mulai'
            : date.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });

        const total = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(days * {{ (float) $pokemon->rental_price }});

        document.getElementById('cost-days').textContent = days;
        document.getElementById('subtotal-price').textContent = total;
        document.getElementById('total-price').textContent = total;
    }

    start.addEventListener('change', calculate);
    duration.addEventListener('change', calculate);

    calculate();
})();
</script>
@endpush
