<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BahanMakanan;
use App\Models\MenuMbg;
use Illuminate\Http\Request;

class BahanMakananController extends Controller
{
    /**
     * Menampilkan daftar Bahan Makanan berdasarkan menu_id.
     * Admin hanya memiliki akses read-only.
     */
    public function index(Request $request)
    {
        // Validasi menu_id diperlukan
        $request->validate([
            'menu_id' => 'required|exists:menu_mbgs,id',
        ]);
        
        $bahanMakanans = BahanMakanan::where('menu_id', $request->menu_id)
            ->orderBy('id', 'asc')
            ->get();

        return response()->json($bahanMakanans);
    }
}