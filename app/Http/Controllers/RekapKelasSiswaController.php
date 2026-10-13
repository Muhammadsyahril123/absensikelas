<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class RekapKelasSiswaController extends Controller
{
    public function index(Request $request)
    {
        $siswaLogin = Auth::user()->siswa;

        if (!$siswaLogin || !$siswaLogin->kelas_id) {
            abort(403, 'Data kelas siswa tidak ditemukan.');
        }

        $tanggalHariIni = Carbon::now('Asia/Jakarta')->toDateString();

        $validator = Validator::make($request->all(), [
            'tanggal' => [
                'nullable',
                'date_format:Y-m-d',
                'before_or_equal:' . $tanggalHariIni,
            ],
        ], [
            'tanggal.date_format' => 'Format tanggal tidak valid.',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.',
        ]);

        $validator->validate();

        $tanggal = $request->input('tanggal', $tanggalHariIni);

        $daftarSiswa = Siswa::where(
            'kelas_id',
            $siswaLogin->kelas_id
        )
            ->orderBy('nama')
            ->get();

        $dataAbsensi = Absensi::whereIn(
            'siswa_id',
            $daftarSiswa->pluck('id')
        )
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('siswa_id');

        return view('rekap-kelas.index', [
            'siswa' => $siswaLogin,
            'daftarSiswa' => $daftarSiswa,
            'dataAbsensi' => $dataAbsensi,
            'tanggal' => $tanggal,
        ]);
    }
}