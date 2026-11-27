<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use App\Models\Payment;
use App\Services\PublicStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RentalController extends Controller
{
    /**
     * Tampilkan daftar seluruh booking milik customer.
     */
    public function index(Request $request): View
    {
        $bookings = $request->user()
            ->bookings()
            ->with(['car.category', 'payment', 'carReturn'])
            ->latest()
            ->paginate(10);

        return view('customer.rentals.index', compact('bookings'));
    }

    /**
     * Tampilkan form pemesanan mobil.
     */
    public function create(Request $request): View
    {
        $cars = Car::with('category')
            ->where('status', 'available')
            ->orderBy('brand')
            ->orderBy('name')
            ->get();

        $selectedCar = null;
        if ($request->filled('car_id')) {
            $selectedCar = $cars->firstWhere('id', (int) $request->integer('car_id'));
        }

        // Ambil jadwal booking aktif per mobil untuk ditampilkan ke user
        $bookedDates = Booking::query()
            ->whereIn('booking_status', ['pending', 'confirmed', 'ongoing'])
            ->where('end_date', '>=', now()->toDateString())
            ->get(['car_id', 'start_date', 'end_date'])
            ->groupBy('car_id')
            ->map(fn ($items) => $items->map(fn ($b) => [
                'start' => $b->start_date->format('d M Y'),
                'end' => $b->end_date->format('d M Y'),
            ])->values());

        return view('customer.rentals.create', compact('cars', 'selectedCar', 'bookedDates'));
    }

    /**
     * Simpan pemesanan mobil baru beserta data pembayaran awal.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'car_id' => ['required', 'exists:cars,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'payment_method' => ['required', 'in:Transfer Bank,E-Wallet,Tunai'],
            'proof_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'car_id.required' => 'Silakan pilih mobil terlebih dahulu.',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'end_date.after_or_equal' => 'Tanggal selesai minimal sama dengan tanggal mulai.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'proof_image.image' => 'Bukti pembayaran harus berupa gambar.',
            'proof_image.max' => 'Ukuran bukti pembayaran maksimal 2MB.',
        ]);

        $car = Car::findOrFail($validated['car_id']);

        if ($car->status !== 'available') {
            return back()->withInput()->withErrors([
                'car_id' => 'Mobil ini sedang tidak tersedia untuk disewa.',
            ]);
        }

        if (Booking::hasDateConflict($car->id, $validated['start_date'], $validated['end_date'])) {
            return back()->withInput()->withErrors([
                'start_date' => 'Mobil sudah dipesan pada rentang tanggal tersebut. Silakan pilih tanggal lain.',
            ]);
        }

        $totalDays = Booking::calculateDays($validated['start_date'], $validated['end_date']);
        $totalPrice = $totalDays * (float) $car->price_per_day;

        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            $proofPath = PublicStorageService::store($request->file('proof_image'), 'payments');
        }

        $booking = DB::transaction(function () use ($request, $car, $validated, $totalDays, $totalPrice, $proofPath): Booking {
            $booking = Booking::create([
                'user_id' => $request->user()->id,
                'car_id' => $car->id,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'total_days' => $totalDays,
                'total_price' => $totalPrice,
                'booking_status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            Payment::create([
                'booking_id' => $booking->id,
                'payment_method' => $validated['payment_method'],
                'amount' => $totalPrice,
                'proof_image' => $proofPath,
                'payment_status' => 'pending',
                'paid_at' => $proofPath ? now() : null,
            ]);

            return $booking;
        });

        return redirect()
            ->route('customer.rentals.show', $booking)
            ->with('success', 'Booking berhasil dibuat! Silakan lengkapi atau tunggu verifikasi pembayaran.');
    }

    /**
     * Tampilkan detail booking milik customer.
     */
    public function show(Request $request, Booking $booking): View
    {
        if ($booking->user_id !== $request->user()->id) {
            abort(403, 'Anda tidak berhak melihat booking ini.');
        }

        $booking->load(['car.category', 'payment', 'carReturn']);

        return view('customer.rentals.show', compact('booking'));
    }

    /**
     * Batalkan booking yang masih berstatus pending.
     */
    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        if ($booking->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($booking->booking_status !== 'pending') {
            return back()->with('error', 'Hanya booking berstatus pending yang dapat dibatalkan.');
        }

        $booking->update(['booking_status' => 'cancelled']);

        return back()->with('success', 'Booking berhasil dibatalkan.');
    }

    /**
     * Tampilkan status pengembalian mobil milik customer.
     */
    public function returns(Request $request): View
    {
        $bookings = $request->user()
            ->bookings()
            ->with(['car', 'carReturn'])
            ->whereIn('booking_status', ['ongoing', 'completed'])
            ->latest()
            ->get();

        return view('customer.returns.index', compact('bookings'));
    }
}
