<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use App\Models\CarReturn;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RentalController extends Controller
{
    /**
     * Tampilkan seluruh daftar booking/rental.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $bookings = Booking::with(['user', 'car', 'payment', 'carReturn'])
            ->when($status, fn ($q) => $q->where('booking_status', $status))
            ->latest()
            ->paginate(12);

        return view('admin.rentals.index', compact('bookings', 'status'));
    }

    /**
     * Tampilkan form tambah booking oleh admin.
     */
    public function create(Request $request): View
    {
        $customers = User::where('role', 'customer')->orderBy('name')->get();
        $cars = Car::where('status', 'available')->orderBy('brand')->orderBy('name')->get();
        $selectedCarId = $request->integer('car_id') ?: null;

        return view('admin.rentals.create', compact('customers', 'cars', 'selectedCarId'));
    }

    /**
     * Simpan booking baru oleh admin.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'car_id' => ['required', 'exists:cars,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'payment_method' => ['required', 'in:Transfer Bank,E-Wallet,Tunai'],
            'payment_status' => ['required', 'in:pending,verified'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $car = Car::findOrFail($validated['car_id']);

        if ($car->status !== 'available') {
            return back()->withInput()->withErrors(['car_id' => 'Mobil sedang tidak tersedia.']);
        }

        if (Booking::hasDateConflict($car->id, $validated['start_date'], $validated['end_date'])) {
            return back()->withInput()->withErrors(['start_date' => 'Jadwal mobil bertabrakan dengan booking lain.']);
        }

        $totalDays = Booking::calculateDays($validated['start_date'], $validated['end_date']);
        $totalPrice = $totalDays * (float) $car->price_per_day;
        $isVerified = $validated['payment_status'] === 'verified';

        $booking = DB::transaction(function () use ($validated, $car, $totalDays, $totalPrice, $isVerified): Booking {
            $booking = Booking::create([
                'user_id' => $validated['user_id'],
                'car_id' => $car->id,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'total_days' => $totalDays,
                'total_price' => $totalPrice,
                'booking_status' => $isVerified ? 'confirmed' : 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            Payment::create([
                'booking_id' => $booking->id,
                'payment_method' => $validated['payment_method'],
                'amount' => $totalPrice,
                'payment_status' => $validated['payment_status'],
                'paid_at' => $isVerified ? now() : null,
            ]);

            return $booking;
        });

        return redirect()->route('admin.rentals.show', $booking)->with('success', 'Booking berhasil dibuat oleh admin.');
    }

    /**
     * Tampilkan detail booking, pembayaran, serah terima, dan pengembalian.
     */
    public function show(Booking $booking): View
    {
        $booking->load(['user', 'car.category', 'payment', 'carReturn']);

        return view('admin.rentals.show', compact('booking'));
    }

    /**
     * Serah terima mobil ke customer (Confirmed -> Ongoing).
     */
    public function handover(Booking $booking): RedirectResponse
    {
        if ($booking->booking_status !== 'confirmed') {
            return back()->with('error', 'Serah terima hanya dapat dilakukan untuk booking berstatus Confirmed.');
        }

        DB::transaction(function () use ($booking): void {
            $booking->update(['booking_status' => 'ongoing']);

            CarReturn::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'condition_description' => 'Mobil diserahkan kepada penyewa dalam kondisi baik.',
                    'late_days' => 0,
                    'late_fee' => 0,
                    'return_status' => 'pending',
                ]
            );
        });

        return back()->with('success', 'Mobil berhasil diserahkan. Status sewa kini Ongoing.');
    }

    /**
     * Proses pengembalian mobil dan hitung denda keterlambatan (Ongoing -> Completed).
     */
    public function returnCar(Request $request, Booking $booking): RedirectResponse
    {
        if ($booking->booking_status !== 'ongoing') {
            return back()->with('error', 'Pengembalian hanya berlaku untuk booking berstatus Ongoing.');
        }

        $validated = $request->validate([
            'actual_return_date' => ['required', 'date'],
            'condition_description' => ['required', 'string', 'max:1000'],
            'late_fee_per_day' => ['nullable', 'numeric', 'min:0'],
        ], [
            'actual_return_date.required' => 'Tanggal pengembalian aktual wajib diisi.',
            'condition_description.required' => 'Deskripsi kondisi mobil wajib diisi.',
        ]);

        $actualDate = Carbon::parse($validated['actual_return_date']);
        $dueDate = Carbon::parse($booking->end_date)->endOfDay();

        $lateDays = 0;
        if ($actualDate->gt($dueDate)) {
            $lateDays = (int) Carbon::parse($booking->end_date)->startOfDay()->diffInDays($actualDate->copy()->startOfDay());
            $lateDays = max(1, $lateDays);
        }

        $ratePerDay = isset($validated['late_fee_per_day']) && $validated['late_fee_per_day'] !== ''
            ? (float) $validated['late_fee_per_day']
            : (float) $booking->car->price_per_day;

        $lateFee = $lateDays * $ratePerDay;

        DB::transaction(function () use ($booking, $actualDate, $validated, $lateDays, $lateFee): void {
            CarReturn::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'actual_return_date' => $actualDate,
                    'condition_description' => $validated['condition_description'],
                    'late_days' => $lateDays,
                    'late_fee' => $lateFee,
                    'return_status' => 'completed',
                ]
            );

            $booking->update(['booking_status' => 'completed']);
        });

        return back()->with('success', 'Pengembalian mobil selesai dicatat. Denda keterlambatan: Rp ' . number_format($lateFee, 0, ',', '.'));
    }

    /**
     * Perbarui status booking secara cepat dari dashboard admin.
     */
    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'booking_status' => ['required', 'in:pending,confirmed,ongoing,completed,cancelled'],
        ]);

        DB::transaction(function () use ($booking, $validated): void {
            $booking->update(['booking_status' => $validated['booking_status']]);

            if ($validated['booking_status'] === 'confirmed' && $booking->payment && $booking->payment->payment_status === 'pending') {
                $booking->payment->update([
                    'payment_status' => 'verified',
                    'paid_at' => $booking->payment->paid_at ?? now(),
                ]);
            }

            if ($validated['booking_status'] === 'ongoing') {
                CarReturn::updateOrCreate(
                    ['booking_id' => $booking->id],
                    [
                        'condition_description' => 'Mobil diserahkan kepada penyewa dalam kondisi baik.',
                        'late_days' => 0,
                        'late_fee' => 0,
                        'return_status' => 'pending',
                    ]
                );
            }
        });

        return back()->with('success', 'Status booking berhasil diperbarui menjadi ' . ucfirst($validated['booking_status']) . '.');
    }

    /**
     * Batalkan booking oleh admin.
     */
    public function cancel(Booking $booking): RedirectResponse
    {
        if (! in_array($booking->booking_status, ['pending', 'confirmed'], true)) {
            return back()->with('error', 'Booking yang sedang berjalan atau selesai tidak dapat dibatalkan.');
        }

        $booking->update(['booking_status' => 'cancelled']);

        return back()->with('success', 'Booking berhasil dibatalkan.');
    }
}
