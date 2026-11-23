@extends('layouts.dashboard')

@section('title', 'Pengembalian Mobil')
@section('page-title', 'Pengembalian Mobil')

@section('content')
<div class="mb-6">
    <a href="{{ route('customer.dashboard') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">← Kembali ke Dashboard</a>
    <h1 class="mt-3 text-2xl font-black text-slate-900">Pengembalian Mobil</h1>
    <p class="mt-1 text-sm text-slate-500">Pilih rental yang sedang berlangsung, lalu isi kondisi mobil saat dikembalikan.</p>
</div>

@if ($errors->any())
    <div class="mb-5 rounded-xl bg-rose-50 p-4 text-sm text-rose-700">
        <ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<div class="space-y-5">
    @forelse ($rentals as $rental)
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                <img src="{{ $rental->car->image_url }}" alt="{{ $rental->car->name }}" class="h-24 w-full rounded-xl object-cover sm:w-36">
                <div class="flex-1">
                    <h2 class="text-lg font-black text-slate-900">{{ $rental->car->name }}</h2>
                    <p class="text-xs text-slate-500">{{ $rental->car->plate_number }}</p>
                    <p class="mt-2 text-sm text-slate-600">Masa rental: {{ $rental->start_date->format('d M Y') }} – {{ $rental->end_date->format('d M Y') }}</p>
                    <p class="text-sm font-bold text-slate-800">Total: Rp {{ number_format($rental->total_price, 0, ',', '.') }}</p>
                </div>
                <span class="w-fit rounded-full bg-indigo-100 px-3 py-1 text-xs font-bold text-indigo-700">Sedang Disewa</span>
            </div>

            <form method="POST" action="{{ route('customer.returns.store', $rental) }}" class="mt-5 border-t border-slate-100 pt-5">
                @csrf
                <label for="condition_{{ $rental->id }}" class="mb-2 block text-sm font-bold text-slate-700">Kondisi Mobil Saat Dikembalikan</label>
                <textarea id="condition_{{ $rental->id }}" name="condition_description" rows="3" required minlength="5" maxlength="2000" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: Mobil dalam kondisi baik, bersih, bensin penuh, tidak ada kerusakan."></textarea>
                <p class="mt-2 text-xs text-slate-500">Denda keterlambatan dihitung otomatis sebesar Rp100.000 per hari setelah tanggal akhir rental.</p>
                <button type="submit" onclick="return confirm('Catat pengembalian mobil ini?')" class="mt-4 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">Konfirmasi Pengembalian</button>
            </form>
        </div>
    @empty
        <div class="rounded-2xl border border-slate-100 bg-white p-10 text-center shadow-sm">
            <p class="font-semibold text-slate-700">Tidak ada mobil yang perlu dikembalikan saat ini.</p>
            <p class="mt-1 text-sm text-slate-500">Rental yang sudah dimulai akan muncul di halaman ini.</p>
        </div>
    @endforelse
</div>

<div class="mt-8 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
    <div class="border-b border-slate-100 px-5 py-4">
        <h2 class="font-bold text-slate-800">Riwayat Pengembalian</h2>
    </div>
    @forelse ($returnHistory as $history)
        <div class="flex flex-col gap-2 border-b border-slate-50 px-5 py-4 last:border-0 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold text-slate-800">{{ $history->car->name }} <span class="font-normal text-slate-400">· {{ $history->car->plate_number }}</span></p>
                <p class="text-xs text-slate-500">Dikembalikan: {{ optional($history->carReturn)->actual_return_date?->format('d M Y, H:i') ?? '-' }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $history->carReturn->condition_description ?? '' }}</p>
            </div>
            <div class="text-left sm:text-right">
                <p class="text-xs font-bold text-emerald-700">Selesai</p>
                <p class="text-sm font-bold text-slate-800">Denda: Rp {{ number_format($history->carReturn->late_fee ?? 0, 0, ',', '.') }}</p>
                <p class="text-xs text-slate-500">{{ $history->carReturn->late_days ?? 0 }} hari terlambat</p>
            </div>
        </div>
    @empty
        <p class="px-5 py-8 text-center text-sm text-slate-400">Belum ada riwayat pengembalian.</p>
    @endforelse
</div>
@endsection
