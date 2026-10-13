<?php

namespace App\Http\Controllers;

use App\Models\PengajuanIzinSakit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengajuanIzinSakitController extends Controller
{
    public function index()
    {
        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            abort(403, 'Data siswa tidak ditemukan.');
        }

        $pengajuan = PengajuanIzinSakit::where('siswa_id', $siswa->id)
            ->orderByDesc('created_at')
            ->get();

        return view('pengajuan.index', compact('pengajuan'));
    }

    public function store(Request $request)
    {
        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            abort(403, 'Data siswa tidak ditemukan.');
        }

        $data = $request->validate([
            'jenis' => ['required', 'in:izin,sakit'],
            'alasan' => ['required', 'string', 'max:2000'],
            'bukti' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048',
            ],
        ], [
            'jenis.required' => 'Pilih jenis pengajuan.',
            'jenis.in' => 'Jenis pengajuan tidak valid.',
            'alasan.required' => 'Alasan wajib diisi.',
            'alasan.max' => 'Alasan maksimal 2000 karakter.',
            'bukti.mimes' => 'Bukti harus berupa JPG, PNG, atau PDF.',
            'bukti.max' => 'Ukuran bukti maksimal 2 MB.',
        ]);

        $tanggalHariIni = Carbon::now('Asia/Jakarta')->toDateString();

        // Cegah siswa mengajukan izin/sakit lebih dari sekali sehari.
        $sudahMengajukan = PengajuanIzinSakit::where(
            'siswa_id',
            $siswa->id
        )
            ->whereDate('tanggal', $tanggalHariIni)
            ->exists();

        if ($sudahMengajukan) {
            return back()->withErrors([
                'pengajuan' => 'Kamu sudah mengajukan izin/sakit hari ini. Pengajuan hanya boleh satu kali sehari.',
            ])->withInput();
        }

        $path = null;

        if ($request->hasFile('bukti')) {
            $path = $request->file('bukti')->store(
                'bukti-pengajuan',
                'local'
            );
        }

        try {
            DB::transaction(function () use (
                $siswa,
                $data,
                $tanggalHariIni,
                $path
            ) {
                // Periksa ulang agar tidak melewati batas pengajuan harian.
                $sudahMengajukan = PengajuanIzinSakit::where(
                    'siswa_id',
                    $siswa->id
                )
                    ->whereDate('tanggal', $tanggalHariIni)
                    ->lockForUpdate()
                    ->exists();

                if ($sudahMengajukan) {
                    throw new \RuntimeException(
                        'Kamu sudah mengajukan izin/sakit hari ini.'
                    );
                }

                $pengajuan = new PengajuanIzinSakit();
                $pengajuan->siswa_id = $siswa->id;
                $pengajuan->tanggal = $tanggalHariIni;
                $pengajuan->jenis = $data['jenis'];
                $pengajuan->alasan = $data['alasan'];
                $pengajuan->bukti = $path;
                $pengajuan->status = 'menunggu';
                $pengajuan->save();
            });
        } catch (\RuntimeException $e) {
            if ($path) {
                Storage::disk('local')->delete($path);
            }

            return back()->withErrors([
                'pengajuan' => $e->getMessage(),
            ])->withInput();
        } catch (\Throwable $e) {
            if ($path) {
                Storage::disk('local')->delete($path);
            }

            throw $e;
        }

        return redirect()
            ->route('pengajuan.index')
            ->with('success', 'Pengajuan berhasil dikirim dan menunggu pemeriksaan.');
    }
}