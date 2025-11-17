<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuMbg extends Model
{
    use HasFactory;

    // Nama tabel akan otomatis 'menu_mbgs'
    // protected $table = 'menu_mbgs'; 

    protected $fillable = [
        'penyedia_id',
        'nama_menu',
        'deskripsi',
        'tanggal_menu',
    ];

    protected $casts = [
        'tanggal_menu' => 'date',
    ];

    /**
     * Menu ini dimiliki oleh satu Penyedia.
     */
    public function penyedia(): BelongsTo
    {
        return $this->belongsTo(Penyedia::class);
    }

    /**
     * Satu Menu memiliki banyak Bahan Makanan.
     * Kita spesifikasikan foreign key 'menu_id' karena nama modelnya MenuMbg.
     */
    public function bahanMakanans(): HasMany
    {
        return $this->hasMany(BahanMakanan::class, 'menu_id');
    }

    /**
     * Satu Menu digunakan di banyak Distribusi.
     * Kita spesifikasikan foreign key 'menu_id'.
     */
    public function distribusis(): HasMany
    {
        return $this->hasMany(Distribusi::class, 'menu_id');
    }
}