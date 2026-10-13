<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    protected $table = 'siswa';

    protected $fillable = [
        'user_id',
        'kelas_id',
        'nis',
        'nama',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    public function pengajuanIzinSakit(): HasMany
    {
        return $this->hasMany(PengajuanIzinSakit::class);
    }

    public function pengajuanKeterlambatan(): HasMany
    {
        return $this->hasMany(PengajuanKeterlambatan::class);
    }

    public function catatanAbsensi(): HasMany
    {
        return $this->hasMany(CatatanAbsensi::class);
    }
}