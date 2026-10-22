<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\PokemonController;

// Halaman awal
Route::get('/', function () {
    return redirect()->route('login');
});

// Halaman untuk pengunjung yang belum login
Route::middleware('guest')->group(function () {

    // Login
    Route::get('/login', [LoginController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:6,1')
        ->name('login.process');

    // Register Trainer
    Route::get('/register', [RegisterController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [RegisterController::class, 'register'])
        ->middleware('throttle:6,1')
        ->name('register.process');
});

// Halaman untuk pengguna yang sudah login
Route::middleware('auth')->group(function () {

    // =====================================
    // DASHBOARD ADMIN
    // =====================================
    Route::get('/admin/dashboard', function () {
        abort_unless(auth()->user()->role === 'admin', 403);

        return view('admin.dashboard');
    })->name('admin.dashboard');

    // =====================================
    // KELOLA POKÉMON - KHUSUS ADMIN
    // =====================================

    // READ - Menampilkan daftar dan pencarian Pokémon
    Route::get('/admin/pokemons', [PokemonController::class, 'index'])
        ->name('admin.pokemons.index');

    // CREATE - Menampilkan form tambah Pokémon
    Route::get('/admin/pokemons/create', [PokemonController::class, 'create'])
        ->name('admin.pokemons.create');

    // STORE - Menyimpan Pokémon baru
    Route::post('/admin/pokemons', [PokemonController::class, 'store'])
        ->name('admin.pokemons.store');

    // EDIT - Menampilkan form edit Pokémon
    Route::get('/admin/pokemons/{pokemon}/edit', [PokemonController::class, 'edit'])
        ->name('admin.pokemons.edit');

    // UPDATE - Menyimpan perubahan Pokémon
    Route::put('/admin/pokemons/{pokemon}', [PokemonController::class, 'update'])
        ->name('admin.pokemons.update');

    // DELETE - Menghapus Pokémon
    Route::delete('/admin/pokemons/{pokemon}', [PokemonController::class, 'destroy'])
        ->name('admin.pokemons.destroy');

    // =====================================
    // DASHBOARD TRAINER
    // =====================================
    Route::get('/trainer/dashboard', function () {
        abort_unless(auth()->user()->role === 'trainer', 403);

        return view('trainer.dashboard');
    })->name('trainer.dashboard');

    // =====================================
    // LOGOUT
    // =====================================
    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');
});
