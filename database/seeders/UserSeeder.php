<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID Role
        $adminRole = Role::where('nama_role', 'admin')->first();
        $penyediaRole = Role::where('nama_role', 'penyedia')->first();
        $sekolahRole = Role::where('nama_role', 'sekolah')->first();
        $supervisorRole = Role::where('nama_role', 'supervisor')->first();

        // Buat User Admin
        User::updateOrCreate(
            ['email' => 'admin@gizimon.com'],
            [
                'name' => 'Admin Gizimon',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id
            ]
        );

        // Buat User Penyedia
        User::updateOrCreate(
            ['email' => 'penyedia.cvmaju@gizimon.com'],
            [
                'name' => 'CV Maju Pangan',
                'password' => Hash::make('password'),
                'role_id' => $penyediaRole->id
            ]
        );

        // Buat User Sekolah
        User::updateOrCreate(
            ['email' => 'sekolah.sdnbeji1@gizimon.com'],
            [
                'name' => 'SDN Beji 1',
                'password' => Hash::make('password'),
                'role_id' => $sekolahRole->id
            ]
        );

        // Buat User Supervisor
        User::updateOrCreate(
            ['email' => 'supervisor.dinas@gizimon.com'],
            [
                'name' => 'Supervisor Dinas',
                'password' => Hash::make('password'),
                'role_id' => $supervisorRole->id
            ]
        );
    }
}