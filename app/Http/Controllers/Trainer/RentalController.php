<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Pokemon;
use App\Models\Rental;
use App\Services\RentalService;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    public function index(Request $request)
    {
        $history = $request->routeIs('trainer.rentals.history');
        $request->validate(['search' => ['nullable', 'string', 'max:100']]);
        $rentals = $request->user()->rentals()->with('rentalDetails.pokemon.types')
            ->when($request->filled('search'), fn ($q) => $q->whereHas('rentalDetails.pokemon', fn ($q) => $q->where('pokemon_name', 'like', '%'.$request->search.'%')))
            ->when($history, fn ($q) => $q->whereIn('rental_status', ['completed', 'cancelled']),
                fn ($q) => $q->whereIn('rental_status', RentalService::OPEN_STATUSES))
            ->latest()->paginate(10);

        return view('trainer.rentals.index', [
            'rentals' => $rentals, 'history' => $history,
            'slots' => app(RentalService::class)->usedSlots($request->user()),
            'allCount' => $request->user()->rentals()->count(),
            'completedCount' => $request->user()->rentals()->where('rental_status', 'completed')->count(),
            'totalFees' => $request->user()->rentals()->sum('late_fee'),
            'nearestDue' => $request->user()->rentals()->whereIn('rental_status', RentalService::OPEN_STATUSES)->min('due_date'),
        ]);
    }

    public function create(Request $request, Pokemon $pokemon, RentalService $service)
    {
        $request->validate([
            'duration' => ['nullable', 'integer', 'between:1,5'],
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'destination' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        return view('trainer.rentals.create', [
            'pokemon' => $pokemon->load('types'), 'slots' => $service->usedSlots($request->user()),
        ]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'pokemon_id' => ['required', 'integer', 'exists:pokemons,id'],
            'start_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'duration' => ['required', 'integer', 'between:1,5'],
            'destination' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'agreement' => ['accepted'],
        ], [
            'pokemon_id.required' => 'Pilih Pokémon yang ingin disewa.',
            'pokemon_id.exists' => 'Pokémon yang dipilih tidak ditemukan.',
            'start_date.required' => 'Pilih tanggal mulai petualangan.',
            'start_date.date_format' => 'Tanggal mulai petualangan tidak valid.',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'duration.required' => 'Pilih durasi sewa antara 1 sampai 5 hari.',
            'duration.integer' => 'Durasi sewa harus berupa jumlah hari yang valid.',
            'duration.between' => 'Durasi sewa harus antara 1 sampai 5 hari.',
            'destination.required' => 'Isi tujuan dan medan petualangan, misalnya Hutan Viridian.',
            'destination.max' => 'Tujuan dan medan petualangan maksimal 255 karakter.',
            'notes.max' => 'Catatan khusus maksimal 2.000 karakter.',
            'agreement.accepted' => 'Centang persetujuan aturan sewa sebelum melanjutkan.',
        ]);
    }

    public function confirm(Request $request, RentalService $service)
    {
        $data = $this->validated($request);
        $pokemon = Pokemon::with('types')->findOrFail($data['pokemon_id']);

        return view('trainer.rentals.confirm', compact('data', 'pokemon'));
    }

    public function store(Request $request, RentalService $service)
    {
        $rental = $service->create($request->user(), $this->validated($request));

        return redirect()->route('trainer.rentals.show', $rental)->with('success', 'Rental berhasil! Partner siap menemani petualanganmu.');
    }

    public function show(Request $request, Rental $rental)
    {
        abort_unless($rental->trainer_id === $request->user()->id, 403);

        return view('trainer.rentals.show', ['rental' => $rental->load('rentalDetails.pokemon.types', 'processedByAdmin')]);
    }

    public function returnForm(Request $request, Rental $rental)
    {
        abort_unless($rental->trainer_id === $request->user()->id, 403);

        return view('trainer.rentals.return', ['rental' => $rental->load('rentalDetails.pokemon.types')]);
    }

    public function requestReturn(Request $request, Rental $rental, RentalService $service)
    {
        abort_unless($rental->trainer_id === $request->user()->id, 403);
        $request->validate(['agreement' => ['accepted']]);
        $service->requestReturn($rental);

        return redirect()->route('trainer.rentals.show', $rental)->with('success', 'Pengembalian diajukan. Silakan serahkan partner kepada admin PokéCenter.');
    }
}
