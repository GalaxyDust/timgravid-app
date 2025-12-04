<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\GraveBlock;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $role = $user->role;

        // Data Statistik Umum
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $stats = [
            'orders_this_month' => Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
            'revenue_this_month' => Invoice::where('status', 'paid')->whereBetween('paid_at', [$startOfMonth, $endOfMonth])->sum('amount'),
            'pending_approvals' => Invoice::where('status', 'waiting_approval')->count(),
            'available_blocks' => GraveBlock::where('status', 'available')->count(),
        ];

        // --- LOGIKA NOTIFIKASI PER ROLE ---
        $notifications = [];

        // 1. APPROVER: Butuh Verifikasi Pembayaran (KUNING)
        if ($role === 'approver' || $role === 'super_admin') {
            $pendingCount = Invoice::where('status', 'waiting_approval')->count();
            if ($pendingCount > 0) {
                $notifications[] = [
                    'type' => 'warning', 
                    'message' => "Ada <b>{$pendingCount} tagihan</b> baru yang menunggu verifikasi pembayaran.",
                    'link' => route('invoices.approval'),
                    'link_text' => 'Verifikasi Sekarang'
                ];
            }
        }

        // 2. ADMIN/SUPER ADMIN: Tagihan Ditolak / Rejected (MERAH)
        if ($role === 'admin' || $role === 'super_admin') {
            $rejectedCount = Invoice::where('status', 'rejected')->count();
            if ($rejectedCount > 0) {
                $notifications[] = [
                    'type' => 'danger',
                    'message' => "Terdapat <b>{$rejectedCount} pembayaran ditolak</b>. Mohon hubungi PJ untuk upload ulang bukti.",
                    'link' => route('orders.index'),
                    'link_text' => 'Lihat Pesanan'
                ];
            }
        }

        // 3. ADMIN/SUPER ADMIN: Tagihan Belum Dibayar / Unpaid (ORANGE) -> [BARU]
        if ($role === 'admin' || $role === 'super_admin') {
            $unpaidCount = Invoice::where('status', 'unpaid')->count();
            if ($unpaidCount > 0) {
                $notifications[] = [
                    'type' => 'alert', // Tipe baru untuk unpaid
                    'message' => "Ada <b>{$unpaidCount} tagihan aktif</b> yang belum dibayar oleh pelanggan.",
                    'link' => route('orders.index'),
                    'link_text' => 'Follow Up Pelanggan'
                ];
            }
        }

        // 4. ADMIN/SUPER ADMIN: Pengingat Iuran Tahunan Jatuh Tempo (BIRU)
        if ($role === 'admin' || $role === 'super_admin') {
            $annualDueCount = Order::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', '<', Carbon::now()->year)
                ->where('status', 'active')
                ->whereDoesntHave('invoices', function($q) {
                    $q->where('type', 'annual_fee')
                      ->whereYear('created_at', Carbon::now()->year);
                })
                ->count();

            if ($annualDueCount > 0) {
                $notifications[] = [
                    'type' => 'info',
                    'message' => "Ada <b>{$annualDueCount} makam</b> yang jatuh tempo iuran tahunan bulan ini.",
                    'link' => route('orders.index'),
                    'link_text' => 'Cek Data'
                ];
            }
        }

        return view('dashboard.index', compact('user', 'stats', 'notifications'));
    }
}