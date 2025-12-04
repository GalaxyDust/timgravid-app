@extends('layouts.admin')

@section('title', 'Buat Artikel Baru')

@section('content')
<!-- Container Lebih Lebar (6xl) -->
<div class="max-w-6xl mx-auto">
    
    <div class="mb-8">
        <a href="{{ route('articles.index') }}" class="text-slate-500 hover:text-slate-800 font-bold text-sm flex items-center gap-2 mb-2 transition w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar
        </a>
        <h1 class="text-3xl font-bold text-slate-800 font-serif">Tulis Artikel Baru</h1>
    </div>

    <form action="{{ route('articles.store') }}" method="POST" enctype="multipart/form-data" 
          class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden"
          x-data="{ imagePreview: null }">
        @csrf
        
        <div class="p-8 lg:p-10 space-y-8">
            
            <!-- Judul & Kategori -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <div class="lg:col-span-3">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Judul Artikel</label>
                    <input type="text" name="title" required placeholder="Judul yang menarik..." class="w-full border-slate-300 rounded-xl shadow-sm focus:border-slate-800 focus:ring-slate-800 px-5 py-3.5 font-bold text-lg">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Kategori</label>
                    <select name="category" class="w-full border-slate-300 rounded-xl shadow-sm focus:border-slate-800 focus:ring-slate-800 px-4 py-3.5 font-medium bg-slate-50 cursor-pointer">
                        <option value="Tokoh">Tokoh</option>
                        <option value="Sejarah">Sejarah</option>
                        <option value="Bangunan">Bangunan/Arsitektur</option>
                        <option value="Umum">Umum</option>
                    </select>
                </div>
            </div>

            <!-- Upload Gambar -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <div class="lg:col-span-1">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Thumbnail</label>
                    <div class="aspect-video bg-slate-50 border-2 border-dashed border-slate-300 rounded-xl flex items-center justify-center overflow-hidden relative group cursor-pointer hover:bg-slate-100 transition" @click="$refs.fileInput.click()">
                        <template x-if="!imagePreview">
                            <div class="text-center p-4">
                                <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="text-xs text-slate-400 font-medium">Klik Upload</span>
                            </div>
                        </template>
                        <template x-if="imagePreview">
                            <img :src="imagePreview" class="w-full h-full object-cover">
                        </template>
                    </div>
                    <input x-ref="fileInput" type="file" name="image" accept="image/*" class="hidden" @change="imagePreview = URL.createObjectURL($event.target.files[0])">
                    <p class="text-xs text-slate-400 mt-2 text-center">Format: JPG, PNG (Max 2MB)</p>
                </div>

                <div class="lg:col-span-3">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Isi Konten</label>
                    <!-- Textarea Lebih Tinggi (rows=15) -->
                    <textarea name="content" rows="15" class="w-full border-slate-300 rounded-xl shadow-sm focus:border-slate-800 focus:ring-slate-800 px-5 py-4 leading-relaxed" placeholder="Tulis cerita lengkap di sini..."></textarea>
                </div>
            </div>

            <!-- Featured Checkbox -->
            <div class="flex items-center gap-4 pt-4 border-t border-slate-100">
                <label class="flex items-center cursor-pointer gap-3 p-4 border border-slate-200 rounded-xl hover:bg-slate-50 transition w-full sm:w-auto">
                    <input type="checkbox" name="is_featured" value="1" class="rounded border-gray-300 text-slate-800 shadow-sm focus:border-slate-800 focus:ring focus:ring-slate-200 focus:ring-opacity-50 w-5 h-5 cursor-pointer">
                    <div class="flex flex-col">
                        <span class="text-sm font-bold text-slate-800">Jadikan Featured</span>
                        <span class="text-xs text-slate-500">Artikel akan muncul di halaman depan website.</span>
                    </div>
                </label>
            </div>

        </div>

        <div class="bg-slate-50 px-8 py-6 border-t border-slate-200 flex justify-end gap-4">
            <a href="{{ route('articles.index') }}" class="px-6 py-3 rounded-xl border border-slate-300 bg-white text-slate-700 font-bold hover:bg-slate-100 transition shadow-sm">Batal</a>
            <button type="submit" class="px-8 py-3 rounded-xl bg-slate-800 text-white font-bold hover:bg-slate-900 transition shadow-lg transform hover:-translate-y-0.5">Terbitkan Artikel</button>
        </div>
    </form>
</div>
@endsection