<?php

namespace App\Services;

use App\Models\Pokemon;
use App\Models\Rental;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RentalService
{
    public const OPEN_STATUSES = ['pending', 'active', 'late', 'return_requested'];

    public function usedSlots(User $trainer): int
    {
        return $trainer->rentals()->whereIn('rental_status', self::OPEN_STATUSES)
            ->withCount('rentalDetails')->get()->sum('rental_details_count');
    }

    public function create(User $trainer, array $data): Rental
    {
        return DB::transaction(function () use ($trainer, $data) {
            // Serialize submissions for this trainer as well as for the selected partner.
            $trainer = User::whereKey($trainer->id)->lockForUpdate()->firstOrFail();
            $pokemon = Pokemon::whereKey($data['pokemon_id'])->lockForUpdate()->firstOrFail();
            if ($pokemon->status !== 'available') {
                throw ValidationException::withMessages(['pokemon_id' => 'Partner ini sudah tidak tersedia. Pilih Pokémon lain.']);
            }
            if ($this->usedSlots($trainer) >= 2) {
                throw ValidationException::withMessages(['pokemon_id' => 'Maksimal 2 Pokémon dalam rental yang belum selesai.']);
            }
            $rental = $trainer->rentals()->create([
                'start_date' => $data['start_date'],
                'due_date' => Carbon::parse($data['start_date'])->addDays((int) $data['duration']),
                'rental_status' => 'active',
                'total_price' => $pokemon->rental_price * $data['duration'],
                'destination' => $data['destination'],
                'notes' => $data['notes'] ?? null,
            ]);
            $rental->rentalDetails()->create([
                'pokemon_id' => $pokemon->id,
                'rental_price' => $pokemon->rental_price,
            ]);
            $pokemon->update(['status' => 'on_adventure']);

            return $rental;
        });
    }

    public function requestReturn(Rental $rental): void
    {
        DB::transaction(function () use ($rental) {
            $rental = Rental::whereKey($rental->id)->lockForUpdate()->firstOrFail();
            if (! in_array($rental->rental_status, ['active', 'late'], true)) {
                throw ValidationException::withMessages(['rental' => 'Rental ini tidak dapat diajukan kembali.']);
            }
            if ($rental->start_date->isFuture()) {
                throw ValidationException::withMessages(['rental' => 'Petualangan belum dimulai.']);
            }
            $rental->update(['rental_status' => 'return_requested', 'return_requested_at' => now()]);
        });
    }

    public function complete(Rental $rental, User $admin, string $condition): void
    {
        DB::transaction(function () use ($rental, $admin, $condition) {
            $rental = Rental::whereKey($rental->id)->lockForUpdate()->firstOrFail();
            if ($rental->rental_status !== 'return_requested') {
                throw ValidationException::withMessages(['rental' => 'Hanya pengajuan pengembalian yang dapat diproses.']);
            }
            // Late fee stops at the trainer's request, so admin processing time is not charged.
            $days = max(0, (int) $rental->due_date->diffInDays($rental->return_requested_at->copy()->startOfDay(), false));
            $rental->update([
                'rental_status' => 'completed', 'returned_at' => now(),
                'processed_by_admin_id' => $admin->id, 'late_fee' => $days * 100000,
            ]);
            $ids = $rental->rentalDetails()->pluck('pokemon_id');
            Pokemon::whereIn('id', $ids)->lockForUpdate()->get()->each->update(['status' => $condition]);
        });
    }
}
