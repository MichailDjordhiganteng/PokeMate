@extends('layouts.app')
@section('title', 'Registrasi Trainer')
@section('content')
<div class="auth-shell">
    @include('partials.auth-story', ['register' => true])
    <section class="auth-form-panel"><h1>Mulai Petualanganmu Bersama PokePartner!</h1><p class="muted">Daftar sebagai trainer dan temukan Pokémon yang cocok menemani petualanganmu.</p><div class="note-box mint"><x-icon name="shield" /><p>Registrasi publik khusus akun Trainer. Akun Admin dikelola melalui menu administrasi.</p></div>
        <form method="POST" action="{{ route('register.process') }}" data-validate>@csrf
            <label for="full_name">Nama Lengkap</label><input id="full_name" name="full_name" value="{{ old('full_name') }}" maxlength="255" required autocomplete="name" placeholder="Nama lengkap trainer">
            <label for="email">Alamat Email</label><div class="input-icon"><x-icon name="mail" /><input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="contoh: trainer@email.com"></div>
            <div class="field-grid"><div><label for="phone">Nomor Telepon</label><input id="phone" type="tel" name="phone" value="{{ old('phone') }}" maxlength="20" required autocomplete="tel" placeholder="08xxxxxxxxxx"></div><div><label for="address">Domisili / Alamat</label><input id="address" name="address" value="{{ old('address') }}" required autocomplete="street-address" placeholder="Alamat tempat tinggal"></div></div>
            <div class="field-grid"><div><label for="password">Kata Sandi</label><div class="input-icon"><x-icon name="lock" /><input id="password" type="password" name="password" required minlength="8" autocomplete="new-password" placeholder="Minimal 8 karakter"><button type="button" data-toggle-password="password" aria-label="Tampilkan password" aria-pressed="false"><x-icon name="eye" /></button></div></div><div><label for="password_confirmation">Konfirmasi Kata Sandi</label><input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi"></div></div>
            <span class="field-help">Gunakan minimal 8 karakter untuk melindungi akun petualanganmu.</span><button type="submit" class="full-width"><x-icon name="pokemon" />Daftar Sebagai Trainer</button>
        </form><p class="auth-switch">Sudah punya akun? <a href="{{ route('login') }}">Masuk ke Akun</a></p>
    </section>
</div>
@endsection
