<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\PengaturanSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function absen(Request $request)
    {
        $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $user = Auth::user();

        if ($user->role !== 'siswa' || !$user->siswa) {
            abort(403, 'Hanya siswa yang dapat melakukan absensi.');
        }

        $pengaturan = PengaturanSekolah::first();

        if (!$pengaturan) {
            return back()->withErrors([
                'absensi' => 'Pengaturan lokasi sekolah belum tersedia.',
            ]);
        }

        $sekarang = Carbon::now('Asia/Jakarta');
        $tanggal = $sekarang->toDateString();
        $siswaId = $user->siswa->id;

        // Batasan jam absen (dari jam_masuk - 1 jam s/d jam_tutup_absen)
        $jamBuka = Carbon::parse(
            $tanggal . ' ' . ($pengaturan->jam_masuk ?? '06:30:00'),
            'Asia/Jakarta'
        )->subHour();

        $jamTutup = Carbon::parse(
            $tanggal . ' ' . ($pengaturan->jam_tutup_absen ?? '10:00:00'),
            'Asia/Jakarta'
        );

        if ($sekarang->lt($jamBuka)) {
            return back()->withErrors([
                'absensi' => 'Belum waktunya absen. Absen dibuka mulai ' . $jamBuka->format('H:i') . '.',
            ]);
        }

        if ($sekarang->gt($jamTutup)) {
            return back()->withErrors([
                'absensi' => 'Waktu absen sudah ditutup pukul ' . $jamTutup->format('H:i') . '.',
            ]);
        }

        $latitude = (float) $request->latitude;
        $longitude = (float) $request->longitude;

        $jarak = $this->hitungJarak(
            $latitude,
            $longitude,
            (float) $pengaturan->latitude_sekolah,
            (float) $pengaturan->longitude_sekolah
        );

        if ($jarak > (float) $pengaturan->radius_meter) {
            return back()->withErrors([
                'absensi' => 'Anda berada di luar area sekolah.',
            ]);
        }

        $absensi = DB::transaction(function () use (
            $siswaId,
            $sekarang,
            $tanggal,
            $latitude,
            $longitude,
            $jarak,
            $pengaturan
        ) {
            $sudahAbsen = Absensi::where('siswa_id', $siswaId)
                ->whereDate('tanggal', $tanggal)
                ->lockForUpdate()
                ->exists();

            if ($sudahAbsen) {
                return null;
            }

            $batasToleransi = Carbon::parse(
                $tanggal . ' ' . $pengaturan->batas_toleransi,
                'Asia/Jakarta'
            );

            $status = $sekarang->gt($batasToleransi)
                ? 'terlambat'
                : 'hadir';

            return Absensi::create([
                'siswa_id' => $siswaId,
                'tanggal' => $tanggal,
                'waktu_absen' => $sekarang->format('H:i:s'),
                'status' => $status,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'jarak_meter' => round($jarak, 2),
            ]);
        });

        if (!$absensi) {
            return back()->withErrors([
                'absensi' => 'Anda sudah melakukan absensi hari ini.',
            ]);
        }

        // Status terlambat sudah tercatat di absensi.
        // Tidak perlu pengajuan/konfirmasi di web — penanganan di sekolah oleh guru piket.
        return back()->with(
            'success',
            'Absensi berhasil. Status: ' . $absensi->status
        );
    }

    private function hitungJarak(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $radiusBumi = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1))
            * cos(deg2rad($lat2))
            * sin($dLon / 2) ** 2;

        return $radiusBumi * 2 * atan2(
            sqrt($a),
            sqrt(1 - $a)
        );
    }
}