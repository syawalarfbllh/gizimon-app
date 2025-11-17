<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,        // 1. Buat Roles (admin, penyedia, dll)
            WilayahSeeder::class,     // 2. Buat data Kota Depok, Kecamatan, Kelurahan
            UserSeeder::class,        // 3. Buat user-user contoh (terhubung ke Roles)
            ProfilSeeder::class,      // 4. Buat profil Penyedia & Sekolah (terhubung ke User & Kelurahan)
            MenuSeeder::class,        // 5. Buat menu (terhubung ke Penyedia)
            DistribusiSeeder::class,  // 6. Buat 1 data distribusi (terhubung ke Penyedia, Sekolah, Menu)
            LaporanSeeder::class,     // 7. Buat laporan (terhubung ke Distribusi & User)
        ]);
    }
}
