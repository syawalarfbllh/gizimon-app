<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Penyedia;
use App\Models\Sekolah;
use App\Models\Kelurahan;

class ProfilSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil User
        $userPenyedia = User::where('email', 'penyedia.cvmaju@gizimon.com')->first();
        $userSekolah = User::where('email', 'sekolah.sdnbeji1@gizimon.com')->first();
        
        // 2. Ambil Kelurahan (contoh: Beji)
        $kelurahan = Kelurahan::where('nama_kelurahan', 'Beji')->first();
        if (!$kelurahan) {
            // Jika seeder wilayah belum jalan, ambil kelurahan pertama
            $kelurahan = Kelurahan::first();
        }

        // 3. Buat Profil Penyedia
        Penyedia::updateOrCreate(
            ['user_id' => $userPenyedia->id],
            [
                'nama_perusahaan' => 'CV Maju Pangan Sejahtera',
                'alamat' => 'Jl. Raya Sawangan No. 10',
                'no_telp' => '08123456789'
            ]
        );

        // 4. Buat Profil Sekolah
        Sekolah::updateOrCreate(
            ['user_id' => $userSekolah->id],
            [
                'kelurahan_id' => $kelurahan->id,
                'npsn' => '2022Depok01',
                'nama_sekolah' => 'SDN Beji 1',
                'alamat' => 'Jl. Beji Raya No. 1',
                'jenis_sekolah' => 'SD'
            ]
        );
    }
}