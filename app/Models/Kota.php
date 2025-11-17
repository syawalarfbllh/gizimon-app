<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kota extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kota',
    ];

    /**
     * Satu Kota memiliki banyak Kecamatan.
     */
    public function kecamatans(): HasMany
    {
        return $this->hasMany(Kecamatan::class);
    }
}