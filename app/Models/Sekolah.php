<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Sekolah extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'kelurahan_id',
        'npsn',
        'nama_sekolah',
        'alamat',
        'jenis_sekolah',
    ];

    /**
     * Profil Sekolah ini dimiliki oleh satu User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Satu Sekolah berada di satu Kelurahan.
     */
    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(Kelurahan::class);
    }

    /**
     * Satu Sekolah menerima banyak Distribusi.
     */
    public function distribusis(): HasMany
    {
        return $this->hasMany(Distribusi::class);
    }
}