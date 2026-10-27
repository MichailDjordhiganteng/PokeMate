<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RentalSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RentalSettingController extends Controller
{
    // Halaman pengaturan digabung ke Kelola Trainer.
    public function index()
    {
        return redirect()->route('admin.trainers.index');
    }

    // Admin mengubah batas maksimal rental seluruh Trainer.
    public function update(Request $request)
    {
        $data = $request->validate([
            'max_active_pokemon' => [
                'required',
                'integer',
                'min:1',
                'max:20',
            ],
        ], [
            'max_active_pokemon.required' =>
                'Batas rental wajib diisi.',
            'max_active_pokemon.integer' =>
                'Batas rental harus berupa angka bulat.',
            'max_active_pokemon.min' =>
                'Batas rental minimal 1 Pokémon.',
            'max_active_pokemon.max' =>
                'Batas rental maksimal 20 Pokémon.',
        ]);

        DB::transaction(function () use ($data) {

            // Mengunci pengaturan selama pembaruan kuota.
            $setting = RentalSetting::whereKey(1)
                ->lockForUpdate()
                ->firstOrFail();

            $setting->update([
                'max_active_pokemon' => $data['max_active_pokemon'],
            ]);
        });

        return redirect()
            ->route('admin.trainers.index')
            ->with(
                'success',
                'Batas maksimal rental Pokémon berhasil diperbarui.'
            );
    }
}
