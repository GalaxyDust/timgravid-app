@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    @php
        $role = Auth::user()->role;
        
        $dashThemes = [
            'super_admin' => [
                'card_bg' => 'bg-slate-900', 
                'accent_badge' => 'text-slate-200 border-slate-400 bg-slate-800', 
                'icon_style' => 'bg-slate-100 text-slate-600 group-hover:bg-slate-600 group-hover:text-white'
            ],
            'admin' => [
                'card_bg' => 'bg-yellow-900',
                'accent_badge' => 'text-yellow-100 border-yellow-400 bg-yellow-800',
                'icon_style' => 'bg-amber-50 text-amber-600 group-hover:bg-amber-600 group-hover:text-white'
            ],
            'approver' => [
                'card_bg' => 'bg-emerald-900',
                'accent_badge' => 'text-emerald-100 border-emerald-400 bg-emerald-800',
                'icon_style' => 'bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white'
            ],
            'pengawas' => [
                'card_bg' => 'bg-sky-900',
                'accent_badge' => 'text-sky-100 border-sky-400 bg-sky-800',
                'icon_style' => 'bg-sky-50 text-sky-600 group-hover:bg-sky-600 group-hover:text-white'
            ]
        ];
        
        $theme = $dashThemes[$role] ?? $dashThemes['super_admin'];
    @endphp

    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 font-serif tracking-tight">Dashboard Utama</h1>
            <p class="text-sm text-slate-500 mt-1">Ringkasan aktivitas sistem pemakaman TIMGRAVID.</p>
        </div>
        <div class="px-4 py-2 bg-white border border-gray-200 rounded-lg shadow-sm text-sm text-gray-600 font-medium">
            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </div>
    </div>

    <!-- WELCOME CARD -->
    <div class="{{ $theme['card_bg'] }} rounded-2xl p-8 md:p-10 text-white shadow-xl mb-10 relative overflow-hidden group">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute -right-10 -top-10 w-64 h-64 bg-white/5 rounded-full blur-3xl group-hover:bg-white/10 transition duration-700"></div>

        <div class="relative z-10">
            <h2 class="text-3xl md:text-4xl font-bold font-serif mb-3 tracking-wide text-white drop-shadow-md">
                Selamat Datang, {{ Auth::user()->name }}!
            </h2>
            <p class="text-white/90 max-w-2xl leading-relaxed text-sm md:text-base font-light">
                Anda login sebagai 
                <span class="inline-block px-3 py-1 rounded text-xs font-bold uppercase tracking-widest border {{ $theme['accent_badge'] }} ml-1 shadow-sm">
                    {{ str_replace('_', ' ', $role) }}
                </span>. 
                Sistem siap digunakan untuk pengelolaan data.
            </p>
        </div>
    </div>

    <!-- BAGIAN NOTIFIKASI (UPDATED) -->
    @if(count($notifications) > 0)
        <div class="mb-10">
            <h3 class="text-lg font-bold text-slate-700 mb-4 flex items-center gap-2">
                <span class="w-2 h-6 bg-red-500 rounded-full"></span>
                Tugas Penting Hari Ini
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($notifications as $notif)
                    @php
                        // Style berdasarkan tipe notifikasi
                        $style = match($notif['type']) {
                            'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800', // Menunggu Verifikasi
                            'danger'  => 'bg-red-50 border-red-200 text-red-800',          // Ditolak
                            'alert'   => 'bg-orange-50 border-orange-200 text-orange-800',  // Belum Bayar (BARU)
                            'info'    => 'bg-blue-50 border-blue-200 text-blue-800',        // Info Iuran
                            default   => 'bg-slate-50 border-slate-200 text-slate-800'
                        };
                        
                        $icon = match($notif['type']) {
                            'warning' => '<svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                            'danger'  => '<svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                            'alert'   => '<svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
                            'info'    => '<svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                            default   => ''
                        };
                    @endphp

                    <div class="{{ $style }} border p-5 rounded-xl shadow-sm flex flex-col justify-between hover:shadow-md transition">
                        <div class="flex gap-4">
                            <div class="shrink-0 bg-white p-2 rounded-lg shadow-sm h-fit">
                                {!! $icon !!}
                            </div>
                            <div>
                                <p class="text-sm leading-relaxed">{!! $notif['message'] !!}</p>
                            </div>
                        </div>
                        <div class="mt-4 text-right">
                            <a href="{{ $notif['link'] }}" class="text-xs font-bold uppercase tracking-wider underline opacity-80 hover:opacity-100 transition">
                                {{ $notif['link_text'] }} &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- STATISTIK (Clean Style) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Stat 1 -->
        <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Pesanan Baru</p>
                    <h3 class="text-3xl font-bold text-slate-800">{{ $stats['orders_this_month'] }}</h3>
                </div>
                <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-colors duration-300 {{ $theme['icon_style'] }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Stat 2 -->
        <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Pendapatan</p>
                    <h3 class="text-3xl font-bold text-slate-800 tracking-tight">Rp {{ number_format($stats['revenue_this_month'], 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-colors duration-300 {{ $theme['icon_style'] }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Stat 3 -->
        <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Menunggu Approval</p>
                    <h3 class="text-3xl font-bold text-slate-800">{{ $stats['pending_approvals'] }}</h3>
                </div>
                <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-colors duration-300 {{ $theme['icon_style'] }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Stat 4 -->
        <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Unit Tersedia</p>
                    <h3 class="text-3xl font-bold text-slate-800">{{ $stats['available_blocks'] }}</h3>
                </div>
                <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-colors duration-300 {{ $theme['icon_style'] }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
            </div>
        </div>
    </div>
@endsection