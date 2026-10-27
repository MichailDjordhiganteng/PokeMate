@extends('layouts.app')

@section('title', 'Kelola Trainer')

@section('content')

<section class="admin-hero watermark-surface">
    <x-page-heading
        eyebrow="Registrasi Resmi Trainer"
        title="Kelola Akun Trainer"
        description="Kelola identitas, data kontak, level trainer, serta pantau kapasitas partner dan riwayat petualangan."
    />
</section>

{{-- Statistik Trainer --}}
<div class="metrics-grid">
    <x-metric
        label="Total Trainer"
        :value="$totalTrainers"
        hint="Terdaftar di PokePartner"
        icon="users"
    />

    <x-metric
        label="Trainer Aktif Sewa"
        :value="$activeTrainers"
        hint="Memiliki rental belum selesai"
        icon="compass"
        tone="blue"
    />

    <x-metric
        label="Trainer Baru Bulan Ini"
        :value="$newTrainers"
        hint="Bergabung pada bulan berjalan"
        icon="user"
    />

    <x-metric
        label="Ekspedisi Selesai"
        :value="$completedRentals"
        hint="Pengembalian telah diproses"
        icon="check"
        tone="mint"
    />
</div>

{{-- BARU: Pengaturan Batas Maksimal Rental --}}
<div class="table-wrap" style="padding: 24px; margin-bottom: 24px;">

    <div style="display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap; margin-bottom: 20px;">
        <div>
            <h2 style="margin: 0 0 8px;">
                Pengaturan Batas Rental
            </h2>

            <p style="margin: 0; color: #687386;">
                Tentukan jumlah maksimal Pokémon yang dapat disewa
                setiap Trainer dalam transaksi yang belum selesai.
            </p>
        </div>

        <span class="badge">
            Batas Saat Ini: {{ $maxActivePokemon }} Pokémon
        </span>
    </div>

    <form
        method="POST"
        action="{{ route('admin.rental-settings.update') }}"
        style="display: flex; align-items: flex-end; gap: 14px; flex-wrap: wrap;"
    >
        @csrf
        @method('PUT')

        <div style="flex: 1; min-width: 220px;">
            <label for="max_active_pokemon">
                Maksimal Pokémon per Trainer
            </label>

            <input
                type="number"
                id="max_active_pokemon"
                name="max_active_pokemon"
                min="1"
                max="20"
                step="1"
                value="{{ old('max_active_pokemon', $maxActivePokemon) }}"
                required
            >
        </div>

        <button type="submit" class="navy">
            Simpan Pengaturan
        </button>
    </form>

    @error('max_active_pokemon')
        <p style="color: #b42318; margin-top: 12px;">
            {{ $message }}
        </p>
    @enderror

    <p style="font-size: 13px; color: #687386; margin: 14px 0 0;">
        Perubahan berlaku untuk penyewaan berikutnya.
        Rental yang sedang berlangsung tidak otomatis dibatalkan.
    </p>

</div>

{{-- Pencarian Trainer --}}
<form class="filters">
    <div>
        <label for="search">Cari Trainer</label>

        <div class="catalog-search">
            <x-icon name="search" />

            <input
                id="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari berdasarkan nama atau email..."
            >
        </div>
    </div>

    <button class="navy">Cari Trainer</button>

    <a href="{{ route('admin.trainers.index') }}" class="button secondary">
        Reset
    </a>
</form>

