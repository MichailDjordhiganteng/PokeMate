<header class="lp-header" data-landing-header>
    <div class="lp-container lp-navbar">
        <div class="lp-brand"><x-brand /></div>
        <button class="lp-menu-toggle" type="button" aria-expanded="false" aria-controls="landing-navigation" aria-label="Buka menu navigasi" data-landing-menu-toggle>
            <span class="lp-menu-icon" aria-hidden="true"><span></span><span></span><span></span></span>
        </button>
        <nav class="lp-navigation" id="landing-navigation" aria-label="Navigasi utama">
            <div class="lp-nav-links">
                <a href="#explore" data-section-link="explore">Explore</a>
                <a href="#how-it-works" data-section-link="how-it-works">How It Works</a>
                <a href="#about" data-section-link="about">About</a>
            </div>
            <div class="lp-nav-actions">
                @auth
                    <a class="lp-login" href="{{ route($landingAdmin ? 'admin.dashboard' : 'trainer.dashboard') }}">Dashboard</a>
                    <a class="lp-button lp-button--small lp-button--primary" href="{{ $catalogUrl }}">Explore Partners <x-icon name="arrow" /></a>
                @else
                    <a class="lp-login" href="{{ route('login') }}">Login</a>
                    <a class="lp-button lp-button--small lp-button--primary" href="{{ route('register') }}">Daftar Trainer <x-icon name="arrow" /></a>
                @endauth
            </div>
        </nav>
    </div>
</header>
