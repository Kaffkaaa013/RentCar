<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\CarCategory;
use App\Models\CarImage;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    /**
     * Seed cars and car_images table.
     */
    public function run(): void
    {
        $mpv = CarCategory::where('name', 'MPV')->first();
        $suv = CarCategory::where('name', 'SUV')->first();
        $cityCar = CarCategory::where('name', 'City Car')->first();
        $hatchback = CarCategory::where('name', 'Hatchback')->first();

        $cars = [
            [
                'category_id' => $mpv->id,
                'name' => 'Avanza',
                'brand' => 'Toyota',
                'plate_number' => 'M 1001 AA',
                'year' => 2023,
                'transmission' => 'manual',
                'seats' => 7,
                'price_per_day' => 350000.00,
                'image' => 'cars/avanza.jpg',
                'description' => 'Toyota Avanza untuk perjalanan keluarga',
                'status' => 'available',
            ],
            [
                'category_id' => $mpv->id,
                'name' => 'Xpander',
                'brand' => 'Mitsubishi',
                'plate_number' => 'M 1002 AA',
                'year' => 2022,
                'transmission' => 'automatic',
                'seats' => 7,
                'price_per_day' => 400000.00,
                'image' => 'cars/xpander.jpg',
                'description' => 'Mitsubishi Xpander nyaman untuk perjalanan',
                'status' => 'available',
            ],
            [
                'category_id' => $cityCar->id,
                'name' => 'Brio',
                'brand' => 'Honda',
                'plate_number' => 'M 1003 AA',
                'year' => 2023,
                'transmission' => 'automatic',
                'seats' => 5,
                'price_per_day' => 250000.00,
                'image' => 'cars/brio.jpg',
                'description' => 'Honda Brio cocok untuk perjalanan perkotaan',
                'status' => 'available',
            ],
            [
                'category_id' => $suv->id,
                'name' => 'Fortuner',
                'brand' => 'Toyota',
                'plate_number' => 'M 1004 AA',
                'year' => 2022,
                'transmission' => 'automatic',
                'seats' => 7,
                'price_per_day' => 900000.00,
                'image' => 'cars/fortuner.jpg',
                'description' => 'Toyota Fortuner untuk perjalanan nyaman',
                'status' => 'available',
            ],
            [
                'category_id' => $mpv->id,
                'name' => 'Ertiga',
                'brand' => 'Suzuki',
                'plate_number' => 'M 1005 AA',
                'year' => 2021,
                'transmission' => 'manual',
                'seats' => 7,
                'price_per_day' => 300000.00,
                'image' => 'cars/ertiga.jpg',
                'description' => 'Suzuki Ertiga untuk perjalanan keluarga',
                'status' => 'available',
            ],
            [
                'category_id' => $hatchback->id,
                'name' => 'Jazz',
                'brand' => 'Honda',
                'plate_number' => 'M 1006 AA',
                'year' => 2019,
                'transmission' => 'automatic',
                'seats' => 5,
                'price_per_day' => 350000.00,
                'image' => 'cars/jazz.jpg',
                'description' => 'Honda Jazz dengan desain hatchback',
                'status' => 'available',
            ],
            [
                'category_id' => $suv->id,
                'name' => 'Rush',
                'brand' => 'Toyota',
                'plate_number' => 'M 1007 AA',
                'year' => 2021,
                'transmission' => 'manual',
                'seats' => 7,
                'price_per_day' => 400000.00,
                'image' => 'cars/rush.jpg',
                'description' => 'Toyota Rush untuk perjalanan bersama',
                'status' => 'available',
            ],
            [
                'category_id' => $cityCar->id,
                'name' => 'Agya',
                'brand' => 'Toyota',
                'plate_number' => 'M 1008 AA',
                'year' => 2022,
                'transmission' => 'manual',
                'seats' => 5,
                'price_per_day' => 200000.00,
                'image' => 'cars/agya.jpg',
                'description' => 'Toyota Agya hemat untuk penggunaan harian',
                'status' => 'available',
            ],
        ];

        foreach ($cars as $carData) {
            $car = Car::updateOrCreate(
                ['plate_number' => $carData['plate_number']],
                $carData
            );

            // Tambahkan data dummy ke car_images (galeri foto tambahan)
            CarImage::updateOrCreate(
                [
                    'car_id' => $car->id,
                    'image_path' => $carData['image'],
                ]
            );
        }
    }
}
