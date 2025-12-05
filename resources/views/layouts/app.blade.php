<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TIMGRAVID - @yield('title', 'Sistem Informasi Pemakaman')</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream text-text-dark flex flex-col min-h-screen font-sans">

    <!-- NAVBAR -->
    <header id="main-navbar" class="fixed w-full top-0 z-50 transition-all duration-300 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 md:h-20">
                
                <!-- Logo -->
                <a href="{{ route('landing') }}" class="flex items-center gap-2 group">
                    <img id="navbar-logo" 
                         src="{{ asset('images/logo-white.png') }}" 
                         data-white="{{ asset('Images/logo-white.png') }}"
                         data-green="{{ asset('Images/logo-green.png') }}"
                         alt="Logo" class="h-12 w-auto transform transition group-hover:scale-105 drop-shadow-md">
                    <span id="logo-text" class="text-2xl font-bold text-white font-serif tracking-wide drop-shadow-md hidden md:block">TIMGRAVID</span>
                </a>

                <!-- Menu Desktop -->
                <nav class="hidden md:flex space-x-8">
                    <a href="{{ route('landing') }}" class="nav-link-item text-white hover:text-accent font-medium transition text-sm uppercase tracking-wider drop-shadow-sm">Beranda</a>
                    <a href="{{ route('landing') }}#peta" class="nav-link-item text-white hover:text-accent font-medium transition text-sm uppercase tracking-wider drop-shadow-sm">Cek Lokasi</a>
                    <a href="{{ route('landing') }}#sejarah" class="nav-link-item text-white hover:text-accent font-medium transition text-sm uppercase tracking-wider drop-shadow-sm">Sejarah</a>
                </nav>

                <!-- Tombol Login -->
                <div class="hidden md:block">
                    <a href="{{ route('login') }}" class="bg-accent hover:bg-yellow-600 text-white px-7 py-2.5 rounded-full font-medium transition shadow-lg hover:shadow-accent/50 flex items-center text-sm transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        Login Staff
                    </a>
                </div>

                <!-- Burger Mobile -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-accent hover:text-white focus:outline-none p-2 bg-black/20 rounded-md backdrop-blur-sm transition">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div id="mobile-menu" class="hidden md:hidden bg-primary-gradient backdrop-blur-md border-t border-white/10 shadow-xl absolute w-full left-0 top-full transition-all z-40">
            <div class="px-4 pt-4 pb-6 space-y-3">
                <a href="{{ route('landing') }}" class="block px-3 py-2 rounded-md text-base font-medium text-cream hover:bg-white/10 transition">Beranda</a>
                <a href="{{ route('landing') }}#peta" class="block px-3 py-2 rounded-md text-base font-medium text-cream hover:bg-white/10 transition">Cek Lokasi</a>
                <a href="{{ route('landing') }}#sejarah" class="block px-3 py-2 rounded-md text-base font-medium text-cream hover:bg-white/10 transition">Arsip Sejarah</a>
                <a href="{{ route('login') }}" class="w-full flex items-center justify-center px-4 py-3 mt-4 border border-accent rounded-lg text-base font-medium text-accent hover:bg-accent hover:text-white transition">Login Staff Area</a>
            </div>
        </div>
    </header>

    <!-- KONTEN UTAMA -->
    <main class="flex-grow">
        
        <!-- NOTIFIKASI CERDAS (Hanya jika login & bukan Super Admin) -->
        @auth
            @if(Auth::user()->role !== 'super_admin')
                @php
                    // Logika untuk mengambil notifikasi relevan berdasarkan role
                    $notification = null;
                    if (Auth::user()->can('is-approver')) {
                        $count = \App\Models\Invoice::where('status', 'waiting_approval')->count();
                        if ($count > 0) {
                            $notification = [
                                'message' => "Ada <strong>{$count}</strong> pembayaran menunggu persetujuan Anda.",
                                'route' => route('admin.invoices.approval'),
                                'color' => 'yellow'
                            ];
                        }
                    }
                    // Tambahkan logika notifikasi lain untuk role 'admin' atau 'pengawas' di sini
                @endphp
            
                @if($notification)
                    <div class="bg-{{ $notification['color'] }}-100 border-b-2 border-{{ $notification['color'] }}-300 fixed top-0 w-full z-40 mt-20 md:mt-[104px]">
                        <div class="max-w-7xl mx-auto py-2.5 px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">
                            <p class="text-sm font-medium text-{{ $notification['color'] }}-800">
                                {!! $notification['message'] !!}
                            </p>
                            <a href="{{ $notification['route'] }}" class="text-sm font-bold text-{{ $notification['color'] }}-900 hover:underline shrink-0">
                                Lihat Sekarang &rarr;
                            </a>
                        </div>
                    </div>
                @endif
            @endif
        @endauth

        @yield('content')
    </main>

    <!-- FOOTER (LINK AKTIF: MAPS & WA) -->
    <footer class="bg-primary text-white py-16 relative overflow-hidden">
        <!-- Dekorasi Background -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-accent/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-emerald-500/5 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>

        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-8">
                
                <!-- Kolom 1: Brand & Intro -->
                <div class="lg:col-span-2 space-y-6 text-center md:text-left">
                    <div class="flex items-center justify-center md:justify-start gap-3 mb-2">
                        <img src="{{ asset('images/logo-white.png') }}" alt="Logo" class="h-10 w-auto">
                        <div class="flex flex-col items-start">
                            <span class="text-xl font-bold tracking-widest font-serif text-cream">TIMGRAVID</span>
                            <span class="text-[10px] tracking-[0.3em] text-accent uppercase font-bold">EST. 2025</span>
                        </div>
                    </div>
                    <p class="text-slate-300 text-sm leading-relaxed max-w-md mx-auto md:mx-0 border-l-0 md:border-l-2 border-accent pl-0 md:pl-4">
                        Melestarikan sejarah melalui digitalisasi. Layanan pemakaman yang amanah, transparan, dan menghormati nilai-nilai leluhur Timbanganten.
                    </p>
                </div>

                <!-- Kolom 2: Kontak (LINK AKTIF) -->
                <div class="space-y-6 text-center md:text-left">
                    <h3 class="text-accent font-bold tracking-widest uppercase text-sm border-b border-white/10 pb-2 inline-block">
                        Hubungi Kami
                    </h3>
                    <ul class="space-y-4 text-sm text-slate-300">
                        
                        <!-- Alamat (Link ke Google Maps) -->
                        <li>
                            <a href="https://www.google.com/maps/search/?api=1&query=Jl.+Dalem+Kaum+No.+12,+Bandung" target="_blank" rel="noopener noreferrer" 
                               class="flex flex-col md:flex-row items-center md:items-start gap-3 md:gap-4 group hover:opacity-90 transition">
                                <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center group-hover:bg-accent group-hover:text-primary transition shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <span class="leading-relaxed group-hover:text-white transition decoration-accent/50 group-hover:underline decoration-1 underline-offset-4">
                                    Jl. Dalem Kaum No. 12,<br>Bandung, Jawa Barat
                                </span>
                            </a>
                        </li>

                        <!-- Telepon (Link ke WhatsApp) -->
                        <li>
                            <a href="https://wa.me/6281234567890" target="_blank" rel="noopener noreferrer"
                               class="flex flex-col md:flex-row items-center md:items-start gap-3 md:gap-4 group hover:opacity-90 transition">
                                <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center group-hover:bg-accent group-hover:text-primary transition shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </div>
                                <span class="group-hover:text-white transition decoration-accent/50 group-hover:underline decoration-1 underline-offset-4">
                                    +62 812-3456-7890
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Kolom 3: Jam Layanan -->
                <div class="space-y-6 text-center md:text-left">
                    <h3 class="text-accent font-bold tracking-widest uppercase text-sm border-b border-white/10 pb-2 inline-block">
                        Jam Layanan
                    </h3>
                    <ul class="space-y-3 text-sm text-slate-300 max-w-xs mx-auto md:mx-0">
                        <li class="flex justify-between items-center border-b border-dashed border-white/10 pb-2 hover:text-white transition">
                            <span>Senin - Jumat</span>
                            <span class="font-bold text-white">08.00 - 16.00</span>
                        </li>
                        <li class="flex justify-between items-center border-b border-dashed border-white/10 pb-2 hover:text-white transition">
                            <span>Sabtu - Minggu</span>
                            <span class="font-bold text-white">08.00 - 14.00</span>
                        </li>
                    </ul>
                </div>

            </div>

            <!-- Copyright -->
            <div class="border-t border-white/10 mt-16 pt-8 text-center">
                <p class="text-xs text-slate-500">
                    &copy; {{ date('Y') }} Yayasan Sajarah Timbanganten. Hak Cipta Dilindungi.
                </p>
            </div>
        </div>
    </footer>

</body>
</html>