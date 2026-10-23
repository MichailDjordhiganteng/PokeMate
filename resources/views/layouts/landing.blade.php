<!DOCTYPE html>
<html lang="id" class="lp-document">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Temukan Pokémon companion yang sesuai dengan medan, kebutuhan, dan gaya petualanganmu. Jelajahi partner dan rencanakan ekspedisi bersama PokePartner.">
    <meta name="theme-color" content="#0F2347">
    <title>PokePartner — Your Perfect Adventure Companion</title>
    <link rel="icon" type="image/png" href="{{ asset('images/pokeball.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @if($heroPartner && str_starts_with($heroPartner->imageUrl() ?? '', 'https://raw.githubusercontent.com/'))
        <link rel="preconnect" href="https://raw.githubusercontent.com">
    @endif
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v={{ filemtime(public_path('css/landing.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/feedback.css') }}?v={{ filemtime(public_path('css/feedback.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/audio-player.css') }}?v={{ filemtime(public_path('css/audio-player.css')) }}">
    <script src="{{ asset('js/feedback.js') }}?v={{ filemtime(public_path('js/feedback.js')) }}" defer></script>
    <script src="{{ asset('js/landing.js') }}?v={{ filemtime(public_path('js/landing.js')) }}" defer></script>
    <script src="{{ asset('js/audio-player.js') }}?v={{ filemtime(public_path('js/audio-player.js')) }}" defer></script>
</head>
<body class="lp-body">
    <a class="lp-skip" href="#main-content">Langsung ke konten</a>
    @include('landing.navbar')
    <main id="main-content">
        @include('partials.notifications')
        @yield('content')
    </main>
    @include('landing.footer')
    @include('partials.notification-templates')
    @include('partials.audio-player')
</body>
</html>
