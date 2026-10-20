<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sebelumnya memaksa siswa mengisi alasan keterlambatan.
 * Dinonaktifkan: status terlambat di absensi sudah cukup.
 * Hukuman / pemanggilan ditangani guru piket di sekolah.
 */
class WajibAlasanKeterlambatan
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
