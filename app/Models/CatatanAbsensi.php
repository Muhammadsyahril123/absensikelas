<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatatanAbsensi extends Model
{
    protected $table = 'catatan_absensi';

    protected $fillable = [
        'siswa_id',
        'jenis',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'kegiatan',
        'keterangan',
        'status_tindak_lanjut',
        'catatan_tindak_lanjut',
        'ditindaklanjuti_oleh',
        'ditindaklanjuti_at',
        'dibuat_oleh',
        'guru_pelapor_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'ditindaklanjuti_at' => 'datetime',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function penindakLanjut(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'ditindaklanjuti_oleh'
        );
    }

    public function guruPelapor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'guru_pelapor_id'
        );
    }
}