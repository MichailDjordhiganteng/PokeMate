<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('max_active_pokemon')->default(2);
            $table->timestamps();
        });

        // Satu baris pengaturan global untuk seluruh Trainer.
        DB::table('rental_settings')->insert([
            'id' => 1,
            'max_active_pokemon' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_settings');
    }
};
