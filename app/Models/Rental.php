<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rental extends Model
{
    protected $table = 'rentals';

    protected $fillable = [
        'trainer_id',
        'start_date',
        'due_date',
        'rental_status',
        'total_price',
        'late_fee',
        'return_requested_at',
        'returned_at',
        'processed_by_admin_id',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'due_date' => 'date',
            'total_price' => 'decimal:2',
            'late_fee' => 'decimal:2',
            'return_requested_at' => 'datetime',
            'returned_at' => 'datetime',
        ];
    }

    // Trainer yang melakukan penyewaan
    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    // Admin yang memproses pengembalian
    public function processedByAdmin(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'processed_by_admin_id'
        );
    }

    // Satu transaksi berisi beberapa Pokémon
    public function rentalDetails(): HasMany
    {
        return $this->hasMany(RentalDetail::class);
    }
}
