<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('booking.car')
            ->whereHas('booking', function ($query) {$query->where('user_id', Auth::id());
            })
            ->latest()
            ->get();

        return view('customer.payments.index', compact('payments'));
    }

    public function create(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke transaksi ini.');
        }

        return view('customer.payments.create', compact('booking'));
    }

    public function store(Request $request, Booking$booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke transaksi ini.');
        }

        // Validasi upload bukti bayar maksimal 1 GB (1.048.576 KB)
        $request->validate([
            'payment_method' => 'required|string|max:50',
            'proof_image' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:1048576',
        ]);

        $filePath =$request->file('proof_image')->store('payments', 'public');

        Payment::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'amount' => $booking->total_price ?? $booking->total_harga ?? 0,
                'payment_method' => $request->payment_method,
                'proof_image' => $filePath,
                'payment_status' => 'pending',
                'paid_at' => now(),
            ]
        );

        return redirect()
            ->route('customer.payments.index')
            ->with('success', 'Bukti pembayaran berhasil diunggah dan menunggu konfirmasi Admin.');
    }
}