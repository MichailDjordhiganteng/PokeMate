<div class="catalog-grid">
@forelse($pokemons as $pokemon)
    <article class="card pokemon-card">
        <x-pokemon-art :pokemon="$pokemon"><div class="art-elements">@foreach($pokemon->types as $type)<x-type-badge :name="$type->type" />@endforeach</div></x-pokemon-art>
        <div class="pokemon-card-body"><div class="card-title"><h2>{{ $pokemon->pokemon_name }}</h2><x-status :status="$pokemon->status" /></div>
            @if(request()->routeIs('trainer.match'))<div class="recommendation"><x-icon name="shield" />Tipe {{ $pokemon->types->pluck('type')->join(' / ') }} sesuai filter medan dan anggaran yang dipilih.</div>@endif
            <div class="rental-rate"><span>Biaya Sewa</span><strong>Rp {{ number_format($pokemon->rental_price, 0, ',', '.') }} <small>/ hari</small></strong></div>
            <div class="actions partner-actions"><a class="button secondary small" href="{{ route('trainer.catalog.show', $pokemon) }}"><x-icon name="eye" />Detail</a>@if($pokemon->status === 'available')<a class="button small" href="{{ route('trainer.rentals.create', $pokemon) }}"><x-icon name="pokemon" />Sewa</a>@else<button class="disabled-action" disabled>{{ $pokemon->status === 'on_adventure' ? 'Sedang Disewa' : ($pokemon->status === 'resting' ? 'Pemulihan' : 'Nonaktif') }}</button>@endif</div>
        </div>
    </article>
@empty
    <div class="panel" style="grid-column:1/-1"><x-empty-state title="Belum ada partner yang cocok" description="Coba ubah filter pencarian atau pilih medan petualangan lainnya." /></div>
@endforelse
</div>
