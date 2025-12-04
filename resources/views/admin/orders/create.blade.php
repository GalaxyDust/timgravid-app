@extends('layouts.admin')

@section('title', 'Input Pesanan Baru')

@section('content')
@php
    $role = Auth::user()->role;
    $theme = match($role) {
        'super_admin' => 'slate', 
        'admin' => 'amber', 
        'approver' => 'emerald', 
        'pengawas' => 'sky', 
        default => 'slate',
    };
@endphp

<div x-data="orderForm()">
    <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-800 font-serif tracking-tight">Formulir Pesanan Baru</h1>
            <p class="text-sm text-slate-500 mt-1">Lengkapi semua data untuk mendaftarkan pesanan.</p>
        </div>

        <div class="space-y-10">
            
            <!-- 1. DATA PENANGGUNG JAWAB -->
            <div class="bg-white p-8 rounded-xl border border-slate-200 shadow-sm animate-fade-in-up">
                <div class="flex items-center gap-4 mb-8 pb-6 border-b border-slate-100">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">1. Data Penanggung Jawab</h2>
                        <p class="text-xs text-slate-500">Informasi kontak utama.</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <!-- Input NIK -->
                    <div>
                        <label for="pj_nik" class="block text-sm font-medium text-slate-700 mb-1">Nomor Induk Kependudukan (NIK)</label>
                        <div class="relative">
                            <input type="text" name="pj_nik" id="pj_nik" required x-ref="nikInput" 
                                   value="{{ old('pj_nik') }}" 
                                   @input.debounce.500ms="customer.nik = $event.target.value; checkCustomer()" 
                                   pattern="\d{16}" title="Harus 16 digit angka" 
                                   placeholder="Ketik NIK untuk cek data..." 
                                   class="block w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-{{ $theme }}-500 focus:border-{{ $theme }}-500 transition sm:text-sm bg-slate-50">
                            
                            <!-- Loading Spinner -->
                            <div x-show="isCheckingNik" class="absolute right-3 top-1/2 -translate-y-1/2 animate-spin rounded-full h-5 w-5 border-b-2 border-{{ $theme }}-600"></div>
                        </div>

                        <!-- Pesan 1: Data Ditemukan -->
                        <p x-show="customerExists" class="text-xs text-green-600 mt-1 font-medium flex items-center gap-1" x-transition>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Data PJ ditemukan, form diisi otomatis.
                        </p>

                        <!-- Pesan 2: Data Baru (Notifikasi yang Anda Minta) -->
                        <p x-show="isNewCustomer && !isCheckingNik" class="text-xs text-blue-600 mt-1 font-medium flex items-center gap-1" x-transition>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Selamat datang PJ baru, silakan isi form dengan lengkap.
                        </p>
                    </div>

                    <!-- Input Nama -->
                    <div>
                        <label for="pj_name" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="pj_name" id="pj_name" required x-ref="nameInput" 
                               value="{{ old('pj_name') }}" 
                               placeholder="Contoh: Budi Santoso" 
                               class="block w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-{{ $theme }}-500 focus:border-{{ $theme }}-500 transition sm:text-sm bg-slate-50">
                    </div>

                    <!-- Input WhatsApp -->
                    <div>
                        <label for="pj_phone" class="block text-sm font-medium text-slate-700 mb-1">Nomor WhatsApp</label>
                        <input type="tel" name="pj_phone" id="pj_phone" required x-ref="phoneInput" 
                               value="{{ old('pj_phone') }}" 
                               placeholder="Contoh: 081234567890" 
                               class="block w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-{{ $theme }}-500 focus:border-{{ $theme }}-500 transition sm:text-sm bg-slate-50">
                    </div>

                    <!-- Input Alamat -->
                    <div class="md:row-span-2">
                        <label for="pj_address" class="block text-sm font-medium text-slate-700 mb-1">Alamat Lengkap</label>
                        <textarea name="pj_address" id="pj_address" rows="5" required x-ref="addressInput" 
                                  class="block w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-{{ $theme }}-500 focus:border-{{ $theme }}-500 transition sm:text-sm bg-slate-50 resize-y">{{ old('pj_address') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- 2. DATA PEMAKAMAN & PETA -->
            <div class="bg-white p-8 rounded-xl border border-slate-200 shadow-sm animate-fade-in-up delay-100">
                <div class="flex items-center gap-4 mb-8 pb-6 border-b border-slate-100">
                    <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">2. Data Pemakaman</h2>
                        <p class="text-xs text-slate-500">Pilih lokasi makam dari peta interaktif.</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div>
                        <label for="deceased_name" class="block text-sm font-medium text-slate-700 mb-1">Nama Jenazah</label>
                        <input type="text" name="deceased_name" id="deceased_name" required value="{{ old('deceased_name') }}" placeholder="Contoh: Siti Aminah" class="block w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-{{ $theme }}-500 focus:border-{{ $theme }}-500 transition sm:text-sm bg-slate-50">
                    </div>
                    <div>
                        <label for="burial_date" class="block text-sm font-medium text-slate-700 mb-1">Tanggal Dimakamkan</label>
                        <input type="date" name="burial_date" id="burial_date" required value="{{ old('burial_date', date('Y-m-d')) }}" class="block w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-{{ $theme }}-500 focus:border-{{ $theme }}-500 transition sm:text-sm bg-slate-50">
                    </div>
                </div>

                <!-- Bagian Peta -->
                <div class="mt-8 pt-6 border-t border-slate-100">
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
                        <div class="lg:col-span-1 space-y-4">
                            <div>
                                <label for="location-selector" class="block text-sm font-medium text-slate-700 mb-1">Pilih Lokasi</label>
                                <select id="location-selector" x-model="selectedLocation" @change="fetchBlocks()" class="block w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-{{ $theme }}-500 focus:border-{{ $theme }}-500 transition sm:text-sm bg-slate-50">
                                    <option value="" disabled>-- Pilih TPU --</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->code }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="block-display" class="block text-sm font-medium text-slate-700 mb-1">Blok Terpilih</label>
                                <input type="text" id="block-display" x-model="selectedBlock" readonly class="block w-full px-4 py-3 border border-slate-300 rounded-lg bg-slate-100 text-slate-600 font-mono cursor-not-allowed">
                                <input type="hidden" name="block_id" x-model="selectedBlock">
                            </div>
                            <div class="pt-2">
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Legenda Peta</h4>
                                <div class="space-y-3">
                                    <div class="flex items-center"><div class="w-5 h-5 rounded-md mr-2 status-available"></div><span class="text-xs font-medium text-slate-600">Tersedia (Baru)</span></div>
                                    <div class="flex items-center"><div class="w-5 h-5 rounded-md mr-2 status-reserved"></div><span class="text-xs font-medium text-slate-600">Milik PJ (Bisa Tumpuk)</span></div>
                                    <div class="flex items-center"><div class="w-5 h-5 rounded-md mr-2 status-occupied"></div><span class="text-xs font-medium text-slate-500 italic">Terisi (Tidak Bisa Dipesan)</span></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Map Grid -->
                        <div class="lg:col-span-3">
                            <div class="relative min-h-[300px] bg-slate-50 rounded-lg border p-1">
                                <div x-show="isLoading" class="absolute inset-0 flex flex-col items-center justify-center bg-white/80 backdrop-blur-sm z-20 rounded-lg"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-{{ $theme }}-600"></div><p class="mt-3 text-sm text-slate-500">Memuat peta...</p></div>
                                <div id="map-container" class="map-grid-container h-full">
                                    <template x-if="!isLoading && blocks.length === 0"><p class="col-span-full text-center text-sm text-slate-400 py-10">Pilih lokasi untuk menampilkan peta.</p></template>
                                    <template x-for="block in blocks" :key="block.id">
                                        <div @click="block.is_clickable ? selectBlock(block.id) : null" 
                                             :class="{
                                                 'status-available': block.map_status === 'available',
                                                 'status-reserved': block.map_status === 'owned',
                                                 'status-occupied': block.map_status === 'occupied',
                                                 'border-4 border-{{ $theme }}-500 ring-4 ring-{{ $theme }}-200 scale-110 z-10 shadow-xl': selectedBlock === block.id,
                                                 'cursor-pointer': block.is_clickable,
                                                 'cursor-not-allowed': !block.is_clickable
                                             }" 
                                             class="map-block relative transition-all duration-200">
                                            <span x-text="block.id"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 3. PEMBAYARAN AWAL -->
            <div class="bg-white p-8 rounded-xl border border-slate-200 shadow-sm animate-fade-in-up delay-200" x-data="{ paymentValue: '{{ old('initial_payment', 0) }}', get showProof() { return Number(this.paymentValue) > 0; } }">
                <div class="flex items-center gap-4 mb-8 pb-6 border-b border-slate-100">
                    <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div><h2 class="text-xl font-bold text-slate-800">3. Pembayaran Awal (Opsional)</h2><p class="text-xs text-slate-500">Jika pelanggan membayar tunai saat pendaftaran.</p></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div class="max-w-sm">
                        <label for="payment-display" class="block text-sm font-medium text-slate-700 mb-1">Jumlah Bayar Tunai</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 font-medium">Rp</div>
                            <input type="text" id="payment-display" x-ref="paymentDisplay" @input="paymentValue = $event.target.value.replace(/\D/g, '')" @focus="if(Number(paymentValue) === 0) { $event.target.value = ''; }" @blur="if($event.target.value === '') { $event.target.value = '0'; paymentValue = '0'; }" x-init="new Cleave($refs.paymentDisplay, { numeral: true, numeralThousandsGroupStyle: 'thousand' })" value="{{ number_format(old('initial_payment', 0), 0, ',', '.') }}" class="block w-full pl-10 pr-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-{{ $theme }}-500 focus:border-{{ $theme }}-500 transition sm:text-sm bg-slate-50">
                            <input type="hidden" name="initial_payment" :value="paymentValue">
                        </div>
                        <p class="text-xs text-slate-500 mt-2">Biarkan 0 jika belum ada. Jika diisi, tagihan akan otomatis dikirim ke Approver.</p>
                    </div>
                    <div id="proof-upload-container" class="transition-opacity duration-300" :class="{ 'opacity-100': showProof, 'opacity-40': !showProof }">
                        <label for="proof_file" class="block text-sm font-medium text-slate-700 mb-1">Unggah Bukti <span class="text-red-500" x-show="showProof">* (Wajib)</span></label>
                        <label :for="showProof ? 'proof_file' : ''" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-lg" :class="{ 'cursor-pointer hover:border-{{ $theme }}-400 bg-slate-50 hover:bg-slate-100': showProof, 'cursor-not-allowed bg-slate-100': !showProof }">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                <div class="flex text-sm text-slate-600"><p class="relative font-medium text-{{ $theme }}-600 hover:text-{{ $theme }}-500"><span>Pilih file untuk diunggah</span></p></div>
                                <p id="file-name-preview" class="text-xs text-slate-500">PNG, JPG, JPEG hingga 2MB</p>
                            </div>
                            <input id="proof_file" name="proof_file" type="file" class="sr-only" :disabled="!showProof">
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4 mt-10 pt-6 border-t border-slate-200">
            <a href="{{ route('orders.index') }}" class="px-6 py-2.5 rounded-lg text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Batal</a>
            <button type="submit" class="px-8 py-2.5 rounded-lg text-sm font-bold text-white bg-{{ $theme }}-800 hover:bg-{{ $theme }}-900 transition shadow-lg hover:shadow-{{ $theme }}-400/50 transform hover:-translate-y-0.5 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Pesanan
            </button>
        </div>
    </form>
