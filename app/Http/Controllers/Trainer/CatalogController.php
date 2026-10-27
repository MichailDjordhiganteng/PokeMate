<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Pokemon;
use App\Models\RentalSetting;
use App\Models\Type;
use App\Services\RentalService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CatalogController extends Controller
{
    public const TERRAINS = [
        'forest' => [
            'label' => 'Hutan Viridian',
            'types' => ['Grass', 'Bug', 'Flying']
        ],
        'water' => [
            'label' => 'Sungai Cerulean',
            'types' => ['Water', 'Electric']
        ],
        'mountain' => [
            'label' => 'Gunung Moon',
            'types' => ['Rock', 'Ground', 'Fighting']
        ],
        'cave' => [
            'label' => 'Gua Cerulean',
            'types' => ['Rock', 'Ground', 'Psychic']
        ],
        'night' => [
            'label' => 'Malam di Lavender',
            'types' => ['Dark', 'Ghost']
        ],
        'city' => [
            'label' => 'Taman Kota Saffron',
            'types' => ['Normal', 'Psychic', 'Fairy']
        ],
    ];

    // Mengambil batas maksimal rental dari pengaturan Admin.
    private function maxActivePokemon(): int
    {
        return RentalSetting::findOrFail(1)->max_active_pokemon;
    }

    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'type_id' => ['nullable', 'integer', 'exists:types,id'],
            'status' => [
                'nullable',
                Rule::in([
                    'available',
                    'on_adventure',
                    'resting',
                    'unavailable'
                ])
            ],
            'sort' => [
                'nullable',
                Rule::in([
                    'code',
                    'price_low',
                    'price_high',
                    'level'
                ])
            ],
        ]);

        $pokemons = Pokemon::with('types')
            ->when(
                $request->filled('search'),
                fn ($q) => $q->where(
                    fn ($q) => $q
                        ->where(
                            'pokemon_name',
                            'like',
                            '%'.$request->search.'%'
                        )
                        ->orWhere(
                            'pokemon_code',
                            'like',
                            '%'.$request->search.'%'
                        )
                )
            )
            ->when(
                $request->filled('type_id'),
                fn ($q) => $q->whereHas(
                    'types',
                    fn ($q) => $q->where(
                        'types.id',
                        $request->type_id
                    )
                )
            )
            ->when(
                $request->filled('status'),
                fn ($q) => $q->where(
                    'status',
                    $request->status
                )
            )
            ->orderBy(
                match ($request->input('sort')) {
                    'price_low', 'price_high' => 'rental_price',
                    'level' => 'pokemon_level',
                    default => 'pokemon_code'
                },
                in_array(
                    $request->input('sort'),
                    ['price_high', 'level']
                ) ? 'desc' : 'asc'
            )
            ->paginate(8)
            ->withQueryString();

        return view('trainer.catalog', [
            'pokemons' => $pokemons,
            'types' => Type::orderBy('type')->get(),
            'slots' => app(RentalService::class)
                ->usedSlots($request->user()),
            'maxActivePokemon' => $this->maxActivePokemon(),
        ]);
    }

    public function show(Pokemon $pokemon)
    {
        return view('trainer.pokemon', [
            'pokemon' => $pokemon->load('types'),
            'slots' => app(RentalService::class)
                ->usedSlots(auth()->user()),
            'maxActivePokemon' => $this->maxActivePokemon(),
        ]);
    }

    public function match(Request $request)
    {
        $data = $request->validate([
            'terrain' => [
                'nullable',
                Rule::in(array_keys(self::TERRAINS))
            ],
            'budget' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999999'
            ],
        ]);

        $pokemons = Pokemon::with('types')
            ->where('status', 'available')
            ->when(
                ! empty($data['terrain']),
                fn ($q) => $q->whereHas(
                    'types',
                    fn ($q) => $q->whereIn(
                        'type',
                        self::TERRAINS[$data['terrain']]['types']
                    )
                )
            )
            ->when(
                isset($data['budget']),
                fn ($q) => $q->where(
                    'rental_price',
                    '<=',
                    $data['budget']
                )
            )
            ->orderBy('rental_price')
            ->paginate(9)
            ->withQueryString();

        return view('trainer.match', [
            'pokemons' => $pokemons,
            'terrains' => self::TERRAINS,
            'slots' => app(RentalService::class)
                ->usedSlots($request->user()),
            'maxActivePokemon' => $this->maxActivePokemon(),
        ]);
    }
}
