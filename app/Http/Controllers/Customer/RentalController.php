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
    public function create(Request $request): View
    {
        $cars = Car::with('category')->where('status', 'available')->orderBy('name')->get();
        $selectedCarId = $request->query('car_id');

        // Abaikan pilihan jika mobil tidak tersedia atau tidak ditemukan.
        if ($selectedCarId && !$cars->contains('id', (int) $selectedCarId)) {
            $selectedCarId = null;
        }

        return view('customer.rentals.create', compact('cars', 'selectedCarId'));
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

        if ($request->user()->isAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('success', 'Pengajuan rental berhasil dibuat.');
        }

        return redirect()
            ->route('customer.payments.create', $booking->id)
            ->with('success', 'Pesanan berhasil dibuat. Silakan unggah bukti pembayaran.');
    }

    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        if ($booking->user_id !== $request->user()->id) {
            abort(403, 'Anda tidak memiliki akses ke transaksi ini.');
        }

        if ($booking->booking_status !== 'pending') {
            return back()->with('error', 'Pesanan yang sudah diproses tidak dapat dibatalkan.');
        }

        $booking->update([
            'booking_status' => 'cancelled',
        ]);

        return back()->with('success', 'Pesanan rental berhasil dibatalkan.');
    }
}