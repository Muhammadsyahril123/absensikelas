<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;

class RiwayatAbsensiController extends Controller
{
    public function index()
    {
        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            abort(403, 'Data siswa tidak ditemukan.');
        }

        $riwayat = Absensi::where('siswa_id', $siswa->id)
            ->orderByDesc('tanggal')
            ->orderByDesc('waktu_absen')
            ->paginate(10);

        return view('absensi.riwayat', compact('riwayat'));
    }
}