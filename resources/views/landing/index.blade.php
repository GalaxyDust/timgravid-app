@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <!-- Load Script JS Khusus Landing Page -->
    @vite('resources/js/landing.js')

    <!-- =========================================
       1. HERO SECTION (FULL SCREEN)
       ========================================= -->
    <section id="hero-section" class="relative h-[100vh] min-h-[600px] flex items-center justify-center text-center px-4 overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('Images/hero-background.jpg') }}" 
                 alt="Background Pemakaman" 
                 class="w-full h-full object-cover transform scale-105 filter brightness-[0.55]">
            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-primary/90 via-primary/40 to-black/40"></div>
        </div>
        
        <div class="relative z-10 max-w-5xl mx-auto text-white mt-10 px-4">
            <!-- Badge -->
            <div class="inline-block mb-4 md:mb-6 px-4 md:px-5 py-2 border border-accent/60 rounded-full bg-black/40 backdrop-blur-md shadow-2xl animate-fade-in-down">
                <span class="text-accent font-bold tracking-[0.15em] uppercase text-[10px] md:text-sm">Yayasan Sajarah Timbanganten</span>
            </div>
            
            <!-- Headline -->
            <h1 class="text-4xl md:text-7xl font-bold mb-6 md:mb-8 leading-tight font-serif text-cream drop-shadow-2xl animate-fade-in-up delay-100">
                Menjaga Warisan,<br>
                <span class="text-accent italic relative inline-block mt-1 md:mt-2">Memberikan Ketenangan.</span>
            </h1>
            
            <!-- Subheadline -->
            <p class="text-gray-200 text-sm md:text-xl mb-8 md:mb-12 max-w-2xl mx-auto leading-relaxed font-light drop-shadow animate-fade-in-up delay-200 px-2">
                Sistem informasi pemakaman terpadu Kota Bandung. Akses informasi ketersediaan lahan dan sejarah secara digital, transparan, dan amanah.
            </p>
            
            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row justify-center gap-4 animate-fade-in-up delay-300 w-full sm:w-auto">
                <a href="#peta" class="bg-accent hover:bg-[#a37f4b] text-white px-8 py-3.5 rounded-full font-bold transition-all shadow-xl hover:shadow-accent/50 text-sm md:text-base tracking-wide transform hover:-translate-y-1 hover:scale-105 block sm:inline-block w-full sm:w-auto">
                    Cek Ketersediaan
                </a>
                <a href="#sejarah" class="bg-white/10 backdrop-blur-md border border-white/40 text-white px-8 py-3.5 rounded-full font-bold hover:bg-white hover:text-primary transition-all shadow-lg text-sm md:text-base tracking-wide transform hover:-translate-y-1 block sm:inline-block w-full sm:w-auto">
                    Telusuri Sejarah
                </a>
            </div>
        </div>

        <!-- Scroll Indicator (Desktop Only) -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce text-white/50 cursor-pointer hidden md:block">
            <a href="#layanan">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </a>
        </div>
    </section>


    <!-- =========================================
       2. SECTION LAYANAN (LURUS / STRAIGHT)
       ========================================= -->
    <!-- Class 'rounded' dihapus agar lurus total -->
    <section id="layanan" class="py-20 md:py-24 bg-white relative z-20">
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="text-center mb-12 md:mb-16">
                <span class="text-accent font-bold tracking-widest uppercase text-xs mb-2 block">Proses Mudah</span>
                <h2 class="text-3xl md:text-4xl font-bold font-serif mb-4 text-primary drop-shadow-sm">Layanan Kami</h2>
                <div class="w-20 h-1.5 bg-accent mx-auto rounded-full mb-6"></div>
                <p class="text-slate-600 max-w-2xl mx-auto text-base md:text-lg leading-relaxed">
                    Proses administrasi yang transparan, mudah, dan amanah untuk ketenangan keluarga Anda.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="group bg-white p-8 rounded-2xl text-center border-2 border-primary/10 hover:border-accent transition-all duration-300 hover:shadow-2xl hover:shadow-accent/10 transform hover:-translate-y-2 cursor-default relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-accent/5 rounded-full group-hover:scale-150 transition duration-500"></div>
                    <div class="w-16 h-16 mx-auto mb-6 relative">
                        <div class="absolute inset-0 bg-accent/10 rounded-2xl transform rotate-6 group-hover:rotate-12 transition duration-300"></div>
                        <div class="relative bg-white border border-accent/20 rounded-2xl w-full h-full flex items-center justify-center text-accent shadow-sm group-hover:bg-accent group-hover:text-white transition duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 7m0 13V7m0 0L9 7"></path></svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-primary group-hover:text-accent transition">1. Cek Ketersediaan</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">Pantau ketersediaan blok makam secara real-time melalui peta digital.</p>
                </div>
                
                <!-- Step 2 -->
                <div class="group bg-white p-8 rounded-2xl text-center border-2 border-primary/10 hover:border-accent transition-all duration-300 hover:shadow-2xl hover:shadow-accent/10 transform hover:-translate-y-2 cursor-default relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-primary/5 rounded-full group-hover:scale-150 transition duration-500"></div>
                    <div class="w-16 h-16 mx-auto mb-6 relative">
                        <div class="absolute inset-0 bg-primary/10 rounded-2xl transform -rotate-3 group-hover:-rotate-6 transition duration-300"></div>
                        <div class="relative bg-white border border-primary/20 rounded-2xl w-full h-full flex items-center justify-center text-primary shadow-sm group-hover:bg-primary group-hover:text-white transition duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-primary group-hover:text-primary transition">2. Administrasi Mudah</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">Proses pendaftaran dan pemberkasan yang simpel dibantu tim admin.</p>
                </div>

                <!-- Step 3 -->
                <div class="group bg-white p-8 rounded-2xl text-center border-2 border-primary/10 hover:border-accent transition-all duration-300 hover:shadow-2xl hover:shadow-accent/10 transform hover:-translate-y-2 cursor-default relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-emerald-500/5 rounded-full group-hover:scale-150 transition duration-500"></div>
                    <div class="w-16 h-16 mx-auto mb-6 relative">
                        <div class="absolute inset-0 bg-emerald-500/10 rounded-2xl transform rotate-3 group-hover:rotate-6 transition duration-300"></div>
                        <div class="relative bg-white border border-emerald-500/20 rounded-2xl w-full h-full flex items-center justify-center text-emerald-600 shadow-sm group-hover:bg-emerald-600 group-hover:text-white transition duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-primary group-hover:text-emerald-600 transition">3. Resmi Terdaftar</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">Data tersimpan aman dan Anda mendapatkan bukti kepemilikan resmi.</p>
                </div>
            </div>
        </div>
    </section>


    <!-- =========================================
       3. SECTION PETA KETERSEDIAAN
       ========================================= -->
    <section id="peta" class="py-16 md:py-24 bg-slate-50 relative border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 md:mb-16">
                <span class="text-accent font-bold tracking-widest uppercase text-xs mb-3 block">Sistem Informasi Geografis</span>
                <h2 class="text-3xl md:text-5xl font-bold text-primary mb-6 font-serif">Peta Ketersediaan Lahan</h2>
                <div class="w-24 h-1 bg-accent mx-auto rounded-full mb-6"></div>
                <p class="text-slate-500 max-w-2xl mx-auto text-base md:text-lg">Pilih lokasi pemakaman untuk melihat denah dan status ketersediaan blok.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
                
                <!-- Sidebar Filter & Legend -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Dropdown Lokasi -->
                    <div class="bg-white p-6 rounded-xl border border-primary/10 shadow-lg">
                        <label for="location-select" class="block text-sm font-bold text-primary mb-3 uppercase tracking-wide">Pilih Lokasi TPU</label>
                        <div class="relative">
                            <select id="location-select" class="block w-full pl-4 pr-10 py-3 text-base border-primary/20 focus:outline-none focus:ring-2 focus:ring-accent focus:border-accent sm:text-sm rounded-lg bg-slate-50 text-text-dark shadow-sm transition appearance-none cursor-pointer font-bold">
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->code }}">{{ $loc->name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-primary"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></div>
                        </div>
                    </div>

                    <!-- Legenda Status (Update Warna Pastel) -->
                    <div class="bg-white p-6 rounded-xl border border-primary/10 shadow-lg">
                        <h4 class="text-sm font-bold text-primary mb-4 uppercase tracking-wide border-b border-primary/10 pb-2">Keterangan Status</h4>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="w-6 h-6 rounded bg-[#ecfdf5] border-2 border-[#34d399] mr-3 shadow-sm flex-shrink-0"></div>
                                <span class="text-sm font-bold text-slate-700">Tersedia</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-6 h-6 rounded bg-[#fffbeb] border-2 border-[#fbbf24] mr-3 shadow-sm flex-shrink-0"></div>
                                <span class="text-sm font-bold text-slate-700">Dipesan</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-6 h-6 rounded bg-[#f1f5f9] border-2 border-[#cbd5e1] mr-3 shadow-sm flex-shrink-0"></div>
                                <span class="text-sm font-bold text-slate-500">Terisi</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Area Peta Grid -->
                <div class="lg:col-span-3">
                    <div class="relative bg-white rounded-xl border border-primary/10 p-2 shadow-inner min-h-[400px] md:min-h-[500px]">
                        <!-- Loading -->
                        <div id="map-loading" class="absolute inset-0 flex flex-col items-center justify-center bg-white/90 z-20 hidden rounded-xl backdrop-blur-sm">
                            <div class="animate-spin rounded-full h-12 w-12 border-4 border-gray-200 border-t-accent mb-4"></div>
                            <span class="text-primary font-medium tracking-wide">Memuat Data...</span>
                        </div>
                        
                        <!-- Grid Container Responsif -->
                        <div id="map-container" class="map-grid-container h-full min-h-[400px] md:min-h-[500px] p-4 md:p-6 grid grid-cols-[repeat(auto-fill,minmax(60px,1fr))] gap-3 content-start"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- =========================================
       4. SECTION ARTIKEL SEJARAH
       ========================================= -->
    <section id="sejarah" class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center md:items-end mb-12 border-b-2 border-primary/10 pb-6 gap-4 text-center md:text-left">
                <div>
                    <span class="text-accent font-bold tracking-widest uppercase text-xs mb-2 block">Arsip Digital</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-primary font-serif">Jejak Sejarah</h2>
                </div>
                <div class="hidden md:block">
                    <p class="text-slate-400 text-sm italic">"Bangsa yang besar adalah bangsa yang menghargai jasa pahlawannya."</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($featuredArticles as $article)
                    <article class="group bg-white rounded-2xl shadow-sm hover:shadow-2xl transition-all duration-500 border border-slate-100 overflow-hidden flex flex-col h-full transform hover:-translate-y-2">
                        <!-- Gambar -->
                        <div class="h-56 overflow-hidden relative">
                            <div class="absolute inset-0 bg-primary/40 opacity-0 group-hover:opacity-100 transition-opacity duration-500 z-10 flex items-center justify-center">
                                <span class="text-white border-2 border-white px-6 py-2 rounded-full text-sm font-bold tracking-wide transform translate-y-4 group-hover:translate-y-0 transition duration-500">Baca Artikel</span>
                            </div>
                            <!-- Panggil Gambar -->
                            <img src="{{ $article->full_image_url }}" 
                                 alt="{{ $article->title }}" 
                                 class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700"
                                 onerror="this.src='https://via.placeholder.com/400x300?text=No+Image'">
                        </div>
                        
                        <!-- Konten -->
                        <div class="p-6 md:p-8 flex-1 flex flex-col relative">
                            <div class="absolute -top-4 right-6 bg-accent text-white text-[10px] font-bold px-3 py-1.5 rounded shadow-md uppercase tracking-wider">
                                {{ $article->category ?? 'Sejarah' }}
                            </div>

                            <h3 class="text-xl font-bold text-primary mb-3 font-serif leading-snug group-hover:text-accent transition-colors line-clamp-2 h-[3.5rem]">
                                <a href="{{ route('article.show', $article->id) }}">{{ $article->title }}</a>
                            </h3>
                            
                            <p class="text-slate-500 text-sm leading-relaxed mb-6 line-clamp-3 flex-1">
                                {{ $article->summary }}
                            </p>
                            
                            <a href="{{ route('article.show', $article->id) }}" class="inline-flex items-center text-primary font-bold text-xs uppercase tracking-wide hover:text-accent transition group-hover:translate-x-2 duration-300 mt-auto">
                                Baca Selengkapnya <span class="ml-2 text-lg">→</span>
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full py-20 text-center bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                        <p class="text-slate-400 italic text-lg">Belum ada artikel sejarah yang dipublikasikan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection