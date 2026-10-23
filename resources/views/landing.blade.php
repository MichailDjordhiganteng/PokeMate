@extends('layouts.app')
@section('title', 'Partner Petualanganmu')
@section('content')
<section class="hero">
    <span class="eyebrow">Pokémon Companion System</span>
    <h1>Petualangan hebat dimulai dengan partner yang tepat.</h1>
    <p>Temukan Pokémon pendamping, rencanakan ekspedisimu, dan jelajahi dunia bersama PokePartner.</p>
    @auth
        <a class="button" href="{{ route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'trainer.dashboard') }}">Buka Dashboard →</a>
    @else
        <a class="button" href="{{ route('register') }}">Mulai Petualangan →</a>
        <a class="button secondary" href="{{ route('login') }}">Saya Sudah Punya Akun</a>
    @endauth
</section>
<div class="grid">
    <section class="panel"><span class="eyebrow">01 · Temukan</span><h2>Partner Pilihanmu</h2><p class="muted">Jelajahi katalog berdasarkan tipe, level, dan ketersediaan Pokémon.</p></section>
    <section class="panel"><span class="eyebrow">02 · Rencanakan</span><h2>Adventure Match</h2><p class="muted">Temukan partner yang sesuai dengan medan dan anggaran petualanganmu.</p></section>
    <section class="panel"><span class="eyebrow">03 · Jelajahi</span><h2>Sewa & Berpetualang</h2><p class="muted">Sewa hingga 2 partner selama 1–5 hari, lalu kembalikan melalui PokéCenter.</p></section>
</div>
@endsection
