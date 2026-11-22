<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Car;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Seed bookings table with realistic multi-status rental bookings.
     */
    public function run(): void
    {
        $kafka = User::where('email', 'kafka@gmail.com')->first();
        $ahmad = User::where('email', 'ahmad.rizky@gmail.com')->first();
        $budi  = User::where('email', 'budi.santoso@gmail.com')->first();
        $siti  = User::where('email', 'siti.nur@gmail.com')->first();
        $dewi  = User::where('email', 'dewi.lestari@gmail.com')->first();
        $hendra = User::where('email', 'hendra.wijaya@gmail.com')->first();

        $avanza   = Car::where('name', 'Avanza')->first();
        $xpander  = Car::where('name', 'Xpander')->first();
        $fortuner = Car::where('name', 'Fortuner')->first();
        $brio     = Car::where('name', 'Brio')->first();
        $ertiga   = Car::where('name', 'Ertiga')->first();
        $jazz     = Car::where('name', 'Jazz')->first();
        $rush     = Car::where('name', 'Rush')->first();

        $bookings = [
            // 1. Selesai (Completed): Kafka sewa Avanza
            [
                'user_id' => $kafka->id,
                'car_id' => $avanza->id,
                'start_date' => now()->subDays(6)->format('Y-m-d'),
                'end_date' => now()->subDays(3)->format('Y-m-d'),
                'total_days' => 3,
                'total_price' => 3 * $avanza->price_per_day,
                'booking_status' => 'completed',
                'notes' => 'Tolong siapkan mobil bersih untuk liburan keluarga ke Bandung.',
            ],
            // 2. Sedang Berlangsung (Ongoing): Ahmad sewa Xpander
            [
                'user_id' => $ahmad->id,
                'car_id' => $xpander->id,
                'start_date' => now()->subDays(1)->format('Y-m-d'),
                'end_date' => now()->addDays(2)->format('Y-m-d'),
                'total_days' => 3,
                'total_price' => 3 * $xpander->price_per_day,
                'booking_status' => 'ongoing',
                'notes' => 'Perjalanan dinas ke Yogyakarta, butuh nota perusahaan.',
            ],
            // 3. Disetujui (Confirmed): Budi sewa Fortuner
            [
                'user_id' => $budi->id,
                'car_id' => $fortuner->id,
                'start_date' => now()->addDays(2)->format('Y-m-d'),
                'end_date' => now()->addDays(4)->format('Y-m-d'),
                'total_days' => 2,
                'total_price' => 2 * $fortuner->price_per_day,
                'booking_status' => 'confirmed',
                'notes' => 'Penjemputan di Bandara Soekarno-Hatta Terminal 3.',
            ],
            // 4. Menunggu Pembayaran (Pending): Siti sewa Brio
            [
                'user_id' => $siti->id,
                'car_id' => $brio->id,
                'start_date' => now()->addDays(3)->format('Y-m-d'),
                'end_date' => now()->addDays(5)->format('Y-m-d'),
                'total_days' => 2,
                'total_price' => 2 * $brio->price_per_day,
                'booking_status' => 'pending',
                'notes' => 'Sewa lepas kunci untuk keperluan pribadi.',
            ],
            // 5. Selesai Terlambat (Completed): Dewi sewa Ertiga
            [
                'user_id' => $dewi->id,
                'car_id' => $ertiga->id,
                'start_date' => now()->subDays(8)->format('Y-m-d'),
                'end_date' => now()->subDays(5)->format('Y-m-d'),
                'total_days' => 3,
                'total_price' => 3 * $ertiga->price_per_day,
                'booking_status' => 'completed',
                'notes' => 'Acara pernikahan keluarga di Bogor.',
            ],
            // 6. Dibatalkan (Cancelled): Hendra sewa Jazz
            [
                'user_id' => $hendra->id,
                'car_id' => $jazz->id,
                'start_date' => now()->subDays(3)->format('Y-m-d'),
                'end_date' => now()->subDays(1)->format('Y-m-d'),
                'total_days' => 2,
                'total_price' => 2 * $jazz->price_per_day,
                'booking_status' => 'cancelled',
                'notes' => 'Jadwal kunjungan mendadak dibatalkan oleh pihak klien.',
            ],
            // 7. Selesai (Completed): Kafka sewa Rush
            [
                'user_id' => $kafka->id,
                'car_id' => $rush->id,
                'start_date' => now()->subDays(12)->format('Y-m-d'),
                'end_date' => now()->subDays(10)->format('Y-m-d'),
                'total_days' => 2,
                'total_price' => 2 * $rush->price_per_day,
                'booking_status' => 'completed',
                'notes' => 'Perjalanan wisata ke Pantai Anyer.',
            ],
        ];

        foreach ($bookings as $data) {
            Booking::updateOrCreate(
                [
                    'user_id' => $data['user_id'],
                    'car_id' => $data['car_id'],
                    'start_date' => $data['start_date'],
                ],
                $data
            );
        }
    }
}
