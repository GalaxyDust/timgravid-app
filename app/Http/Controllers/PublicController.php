<?php

namespace App\Http\Controllers;

use App\Models\GraveBlock;
use App\Models\HistoricalArticle;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function index(): View
    {
        // 1. Data Lokasi untuk Dropdown Peta
        $locations = Location::all(); 

        // 2. Artikel Unggulan (Featured & Published Only)
        $featuredArticles = HistoricalArticle::where('is_published', true)
            ->latest()
            ->take(3)
            ->get();

        return view('landing.index', compact('locations', 'featuredArticles'));
    }

    public function showArticle(HistoricalArticle $article): View
    {
        // Cegah akses artikel draft via URL langsung
        if (!$article->is_published) {
            abort(404);
        }
        return view('landing.article', compact('article'));
    }

    // API Peta (Untuk JS di Landing Page)
    public function getBlockStatus(Request $request)
    {
        $blocks = GraveBlock::select('id', 'location_code', 'status')
            ->where('location_code', $request->query('location'))
            ->get();

        return response()->json($blocks);
    }
}