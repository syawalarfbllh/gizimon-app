<?php

use Illuminate\Support\Facades\Route;

// === Impor Controller (Agar rapi) ===
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\WilayahController;
use App\Http\Controllers\Api\Penyedia\MenuController;
use App\Http\Controllers\Api\Penyedia\BahanMakananController;
use App\Http\Controllers\Api\Penyedia\DistribusiPenyediaController;
use App\Http\Controllers\Api\Sekolah\DistribusiSekolahController;
use App\Http\Controllers\Api\Sekolah\KonfirmasiPenerimaanController;
use App\Http\Controllers\Api\Supervisor\LaporanMonitoringController;
use App\Http\Controllers\Api\Supervisor\LaporanNutrisiController;
use App\Http\Controllers\Api\Supervisor\DistribusiSupervisorController;

/*
|--------------------------------------------------------------------------
| Rute API
|--------------------------------------------------------------------------
|
| Di sinilah Anda dapat mendaftarkan rute API untuk aplikasi Anda. Rute-
| rute ini dimuat oleh RouteServiceProvider dan semuanya akan
| ditugaskan ke grup middleware "api".
|
*/

// ### GRUP 1: RUTE AUTENTIKASI (PUBLIK) ###
// Rute ini tidak perlu login
Route::post('/login', [AuthController::class, 'login']);

// TODO: Rute registrasi bisa lebih kompleks,
// tapi ini contoh dasarnya
// Route::post('/register-penyedia', [AuthController::class, 'registerPenyedia']);
// Route::post('/register-sekolah', [AuthController::class, 'registerSekolah']);


// ### GRUP 2: RUTE TERPROTEKSI (HARUS LOGIN) ###
// Semua rute di dalam grup ini WAJIB login (menggunakan token Sanctum)
Route::middleware(['auth:sanctum'])->group(function () {
    
    // Endpoint untuk logout
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Endpoint untuk mengambil data user yang sedang login
    Route::get('/me', [AuthController::class, 'me']);
    
    // Endpoint untuk data wilayah (kota, kec, kel)
    // Dibutuhkan oleh banyak peran (Admin, Sekolah, Penyedia) saat mendaftar/edit profil
    Route::get('/wilayah/kota', [WilayahController::class, 'getAllKota']);
    Route::get('/wilayah/kecamatan/{kota}', [WilayahController::class, 'getKecamatanByKota']);
    Route::get('/wilayah/kelurahan/{kecamatan}', [WilayahController::class, 'getKelurahanByKecamatan']);

    //-------------------------------------------------
    // ### GRUP 3: RUTE KHUSUS ADMIN ###
    // Hanya user dengan peran 'admin' yang bisa akses
    //-------------------------------------------------
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        // CRUD User (Admin, Penyedia, Sekolah, Supervisor)
        Route::apiResource('/users', AdminUserController::class);
        Route::get('/form-data/roles', [AdminUserController::class, 'getRoles']);
        
        // CRUD Wilayah (hanya admin yang bisa CUD, 'index' & 'show' ada di publik)
        Route::get('/kota', [WilayahController::class, 'indexKota']);
        Route::post('/kota', [WilayahController::class, 'storeKota']);
        Route::put('/kota/{kota}', [WilayahController::class, 'updateKota']);
        Route::delete('/kota/{kota}', [WilayahController::class, 'destroyKota']);

        Route::post('/kecamatan', [WilayahController::class, 'storeKecamatan']);
        Route::put('/kecamatan/{kecamatan}', [WilayahController::class, 'updateKecamatan']);
        Route::delete('/kecamatan/{kecamatan}', [WilayahController::class, 'destroyKecamatan']);
        
        Route::post('/kelurahan', [WilayahController::class, 'storeKelurahan']);
        Route::put('/kelurahan/{kelurahan}', [WilayahController::class, 'updateKelurahan']);
        Route::delete('/kelurahan/{kelurahan}', [WilayahController::class, 'destroyKelurahan']);

        Route::get('/menu', [MenuController::class, 'index']);
        Route::get('/menu/{menu}', [MenuController::class, 'show']);

        Route::get('/distribusi', [DistribusiSupervisorController::class, 'index']);
        Route::get('/distribusi/{distribusi}', [DistribusiSupervisorController::class, 'show']);

        Route::get('/bahan-makanan', [Admin\BahanMakananController::class, 'index']);
        
        // TODO: Tambahkan rute untuk CRUD Profil Penyedia/Sekolah oleh Admin
    });

    //-------------------------------------------------
    // ### GRUP 4: RUTE KHUSUS PENYEDIA ###
    // Hanya user dengan peran 'penyedia' yang bisa akses
    //-------------------------------------------------
    Route::prefix('penyedia')->middleware('role:penyedia')->group(function () {
        // TODO: Rute untuk mengelola profil penyedia sendiri
        // Route::get('/profil', [PenyediaProfilController::class, 'show']);
        // Route::put('/profil', [PenyediaProfilController::class, 'update']);

        // CRUD Menu (MenuMbg)
        Route::apiResource('/menu', MenuController::class);
        
        // CRUD Bahan Makanan (di dalam menu)
        Route::apiResource('/menu/{menu}/bahan', BahanMakananController::class);
        
        // CRUD Distribusi
        Route::apiResource('/distribusi', DistribusiPenyediaController::class);
    });

    //-------------------------------------------------
    // ### GRUP 5: RUTE KHUSUS SEKOLAH ###
    // Hanya user dengan peran 'sekolah' yang bisa akses
    //-------------------------------------------------
    Route::prefix('sekolah')->middleware('role:sekolah')->group(function () {
        // TODO: Rute untuk mengelola profil sekolah sendiri
        
        // Melihat daftar distribusi yang ditujukan ke sekolahnya
        Route::get('/distribusi', [DistribusiSekolahController::class, 'index']);
        Route::get('/distribusi/{distribusi}', [DistribusiSekolahController::class, 'show']);

        // Membuat/Update Konfirmasi Penerimaan
        Route::post('/distribusi/{distribusi}/konfirmasi', [KonfirmasiPenerimaanController::class, 'store']);
        Route::put('/konfirmasi/{konfirmasi}', [KonfirmasiPenerimaanController::class, 'update']);
    });

    //-------------------------------------------------
    // ### GRUP 6: RUTE KHUSUS SUPERVISOR ###
    // Hanya user dengan peran 'supervisor' yang bisa akses
    //-------------------------------------------------
    Route::prefix('supervisor')->middleware('role:supervisor')->group(function () {
        // Melihat semua distribusi
        Route::get('/distribusi', [DistribusiSupervisorController::class, 'index']);
        Route::get('/distribusi/{distribusi}', [DistribusiSupervisorController::class, 'show']);

        // Membuat/Update Laporan Nutrisi
        Route::post('/distribusi/{distribusi}/laporan-nutrisi', [LaporanNutrisiController::class, 'store']);
        Route::put('/laporan-nutrisi/{laporan}', [LaporanNutrisiController::class, 'update']);
        
        // Membuat/Update Laporan Monitoring
        Route::post('/distribusi/{distribusi}/laporan-monitoring', [LaporanMonitoringController::class, 'store']);
        Route::put('/laporan-monitoring/{laporan}', [LaporanMonitoringController::class, 'update']);
    });

});