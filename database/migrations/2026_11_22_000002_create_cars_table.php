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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('car_categories')->restrictOnDelete();
            $table->string('name', 100);
            $table->string('brand', 100);
            $table->string('plate_number', 20)->unique();
            $table->year('year');
            $table->enum('transmission', ['manual', 'automatic']);
            $table->integer('seats');
            $table->decimal('price_per_day', 12, 2);
            $table->string('image', 255)->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['available', 'unavailable', 'maintenance'])->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
