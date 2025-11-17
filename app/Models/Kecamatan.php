<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kecamatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kota_id',
        'nama_kecamatan',
    ];

    /**
     * Satu Kecamatan dimiliki oleh satu Kota.
     */
    public function kota(): BelongsTo
    {
        return $this->belongsTo(Kota::class);
    }

    /**
     * Satu Kecamatan memiliki banyak Kelurahan.
     */
    public function kelurahans(): HasMany
    {
        return $this->hasMany(Kelurahan::class);
    }
}