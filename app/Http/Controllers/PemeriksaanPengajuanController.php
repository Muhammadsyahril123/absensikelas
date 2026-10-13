<?php

namespace App\Http\Controllers;

use App\Models\PengajuanIzinSakit;
use App\Models\PengajuanKeterlambatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PemeriksaanPengajuanController extends Controller
{
    public function index()
    {
        $pengajuanIzinSakit = PengajuanIzinSakit::with([
            'siswa.kelas',
            'pemeriksa',
        ])
            ->orderByRaw(
                "CASE WHEN status = 'menunggu' THEN 0 ELSE 1 END"
            )
            ->orderByDesc('tanggal')
            ->get();

        $pengajuanKeterlambatan = PengajuanKeterlambatan::with([
            'siswa.kelas',
            'pemeriksa',
            'absensi',
        ])
            ->orderByRaw(
                "CASE WHEN status = 'menunggu' THEN 0 ELSE 1 END"
            )
            ->orderByDesc('id')
            ->get();

        return view('guru-piket.pemeriksaan', compact(
            'pengajuanIzinSakit',
            'pengajuanKeterlambatan'
        ));
    }

    public function periksaIzinSakit(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => [
                'required',
                Rule::in(['disetujui', 'ditolak']),
            ],
        ]);

        $berhasil = DB::transaction(function () use ($id, $data) {
            $pengajuan = PengajuanIzinSakit::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($pengajuan->status !== 'menunggu') {
                return false;
            }

            $pengajuan->status = $data['status'];
            $pengajuan->diperiksa_oleh = Auth::id();
            $pengajuan->diperiksa_pada = now('Asia/Jakarta');
            $pengajuan->save();

            return true;
        });

        if (! $berhasil) {
            return back()->with(
                'error',
                'Pengajuan ini sudah pernah diperiksa.'
            );
        }

        return back()->with(
            'success',
            'Pengajuan izin/sakit berhasil diperiksa.'
        );
    }

    public function periksaKeterlambatan(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => [
                'required',
                Rule::in(['disetujui', 'ditolak']),
            ],
        ]);

        $berhasil = DB::transaction(function () use ($id, $data) {
            $pengajuan = PengajuanKeterlambatan::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($pengajuan->status !== 'menunggu') {
                return false;
            }

            $pengajuan->status = $data['status'];
            $pengajuan->diperiksa_oleh = Auth::id();
            $pengajuan->diperiksa_pada = now('Asia/Jakarta');
            $pengajuan->save();

            return true;
        });

        if (! $berhasil) {
            return back()->with(
                'error',
                'Pengajuan keterlambatan sudah pernah diperiksa.'
            );
        }

        return back()->with(
            'success',
            'Pengajuan keterlambatan berhasil diperiksa.'
        );
    }
}