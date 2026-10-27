<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Trainer;
use App\Http\Middleware\EnsureRole;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('landing'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:6,1')->name('login.process');
    Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:6,1')->name('register.process');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // ROUTE ADMIN
    Route::prefix('admin')->name('admin.')->middleware(EnsureRole::class.':admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

        Route::resource('pokemons', Admin\PokemonController::class)->except('show');
        Route::resource('types', Admin\TypeController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('trainers', Admin\TrainerController::class)->only(['index', 'update', 'destroy']);
        Route::resource('admins', Admin\AdminAccountController::class)->only(['index', 'store', 'update', 'destroy']);

        // Rental dan pengembalian Pokémon
        Route::get('/rentals', [Admin\RentalController::class, 'index'])->name('rentals.index');
        Route::get('/rentals/{rental}', [Admin\RentalController::class, 'show'])->name('rentals.show');
        Route::get('/returns', [Admin\RentalController::class, 'index'])->name('returns.index');
        Route::post('/returns/{rental}', [Admin\RentalController::class, 'complete'])->name('returns.complete');

        // BARU: Pengaturan batas maksimal rental Pokémon
        Route::get('/rental-settings', [Admin\RentalSettingController::class, 'index'])
            ->name('rental-settings.index');

        Route::put('/rental-settings', [Admin\RentalSettingController::class, 'update'])
            ->name('rental-settings.update');

        // Laporan rental
        Route::get('/reports', [Admin\RentalController::class, 'report'])->name('reports.index');
        Route::get('/reports/export', [Admin\RentalController::class, 'export'])->name('reports.export');
        Route::get('/reports/print', [Admin\RentalController::class, 'printReport'])->name('reports.print');
    });

    // ROUTE TRAINER
    Route::prefix('trainer')->name('trainer.')->middleware(EnsureRole::class.':trainer')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'trainer'])->name('dashboard');
        Route::get('/catalog', [Trainer\CatalogController::class, 'index'])->name('catalog.index');
        Route::get('/catalog/{pokemon}', [Trainer\CatalogController::class, 'show'])->name('catalog.show');
        Route::get('/adventure-match', [Trainer\CatalogController::class, 'match'])->name('match');

        Route::get('/profile', [Trainer\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [Trainer\ProfileController::class, 'update'])->name('profile.update');

        Route::get('/rentals', [Trainer\RentalController::class, 'index'])->name('rentals.index');
        Route::get('/rentals/history', [Trainer\RentalController::class, 'index'])->name('rentals.history');
        Route::get('/rentals/create/{pokemon}', [Trainer\RentalController::class, 'create'])->name('rentals.create');
        Route::post('/rentals/confirm', [Trainer\RentalController::class, 'confirm'])->name('rentals.confirm');
        Route::post('/rentals', [Trainer\RentalController::class, 'store'])->name('rentals.store');
        Route::get('/rentals/{rental}', [Trainer\RentalController::class, 'show'])->name('rentals.show');
        Route::get('/rentals/{rental}/return', [Trainer\RentalController::class, 'returnForm'])->name('rentals.return');
        Route::post('/rentals/{rental}/return', [Trainer\RentalController::class, 'requestReturn'])->name('rentals.request-return');
    });
});
