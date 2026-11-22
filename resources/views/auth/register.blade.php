<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Daftar Akun Baru - RentCar Rental Mobil</title>

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        navy: {
                            800: '#152544',
                            900: '#0f1f3d',
                            950: '#0a1428',
                        }
                    }
                }
            }
        }
    </script>

    {{-- Lucide Icons CDN --}}
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
    </style>
</head>
<body class="min-h-full flex flex-col justify-center py-10 sm:px-6 lg:px-8 bg-slate-50">
    <div class="sm:mx-auto sm:w-full sm:max-w-xl px-4">
        {{-- Logo Brand --}}
        <a href="{{ route('homepage') }}" class="flex items-center justify-center gap-2.5 mb-2 group">
            <div class="w-11 h-11 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform">
                <i data-lucide="car" class="w-6 h-6"></i>
            </div>
            <span class="text-3xl font-black text-[#0f2557] tracking-tight">Rent<span class="text-blue-600">Car</span></span>
        </a>

        <h2 class="mt-4 text-center text-2xl font-black text-slate-900 tracking-tight">
            Buat Akun Customer Baru
        </h2>
        <p class="mt-1 text-center text-sm text-slate-500">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-500 hover:underline">
                Masuk di sini
            </a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-xl px-4">
        {{-- Card Form Register --}}
        <div class="bg-white py-8 px-6 sm:px-10 shadow-xl shadow-slate-200/60 rounded-3xl border border-slate-100">
            {{-- Alert Error Global jika ada --}}
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-700 text-sm flex items-start gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5 text-rose-600"></i>
                    <div>
                        <strong class="font-semibold block mb-0.5">Pendaftaran belum berhasil</strong>
                        <ul class="list-disc list-inside space-y-0.5 text-xs">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Input Nama Lengkap --}}
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </div>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name') }}"
                            required
                            placeholder="Contoh: Budi Santoso"
                            class="block w-full pl-10 pr-3.5 py-2.5 border @error('name') border-rose-300 bg-rose-50/20 @else border-slate-200 @enderror rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all font-medium"
                        >
                    </div>
                    @error('name')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Grid 2 Kolom: Email & No. HP --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Input Email --}}
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Alamat Email <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="mail" class="w-4 h-4"></i>
                            </div>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old('email') }}"
                                required
                                placeholder="nama@email.com"
                                class="block w-full pl-10 pr-3.5 py-2.5 border @error('email') border-rose-300 bg-rose-50/20 @else border-slate-200 @enderror rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all font-medium"
                            >
                        </div>
                        @error('email')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Input Nomor Telepon/WhatsApp --}}
                    <div>
                        <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            No. Telepon / WhatsApp <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="phone" class="w-4 h-4"></i>
                            </div>
                            <input
                                type="tel"
                                name="phone"
                                id="phone"
                                value="{{ old('phone') }}"
                                required
                                placeholder="081234567890"
                                class="block w-full pl-10 pr-3.5 py-2.5 border @error('phone') border-rose-300 bg-rose-50/20 @else border-slate-200 @enderror rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all font-medium"
                            >
                        </div>
                        @error('phone')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Input Alamat --}}
                <div>
                    <label for="address" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Alamat Domisili <span class="text-slate-400 font-normal lowercase">(opsional)</span>
                    </label>
                    <div class="relative">
                        <div class="absolute top-3 left-3.5 flex items-start pointer-events-none text-slate-400">
                            <i data-lucide="map-pin" class="w-4 h-4"></i>
                        </div>
                        <textarea
                            name="address"
                            id="address"
                            rows="2"
                            placeholder="Jl. Merdeka No. 123, Sleman, Yogyakarta"
                            class="block w-full pl-10 pr-3.5 py-2.5 border border-slate-200 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all font-medium resize-none"
                        >{{ old('address') }}</textarea>
                    </div>
                </div>

                {{-- Grid 2 Kolom: Password & Konfirmasi Password --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Input Password --}}
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="lock" class="w-4 h-4"></i>
                            </div>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                required
                                placeholder="Minimal 6 karakter"
                                class="block w-full pl-10 pr-10 py-2.5 border @error('password') border-rose-300 bg-rose-50/20 @else border-slate-200 @enderror rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all font-medium"
                            >
                            <button
                                type="button"
                                onclick="togglePasswordVisibility('password', 'eyeIcon1', 'eyeOffIcon1')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer"
                            >
                                <i data-lucide="eye" id="eyeIcon1" class="w-4 h-4"></i>
                                <i data-lucide="eye-off" id="eyeOffIcon1" class="w-4 h-4 hidden"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Ulangi Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="shield-check" class="w-4 h-4"></i>
                            </div>
                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                required
                                placeholder="Ulangi password di atas"
                                class="block w-full pl-10 pr-10 py-2.5 border border-slate-200 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all font-medium"
                            >
                            <button
                                type="button"
                                onclick="togglePasswordVisibility('password_confirmation', 'eyeIcon2', 'eyeOffIcon2')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer"
                            >
                                <i data-lucide="eye" id="eyeIcon2" class="w-4 h-4"></i>
                                <i data-lucide="eye-off" id="eyeOffIcon2" class="w-4 h-4 hidden"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Ketentuan Layanan --}}
                <div class="pt-2">
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Dengan mendaftar, Anda menyetujui <span class="text-blue-600 font-medium">Ketentuan Layanan</span> dan <span class="text-blue-600 font-medium">Kebijakan Privasi</span> penyewaan mobil RentCar.
                    </p>
                </div>

                {{-- Tombol Submit Daftar --}}
                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full py-3.5 px-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-600/20 hover:shadow-xl transition-all flex items-center justify-center gap-2 cursor-pointer"
                    >
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        <span>Daftar Akun Sekarang</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Kembali ke Beranda --}}
        <div class="mt-6 text-center">
            <a href="{{ route('homepage') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-colors">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Kembali ke Halaman Utama</span>
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        function togglePasswordVisibility(fieldId, iconId, offIconId) {
            const field = document.getElementById(fieldId);
            const eye = document.getElementById(iconId);
            const eyeOff = document.getElementById(offIconId);

            if (field.type === 'password') {
                field.type = 'text';
                eye.classList.add('hidden');
                eyeOff.classList.remove('hidden');
            } else {
                field.type = 'password';
                eye.classList.remove('hidden');
                eyeOff.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
