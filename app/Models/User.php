<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Penyedia;
use App\Models\Sekolah;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id', // <--- Diperbarui dari 'role' ke 'role_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //### RELASI ###

    /**
     * User memiliki satu Role.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * User (penyedia) memiliki satu profil Penyedia.
     */
    public function penyedia(): HasOne
    {
        return $this->hasOne(Penyedia::class, 'user_id');
    }

    /**
     * User (sekolah) memiliki satu profil Sekolah.
     */
    public function sekolah(): HasOne
    {
        return $this->hasOne(Sekolah::class, 'user_id');
    }

    /**
     * User (sekolah) membuat banyak Konfirmasi Penerimaan.
     */
    public function konfirmasiPenerimaans(): HasMany
    {
        return $this->hasMany(KonfirmasiPenerimaan::class);
    }

    /**
     * User (supervisor) membuat banyak Laporan Nutrisi.
     */
    public function laporanNutrisis(): HasMany
    {
        return $this->hasMany(LaporanNutrisi::class);
    }

    /**
     * User (supervisor) membuat banyak Laporan Monitoring.
     */
    public function laporanMonitorings(): HasMany
    {
        return $this->hasMany(LaporanMonitoring::class);
    }
}