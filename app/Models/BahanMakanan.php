<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BahanMakanan extends Model
{
    use HasFactory;

    // Nama tabel akan otomatis 'bahan_makanans'
    // protected $table = 'bahan_makanans';

    protected $fillable = [
        'menu_id',
        'nama_bahan',
        'jumlah',
        'satuan',
    ];

    /**
     * Bahan Makanan ini milik satu Menu.
     * Kita spesifikasikan foreign key 'menu_id'.
     */
    public function menuMbg(): BelongsTo
    {
        return $this->belongsTo(MenuMbg::class, 'menu_id');
    }
}