<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Absensi extends Model
{
    protected $table = 'absensi';

    protected $fillable = [
        'siswa_id',
        'tanggal',
        'waktu_absen',
        'status',
        'latitude',
        'longitude',
        'jarak_meter',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function pengajuanKeterlambatan(): HasOne
    {
        return $this->hasOne(PengajuanKeterlambatan::class);
    }
}