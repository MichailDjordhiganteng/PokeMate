<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Normal',
            'Fire',
            'Water',
            'Electric',
            'Grass',
            'Ice',
            'Fighting',
            'Poison',
            'Ground',
            'Flying',
            'Psychic',
            'Bug',
            'Rock',
            'Ghost',
            'Dragon',
            'Dark',
            'Steel',
            'Fairy',
        ];

        foreach ($types as $type) {
            Type::firstOrCreate(
                ['type' => $type],
                ['description' => 'Pokemon dengan tipe ' . $type]
            );
        }
    }
}
