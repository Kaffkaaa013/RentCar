{{-- Footer - Sistem Manajemen Rental Mobil sesuai tema navy & profesional --}}
<footer class="bg-[#0f1f3d] text-slate-300 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
            {{-- Kolom 1: Profil Brand --}}
            <div class="space-y-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white">
                        <i data-lucide="car" class="w-5 h-5"></i>
                    </div>
                    <span class="text-2xl font-extrabold text-white tracking-tight">Rent<span class="text-blue-400">Car</span></span>
                </div>
                <p class="text-sm text-slate-400 leading-relaxed">
                    Layanan rental mobil terpercaya untuk kebutuhan pribadi, bisnis, maupun wisata. Kendaraan prima dengan harga terbaik dan transparan.
                </p>
                <div class="flex items-center gap-3 pt-2">
                    <a href="#" class="w-9 h-9 rounded-lg bg-slate-800/80 hover:bg-blue-600 text-slate-400 hover:text-white flex items-center justify-center transition-colors" aria-label="Facebook">
                        <i data-lucide="facebook" class="w-4 h-4"></i>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-lg bg-slate-800/80 hover:bg-blue-600 text-slate-400 hover:text-white flex items-center justify-center transition-colors" aria-label="Instagram">
                        <i data-lucide="instagram" class="w-4 h-4"></i>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-lg bg-slate-800/80 hover:bg-blue-600 text-slate-400 hover:text-white flex items-center justify-center transition-colors" aria-label="Twitter">
                        <i data-lucide="twitter" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

            {{-- Kolom 2: Navigasi Cepat --}}
            <div>
                <h4 class="text-white font-semibold text-base mb-4 tracking-wide">Navigasi</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('homepage') }}" class="hover:text-white transition-colors">Beranda</a></li>
                    <li><a href="{{ route('homepage') }}#katalog" class="hover:text-white transition-colors">Daftar Mobil</a></li>
                    <li><a href="{{ route('homepage') }}#keunggulan" class="hover:text-white transition-colors">Keunggulan Layanan</a></li>
                    <li><a href="{{ route('homepage') }}#tentang" class="hover:text-white transition-colors">Tentang Kami</a></li>
                    <li><a href="{{ route('homepage') }}#kontak" class="hover:text-white transition-colors">Hubungi Kami</a></li>
                </ul>
            </div>

            {{-- Kolom 3: Layanan Kami --}}
            <div>
                <h4 class="text-white font-semibold text-base mb-4 tracking-wide">Layanan</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('homepage') }}#katalog" class="hover:text-white transition-colors">Sewa Mobil Harian</a></li>
                    <li><a href="{{ route('homepage') }}#katalog" class="hover:text-white transition-colors">Sewa Mobil Mingguan / Bulanan</a></li>
                    <li><a href="{{ route('homepage') }}#katalog" class="hover:text-white transition-colors">Sewa Lepas Kunci</a></li>
                    <li><a href="{{ route('homepage') }}#katalog" class="hover:text-white transition-colors">Sewa dengan Driver</a></li>
                    <li><a href="{{ route('homepage') }}#katalog" class="hover:text-white transition-colors">Paket Perjalanan Wisata</a></li>
                </ul>
            </div>

            {{-- Kolom 4: Kontak Placeholder (Mudah diganti oleh kelompok) --}}
            <div>
                <h4 class="text-white font-semibold text-base mb-4 tracking-wide">Kontak Kami</h4>
                <ul class="space-y-3 text-sm text-slate-400">
                    <li class="flex items-start gap-3">
                        <i data-lucide="map-pin" class="w-4 h-4 text-blue-400 mt-1 shrink-0"></i>
                        <span>Jl. Sudirman No. 45, Jakarta Pusat, Indonesia</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i data-lucide="phone" class="w-4 h-4 text-blue-400 shrink-0"></i>
                        <span>+62 812-3456-7890</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i data-lucide="mail" class="w-4 h-4 text-blue-400 shrink-0"></i>
                        <span>info@rentcar.com</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i data-lucide="clock" class="w-4 h-4 text-blue-400 shrink-0"></i>
                        <span>Setiap Hari (24 Jam)</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Baris Hak Cipta & Kebijakan --}}
        <div class="mt-12 pt-8 border-t border-slate-800/80 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-slate-500">
            <p>&copy; {{ date('Y') }} RentCar - Sistem Manajemen Rental Mobil. Hak Cipta Dilindungi. Proyek PBWL Kelompok 2.</p>
            <div class="flex gap-6">
                <a href="#" class="hover:text-slate-400 transition-colors">Kebijakan Privasi</a>
                <a href="#" class="hover:text-slate-400 transition-colors">Syarat & Ketentuan</a>
                <a href="#" class="hover:text-slate-400 transition-colors">Bantuan</a>
            </div>
        </div>
    </div>
</footer>
