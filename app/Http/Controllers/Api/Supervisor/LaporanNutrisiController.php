<?php

namespace App\Http\Controllers\Api\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Distribusi;
use App\Models\LaporanNutrisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LaporanNutrisiController extends Controller
{
    /**
     * Menyimpan laporan nutrisi baru.
     */
    public function store(Request $request, Distribusi $distribusi)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user(); // User supervisor yang login

        // 1. Validasi: Cek apakah sudah ada laporan
        if ($distribusi->laporanNutrisi) {
            return response()->json(['message' => 'Laporan nutrisi untuk distribusi ini sudah ada.'], 400);
        }

        // 2. Validasi Input
        $validator = Validator::make($request->all(), [
            'lemak' => 'required|numeric|min:0',
            'protein' => 'required|numeric|min:0',
            'karbohidrat' => 'required|numeric|min:0',
            'catatan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 3. Buat Laporan
        $laporan = $distribusi->laporanNutrisi()->create([
            'user_id' => $user->id,
            'lemak' => $request->lemak,
            'protein' => $request->protein,
            'karbohidrat' => $request->karbohidrat,
            'catatan' => $request->catatan,
        ]);

        return response()->json($laporan, 201);
    }

    /**
     * Update laporan nutrisi.
     */
    public function update(Request $request, LaporanNutrisi $laporan)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Otorisasi: Pastikan supervisor hanya bisa update laporannya sendiri
        //    (Bisa diubah jika admin atau supervisor lain boleh update)
        if ($laporan->user_id !== $user->id) {
             return response()->json(['message' => 'Tidak diizinkan, Anda bukan pembuat laporan ini.'], 403);
        }
        
        // 2. Validasi Input
        $validator = Validator::make($request->all(), [
            'lemak' => 'required|numeric|min:0',
            'protein' => 'required|numeric|min:0',
            'karbohidrat' => 'required|numeric|min:0',
            'catatan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 3. Update Laporan
        $laporan->update($request->all());

        return response()->json($laporan, 200);
    }
}