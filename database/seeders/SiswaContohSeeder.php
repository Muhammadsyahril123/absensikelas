<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Support\Facades\Hash;

class SiswaContohSeeder extends Seeder
{
    public function run(): void
    {
        $kelas = Kelas::where('nama_kelas', 'XII RPL 1')->first();

        if (!$kelas) {
            $this->command->error(
                'Kelas XII RPL 1 tidak ditemukan.'
            );

            return;
        }

        $dataSiswa = [
            [
                'nis' => '1234567891',
                'nama' => 'Budi Santoso',
            ],
            [
                'nis' => '1234567892',
                'nama' => 'Citra Amelia',
            ],
            [
                'nis' => '1234567893',
                'nama' => 'Dimas Pratama',
            ],
        ];

        foreach ($dataSiswa as $data) {
            // Lewati jika NIS sudah terdaftar.
            if (Siswa::where('nis', $data['nis'])->exists()) {
                $this->command->info(
                    $data['nama'] . ' sudah terdaftar, dilewati.'
                );

                continue;
            }

            // Buat akun menggunakan struktur login proyek.
            $user = User::create([
                'username' => $data['nis'],
                'name' => $data['nama'],
                'email' => $data['nis'] . '@contoh.local',
                'password' => Hash::make('password123'),
                'role' => 'siswa',
                'wajib_ganti_password' => true,
            ]);

            Siswa::create([
                'user_id' => $user->id,
                'kelas_id' => $kelas->id,
                'nis' => $data['nis'],
                'nama' => $data['nama'],
            ]);

            $this->command->info(
                $data['nama'] . ' berhasil ditambahkan.'
            );
        }
    }
}