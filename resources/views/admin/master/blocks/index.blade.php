@extends('layouts.admin')

@section('title', 'Manajemen Blok Makam')

@section('content')
<style>
    /* --- CSS Grid & Tampilan Nisan --- */
    .map-grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); 
        gap: 16px;
        padding: 24px;
        align-content: start;
        background-color: #fff;
        border-radius: 16px;
    }

    .map-block {
        aspect-ratio: 1.3 / 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border: 3px solid transparent;
        border-top-left-radius: 60px;
        border-top-right-radius: 60px;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        position: relative;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .map-block:hover {
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.15);
        z-index: 10;
    }

    /* --- Warna Status (SOLID FILL) --- */
    .status-available { background-color: #d1fae5; color: #065f46; border-color: #34d399; }
    .status-reserved { background-color: #fef3c7; color: #92400e; border-color: #fbbf24; }
    .status-occupied_1 { background-color: #f1f5f9; color: #475569; border-color: #cbd5e1; }
    .status-occupied_2 { background-color: #cbd5e1; color: #334155; border-color: #94a3b8; }
    .status-occupied_3 { background-color: #64748b; color: #ffffff; border-color: #475569; }

    /* --- Tombol Hapus (GLOWING) --- */
    .btn-danger-glow {
        background-color: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; transition: all 0.3s ease;
    }
    .btn-danger-glow:hover {
        background-color: #e11d48; color: white; border-color: #e11d48; 
        box-shadow: 0 0 15px rgba(225, 29, 72, 0.6); transform: scale(1.02);
    }

    /* --- Utilitas --- */
    .badge-solid { padding: 4px 12px; border-radius: 99px; font-size: 12px; font-weight: bold; color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .legend-box { width: 20px; height: 20px; border-radius: 6px; border-width: 2px; }
    .loading-overlay { position: absolute; inset: 0; background: rgba(255,255,255,0.85); backdrop-filter: blur(4px); z-index: 40; display: flex; flex-direction: column; justify-content: center; align-items: center; }
</style>

<!-- MAIN ALPINE COMPONENT -->
<div x-data="{ 
    showAddModal: false, 
    showDetailModal: false,
    isLoading: false, 
    
    // Data Detail Blok
    activeBlock: { id: '', status: '', location: '', burial_date: null, orders: [] },
    
    // Data Input Tambah Blok
    newBlockNumber: '', 
    locationPrefix: '{{ $selectedLocationCode ?? '' }}', 

    // Fungsi Buka Detail
    openDetail(blockData) {
        this.activeBlock = blockData;
        this.showDetailModal = true;
    },
    
    // Fungsi Buka Modal Tambah
    openAddModal() {
        this.newBlockNumber = ''; 
        this.showAddModal = true;
    },
    
    // Loading saat ganti lokasi di Sidebar
    changeLocation(event) {
        this.isLoading = true;
        event.target.form.submit();
    }
}">

    <!-- TITLE PAGE -->
    <div class="mb-10">
        <h1 class="text-3xl font-bold text-slate-800 font-serif">Manajemen Blok Makam</h1>
        <p class="text-sm text-slate-500 mt-1">Kelola ketersediaan lahan TPU secara visual.</p>
    </div>

    <!-- LAYOUT GRID UTAMA -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start relative z-0">
        
        <!-- SIDEBAR KIRI (CONTROLS) -->
        <div class="lg:col-span-1 flex flex-col gap-6 sticky top-6">
            
            <!-- 1. Pilih Lokasi -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Pilih Lokasi</label>
                <form action="{{ route('blocks.index') }}" method="GET">
                    <select name="location" @change="changeLocation($event)" class="block w-full border-slate-300 rounded-lg shadow-sm focus:border-slate-500 focus:ring-slate-500 text-sm font-bold py-3 px-4 bg-slate-50 cursor-pointer hover:bg-slate-100 transition text-slate-700">
                        <option value="" disabled {{ !$selectedLocationCode ? 'selected' : '' }}>-- Pilih Lokasi --</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->code }}" {{ $selectedLocationCode == $loc->code ? 'selected' : '' }}>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <!-- 2. Legenda -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-5 border-b border-slate-100 pb-2">Legenda Peta</h4>
                <div class="flex flex-col gap-3">
                    <div class="flex items-center"><div class="legend-box status-available mr-3"></div><span class="text-sm font-bold text-slate-600">Tersedia (Baru)</span></div>
                    <div class="flex items-center"><div class="legend-box status-reserved mr-3"></div><span class="text-sm font-bold text-slate-600">Milik PJ (Dipesan)</span></div>
                    <div class="flex items-center"><div class="legend-box status-occupied_1 mr-3"></div><span class="text-sm font-bold text-slate-600">Terisi (1 Jenazah)</span></div>
                    <div class="flex items-center"><div class="legend-box status-occupied_2 mr-3"></div><span class="text-sm font-bold text-slate-600">Terisi (2 Jenazah)</span></div>
                    <div class="flex items-center"><div class="legend-box status-occupied_3 mr-3"></div><span class="text-sm font-bold text-slate-800">Penuh / Closed</span></div>
                </div>
            </div>

            <!-- 3. Tombol Tambah Blok -->
            <button @click="openAddModal()" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-4 px-4 rounded-xl shadow-lg flex items-center justify-center gap-3 transition transform hover:-translate-y-1 group">
                <div class="bg-white/20 p-1.5 rounded-full group-hover:bg-white/30 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
                Tambah Blok Baru
            </button>
        </div>

        <!-- AREA KANAN (MAP) -->
        <div class="lg:col-span-3 relative">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm min-h-[600px] relative p-1 overflow-hidden">
                
                <!-- Loading Animation -->
                <div x-show="isLoading" class="loading-overlay" x-transition.opacity>
                    <div class="animate-spin rounded-full h-14 w-14 border-[5px] border-slate-200 border-t-slate-800 mb-4"></div>
                    <p class="text-slate-800 font-bold animate-pulse text-lg tracking-wide">Sedang Memuat Peta...</p>
                </div>

                @if(isset($blocks) && count($blocks) > 0)
                    <div class="map-grid-container">
                        @foreach($blocks as $block)
                            @php
                                $cssClass = match($block->status) {
                                    'available' => 'status-available',
                                    'reserved' => 'status-reserved',
                                    'occupied_1' => 'status-occupied_1',
                                    'occupied_2' => 'status-occupied_2',
                                    'occupied_3' => 'status-occupied_3',
                                    default => 'status-occupied_1'
                                };
                                $burialDate = $block->last_burial_date ? \Carbon\Carbon::parse($block->last_burial_date)->translatedFormat('d F Y') : '-';
                                
                                $blockData = [
                                    'id' => $block->id,
                                    'status' => $block->status,
                                    'burial_date' => $burialDate,
                                    'orders' => $block->orders->map(function($order) {
                                        return [
                                            'id' => $order->id,
                                            'deceased_name' => $order->deceased_name,
                                            'pj_name' => $order->customer->name,
                                            'url' => route('orders.show', $order->id)
                                        ];
                                    })
                                ];
                            @endphp
                            
                            <div @click='openDetail(@json($blockData))' 
                                 class="map-block {{ $cssClass }}" 
                                 title="ID: {{ $block->id }}">
                                <span>{{ $block->id }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-400">
                        <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-12 h-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <p class="font-bold text-lg">Pilih lokasi atau belum ada blok data.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- =============================================
         MODAL 1: DETAIL BLOK (Z-INDEX 9999)
    ============================================= -->
    <div x-show="showDetailModal" style="display: none;" class="fixed inset-0 z-[9999] overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 transition-opacity backdrop-blur-sm" @click="showDetailModal = false" x-transition.opacity></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-100"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                 
                <div class="bg-white px-8 py-6 border-b border-slate-50 flex justify-between items-center">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">DETAIL BLOK</p>
                        <h3 class="text-4xl font-serif font-bold text-slate-800" x-text="activeBlock.id"></h3>
                    </div>
                    <!-- Badge Status Solid -->
                    <div x-show="activeBlock.status === 'available'" class="badge-solid" style="background: #10b981;">Tersedia</div>
                    <div x-show="activeBlock.status === 'reserved'" class="badge-solid" style="background: #f59e0b;">Reserved</div>
                    <div x-show="activeBlock.status === 'occupied_1'" class="badge-solid" style="background: #64748b;">Terisi (1)</div>
                    <div x-show="activeBlock.status === 'occupied_2'" class="badge-solid" style="background: #475569;">Terisi (2)</div>
                    <div x-show="activeBlock.status === 'occupied_3'" class="badge-solid" style="background: #1e293b;">Penuh</div>
                </div>

                <div class="p-8">
                    <!-- Info Penghuni -->
                    <div x-show="activeBlock.status !== 'available'" class="bg-slate-50 p-5 rounded-xl border border-slate-200 mb-6 flex items-center gap-4">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-slate-400 border border-slate-200 shadow-sm shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Terakhir Diisi</p>
                            <p class="text-xl font-bold text-slate-800 mt-0.5 font-serif" x-text="activeBlock.burial_date"></p>
                        </div>
                    </div>

                    <!-- List Orders -->
                    <div x-show="activeBlock.orders.length > 0" class="mb-8 space-y-3">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Data Penghuni Makam</p>
                        <template x-for="(order, index) in activeBlock.orders" :key="order.id">
                            <a :href="order.url" class="block bg-white border border-slate-200 hover:border-blue-400 hover:shadow-md rounded-lg p-4 transition group">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center gap-3">
                                        <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold" x-text="index + 1"></span>
                                        <div>
                                            <p class="text-sm font-bold text-slate-800 group-hover:text-blue-600 transition" x-text="order.deceased_name"></p>
                                            <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wide">PJ: <span x-text="order.pj_name"></span></p>
                                        </div>
                                    </div>
                                    <div class="text-blue-500 text-xs font-bold flex items-center gap-1 group-hover:gap-2 transition-all">
                                        Detail <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </div>
                                </div>
                            </a>
                        </template>
                    </div>

                    <!-- Form Update Status -->
                    <form x-bind:action="'/app/master-data/blok-makam/' + activeBlock.id" method="POST">
                        @csrf @method('PUT')
                        <label class="block text-sm font-bold text-slate-700 mb-3">Ubah Status Blok</label>
                        <div class="flex gap-3">
                            <select name="status" x-model="activeBlock.status" class="flex-1 border-slate-300 rounded-lg shadow-sm focus:border-slate-500 focus:ring-slate-500 py-3 font-medium text-slate-700">
                                <option value="available">Tersedia</option>
                                <option value="reserved">Reserved (Dipesan)</option>
                                <option value="occupied_1">Terisi (Lapis 1)</option>
                                <option value="occupied_2">Terisi (Lapis 2)</option>
                                <option value="occupied_3">Terisi (Lapis 3)</option>
                            </select>
                            <button type="submit" class="bg-blue-600 text-white font-bold px-6 py-3 rounded-lg hover:bg-blue-700 transition shadow-lg hover:shadow-blue-500/30">Update</button>
                        </div>
                    </form>

                    <!-- Form Delete -->
                    <div class="mt-10 pt-6 border-t border-slate-100">
                        <form x-bind:action="'/app/master-data/blok-makam/' + activeBlock.id" method="POST" onsubmit="return confirm('Hapus permanen?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-full btn-danger-glow font-bold px-4 py-3.5 rounded-xl flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Hapus Blok Ini Secara Permanen
                            </button>
                        </form>
                    </div>
                </div>
                <div class="bg-slate-50 px-8 py-4 flex justify-end border-t border-slate-100">
                    <button type="button" @click="showDetailModal = false" class="text-sm font-bold text-slate-500 hover:text-slate-800 transition">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- =============================================
         MODAL 2: TAMBAH BLOK BARU (Z-INDEX 9999)
    ============================================= -->
    <div x-show="showAddModal" style="display: none;" class="fixed inset-0 z-[9999] overflow-y-auto" role="dialog" aria-modal="true">
        
        <!-- Overlay Gelap + Blur -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
             @click="showAddModal = false"
             x-show="showAddModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-100 relative z-50"
                 x-show="showAddModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <form action="{{ route('blocks.store') }}" method="POST">
                    @csrf
                    
                    <!-- HEADER MODAL -->
                    <div class="bg-white px-8 py-6 flex justify-between items-center border-b border-slate-50">
                        <h3 class="text-2xl font-serif font-bold text-slate-800">Tambah Blok Baru</h3>
                        <button type="button" @click="showAddModal = false" class="bg-slate-50 p-2 rounded-full text-slate-400 hover:text-red-500 hover:bg-red-50 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- BODY MODAL -->
                    <div class="p-8 space-y-6">
                        
                        <!-- DROPDOWN PILIH LOKASI (BARU) -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Lokasi Lahan</label>
                            <div class="relative">
                                <select x-model="locationPrefix" class="block w-full border-slate-300 rounded-xl shadow-sm focus:border-slate-500 focus:ring-slate-500 font-bold text-slate-700 py-3.5 px-4 bg-slate-50 cursor-pointer hover:bg-slate-100 transition appearance-none">
                                    <option value="" disabled>-- Pilih Lokasi --</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc->code }}">{{ $loc->name }} (Kode: {{ $loc->code }})</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>

                        <!-- INPUT ID BLOK -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Nomor Blok</label>
                            <div class="flex rounded-xl shadow-sm border border-slate-300 overflow-hidden focus-within:ring-2 focus-within:ring-slate-800 focus-within:border-slate-800 transition group">
                                <div class="bg-slate-100 px-5 flex items-center border-r border-slate-300 group-focus-within:bg-slate-200 transition">
                                    <!-- Menampilkan Prefix sesuai Dropdown -->
                                    <span class="text-slate-500 font-extrabold text-lg select-none" x-text="locationPrefix ? locationPrefix + '-' : '???-'"></span>
                                </div>
                                <input type="text" 
                                       x-model="newBlockNumber" 
                                       required 
                                       maxlength="4"
                                       placeholder="001" 
                                       class="flex-1 block w-full border-0 focus:ring-0 font-bold text-xl text-slate-800 placeholder-slate-300 uppercase tracking-widest py-3.5"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"> 
                            </div>
                            <p class="text-xs text-slate-500 mt-2 ml-1">Masukkan nomor urut saja. Sistem otomatis menggabungkan dengan kode lokasi.</p>
                        </div>

                        <!-- HIDDEN INPUTS -->
                        <input type="hidden" name="id" :value="locationPrefix + '-' + newBlockNumber">
                        <input type="hidden" name="location_code" :value="locationPrefix">
                        <input type="hidden" name="status" value="available">
                    </div>

                    <!-- FOOTER MODAL -->
                    <div class="bg-slate-50 px-8 py-6 flex justify-end gap-3 border-t border-slate-100">
                        <button type="button" @click="showAddModal = false" class="px-6 py-3 bg-white border border-slate-300 rounded-xl text-slate-700 font-bold hover:bg-slate-100 transition shadow-sm text-sm">
                            Batal
                        </button>
                        
                        <button type="submit" 
                                :disabled="!newBlockNumber || !locationPrefix"
                                class="px-6 py-3 rounded-xl font-bold transition transform shadow-lg text-sm flex items-center gap-2"
                                :class="(!newBlockNumber || !locationPrefix) 
                                    ? 'bg-slate-200 text-slate-400 cursor-not-allowed shadow-none border border-slate-200' 
                                    : 'bg-slate-800 text-white hover:bg-slate-900 hover:-translate-y-0.5 hover:shadow-slate-500/30'">
                            Simpan Blok
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection