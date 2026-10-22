
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pokemons', function (Blueprint $table) {
            $table->id();

            $table->string('pokemon_code', 30)->unique();
            $table->string('pokemon_name', 100);
            $table->integer('pokemon_level')->default(1);
            $table->text('description');
            $table->decimal('rental_price', 12, 2);

            $table->enum('status', [
                'available',
                'on_adventure',
                'resting',
                'unavailable'
            ])->default('available');

            $table->string('pokemon_image')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pokemons');
    }
};
