<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - RentCar</title>

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

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800 min-h-screen flex flex-col">
    {{-- Top Navigation Bar --}}
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                {{-- Logo --}}
                <a href="{{ route('homepage') }}" class="flex items-center gap-2 group">
                    <div class="w-9 h-9 rounded-xl bg-blue-600/10 flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <i data-lucide="car" class="w-5 h-5"></i>
                    </div>
                    <span class="text-xl font-extrabold text-[#0f2557] tracking-tight">Rent<span class="text-blue-600">Car</span></span>
                </a>

                {{-- Breadcrumbs / Section Title --}}
                <div class="hidden sm:flex items-center gap-2 text-sm">
                    <a href="{{ route('homepage') }}" class="text-slate-400 hover:text-blue-600 transition-colors flex items-center gap-1">
                        <i data-lucide="home" class="w-3.5 h-3.5"></i>
                        Beranda
                    </a>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-300"></i>
                    <span class="text-slate-700 font-semibold">@yield('page-title', 'Dashboard')</span>
                </div>

                {{-- User Info & Actions --}}
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2.5 text-right">
                        <div>
                            <span class="block text-xs font-bold text-slate-800 leading-tight">{{ Auth::user()->name }}</span>
                            <span class="inline-block text-[10px] font-semibold uppercase px-1.5 py-0.5 rounded {{ Auth::user()->isAdmin() ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ Auth::user()->role }}
                            </span>
                        </div>
                        <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" title="Keluar Akun" class="w-9 h-9 flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash Notifications --}}
    @if (session('success'))
        <div id="flashSuccess" class="bg-emerald-600 text-white px-4 py-3 shadow-md">
            <div class="max-w-7xl mx-auto flex items-center justify-between text-sm font-medium">
                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="document.getElementById('flashSuccess').remove()" class="text-white/80 hover:text-white">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div id="flashError" class="bg-rose-600 text-white px-4 py-3 shadow-md">
            <div class="max-w-7xl mx-auto flex items-center justify-between text-sm font-medium">
                <div class="flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="document.getElementById('flashError').remove()" class="text-white/80 hover:text-white">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full">
        @yield('content')
    </main>

    {{-- Footer Mini --}}
    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-400 mt-auto">
        <p>&copy; {{ date('Y') }} RentCar - Sistem Manajemen Rental Mobil. All rights reserved.</p>
    </footer>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
