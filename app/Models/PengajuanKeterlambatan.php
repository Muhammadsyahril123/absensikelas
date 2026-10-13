<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanKeterlambatan extends Model
{
    protected $table = 'pengajuan_keterlambatan';

    protected $fillable = [
        'siswa_id',
        'absensi_id',
        'alasan',
        'bukti',
        'status',
        'diperiksa_oleh',
        'diperiksa_pada',
    ];

    protected function casts(): array
    {
        return [
            'diperiksa_pada' => 'datetime',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function absensi(): BelongsTo
    {
        return $this->belongsTo(Absensi::class);
    }

    public function pemeriksa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diperiksa_oleh');
    }
}