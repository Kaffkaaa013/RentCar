<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('car_id')->constrained('cars')->restrictOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days');
            $table->decimal('total_price', 12, 2);
            $table->enum('booking_status', [
                'pending',
                'confirmed',
                'ongoing',
                'completed',
                'cancelled',
            ])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
