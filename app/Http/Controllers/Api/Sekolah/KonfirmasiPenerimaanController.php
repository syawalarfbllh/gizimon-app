<?php

namespace App\Http\Controllers\Api\Sekolah;

use App\Http\Controllers\Controller;
use App\Models\Distribusi;
use App\Models\KonfirmasiPenerimaan;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class KonfirmasiPenerimaanController extends Controller
{
    /**
     * Dapatkan profil sekolah yang sedang login.
     */
    private function getSekolah(): Sekolah
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->sekolah) {
            abort(403, 'Profil sekolah tidak ditemukan.');
        }
        return $user->sekolah;
    }

    /**
     * Menyimpan konfirmasi penerimaan baru.
     */
    public function store(Request $request, Distribusi $distribusi)
    {
        $sekolah = $this->getSekolah();
        
        /** @var \App\Models\User $user */
        $user = Auth::user(); // User sekolah yang login

        // 1. Otorisasi: Pastikan distribusi ini untuk sekolah yang login
        if ($distribusi->sekolah_id !== $sekolah->id) {
            return response()->json(['message' => 'Tidak diizinkan'], 403);
        }

        // 2. Validasi: Cek apakah sudah ada konfirmasi
        if ($distribusi->konfirmasiPenerimaan) {
            return response()->json(['message' => 'Konfirmasi untuk distribusi ini sudah ada.'], 400);
        }

        // 3. Validasi Input
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:diterima,ditolak',
            'catatan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 4. Buat Konfirmasi
        $konfirmasi = $distribusi->konfirmasiPenerimaan()->create([
            'user_id' => $user->id,
            'waktu_konfirmasi' => Carbon::now(),
            'status' => $request->status,
            'catatan' => $request->catatan,
        ]);

        // 5. Update status distribusi utama (opsional tapi bagus)
        if ($request->status == 'diterima') {
            $distribusi->update(['status' => 'diterima']);
        }

        return response()->json($konfirmasi, 201);
    }

    /**
     * Update konfirmasi penerimaan (jika diizinkan).
     */
    public function update(Request $request, KonfirmasiPenerimaan $konfirmasi)
    {
        $sekolah = $this->getSekolah();
        
        // 1. Otorisasi: Pastikan konfirmasi ini dibuat oleh user sekolah ini
        if ($konfirmasi->user_id !== Auth::id()) {
             return response()->json(['message' => 'Tidak diizinkan'], 403);
        }
        
        // Cek juga apakah konfirmasi ini terkait dengan sekolahnya
        // (melalui distribusi)
        if ($konfirmasi->distribusi->sekolah_id !== $sekolah->id) {
            return response()->json(['message' => 'Tidak diizinkan (data tidak cocok)'], 403);
        }

        // 2. Validasi Input
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:diterima,ditolak',
            'catatan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 3. Update Konfirmasi
        $konfirmasi->update([
            'status' => $request->status,
            'catatan' => $request->catatan,
            'waktu_konfirmasi' => Carbon::now(), // Catat waktu update
        ]);
        
        // 4. Update status distribusi utama (opsional tapi bagus)
        if ($request->status == 'diterima') {
            $konfirmasi->distribusi->update(['status' => 'diterima']);
        } else {
             // Jika status diubah kembali jadi 'ditolak'
             $konfirmasi->distribusi->update(['status' => 'dikirim']); // atau status lain yg sesuai
        }

        return response()->json($konfirmasi, 200);
    }
}