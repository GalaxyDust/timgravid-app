@extends('layouts.admin')

@section('title', 'Data Pesanan')

@section('content')
    <!-- Header Halaman -->
    <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 font-serif">Data Pesanan</h1>
            <p class="text-sm text-slate-500 mt-1">Daftar semua pesanan makam yang telah terdaftar.</p>
        </div>
        
        <a href="{{ route('orders.create') }}" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-lg font-bold text-sm transition shadow-lg flex items-center gap-2 transform hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Input Pesanan Baru
        </a>
    </div>

    <!-- Area Konten (Dibungkus Alpine.js) -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden" x-data="tableActions()">
        
        <!-- Header Card: Search & Filter -->
        <div class="p-6 border-b border-slate-200">
            <form action="{{ route('orders.index') }}" method="GET" id="searchForm" class="flex flex-col sm:flex-row gap-4">
                
                <!-- Search Input -->
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="search" name="search" id="searchInput" value="{{ $search ?? '' }}"
                           class="block w-full pl-10 pr-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-slate-500 transition sm:text-sm bg-slate-50"
                           placeholder="Cari nama jenazah, NIK, atau ID blok...">
                </div>

                <!-- [BARU] Filter Status Pembayaran -->
                <div class="relative">
                    <select name="payment_status" onchange="this.form.submit()" class="block w-full sm:w-64 appearance-none pl-4 pr-10 py-2.5 border border-slate-300 rounded-lg bg-slate-50 focus:ring-2 focus:ring-slate-500 text-sm font-medium text-slate-700">
                        <option value="">-- Semua Status Pembayaran --</option>
                        <option value="unpaid" {{ ($paymentStatus ?? '') == 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                        <option value="waiting_approval" {{ ($paymentStatus ?? '') == 'waiting_approval' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="rejected" {{ ($paymentStatus ?? '') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        <option value="paid" {{ ($paymentStatus ?? '') == 'paid' ? 'selected' : '' }}>Lunas</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>

            </form>
        </div>

        <!-- Tabel Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-4 py-3 w-12 text-center"><input type="checkbox" @click="toggleAll($event.target.checked)" class="rounded border-gray-300"></th>
                        <th class="px-6 py-3 text-left font-semibold">ID</th>
                        <th class="px-6 py-3 text-left font-semibold">Jenazah</th>
                        <th class="px-6 py-3 text-left font-semibold">Penanggung Jawab</th>
                        <th class="px-6 py-3 text-left font-semibold">Lokasi</th>
                        <th class="px-6 py-3 text-left font-semibold">Tgl Input</th>
                        <th class="px-6 py-3 text-left font-semibold">Status</th>
                        <th class="px-6 py-3 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($orders as $order)
                        <!-- [BARU] Highlighting Baris dengan Tagihan Tertunda -->
                        <tr class="transition {{ $order->has_pending_invoice ? 'bg-amber-50 hover:bg-amber-100 border-l-4 border-amber-400' : 'hover:bg-slate-50' }}">
                            <td class="px-4 py-4 text-center"><input type="checkbox" :value="{{ $order->id }}" x-model="selectedItems" class="rounded border-gray-300"></td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono font-bold text-slate-700">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-800">{{ $order->deceased_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-slate-700">{{ $order->customer->name ?? 'N/A' }}</div>
                                <div class="text-xs text-slate-500">{{ $order->customer->nik ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-slate-700">{{ $order->block->location->name ?? 'N/A' }}</div>
                                <div class="text-xs text-slate-500 font-mono">{{ $order->block_id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-600">{{ $order->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($order->status == 'active')
                                    <span class="px-2.5 py-1 inline-flex text-xs font-bold rounded-full bg-emerald-100 text-emerald-800">Aktif</span>
                                @else
                                    <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full bg-slate-100 text-slate-600">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('orders.show', $order) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-16 text-slate-500 italic">
                                <p class="mt-4 font-medium">Tidak ada data pesanan ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginasi -->
        @if($orders->hasPages())
            <div class="p-6 border-t border-slate-200 bg-slate-50/50">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <!-- SCRIPT ALPINE.JS & PENCARIAN OTOMATIS -->
    <script>
        function tableActions() {
            return {
                selectedItems: [],
                toggleAll(checked) {
                    this.selectedItems = checked ? Array.from(document.querySelectorAll('input[x-model="selectedItems"]')).map(el => el.value) : [];
                },
                confirmDelete() { alert('Fitur hapus massal belum terhubung.'); }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchForm = document.getElementById('searchForm');
            let typingTimer;
            const doneTypingInterval = 500; // 0.5 detik

            if (searchInput) {
                searchInput.addEventListener('keyup', () => {
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(() => {
                        searchForm.submit();
                    }, doneTypingInterval);
                });
            }
        });
    </script>
@endsection