<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\CarImage;
use Illuminate\Database\Seeder;

class CarImageSeeder extends Seeder
{
    /**
     * Seed car_images table (galeri foto mobil).
     */
    public function run(): void
    {
        $cars = Car::all();

        foreach ($cars as $car) {
            // Foto Utama Mobil
            CarImage::updateOrCreate(
                [
                    'car_id' => $car->id,
                    'image_path' => $car->image,
                ]
            );

            // Foto Tambahan / Interior dummy
            CarImage::updateOrCreate(
                [
                    'car_id' => $car->id,
                    'image_path' => 'cars/' . strtolower($car->name) . '_interior.jpg',
                ]
            );
        }
    }
}
