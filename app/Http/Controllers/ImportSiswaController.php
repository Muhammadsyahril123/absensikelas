<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ImportSiswaController extends Controller
{
    public function index()
    {
        abort_unless(Auth::user()->role === 'guru', 403);

        $kelas = Kelas::orderBy('nama_kelas')->get();

        return view('import.siswa', compact('kelas'));
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->role === 'guru', 403);

        $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        if (! $handle) {
            return back()->withErrors(['file' => 'File tidak bisa dibaca.']);
        }

        // Format CSV: nis,nama (baris pertama boleh header)
        $baris = 0;
        $berhasil = 0;
        $gagal = [];

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {
                $baris++;

                if (count($row) < 2) {
                    continue;
                }

                $nis = trim($row[0]);
                $nama = trim($row[1]);

                // Skip header
                if ($baris === 1 && strtolower($nis) === 'nis') {
                    continue;
                }

                if ($nis === '' || $nama === '') {
                    $gagal[] = "Baris {$baris}: NIS/nama kosong";
                    continue;
                }

                if (User::where('username', $nis)->exists() || Siswa::where('nis', $nis)->exists()) {
                    $gagal[] = "Baris {$baris}: NIS {$nis} sudah ada";
                    continue;
                }

                $user = User::create([
                    'name' => $nama,
                    'username' => $nis,
                    'password' => Hash::make('password'),
                    'role' => 'siswa',
                    'wajib_ganti_password' => true,
                ]);

                Siswa::create([
                    'user_id' => $user->id,
                    'kelas_id' => $request->kelas_id,
                    'nis' => $nis,
                    'nama' => $nama,
                ]);

                $berhasil++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);

            return back()->withErrors(['file' => 'Import gagal: ' . $e->getMessage()]);
        }

        fclose($handle);

        $pesan = "Berhasil import {$berhasil} siswa.";
        if (count($gagal)) {
            $pesan .= ' Gagal: ' . implode('; ', array_slice($gagal, 0, 5));
            if (count($gagal) > 5) {
                $pesan .= ' ...';
            }
        }

        return back()->with('success', $pesan);
    }
}
