<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
        'jurusan',
    ];

    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    public function guru(): BelongsToMany
    {
        return $this->belongsToMany(
            Guru::class,
            'guru_kelas',
            'kelas_id',
            'guru_id'
        )->withPivot('peran')->withTimestamps();
    }
}