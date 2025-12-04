<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GraveBlock;
use App\Models\HistoricalArticle;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MasterDataController extends Controller
{
    // =====================================================================
    // 1. MANAJEMEN BLOK MAKAM (PETA GRID)
    // =====================================================================

    /**
     * Menampilkan Peta Blok berdasarkan Lokasi
     */
    public function blockIndex(Request $request)
    {
        $locations = Location::orderBy('name')->get();
        $selectedLocationCode = $request->query('location', $locations->first()->code ?? null);
        
        $blocks = [];
        if ($selectedLocationCode) {
            $blocks = GraveBlock::where('location_code', $selectedLocationCode)
                // Load relasi order yang aktif untuk tombol detail
                ->with(['orders' => function($q) {
                    $q->where('status', 'active')->with('customer');
                }])
                ->orderBy('id', 'asc')
                ->get();
        }

        return view('admin.master.blocks.index', compact('locations', 'selectedLocationCode', 'blocks'));
    }

    /**
     * Menambah Blok Baru Secara Manual
     */
    public function blockStore(Request $request)
    {
        $request->validate([
            'id' => 'required|unique:grave_blocks,id|max:10', // ID harus unik (misal: DK-999)
            'location_code' => 'required|exists:locations,code',
        ]);

        GraveBlock::create([
            'id' => strtoupper($request->id), // Paksa huruf besar
            'location_code' => $request->location_code,
            'status' => 'available', // Default status: Tersedia
            'last_burial_date' => null
        ]);

        return back()->with('success', 'Blok makam baru berhasil ditambahkan.');
    }

    /**
     * Update Status Blok (Tersedia / Terisi / Reserved)
     */
    public function blockUpdate(Request $request, GraveBlock $block)
    {
        $request->validate([
            'status' => 'required|in:available,occupied_1,occupied_2,occupied_3,reserved',
        ]);

        $block->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Status blok berhasil diperbarui.');
    }

    /**
     * Hapus Blok Makam
     */
    public function blockDestroy(GraveBlock $block)
    {
        try {
            // Blok hanya bisa dihapus jika tidak terikat relasi (optional, tergantung setting database)
            $block->delete();
            return back()->with('success', 'Blok makam berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus blok. Pastikan blok ini tidak sedang digunakan dalam pesanan.']);
        }
    }


    // =====================================================================
    // 2. MANAJEMEN ARTIKEL SEJARAH
    // =====================================================================

    public function articleIndex()
    {
        $articles = HistoricalArticle::latest()->paginate(10);
        return view('admin.master.articles.index', compact('articles'));
    }

    public function articleCreate()
    {
        return view('admin.master.articles.create');
    }

public function articleStore(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'category' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            
            // FORMAT BARU: kategori_judul_tanggal
            $slugCategory = Str::slug($validated['category'], '_');
            $slugTitle = Str::slug($validated['title'], '_');
            $date = now()->format('Ymd'); // TahunBulanTanggal
            
            $fileName = "{$slugCategory}_{$slugTitle}_{$date}." . $file->getClientOriginalExtension();
            
            // Simpan ke folder: storage/app/public/images
            $path = $file->storeAs('images', $fileName, 'public'); 
        }

        HistoricalArticle::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'summary' => Str::limit(strip_tags($validated['content']), 150),
            'content' => $validated['content'],
            'category' => $validated['category'],
            'image' => $path, // Contoh hasil: images/sejarah_solar_20251204.jpg
            'is_published' => true,
            'is_featured' => $request->has('is_featured'),
        ]);

        return redirect()->route('articles.index')->with('success', 'Artikel berhasil diterbitkan.');
    }

    public function articleUpdate(Request $request, HistoricalArticle $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'category' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($article->image && Storage::disk('public')->exists($article->image)) {
                Storage::disk('public')->delete($article->image);
            }

            $file = $request->file('image');
            
            // FORMAT BARU: kategori_judul_tanggal
            $slugCategory = Str::slug($validated['category'], '_');
            $slugTitle = Str::slug($validated['title'], '_');
            $date = now()->format('Ymd');
            
            $fileName = "{$slugCategory}_{$slugTitle}_{$date}." . $file->getClientOriginalExtension();
            
            $path = $file->storeAs('images', $fileName, 'public');
            
            $article->image = $path;
        }

        $article->update([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'summary' => Str::limit(strip_tags($validated['content']), 150),
            'content' => $validated['content'],
            'category' => $validated['category'],
            'is_featured' => $request->has('is_featured'),
        ]);

        return redirect()->route('articles.index')->with('success', 'Artikel berhasil diperbarui.');
    }

    public function articleEdit(HistoricalArticle $article)
    {
        return view('admin.master.articles.edit', compact('article'));
    }

    public function articleDestroy(HistoricalArticle $article)
    {
        if ($article->image && Storage::disk('public')->exists($article->image)) {
            Storage::disk('public')->delete($article->image);
        }
        $article->delete();
        return back()->with('success', 'Artikel berhasil dihapus.');
    }
}