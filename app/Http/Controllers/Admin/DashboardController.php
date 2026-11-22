<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use App\Models\Payment;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard admin dengan ringkasan statistik seluruh sistem.
     */
    public function index(): View
    {
        // Statistik ringkasan
        $totalCars = Car::count();
        $availableCars = Car::where('status', 'available')->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalBookings = Booking::count();

        // Total pendapatan dari pembayaran yang sudah verified
        $totalRevenue = Payment::where('payment_status', 'verified')->sum('amount');

        // Status booking breakdown
        $bookingsByStatus = [
            'pending' => Booking::where('booking_status', 'pending')->count(),
            'confirmed' => Booking::where('booking_status', 'confirmed')->count(),
            'ongoing' => Booking::where('booking_status', 'ongoing')->count(),
            'completed' => Booking::where('booking_status', 'completed')->count(),
            'cancelled' => Booking::where('booking_status', 'cancelled')->count(),
        ];

        // Jumlah pembayaran menunggu verifikasi
        $pendingPayments = Payment::where('payment_status', 'pending')->count();

        // 5 booking terbaru
        $recentBookings = Booking::with(['user', 'car', 'payment'])
            ->latest()
            ->take(5)
            ->get();

        // Mobil yang sedang disewa (booking ongoing)
        $ongoingRentals = Booking::with(['user', 'car'])
            ->where('booking_status', 'ongoing')
            ->get();

        return view('admin.dashboard', compact(
            'totalCars',
            'availableCars',
            'totalCustomers',
            'totalBookings',
            'totalRevenue',
            'bookingsByStatus',
            'pendingPayments',
            'recentBookings',
            'ongoingRentals'
        ));
    }
}
