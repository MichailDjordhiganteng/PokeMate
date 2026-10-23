<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pokemon;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TypeController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['search' => ['nullable', 'string', 'max:100']]);

        return view('admin.types', [
            'types' => Type::withCount('pokemons')->when($request->filled('search'), fn ($q) => $q->where('type', 'like', '%'.$request->search.'%'))->orderBy('type')->paginate(10)->withQueryString(),
            'totalTypes' => Type::count(),
            'linkedPokemon' => Pokemon::has('types')->count(),
            'topType' => Type::withCount('pokemons')->orderByDesc('pokemons_count')->first(),
        ]);
    }

    public function store(Request $request)
    {
        Type::create($this->validated($request));

        return back()->with('success', 'Tipe berhasil ditambahkan.');
    }

    public function update(Request $request, Type $type)
    {
        $type->update($this->validated($request, $type));

        return back()->with('success', 'Tipe berhasil diperbarui.');
    }

    private function validated(Request $request, ?Type $type = null): array
    {
        return $request->validate([
            'type' => ['required', 'string', 'max:50', Rule::unique('types', 'type')->ignore($type?->id)],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    public function destroy(Type $type)
    {
        if ($type->pokemons()->exists()) {
            return back()->withErrors(['type' => 'Tipe masih digunakan Pokémon dan tidak dapat dihapus.']);
        }
        $type->delete();

        return back()->with('success', 'Tipe berhasil dihapus.');
    }
}
