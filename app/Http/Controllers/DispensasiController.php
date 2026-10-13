<?php

namespace App\Http\Controllers;

use App\Models\Dispensasi;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DispensasiController extends Controller
{
    // Daftar dispensasi untuk guru piket.
    public function index()
    {
        $dispensasi = Dispensasi::with([
            'siswa',
            'kelas',
            'guruPiket',
        ])->latest()->get();

        $kelas = Kelas::orderBy('nama_kelas')->get();

        $siswa = Siswa::with('kelas')
            ->orderBy('nama')
            ->get();

        return view('dispensasi.piket', compact(
            'dispensasi',
            'kelas',
            'siswa'
        ));
    }

    // Menyimpan dispensasi baru.
    public function store(Request $request)
    {
        $data = $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'keterangan' => ['required', 'string', 'max:3000'],
            'tanggal' => ['required', 'date'],
            'waktu_mulai' => ['nullable', 'date_format:H:i'],
            'waktu_selesai' => [
                'nullable',
                'date_format:H:i',
                'after:waktu_mulai',
            ],
            'tujuan' => [
                'required',
                Rule::in(['sekolah', 'kelas', 'siswa']),
            ],
        ]);

        $tujuan = $data['tujuan'];

        // Dispensasi untuk seluruh sekolah.
        if ($tujuan === 'sekolah') {
            $data['siswa_id'] = null;
            $data['kelas_id'] = null;
        }

        // Dispensasi untuk kelas tertentu.
        elseif ($tujuan === 'kelas') {
            $request->validate([
                'kelas_id' => [
                    'required',
                    'exists:kelas,id',
                ],
            ]);

            $data['kelas_id'] = $request->input('kelas_id');
            $data['siswa_id'] = null;
        }

        // Dispensasi untuk siswa tertentu.
        elseif ($tujuan === 'siswa') {
            $request->validate([
                'siswa_id' => [
                    'required',
                    'exists:siswa,id',
                ],
            ]);

            $data['siswa_id'] = $request->input('siswa_id');
            $data['kelas_id'] = null;
        }

        unset($data['tujuan']);

        $data['guru_piket_id'] = Auth::id();

        Dispensasi::create($data);

        return redirect()
            ->route('dispensasi.piket')
            ->with('success', 'Informasi dispensasi berhasil ditambahkan.');
    }

    // Menghapus dispensasi.
    public function destroy(Dispensasi $dispensasi)
    {
        $dispensasi->delete();

        return redirect()
            ->route('dispensasi.piket')
            ->with('success', 'Informasi dispensasi berhasil dihapus.');
    }

    // Daftar dispensasi yang berlaku untuk siswa.
    public function siswa()
    {
        $siswa = Auth::user()->siswa;

        abort_unless($siswa, 403, 'Data siswa tidak ditemukan.');

        $dispensasi = Dispensasi::with([
            'siswa',
            'kelas',
            'guruPiket',
        ])
            ->whereDate(
                'tanggal',
                '>=',
                now('Asia/Jakarta')->toDateString()
            )
            ->where(function ($query) use ($siswa) {
                $query->where(function ($subQuery) {
                    $subQuery->whereNull('siswa_id')
                        ->whereNull('kelas_id');
                })
                ->orWhere('siswa_id', $siswa->id)
                ->orWhere('kelas_id', $siswa->kelas_id);
            })
            ->orderBy('tanggal')
            ->get();

        return view('dispensasi.siswa', compact('dispensasi'));
    }

    // Daftar dispensasi untuk guru.
    public function guru()
    {
        $dispensasi = Dispensasi::with([
            'siswa',
            'kelas',
            'guruPiket',
        ])
            ->whereDate(
                'tanggal',
                '>=',
                now('Asia/Jakarta')->toDateString()
            )
            ->orderBy('tanggal')
            ->get();

        return view('dispensasi.guru', compact('dispensasi'));
    }
}