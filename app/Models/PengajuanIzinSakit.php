<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanIzinSakit extends Model
{
    protected $table = 'pengajuan_izin_sakit';

    protected $fillable = [
        'siswa_id',
        'tanggal',
        'tanggal_selesai',
        'jenis',
        'alasan',
        'bukti',
        'status',
        'diperiksa_oleh',
        'diperiksa_pada',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'tanggal_selesai' => 'date',
            'diperiksa_pada' => 'datetime',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function pemeriksa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diperiksa_oleh');
    }
}
