<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\GraveBlock;
use App\Models\Invoice;
use App\Models\Location;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar pesanan dengan fitur pencarian.
     */
    public function index(Request $request): View
    {
        $query = Order::with(['customer', 'block.location', 'creator'])->latest();

        // 1. Filter Pencarian Teks (Tetap)
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('deceased_name', 'like', "%{$search}%")
                  ->orWhere('block_id', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn($subq) => $subq->where('name', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%"));
            });
        }

        // 2. [BARU] Filter Status Pembayaran
        if ($paymentStatus = $request->query('payment_status')) {
            if ($paymentStatus === 'paid') {
                // Jika filter 'Lunas', cari pesanan yang TIDAK PUNYA tagihan selain 'paid'.
                $query->whereDoesntHave('invoices', function($q) {
                    $q->where('status', '!=', 'paid');
                });
            } else {
                // Jika filter lain (unpaid, waiting_approval, rejected), cari yang punya tagihan dengan status tsb.
                $query->whereHas('invoices', function ($q) use ($paymentStatus) {
                    $q->where('status', $paymentStatus);
                });
            }
        }

        $orders = $query->paginate(10)->withQueryString();

        // Kirim nilai filter ke view
        return view('admin.orders.index', compact('orders', 'search', 'paymentStatus'));
    }
    /**
     * Menampilkan form buat pesanan baru.
     */
    public function create(): View
    {
        $locations = Location::orderBy('name')->get();
        return view('admin.orders.create', compact('locations'));
    }

    /**
     * Menyimpan pesanan baru ke database.
     * Termasuk logika: Update Customer, Cek Blok, Upload Bukti, Buat Invoice (DP/Lunas).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pj_name' => 'required|string|max:255',
            'pj_nik' => 'required|numeric|digits:16',
            'pj_phone' => 'required|string|max:20',
            'pj_address' => 'required|string',
            'deceased_name' => 'required|string|max:255',
            'burial_date' => 'required|date',
            'block_id' => 'required|exists:grave_blocks,id',
            'initial_payment' => 'required|numeric|min:0',
            'proof_file' => [
                'nullable',
                Rule::requiredIf(fn() => $request->input('initial_payment') > 0),
                'image', 'mimes:jpg,jpeg,png', 'max:2048'
            ],
        ], ['proof_file.required' => 'Unggah bukti pembayaran wajib jika ada pembayaran awal.']);

        DB::beginTransaction();
        try {
            // 1. Update/Create Customer berdasarkan NIK
            $customer = Customer::updateOrCreate(
                ['nik' => $validated['pj_nik']],
                ['name' => $validated['pj_name'], 'phone_number' => $validated['pj_phone'], 'address' => $validated['pj_address']]
            );

            // 2. Validasi Ketersediaan Blok (Cek tumpuk/kepemilikan)
            $block = GraveBlock::find($validated['block_id']);
            $isOwned = Order::where('customer_id', $customer->id)
                            ->where('block_id', $block->id)
                            ->where('status', 'active')
                            ->exists();
            
            // Jika blok tidak tersedia DAN bukan milik pelanggan yang sama (untuk penumpukan)
            if ($block->status !== 'available' && !$isOwned) {
                throw new \Exception('Blok makam ini sudah terisi oleh orang lain.');
            }

            // 3. Buat Pesanan Utama
            $order = Order::create([
                'customer_id' => $customer->id, 
                'block_id' => $validated['block_id'], 
                'deceased_name' => $validated['deceased_name'],
                'burial_date' => $validated['burial_date'], 
                'relationship' => 'Keluarga', 
                'status' => 'active', 
                'created_by' => Auth::id(),
            ]);

            // 4. Update Status Blok (Meningkat level hunian)
            $newStatus = match ($block->status) {
                'available' => 'occupied_1', 
                'occupied_1' => 'occupied_2', 
                'occupied_2' => 'occupied_3', 
                default => $block->status
            };
            $block->update(['status' => $newStatus, 'last_burial_date' => $validated['burial_date']]);

            // 5. LOGIKA HARGA & PEMBAYARAN
            
            // Ambil harga dari tabel Settings (jika ada), default 500.000
            $settingPrice = Setting::where('key', 'initial_fee')->value('value');
            $totalFee = $settingPrice ? (int)$settingPrice : 500000; 

            $paidAmount = (int) $validated['initial_payment'];
            $remainingAmount = $totalFee - $paidAmount;

            // Handle File Upload (Rename File agar rapi)
            $proofPath = null;
            if ($paidAmount > 0 && $request->hasFile('proof_file')) {
                $file = $request->file('proof_file');
                
                // Format: awal_namapj_namajenazah_YYYYMMDDHHiiss.ext
                $pjName = Str::slug($validated['pj_name'], '_');
                $deceasedName = Str::slug($validated['deceased_name'], '_');
                $date = now()->format('YmdHis'); 
                
                $fileName = "awal_{$pjName}_{$deceasedName}_{$date}." . $file->getClientOriginalExtension();
                $proofPath = $file->storeAs('proofs', $fileName, 'public');
            }

            // Logika Pembuatan Invoice berdasarkan jumlah bayar
            if ($paidAmount == 0) {
                // A. Belum Bayar Sama Sekali
                Invoice::create([
                    'order_id' => $order->id,
                    'type' => 'initial_fee',
                    'description' => 'Biaya Pemesanan Awal Makam (Full)',
                    'amount' => $totalFee,
                    'due_date' => now()->addDays(7),
                    'status' => 'unpaid',
                ]);

            } elseif ($remainingAmount <= 0) {
                // B. Lunas Langsung (Full Payment)
                Invoice::create([
                    'order_id' => $order->id,
                    'type' => 'initial_fee',
                    'description' => 'Biaya Pemesanan Awal Makam (Lunas)',
                    'amount' => $paidAmount,
                    'due_date' => now()->addDays(7),
                    'status' => 'waiting_approval',
                    'payment_method' => 'Tunai (diinput Admin)',
                    'paid_at' => now(),
                    'proof_file' => $proofPath
                ]);

            } else {
                // C. Bayar Parsial (DP + Sisa Tagihan)
                
                // Invoice 1: DP (Status Waiting Approval)
                Invoice::create([
                    'order_id' => $order->id,
                    'type' => 'initial_fee',
                    'description' => 'Pembayaran Awal (DP / Uang Muka)',
                    'amount' => $paidAmount,
                    'due_date' => now(),
                    'status' => 'waiting_approval',
                    'payment_method' => 'Tunai (diinput Admin)',
                    'paid_at' => now(),
                    'proof_file' => $proofPath
                ]);

                // Invoice 2: Sisa Tagihan (Status Unpaid)
                Invoice::create([
                    'order_id' => $order->id,
                    'type' => 'initial_fee',
                    'description' => 'Pelunasan Biaya Pemesanan (Sisa)',
                    'amount' => $remainingAmount,
                    'due_date' => now()->addDays(14),
                    'status' => 'unpaid',
                ]);
            }

            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Pesanan atas nama '. $validated['deceased_name'] .' berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Menampilkan detail pesanan.
     */
    public function show(Order $order): View
    {
        // Load data relasi untuk ditampilkan di View
        $order->load(['customer', 'block.location', 'creator', 'invoices.approver']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Memperbarui data dasar pesanan (Nama Jenazah & Tgl Makam).
     */
    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'deceased_name' => 'required|string|max:255',
            'burial_date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $order->update([
                'deceased_name' => $validated['deceased_name'],
                'burial_date' => $validated['burial_date'],
            ]);
            
            // Update juga tanggal makam terakhir di data blok
            $order->block->update(['last_burial_date' => $validated['burial_date']]);

            DB::commit();
            return redirect()->route('orders.show', $order->id)->with('success', 'Data pesanan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    /**
     * [FITUR BARU] Membuat Tagihan Manual (Misal: Iuran Tahunan).
     * Diakses dari tombol "Buat Tagihan Iuran" di halaman Show.
     */
    public function createInvoice(Request $request, $orderId): RedirectResponse
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $order = Order::findOrFail($orderId);

        Invoice::create([
            'order_id' => $order->id,
            'description' => $request->description,
            'amount' => $request->amount,
            
            // PERBAIKAN: Tambahkan Tanggal Jatuh Tempo
            'due_date' => now()->addDays(30), // Jatuh tempo 30 hari dari sekarang
            
            'status' => 'unpaid',
            'type' => 'annual_fee', // Asumsi ini tagihan tahunan
        ]);

        return back()->with('success', 'Tagihan baru berhasil dibuat.');
    }

    // =========================================================
    // API / AJAX HELPER FUNCTIONS
    // =========================================================

    /**
     * API untuk autofill data pelanggan berdasarkan NIK.
     */
    public function checkCustomer(Request $request): JsonResponse
    {
        $nik = $request->query('nik');
        if ($nik && strlen($nik) === 16) {
            $customer = Customer::where('nik', $nik)->first();
            if ($customer) {
                return response()->json([
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone_number' => $customer->phone_number,
                    'address' => $customer->address
                ]);
            }
        }
        return response()->json(null);
    }

    /**
     * API untuk mengambil data denah blok berdasarkan lokasi.
     * Mengatur status visual (occupied/available/owned).
     */
    public function getBlockData(Request $request): JsonResponse
    {
        $locationCode = $request->query('location');
        $customerNik = $request->query('customer_nik');
        
        if (!$locationCode) return response()->json([]);

        // Cek apakah user ini punya blok di lokasi ini (untuk fitur tumpuk makam)
        $ownedBlocks = [];
        if (strlen($customerNik) === 16) {
            $customer = Customer::where('nik', $customerNik)->first();
            if ($customer) {
                $ownedBlocks = Order::where('customer_id', $customer->id)
                    ->where('status', 'active')
                    ->pluck('block_id')
                    ->toArray();
            }
        }

        $blocks = GraveBlock::where('location_code', $locationCode)
            ->orderBy('id')
            ->get()
            ->map(function ($block) use ($ownedBlocks) {
                // Logika Status Visual Peta
                if (in_array($block->id, $ownedBlocks)) {
                    $block->map_status = 'owned'; // Milik sendiri (Bisa ditumpuk)
                    $block->is_clickable = true; 
                } elseif ($block->status === 'available') {
                    $block->map_status = 'available'; // Kosong
                    $block->is_clickable = true;
                } else {
                    $block->map_status = 'occupied'; // Punya orang lain
                    $block->is_clickable = false;
                }
                return $block;
            });
        
        return response()->json($blocks);
    }

}