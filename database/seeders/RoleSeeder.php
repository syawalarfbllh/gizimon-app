<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menggunakan updateOrCreate agar aman dijalankan berkali-kali
        Role::updateOrCreate(['nama_role' => 'admin']);
        Role::updateOrCreate(['nama_role' => 'penyedia']);
        Role::updateOrCreate(['nama_role' => 'sekolah']);
        Role::updateOrCreate(['nama_role' => 'supervisor']);
    }
}
