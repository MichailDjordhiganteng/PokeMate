<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pokemon;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PokemonController extends Controller
{
    // READ - Menampilkan daftar Pokémon
    public function index(Request $request)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'type_id' => ['nullable', 'integer', 'exists:types,id'],
            'status' => ['nullable', Rule::in(['available', 'on_adventure', 'resting', 'unavailable'])],
            'sort' => ['nullable', Rule::in(['code', 'level', 'price_low', 'price_high'])],
        ]);

        $query = Pokemon::with('types');

        // Pencarian berdasarkan nama atau kode
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('pokemon_name', 'like', "%{$search}%")
                    ->orWhere('pokemon_code', 'like', "%{$search}%");
            });
        }

        $query->when($request->filled('type_id'), fn ($q) => $q->whereHas('types', fn ($q) => $q->where('types.id', $request->type_id)))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status));
        $sort = $request->input('sort', 'code');
        $pokemons = $query
            ->orderBy(match ($sort) {
                'level' => 'pokemon_level', 'price_low', 'price_high' => 'rental_price', default => 'pokemon_code'
            }, in_array($sort, ['level', 'price_high']) ? 'desc' : 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.pokemons.index', [
            'pokemons' => $pokemons, 'types' => Type::orderBy('type')->get(),
            'inventory' => Pokemon::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }

    // CREATE - Menampilkan formulir tambah Pokémon
    public function create()
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $types = Type::orderBy('type')->get();

        return view('admin.pokemons.create', compact('types'));
    }

    // STORE - Menyimpan Pokémon baru ke database
    public function store(Request $request)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $validated = $request->validate([
            'pokemon_code' => [
                'required',
                'string',
                'max:30',
                'unique:pokemons,pokemon_code',
            ],
            'pokemon_name' => [
                'required',
                'string',
                'max:100',
            ],
            'pokemon_level' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],
            'description' => [
                'required',
                'string',
            ],
            'rental_price' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999.99',
            ],
            'status' => [
                'required',
                Rule::in([
                    'available',
                    'resting',
                    'unavailable',
                ]),
            ],
            'type_ids' => [
                'required',
                'array',
                'min:1',
            ],
            'type_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:types,id',
            ],
            'pokemon_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        $imagePath = null;

        if ($request->hasFile('pokemon_image')) {
            $imagePath = $request->file('pokemon_image')
                ->store('pokemons', 'public');
        }

        try {
            DB::transaction(function () use ($validated, $imagePath) {
                $pokemon = Pokemon::create([
                    'pokemon_code' => $validated['pokemon_code'],
                    'pokemon_name' => $validated['pokemon_name'],
                    'pokemon_level' => $validated['pokemon_level'],
                    'description' => $validated['description'],
                    'rental_price' => $validated['rental_price'],
                    'status' => $validated['status'],
                    'pokemon_image' => $imagePath,
                ]);

                // Simpan hubungan Pokémon dengan tipe
                $pokemon->types()->sync($validated['type_ids']);
            });
        } catch (\Throwable $e) {
            // Hapus gambar jika penyimpanan database gagal
            if ($imagePath !== null) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $e;
        }

        return redirect()
            ->route('admin.pokemons.index')
            ->with('success', 'Pokémon berhasil ditambahkan!');
    }

    // EDIT - Menampilkan formulir edit Pokémon
    public function edit(Pokemon $pokemon)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        // Ambil tipe yang sudah dimiliki Pokémon
        $pokemon->load('types');

        // Ambil seluruh pilihan tipe
        $types = Type::orderBy('type')->get();

        return view(
            'admin.pokemons.edit',
            compact('pokemon', 'types')
        );
    }

    // UPDATE - Menyimpan perubahan data Pokémon
    public function update(Request $request, Pokemon $pokemon)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        // Cegah perubahan Pokémon yang sedang disewa
        if ($pokemon->status === 'on_adventure') {
            return back()->withErrors([
                'status' => 'Pokémon sedang disewa dan tidak dapat diedit.',
            ]);
        }

        $validated = $request->validate([
            'pokemon_code' => [
                'required',
                'string',
                'max:30',
                Rule::unique('pokemons', 'pokemon_code')
                    ->ignore($pokemon->id),
            ],
            'pokemon_name' => [
                'required',
                'string',
                'max:100',
            ],
            'pokemon_level' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],
            'description' => [
                'required',
                'string',
            ],
            'rental_price' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999.99',
            ],
            'status' => [
                'required',
                Rule::in([
                    'available',
                    'resting',
                    'unavailable',
                ]),
            ],
            'type_ids' => [
                'required',
                'array',
                'min:1',
            ],
            'type_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:types,id',
            ],
            'pokemon_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        $oldImage = $pokemon->pokemon_image;
        $newImage = null;

        // Upload gambar baru jika dipilih
        if ($request->hasFile('pokemon_image')) {
            $newImage = $request->file('pokemon_image')
                ->store('pokemons', 'public');
        }

        try {
            DB::transaction(function () use (
                $pokemon,
                $validated,
                $newImage
            ) {
                $pokemon = Pokemon::whereKey($pokemon->id)->lockForUpdate()->firstOrFail();
                if ($pokemon->status === 'on_adventure') {
                    throw ValidationException::withMessages(['status' => 'Pokémon sedang disewa dan tidak dapat diedit.']);
                }

                $data = [
                    'pokemon_code' => $validated['pokemon_code'],
                    'pokemon_name' => $validated['pokemon_name'],
                    'pokemon_level' => $validated['pokemon_level'],
                    'description' => $validated['description'],
                    'rental_price' => $validated['rental_price'],
                    'status' => $validated['status'],
                ];

                if ($newImage !== null) {
                    $data['pokemon_image'] = $newImage;
                }

                // Update informasi Pokémon
                $pokemon->update($data);

                // Update hubungan tipe Pokémon
                $pokemon->types()->sync($validated['type_ids']);
            });
        } catch (\Throwable $e) {
            // Hapus gambar baru jika update gagal
            if ($newImage !== null) {
                Storage::disk('public')->delete($newImage);
            }

            throw $e;
        }

        // Hapus gambar lama jika diganti
        if ($newImage !== null && $oldImage !== null) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()
            ->route('admin.pokemons.index')
            ->with('success', 'Data Pokémon berhasil diperbarui!');
    }

    // DELETE - Menghapus Pokémon
    public function destroy(Pokemon $pokemon)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        // Pokémon yang sedang disewa tidak boleh dihapus
        if ($pokemon->status === 'on_adventure') {
            return redirect()
                ->route('admin.pokemons.index')
                ->with(
                    'error',
                    'Pokémon sedang disewa dan tidak dapat dihapus.'
                );
        }

        // Pokémon yang punya riwayat rental tidak boleh dihapus
        if ($pokemon->rentalDetails()->exists()) {
            return redirect()
                ->route('admin.pokemons.index')
                ->with(
                    'error',
                    'Pokémon tidak dapat dihapus karena sudah memiliki riwayat penyewaan.'
                );
        }

        // Simpan path gambar sebelum data dihapus
        $imagePath = $pokemon->pokemon_image;

        DB::transaction(function () use ($pokemon) {
            $pokemon = Pokemon::whereKey($pokemon->id)->lockForUpdate()->firstOrFail();
            if ($pokemon->status === 'on_adventure' || $pokemon->rentalDetails()->exists()) {
                throw ValidationException::withMessages(['pokemon' => 'Pokémon sedang disewa atau memiliki riwayat rental.']);
            }

            // Hapus hubungan Pokémon dengan tipe
            $pokemon->types()->detach();

            // Hapus Pokémon dari database
            $pokemon->delete();
        });

        // Hapus gambar setelah database berhasil diperbarui
        if ($imagePath !== null) {
            Storage::disk('public')->delete($imagePath);
        }

        return redirect()
            ->route('admin.pokemons.index')
            ->with('success', 'Pokémon berhasil dihapus!');
    }
}
