<?php

namespace App\Http\Controllers\Api\Sekolah;

use App\Http\Controllers\Controller;
use App\Models\Distribusi;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DistribusiSekolahController extends Controller
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
     * Menampilkan semua distribusi yang ditujukan ke sekolah yang login.
     */
    public function index()
    {
        $sekolah = $this->getSekolah();

        $distribusi = $sekolah->distribusis() // Ambil dari relasi
                        ->with(['penyedia', 'menuMbg', 'konfirmasiPenerimaan'])
                        ->latest('tanggal_distribusi')
                        ->get();
        
        return response()->json($distribusi, 200);
    }

    /**
     * Menampilkan satu data distribusi spesifik.
     */
    public function show(Distribusi $distribusi)
    {
        $sekolah = $this->getSekolah();

        // Pastikan distribusi ini milik sekolah yang login
        if ($distribusi->sekolah_id !== $sekolah->id) {
            return response()->json(['message' => 'Tidak diizinkan'], 403);
        }

        // Load semua relasi terkait
        $distribusi->load([
            'penyedia', 
            'menuMbg.bahanMakanans', 
            'konfirmasiPenerimaan', 
            'laporanNutrisi', 
            'laporanMonitoring'
        ]);

        return response()->json($distribusi, 200);
    }
}