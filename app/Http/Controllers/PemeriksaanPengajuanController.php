<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\PengajuanIzinSakit;
use App\Models\PengaturanSekolah;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PemeriksaanPengajuanController extends Controller
{
    public function index()
    {
        $hariIni = Carbon::now('Asia/Jakarta')->toDateString();
        $sekarang = Carbon::now('Asia/Jakarta');

        $pengaturan = PengaturanSekolah::first();
        $jamTutup = $pengaturan
            ? Carbon::parse($hariIni . ' ' . ($pengaturan->jam_tutup_absen ?? '10:00:00'), 'Asia/Jakarta')
            : Carbon::parse($hariIni . ' 10:00:00', 'Asia/Jakarta');
        $sudahLewatJamTutup = $sekarang->gte($jamTutup);

        $pengajuanIzinSakit = PengajuanIzinSakit::with(['siswa.kelas', 'pemeriksa'])
            ->orderByRaw("CASE WHEN status = 'menunggu' THEN 0 ELSE 1 END")
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        $absensiHariIni = Absensi::with(['siswa.kelas'])
            ->whereDate('tanggal', $hariIni)
            ->get()
            ->keyBy('siswa_id');

        // Izin disetujui yang mencakup hari ini
        $izinDisetujuiHariIni = PengajuanIzinSakit::with(['siswa.kelas'])
            ->where('status', 'disetujui')
            ->whereDate('tanggal', '<=', $hariIni)
            ->where(function ($q) use ($hariIni) {
                $q->where(function ($q2) use ($hariIni) {
                    $q2->whereNull('tanggal_selesai')->whereDate('tanggal', $hariIni);
                })->orWhere(function ($q2) use ($hariIni) {
                    $q2->whereNotNull('tanggal_selesai')
                        ->whereDate('tanggal', '<=', $hariIni)
                        ->whereDate('tanggal_selesai', '>=', $hariIni);
                });
            })
            ->get()
            ->keyBy('siswa_id');

        $semuaSiswa = Siswa::with('kelas')->orderBy('nama')->get();

        $hadir = collect();
        $terlambat = collect();
        $izin = collect();
        $alfa = collect();
        $belum = collect();

        foreach ($semuaSiswa as $siswa) {
            $abs = $absensiHariIni->get($siswa->id);
            $izinRow = $izinDisetujuiHariIni->get($siswa->id);

            if ($abs && $abs->status === 'hadir') {
                $hadir->push(['siswa' => $siswa, 'absensi' => $abs]);
            } elseif ($abs && $abs->status === 'terlambat') {
                $terlambat->push(['siswa' => $siswa, 'absensi' => $abs]);
            } elseif ($izinRow) {
                $izin->push(['siswa' => $siswa, 'izin' => $izinRow]);
            } elseif ($sudahLewatJamTutup) {
                $alfa->push(['siswa' => $siswa]);
            } else {
                $belum->push(['siswa' => $siswa]);
            }
        }

        $jumlahMenungguIzin = $pengajuanIzinSakit->where('status', 'menunggu')->count();

        return view('guru-piket.pemeriksaan', [
            'pengajuanIzinSakit' => $pengajuanIzinSakit,
            'jumlahMenungguIzin' => $jumlahMenungguIzin,
            'hariIni' => $hariIni,
            'sudahLewatJamTutup' => $sudahLewatJamTutup,
            'jamTutup' => $jamTutup->format('H:i'),
            'ringkasan' => [
                'hadir' => $hadir->count(),
                'terlambat' => $terlambat->count(),
                'izin' => $izin->count(),
                'alfa' => $alfa->count(),
                'belum' => $belum->count(),
                'total' => $semuaSiswa->count(),
            ],
            'daftarTerlambat' => $terlambat,
            'daftarAlfa' => $alfa,
            'daftarIzin' => $izin,
        ]);
    }

    public function periksaIzinSakit(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['disetujui', 'ditolak'])],
        ]);

        $berhasil = DB::transaction(function () use ($id, $data) {
            $pengajuan = PengajuanIzinSakit::whereKey($id)->lockForUpdate()->firstOrFail();

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
            return back()->with('error', 'Pengajuan ini sudah pernah diperiksa.');
        }

        return back()->with('success', 'Pengajuan izin/sakit berhasil diperiksa.');
    }

    public function periksaKeterlambatan(Request $request, int $id)
    {
        return back()->with(
            'error',
            'Konfirmasi keterlambatan tidak digunakan. Lihat ringkasan harian di halaman ini.'
        );
    }
}
