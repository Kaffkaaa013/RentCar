<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Seed reviews table.
     */
    public function run(): void
    {
        // Ulasan dari customer untuk booking yang sudah selesai
        $completedBookings = Booking::where('booking_status', 'completed')->get();

        $reviews = [
            [
                'rating' => 5,
                'comment' => 'Pelayanan luar biasa memuaskan! Unit mobil sangat bersih, wangi, dan irit bbm untuk liburan keluarga. Pasti akan sewa di sini lagi.',
            ],
            [
                'rating' => 4,
                'comment' => 'Mobil sangat nyaman dan AC dingin sekali. Proses serah terima cepat dan staf ramah, sangat direkomendasikan untuk rental harian.',
            ],
            [
                'rating' => 5,
                'comment' => 'Harga paling transparan tanpa embel-embel biaya tersembunyi. Kondisi mesin prima tidak ada kendala sama sekali di tanjakan.',
            ],
        ];

        foreach ($completedBookings as $index => $booking) {
            $rev = $reviews[$index % count($reviews)];

            Review::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'user_id' => $booking->user_id,
                    'car_id' => $booking->car_id,
                    'rating' => $rev['rating'],
                    'comment' => $rev['comment'],
                ]
            );
        }
    }
}
