<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Distribusi;
use App\Models\User;
use App\Models\KonfirmasiPenerimaan;
use App\Models\LaporanNutrisi;
use App\Models\LaporanMonitoring;
use Carbon\Carbon;

class LaporanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil data master
        $distribusi = Distribusi::first();
        $userSekolah = User::where('email', 'sekolah.sdnbeji1@gizimon.com')->first();
        $userSupervisor = User::where('email', 'supervisor.dinas@gizimon.com')->first();

        // 2. Buat Konfirmasi (Contoh: Diterima oleh sekolah)
        KonfirmasiPenerimaan::updateOrCreate(
            ['distribusi_id' => $distribusi->id],
            [
                'user_id' => $userSekolah->id,
                'waktu_konfirmasi' => $distribusi->tanggal_distribusi->addHours(8), // Jam 8 pagi
                'status' => 'diterima',
                'catatan' => 'Paket diterima dalam kondisi baik.'
            ]
        );

        // 3. Buat Laporan Nutrisi (Oleh Supervisor)
        LaporanNutrisi::updateOrCreate(
            ['distribusi_id' => $distribusi->id],
            [
                'user_id' => $userSupervisor->id,
                'lemak' => 15.5,
                'protein' => 20.1,
                'karbohidrat' => 50.0,
                'catatan' => 'Kandungan nutrisi sudah sesuai standar.'
            ]
        );

        // 4. Buat Laporan Monitoring (Oleh Supervisor)
        LaporanMonitoring::updateOrCreate(
            ['distribusi_id' => $distribusi->id],
            [
                'user_id' => $userSupervisor->id,
                'hasil_monitoring' => 'Distribusi tepat waktu, kebersihan terjaga.',
                'tanggal_monitoring' => $distribusi->tanggal_distribusi
            ]
        );
    }
}