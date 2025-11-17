<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kota;
use App\Models\Kecamatan;
use App\Models\Kelurahan;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Kota
        $kota = Kota::updateOrCreate(['nama_kota' => 'Kota Depok']);

        // 2. Data Kecamatan dan Kelurahan di Depok
        $dataWilayah = [
            'Beji' => ['Beji', 'Beji Timur', 'Kemiri Muka', 'Kukusan', 'Pondok Cina', 'Tanah Baru'],
            'Bojongsari' => ['Bojongsari', 'Bojongsari Baru', 'Curug', 'Duren Mekar', 'Duren Seribu', 'Pondok Petir', 'Serua'],
            'Cilodong' => ['Cilodong', 'Jatimulya', 'Kalibaru', 'Kalimulya', 'Sukamaju'],
            'Cimanggis' => ['Cisalak Pasar', 'Curug', 'Harjamukti', 'Mekarsari', 'Pasir Gunung Selatan', 'Tugu'],
            'Cinere' => ['Cinere', 'Gandul', 'Pangkal Jati', 'Pangkal Jati Baru'],
            'Cipayung' => ['Bojong Pondok Terong', 'Cipayung', 'Cipayung Jaya', 'Pondok Jaya', 'Ratu Jaya'],
            'Limo' => ['Grogol', 'Krukut', 'Limo', 'Meruyung'],
            'Pancoran Mas' => ['Depok', 'Depok Jaya', 'Mampang', 'Pancoran Mas', 'Rangkapan Jaya', 'Rangkapan Jaya Baru'],
            'Sawangan' => ['Bedahan', 'Cinangka', 'Kedaung', 'Pasir Putih', 'Pengasinan', 'Sawangan', 'Sawangan Baru'],
            'Sukmajaya' => ['Abadijaya', 'Baktijaya', 'Cisalak', 'Mekarjaya', 'Sukmajaya', 'Tirtajaya'],
            'Tapos' => ['Cilangkap', 'Cimpaeun', 'Jatijajar', 'Leuwinanggung', 'Sukamaju Baru', 'Sukatani', 'Tapos'],
        ];

        // 3. Masukkan data ke database
        foreach ($dataWilayah as $namaKecamatan => $kelurahans) {
            $kecamatan = Kecamatan::updateOrCreate(
                ['nama_kecamatan' => $namaKecamatan, 'kota_id' => $kota->id],
                ['kota_id' => $kota->id]
            );

            foreach ($kelurahans as $namaKelurahan) {
                Kelurahan::updateOrCreate(
                    ['nama_kelurahan' => $namaKelurahan, 'kecamatan_id' => $kecamatan->id],
                    ['kecamatan_id' => $kecamatan->id]
                );
            }
        }
    }
}