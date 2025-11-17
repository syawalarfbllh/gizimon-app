<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kota;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WilayahController extends Controller
{
    // ==========================================================
    // FUNGSI PUBLIK (UNTUK SEMUA ROLE YANG LOGIN)
    // ==========================================================

    /**
     * Mengambil semua data kota (sesuai seeder, hanya Depok)
     */
    public function getAllKota()
    {
        // Kita preload kecamatan dan kelurahan untuk frontend (jika perlu)
        $kota = Kota::with('kecamatans.kelurahans')->get();
        return response()->json($kota, 200);
    }

    /**
     * Mengambil data kecamatan berdasarkan ID kota
     * Menggunakan Route Model Binding (Kota $kota)
     */
    public function getKecamatanByKota(Kota $kota)
    {
        return response()->json($kota->kecamatans, 200);
    }

    /**
     * Mengambil data kelurahan berdasarkan ID kecamatan
     * Menggunakan Route Model Binding (Kecamatan $kecamatan)
     */
    public function getKelurahanByKecamatan(Kecamatan $kecamatan)
    {
        return response()->json($kecamatan->kelurahans, 200);
    }

    // ==========================================================
    // FUNGSI KHUSUS ADMIN (CRUD)
    // ==========================================================

    // --- KOTA ---
    public function storeKota(Request $request)
    {
        $validator = Validator::make($request->all(), ['nama_kota' => 'required|string|max:255|unique:kotas']);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $kota = Kota::create($request->all());
        return response()->json($kota, 201);
    }

    public function updateKota(Request $request, Kota $kota)
    {
        $validator = Validator::make($request->all(), ['nama_kota' => 'required|string|max:255|unique:kotas,nama_kota,' . $kota->id]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $kota->update($request->all());
        return response()->json($kota, 200);
    }

    public function destroyKota(Kota $kota)
    {
        // Tambahkan logic untuk cek jika kota masih punya kecamatan
        if ($kota->kecamatans()->count() > 0) {
            return response()->json(['message' => 'Hapus gagal. Kota ini masih memiliki kecamatan.'], 400);
        }
        $kota->delete();
        return response()->json(['message' => 'Kota berhasil dihapus'], 200);
    }

    // --- KECAMATAN ---
    public function storeKecamatan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kecamatan' => 'required|string|max:255|unique:kecamatans',
            'kota_id' => 'required|exists:kotas,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $kecamatan = Kecamatan::create($request->all());
        return response()->json($kecamatan, 201);
    }

    public function updateKecamatan(Request $request, Kecamatan $kecamatan)
    {
        $validator = Validator::make($request->all(), [
            'nama_kecamatan' => 'required|string|max:255|unique:kecamatans,nama_kecamatan,' . $kecamatan->id,
            'kota_id' => 'required|exists:kotas,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $kecamatan->update($request->all());
        return response()->json($kecamatan, 200);
    }

    public function destroyKecamatan(Kecamatan $kecamatan)
    {
        if ($kecamatan->kelurahans()->count() > 0) {
            return response()->json(['message' => 'Hapus gagal. Kecamatan ini masih memiliki kelurahan.'], 400);
        }
        $kecamatan->delete();
        return response()->json(['message' => 'Kecamatan berhasil dihapus'], 200);
    }

    // --- KELURAHAN ---
    public function storeKelurahan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kelurahan' => 'required|string|max:255|unique:kelurahans',
            'kecamatan_id' => 'required|exists:kecamatans,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $kelurahan = Kelurahan::create($request->all());
        return response()->json($kelurahan, 201);
    }

    public function updateKelurahan(Request $request, Kelurahan $kelurahan)
    {
        $validator = Validator::make($request->all(), [
            'nama_kelurahan' => 'required|string|max:255|unique:kelurahans,nama_kelurahan,' . $kelurahan->id,
            'kecamatan_id' => 'required|exists:kecamatans,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $kelurahan->update($request->all());
        return response()->json($kelurahan, 200);
    }

    public function destroyKelurahan(Kelurahan $kelurahan)
    {
        if ($kelurahan->sekolahs()->count() > 0) {
            return response()->json(['message' => 'Hapus gagal. Kelurahan ini masih terdaftar di sekolah.'], 400);
        }
        $kelurahan->delete();
        return response()->json(['message' => 'Kelurahan berhasil dihapus'], 200);
    }

    public function indexKota()
    {
        // 'indexKota' akan dipanggil oleh GET /api/admin/kota
        $kota = Kota::latest()->get();
        return response()->json($kota, 200);
    }
}