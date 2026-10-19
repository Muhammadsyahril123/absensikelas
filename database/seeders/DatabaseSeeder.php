<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\PengaturanSekolah;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // -------------------------------------------------
        // 1. Pengaturan sekolah
        //    Sesuaikan latitude/longitude dengan lokasi sekolah
        // -------------------------------------------------
        PengaturanSekolah::create([
            'nama_sekolah' => 'SMK Informatika Sumedang',
            'latitude_sekolah' => -6.8591000,
            'longitude_sekolah' => 107.9239000,
            'radius_meter' => 150,
            'jam_masuk' => '06:30:00',
            'batas_toleransi' => '06:40:00',
            'batas_wajib_alasan' => '08:00:00',
            'jam_tutup_absen' => '10:00:00',
        ]);

        // -------------------------------------------------
        // 2. 12 Kelas — hanya angkatan XII
        //    9 RPL + 3 DKV
        // -------------------------------------------------
        $kelasData = [
            // RPL
            ['nama_kelas' => 'XII RPL 1', 'jurusan' => 'RPL'],
            ['nama_kelas' => 'XII RPL 2', 'jurusan' => 'RPL'],
            ['nama_kelas' => 'XII RPL 3', 'jurusan' => 'RPL'],
            ['nama_kelas' => 'XII RPL 4', 'jurusan' => 'RPL'],
            ['nama_kelas' => 'XII RPL 5', 'jurusan' => 'RPL'],
            ['nama_kelas' => 'XII RPL 6', 'jurusan' => 'RPL'],
            ['nama_kelas' => 'XII RPL 7', 'jurusan' => 'RPL'],
            ['nama_kelas' => 'XII RPL 8', 'jurusan' => 'RPL'],
            ['nama_kelas' => 'XII RPL 9', 'jurusan' => 'RPL'],
            // DKV
            ['nama_kelas' => 'XII DKV 1', 'jurusan' => 'DKV'],
            ['nama_kelas' => 'XII DKV 2', 'jurusan' => 'DKV'],
            ['nama_kelas' => 'XII DKV 3', 'jurusan' => 'DKV'],
        ];

        $kelasModels = [];
        foreach ($kelasData as $k) {
            $kelasModels[$k['nama_kelas']] = Kelas::create($k);
        }

        // -------------------------------------------------
        // 3. Guru Piket — login pakai NIP
        // -------------------------------------------------
        $userPiket = User::create([
            'name' => 'Ahmad Fauzi',
            'username' => '99999999',
            'password' => Hash::make('password'),
            'role' => 'guru_piket',
            'wajib_ganti_password' => false,
        ]);

        Guru::create([
            'user_id' => $userPiket->id,
            'nip' => '99999999',
            'nama' => 'Ahmad Fauzi',
        ]);

        // -------------------------------------------------
        // 4. Guru biasa (pengajar) — bisa lapor ketidakhadiran
        // -------------------------------------------------
        $userGuruPengajar = User::create([
            'name' => 'Siti Rahayu',
            'username' => '88888888',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'wajib_ganti_password' => false,
        ]);

        $guruPengajar = Guru::create([
            'user_id' => $userGuruPengajar->id,
            'nip' => '88888888',
            'nama' => 'Siti Rahayu',
        ]);

        $guruPengajar->kelas()->attach($kelasModels['XII RPL 1']->id, [
            'peran' => 'pengajar',
        ]);
        $guruPengajar->kelas()->attach($kelasModels['XII RPL 2']->id, [
            'peran' => 'pengajar',
        ]);

        // -------------------------------------------------
        // 5. Guru Wali Kelas XII RPL 1 — konfirmasi laporan
        // -------------------------------------------------
        $userWali = User::create([
            'name' => 'Budi Santoso',
            'username' => '77777777',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'wajib_ganti_password' => false,
        ]);

        $guruWali = Guru::create([
            'user_id' => $userWali->id,
            'nip' => '77777777',
            'nama' => 'Budi Santoso',
        ]);

        $guruWali->kelas()->attach($kelasModels['XII RPL 1']->id, [
            'peran' => 'wali_kelas',
        ]);

        // -------------------------------------------------
        // 6. Siswa contoh — login pakai NIS
        // -------------------------------------------------
        $siswaData = [
            [
                'nama' => 'Andi Pratama',
                'nis' => '11111111',
                'kelas' => 'XII RPL 1',
            ],
            [
                'nama' => 'Siti Aminah',
                'nis' => '22222222',
                'kelas' => 'XII RPL 1',
            ],
            [
                'nama' => 'Rizki Maulana',
                'nis' => '33333333',
                'kelas' => 'XII RPL 2',
            ],
            [
                'nama' => 'Dewi Lestari',
                'nis' => '44444444',
                'kelas' => 'XII DKV 1',
            ],
        ];

        foreach ($siswaData as $s) {
            $userSiswa = User::create([
                'name' => $s['nama'],
                'username' => $s['nis'],
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'wajib_ganti_password' => true,
            ]);

            Siswa::create([
                'user_id' => $userSiswa->id,
                'kelas_id' => $kelasModels[$s['kelas']]->id,
                'nis' => $s['nis'],
                'nama' => $s['nama'],
            ]);
        }

        $this->command?->info('');
        $this->command?->info('=== Seeder selesai ===');
        $this->command?->info('12 kelas: XII RPL 1–9 + XII DKV 1–3');
        $this->command?->info('Login (NIS siswa / NIP guru):');
        $this->command?->info('  Guru Piket  : 197901012003011001 / password');
        $this->command?->info('  Guru biasa  : 198503152008012002 / password  (lapor ketidakhadiran)');
        $this->command?->info('  Wali Kelas  : 198002102005011003 / password  (konfirmasi laporan)');
        $this->command?->info('  Siswa       : 2024001001 / password  (wajib ganti password)');
        $this->command?->info('');
    }
}
