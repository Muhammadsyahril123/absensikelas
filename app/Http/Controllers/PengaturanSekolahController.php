<?php

namespace App\Http\Controllers;

use App\Models\PengaturanSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengaturanSekolahController extends Controller
{
    public function edit()
    {
        abort_unless(Auth::user()->role === 'guru_piket', 403);

        $pengaturan = PengaturanSekolah::first();

        if (! $pengaturan) {
            $pengaturan = PengaturanSekolah::create([
                'nama_sekolah' => 'SMK Informatika Sumedang',
                'latitude_sekolah' => -6.8591000,
                'longitude_sekolah' => 107.9239000,
                'radius_meter' => 150,
                'jam_masuk' => '06:30:00',
                'batas_toleransi' => '06:40:00',
                'batas_wajib_alasan' => '08:00:00',
                'jam_tutup_absen' => '10:00:00',
            ]);
        }

        return view('pengaturan.edit', compact('pengaturan'));
    }

    public function update(Request $request)
    {
        abort_unless(Auth::user()->role === 'guru_piket', 403);

        $data = $request->validate([
            'nama_sekolah' => ['required', 'string', 'max:150'],
            'latitude_sekolah' => ['required', 'numeric', 'between:-90,90'],
            'longitude_sekolah' => ['required', 'numeric', 'between:-180,180'],
            'radius_meter' => ['required', 'integer', 'min:10', 'max:5000'],
            'jam_masuk' => ['required', 'date_format:H:i'],
            'batas_toleransi' => ['required', 'date_format:H:i'],
            'batas_wajib_alasan' => ['required', 'date_format:H:i'],
            'jam_tutup_absen' => ['required', 'date_format:H:i'],
        ]);

        // Simpan dengan detik agar cocok kolom time
        foreach (['jam_masuk', 'batas_toleransi', 'batas_wajib_alasan', 'jam_tutup_absen'] as $field) {
            $data[$field] = $data[$field] . ':00';
        }

        $pengaturan = PengaturanSekolah::firstOrFail();
        $pengaturan->update($data);

        return back()->with('success', 'Pengaturan sekolah berhasil disimpan.');
    }
}
