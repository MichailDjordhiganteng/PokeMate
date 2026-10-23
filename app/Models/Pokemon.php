<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pokemon extends Model
{
    protected $table = 'pokemons';

    protected $fillable = [
        'pokemon_code',
        'pokemon_name',
        'pokemon_level',
        'description',
        'rental_price',
        'status',
        'pokemon_image',
    ];

    protected function casts(): array
    {
        return [
            'pokemon_level' => 'integer',
            'rental_price' => 'decimal:2',
        ];
    }

    public function types(): BelongsToMany
    {
        return $this->belongsToMany(
            Type::class,
            'pokemon_types',
            'pokemon_id',
            'type_id'
        );
    }

    public function rentalDetails(): HasMany
    {
        return $this->hasMany(RentalDetail::class);
    }

    public function imageUrl(): ?string
    {
        if ($this->pokemon_image) {
            return asset('storage/'.$this->pokemon_image);
        }

        $number = [
            'pikachu' => 25, 'bulbasaur' => 1, 'charmander' => 4, 'squirtle' => 7,
            'rowlet' => 722, 'gardevoir' => 282, 'lucario' => 448, 'gengar' => 94,
        ][strtolower($this->pokemon_name)] ?? null;

        return $number ? 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/'.$number.'.png' : null;
    }
}
