<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

// --- Impor Model yang diperlukan ---
use App\Models\Penyedia;
use App\Models\Sekolah;
use App\Models\MenuMbg;
use App\Models\KonfirmasiPenerimaan;
use App\Models\LaporanNutrisi;
use App\Models\LaporanMonitoring;

class Distribusi extends Model
{
    use HasFactory;

    protected $fillable = [
        'penyedia_id',
        'sekolah_id',
        'menu_id', // <-- Ini harus 'menu_id' sesuai database Anda
        'tanggal_distribusi',
        'jumlah_paket',
        'status',
    ];

    protected $casts = [
        'tanggal_distribusi' => 'date',
    ];

    /**
     * Relasi ke Penyedia.
     */
    public function penyedia(): BelongsTo
    {
        return $this->belongsTo(Penyedia::class);
    }

    /**
     * Relasi ke Sekolah.
     */
    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    /**
     * --- INI ADALAH PERBAIKAN PENTING ---
     * * Nama fungsi 'menuMbg' (camelCase)
     * HARUS cocok dengan 'with('menuMbg')' di Controller.
     *
     * Foreign key 'menu_id' (snake_case)
     * HARUS cocok dengan kolom 'menu_id' di file database .sql Anda.
     */
    public function menuMbg(): BelongsTo
    {
        return $this->belongsTo(MenuMbg::class, 'menu_id');
    }

    /**
     * Relasi ke Konfirmasi Penerimaan.
     * Nama fungsi 'konfirmasiPenerimaan' (camelCase)
     * HARUS cocok dengan 'with('konfirmasiPenerimaan')' di Controller.
     */
    public function konfirmasiPenerimaan(): HasOne
    {
        return $this->hasOne(KonfirmasiPenerimaan::class);
    }

    /**
     * Relasi ke Laporan Nutrisi.
     */
    public function laporanNutrisi(): HasOne
    {
        return $this->hasOne(LaporanNutrisi::class);
    }

    /**
     * Relasi ke Laporan Monitoring.
     */
    public function laporanMonitoring(): HasOne
    {
        return $this->hasOne(LaporanMonitoring::class);
    }
}