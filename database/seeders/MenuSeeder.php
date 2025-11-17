<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Penyedia;
use App\Models\MenuMbg;
use App\Models\BahanMakanan;
use Carbon\Carbon;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil Penyedia
        $penyedia = Penyedia::first();

        // 2. Buat Menu
        $menu = MenuMbg::updateOrCreate(
            [
                'penyedia_id' => $penyedia->id,
                'tanggal_menu' => Carbon::now()->addDay()->toDateString() // Menu untuk besok
            ],
            [
                'nama_menu' => 'Paket Nasi Ayam Goreng',
                'deskripsi' => 'Nasi, Ayam Goreng, Tahu, Tempe, Sayur Sop, Buah Pisang'
            ]
        );

        // 3. Buat Bahan Makanan
        BahanMakanan::updateOrCreate([
            'menu_id' => $menu->id,
            'nama_bahan' => 'Nasi Putih'
        ], ['jumlah' => '150', 'satuan' => 'gr']);
        
        BahanMakanan::updateOrCreate([
            'menu_id' => $menu->id,
            'nama_bahan' => 'Ayam Goreng'
        ], ['jumlah' => '1', 'satuan' => 'potong']);

        BahanMakanan::updateOrCreate([
            'menu_id' => $menu->id,
            'nama_bahan' => 'Sayur Sop'
        ], ['jumlah' => '100', 'satuan' => 'gr']);
    }
}