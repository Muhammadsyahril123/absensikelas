<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'wajib_ganti_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'wajib_ganti_password' => 'boolean',
        ];
    }

    public function siswa(): HasOne
    {
        return $this->hasOne(Siswa::class);
    }

    public function guru(): HasOne
    {
        return $this->hasOne(Guru::class);
    }

    public function pengajuanDiperiksa(): HasMany
    {
        return $this->hasMany(PengajuanIzinSakit::class, 'diperiksa_oleh');
    }

    public function keterlambatanDiperiksa(): HasMany
    {
        return $this->hasMany(PengajuanKeterlambatan::class, 'diperiksa_oleh');
    }

    public function catatanDibuat(): HasMany
    {
        return $this->hasMany(CatatanAbsensi::class, 'dibuat_oleh');
    }
}
