<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Penyedia;
use App\Models\Sekolah;
use App\Models\MenuMbg;
use App\Models\Distribusi;
use Carbon\Carbon;

class DistribusiSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil data master
        $penyedia = Penyedia::first();
        $sekolah = Sekolah::first();
        $menu = MenuMbg::first(); // Ambil menu yang sudah dibuat

        // 2. Buat data distribusi
        Distribusi::updateOrCreate(
            [
                'penyedia_id' => $penyedia->id,
                'sekolah_id' => $sekolah->id,
                'menu_id' => $menu->id,
                'tanggal_distribusi' => $menu->tanggal_menu // Samakan dgn tanggal menu
            ],
            [
                'jumlah_paket' => 100,
                'status' => 'disiapkan' // Status awal
            ]
        );
    }
}