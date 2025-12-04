@extends('layouts.app')

@section('title', $article->title)

@section('content')
<div class="bg-cream min-h-screen py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Tombol Kembali -->
        <a href="{{ route('landing') }}#sejarah" class="inline-flex items-center text-sm font-bold text-text-muted hover:text-accent mb-8 transition group">
            <div class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center mr-3 group-hover:border-accent group-hover:bg-accent group-hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </div>
            Kembali ke Arsip
        </a>

        <article class="bg-white rounded-2xl shadow-xl overflow-hidden border border-primary/5">
            <!-- Gambar Utama -->
            <div class="h-[400px] w-full relative">
                <img src="{{ $article->full_image_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-primary/90 via-primary/40 to-transparent flex items-end">
                    <div class="p-8 md:p-12 w-full">
                        <span class="bg-accent text-white text-xs font-bold px-4 py-1.5 rounded-full uppercase tracking-wider mb-4 inline-block shadow-lg">
                            {{ $article->category ?? 'Sejarah' }}
                        </span>
                        <h1 class="text-3xl md:text-5xl font-bold text-white font-serif shadow-sm leading-tight">
                            {{ $article->title }}
                        </h1>
                    </div>
                </div>
            </div>

            <!-- Konten -->
            <div class="p-8 md:p-12">
                <!-- Meta Info -->
                <div class="flex items-center text-sm text-text-muted mb-8 pb-8 border-b border-gray-100">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Dipublikasikan pada: <span class="font-semibold text-primary">{{ $article->created_at->format('d F Y') }}</span></span>
                </div>

                <!-- Ringkasan (Lead) -->
                <div class="text-xl font-medium text-primary/90 mb-10 italic border-l-4 border-accent pl-6 leading-relaxed">
                    {{ $article->summary }}
                </div>

                <!-- Isi Lengkap (Typography Prose) -->
                <div class="prose prose-lg prose-green max-w-none text-text-dark leading-loose font-light">
                    {!! nl2br(e($article->content)) !!}
                </div>

                <!-- Footer Artikel -->
                <div class="mt-16 pt-8 border-t-2 border-primary/5 text-center">
                    <p class="text-sm text-text-muted italic">Yayasan Sajarah Timbanganten &copy; {{ date('Y') }}</p>
                </div>
            </div>
        </article>
    </div>
</div>
@endsection