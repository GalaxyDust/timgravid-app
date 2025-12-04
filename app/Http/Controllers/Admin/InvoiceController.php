<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    // =================================================================
    // 1. HALAMAN DAFTAR TAGIHAN (Hanya yang Status 'waiting_approval')
    // =================================================================
    public function index()
    {
        $invoices = Invoice::with(['order.customer', 'order.block'])
            ->where('status', 'waiting_approval')
            ->orderBy('paid_at', 'asc') // Yang bayar duluan ditaruh atas
            ->get();

        return view('admin.invoices.approval', compact('invoices'));
    }

    // =================================================================
    // 2. PROSES KEPUTUSAN (TERIMA / TOLAK)
    // =================================================================
    public function decide(Request $request, Invoice $invoice)
    {
        $request->validate([
            'decision' => 'required|in:approve,reject',
            'note' => 'nullable|string|max:255' // Catatan opsional (misal alasan penolakan)
        ]);

        if ($request->decision === 'approve') {
            // JIKA DITERIMA
            $invoice->update([
                'status' => 'paid',
                'approved_by' => Auth::id(),
                'admin_note' => $request->note ?? 'Pembayaran diverifikasi valid.',
            ]);

            // (Opsional) Kirim Notif WA disini nanti
            Log::info("Invoice #{$invoice->id} APPROVED by " . Auth::user()->name);

            return back()->with('success', 'Pembayaran berhasil diverifikasi & status pesanan LUNAS.');

        } else {
            // JIKA DITOLAK
            $invoice->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'admin_note' => $request->note ?? 'Bukti pembayaran tidak valid / buram.',
            ]);

            Log::info("Invoice #{$invoice->id} REJECTED by " . Auth::user()->name);

            return back()->with('success', 'Pembayaran ditolak. Admin diminta upload ulang.');
        }
    }

    // Method uploadProof (Upload Ulang) tetap sama seperti sebelumnya...
    public function uploadProof(Request $request, Invoice $invoice)
    {
        $request->validate([ 'proof_file' => 'required|image|mimes:jpg,jpeg,png|max:2048' ]);

        if ($request->hasFile('proof_file')) {
            if ($invoice->proof_file && Storage::disk('public')->exists($invoice->proof_file)) {
                Storage::disk('public')->delete($invoice->proof_file);
            }

            $file = $request->file('proof_file');
            // Format Nama: iuran_namapj_namajenazah_tanggal
            $pjName = \Illuminate\Support\Str::slug($invoice->order->customer->name ?? 'na', '_');
            $deceasedName = \Illuminate\Support\Str::slug($invoice->order->deceased_name ?? 'na', '_');
            $date = now()->format('YmdHis'); 
            
            $fileName = "iuran_{$pjName}_{$deceasedName}_{$date}." . $file->getClientOriginalExtension();
            $path = $file->storeAs('proofs', $fileName, 'public');

            $invoice->update([
                'proof_file' => $path,
                'status' => 'waiting_approval', 
                'payment_method' => 'Transfer/Tunai (Re-upload)',
                'paid_at' => now(),
            ]);
        }
        return back()->with('success', 'Bukti berhasil diupload ulang.');
    }

        /**
     * Update deskripsi invoice (opsional)
     */
    public function update(Request $request, Invoice $invoice)
    {
        $request->validate(['description' => 'required|string|max:255']);

        // Hanya boleh edit jika statusnya unpaid
        if ($invoice->status === 'unpaid') {
            $invoice->update(['description' => $request->description]);
            return back()->with('success', 'Deskripsi tagihan berhasil diubah.');
        }
        return back()->withErrors(['error' => 'Tagihan yang sudah diproses tidak bisa diedit.']);
    }

    /**
     * Hapus invoice (jika salah input)
     */
    public function destroy(Invoice $invoice)
    {
        // Hanya boleh hapus jika statusnya unpaid
        if ($invoice->status === 'unpaid') {
            $invoice->delete();
            return back()->with('success', 'Tagihan berhasil dibatalkan/dihapus.');
        }
        return back()->withErrors(['error' => 'Tagihan yang sudah diproses tidak bisa dihapus.']);
    }
}