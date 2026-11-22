<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'RentCar - Rental Mobil Mudah & Nyaman')</title>

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tailwind CSS CDN (Fallback yang menjamin tampilan tetap rapi di semua environment) --}}
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
                        },
                        brand: {
                            50: '#f0f5ff',
                            100: '#e5edff',
                            500: '#2563eb',
                            600: '#1d4ed8',
                            700: '#1e40af',
                        }
                    }
                }
            }
        }
    </script>

    {{-- Lucide Icons CDN --}}
    <script src="https://unpkg.com/lucide@latest"></script>

    {{-- Vite Assets jika tersedia --}}
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-[#F8FAFC] text-slate-800 flex flex-col min-h-screen">
    {{-- Navbar --}}
    @include('components.navbar')

    {{-- Flash Notifications (Sukses / Error) --}}
    @if (session('success'))
        <div id="flashSuccessAlert" class="bg-emerald-600 text-white px-4 py-3 shadow-md transition-all">
            <div class="max-w-7xl mx-auto flex items-center justify-between text-sm font-medium">
                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="document.getElementById('flashSuccessAlert').remove()" class="text-white/80 hover:text-white">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div id="flashErrorAlert" class="bg-rose-600 text-white px-4 py-3 shadow-md transition-all">
            <div class="max-w-7xl mx-auto flex items-center justify-between text-sm font-medium">
                <div class="flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="document.getElementById('flashErrorAlert').remove()" class="text-white/80 hover:text-white">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('components.footer')

    @stack('scripts')

    <script>
        // Inisialisasi ikon Lucide
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
