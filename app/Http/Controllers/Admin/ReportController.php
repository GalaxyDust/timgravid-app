<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GraveBlock;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Halaman Utama Laporan (Dashboard Statistik).
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // =============================================================
        // BAGIAN 1: DATA UNTUK KPI CARDS & TRANSAKSI TERAKHIR
        // =============================================================
        
        // 1. Filter Tanggal
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // 2. Statistik Keuangan
        $totalRevenue = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->sum('amount');

        // 3. Statistik Pesanan Baru
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();

        // 4. Ketersediaan Makam
        $totalBlocks = GraveBlock::count();
        $availableBlocks = GraveBlock::where('status', 'available')->count();
        $occupiedBlocks = $totalBlocks - $availableBlocks;
        $occupancyRate = $totalBlocks > 0 ? round(($occupiedBlocks / $totalBlocks) * 100, 1) : 0;

        // 5. Transaksi Terakhir
        $recentTransactions = Invoice::with(['order.customer'])
            ->where('status', 'paid')
            ->latest('paid_at')
            ->take(5)
            ->get();

        // =============================================================
        // BAGIAN 2: DATA UNTUK GRAFIK
        // =============================================================

        // 6. Data Grafik Pesanan Baru (12 Bulan Terakhir)
        $ordersByMonth = Order::select(
                DB::raw('COUNT(*) as count'), 
                DB::raw("DATE_FORMAT(created_at, '%b %Y') as month") // Format: Jan 2025
            )
            ->where('created_at', '>=', now()->subYear())
            ->groupBy('month')
            ->orderByRaw('MIN(created_at) ASC') // Urutkan berdasarkan tanggal asli
            ->get();
        
        $orderLabels = $ordersByMonth->pluck('month');
        $orderData = $ordersByMonth->pluck('count');

        // 7. Data Grafik Komposisi Lahan
        $blocksByStatus = GraveBlock::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
        
        $blockLabels = $blocksByStatus->map(function($item) {
            $statusName = Str::ucfirst(str_replace('_', ' ', $item->status));
            if (Str::startsWith($item->status, 'occupied')) {
                $level = str_replace('occupied_', '', $item->status);
                return "Terisi (Lapis $level)";
            }
            return $statusName;
        });
        $blockData = $blocksByStatus->pluck('count');

        // =============================================================
        // KIRIM SEMUA DATA KE VIEW
        // =============================================================
        return view('admin.reports.index', compact(
            'totalRevenue', 
            'totalOrders', 
            'totalBlocks', 
            'availableBlocks', 
            'occupancyRate', 
            'recentTransactions',
            'startDate',
            'endDate',
            'orderLabels',
            'orderData',
            'blockLabels',
            'blockData'
        ));
    }

    /**
     * Export Data Pesanan ke CSV
     *
     * @param Request $request
     * @return StreamedResponse
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        // Ambil filter tanggal dari URL
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Nama file
        $fileName = 'laporan_pesanan_' . $startDate . '_sampai_' . $endDate . '.csv';

        // Ambil data yang akan diexport
        $orders = Order::with(['customer', 'block.location'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0'
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');

            // Header Kolom CSV
            fputcsv($file, [
                'ID Pesanan',
                'Nama Jenazah',
                'Tanggal Dimakamkan',
                'Blok Makam',
                'Lokasi TPU',
                'Nama Penanggung Jawab',
                'NIK PJ',
                'No. HP PJ',
                'Tanggal Input',
            ]);

            // Isi Data Baris
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->deceased_name,
                    $order->burial_date->format('d-m-Y'),
                    $order->block_id,
                    $order->block->location->name ?? 'N/A',
                    $order->customer->name ?? 'N/A',
                    $order->customer->nik ?? 'N/A',
                    $order->customer->phone_number ?? 'N/A',
                    $order->created_at->format('d-m-Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}