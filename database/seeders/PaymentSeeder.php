<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Seed payments table.
     */
    public function run(): void
    {
        $bookings = Booking::all();

        foreach ($bookings as $booking) {
            $paymentStatus = match ($booking->booking_status) {
                'completed', 'ongoing', 'confirmed' => 'verified',
                'cancelled' => 'rejected',
                default => 'pending',
            };

            $paidAt = in_array($paymentStatus, ['verified', 'rejected']) ? $booking->created_at : null;

            Payment::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'payment_method' => $this->getRandomMethod($booking->id),
                    'amount' => $booking->total_price,
                    'proof_image' => 'payments/bukti_transfer_' . $booking->id . '.jpg',
                    'payment_status' => $paymentStatus,
                    'paid_at' => $paidAt,
                ]
            );
        }
    }

    private function getRandomMethod(int $id): string
    {
        $methods = [
            'Transfer Bank BCA',
            'Transfer Bank Mandiri',
            'Transfer Bank BRI',
            'QRIS / GoPay',
            'Tunai di Kantor',
        ];

        return $methods[$id % count($methods)];
    }
}
