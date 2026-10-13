<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Guru extends Model
{
    protected $table = 'guru';

    protected $fillable = [
        'user_id',
        'nip',
        'nama',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(
            Kelas::class,
            'guru_kelas',
            'guru_id',
            'kelas_id'
        )->withPivot('peran')->withTimestamps();
    }
}