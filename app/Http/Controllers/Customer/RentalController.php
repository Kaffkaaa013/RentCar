<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RentalController extends Controller
{
    public function create(): View
    {
        $cars = Car::with('category')->where('status', 'available')->orderBy('name')->get();

        return view('customer.rentals.create', compact('cars'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'car_id' => ['required', 'exists:cars,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $booking = DB::transaction(function () use ($request, $validated) {
            $car = Car::whereKey($validated['car_id'])->lockForUpdate()->firstOrFail();

            if ($car->status !== 'available') {
                throw ValidationException::withMessages([
                    'car_id' => 'Mobil sedang tidak tersedia.',
                ]);
            }

            $conflict = Booking::where('car_id', $car->id)
                ->whereIn('booking_status', ['pending', 'confirmed', 'ongoing'])
                ->whereDate('start_date', '<=', $validated['end_date'])
                ->whereDate('end_date', '>=', $validated['start_date'])
                ->exists();

            if ($conflict) {
                throw ValidationException::withMessages([
                    'start_date' => 'Mobil sudah dipesan pada rentang tanggal tersebut. Silakan pilih tanggal lain.',
                ]);
            }

            $days = max(1, (int) \Carbon\Carbon::parse($validated['start_date'])
                ->diffInDays(\Carbon\Carbon::parse($validated['end_date'])));

            return Booking::create([
                'user_id' => $request->user()->id,
                'car_id' => $car->id,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'total_days' => $days,
                'total_price' => $days * $car->price_per_day,
                'booking_status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('customer.dashboard')
            ->with('success', 'Pengajuan rental berhasil dibuat. Silakan tunggu konfirmasi admin.');
    }
}
