<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dispensasi extends Model
{
    protected $fillable = [
        'siswa_id',
        'kelas_id',
        'guru_piket_id',
        'judul',
        'keterangan',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function guruPiket(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_piket_id');
    }
}