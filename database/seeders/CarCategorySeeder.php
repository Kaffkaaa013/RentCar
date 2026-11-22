<?php

namespace Database\Seeders;

use App\Models\CarCategory;
use Illuminate\Database\Seeder;

class CarCategorySeeder extends Seeder
{
    /**
     * Seed car categories.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'MPV', 'description' => 'Mobil keluarga'],
            ['name' => 'SUV', 'description' => 'Mobil sport utility'],
            ['name' => 'City Car', 'description' => 'Mobil kecil untuk perkotaan'],
            ['name' => 'Hatchback', 'description' => 'Mobil kompak'],
        ];

        foreach ($categories as $cat) {
            CarCategory::updateOrCreate(
                ['name' => $cat['name']],
                $cat
            );
        }
    }
}
