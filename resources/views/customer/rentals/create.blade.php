@extends('layouts.dashboard')

@section('title', 'Form Sewa Mobil')
@section('page-title', 'Sewa Mobil')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Formulir Pemesanan Mobil</h1>
        <p class="mt-1 text-sm text-slate-500">Pilih unit mobil dan tentukan tanggal sewa Anda. Sistem akan otomatis mengecek ketersediaan jadwal.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            @if ($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                    <strong class="font-bold block mb-1">Periksa kembali data Anda:</strong>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('customer.rentals.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label for="car_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pilih Mobil *</label>
                    <select name="car_id" id="car_id" required onchange="updateEstimate()"
                        class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
                        <option value="">-- Pilih Armada Mobil --</option>
                        @foreach ($cars as $car)
                            <option value="{{ $car->id }}"
                                data-price="{{ (float) $car->price_per_day }}"
                                data-name="{{ $car->brand }} {{ $car->name }}"
                                {{ (old('car_id', $selectedCar?->id) == $car->id) ? 'selected' : '' }}>
                                {{ $car->brand }} {{ $car->name }} ({{ $car->category->name ?? '-' }} &middot; {{ ucfirst($car->transmission) }}) - Rp {{ number_format($car->price_per_day, 0, ',', '.') }}/hari
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Mulai *</label>
                        <input type="date" name="start_date" id="start_date" min="{{ date('Y-m-d') }}" value="{{ old('start_date', date('Y-m-d')) }}" required onchange="updateEstimate()"
                            class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
                    </div>

                    <div>
                        <label for="end_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Selesai *</label>
                        <input type="date" name="end_date" id="end_date" min="{{ date('Y-m-d') }}" value="{{ old('end_date', date('Y-m-d', strtotime('+1 day'))) }}" required onchange="updateEstimate()"
                            class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
                    </div>
                </div>

                {{-- Info Jadwal Terpesan untuk Mobil Terpilih --}}
                <div id="bookedDatesBox" class="hidden p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800">
                    <strong class="font-bold block mb-1">Tanggal sudah terpesan untuk mobil ini:</strong>
                    <ul id="bookedDatesList" class="list-disc list-inside space-y-0.5"></ul>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="payment_method" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Metode Pembayaran *</label>
                        <select name="payment_method" id="payment_method" required
                            class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20">
                            <option value="Transfer Bank" {{ old('payment_method') === 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank (BCA / Mandiri)</option>
                            <option value="E-Wallet" {{ old('payment_method') === 'E-Wallet' ? 'selected' : '' }}>E-Wallet (QRIS / GoPay / OVO)</option>
                            <option value="Tunai" {{ old('payment_method') === 'Tunai' ? 'selected' : '' }}>Tunai di Lokasi</option>
                        </select>
                    </div>

                    <div>
                        <label for="proof_image" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Bukti Pembayaran <span class="text-slate-400 font-normal lowercase">(opsional, bisa menyusul)</span></label>
                        <input type="file" name="proof_image" id="proof_image" accept="image/*"
                            class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Catatan Tambahan</label>
                    <textarea name="notes" id="notes" rows="2" placeholder="Contoh: Pengambilan jam 09.00 WIB atau butuh supir."
                        class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 resize-none">{{ old('notes') }}</textarea>
                </div>

                <div class="pt-2 flex items-center justify-end gap-3">
                    <a href="{{ route('customer.rentals.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors">Batal</a>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md transition-all cursor-pointer">
                        Buat Pesanan Sekarang
                    </button>
                </div>
            </form>
        </div>

        {{-- Ringkasan Estimasi Biaya --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm h-fit">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4">Ringkasan Biaya</h2>
            <div class="space-y-3 text-sm border-b border-slate-100 pb-4">
                <div class="flex justify-between">
                    <span class="text-slate-500">Mobil</span>
                    <span id="summaryCar" class="font-bold text-slate-800 text-right">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Harga / Hari</span>
                    <span id="summaryPrice" class="font-semibold text-slate-700">Rp 0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Durasi Sewa</span>
                    <span id="summaryDays" class="font-semibold text-slate-700">0 hari</span>
                </div>
            </div>
            <div class="pt-4 flex justify-between items-center">
                <span class="text-xs font-bold uppercase text-slate-500">Estimasi Total</span>
                <span id="summaryTotal" class="text-xl font-black text-[#0f2557]">Rp 0</span>
            </div>
            <div class="mt-5 p-3.5 rounded-xl bg-blue-50/70 border border-blue-100 text-xs text-slate-600 space-y-1">
                <p class="font-bold text-blue-800">Info Rekening Transfer:</p>
                <p>BCA: <strong>1234-5678-90</strong> a.n. RentCar Indonesia</p>
                <p>Mandiri: <strong>0987-6543-21</strong> a.n. RentCar Indonesia</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const bookedDates = @json($bookedDates);

    function formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
    }

    function updateEstimate() {
        const carSelect = document.getElementById('car_id');
        const option = carSelect.options[carSelect.selectedIndex];
        const carId = carSelect.value;
        const price = parseFloat(option?.dataset?.price || 0);
        const carName = option?.dataset?.name || '-';

        const startVal = document.getElementById('start_date').value;
        const endVal = document.getElementById('end_date').value;

        let days = 0;
        if (startVal && endVal) {
            const diff = Math.round((new Date(endVal) - new Date(startVal)) / (1000 * 60 * 60 * 24));
            days = diff >= 0 ? Math.max(1, diff) : 0;
        }

        document.getElementById('summaryCar').textContent = carName;
        document.getElementById('summaryPrice').textContent = formatRupiah(price);
        document.getElementById('summaryDays').textContent = days + ' hari';
        document.getElementById('summaryTotal').textContent = formatRupiah(price * days);

        const box = document.getElementById('bookedDatesBox');
        const list = document.getElementById('bookedDatesList');
        list.innerHTML = '';

        if (carId && bookedDates[carId] && bookedDates[carId].length > 0) {
            bookedDates[carId].forEach(item => {
                const li = document.createElement('li');
                li.textContent = `${item.start} s/d ${item.end}`;
                list.appendChild(li);
            });
            box.classList.remove('hidden');
        } else {
            box.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', updateEstimate);
</script>
@endpush
