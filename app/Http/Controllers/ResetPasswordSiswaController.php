<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ResetPasswordSiswaController extends Controller
{
    public function reset(Siswa $siswa): RedirectResponse
    {
        $user = Auth::user();

        abort_unless(
            $user->role === 'guru' && $user->guru,
            403,
            'Akses hanya untuk guru.'
        );

        $guru = $user->guru;

        $berwenang = $guru->kelasWali()
            ->where('kelas.id', $siswa->kelas_id)
            ->exists();

        abort_unless(
            $berwenang,
            403,
            'Lu bukan wali kelas siswa ini.'
        );

        $akunSiswa = $siswa->user;

        if (!$akunSiswa) {
            return back()->withErrors([
                'reset_password' => 'Akun siswa tidak ditemukan.',
            ]);
        }

        $akunSiswa->password = Hash::make('123456');
        $akunSiswa->wajib_ganti_password = true;
        $akunSiswa->save();

        return back()->with(
            'success',
            'Password siswa berhasil direset menjadi 123456. Siswa wajib mengganti password saat login.'
        );
    }
}