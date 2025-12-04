@extends('layouts.admin')

@section('title', 'Artikel Sejarah')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-3xl font-bold text-slate-800 font-serif">Artikel Sejarah</h1>
        <p class="text-sm text-slate-500 mt-1">Kelola konten publikasi sejarah dan tokoh makam.</p>
    </div>
    
    <a href="{{ route('articles.create') }}" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-3 rounded-xl font-bold text-sm transition shadow-lg flex items-center gap-2 transform hover:-translate-y-0.5">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tulis Artikel Baru
    </a>
</div>

<!-- GRID CARD SYSTEM -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    @forelse($articles as $article)
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col h-full group">
            
            <!-- Gambar Thumbnail (Fixed Aspect Ratio 16:9) -->
            <div class="relative w-full aspect-video bg-slate-100 overflow-hidden">
                @if($article->image)
                    {{-- FIX: Menggunakan asset('storage/') agar gambar muncul --}}
                    <img src="{{ asset('storage/' . $article->image) }}" 
                         class="w-full h-full object-cover transition duration-700 group-hover:scale-110" 
                         alt="{{ $article->title }}"
                         onerror="this.src='https://via.placeholder.com/400x300?text=No+Image'">
                @else
                    <div class="w-full h-full flex items-center justify-center text-slate-300 bg-slate-50">
                        <svg class="w-12 h-12 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                @endif
                
                <!-- Badge Kategori -->
                <div class="absolute top-3 left-3 bg-black/60 backdrop-blur-md text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm">
                    {{ $article->category ?? 'Umum' }}
                </div>

                <!-- Badge Featured -->
                @if($article->is_featured)
                    <div class="absolute top-3 right-3 bg-amber-400 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.603 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        FEATURED
                    </div>
                @endif
            </div>

            <!-- Konten -->
            <div class="flex-1 p-6 flex flex-col">
                <h3 class="text-lg font-bold text-slate-800 font-serif leading-snug mb-3 line-clamp-2 group-hover:text-blue-700 transition h-[3.5rem]">
                    {{ $article->title }}
                </h3>
                
                <p class="text-sm text-slate-500 leading-relaxed line-clamp-3 mb-6 flex-1">
                    {{ $article->summary ?? Str::limit(strip_tags($article->content), 100) }}
                </p>

                <!-- Footer Card -->
                <div class="pt-5 border-t border-slate-100 flex items-center justify-between mt-auto gap-3">
                    <div class="text-xs text-slate-400 font-medium w-1/3">
                        {{ $article->created_at->format('d M Y') }}
                    </div>
                    
                    <div class="flex items-center gap-2 w-2/3 justify-end">
                        <!-- Edit Button -->
                        <a href="{{ route('articles.edit', $article->id) }}" class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-2 rounded-lg text-xs font-bold transition flex items-center justify-center gap-1.5 border border-blue-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit
                        </a>
                        
                        <!-- Delete Button -->
                        <form action="{{ route('articles.destroy', $article->id) }}" method="POST" onsubmit="return confirm('Hapus artikel ini?');" class="flex-1">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-full bg-red-50 hover:bg-red-100 text-red-600 px-3 py-2 rounded-lg text-xs font-bold transition flex items-center justify-center gap-1.5 border border-red-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full py-20 text-center bg-white rounded-2xl border-2 border-dashed border-slate-200">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800">Belum ada artikel</h3>
            <p class="text-slate-500 mt-1">Mulai tulis konten sejarah sekarang.</p>
            <a href="{{ route('articles.create') }}" class="inline-block mt-4 text-blue-600 font-bold hover:underline">Buat Artikel Pertama &rarr;</a>
        </div>
    @endforelse
</div>

<div class="mt-10">
    {{ $articles->links() }}
</div>
@endsection