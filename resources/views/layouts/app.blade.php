@php
    $adminLayout = auth()->user()?->isAdmin() ?? false;
    $authLayout = request()->routeIs('login', 'register');
    $navigation = $adminLayout ? [
        ['admin.dashboard', 'Dashboard', 'admin.dashboard', 'dashboard'],
        ['admin.pokemons.index', 'Kelola Pokémon', 'admin.pokemons.*', 'pokemon'],
        ['admin.types.index', 'Kelola Tipe', 'admin.types.*', 'types'],
        ['admin.trainers.index', 'Kelola Trainer', 'admin.trainers.*', 'users'],
        ['admin.rentals.index', 'Monitoring Rental', 'admin.rentals.*', 'compass'],
        ['admin.returns.index', 'Pengembalian', 'admin.returns.*', 'return'],
        ['admin.reports.index', 'Laporan', 'admin.reports.*', 'report'],
        ['admin.admins.index', 'Kelola Admin', 'admin.admins.*', 'shield'],
    ] : [
        ['trainer.dashboard', 'Beranda', 'trainer.dashboard', 'dashboard'],
        ['trainer.catalog.index', 'Jelajahi Pokémon', 'trainer.catalog.*', 'pokemon'],
        ['trainer.match', 'Adventure Match', 'trainer.match', 'sparkle'],
        ['trainer.rentals.index', 'Rental Saya', 'trainer.rentals.index', 'compass'],
        ['trainer.rentals.history', 'Riwayat', 'trainer.rentals.history', 'clock'],
    ];
@endphp
<!DOCTYPE html>
<html lang="id"><head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Partner Petualanganmu') — PokePartner</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/pokepartner.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pokepartner-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/stitch-ui.css') }}?v={{ filemtime(public_path('css/stitch-ui.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/feedback.css') }}?v={{ filemtime(public_path('css/feedback.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/interactions.css') }}?v={{ filemtime(public_path('css/interactions.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/audio-player.css') }}?v={{ filemtime(public_path('css/audio-player.css')) }}">
    @foreach(['feedback', 'form-validation', 'site', 'interactions'] as $script)<script src="{{ asset('js/'.$script.'.js') }}?v={{ filemtime(public_path('js/'.$script.'.js')) }}" defer></script>@endforeach
    <script src="{{ asset('js/audio-player.js') }}?v={{ filemtime(public_path('js/audio-player.js')) }}" defer></script>
</head>
<body class="{{ $adminLayout ? 'admin-layout' : ($authLayout ? 'auth-layout' : 'trainer-layout') }}">
<a class="skip-link" href="#main-content">Langsung ke konten</a>
@if($adminLayout)
    <button class="sidebar-backdrop" data-close-sidebar aria-label="Tutup navigasi" hidden></button>
    <aside class="admin-sidebar" id="admin-sidebar" aria-label="Navigasi admin">
        <x-brand :admin="true" :href="route('admin.dashboard')" />
        <div class="sidebar-section-label">Menu Operasional</div>
        <nav class="sidebar-nav" aria-label="Menu operasional">
            @foreach($navigation as [$destination, $label, $pattern, $icon])
                <a href="{{ route($destination) }}" @if(request()->routeIs($pattern)) aria-current="page" @endif><x-icon :name="$icon" /><span>{{ $label }}</span>@if($destination === 'admin.returns.index' && $pendingReturns)<b class="nav-count">{{ $pendingReturns }}</b>@endif</a>
            @endforeach
        </nav>
        <div class="sidebar-account"><div class="account-chip"><x-avatar :user="auth()->user()" /><div><strong>{{ auth()->user()->full_name }}</strong><small>Administrator PokéCenter</small></div><span class="online-dot"></span></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="sidebar-logout"><x-icon name="logout" />Keluar Sistem</button></form></div>
    </aside>
    <div class="admin-topbar">
        <button class="icon-button mobile-menu" type="button" data-toggle-sidebar aria-controls="admin-sidebar" aria-expanded="false" aria-label="Buka navigasi"><x-icon name="menu" /></button>
        <form class="global-search" action="{{ route('admin.pokemons.index') }}"><x-icon name="search" /><input name="search" aria-label="Cari Pokémon" placeholder="Cari Pokémon berdasarkan nama atau kode..."></form>
        <span class="topbar-date"><x-icon name="calendar" />{{ now()->translatedFormat('l, d F Y') }}</span>
        <span class="system-status"><span class="status-dot"></span>Sistem Rental Aktif</span>
        <a class="icon-button notification-link" href="{{ route('admin.returns.index') }}" aria-label="{{ $pendingReturns }} pengembalian menunggu"><x-icon name="bell" />@if($pendingReturns)<b>{{ $pendingReturns }}</b>@endif</a>
    </div>
@else
    <header class="site-header">
        <x-brand :href="auth()->check() ? route('trainer.dashboard') : route('home')" />
        @auth
            <button class="icon-button mobile-menu" type="button" data-toggle-trainer-nav aria-controls="trainer-nav" aria-expanded="false" aria-label="Buka menu"><x-icon name="menu" /></button>
            <nav id="trainer-nav" class="trainer-nav" aria-label="Navigasi utama">
                @foreach($navigation as [$destination, $label, $pattern, $icon])<a href="{{ route($destination) }}" @if(request()->routeIs($pattern) || ($destination === 'trainer.rentals.index' && request()->routeIs('trainer.rentals.show', 'trainer.rentals.return'))) aria-current="page" @endif>{{ $label }}@if($destination === 'trainer.rentals.index')<span class="slot-count">{{ $navSlots }}/2</span>@endif</a>@endforeach
            </nav>
            <details class="profile-menu"><summary><x-avatar :user="auth()->user()" /><span><strong>{{ auth()->user()->full_name }}</strong><small>Trainer Level {{ auth()->user()->trainerProfile?->trainer_level ?? 1 }}</small></span><x-icon name="chevron" /></summary><div class="profile-dropdown"><a href="{{ route('trainer.profile.edit') }}"><x-icon name="user" />Profil Saya</a><form method="POST" action="{{ route('logout') }}">@csrf<button><x-icon name="logout" />Keluar</button></form></div></details>
        @else
            <nav aria-label="Navigasi pengunjung">@if($authLayout)<span class="system-status"><span class="status-dot"></span>Akses Trainer & Admin</span>@endif<a href="{{ route('login') }}">Masuk</a><a class="button" href="{{ route('register') }}">Daftar Trainer</a></nav>
        @endauth
    </header>
@endif
<main id="main-content" class="app-main">
    @include('partials.notifications')
    @yield('content')
</main>
@unless($adminLayout)
    <footer class="site-footer"><div class="footer-top"><div><x-brand /><p>Your Perfect Pokémon Adventure Companion!</p></div>@auth<nav aria-label="Navigasi footer"><a href="{{ route('trainer.dashboard') }}">Beranda</a><a href="{{ route('trainer.catalog.index') }}">Katalog Pokémon</a><a href="{{ route('trainer.match') }}">Adventure Match</a><a href="{{ route('trainer.rentals.index') }}">Status Rental</a><a href="{{ route('trainer.profile.edit') }}">Profil Saya</a></nav>@endauth</div><div class="footer-bottom"><small>© {{ date('Y') }} PokePartner Indonesia. Seluruh hak cipta dilindungi.</small><span>Dirancang untuk ekspedisi & komunitas Trainer Pokémon.</span></div></footer>
@endunless
@include('partials.confirm-dialog')
@include('partials.notification-templates')
@include('partials.audio-player')
@stack('scripts')
</body></html>
