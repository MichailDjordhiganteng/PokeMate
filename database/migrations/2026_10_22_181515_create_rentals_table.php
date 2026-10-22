<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('trainer_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->date('start_date');
            $table->date('due_date');

            $table->enum('rental_status', [
                'pending',
                'active',
                'return_requested',
                'completed',
                'late',
                'cancelled'
            ])->default('pending');

            $table->decimal('total_price', 12, 2)->default(0);
            $table->decimal('late_fee', 12, 2)->default(0);

            $table->timestamp('return_requested_at')->nullable();
            $table->timestamp('returned_at')->nullable();

            $table->foreignId('processed_by_admin_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};