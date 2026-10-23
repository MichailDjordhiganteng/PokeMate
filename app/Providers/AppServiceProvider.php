<?php

namespace App\Providers;

use App\Models\Rental;
use App\Services\RentalService;
use App\View\Composers\LandingPageComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('landing', LandingPageComposer::class);

        View::composer('layouts.app', function ($view) {
            $user = auth()->user();
            $user?->loadMissing('trainerProfile');
            $view->with('navSlots', $user?->isTrainer() ? app(RentalService::class)->usedSlots($user) : 0);
            $view->with('pendingReturns', $user?->isAdmin() ? Rental::where('rental_status', 'return_requested')->count() : 0);
        });
    }
}
