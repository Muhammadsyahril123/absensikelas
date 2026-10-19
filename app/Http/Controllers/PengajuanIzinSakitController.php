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

        if (! $siswa) {
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

        if (! $siswa) {
            abort(403, 'Data siswa tidak ditemukan.');
        }

        $hariIni = Carbon::now('Asia/Jakarta')->toDateString();

        $data = $request->validate([
            'jenis' => ['required', 'in:izin,sakit'],
            'tanggal' => ['required', 'date', 'after_or_equal:' . $hariIni],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal'],
            'alasan' => ['required', 'string', 'max:2000'],
            'bukti' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'jenis.required' => 'Pilih jenis pengajuan.',
            'tanggal.required' => 'Tanggal mulai wajib diisi.',
            'tanggal.after_or_equal' => 'Tanggal tidak boleh sebelum hari ini.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'alasan.required' => 'Alasan wajib diisi.',
            'bukti.mimes' => 'Bukti harus JPG, PNG, atau PDF.',
            'bukti.max' => 'Ukuran bukti maksimal 2 MB.',
        ]);

        $tanggalMulai = $data['tanggal'];
        $tanggalSelesai = $data['tanggal_selesai'] ?? $tanggalMulai;

        // Cek overlap pengajuan aktif (menunggu / disetujui)
        $overlap = PengajuanIzinSakit::where('siswa_id', $siswa->id)
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->where(function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->whereDate('tanggal', '<=', $tanggalSelesai)
                    ->where(function ($q2) use ($tanggalMulai) {
                        $q2->whereNull('tanggal_selesai')
                            ->whereDate('tanggal', '>=', $tanggalMulai)
                            ->orWhereDate('tanggal_selesai', '>=', $tanggalMulai);
                    });
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors([
                'pengajuan' => 'Sudah ada pengajuan yang overlap di tanggal tersebut.',
            ])->withInput();
        }

        $path = null;

        if ($request->hasFile('bukti')) {
            $path = $request->file('bukti')->store('bukti-pengajuan', 'local');
        }

        try {
            PengajuanIzinSakit::create([
                'siswa_id' => $siswa->id,
                'tanggal' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'jenis' => $data['jenis'],
                'alasan' => $data['alasan'],
                'bukti' => $path,
                'status' => 'menunggu',
            ]);
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
