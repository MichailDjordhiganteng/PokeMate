<?php

namespace Database\Seeders;

use App\Models\Pokemon;
use App\Models\Type;
use Illuminate\Database\Seeder;

class PokemonSeeder extends Seeder
{
    public function run(): void
    {
        $pokemons = [
            [
                'pokemon_code' => 'PKM001',
                'pokemon_name' => 'Pikachu',
                'pokemon_level' => 25,
                'description' => 'Pokemon Electric yang lincah dan bersahabat.',
                'rental_price' => 75000,
                'types' => ['Electric'],
            ],
            [
                'pokemon_code' => 'PKM002',
                'pokemon_name' => 'Bulbasaur',
                'pokemon_level' => 18,
                'description' => 'Pokemon Grass dan Poison yang cocok untuk petualangan alam.',
                'rental_price' => 60000,
                'types' => ['Grass', 'Poison'],
            ],
            [
                'pokemon_code' => 'PKM003',
                'pokemon_name' => 'Charmander',
                'pokemon_level' => 20,
                'description' => 'Pokemon Fire dengan semangat petualangan tinggi.',
                'rental_price' => 70000,
                'types' => ['Fire'],
            ],
            [
                'pokemon_code' => 'PKM004',
                'pokemon_name' => 'Squirtle',
                'pokemon_level' => 19,
                'description' => 'Pokemon Water yang tangguh dan ceria.',
                'rental_price' => 65000,
                'types' => ['Water'],
            ],
            [
                'pokemon_code' => 'PKM005',
                'pokemon_name' => 'Rowlet',
                'pokemon_level' => 17,
                'description' => 'Pokemon Grass dan Flying yang cocok menjelajahi hutan.',
                'rental_price' => 55000,
                'types' => ['Grass', 'Flying'],
            ],
            [
                'pokemon_code' => 'PKM006',
                'pokemon_name' => 'Gardevoir',
                'pokemon_level' => 35,
                'description' => 'Pokemon Psychic dan Fairy yang setia melindungi Trainer.',
                'rental_price' => 110000,
                'types' => ['Psychic', 'Fairy'],
            ],
            [
                'pokemon_code' => 'PKM007',
                'pokemon_name' => 'Lucario',
                'pokemon_level' => 32,
                'description' => 'Pokemon Fighting dan Steel yang mampu merasakan aura.',
                'rental_price' => 100000,
                'types' => ['Fighting', 'Steel'],
            ],
            [
                'pokemon_code' => 'PKM008',
                'pokemon_name' => 'Gengar',
                'pokemon_level' => 30,
                'description' => 'Pokemon Ghost dan Poison yang usil dan misterius.',
                'rental_price' => 95000,
                'types' => ['Ghost', 'Poison'],
            ],
        ];

        foreach ($pokemons as $data) {
            $typeNames = $data['types'];
            unset($data['types']);

            $pokemon = Pokemon::updateOrCreate(
                ['pokemon_code' => $data['pokemon_code']],
                array_merge($data, [
                    'status' => 'available',
                ])
            );

            $typeIds = Type::whereIn('type', $typeNames)
                ->pluck('id')
                ->toArray();

            $pokemon->types()->sync($typeIds);
        }
    }
}
