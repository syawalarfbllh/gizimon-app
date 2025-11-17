<?php

namespace App\Http\Controllers\Api\Penyedia;

use App\Http\Controllers\Controller;
use App\Models\BahanMakanan;
use App\Models\MenuMbg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BahanMakananController extends Controller
{
    /**
     * Pengecekan otorisasi: pastikan menu milik penyedia yang login.
     */
    private function authorizePenyedia(MenuMbg $menu)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->penyedia || $menu->penyedia_id !== $user->penyedia->id) {
            abort(403, 'Tidak diizinkan');
        }
    }

    /**
     * Menampilkan semua bahan makanan untuk menu tertentu.
     * Dapat diakses oleh Penyedia (hanya miliknya) dan Admin (semua).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $is_admin = $user->role->nama_role === 'admin';
        
        $request->validate([
            'menu_id' => 'required|exists:menu_mbgs,id',
        ]);

        $menuId = $request->menu_id;
        
        // 1. Logika Otorisasi/Filtering
        // Jika BUKAN Admin (Asumsi: Penyedia), harus memverifikasi kepemilikan menu.
        if (!$is_admin) {
            $menu = MenuMbg::where('id', $menuId)
                        ->where('penyedia_id', $user->penyedia->id) 
                        ->first();
            
            if (!$menu) {
                return response()->json(['message' => 'Akses ditolak atau Menu tidak ditemukan.'], 403);
            }
        }
        
        // 2. Query Bahan Makanan
        // Query akan dijalankan untuk Admin (tanpa filter kepemilikan) atau Penyedia (setelah lolos filter kepemilikan)
        $bahanMakanans = BahanMakanan::where('menu_id', $menuId)
            ->orderBy('id', 'asc')
            ->get();

        return response()->json($bahanMakanans);
    }

    /**
     * Menyimpan bahan makanan baru ke menu.
     */
    public function store(Request $request, MenuMbg $menu)
    {
        // Hanya Penyedia yang bisa melakukan STORE
        $this->authorizePenyedia($menu); 

        $validator = Validator::make($request->all(), [
            'nama_bahan' => 'required|string|max:255',
            'jumlah' => 'required|string',
            'satuan' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Buat bahan makanan baru dan hubungkan ke menu
        $bahan = $menu->bahanMakanans()->create($request->all());

        return response()->json($bahan, 201);
    }

    /**
     * Menampilkan satu bahan makanan spesifik.
     */
    public function show(MenuMbg $menu, BahanMakanan $bahan)
    {
        // Hanya Penyedia yang bisa melihat detail bahan makanan miliknya
        $this->authorizePenyedia($menu); 
        
        // Cek apakah bahan makanan benar-benar milik menu tsb
        if ($bahan->menu_id !== $menu->id) {
             return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($bahan, 200);
    }

    /**
     * Update bahan makanan.
     */
    public function update(Request $request, MenuMbg $menu, BahanMakanan $bahan)
    {
        // Hanya Penyedia yang bisa melakukan UPDATE
        $this->authorizePenyedia($menu); 

        if ($bahan->menu_id !== $menu->id) {
             return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'nama_bahan' => 'required|string|max:255',
            'jumlah' => 'required|string',
            'satuan' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $bahan->update($request->all());

        return response()->json($bahan, 200);
    }

    /**
     * Hapus bahan makanan.
     */
    public function destroy(MenuMbg $menu, BahanMakanan $bahan)
    {
        // Hanya Penyedia yang bisa melakukan DELETE
        $this->authorizePenyedia($menu); 

        if ($bahan->menu_id !== $menu->id) {
             return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $bahan->delete();

        return response()->json(['message' => 'Bahan makanan berhasil dihapus'], 200);
    }
}