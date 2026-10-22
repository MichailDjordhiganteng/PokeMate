<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Admin untuk testing
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'full_name' => 'Admin PokePartner',
                'password' => 'password',
                'role' => 'admin',
            ]
        );

        // Akun Trainer untuk testing
        $trainer = User::updateOrCreate(
            ['email' => 'trainer@gmail.com'],
            [
                'full_name' => 'Ash Ketchum',
                'password' => 'password',
                'role' => 'trainer',
            ]
        );

        // Membuat profil Trainer
        $trainer->trainerProfile()->updateOrCreate(
            [],
            [
                'phone' => '081234567890',
                'address' => 'Pallet Town',
                'trainer_level' => 1,
                'avatar' => null,
            ]
        );
    }
}
