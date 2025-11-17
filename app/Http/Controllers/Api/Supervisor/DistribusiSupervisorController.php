<?php

namespace App\Http\Controllers\Api\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Distribusi;
use Illuminate\Http\Request;

class DistribusiSupervisorController extends Controller
{
    /**
     * Menampilkan semua data distribusi.
     */
    public function index(Request $request)
    {
        // Ambil semua distribusi, load relasi penting
        $distribusi = Distribusi::with(['penyedia.user', 'sekolah.user', 'menuMbg', 'konfirmasiPenerimaan'])
            ->latest('tanggal_distribusi')
            ->get();

        return response()->json($distribusi, 200);
    }

    /**
     * Menampilkan satu data distribusi spesifik.
     */
    public function show(Distribusi $distribusi)
    {
        // Supervisor bisa melihat detail apapun
        $distribusi->load([
            'penyedia',
            'sekolah',
            'menuMbg.bahanMakanans',
            'konfirmasiPenerimaan',
            'laporanNutrisi',
            'laporanMonitoring'
        ]);

        return response()->json($distribusi, 200);
    }
}
