<?php

namespace App\Http\Controllers\Api\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Distribusi;
use App\Models\LaporanMonitoring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LaporanMonitoringController extends Controller
{
    /**
     * Menyimpan laporan monitoring baru.
     */
    public function store(Request $request, Distribusi $distribusi)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user(); // User supervisor yang login

        // 1. Validasi: Cek apakah sudah ada laporan
        if ($distribusi->laporanMonitoring) {
            return response()->json(['message' => 'Laporan monitoring untuk distribusi ini sudah ada.'], 400);
        }

        // 2. Validasi Input
        $validator = Validator::make($request->all(), [
            'hasil_monitoring' => 'required|string',
            'tanggal_monitoring' => 'required|date|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 3. Buat Laporan
        $laporan = $distribusi->laporanMonitoring()->create([
            'user_id' => $user->id,
            'hasil_monitoring' => $request->hasil_monitoring,
            'tanggal_monitoring' => $request->tanggal_monitoring,
        ]);

        return response()->json($laporan, 201);
    }

    /**
     * Update laporan monitoring.
     */
    public function update(Request $request, LaporanMonitoring $laporan)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Otorisasi: Pastikan supervisor hanya bisa update laporannya sendiri
        if ($laporan->user_id !== $user->id) {
             return response()->json(['message' => 'Tidak diizinkan, Anda bukan pembuat laporan ini.'], 403);
        }
        
        // 2. Validasi Input
        $validator = Validator::make($request->all(), [
            'hasil_monitoring' => 'required|string',
            'tanggal_monitoring' => 'required|date|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 3. Update Laporan
        $laporan->update($request->all());

        return response()->json($laporan, 200);
    }
}