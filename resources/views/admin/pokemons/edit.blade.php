@extends('layouts.app')
@section('title', 'Edit Pokémon')
@section('content')
<div class="breadcrumb"><a href="{{ route('admin.pokemons.index') }}">Kelola Pokémon</a><x-icon name="chevron" /><strong>Edit {{ $pokemon->pokemon_name }}</strong></div>
<x-page-heading eyebrow="Inventaris Unit Partner" :title="'Edit '.$pokemon->pokemon_name" description="Perbarui data dan ketersediaan partner. Riwayat tarif rental yang sudah tercatat tetap dipertahankan." />
@if($pokemon->status === 'on_adventure')<div class="notice">Partner sedang disewa. Perubahan data belum dapat disimpan.</div>@endif
<form class="panel" method="POST" action="{{ route('admin.pokemons.update', $pokemon) }}" enctype="multipart/form-data" data-validate>@csrf @method('PUT')
@include('admin.pokemons.form')
<div class="form-actions"><a class="button secondary" href="{{ route('admin.pokemons.index') }}">Kembali</a><button @disabled($pokemon->status === 'on_adventure')><x-icon name="check" />Simpan Perubahan</button></div>
</form>
@endsection
