@extends('layouts.dashboard')

@section('title', 'Verifikasi Pembayaran')
@section('page-title', 'Verifikasi Pembayaran')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-200">
        <h2 class="text-lg font-bold text-[#0f2557]">Verifikasi Pembayaran Masuk</h2>
        <p class="text-xs text-slate-500 mt-0.5">Periksa bukti transfer dari penyewa sebelum menyetujui transaksi.</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase text-slate-500 font-semibold">
                    <th class="px-6 py-3.5">ID Booking</th>
                    <th class="px-6 py-3.5">Nama Mobil</th>
                    <th class="px-6 py-3.5">Penyewa</th>
                    <th class="px-6 py-3.5">Metode</th>
                    <th class="px-6 py-3.5">Nominal</th>
                    <th class="px-6 py-3.5">Tanggal Bayar</th>
                    <th class="px-6 py-3.5">Bukti Transfer</th>
                    <th class="px-6 py-3.5">Status</th>
                    <th class="px-6 py-3.5">Verifikasi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($payments as $payment)
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-6 py-4 font-bold text-slate-800">#{{ $payment->booking_id }}</td>
                        <td class="px-6 py-4 font-semibold text-slate-700">{{ $payment->booking->car->name ?? '-' }}</td>
                        <td class="px-6 py-4 font-medium text-slate-700">{{ $payment->booking->user->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $payment->payment_method }}</td>
                        <td class="px-6 py-4 font-semibold text-slate-800">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $payment->paid_at ? $payment->paid_at->format('d M Y H:i') : '-' }}</td>
                        <td class="px-6 py-4">
                            @if ($payment->proof_image)
                                <a href="{{ asset('storage/' . $payment->proof_image) }}" target="_blank"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs font-semibold">
                                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                    Buka Bukti
                                </a>
                            @else
                                <span class="text-slate-400 text-xs">Tidak ada</span>
                            @endif
                        </td>

                        {{-- Kolom STATUS --}}
                        <td class="px-6 py-4">
                            @if (in_array($payment->payment_status, ['verified', 'paid']))
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase bg-blue-100 text-blue-700">
                                    Confirmed
                                </span>
                            @elseif ($payment->payment_status === 'rejected')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase bg-rose-100 text-rose-700">
                                    Ditolak
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase bg-amber-100 text-amber-800">
                                    Pending
                                </span>
                            @endif
                        </td>

                        {{-- Kolom VERIFIKASI --}}
                        <td class="px-6 py-4">
                            @if ($payment->payment_status === 'pending')
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('admin.payments.update', $payment->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="payment_status" value="verified">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold cursor-pointer">
                                            Terima
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.payments.update', $payment->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="payment_status" value="rejected">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold cursor-pointer">
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            @elseif (in_array($payment->payment_status, ['verified', 'paid']))
                                @if ($payment->booking && $payment->booking->booking_status === 'confirmed')
                                    <form action="{{ route('admin.bookings.status', $payment->booking_id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="booking_status" value="ongoing">
                                        <input type="hidden" name="status" value="ongoing">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 text-xs font-semibold transition-colors cursor-pointer">
                                            Mulai Rental
                                        </button>
                                    </form>
                                @elseif ($payment->booking && $payment->booking->booking_status === 'ongoing')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                                        Sedang Disewa
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">Selesai</span>
                                @endif
                            @else
                                <span class="text-rose-500 text-xs font-medium">Dibatalkan</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-10 text-center text-slate-400">Belum ada data pembayaran masuk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection