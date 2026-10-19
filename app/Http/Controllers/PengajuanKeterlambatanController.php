<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\PengajuanKeterlambatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanKeterlambatanController extends Controller
{
    public function index()
    {
        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            abort(403, 'Data siswa tidak ditemukan.');
        }

        $pengajuan = PengajuanKeterlambatan::with('absensi')
            ->where('siswa_id', $siswa->id)
            ->latest()
            ->get();

        return view('keterlambatan.index', compact('pengajuan'));
    }

    public function store(Request $request)
    {
        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            abort(403, 'Data siswa tidak ditemukan.');
        }

        $data = $request->validate([
            'tanggal' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'alasan' => [
                'required',
                'string',
                'max:2000',
            ],
        ], [
            'tanggal.required' => 'Tanggal wajib dipilih.',
            'tanggal.date' => 'Tanggal tidak valid.',
            'tanggal.before_or_equal' =>
                'Tanggal tidak boleh di masa depan.',
            'alasan.required' => 'Alasan keterlambatan wajib diisi.',
            'alasan.max' => 'Alasan maksimal 2000 karakter.',
        ]);

        // Cari absensi terlambat milik siswa pada tanggal tersebut.
        $absensi = Absensi::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', $data['tanggal'])
            ->where('status', 'terlambat')
            ->first();

        if (!$absensi) {
            return back()
                ->withInput()
                ->withErrors([
                    'tanggal' =>
                        'Data absensi terlambat pada tanggal tersebut tidak ditemukan.',
                ]);
        }

        // Cegah pengajuan ganda untuk absensi yang sama.
        $sudahMengajukan = PengajuanKeterlambatan::where(
            'absensi_id',
            $absensi->id
        )->exists();

        if ($sudahMengajukan) {
            return back()
                ->withInput()
                ->withErrors([
                    'tanggal' =>
                        'Kamu sudah mengajukan alasan untuk tanggal tersebut.',
                ]);
        }

        // Simpan pengajuan alasan keterlambatan.
        PengajuanKeterlambatan::create([
            'siswa_id' => $siswa->id,
            'absensi_id' => $absensi->id,
            'alasan' => $data['alasan'],
            'status' => 'menunggu',
        ]);

        return redirect()
            ->route('keterlambatan.index')
            ->with(
                'success',
                'Alasan keterlambatan berhasil dikirim.'
            );
    }
}