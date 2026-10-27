<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\RentalSetting;
use App\Models\User;
use App\Services\RentalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TrainerController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100']
        ]);

        // Mengambil pengaturan batas maksimal rental dari database.
        $rentalSetting = RentalSetting::findOrFail(1);
        $maxActivePokemon = $rentalSetting->max_active_pokemon;

        // Mengambil data Trainer beserta profil dan jumlah rental.
        $trainers = User::where('role', 'trainer')
            ->with('trainerProfile')
            ->withCount('rentals')
            ->with([
                'rentals' => fn ($q) => $q
                    ->whereIn('rental_status', RentalService::OPEN_STATUSES)
                    ->withCount('rentalDetails')
            ])
            ->when(
                $request->filled('search'),
                fn ($q) => $q->where(
                    fn ($q) => $q
                        ->where('full_name', 'like', '%'.$request->search.'%')
                        ->orWhere('email', 'like', '%'.$request->search.'%')
                )
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.trainers', [
            'trainers' => $trainers,
            'totalTrainers' => User::where('role', 'trainer')->count(),
            'activeTrainers' => User::where('role', 'trainer')
                ->whereHas(
                    'rentals',
                    fn ($q) => $q->whereIn(
                        'rental_status',
                        RentalService::OPEN_STATUSES
                    )
                )
                ->count(),
            'completedRentals' => Rental::where(
                'rental_status',
                'completed'
            )->count(),
            'newTrainers' => User::where('role', 'trainer')
                ->where('created_at', '>=', now()->startOfMonth())
                ->count(),

            // Data baru untuk pengaturan kuota dinamis.
            'maxActivePokemon' => $maxActivePokemon,
        ]);
    }

    public function update(Request $request, User $trainer)
    {
        abort_unless($trainer->isTrainer(), 404);

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($trainer->id)
            ],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:2000'],
            'trainer_level' => [
                'required',
                'integer',
                'between:1,100'
            ],
        ]);

        DB::transaction(function () use ($trainer, $data) {
            $trainer->update(
                collect($data)
                    ->only(['full_name', 'email'])
                    ->all()
            );

            $trainer->trainerProfile()->updateOrCreate(
                [],
                collect($data)
                    ->only(['phone', 'address', 'trainer_level'])
                    ->all()
            );
        });

        return back()->with(
            'success',
            'Data trainer berhasil diperbarui.'
        );
    }

    public function destroy(User $trainer)
    {
        abort_unless($trainer->isTrainer(), 404);

        if ($trainer->rentals()->exists()) {
            return back()->withErrors([
                'trainer' => 'Trainer memiliki riwayat rental dan tidak dapat dihapus.'
            ]);
        }

        $trainer->delete();

        return back()->with(
            'success',
            'Trainer berhasil dihapus.'
        );
    }
}
