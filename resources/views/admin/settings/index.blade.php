@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="max-w-3xl mx-auto">
    
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-800 font-serif tracking-tight">Pengaturan Sistem</h1>
        <p class="text-sm text-slate-500 mt-1">Kelola biaya dan konfigurasi aplikasi. Hanya dapat diakses oleh Admin & Super Admin.</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden" 
         x-data="{
             // Ambil nilai tanpa format dari PHP
             initialFee: '{{ old('initial_fee', $settings['initial_fee'] ?? 500000) }}',
             annualFee: '{{ old('annual_fee', $settings['annual_fee'] ?? 150000) }}',
         }">
        
        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="p-8 space-y-10">
                
                <!-- Biaya Pendaftaran -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-bold text-slate-800">Biaya Pendaftaran</h3>
                        <p class="text-sm text-slate-500 mt-1">Biaya saat pendaftaran makam baru.</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Biaya Pemesanan Awal (Rp)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500 font-bold">Rp</span>
                            <input type="text" 
                                   @input="initialFee = $event.target.value.replace(/\D/g, '')"
                                   x-ref="initialFeeDisplay"
                                   x-init="new Cleave($refs.initialFeeDisplay, { numeral: true, numeralThousandsGroupStyle: 'thousand' })"
                                   value="{{ number_format(old('initial_fee', $settings['initial_fee'] ?? 500000), 0, ',', '.') }}"
                                   class="block w-full rounded-xl border-slate-300 pl-12 focus:border-slate-500 focus:ring-slate-500 sm:text-lg py-3 font-bold shadow-sm">
                            <input type="hidden" name="initial_fee" :value="initialFee">
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100">

                <!-- Iuran Tahunan -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-bold text-slate-800">Biaya Perpanjangan</h3>
                        <p class="text-sm text-slate-500 mt-1">Biaya retribusi tahunan.</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Iuran Tahunan (Rp)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500 font-bold">Rp</span>
                            <input type="text" 
                                   @input="annualFee = $event.target.value.replace(/\D/g, '')"
                                   x-ref="annualFeeDisplay"
                                   x-init="new Cleave($refs.annualFeeDisplay, { numeral: true, numeralThousandsGroupStyle: 'thousand' })"
                                   value="{{ number_format(old('annual_fee', $settings['annual_fee'] ?? 150000), 0, ',', '.') }}"
                                   class="block w-full rounded-xl border-slate-300 pl-12 focus:border-slate-500 focus:ring-slate-500 sm:text-lg py-3 font-bold shadow-sm">
                            <input type="hidden" name="annual_fee" :value="annualFee">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Simpan -->
            <div class="bg-slate-50 px-8 py-5 border-t border-slate-200 flex justify-end">
                <button type="submit" class="px-8 py-3 bg-slate-800 text-white font-bold rounded-xl hover:bg-slate-900 transition shadow-lg hover:shadow-slate-300 transform hover:-translate-y-0.5">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection