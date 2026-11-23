@extends('layouts.dashboard')

@section('title', 'Rental Mobil')
@section('page-title', 'Rental Mobil')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('customer.dashboard') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">← Kembali ke Dashboard</a>
        <h1 class="mt-3 text-2xl font-black text-slate-900">Ajukan Rental Mobil</h1>
        <p class="mt-1 text-sm text-slate-500">Pilih mobil dan tentukan tanggal penggunaan. Pengajuan akan menunggu konfirmasi admin.</p>
    </div>

    @if ($cars->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-10 text-center">
            <p class="font-semibold text-slate-700">Belum ada mobil yang tersedia.</p>
            <p class="mt-1 text-sm text-slate-500">Silakan coba lagi nanti.</p>
        </div>
    @else
    <form method="POST" action="{{ route('customer.rentals.store') }}" class="space-y-6 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
        @csrf
        @if ($errors->any())
            <div class="rounded-xl bg-rose-50 p-4 text-sm text-rose-700">
                <ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div>
            <label for="car_id" class="mb-2 block text-sm font-bold text-slate-700">Pilih Mobil</label>
            <select id="car_id" name="car_id" required class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">-- Pilih mobil --</option>
                @foreach ($cars as $car)
                    <option value="{{ $car->id }}" data-price="{{ $car->price_per_day }}" @selected(old('car_id', $selectedCarId) == $car->id)>
                        {{ $car->name }} - {{ $car->plate_number }} (Rp {{ number_format($car->price_per_day, 0, ',', '.') }}/hari)
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="start_date" class="mb-2 block text-sm font-bold text-slate-700">Tanggal Mulai Rental</label>
                <input id="start_date" name="start_date" type="date" min="{{ now()->toDateString() }}" value="{{ old('start_date') }}" required class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label for="end_date" class="mb-2 block text-sm font-bold text-slate-700">Tanggal Selesai Rental</label>
                <input id="end_date" name="end_date" type="date" min="{{ now()->addDay()->toDateString() }}" value="{{ old('end_date') }}" required class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <div class="rounded-xl bg-blue-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Estimasi Biaya Rental</p>
            <p id="estimate" class="mt-1 text-2xl font-black text-blue-900">Rp 0</p>
            <p id="duration" class="mt-1 text-xs text-blue-700">Pilih mobil dan tanggal rental untuk melihat estimasi.</p>
            <p class="mt-2 text-xs text-blue-700">Perhitungan berdasarkan jumlah hari antara tanggal mulai dan selesai dikalikan harga sewa per hari.</p>
        </div>

        <div>
            <label for="notes" class="mb-2 block text-sm font-bold text-slate-700">Catatan (Opsional)</label>
            <textarea id="notes" name="notes" rows="3" maxlength="1000" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Tambahkan catatan untuk admin...">{{ old('notes') }}</textarea>
        </div>

        <button type="submit" class="w-full rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700">Kirim Pengajuan Rental</button>
    </form>
    @endif
</div>
@endsection

@push('scripts')
<script>
(() => {
    const car = document.getElementById('car_id');
    const start = document.getElementById('start_date');
    const end = document.getElementById('end_date');
    const estimate = document.getElementById('estimate');
    const duration = document.getElementById('duration');
    if (!car || !start || !end) return;

    const rupiah = value => new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', maximumFractionDigits: 0
    }).format(value);

    function calculate() {
        const price = Number(car.selectedOptions[0]?.dataset.price || 0);
        const first = start.value ? new Date(start.value + 'T00:00:00') : null;
        const last = end.value ? new Date(end.value + 'T00:00:00') : null;
        const days = first && last ? Math.round((last - first) / 86400000) : 0;
        if (price && days > 0) {
            estimate.textContent = rupiah(price * days);
            duration.textContent = days + ' hari × ' + rupiah(price) + ' per hari';
        } else {
            estimate.textContent = 'Rp 0';
            duration.textContent = 'Pilih mobil dan tanggal rental untuk melihat estimasi.';
        }
    }
    car.addEventListener('change', calculate);
    start.addEventListener('change', () => {
        end.min = start.value ? new Date(new Date(start.value + 'T00:00:00').getTime() + 86400000).toISOString().slice(0, 10) : '';
        calculate();
    });
    end.addEventListener('change', calculate);
    calculate();
})();
</script>
@endpush
