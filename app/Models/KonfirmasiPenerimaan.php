<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KonfirmasiPenerimaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'distribusi_id',
        'user_id', // User sekolah
        'waktu_konfirmasi',
        'status',
        'catatan',
    ];

    protected $casts = [
        'waktu_konfirmasi' => 'datetime',
    ];

    /**
     * Konfirmasi ini milik satu Distribusi.
     */
    public function distribusi(): BelongsTo
    {
        return $this->belongsTo(Distribusi::class);
    }

    /**
     * Konfirmasi ini dibuat oleh satu User (Sekolah).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}