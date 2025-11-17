<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanMonitoring extends Model
{
    use HasFactory;

    protected $fillable = [
        'distribusi_id',
        'user_id', // User supervisor
        'hasil_monitoring',
        'tanggal_monitoring',
    ];

    protected $casts = [
        'tanggal_monitoring' => 'date',
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