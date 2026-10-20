<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RekapAbsensiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        abort_unless($user->role === 'guru' && $user->guru, 403);

        $request->validate([
            'tanggal' => ['nullable', 'date'],
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
        ]);

        $tanggal = $request->input(
            'tanggal',
            now('Asia/Jakarta')->toDateString()
        );

        $daftarKelas = $user->guru->kelas()->orderBy('nama_kelas')->get();

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

        $kelasId = $request->input('kelas_id', $daftarKelas->first()->id);
        $kelasTerpilih = $daftarKelas->firstWhere('id', (int) $kelasId);

        if (! $kelasTerpilih) {
            return redirect()
                ->route('rekap.index')
                ->with('error', 'Kelas yang dipilih tidak ditemukan.');
        }

        $data = $this->buildRekap($kelasTerpilih->id, $tanggal);

        return view('rekap.index', array_merge($data, [
            'daftarKelas' => $daftarKelas,
            'kelasTerpilih' => $kelasTerpilih,
            'tanggal' => $tanggal,
        ]));
    }

    public function export(Request $request): StreamedResponse
    {
        $user = Auth::user();
        abort_unless($user->role === 'guru' && $user->guru, 403);

        $request->validate([
            'tanggal' => ['required', 'date'],
            'kelas_id' => ['required', 'integer', 'exists:kelas,id'],
        ]);

        $kelasId = (int) $request->kelas_id;
        $punyaAkses = $user->guru->kelas()->where('kelas.id', $kelasId)->exists();
        abort_unless($punyaAkses, 403);

        $tanggal = $request->tanggal;
        $data = $this->buildRekap($kelasId, $tanggal);
        $namaKelas = $user->guru->kelas()->where('kelas.id', $kelasId)->first()?->nama_kelas ?? 'kelas';

        $filename = 'rekap-' . str_replace(' ', '-', strtolower($namaKelas)) . '-' . $tanggal . '.csv';

        return response()->streamDownload(function () use ($data, $tanggal, $namaKelas) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM Excel
            fputcsv($out, ['Rekap Absensi', $namaKelas, $tanggal]);
            fputcsv($out, ['NIS', 'Nama', 'Waktu Absen', 'Status']);

            foreach ($data['siswaRekap'] as $s) {
                fputcsv($out, [
                    $s['nis'],
                    $s['nama'],
                    $s['waktu_absen'] ?? '-',
                    $s['status'],
                ]);
            }

            fputcsv($out, []);
            fputcsv($out, ['Total siswa', $data['jumlahSiswa']]);
            fputcsv($out, ['Hadir', $data['jumlahHadir']]);
            fputcsv($out, ['Terlambat', $data['jumlahTerlambat']]);
            fputcsv($out, ['Belum absen', $data['jumlahBelumAbsen']]);
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function buildRekap(int $kelasId, string $tanggal): array
    {
        $daftarSiswa = Siswa::where('kelas_id', $kelasId)->orderBy('nama')->get();
        $ids = $daftarSiswa->pluck('id');

        $dataAbsensi = Absensi::whereIn('siswa_id', $ids)
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('siswa_id');

        $izinDisetujui = \App\Models\PengajuanIzinSakit::whereIn('siswa_id', $ids)
            ->where('status', 'disetujui')
            ->whereDate('tanggal', '<=', $tanggal)
            ->where(function ($q) use ($tanggal) {
                $q->where(function ($q2) use ($tanggal) {
                    $q2->whereNull('tanggal_selesai')->whereDate('tanggal', $tanggal);
                })->orWhere(function ($q2) use ($tanggal) {
                    $q2->whereNotNull('tanggal_selesai')
                        ->whereDate('tanggal', '<=', $tanggal)
                        ->whereDate('tanggal_selesai', '>=', $tanggal);
                });
            })
            ->get()
            ->keyBy('siswa_id');

        $pengaturan = \App\Models\PengaturanSekolah::first();
        $sekarang = now('Asia/Jakarta');
        $hariIni = $sekarang->toDateString();
        $jamTutup = $pengaturan
            ? \Carbon\Carbon::parse($tanggal . ' ' . ($pengaturan->jam_tutup_absen ?? '10:00:00'), 'Asia/Jakarta')
            : \Carbon\Carbon::parse($tanggal . ' 10:00:00', 'Asia/Jakarta');

        // Untuk tanggal lampau, atau hari ini setelah jam tutup → belum absen + tanpa izin = Alfa
        $anggapAlfa = $tanggal < $hariIni || ($tanggal === $hariIni && $sekarang->gte($jamTutup));

        $siswaRekap = $daftarSiswa->map(function ($siswa) use ($dataAbsensi, $izinDisetujui, $anggapAlfa) {
            $absensi = $dataAbsensi->get($siswa->id);
            $izin = $izinDisetujui->get($siswa->id);

            if ($absensi) {
                $status = $absensi->status; // hadir / terlambat
            } elseif ($izin) {
                $status = 'izin';
            } elseif ($anggapAlfa) {
                $status = 'alfa';
            } else {
                $status = 'belum';
            }

            return [
                'nama' => $siswa->nama,
                'nis' => $siswa->nis,
                'waktu_absen' => $absensi?->waktu_absen,
                'status' => $status,
            ];
        });

        return [
            'siswaRekap' => $siswaRekap,
            'jumlahSiswa' => $siswaRekap->count(),
            'jumlahHadir' => $siswaRekap->filter(fn ($s) => $s['status'] === 'hadir')->count(),
            'jumlahTerlambat' => $siswaRekap->filter(fn ($s) => $s['status'] === 'terlambat')->count(),
            'jumlahIzin' => $siswaRekap->filter(fn ($s) => $s['status'] === 'izin')->count(),
            'jumlahAlfa' => $siswaRekap->filter(fn ($s) => $s['status'] === 'alfa')->count(),
            'jumlahBelumAbsen' => $siswaRekap->filter(fn ($s) => $s['status'] === 'belum')->count(),
        ];
    }
}