</div>

<!-- SCRIPT PENTING -->
<script>
    function orderForm() {
        return {
            customer: { nik: '{{ old("pj_nik", "") }}' },
            isCheckingNik: false, 
            customerExists: false, 
            isNewCustomer: false, 
            customerHasGrave: false,
            isLoading: false,
            selectedLocation: '{{ $locations->first()->code ?? "" }}',
            selectedBlock: '{{ old("block_id", "") }}',
            blocks: [],
            
            init() {
                // Inisialisasi jika terjadi error validasi sebelumnya
                if (this.$refs.nikInput.value.length === 16) { 
                    this.customer.nik = this.$refs.nikInput.value; 
                    this.checkCustomer(false); 
                }
                if (this.selectedLocation) this.fetchBlocks();
            },
            
            checkCustomer(resetFields = true) {
                // 1. Jika NIK belum 16 digit, reset semua dan berhenti
                if (this.customer.nik.length !== 16) {
                    this.customerExists = false; 
                    this.isNewCustomer = false; 
                    this.customerHasGrave = false;
                    if (resetFields) { this.clearFields(); }
                    this.fetchBlocks(); 
                    return;
                }

                this.isCheckingNik = true;
                
                // Reset status sementara loading
                this.customerExists = false;
                this.isNewCustomer = false; 

                fetch(`{{ route('orders.api.check_customer') }}?nik=${this.customer.nik}`)
                    .then(res => res.json())
                    .then(data => {
                        // PERBAIKAN UTAMA DISINI:
                        // Cek apakah data tidak null DAN data memiliki properti 'id' atau 'name'
                        if (data && (data.id || data.name)) {
                            
                            // === KASUS 1: USER LAMA (Data Ditemukan) ===
                            this.$refs.nameInput.value = data.name;
                            this.$refs.phoneInput.value = data.phone_number;
                            this.$refs.addressInput.value = data.address;
                            
                            this.customerExists = true;
                            this.isNewCustomer = false;
                        } else {
                            
                            // === KASUS 2: USER BARU (Data Tidak Ditemukan/Null) ===
                            if (resetFields) { this.clearFields(); }
                            
                            this.customerExists = false;
                            this.isNewCustomer = true; // Munculkan pesan "Selamat Datang PJ Baru"
                        }
                        
                        this.isCheckingNik = false; 
                        this.fetchBlocks();
                    })
                    .catch(err => {
                        console.error("Error checking customer:", err);
                        this.isCheckingNik = false;
                        // Jika error jaringan, asumsikan user baru saja agar tidak stuck
                        this.isNewCustomer = true; 
                    });
            },
            
            clearFields() { 
                // Pastikan diset ke string kosong (''), JANGAN null atau undefined
                this.$refs.nameInput.value = ''; 
                this.$refs.phoneInput.value = ''; 
                this.$refs.addressInput.value = ''; 
            },
            
            fetchBlocks() {
                // Logika peta tetap sama
                this.isLoading = true; 
                this.blocks = []; 
                this.selectedBlock = ''; 
                this.customerHasGrave = false;
                
                fetch(`{{ route('orders.api.blocks') }}?location=${this.selectedLocation}&customer_nik=${this.customer.nik}`)
                    .then(res => res.json())
                    .then(data => {
                        this.blocks = data;
                        if (data.some(block => block.map_status === 'owned')) { 
                            this.customerHasGrave = true; 
                        }
                        this.isLoading = false;
                    });
            },
            
            selectBlock(blockId) { this.selectedBlock = blockId; }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('proof_file');
        const fileNamePreview = document.getElementById('file-name-preview');
        
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                if(this.files.length > 0) {
                    fileNamePreview.textContent = this.files[0].name;
                    fileNamePreview.classList.add('font-bold', 'text-emerald-600');
                } else {
                    fileNamePreview.textContent = 'PNG, JPG, JPEG hingga 2MB';
                    fileNamePreview.classList.remove('font-bold', 'text-emerald-600');
                }
            });
        }
    });
</script>
@endsection