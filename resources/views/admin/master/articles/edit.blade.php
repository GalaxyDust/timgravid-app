@extends('layouts.admin')

@section('title', 'Edit Artikel')

@section('content')
<div class="max-w-6xl mx-auto">
    
    <div class="mb-8">
        <a href="{{ route('articles.index') }}" class="text-slate-500 hover:text-slate-800 font-bold text-sm flex items-center gap-2 mb-2 transition w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar
        </a>
        <h1 class="text-3xl font-bold text-slate-800 font-serif">Edit Artikel</h1>
    </div>

    <form action="{{ route('articles.update', $article->id) }}" method="POST" enctype="multipart/form-data" 
          class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden"
          x-data="{ imagePreview: '{{ $article->image ? asset($article->image) : '' }}' }">
        @csrf
        @method('PUT')
        
        <div class="p-8 lg:p-10 space-y-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <div class="lg:col-span-3">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Judul Artikel</label>
                    <input type="text" name="title" value="{{ old('title', $article->title) }}" required class="w-full border-slate-300 rounded-xl shadow-sm focus:border-slate-800 focus:ring-slate-800 px-5 py-3.5 font-bold text-lg">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Kategori</label>
                    <select name="category" class="w-full border-slate-300 rounded-xl shadow-sm focus:border-slate-800 focus:ring-slate-800 px-4 py-3.5 font-medium bg-slate-50 cursor-pointer">
                        @foreach(['Tokoh', 'Sejarah', 'Bangunan', 'Umum'] as $cat)
                            <option value="{{ $cat }}" {{ $article->category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <div class="lg:col-span-1">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Thumbnail</label>
                    <div class="aspect-video bg-slate-50 border-2 border-dashed border-slate-300 rounded-xl flex items-center justify-center overflow-hidden relative group cursor-pointer hover:bg-slate-100 transition" @click="$refs.fileInput.click()">
                        <template x-if="!imagePreview">
                            <span class="text-xs text-slate-400">No Image</span>
                        </template>
                        <template x-if="imagePreview">
                            <img :src="imagePreview" class="w-full h-full object-cover">
                        </template>
                    </div>
                    <input x-ref="fileInput" type="file" name="image" accept="image/*" class="hidden" @change="imagePreview = URL.createObjectURL($event.target.files[0])">
                    <p class="text-xs text-slate-400 mt-2 text-center">Klik gambar untuk mengganti.</p>
                </div>

                <div class="lg:col-span-3">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Isi Konten</label>
                    <textarea name="content" rows="15" class="w-full border-slate-300 rounded-xl shadow-sm focus:border-slate-800 focus:ring-slate-800 px-5 py-4 leading-relaxed">{{ old('content', $article->content) }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-4 pt-4 border-t border-slate-100">
                <label class="flex items-center cursor-pointer gap-3 p-4 border border-slate-200 rounded-xl hover:bg-slate-50 transition w-full sm:w-auto">
                    <input type="checkbox" name="is_featured" value="1" {{ $article->is_featured ? 'checked' : '' }} class="rounded border-gray-300 text-slate-800 shadow-sm focus:border-slate-800 focus:ring focus:ring-slate-200 focus:ring-opacity-50 w-5 h-5 cursor-pointer">
                    <div class="flex flex-col">
                        <span class="text-sm font-bold text-slate-800">Jadikan Featured</span>
                        <span class="text-xs text-slate-500">Artikel akan muncul di halaman depan website.</span>
                    </div>
                </label>
            </div>

        </div>

        <div class="bg-slate-50 px-8 py-6 border-t border-slate-200 flex justify-end gap-4">
            <a href="{{ route('articles.index') }}" class="px-6 py-3 rounded-xl border border-slate-300 bg-white text-slate-700 font-bold hover:bg-slate-100 transition shadow-sm">Batal</a>
            <button type="submit" class="px-8 py-3 rounded-xl bg-slate-800 text-white font-bold hover:bg-slate-900 transition shadow-lg transform hover:-translate-y-0.5">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection