<?php

namespace App\Http\Controllers\Api\Penyedia;

use App\Http\Controllers\Controller;
use App\Models\MenuMbg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Penyedia; // Import model Penyedia
use App\Models\User; // <-- 1. PASTIKAN 'User' DIIMPOR

class MenuController extends Controller
{
    /**
     * Dapatkan profil penyedia yang sedang login.
     * (Fungsi ini hanya boleh dipanggil oleh 'store', 'update', 'destroy')
     */
    private function getPenyedia(): Penyedia
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->penyedia) {
            abort(403, 'Aksi ini hanya untuk Penyedia.');
        }
        return $user->penyedia;
    }

    /**
     * PERBAIKAN: Fungsi 'index' sekarang "sadar" akan Role.
     *
     * Menampilkan semua menu.
     * Jika Admin: tampilkan SEMUA menu.
     * Jika Penyedia: tampilkan HANYA menu miliknya.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $userRole = $user->role->nama_role; // (Asumsi dari 'console.log' Anda)

        if ($userRole === 'admin' || $userRole === 'supervisor') {
            // Admin/Supervisor bisa melihat SEMUA menu dari SEMUA penyedia
            // Kita 'with('penyedia.user')' agar tahu menu ini milik siapa
            $menus = MenuMbg::with('penyedia.user', 'bahanMakanans')->latest()->get();
            return response()->json($menus, 200);
        }

        if ($userRole === 'penyedia') {
            // Penyedia HANYA bisa melihat menu miliknya
            $penyedia = $this->getPenyedia(); // Panggil getPenyedia di sini
            $menus = $penyedia->menuMbgs()->with('bahanMakanans')->latest()->get();
            return response()->json($menus, 200);
        }

        // Role lain (misal: Sekolah) tidak boleh mengakses daftar ini
        return response()->json(['message' => 'Tidak diizinkan'], 403);
    }

    /**
     * Menyimpan menu baru.
     * (HANYA UNTUK PENYEDIA - Kode Anda sudah benar)
     */
    public function store(Request $request)
    {
        $penyedia = $this->getPenyedia(); // <-- Ini sudah benar

        $validator = Validator::make($request->all(), [
            'nama_menu' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_menu' => 'required|date|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $menu = $penyedia->menuMbgs()->create($request->all());
        return response()->json($menu, 201);
    }

    /**
     * PERBAIKAN: Fungsi 'show' sekarang "sadar" akan Role.
     *
     * Menampilkan satu menu spesifik.
     * Jika Admin: boleh lihat.
     * Jika Penyedia: boleh lihat HANYA miliknya.
     */
    public function show(MenuMbg $menu)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $userRole = $user->role->nama_role;

        if ($userRole === 'admin' || $userRole === 'supervisor') {
            // Admin/Supervisor bisa melihat detail menu siapapun
            $menu->load('bahanMakanans', 'penyedia.user');
            return response()->json($menu, 200);
        }

        if ($userRole === 'penyedia') {
            // Penyedia HANYA boleh lihat menu miliknya
            $penyedia = $this->getPenyedia();
            if ($menu->penyedia_id !== $penyedia->id) {
                return response()->json(['message' => 'Tidak diizinkan (bukan menu Anda)'], 403);
            }
            $menu->load('bahanMakanans');
            return response()->json($menu, 200);
        }

        // Role lain tidak diizinkan
        return response()->json(['message' => 'Tidak diizinkan'], 403);
    }

    /**
     * Update menu.
     * (HANYA UNTUK PENYEDIA - Kode Anda sudah benar)
     */
    public function update(Request $request, MenuMbg $menu)
    {
        $penyedia = $this->getPenyedia(); // <-- Ini sudah benar

        // Pastikan menu ini milik penyedia yang login
        if ($menu->penyedia_id !== $penyedia->id) {
            return response()->json(['message' => 'Tidak diizinkan'], 403);
        }

        $validator = Validator::make($request->all(), [
            'nama_menu' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_menu' => 'required|date|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $menu->update($request->all());
        return response()->json($menu, 200);
    }

    /**
     * Hapus menu.
     * (HANYA UNTUK PENYEDIA - Kode Anda sudah benar)
     */
    public function destroy(MenuMbg $menu)
    {
        $penyedia = $this->getPenyedia(); // <-- Ini sudah benar

        // Pastikan menu ini milik penyedia yang login
        if ($menu->penyedia_id !== $penyedia->id) {
            return response()->json(['message' => 'Tidak diizinkan'], 403);
        }

        // Cek apakah menu sudah dipakai di distribusi
        if ($menu->distribusis()->count() > 0) {
            return response()->json(['message' => 'Hapus gagal. Menu sudah digunakan dalam distribusi.'], 400);
        }

        $menu->bahanMakanans()->delete();
        $menu->delete();

        return response()->json(['message' => 'Menu berhasil dihapus'], 200);
    }
}
