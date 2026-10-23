@extends('layouts.app')
@section('title', 'Tambah Pokémon')
@section('content')
<div class="breadcrumb"><a href="{{ route('admin.pokemons.index') }}">Kelola Pokémon</a><x-icon name="chevron" /><strong>Tambah Partner</strong></div>
<x-page-heading eyebrow="Registrasi Unit Partner" title="Tambah Pokémon Baru" description="Lengkapi identitas, klasifikasi elemen, tarif harian, dan foto partner sebelum ditampilkan di katalog." />
<form class="panel" method="POST" action="{{ route('admin.pokemons.store') }}" enctype="multipart/form-data" data-validate>@csrf
@include('admin.pokemons.form')
<div class="form-actions"><a class="button secondary" href="{{ route('admin.pokemons.index') }}">Batal</a><button><x-icon name="check" />Simpan Pokémon</button></div>
</form>
@endsection
