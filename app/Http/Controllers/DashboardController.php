<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use App\Models\Rental;
use App\Models\Type;
use App\Models\User;
use App\Services\RentalService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function admin()
    {
        $distribution = Pokemon::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');
        $total = $distribution->sum();
        $late = Rental::whereIn('rental_status', ['active', 'late'])->whereDate('due_date', '<', today())->count();
        $week = collect(range(6, 0))->map(function ($offset) {
            $day = today()->subDays($offset);

            return [
                'label' => $day->translatedFormat('D'),
                'new' => Rental::whereDate('created_at', $day)->count(),
                'returned' => Rental::whereDate('returned_at', $day)->count(),
            ];
        });

        return view('admin.dashboard', [
            'stats' => [
                'Total Pokémon' => Pokemon::count(),
                'Partner Tersedia' => Pokemon::where('status', 'available')->count(),
                'Trainer Terdaftar' => User::where('role', 'trainer')->count(),
                'Menunggu Pengembalian' => Rental::where('rental_status', 'return_requested')->count(),
            ],
            'rentals' => Rental::with('trainer', 'rentalDetails.pokemon')->latest()->limit(5)->get(),
            'distribution' => $distribution,
            'totalPokemon' => $total,
            'trainerCount' => User::where('role', 'trainer')->count(),
            'activeCount' => Rental::whereIn('rental_status', ['active', 'late'])->count(),
            'lateCount' => $late,
            'completedCount' => Rental::where('rental_status', 'completed')->count(),
            'waitingCount' => Rental::where('rental_status', 'return_requested')->count(),
            'typeCount' => Type::count(),
            'week' => $week,
            'weekMax' => max(1, $week->max('new'), $week->max('returned')),
        ]);
    }

    public function trainer(Request $request, RentalService $service)
    {
        $rentals = $request->user()->rentals()->with('rentalDetails.pokemon.types')
            ->whereIn('rental_status', RentalService::OPEN_STATUSES)->latest()->get();

        return view('trainer.dashboard', [
            'slots' => $service->usedSlots($request->user()),
            'rentals' => $rentals,
            'nearestDue' => $rentals->sortBy('due_date')->first()?->due_date,
            'lateCount' => $rentals->filter(fn ($rental) => in_array($rental->rental_status, ['active', 'late'])
                && $rental->due_date->lt(today()))->count(),
            'pokemons' => Pokemon::with('types')->where('status', 'available')->limit(4)->get(),
        ]);
    }
}
