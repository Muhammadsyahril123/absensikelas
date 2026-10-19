<?php

namespace App\Http\Controllers;

use App\Models\CatatanAbsensi;
use App\Models\Kelas;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CatatanAbsensiController extends Controller
{
    /**
     * Daftar catatan ketidakhadiran saat pelajaran.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        abort_unless(
            in_array($user->role, ['guru', 'guru_piket'], true),
            403
        );

        $request->validate([
            'tanggal' => ['nullable', 'date'],
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
        ]);

        $tanggal = $request->input(
            'tanggal',
            Carbon::now('Asia/Jakarta')->toDateString()
        );

        $query = CatatanAbsensi::with([
            'siswa.kelas',
            'pembuat',
            'penindakLanjut',
            'guruPelapor',
        ])
            ->where('jenis', 'ketidakhadiran_pelajaran')
            ->whereDate('tanggal', $tanggal)
            ->orderByDesc('created_at');

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        $catatan = $query->get();

        $daftarKelas = Kelas::orderBy('nama_kelas')->get();

        // Kelas yang diampu sebagai wali kelas.
        $kelasWaliIds = collect();

        if ($user->role === 'guru' && $user->guru) {
            $kelasWaliIds = $user->guru->kelas()
                ->wherePivot('peran', 'wali_kelas')
                ->pluck('kelas.id');
        }

        return view('catatan.index', compact(
            'catatan',
            'daftarKelas',
            'tanggal',
            'kelasWaliIds',
            'user'
        ));
    }

    /**
     * Form membuat catatan baru.
     */
    public function create()
    {
        abort_unless(Auth::user()->role === 'guru', 403);

        $daftarKelas = Kelas::orderBy('nama_kelas')->get();

        return view('catatan.create', compact('daftarKelas'));
    }

    /**
     * Mengambil daftar siswa berdasarkan kelas.
     */
    public function siswaByKelas(Request $request)
    {
        abort_unless(
            in_array(Auth::user()->role, ['guru', 'guru_piket'], true),
            403
        );

        $request->validate([
            'kelas_id' => ['required', 'integer', 'exists:kelas,id'],
        ]);

        $siswa = Siswa::where('kelas_id', $request->kelas_id)
            ->orderBy('nama')
            ->get(['id', 'nama', 'nis']);

        return response()->json($siswa);
    }

    /**
     * Menyimpan laporan ketidakhadiran siswa.
     */
    public function store(Request $request)
    {
        abort_unless(Auth::user()->role === 'guru', 403);

        $data = $request->validate([
            'siswa_id' => [
                'required',
                'integer',
                'exists:siswa,id',
            ],
            'tanggal' => [
                'required',
                'date',
            ],
            'waktu_mulai' => [
                'nullable',
                'date_format:H:i',
            ],
            'waktu_selesai' => [
                'nullable',
                'date_format:H:i',
                'after:waktu_mulai',
            ],
            'kegiatan' => [
                'required',
                'string',
                'max:150',
            ],
            'keterangan' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        CatatanAbsensi::create([
            'siswa_id' => $data['siswa_id'],
            'guru_pelapor_id' => Auth::id(),
            'jenis' => 'ketidakhadiran_pelajaran',
            'tanggal' => $data['tanggal'],
            'waktu_mulai' => $data['waktu_mulai'] ?? null,
            'waktu_selesai' => $data['waktu_selesai'] ?? null,
            'kegiatan' => $data['kegiatan'],
            'keterangan' => $data['keterangan'] ?? null,
            'dibuat_oleh' => Auth::id(),
            'status_tindak_lanjut' => 'belum',
        ]);

        return redirect()
            ->route('catatan.index')
            ->with(
                'success',
                'Catatan ketidakhadiran berhasil disimpan.'
            );
    }

    /**
     * Memperbarui tindak lanjut laporan.
     */
    public function tindakLanjut(
        Request $request,
        CatatanAbsensi $catatan
    ) {
        $user = Auth::user();

        abort_unless(
            in_array($user->role, ['guru', 'guru_piket'], true),
            403
        );

        if ($catatan->jenis !== 'ketidakhadiran_pelajaran') {
            abort(404);
        }

        // Guru hanya boleh menindaklanjuti laporan siswa
        // di kelas yang menjadi tanggung jawabnya sebagai wali kelas.
        if ($user->role === 'guru') {
            $kelasId = $catatan->siswa?->kelas_id;

            $adalahWali = $user->guru
                && $user->guru->kelas()
                    ->wherePivot('peran', 'wali_kelas')
                    ->where('kelas.id', $kelasId)
                    ->exists();

            if (!$adalahWali) {
                abort(
                    403,
                    'Hanya wali kelas siswa tersebut yang dapat menindaklanjuti laporan ini.'
                );
            }
        }

        $data = $request->validate([
            'status_tindak_lanjut' => [
                'required',
                Rule::in([
                    'belum',
                    'sudah_dihubungi',
                    'perlu_tindak_lanjut',
                    'selesai',
                ]),
            ],
            'catatan_tindak_lanjut' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $catatan->update([
            'status_tindak_lanjut' => $data['status_tindak_lanjut'],
            'catatan_tindak_lanjut' => $data['catatan_tindak_lanjut'] ?? null,
            'ditindaklanjuti_oleh' => $user->id,
            'ditindaklanjuti_at' => Carbon::now('Asia/Jakarta'),
        ]);

        $redirectRoute = $user->role === 'guru_piket'
            ? 'guru-piket.catatan'
            : 'catatan.index';

        return redirect()
            ->route($redirectRoute, [
                'tanggal' => $catatan->tanggal->toDateString(),
            ])
            ->with(
                'success',
                'Tindak lanjut berhasil disimpan.'
            );
    }
}