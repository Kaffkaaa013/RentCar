@extends('layouts.dashboard')

@section('title', 'Detail Booking #' . $booking->id)
@section('page-title', 'Detail Booking #' . $booking->id)

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <a href="{{ route('customer.rentals.index') }}" class="text-xs font-semibold text-blue-600 hover:underline inline-flex items-center gap-1 mb-1">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Daftar Booking
            </a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Detail Booking #{{ $booking->id }}</h1>
        </div>

        @if ($booking->booking_status === 'pending')
            <form action="{{ route('customer.rentals.cancel', $booking) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan booking ini?')">
                @csrf
                <button type="submit" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-xl border border-rose-200 transition-colors cursor-pointer">
                    Batalkan Booking
                </button>
            </form>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Detail Mobil & Jadwal --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row gap-5">
                    <img src="{{ $booking->car->image_url }}" alt="{{ $booking->car->name }}" class="w-full sm:w-44 h-32 object-cover rounded-xl bg-slate-100">
                    <div class="flex-1 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-blue-600 uppercase">{{ $booking->car->category->name ?? 'Mobil' }}</span>
                            <span class="text-[11px] font-bold uppercase px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700">{{ $booking->booking_status }}</span>
                        </div>
                        <h2 class="text-xl font-black text-slate-900">{{ $booking->car->brand }} {{ $booking->car->name }}</h2>
                        <p class="text-xs text-slate-500">Plat: <strong class="font-mono">{{ $booking->car->plate_number }}</strong> &middot; {{ ucfirst($booking->car->transmission) }} &middot; {{ $booking->car->seats }} Kursi</p>

                        <div class="pt-3 border-t border-slate-100 grid grid-cols-3 gap-3 text-xs">
                            <div>
                                <span class="text-slate-400 block">Mulai</span>
                                <span class="font-bold text-slate-800">{{ $booking->start_date->format('d M Y') }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block">Selesai</span>
                                <span class="font-bold text-slate-800">{{ $booking->end_date->format('d M Y') }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block">Total ({{ $booking->total_days }} hari)</span>
                                <span class="font-black text-[#0f2557]">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($booking->notes)
                    <div class="mt-4 pt-4 border-t border-slate-100 text-xs text-slate-600">
                        <strong class="text-slate-700">Catatan:</strong> {{ $booking->notes }}
                    </div>
                @endif
            </div>

            {{-- Info Pengembalian jika ada --}}
            @if ($booking->carReturn)
                <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3">Informasi Pengembalian</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 block">Status Pengembalian</span>
                            <span class="font-bold uppercase text-slate-800">{{ $booking->carReturn->return_status }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Tanggal Kembali Aktual</span>
                            <span class="font-bold text-slate-800">{{ $booking->carReturn->actual_return_date ? $booking->carReturn->actual_return_date->format('d M Y H:i') : '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Keterlambatan & Denda</span>
                            <span class="font-bold {{ $booking->carReturn->late_fee > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $booking->carReturn->late_days }} hari (Rp {{ number_format($booking->carReturn->late_fee, 0, ',', '.') }})
                            </span>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-slate-600"><strong>Kondisi:</strong> {{ $booking->carReturn->condition_description }}</p>
                </div>
            @endif
        </div>

        {{-- Panel Pembayaran & Upload Bukti --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm h-fit">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4">Status Pembayaran</h3>

            @if ($booking->payment)
                <div class="space-y-3 text-xs mb-5">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Metode</span>
                        <span class="font-bold text-slate-800">{{ $booking->payment->payment_method }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Jumlah</span>
                        <span class="font-bold text-slate-800">Rp {{ number_format($booking->payment->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Status</span>
                        <span class="font-bold uppercase {{ $booking->payment->payment_status === 'verified' ? 'text-emerald-600' : ($booking->payment->payment_status === 'rejected' ? 'text-rose-600' : 'text-amber-600') }}">
                            {{ $booking->payment->payment_status }}
                        </span>
                    </div>
                </div>

                @if ($booking->payment->proof_image)
                    <div class="mb-5">
                        <span class="block text-xs font-bold text-slate-500 mb-2">Bukti Terunggah:</span>
                        <a href="{{ asset('storage/' . $booking->payment->proof_image) }}" target="_blank">
                            <img src="{{ asset('storage/' . $booking->payment->proof_image) }}" alt="Bukti Pembayaran" class="w-full h-44 object-cover rounded-xl border border-slate-200">
                        </a>
                    </div>
                @endif
            @endif

            @if ($booking->booking_status === 'pending' && (! $booking->payment || $booking->payment->payment_status !== 'verified'))
                <form action="{{ route('customer.payments.store', $booking) }}" method="POST" enctype="multipart/form-data" class="space-y-3 pt-4 border-t border-slate-100">
                    @csrf
                    <div>
                        <label for="payment_method" class="block text-xs font-bold text-slate-700 mb-1">Metode Pembayaran</label>
                        <select name="payment_method" id="payment_method" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs">
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="E-Wallet">E-Wallet</option>
                            <option value="Tunai">Tunai</option>
                        </select>
                    </div>
                    <div>
                        <label for="proof_image" class="block text-xs font-bold text-slate-700 mb-1">Upload Bukti Transfer *</label>
                        <input type="file" name="proof_image" id="proof_image" accept="image/*" required
                            class="w-full px-2.5 py-2 border border-slate-200 rounded-xl text-xs file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700">
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm cursor-pointer">
                        {{ ($booking->payment && $booking->payment->proof_image) ? 'Unggah Ulang Bukti' : 'Kirim Bukti Pembayaran' }}
                    </button>
                </form>
            @endif
        </div>
    </div>
@endsection
