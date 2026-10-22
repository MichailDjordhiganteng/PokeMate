<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalDetail extends Model
{
    protected $table = 'rental_details';

    protected $fillable = [
        'rental_id',
        'pokemon_id',
        'rental_price',
    ];

    protected function casts(): array
    {
        return [
            'rental_price' => 'decimal:2',
        ];
    }

    // Detail ini milik satu transaksi rental
    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }

    // Detail ini mencatat satu unit Pokémon
    public function pokemon(): BelongsTo
    {
        return $this->belongsTo(Pokemon::class);
    }
}
