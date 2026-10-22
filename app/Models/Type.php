<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Type extends Model
{
    protected $table = 'types';

    protected $fillable = [
        'type',
        'description',
    ];

    public function pokemons(): BelongsToMany
    {
        return $this->belongsToMany(
            Pokemon::class,
            'pokemon_types',
            'type_id',
            'pokemon_id'
        );
    }
}
