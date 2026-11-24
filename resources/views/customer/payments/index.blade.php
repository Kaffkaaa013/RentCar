@extends('layouts.dashboard')

@section('title', 'Riwayat Pembayaran')
@section('page-title', 'Riwayat Pembayaran')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-200 flex justify-between items-center">
        <div>
            <h2 class="text-lg font-bold text-[#0f2557]">Riwayat Pembayaran Saya</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pantau status verifikasi bukti transfer penyewaan mobil Anda.</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase text-slate-500 font-semibold">
                    <th class="px-6 py-3.5">ID Booking</th>
                    <th class="px-6 py-3.5">Nama Mobil</th>
                    <th class="px-6 py-3.5">Metode</th>
                    <th class="px-6 py-3.5">Nominal</th>
                    <th class="px-6 py-3.5">Tanggal Bayar</th>
                    <th class="px-6 py-3.5">Bukti Bayar</th>
                    <th class="px-6 py-3.5">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($payments as $payment)
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-6 py-4 font-bold text-slate-800">#{{ $payment->booking_id }}</td>
                        <td class="px-6 py-4 font-semibold text-slate-700">{{ $payment->booking->car->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $payment->payment_method }}</td>
                        <td class="px-6 py-4 font-semibold text-slate-800">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $payment->paid_at ? $payment->paid_at->format('d M Y H:i') : '-' }}</td>
                        <td class="px-6 py-4">
                            @if ($payment->proof_image)
                                <a href="{{ asset('storage/' . $payment->proof_image) }}" target="_blank"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs font-semibold">
                                    <i data-lucide="image" class="w-3.5 h-3.5"></i>
                                    Lihat Bukti
                                </a>
                            @else
                                <span class="text-slate-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if (in_array($payment->payment_status, ['verified', 'paid']))
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Selesai</span>
                            @elseif ($payment->payment_status === 'rejected')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-700">Ditolak</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">Menunggu Verifikasi</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-slate-400">Belum ada riwayat pembayaran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection