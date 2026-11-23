<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\CarReturn;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReturnController extends Controller
{
    private const LATE_FEE_PER_DAY = 100000;

    public function index(Request $request): View
    {
        $rentals = Booking::with(['car', 'carReturn'])
            ->where('user_id', $request->user()->id)
            ->where('booking_status', 'ongoing')
            ->latest()
            ->get();

        $returnHistory = Booking::with(['car', 'carReturn'])
            ->where('user_id', $request->user()->id)
            ->where('booking_status', 'completed')
            ->whereHas('carReturn')
            ->latest()
            ->take(10)
            ->get();

        return view('customer.returns.index', compact('rentals', 'returnHistory'));
    }

    public function store(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'condition_description' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        DB::transaction(function () use ($request, $booking, $validated) {
            $lockedBooking = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();

            if ($lockedBooking->user_id !== $request->user()->id ||
                $lockedBooking->booking_status !== 'ongoing' ||
                $lockedBooking->carReturn()->where('return_status', 'completed')->exists()) {
                throw ValidationException::withMessages([
                    'condition_description' => 'Rental ini tidak dapat diproses untuk pengembalian.',
                ]);
            }

            $returnedAt = now();
            $dueDate = $lockedBooking->end_date->copy()->startOfDay();
            $lateDays = max(0, (int) $dueDate->diffInDays($returnedAt->copy()->startOfDay(), false));
            $lateFee = $lateDays * self::LATE_FEE_PER_DAY;

            CarReturn::updateOrCreate(
                ['booking_id' => $lockedBooking->id],
                [
                    'actual_return_date' => $returnedAt,
                    'condition_description' => $validated['condition_description'],
                    'late_days' => $lateDays,
                    'late_fee' => $lateFee,
                    'return_status' => 'completed',
                ]
            );

            $lockedBooking->update(['booking_status' => 'completed']);
        });

        return redirect()->route('customer.returns.index')
            ->with('success', 'Pengembalian mobil berhasil dicatat.');
    }
}
