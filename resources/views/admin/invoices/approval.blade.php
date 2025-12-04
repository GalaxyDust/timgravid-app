@extends('layouts.admin')

@section('title', 'Verifikasi Tagihan')

@section('content')
<!-- CSS Helper -->
<style>[x-cloak] { display: none !important; }</style>

<div x-data="{ 
    showModal: false,
    activeInvoice: { id: '', amount: '', date: '', proof_url: '', pj_name: '', deceased_name: '', description: '' },
    note: '',
    
    openVerify(invoice) {
        this.activeInvoice = invoice;
        this.note = ''; 
        this.showModal = true;
    }
}" x-cloak>

    <!-- Header Page -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-800 font-serif">Verifikasi Tagihan</h1>
        <p class="text-sm text-slate-500 mt-1">Daftar pembayaran masuk yang menunggu persetujuan Anda.</p>
    </div>

    <!-- Tabel Data -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        @if($invoices->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4">Tgl Bayar</th>
                            <th class="px-6 py-4">Nominal</th>
                            <th class="px-6 py-4">Info Pesanan</th>
                            <th class="px-6 py-4">Metode</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($invoices as $inv)
                            @php
                                $proofUrl = $inv->proof_file ? asset('storage/' . $inv->proof_file) : null;
                                $invoiceData = [
                                    'id' => $inv->id,
                                    'amount' => 'Rp ' . number_format($inv->amount, 0, ',', '.'),
                                    'date' => \Carbon\Carbon::parse($inv->paid_at)->translatedFormat('l, d F Y - H:i'),
                                    'proof_url' => $proofUrl,
                                    'pj_name' => $inv->order->customer->name,
                                    'deceased_name' => $inv->order->deceased_name,
                                    'description' => $inv->description,
                                    'url_decide' => route('invoices.decide', $inv->id)
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-slate-600">
                                    {{ $inv->paid_at ? \Carbon\Carbon::parse($inv->paid_at)->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 font-bold text-emerald-600 text-base">
                                    Rp {{ number_format($inv->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800">{{ $inv->order->deceased_name }}</div>
                                    <div class="text-xs text-slate-500">PJ: {{ $inv->order->customer->name }}</div>
                                    @if($inv->description)
                                        <div class="text-xs text-slate-400 italic mt-1 truncate max-w-[200px]">{{ $inv->description }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $inv->payment_method }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button @click='openVerify(@json($invoiceData))' 
                                            class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-lg font-bold text-xs shadow-md transition transform hover:-translate-y-0.5 inline-flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Verifikasi
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-20 text-center text-slate-400">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                    <svg class="w-10 h-10 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-700">Tidak ada tagihan pending</h3>
                <p class="text-sm">Semua pembayaran telah diverifikasi.</p>
            </div>
        @endif
    </div>

    <!-- =============================================
         MODAL VERIFIKASI (FIXED LAYOUT)
    ============================================= -->
    <div x-show="showModal" 
         style="display: none;" 
         class="fixed inset-0 z-[9999] overflow-y-auto" 
         role="dialog" 
         aria-modal="true">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity" 
             @click="showModal = false"
             x-show="showModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <!-- Wrapper: Centering -->
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            
            <!-- Modal Box -->
            <div class="w-full max-w-5xl transform overflow-hidden rounded-2xl bg-white text-left align-middle shadow-2xl transition-all relative"
                 x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                
                <div class="grid grid-cols-1 md:grid-cols-2 h-[70vh] md:h-[650px]">
                    
                    <!-- KIRI: GAMBAR (FULL HEIGHT) -->
                    <div class="bg-slate-900 flex flex-col items-center justify-center relative overflow-hidden group h-full">
                        <template x-if="activeInvoice.proof_url">
                            <div class="w-full h-full flex items-center justify-center bg-black">
                                <img :src="activeInvoice.proof_url" 
                                     class="max-w-full max-h-full object-contain cursor-zoom-in" 
                                     onclick="window.open(this.src, '_blank')"
                                     title="Klik untuk melihat ukuran penuh">
                            </div>
                        </template>
                        
                        <template x-if="!activeInvoice.proof_url">
                            <div class="text-slate-500 flex flex-col items-center p-4 text-center">
                                <div class="bg-slate-800 p-4 rounded-full mb-3">
                                    <svg class="w-10 h-10 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <span class="font-medium text-slate-400">Tidak ada bukti upload</span>
                                <span class="text-xs mt-1 text-slate-600">Mungkin pembayaran tunai tanpa bukti</span>
                            </div>
                        </template>

                        <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 bg-white/10 text-white text-xs px-4 py-2 rounded-full backdrop-blur-md border border-white/20 opacity-0 group-hover:opacity-100 transition pointer-events-none">
                            Klik gambar untuk memperbesar
                        </div>
                    </div>

                    <!-- KANAN: KONTEN & FORM -->
                    <div class="flex flex-col h-full bg-white">
                        
                        <!-- 1. HEADER & DETAIL (SCROLLABLE AREA) -->
                        <div class="p-8 flex-1 overflow-y-auto">
                            <!-- Header Modal -->
                            <div class="flex justify-between items-start mb-6">
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Verifikasi Pembayaran</p>
                                    <h3 class="text-3xl font-serif font-bold text-emerald-600" x-text="activeInvoice.amount"></h3>
                                </div>
                                <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 p-2 rounded-full hover:bg-slate-100 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>

                            <!-- List Detail -->
                            <div class="space-y-4 text-sm">
                                <div class="flex justify-between border-b border-slate-50 pb-2">
                                    <span class="text-slate-500 font-medium">Tanggal Bayar</span>
                                    <span class="font-bold text-slate-800" x-text="activeInvoice.date"></span>
                                </div>
                                <div class="flex justify-between border-b border-slate-50 pb-2">
                                    <span class="text-slate-500 font-medium">Nama Jenazah</span>
                                    <span class="font-bold text-slate-800" x-text="activeInvoice.deceased_name"></span>
                                </div>
                                <div class="flex justify-between border-b border-slate-50 pb-2">
                                    <span class="text-slate-500 font-medium">Penanggung Jawab</span>
                                    <span class="font-bold text-slate-800" x-text="activeInvoice.pj_name"></span>
                                </div>
                                
                                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 mt-2">
                                    <span class="text-[10px] text-blue-400 uppercase font-bold block mb-1">Keterangan Tagihan</span>
                                    <span class="text-slate-700 italic block" x-text="activeInvoice.description || '- Tidak ada keterangan -'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- 2. FORM AREA (DIPERBAIKI) -->
                        <!-- 
                             - bg-slate-50 : Memberikan warna latar abu-abu muda (membedakan area form).
                             - border-t : Garis pemisah atas.
                             - mt-auto : Memaksa bagian ini menempel di bawah (footer style).
                             - textarea : Background putih agar kontras.
                        -->
                        <div class="bg-slate-50 border-t border-slate-200 p-8 mt-auto">
                            <form :action="activeInvoice.url_decide" method="POST" id="verifyForm">
                                @csrf
                                <div class="mb-5">
                                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2 ml-1">Catatan Admin (Opsional)</label>
                                    <textarea name="note" x-model="note" rows="3" class="w-full border-slate-300 rounded-xl shadow-sm focus:border-slate-800 focus:ring-slate-800 text-sm p-4 bg-white resize-none transition placeholder-slate-400" placeholder="Tulis alasan penolakan atau catatan tambahan..."></textarea>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Tombol Tolak -->
                                    <button type="submit" form="verifyForm" name="decision" value="reject"
                                            class="w-full bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 font-bold py-3.5 rounded-xl transition flex items-center justify-center gap-2 group"
                                            onclick="return confirm('Yakin ingin MENOLAK pembayaran ini?')">
                                        <svg class="w-5 h-5 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Tolak
                                    </button>

                                    <!-- Tombol Terima -->
                                    <button type="submit" form="verifyForm" name="decision" value="approve"
                                            class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-3.5 rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-1 flex items-center justify-center gap-2"
                                            onclick="return confirm('Yakin bukti sudah valid dan ingin MENERIMA pembayaran ini?')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Terima & Lunas
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection