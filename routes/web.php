<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GantiPasswordController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\RiwayatAbsensiController;
use App\Http\Controllers\RekapAbsensiController;
use App\Http\Controllers\PengajuanIzinSakitController;
use App\Http\Controllers\PengajuanKeterlambatanController;
use App\Http\Controllers\RekapKelasSiswaController;
use App\Http\Controllers\DispensasiController;
use App\Http\Controllers\PemeriksaanPengajuanController;
use App\Http\Controllers\CatatanAbsensiController;

/*
|--------------------------------------------------------------------------
| Login dan Logout
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [
    LoginController::class,
    'create',
])->name('login');

Route::post('/login', [
    LoginController::class,
    'store',
])->name('login.store');

Route::post('/logout', [
    LoginController::class,
    'destroy',
])->middleware('auth')->name('logout');


/*
|--------------------------------------------------------------------------
| Ganti Password
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/ganti-password', [
        GantiPasswordController::class,
        'index',
    ])->name('password.change');

    Route::post('/ganti-password', [
        GantiPasswordController::class,
        'update',
    ])->name('password.update');
});


/*
|--------------------------------------------------------------------------
| Dashboard Redirect
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    $role = auth()->user()->role;

    return match ($role) {
        'siswa' => redirect()->route('dashboard.siswa'),
        'guru' => redirect()->route('dashboard.guru'),
        'guru_piket' => redirect()->route('dashboard.guru_piket'),
        default => abort(403),
    };
})->middleware('auth')->name('dashboard');


/*
|--------------------------------------------------------------------------
| Dashboard dan Fitur Siswa
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:siswa',
    'wajib.ganti.password',
    'wajib.alasan.keterlambatan',
])->group(function () {

    Route::get('/dashboard/siswa', function () {
        $siswa = auth()->user()->siswa;

        return view('dashboard.siswa', compact('siswa'));
    })->name('dashboard.siswa');

    // Absensi GPS
    Route::post('/absensi', [
        AbsensiController::class,
        'absen',
    ])->name('absensi.absen');

    // Riwayat absensi
    Route::get('/absensi/riwayat', [
        RiwayatAbsensiController::class,
        'index',
    ])->name('absensi.riwayat');

    // Pengajuan izin dan sakit
    Route::get('/pengajuan-izin-sakit', [
        PengajuanIzinSakitController::class,
        'index',
    ])->name('pengajuan.index');

    Route::post('/pengajuan-izin-sakit', [
        PengajuanIzinSakitController::class,
        'store',
    ])->name('pengajuan.store');

    // Pengajuan keterlambatan
    Route::get('/pengajuan-keterlambatan', [
        PengajuanKeterlambatanController::class,
        'index',
    ])->name('keterlambatan.index');

    Route::post('/pengajuan-keterlambatan', [
        PengajuanKeterlambatanController::class,
        'store',
    ])->name('keterlambatan.store');

    // Rekap kelas siswa
    Route::get('/rekap-kelas', [
        RekapKelasSiswaController::class,
        'index',
    ])->name('rekap-kelas.index');

    // Informasi dispensasi
    Route::get('/siswa/dispensasi', [
        DispensasiController::class,
        'siswa',
    ])->name('dispensasi.siswa');
});


/*
|--------------------------------------------------------------------------
| Dashboard dan Fitur Guru
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:guru'])->group(function () {

    Route::get('/dashboard/guru', function () {
        return view('dashboard.guru');
    })->name('dashboard.guru');

    // Rekap absensi seluruh kelas
    Route::get('/rekap-absensi', [
        RekapAbsensiController::class,
        'index',
    ])->name('rekap.index');

    // Informasi dispensasi
    Route::get('/guru/dispensasi', [
        DispensasiController::class,
        'guru',
    ])->name('dispensasi.guru');

    /*
    |--------------------------------------------------------------------------
    | Catatan Ketidakhadiran
    |--------------------------------------------------------------------------
    */

    Route::get('/catatan', [
        CatatanAbsensiController::class,
        'index',
    ])->name('catatan.index');

    Route::get('/catatan/buat', [
        CatatanAbsensiController::class,
        'create',
    ])->name('catatan.create');

    Route::post('/catatan', [
        CatatanAbsensiController::class,
        'store',
    ])->name('catatan.store');

    // AJAX: mengambil siswa berdasarkan kelas
    // Harus terdaftar agar form Buat Catatan tidak error.
    Route::get('/catatan/siswa-by-kelas', [
        CatatanAbsensiController::class,
        'siswaByKelas',
    ])->name('catatan.siswa-by-kelas');

    // Wali kelas menindaklanjuti laporan
    Route::patch('/catatan/{catatan}/tindak-lanjut', [
        CatatanAbsensiController::class,
        'tindakLanjut',
    ])->name('catatan.tindak-lanjut');
});


/*
|--------------------------------------------------------------------------
| Dashboard dan Fitur Guru Piket
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:guru_piket'])->group(function () {

    Route::get('/dashboard/guru-piket', function () {
        return view('dashboard.guru_piket');
    })->name('dashboard.guru_piket');

    // Dispensasi
    Route::get('/guru-piket/dispensasi', [
        DispensasiController::class,
        'index',
    ])->name('dispensasi.piket');

    Route::post('/guru-piket/dispensasi', [
        DispensasiController::class,
        'store',
    ])->name('dispensasi.store');

    Route::delete('/guru-piket/dispensasi/{dispensasi}', [
        DispensasiController::class,
        'destroy',
    ])->name('dispensasi.destroy');

    // Pemeriksaan pengajuan
    Route::get('/guru-piket/pemeriksaan', [
        PemeriksaanPengajuanController::class,
        'index',
    ])->name('guru-piket.pemeriksaan');

    Route::patch('/guru-piket/pemeriksaan/izin-sakit/{id}', [
        PemeriksaanPengajuanController::class,
        'periksaIzinSakit',
    ])->name('guru-piket.pemeriksaan.izin-sakit');

    Route::patch('/guru-piket/pemeriksaan/keterlambatan/{id}', [
        PemeriksaanPengajuanController::class,
        'periksaKeterlambatan',
    ])->name('guru-piket.pemeriksaan.keterlambatan');

    // Melihat catatan ketidakhadiran
    Route::get('/guru-piket/catatan', [
        CatatanAbsensiController::class,
        'index',
    ])->name('guru-piket.catatan');

    // Menindaklanjuti catatan
    Route::patch('/guru-piket/catatan/{catatan}/tindak-lanjut', [
        CatatanAbsensiController::class,
        'tindakLanjut',
    ])->name('guru-piket.catatan.tindak-lanjut');
});