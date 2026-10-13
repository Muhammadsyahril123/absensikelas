<?php

namespace App\Http\Middleware;

use App\Models\Absensi;
use App\Models\PengajuanKeterlambatan;
use App\Models\PengaturanSekolah;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WajibAlasanKeterlambatan
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || $user->role !== 'siswa' || !$user->siswa) {
            return $next($request);
        }

        $sekarang = Carbon::now('Asia/Jakarta');
        $tanggal = $sekarang->toDateString();

        $pengaturan = PengaturanSekolah::first();

        if (!$pengaturan) {
            return $next($request);
        }

        $batasWajibAlasan = Carbon::parse(
            $tanggal . ' ' . $pengaturan->batas_wajib_alasan,
            'Asia/Jakarta'
        );

        if ($sekarang->lt($batasWajibAlasan)) {
            return $next($request);
        }

        $absensi = Absensi::where('siswa_id', $user->siswa->id)
            ->whereDate('tanggal', $tanggal)
            ->where('status', 'Terlambat')
            ->first();

        if (!$absensi) {
            return $next($request);
        }

        $sudahMengajukan = PengajuanKeterlambatan::where(
            'absensi_id',
            $absensi->id
        )->exists();

        if ($sudahMengajukan) {
            return $next($request);
        }

        if (
            $request->routeIs(
                'keterlambatan.index',
                'keterlambatan.store',
                'logout',
                'password.change',
                'password.update'
            )
        ) {
            return $next($request);
        }

        return redirect()
            ->route('keterlambatan.index')
            ->with(
                'wajib_alasan',
                'Lu wajib mengirim alasan keterlambatan sebelum melanjutkan.'
            );
    }
}