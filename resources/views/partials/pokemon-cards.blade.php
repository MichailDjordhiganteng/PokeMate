@php
    // Mengambil batas maksimal Pokémon dari pengaturan Admin.
    // Jika variabel sudah dikirim Controller, gunakan nilai tersebut.
    // Jika belum tersedia, ambil langsung dari database.
    $maxActivePokemon = $maxActivePokemon
        ?? \App\Models\RentalSetting::findOrFail(1)->max_active_pokemon;

    // Menghitung slot Trainer jika belum dikirim Controller.
    $slots = $slots
        ?? app(\App\Services\RentalService::class)->usedSlots(auth()->user());
@endphp

<div class="catalog-grid">

    @forelse($pokemons as $pokemon)

        <article class="card pokemon-card">

            <x-pokemon-art :pokemon="$pokemon">
                <div class="art-elements">

                    @foreach($pokemon->types as $type)
                        <x-type-badge :name="$type->type" />
                    @endforeach

                </div>
            </x-pokemon-art>

            <div class="pokemon-card-body">

                <div class="card-title">
                    <h2>{{ $pokemon->pokemon_name }}</h2>
                    <x-status :status="$pokemon->status" />
                </div>

                @if(request()->routeIs('trainer.match'))

                    <div class="recommendation">
                        <x-icon name="shield" />

                        Tipe {{ $pokemon->types->pluck('type')->join(' / ') }}
                        sesuai filter medan dan anggaran yang dipilih.
                    </div>

                @endif

                <div class="rental-rate">
                    <span>Biaya Sewa</span>

                    <strong>
                        Rp {{ number_format($pokemon->rental_price, 0, ',', '.') }}
                        <small>/ hari</small>
                    </strong>
                </div>

                <div class="actions partner-actions">

                    <a
                        class="button secondary small"
                        href="{{ route('trainer.catalog.show', $pokemon) }}"
                    >
                        <x-icon name="eye" />
                        Detail
                    </a>

                    {{-- Pokémon tersedia dan kuota masih cukup --}}
                    @if(
                        $pokemon->status === 'available'
                        && $slots < $maxActivePokemon
                    )

                        <a
                            class="button small"
                            href="{{ route('trainer.rentals.create', $pokemon) }}"
                        >
                            <x-icon name="pokemon" />
                            Sewa
                        </a>

                    {{-- Pokémon tersedia tetapi kuota Trainer penuh --}}
                    @elseif($pokemon->status === 'available')

                        <button
                            type="button"
                            class="disabled-action"
                            disabled
                        >
                            Kuota Penuh
                        </button>

                    {{-- Pokémon tidak tersedia --}}
                    @else

                        <button
                            type="button"
                            class="disabled-action"
                            disabled
                        >
                            {{ $pokemon->status === 'on_adventure'
                                ? 'Sedang Disewa'
                                : ($pokemon->status === 'resting'
                                    ? 'Pemulihan'
                                    : 'Nonaktif') }}
                        </button>

                    @endif

                </div>
            </div>

        </article>

    @empty

        <div class="panel" style="grid-column:1/-1">

            <x-empty-state
                title="Belum ada partner yang cocok"
                description="Coba ubah filter pencarian atau pilih medan petualangan lainnya."
            />

        </div>

    @endforelse

</div>
