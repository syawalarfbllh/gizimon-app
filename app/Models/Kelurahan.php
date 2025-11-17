<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelurahan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kecamatan_id',
        'nama_kelurahan',
    ];

    /**
     * Satu Kelurahan dimiliki oleh satu Kecamatan.
     */
    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    /**
     * Satu Kelurahan memiliki banyak Sekolah.
     */
    public function sekolahs(): HasMany
    {
        return $this->hasMany(Sekolah::class);
    }
}