<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Penyedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_perusahaan',
        'alamat',
        'no_telp',
    ];

    /**
     * Profil Penyedia ini dimiliki oleh satu User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Satu Penyedia memiliki banyak Menu.
     */
    public function menuMbgs(): HasMany
    {
        return $this->hasMany(MenuMbg::class);
    }

    /**
     * Satu Penyedia melakukan banyak Distribusi.
     */
    public function distribusis(): HasMany
    {
        return $this->hasMany(Distribusi::class);
    }
}