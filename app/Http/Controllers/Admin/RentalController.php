<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Services\RentalService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RentalController extends Controller
{
    private function query(Request $request)
    {
        $request->validate([
            'status' => ['nullable', Rule::in(['pending', 'active', 'return_requested', 'completed', 'late', 'cancelled'])],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', ...($request->filled('from') ? ['after_or_equal:from'] : [])],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        return Rental::with('trainer', 'rentalDetails.pokemon')
            ->when($request->filled('status'), function ($q) use ($request) {
                if ($request->status === 'late') {
                    $q->where(fn ($q) => $q->where('rental_status', 'late')->orWhere(fn ($q) => $q
                        ->where('rental_status', 'active')->whereDate('due_date', '<', today())));
                } else {
                    $q->where('rental_status', $request->status);
                }
            })
            ->when($request->filled('from'), fn ($q) => $q->whereDate('start_date', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('start_date', '<=', $request->input('to')))
            ->when($request->filled('search'), fn ($q) => $q->whereHas('trainer', fn ($q) => $q
                ->where('full_name', 'like', '%'.$request->search.'%')));
    }

    public function index(Request $request)
    {
        $query = $this->query($request);
        $returns = $request->routeIs('admin.returns.index');
        if ($returns) {
            $query->where('rental_status', 'return_requested');
        }

        return view('admin.rentals', [
            'rentals' => $query->latest()->paginate(15)->withQueryString(), 'returns' => $returns,
            'activeCount' => Rental::whereIn('rental_status', ['active', 'late'])->count(),
            'dueTodayCount' => Rental::whereIn('rental_status', RentalService::OPEN_STATUSES)->whereDate('due_date', today())->count(),
            'lateCount' => Rental::whereIn('rental_status', ['active', 'late'])->whereDate('due_date', '<', today())->count(),
            'waitingCount' => Rental::where('rental_status', 'return_requested')->count(),
            'completedToday' => Rental::whereDate('returned_at', today())->count(),
        ]);
    }

    public function show(Rental $rental)
    {
        return view('rentals.show', [
            'rental' => $rental->load('trainer.trainerProfile', 'rentalDetails.pokemon.types', 'processedByAdmin'),
            'adminView' => true,
        ]);
    }

    public function complete(Request $request, Rental $rental, RentalService $service)
    {
        $data = $request->validate(['condition' => ['required', Rule::in(['available', 'resting', 'unavailable'])]]);
        $service->complete($rental, $request->user(), $data['condition']);

        return back()->with('success', 'Pengembalian selesai. Status partner dan denda telah diperbarui.');
    }

    public function report(Request $request)
    {
        $query = $this->query($request);

        return view('admin.report', [
            'count' => (clone $query)->count(),
            'income' => (clone $query)->where('rental_status', 'completed')->sum('total_price'),
            'fees' => (clone $query)->where('rental_status', 'completed')->sum('late_fee'),
            'completedCount' => (clone $query)->where('rental_status', 'completed')->count(),
            'lateCount' => (clone $query)->where('late_fee', '>', 0)->count(),
            'rentals' => $query->latest()->paginate(20)->withQueryString(),
        ]);
    }

    public function export(Request $request)
    {
        $query = $this->query($request);

        return response()->streamDownload(function () use ($query) {
            $stream = fopen('php://output', 'w');
            fwrite($stream, "\xEF\xBB\xBF");
            fputcsv($stream, ['ID', 'Trainer', 'Pokémon', 'Mulai', 'Jatuh Tempo', 'Status', 'Biaya Sewa', 'Denda'], ',', '"', '');
            foreach ($query->orderBy('id')->lazy(200) as $rental) {
                $safe = fn ($value) => preg_match('/^[=+@\-\t\r\n]/u', (string) $value) ? "'".$value : $value;
                fputcsv($stream, array_map($safe, [
                    $rental->id, $rental->trainer->full_name,
                    $rental->rentalDetails->pluck('pokemon.pokemon_name')->join(', '),
                    $rental->start_date->toDateString(), $rental->due_date->toDateString(),
                    $rental->rental_status, $rental->total_price, $rental->late_fee,
                ]), ',', '"', '');
            }
            fclose($stream);
        }, 'laporan-rental-'.today()->toDateString().'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function printReport(Request $request)
    {
        $query = $this->query($request);

        return view('admin.report-print', [
            'count' => (clone $query)->count(),
            'income' => (clone $query)->where('rental_status', 'completed')->sum('total_price'),
            'fees' => (clone $query)->where('rental_status', 'completed')->sum('late_fee'),
            'rentals' => $query->orderByDesc('id')->lazy(200),
        ]);
    }
}
