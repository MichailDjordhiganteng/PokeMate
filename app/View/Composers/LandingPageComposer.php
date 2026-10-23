<?php

namespace App\View\Composers;

use App\Models\Pokemon;
use App\Models\Type;
use Illuminate\View\View;

class LandingPageComposer
{
    public function compose(View $view): void
    {
        $partners = Pokemon::with('types')
            ->orderByRaw('CASE WHEN status = ? THEN 0 ELSE 1 END', ['available'])
            ->orderBy('pokemon_code')
            ->limit(4)
            ->get();
        $types = Type::whereIn('type', ['Fire', 'Water', 'Grass', 'Electric', 'Flying', 'Psychic', 'Normal', 'Poison'])
            ->get()->keyBy(fn ($type) => strtolower($type->type));
        $admin = auth()->user()?->isAdmin() ?? false;

        $view->with([
            'featuredPartners' => $partners,
            'heroPartner' => $partners->first(),
            'partnerCount' => Pokemon::count(),
            'typeCount' => Type::count(),
            'explorerTypes' => $types,
            'landingAdmin' => $admin,
            'catalogUrl' => route($admin ? 'admin.pokemons.index' : 'trainer.catalog.index'),
            'rentalUrl' => route($admin ? 'admin.rentals.index' : 'trainer.rentals.index'),
            'adventureUrl' => auth()->check()
                ? route($admin ? 'admin.dashboard' : 'trainer.catalog.index')
                : route('register'),
        ]);
    }
}
