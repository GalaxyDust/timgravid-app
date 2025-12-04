@extends('layouts.admin')

@section('title', 'Detail Pesanan #' . str_pad($order->id, 5, '0', STR_PAD_LEFT))

@section('content')
    @php
        $pjPhone = $order->customer->phone_number ?? '';
        $waNumber = Str::startsWith($pjPhone, '0') ? '62' . substr($pjPhone, 1) : $pjPhone;
        $waMessage = "Halo Bapak/Ibu {$order->customer->name}, mengingatkan mengenai tagihan pemakaman untuk Alm/Almh {$order->deceased_name} di Blok {$order->block_id}.";
        $annualFee = \App\Models\Setting::where('key', 'annual_fee')->value('value') ?? 150000;
    @endphp

    <div x-data="{ 
        showEditModal: false, 
        showUploadModal: false, 
        showAddInvoiceModal: false, 
        showEditInvoiceModal: false,
        
        selectedInvoice: {},
        
        openUpload(invoice) {
            this.selectedInvoice = invoice;
            this.showUploadModal = true;
        },
        openEditInvoice(invoice) {
            this.selectedInvoice = invoice;
            this.showEditInvoiceModal = true;
        }
    }" x-cloak>

        <!-- HEADER HALAMAN -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('orders.index') }}" class="text-slate-400 hover:text-slate-600 transition p-1 rounded-full hover:bg-slate-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </a>
                    <h1 class="text-3xl font-bold text-slate-800 font-serif">Detail Pesanan</h1>
                </div>
                <p class="text-sm text-slate-500 ml-10">Rincian lengkap pesanan <span class="font-bold font-mono text-slate-800">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span></p>
            </div>
        </div>

        <!-- LAYOUT 2 KOLOM -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <!-- Card Data Pemakaman -->
                <div class="bg-white p-8 rounded-xl border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                        <h2 class="text-xl font-bold text-slate-800">Data Pemakaman</h2>
                        <button @click="showEditModal = true" class="text-sm font-medium text-slate-600 hover:text-blue-600 flex items-center gap-2 bg-slate-50 hover:bg-blue-50 px-4 py-2 rounded-lg transition border border-slate-200 hover:border-blue-200">
                            Ubah
                        </button>
                    </div>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5 text-sm">
                        <div><dt class="font-semibold text-slate-500">Nama Jenazah</dt><dd class="mt-1 font-semibold text-slate-800 text-base">{{ $order->deceased_name }}</dd></div>
                        <div><dt class="font-semibold text-slate-500">Tgl Dimakamkan</dt><dd class="mt-1 text-slate-800">{{ \Carbon\Carbon::parse($order->burial_date)->translatedFormat('d F Y') }}</dd></div>
                        <div><dt class="font-semibold text-slate-500">Lokasi</dt><dd class="mt-1 text-slate-800">{{ $order->block->location->name ?? 'N/A' }}</dd></div>
                        <div><dt class="font-semibold text-slate-500">ID Blok</dt><dd class="mt-1 font-mono font-bold text-slate-800">{{ $order->block_id }}</dd></div>
                    </dl>
                </div>

                <!-- Card Data PJ -->
                <div class="bg-white p-8 rounded-xl border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                        <h2 class="text-xl font-bold text-slate-800">Data Penanggung Jawab</h2>
                        @if($pjPhone)
                        <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode($waMessage) }}" target="_blank" class="text-sm font-bold text-white bg-green-500 hover:bg-green-600 flex items-center gap-2 px-4 py-2 rounded-lg transition shadow-md">Hubungi PJ</a>
                        @endif
                    </div>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5 text-sm">
                        <div><dt class="font-semibold text-slate-500">Nama</dt><dd class="mt-1 text-slate-800">{{ $order->customer->name ?? 'N/A' }}</dd></div>
                        <div><dt class="font-semibold text-slate-500">NIK</dt><dd class="mt-1 text-slate-800 font-mono">{{ $order->customer->nik ?? 'N/A' }}</dd></div>
                        <div><dt class="font-semibold text-slate-500">No. WhatsApp</dt><dd class="mt-1 text-slate-800">{{ $order->customer->phone_number ?? 'N/A' }}</dd></div>
                        <div class="md:col-span-2"><dt class="font-semibold text-slate-500">Alamat</dt><dd class="mt-1 text-slate-800 leading-relaxed">{{ $order->customer->address ?? 'N/A' }}</dd></div>
                    </dl>
                </div>
            </div>

            <!-- KOLOM KANAN -->
            <div class="lg:col-span-1 space-y-8">
                <div class="bg-white p-8 rounded-xl border border-slate-200 shadow-sm">
                    <h2 class="text-xl font-bold text-slate-800 mb-6 pb-4 border-b border-slate-100">Info Pesanan</h2>
                    <dl class="space-y-5 text-sm">
                        <div><dt class="font-semibold text-slate-500">Status</dt>
                            <dd class="mt-2">
                                @if($order->status == 'active')<span class="px-3 py-1.5 inline-flex text-xs font-bold rounded-full bg-green-100 text-green-800">Aktif</span>
                                @else <span class="px-3 py-1.5 inline-flex text-xs font-bold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($order->status) }}</span> @endif
                            </dd>
                        </div>
                        <div><dt class="font-semibold text-slate-500">Diinput oleh</dt><dd class="mt-1 text-slate-800">{{ $order->creator->name ?? 'N/A' }} ({{ $order->creator->role ? str_replace('_', ' ', $order->creator->role) : 'N/A' }})</dd></div>
                        <div><dt class="font-semibold text-slate-500">Tgl Input</dt><dd class="mt-1 text-slate-800">{{ $order->created_at->translatedFormat('d F Y, H:i') }}</dd></div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- TABEL TAGIHAN -->
        <div class="mt-10 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h2 class="text-xl font-bold text-slate-800">Riwayat Tagihan</h2>
                <button @click="showAddInvoiceModal = true" class="bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold px-4 py-2 rounded-lg flex items-center gap-2 shadow-md">
                    Buat Tagihan
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Deskripsi</th><th class="px-6 py-3">Nominal</th><th class="px-6 py-3">Tgl Bayar</th>
                            <th class="px-6 py-3">Metode</th><th class="px-6 py-3">Status</th><th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($order->invoices as $invoice)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4">{{ $invoice->description }}</td>
                                <td class="px-6 py-4 font-semibold">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">{{ $invoice->paid_at ? $invoice->paid_at->translatedFormat('d M Y') : '-' }}</td>
                                <td class="px-6 py-4">{{ $invoice->payment_method ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    @if($invoice->status == 'paid') <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Lunas</span>
                                    @elseif($invoice->status == 'waiting_approval') <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Verifikasi</span>
                                    @elseif($invoice->status == 'rejected') <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Ditolak</span>
                                    @else <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Belum Bayar</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end items-center gap-2">
                                        @if(in_array($invoice->status, ['unpaid', 'rejected']))
                                            <button @click="openUpload({{ $invoice->toJson() }})" class="text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg shadow-md transition">Upload</button>
                                            <button @click="openEditInvoice({{ $invoice->toJson() }})" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-full" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                                            <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" onsubmit="return confirm('Batalkan tagihan ini?')"> @csrf @method('DELETE')
                                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-full" title="Hapus"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                            </form>
                                        @elseif($invoice->proof_file)
                                            <a href="{{ asset('storage/' . $invoice->proof_file) }}" target="_blank" class="text-xs font-medium text-blue-600 hover:underline">Lihat Bukti</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-10 text-slate-500">Belum ada tagihan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ================= MODAL CONTAINER (BACKDROP & ANIMASI) ================= -->
        <div x-show="showEditModal || showUploadModal || showAddInvoiceModal || showEditInvoiceModal" style="display: none;" class="fixed inset-0 z-[9999] overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                
                <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity" 
                     @click="showEditModal=false; showUploadModal=false; showAddInvoiceModal=false; showEditInvoiceModal=false;"
                     x-show="showEditModal || showUploadModal || showAddInvoiceModal || showEditInvoiceModal"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <!-- ================= 1. MODAL EDIT DATA ================= -->
                <!-- ... -->

                <!-- ================= 2. MODAL UPLOAD BUKTI (FIXED) ================= -->
                <div x-show="showUploadModal" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form x-bind:action="'/app/tagihan/' + selectedInvoice.id + '/upload'" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="px-8 pt-8 text-center">
                            <h3 class="text-xl font-serif font-bold text-slate-800">Upload Bukti Pembayaran</h3>
                            <p class="text-sm text-slate-500 mt-2">Tagihan: <span class="font-bold" x-text="selectedInvoice.description"></span></p>
                            
                            <div class="my-6 bg-blue-50 border-2 border-dashed border-blue-200 rounded-xl p-4">
                                <span class="block text-xs text-blue-500 font-bold uppercase tracking-wide">Nominal</span>
                                <span class="block text-4xl font-bold text-blue-700 mt-1" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedInvoice.amount)"></span>
                            </div>
                            
                            <div x-data="{ fileName: '' }">
                                <label for="file-upload" class="w-full flex items-center justify-center gap-4 px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-100 transition">
                                    <span class="px-4 py-2 text-sm font-bold text-blue-700 bg-blue-100 rounded-full">Choose File</span>
                                    <span class="text-sm text-slate-500" x-text="fileName || 'No file chosen'"></span>
                                </label>
                                <input id="file-upload" type="file" name="proof_file" required accept="image/*" class="hidden" 
                                       @change="fileName = $event.target.files.length > 0 ? $event.target.files[0].name : 'No file chosen'">
                            </div>
                        </div>
                        <div class="bg-slate-50 px-8 py-5 flex justify-end gap-3 border-t mt-8">
                            <button type="button" @click="showUploadModal = false" class="px-6 py-2.5 bg-white border rounded-xl font-bold text-slate-700">Batal</button>
                            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl shadow-lg hover:bg-blue-700">Upload</button>
                        </div>
                    </form>
                </div>

                <!-- ================= 3. MODAL TAMBAH TAGIHAN (FIXED) ================= -->
                <div x-show="showAddInvoiceModal" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form action="{{ route('orders.create_invoice', $order->id) }}" method="POST">
                        @csrf
                        <div class="bg-white px-8 py-6">
                            <h3 class="text-xl font-serif font-bold text-slate-800">Buat Tagihan Manual</h3>
                            <p class="text-sm text-slate-500 mt-1">Gunakan untuk menagih iuran tahunan.</p>
                        </div>
                        <div class="p-8 space-y-6 border-t border-slate-100">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi</label>
                                <input type="text" name="description" required class="w-full border-slate-300 rounded-xl py-3 px-4 shadow-sm" value="Iuran Perpanjangan Makam (Tahun {{ date('Y') }})">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Nominal</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500 font-bold">Rp</span>
                                    <input type="text" x-ref="addAmount" name="amount_display" required class="w-full border-slate-300 rounded-xl py-3 px-4 pl-12 font-bold text-lg shadow-sm" value="{{ number_format($annualFee, 0, ',', '.') }}"
                                           x-init="new Cleave($refs.addAmount, { numeral: true, numeralThousandsGroupStyle: 'thousand' })">
                                    <input type="hidden" name="amount" x-bind:value="$refs.addAmount ? $refs.addAmount.value.replace(/\./g, '') : '{{ $annualFee }}'">
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-8 py-5 flex justify-end gap-3 border-t">
                            <button type="button" @click="showAddInvoiceModal = false" class="px-6 py-2.5 bg-white border rounded-xl font-bold">Batal</button>
                            <button type="submit" class="px-6 py-2.5 bg-slate-800 text-white font-bold rounded-xl shadow-lg">Buat Tagihan</button>
                        </div>
                    </form>
                </div>

                <!-- ================= 4. MODAL EDIT TAGIHAN (FIXED) ================= -->
                <div x-show="showEditInvoiceModal" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form x-bind:action="'/app/tagihan/' + selectedInvoice.id" method="POST">
                        @csrf @method('PUT')
                        <div class="px-8 py-6"><h3 class="text-xl font-serif font-bold text-slate-800">Edit Deskripsi</h3></div>
                        <div class="p-8 border-t">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi Tagihan</label>
                            <textarea name="description" x-model="selectedInvoice.description" rows="3" class="w-full rounded-xl border-slate-300 shadow-sm"></textarea>
                        </div>
                        <div class="bg-slate-50 px-8 py-5 flex justify-end gap-3 border-t">
                            <button type="button" @click="showEditInvoiceModal = false" class="px-6 py-2.5 bg-white border rounded-xl font-bold">Batal</button>
                            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl shadow-lg">Simpan</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
        
    </div>
@endsection