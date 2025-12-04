@extends('layouts.admin')

@section('title', 'Statistik & Laporan')

@section('content')
    @php
        $role = Auth::user()->role;
        $theme = match ($role) {
            'super_admin' => 'slate', 'admin' => 'amber', 'approver' => 'emerald', 'pengawas' => 'sky', default => 'slate',
        };
    @endphp

    <!-- Header & Filter Tanggal -->
    <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 font-serif">Statistik & Laporan</h1>
            <p class="text-sm text-slate-500 mt-1">Ringkasan data operasional dan keuangan.</p>
        </div>
        
        <div class="flex items-center gap-2">
            <!-- Filter Tanggal -->
            <form action="{{ route('reports.index') }}" method="GET" class="flex items-center gap-2 bg-white p-2 rounded-lg border border-slate-200 shadow-sm">
                <input type="date" name="start_date" value="{{ $startDate }}" class="border-slate-200 rounded-md text-sm focus:ring-slate-500 focus:border-slate-500">
                <span class="text-slate-400 font-bold">-</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="border-slate-200 rounded-md text-sm focus:ring-slate-500 focus:border-slate-500">
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white font-bold text-xs rounded-md hover:bg-slate-900 transition shadow-md">Filter</button>
            </form>

            <!-- Tombol Export CSV -->
            <a href="{{ route('reports.export_csv', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
               class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2.5 rounded-lg transition shadow-md"
               title="Export data pesanan sesuai rentang tanggal yang dipilih">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export CSV
            </a>
        </div>
    </div>

    <div class="space-y-8">
        
        <!-- BAGIAN 1: KPI CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Pesanan -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between animate-fade-in-up">
                <div>
                    <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total Pesanan</p>
                    <h3 class="text-4xl font-bold text-slate-800 mt-1">{{ number_format($totalOrders) }}</h3>
                </div>
                <div class="w-14 h-14 bg-slate-100 rounded-full flex items-center justify-center text-slate-500">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
            </div>
            
            <!-- Blok Tersedia -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between animate-fade-in-up delay-100">
                <div>
                    <p class="text-sm font-bold text-emerald-600 uppercase tracking-wider">Blok Tersedia</p>
                    <h3 class="text-4xl font-bold text-slate-800 mt-1">{{ number_format($availableBlocks) }}</h3>
                </div>
                <div class="w-14 h-14 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
            </div>
            
            <!-- Persentase Keterisian -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between animate-fade-in-up delay-200">
                <div>
                    <p class="text-sm font-bold text-amber-600 uppercase tracking-wider">Okupansi Lahan</p>
                    <h3 class="text-4xl font-bold text-slate-800 mt-1">{{ $occupancyRate }}<span class="text-2xl text-slate-400">%</span></h3>
                </div>
                <div class="w-14 h-14 bg-amber-100 rounded-full flex items-center justify-center text-amber-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>
            
            <!-- Total Pendapatan -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between animate-fade-in-up delay-300">
                <div>
                    <p class="text-sm font-bold text-blue-600 uppercase tracking-wider">Pendapatan</p>
                    <h3 class="text-4xl font-bold text-slate-800 mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- BAGIAN 2: GRAFIK-GRAFIK -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
            <!-- Grafik Tren Pesanan -->
            <div class="lg:col-span-3 bg-white p-8 rounded-2xl border border-slate-200 shadow-sm animate-fade-in-up delay-300">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Tren Pesanan Baru (12 Bulan Terakhir)</h3>
                <div class="h-80">
                    <canvas id="ordersChart"></canvas>
                </div>
            </div>

            <!-- Grafik Komposisi Lahan -->
            <div class="lg:col-span-2 bg-white p-8 rounded-2xl border border-slate-200 shadow-sm animate-fade-in-up delay-400">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Komposisi Status Lahan</h3>
                <div class="h-80 flex items-center justify-center">
                    <canvas id="blocksChart"></canvas>
                </div>
            </div>
        </div>

        <!-- BAGIAN 3: TABEL TRANSAKSI TERAKHIR -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden animate-fade-in-up delay-500">
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-800">5 Transaksi Lunas Terakhir</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Tgl Lunas</th><th class="px-6 py-3">Nominal</th>
                            <th class="px-6 py-3">Info Pesanan</th><th class="px-6 py-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentTransactions as $trx)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4">{{ $trx->paid_at->format('d M Y') }}</td>
                                <td class="px-6 py-4 font-bold text-emerald-600">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800">{{ $trx->order->deceased_name }}</div>
                                    <div class="text-xs text-slate-500">PJ: {{ $trx->order->customer->name }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-500 italic">{{ $trx->description }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-10 text-slate-400">Belum ada transaksi lunas pada rentang waktu ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SCRIPT UNTUK INISIALISASI GRAFIK -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const orderLabels = @json($orderLabels);
            const orderData = @json($orderData);
            const blockLabels = @json($blockLabels);
            const blockData = @json($blockData);
            
            // 1. Grafik Pesanan
            const ctxOrders = document.getElementById('ordersChart').getContext('2d');
            new Chart(ctxOrders, {
                type: 'bar',
                data: {
                    labels: orderLabels,
                    datasets: [{
                        label: 'Jumlah Pesanan',
                        data: orderData,
                        backgroundColor: 'rgba(30, 41, 59, 0.8)', // slate-800
                        borderColor: 'rgba(30, 41, 59, 1)',
                        borderWidth: 1,
                        borderRadius: 5
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
            });

            // 2. Grafik Lahan
            const ctxBlocks = document.getElementById('blocksChart').getContext('2d');
            new Chart(ctxBlocks, {
                type: 'doughnut',
                data: {
                    labels: blockLabels,
                    datasets: [{
                        label: 'Jumlah Blok',
                        data: blockData,
                        backgroundColor: [
                            '#10b981', // Hijau (Available)
                            '#f59e0b', // Kuning (Reserved)
                            '#64748b', // Abu (Occupied 1)
                            '#475569', // Abu Sedang (Occupied 2)
                            '#1e293b', // Abu Gelap (Occupied 3)
                        ],
                        borderColor: '#fff',
                        borderWidth: 4
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        });
    </script>
@endsection