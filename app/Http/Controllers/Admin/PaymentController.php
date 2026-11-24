<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['booking.user', 'booking.car'])->latest()->get();

        return view('admin.payments.index', compact('payments'));
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'payment_status' => 'required|in:verified,rejected',
        ]);

        $payment->update([
            'payment_status' => $request->payment_status,
            'paid_at' => $payment->paid_at ?? now(),
        ]);

        // Sinkronkan status booking agar otomatis tampil CONFIRMED di Dashboard Admin
        if ($payment->booking) {
            $payment->booking->update([
                'booking_status' => $request->payment_status === 'verified' ? 'confirmed' : 'cancelled',
            ]);
        }

        $message = $request->payment_status === 'verified'
            ? 'Pembayaran diterima. Status booking berubah menjadi Confirmed.'
            : 'Pembayaran ditolak dan booking dibatalkan.';

        return back()->with('success', $message);
    }
}