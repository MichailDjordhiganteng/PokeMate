@extends('layouts.app')
@section('title', 'Masuk')
@section('content')
<div class="auth-shell">
    @include('partials.auth-story', ['register' => false])
    <section class="auth-form-panel"><span class="eyebrow heading-tag">Otentikasi Akun · Trainer & Admin</span><h1>Selamat Datang Kembali, Trainer!</h1><p class="muted">Masuk ke PokePartner dan lanjutkan petualanganmu bersama Pokémon favorit.</p>
        <div class="note-box blue"><x-icon name="info" /><p><strong>Akses Satu Pintu</strong>Sistem mengarahkan Anda ke dashboard sesuai peran akun Trainer atau Admin.</p></div>
        <form method="POST" action="{{ route('login.process') }}" data-validate>@csrf
            <label for="email">Alamat Email Trainer / Admin</label><div class="input-icon"><x-icon name="mail" /><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" placeholder="contoh: trainer@gmail.com" required data-required-message="Masukkan alamat email akun Anda."></div>
            <label for="password">Kata Sandi</label><div class="input-icon"><x-icon name="lock" /><input id="password" type="password" name="password" autocomplete="current-password" placeholder="Masukkan kata sandi" required data-required-message="Masukkan kata sandi akun Anda."><button type="button" data-toggle-password="password" aria-label="Tampilkan password" aria-pressed="false"><x-icon name="eye" /></button></div>
            <label class="check"><input type="checkbox" name="remember" value="1" @checked(old('remember'))>Ingat saya di perangkat ini</label>
            @if(app()->environment('local'))<div class="demo-accounts"><small>PILIH AKSES CEPAT (AKUN DEMO)</small><div class="actions"><button class="secondary small" type="button" data-fill-demo="trainer@gmail.com">Trainer Demo</button><button class="secondary small" type="button" data-fill-demo="admin@gmail.com">Admin Demo</button></div></div>@endif
            <button type="submit" class="full-width">Masuk ke PokePartner <x-icon name="arrow" /></button>
        </form><p class="auth-switch">Belum punya akun trainer? <a href="{{ route('register') }}">Daftar Sekarang</a></p>
    </section>
</div>
@endsection
