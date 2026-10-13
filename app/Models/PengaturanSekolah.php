<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanSekolah extends Model
{
    protected $table = 'pengaturan_sekolah';

    protected $fillable = [
        'nama_sekolah',
        'latitude_sekolah',
        'longitude_sekolah',
        'radius_meter',
        'jam_masuk',
        'batas_toleransi',
        'batas_wajib_alasan',
    ];

    protected function casts(): array
    {
        return [
            'latitude_sekolah' => 'decimal:7',
            'longitude_sekolah' => 'decimal:7',
        ];
    }
}