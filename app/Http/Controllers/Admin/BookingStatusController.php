<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BookingStatusController extends Controller
{
    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'booking_status' => ['required', 'in:confirmed,ongoing,cancelled'],
        ]);

        $next = $validated['booking_status'];
        $allowed = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['ongoing', 'cancelled'],
        ];

        if (! in_array($next, $allowed[$booking->booking_status] ?? [], true)) {
            throw ValidationException::withMessages([
                'booking_status' => 'Perubahan status rental tidak valid.',
            ]);
        }

        $booking->update(['booking_status' => $next]);

        return back()->with('success', 'Status rental berhasil diperbarui.');
    }
}
