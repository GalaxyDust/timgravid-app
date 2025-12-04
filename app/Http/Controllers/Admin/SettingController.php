<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SettingController extends Controller
{
    /**
     * Menampilkan halaman form pengaturan.
     */
    public function index(): View
    {
        // Ambil semua setting dari database, lalu ubah menjadi format 'key' => 'value'
        // Contoh: ['initial_fee' => '500000', 'annual_fee' => '150000']
        $settings = Setting::all()->pluck('value', 'key');
        
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Menyimpan perubahan pengaturan.
     */
    public function update(Request $request): RedirectResponse
    {
        // Validasi input (opsional, tapi disarankan)
        $request->validate([
            'initial_fee' => 'required|numeric|min:0',
            'annual_fee' => 'required|numeric|min:0',
        ]);

        $dataToUpdate = $request->except('_token', '_method');

        // Loop setiap data yang dikirim dari form
        foreach ($dataToUpdate as $key => $value) {
            // Gunakan updateOrCreate: jika key sudah ada -> update, jika belum -> buat baru.
            Setting::updateOrCreate(
                ['key' => $key], // Kondisi pencarian
                ['value' => $value] // Data yang diupdate/dibuat
            );
        }

        return back()->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }
}