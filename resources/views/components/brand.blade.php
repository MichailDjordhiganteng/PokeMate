@props(['href' => null, 'admin' => false])
<a class="brand" href="{{ $href ?? route('home') }}" aria-label="PokePartner — Beranda">
    <img class="brand-icon" src="{{ asset('images/pokeball.png') }}" alt="" width="36" height="36">
    <span class="brand-copy"><span class="brand-wordmark">Poke<span class="brand-accent">Partner</span>@if($admin)<b class="admin-label">ADMIN</b>@endif</span><small>{{ $admin ? 'Rental Management System' : 'Your Perfect Pokémon Adventure Companion!' }}</small></span>
</a>
