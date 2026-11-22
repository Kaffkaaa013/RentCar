<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database across all 8 tables.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,        // 1. users (admin & customers)
            CarCategorySeeder::class, // 2. car_categories (MPV, SUV, City Car, Hatchback)
            CarSeeder::class,         // 3. cars (8 unit mobil lengkap)
            CarImageSeeder::class,    // 4. car_images (galeri foto mobil)
            BookingSeeder::class,     // 5. bookings (pending, confirmed, ongoing, completed, cancelled)
            PaymentSeeder::class,     // 6. payments (pending, verified, rejected)
            ReturnSeeder::class,      // 7. returns (completed, pending, denda telat)
            ReviewSeeder::class,      // 8. reviews (ulasan & rating dari customer)
        ]);
    }
}
