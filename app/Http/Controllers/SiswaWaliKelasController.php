<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;

class SiswaWaliKelasController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        abort_unless(
            $user->role === 'guru' && $user->guru,
            403,
            'Akses hanya untuk guru.'
        );

        $guru = $user->guru;

        $kelasWali = $guru->kelasWali()->get();

        $kelasIds = $kelasWali->pluck('id');

        $siswa = Siswa::with(['user', 'kelas'])
            ->whereIn('kelas_id', $kelasIds)
            ->orderBy('nama')
            ->paginate(15);

        return view(
            'guru-kelas.data-siswa',
            compact('siswa', 'kelasWali')
        );
    }
}