<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanNutrisi extends Model
{
    use HasFactory;

    protected $fillable = [
        'distribusi_id',
        'user_id', // User supervisor
        'lemak',
        'protein',
        'karbohidrat',
        'catatan',
    ];

    protected $casts = [
        'lemak' => 'float',
        'protein' => 'float',
        'karbohidrat' => 'float',
    ];

    /**
     * Laporan ini milik satu Distribusi.
     */
    public function distribusi(): BelongsTo
    {
        return $this->belongsTo(Distribusi::class);
    }

    /**
     * Laporan ini dibuat oleh satu User (Supervisor).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}