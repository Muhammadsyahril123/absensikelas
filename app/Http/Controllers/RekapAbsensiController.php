<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class RekapAbsensiController extends Controller
{
    public function index(Request $request)
    {
        // Pastikan pengguna sudah login sebagai guru.
        $user = Auth::user();

        abort_unless(
            $user && $user->role === 'guru',
            403,
            'Anda tidak memiliki akses ke halaman ini.'
        );

        // Validasi filter tanggal dan kelas.
        $request->validate([
            'tanggal' => ['nullable', 'date', 'before_or_equal:today'],
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
        ]);

        // Tanggal default adalah hari ini.
        $tanggal = $request->input(
            'tanggal',
            now('Asia/Jakarta')->toDateString()
        );

        // Ambil SEMUA kelas dari database.
        $daftarKelas = Kelas::orderBy('nama_kelas')->get();

        // Jika belum ada kelas.
        if ($daftarKelas->isEmpty()) {
            return view('rekap.index', [
                'daftarKelas' => $daftarKelas,
                'kelasTerpilih' => null,
                'tanggal' => $tanggal,
                'siswaRekap' => collect(),
                'jumlahSiswa' => 0,
                'jumlahHadir' => 0,
                'jumlahTerlambat' => 0,
                'jumlahBelumAbsen' => 0,
            ]);
        }

        // Pilih kelas berdasarkan filter atau kelas pertama.
        $kelasId = $request->input(
            'kelas_id',
            $daftarKelas->first()->id
        );

        $kelasTerpilih = $daftarKelas->firstWhere(
            'id',
            (int) $kelasId
        );

        // Pastikan kelas pilihan tersedia.
        if (!$kelasTerpilih) {
            return redirect()
                ->route('rekap.index')
                ->with('error', 'Kelas yang dipilih tidak ditemukan.');
        }

        // Ambil seluruh siswa dari kelas terpilih.
        $daftarSiswa = Siswa::where(
            'kelas_id',
            $kelasTerpilih->id
        )
            ->orderBy('nama')
            ->get();

        // Ambil absensi berdasarkan tanggal dan siswa kelas tersebut.
        $dataAbsensi = Absensi::whereIn(
            'siswa_id',
            $daftarSiswa->pluck('id')
        )
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('siswa_id');

        // Gabungkan data siswa dengan data absensi.
        $siswaRekap = $daftarSiswa->map(
            function ($siswa) use ($dataAbsensi) {
                $absensi = $dataAbsensi->get($siswa->id);

                $status = $absensi?->status ?? 'Belum Absen';

                return [
                    'nama' => $siswa->nama,
                    'nis' => $siswa->nis,
                    'waktu_absen' => $absensi?->waktu_absen,
                    'status' => $status,
                ];
            }
        );

        // Hitung ringkasan absensi.
        $jumlahSiswa = $siswaRekap->count();

        $jumlahHadir = $siswaRekap->filter(
            fn ($siswa) => strtolower(trim($siswa['status'])) === 'hadir'
        )->count();

        $jumlahTerlambat = $siswaRekap->filter(
            fn ($siswa) => strtolower(trim($siswa['status'])) === 'terlambat'
        )->count();

        $jumlahBelumAbsen = $siswaRekap->filter(
            fn ($siswa) => strtolower(trim($siswa['status'])) === 'belum absen'
        )->count();

        return view('rekap.index', compact(
            'daftarKelas',
            'kelasTerpilih',
            'tanggal',
            'siswaRekap',
            'jumlahSiswa',
            'jumlahHadir',
            'jumlahTerlambat',
            'jumlahBelumAbsen'
        ));
    }
}