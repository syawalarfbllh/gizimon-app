<?php

namespace App\Http\Controllers\Api\Penyedia;

use App\Http\Controllers\Controller;
use App\Models\Distribusi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Penyedia; // Import model Penyedia
use App\Models\MenuMbg; // Import MenuMbg
use App\Models\Sekolah; // Import Sekolah

class DistribusiPenyediaController extends Controller
{
    /**
     * Dapatkan profil penyedia yang sedang login.
     */
    private function getPenyedia(): Penyedia
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->penyedia) {
            abort(403, 'Profil penyedia tidak ditemukan.');
        }
        return $user->penyedia;
    }

    /**
     * Menampilkan semua distribusi milik penyedia yang login.
     */
    public function index(Request $request)
    {
        // Ambil semua distribusi, load relasi penting
        $distribusi = Distribusi::with([
            // --- PERBAIKAN: Gunakan 'dot notation' ---
            'penyedia.user',  // Ambil 'penyedia' DAN 'user' di dalamnya
            'sekolah.user',   // Ambil 'sekolah' DAN 'user' di dalamnya
            'menu_mbg', 
            'konfirmasi_penerimaan'
            // --- AKHIR PERBAIKAN ---
        ])
        ->latest('tanggal_distribusi')
        ->get();
        
        return response()->json($distribusi, 200);
    }
    /**
     * Menyimpan data distribusi baru.
     */
    public function store(Request $request)
    {
        $penyedia = $this->getPenyedia();

        $validator = Validator::make($request->all(), [
            'sekolah_id' => 'required|exists:sekolahs,id',
            'menu_id' => 'required|exists:menu_mbgs,id',
            'tanggal_distribusi' => 'required|date|date_format:Y-m-d',
            'jumlah_paket' => 'required|integer|min:1',
            'status' => 'required|in:disiapkan,dikirim,dibatalkan', // Status awal
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Validasi ekstra: Pastikan menu_id adalah milik penyedia ini
        $menu = MenuMbg::find($request->menu_id);
        if ($menu->penyedia_id !== $penyedia->id) {
            return response()->json(['message' => 'Menu yang dipilih bukan milik Anda.'], 403);
        }

        // Buat distribusi baru
        $distribusi = $penyedia->distribusis()->create($request->all());
        $distribusi->load(['sekolah', 'menuMbg']); // Load relasi untuk respon

        return response()->json($distribusi, 201);
    }

    /**
     * Menampilkan satu data distribusi.
     */
    public function show(Distribusi $distribusi)
    {
        // Supervisor bisa melihat detail apapun
        $distribusi->load([
            'penyedia.user', 
            'sekolah.user',
            'menu_mbg.bahanMakanans', 
            'konfirmasi_penerimaan', 
            'laporanNutrisi', 
            'laporanMonitoring'
        ]);

        return response()->json($distribusi, 200);
    }

    /**
     * Update data distribusi (misal: ubah status, jumlah, dll)
     */
    public function update(Request $request, Distribusi $distribusi)
    {
        $penyedia = $this->getPenyedia();

        // Pastikan distribusi ini milik penyedia yang login
        if ($distribusi->penyedia_id !== $penyedia->id) {
            return response()->json(['message' => 'Tidak diizinkan'], 403);
        }
        
        // Penyedia mungkin hanya boleh update status, bukan data utama
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:disiapkan,dikirim,dibatalkan', // Status yang bisa diubah penyedia
            
            // Opsional: Izinkan update data lain jika belum dikonfirmasi
            'sekolah_id' => 'sometimes|required|exists:sekolahs,id',
            'menu_id' => 'sometimes|required|exists:menu_mbgs,id',
            'tanggal_distribusi' => 'sometimes|required|date|date_format:Y-m-d',
            'jumlah_paket' => 'sometimes|required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Cek lagi jika menu diubah
        if ($request->has('menu_id')) {
            $menu = MenuMbg::find($request->menu_id);
            if ($menu->penyedia_id !== $penyedia->id) {
                return response()->json(['message' => 'Menu yang dipilih bukan milik Anda.'], 403);
            }
        }
        
        $distribusi->update($request->all());
        $distribusi->load(['sekolah', 'menuMbg']); // Load relasi untuk respon

        return response()->json($distribusi, 200);
    }

    /**
     * Hapus data distribusi.
     */
    public function destroy(Distribusi $distribusi)
    {
        $penyedia = $this->getPenyedia();

        // Pastikan distribusi ini milik penyedia yang login
        if ($distribusi->penyedia_id !== $penyedia->id) {
            return response()->json(['message' => 'Tidak diizinkan'], 403);
        }

        // Aturan bisnis: Mungkin tidak boleh dihapus jika sudah diterima sekolah?
        if ($distribusi->konfirmasiPenerimaan && $distribusi->konfirmasiPenerimaan->status == 'diterima') {
            return response()->json(['message' => 'Hapus gagal. Distribusi sudah dikonfirmasi sekolah.'], 400);
        }

        // Hapus semua laporan terkait dulu (jika ada)
        $distribusi->konfirmasiPenerimaan()->delete();
        $distribusi->laporanNutrisi()->delete();
        $distribusi->laporanMonitoring()->delete();
        
        $distribusi->delete();

        return response()->json(['message' => 'Distribusi berhasil dihapus'], 200);
    }
}