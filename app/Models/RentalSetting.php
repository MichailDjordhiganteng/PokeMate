<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalSetting extends Model
{
    protected $fillable = [
        'max_active_pokemon',
    ];

    protected function casts(): array
    {
        return [
            'max_active_pokemon' => 'integer',
        ];
    }
}