{{-- Tabel Trainer --}}
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>ID Trainer</th>
                <th>Avatar & Nama Trainer</th>
                <th>Kontak</th>
                <th>Domisili</th>
                <th>Level</th>
                <th>Slot Sewa</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse($trainers as $trainer)
                @php
                    $usedSlots = $trainer->rentals->sum('rental_details_count');
                @endphp

                <tr>
                    <td>
                        <span class="cell-code">
                            #TRN-{{ str_pad($trainer->id, 5, '0', STR_PAD_LEFT) }}
                        </span>
                    </td>

                    <td>
                        <div class="cell-user">
                            <x-avatar :user="$trainer" />

                            <div>
                                <strong>{{ $trainer->full_name }}</strong>

                                <small>
                                    {{ $trainer->rentals_count }} transaksi rental
                                </small>
                            </div>
                        </div>
                    </td>

                    <td>
                        {{ $trainer->email }}
                        <br>

                        <small>
                            {{ $trainer->trainerProfile?->phone ?? 'Belum diisi' }}
                        </small>
                    </td>

                    <td>
                        {{ Str::limit($trainer->trainerProfile?->address ?? 'Belum diisi', 55) }}
                    </td>

                    <td>
                        <span class="badge">
                            Level {{ $trainer->trainerProfile?->trainer_level ?? 1 }}
                        </span>
                    </td>

                    {{-- DIUBAH: Slot rental mengikuti pengaturan database --}}
                    <td>
                        <strong>
                            {{ $usedSlots }}/{{ $maxActivePokemon }}
                        </strong>

                        <progress
                            value="{{ min($usedSlots, $maxActivePokemon) }}"
                            max="{{ $maxActivePokemon }}"
                            aria-label="Slot terpakai"
                        ></progress>

                        @if($usedSlots > $maxActivePokemon)
                            <small style="color: #b42318;">
                                Melebihi batas terbaru
                            </small>
                        @endif
                    </td>

                    <td>
                        <div class="cell-actions">
                            <button
                                class="icon-button"
                                type="button"
                                data-open-dialog="edit-trainer-{{ $trainer->id }}"
                                aria-label="Edit trainer {{ $trainer->full_name }}"
                            >
                                <x-icon name="edit" />
                            </button>

                            <form
                                method="POST"
                                action="{{ route('admin.trainers.destroy', $trainer) }}"
                                data-confirm="Hapus akun trainer?"
                                data-confirm-description="Akun {{ $trainer->full_name }} ({{ $trainer->email }}) akan dihapus. Trainer dengan riwayat rental tetap dilindungi sistem."
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    class="icon-button danger"
                                    aria-label="Hapus trainer {{ $trainer->full_name }}"
                                >
                                    <x-icon name="trash" />
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="7">
                        <x-empty-state
                            title="Trainer tidak ditemukan"
                            description="Coba nama atau alamat email lain."
                            icon="users"
                        />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination">
    {{ $trainers->links() }}
</div>

{{-- Modal Edit Trainer --}}
@foreach($trainers as $trainer)

    <x-modal
        :id="'edit-trainer-'.$trainer->id"
        :title="'Edit Trainer '.$trainer->full_name"
    >
        <form
            method="POST"
            action="{{ route('admin.trainers.update', $trainer) }}"
            data-validate
        >
            @csrf
            @method('PUT')

            <div class="field-grid">
                <div>
                    <label for="name-{{ $trainer->id }}">
                        Nama Lengkap
                    </label>

                    <input
                        id="name-{{ $trainer->id }}"
                        name="full_name"
                        value="{{ $trainer->full_name }}"
                        required
                        maxlength="255"
                    >
                </div>

                <div>
                    <label for="email-{{ $trainer->id }}">
                        Email
                    </label>

                    <input
                        id="email-{{ $trainer->id }}"
                        type="email"
                        name="email"
                        value="{{ $trainer->email }}"
                        required
                    >
                </div>

                <div>
                    <label for="phone-{{ $trainer->id }}">
                        Telepon
                    </label>

                    <input
                        id="phone-{{ $trainer->id }}"
                        name="phone"
                        value="{{ $trainer->trainerProfile?->phone }}"
                        maxlength="20"
                        required
                    >
                </div>

                <div>
                    <label for="level-{{ $trainer->id }}">
                        Level Trainer
                    </label>

                    <input
                        id="level-{{ $trainer->id }}"
                        type="number"
                        name="trainer_level"
                        min="1"
                        max="100"
                        value="{{ $trainer->trainerProfile?->trainer_level ?? 1 }}"
                        required
                    >
                </div>

                <div class="span-2">
                    <label for="address-{{ $trainer->id }}">
                        Alamat
                    </label>

                    <textarea
                        id="address-{{ $trainer->id }}"
                        name="address"
                        required
                        maxlength="2000"
                    >{{ $trainer->trainerProfile?->address }}</textarea>
                </div>
            </div>

            <div class="form-actions">
                <button
                    class="secondary"
                    type="button"
                    data-close-dialog
                >
                    Batal
                </button>

                <button type="submit">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </x-modal>

@endforeach

@endsection
