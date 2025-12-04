<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - TIMGRAVID</title>
    
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Cleave.js (Format Angka) -->
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>

    <!-- Chart.js (Grafik) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gray-100 font-sans text-slate-800" x-data="{ sidebarOpen: false, loading: false }">
    
    <!-- MODAL LOADING (Untuk Logout) -->
    <div x-show="loading" class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/70 backdrop-blur-sm" x-cloak>
        <div class="flex flex-col items-center text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-4 border-slate-300 border-t-white mb-4"></div>
            <p class="text-white font-bold text-lg">Logging out...</p>
            <p class="text-slate-300 text-sm">Please wait a moment.</p>
        </div>
    </div>
    
    @php
        $role = Auth::user()->role;
        $themes = [
            'super_admin' => ['sidebar_bg' => 'bg-slate-900', 'sidebar_border' => 'border-slate-800', 'menu_hover' => 'hover:bg-slate-800', 'menu_active' => 'bg-slate-800 text-white shadow-md border-l-4 border-slate-400', 'text_sub' => 'text-slate-400'],
            'admin'       => ['sidebar_bg' => 'bg-yellow-950', 'sidebar_border' => 'border-yellow-900', 'menu_hover' => 'hover:bg-yellow-900', 'menu_active' => 'bg-yellow-900 text-white shadow-md border-l-4 border-yellow-500', 'text_sub' => 'text-yellow-500/80'],
            'approver'    => ['sidebar_bg' => 'bg-emerald-950', 'sidebar_border' => 'border-emerald-900', 'menu_hover' => 'hover:bg-emerald-900', 'menu_active' => 'bg-emerald-900 text-white shadow-md border-l-4 border-emerald-500', 'text_sub' => 'text-emerald-500/80'],
            'pengawas'    => ['sidebar_bg' => 'bg-sky-950', 'sidebar_border' => 'border-sky-900', 'menu_hover' => 'hover:bg-sky-900', 'menu_active' => 'bg-sky-900 text-white shadow-md border-l-4 border-sky-500', 'text_sub' => 'text-sky-500/80'],
        ];
        $currentTheme = $themes[$role] ?? $themes['super_admin'];
    @endphp

    <div class="relative min-h-screen lg:flex">
        
        <!-- SIDEBAR -->
        <aside :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }" class="w-64 {{ $currentTheme['sidebar_bg'] }} text-white flex-col fixed inset-y-0 left-0 transform lg:translate-x-0 transition-transform duration-300 z-30 shadow-2xl flex">
            
            <!-- Logo -->
            <div class="h-20 flex items-center justify-center border-b {{ $currentTheme['sidebar_border'] }} bg-black/20 shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('images/logo-white.png') }}" alt="Logo" class="h-9 w-auto opacity-90 group-hover:scale-105 transition">
                    <div class="flex flex-col"><span class="text-lg font-bold tracking-widest text-white/90">ADMIN</span><span class="text-[10px] uppercase tracking-wide {{ $currentTheme['text_sub'] }}">Panel</span></div>
                </a>
            </div>

            <!-- Menu -->
            <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? $currentTheme['menu_active'] : 'text-white/70 ' . $currentTheme['menu_hover'] . ' hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Dashboard
                </a>
                
                @if(in_array(Auth::user()->role, ['admin', 'super_admin']))
                    <div class="pt-6 pb-2 px-4 text-xs font-bold uppercase tracking-wider {{ $currentTheme['text_sub'] }}">Operasional</div>
                    <a href="{{ route('orders.create') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('orders.create') ? $currentTheme['menu_active'] : 'text-white/70 ' . $currentTheme['menu_hover'] . ' hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Input Pesanan
                    </a>
                    <a href="{{ route('orders.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('orders.index') || request()->routeIs('orders.show') ? $currentTheme['menu_active'] : 'text-white/70 ' . $currentTheme['menu_hover'] . ' hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Data Pesanan
                    </a>
                    
                    <div class="pt-6 pb-2 px-4 text-xs font-bold uppercase tracking-wider {{ $currentTheme['text_sub'] }}">Master Data</div>
                    <a href="{{ route('blocks.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('blocks.*') ? $currentTheme['menu_active'] : 'text-white/70 ' . $currentTheme['menu_hover'] . ' hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Blok Makam
                    </a>
                    <a href="{{ route('articles.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('articles.*') ? $currentTheme['menu_active'] : 'text-white/70 ' . $currentTheme['menu_hover'] . ' hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Artikel Sejarah
                    </a>
                @endif

                @if(in_array(Auth::user()->role, ['approver', 'super_admin']))
                    <div class="pt-6 pb-2 px-4 text-xs font-bold uppercase tracking-wider {{ $currentTheme['text_sub'] }}">Keuangan</div>
                    <a href="{{ route('invoices.approval') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('invoices.*') ? $currentTheme['menu_active'] : 'text-white/70 ' . $currentTheme['menu_hover'] . ' hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Verifikasi Tagihan
                    </a>
                @endif
                
                @if(in_array(Auth::user()->role, ['pengawas', 'super_admin']))
                    <div class="pt-6 pb-2 px-4 text-xs font-bold uppercase tracking-wider {{ $currentTheme['text_sub'] }}">Laporan</div>
                    <a href="{{ route('reports.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('reports.*') ? $currentTheme['menu_active'] : 'text-white/70 ' . $currentTheme['menu_hover'] . ' hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        Statistik
                    </a>
                @endif

                @if(in_array(Auth::user()->role, ['admin', 'super_admin']))
                    <div class="pt-6 pb-2 px-4 text-xs font-bold uppercase tracking-wider {{ $currentTheme['text_sub'] }}">Sistem</div>
                    <a href="{{ route('settings.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('settings.*') ? $currentTheme['menu_active'] : 'text-white/70 ' . $currentTheme['menu_hover'] . ' hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Pengaturan
                    </a>
                @endif
            </nav>

            <!-- Profil & Logout -->
            <div class="p-4 border-t {{ $currentTheme['sidebar_border'] }} bg-black/10 mt-auto">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white font-bold border border-white/20 shrink-0">{{ substr(Auth::user()->name ?? 'A', 0, 1) }}</div>
                    <div class="ml-3 overflow-hidden"><p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p><p class="text-[10px] text-white/60 uppercase tracking-wider">{{ str_replace('_', ' ', Auth::user()->role) }}</p></div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            @click="loading = true"
                            class="w-full flex items-center justify-center px-4 py-2 text-xs font-bold text-red-200 bg-red-900/30 border border-red-500/30 hover:bg-red-600 hover:text-white hover:border-red-500 rounded-md transition duration-200">
                        LOGOUT
                    </button>
                </form>
            </div>
        </aside>

        <!-- KONTEN UTAMA -->
        <div class="flex-1 flex flex-col lg:ml-64 transition-all duration-300">
            <header class="bg-white/80 backdrop-blur-md shadow-sm h-20 flex items-center justify-between lg:justify-end px-6 border-b z-20 sticky top-0">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-slate-500 hover:text-slate-800 focus:outline-none p-2 rounded-full hover:bg-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                </button>
                <div class="text-right text-sm font-medium text-slate-600">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</div>
            </header>
            <main class="flex-1 p-6 md:p-8 overflow-y-auto">
                @if(session('success'))
                    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 shadow-sm rounded-r-md animate-fade-in-down">
                        <p class="text-sm text-green-700 font-medium flex items-center">
                            <svg class="w-5 h-5 mr-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>{{ session('success') }}</span>
                        </p>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>

        <!-- Overlay Mobile -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/60 z-20 lg:hidden" x-cloak></div>
    </div>
</body>
</html>