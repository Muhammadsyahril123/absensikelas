<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Guru;
use Illuminate\Database\Seeder;

class ProfilSeeder extends Seeder
{
    public function run(): void
    {
        $kelas = Kelas::where('nama_kelas', 'XII RPL 1')->firstOrFail();

        $userSiswa = User::where('username', '1234567890')->firstOrFail();

        Siswa::updateOrCreate(
            ['user_id' => $userSiswa->id],
            [
                'kelas_id' => $kelas->id,
                'nis' => '1234567890',
                'nama' => 'Andi',
            ]
        );

        $userGuru = User::where('username', '1987654321')->firstOrFail();

        $guru = Guru::updateOrCreate(
            ['user_id' => $userGuru->id],
            [
                'nip' => '1987654321',
                'nama' => 'Budi Santoso',
            ]
        );

        $guru->kelas()->syncWithoutDetaching([
            $kelas->id => ['peran' => 'wali_kelas'],
        ]);

        $userPiket = User::where('username', '1999999999')->firstOrFail();

        Guru::updateOrCreate(
            ['user_id' => $userPiket->id],
            [
                'nip' => '1999999999',
                'nama' => 'Guru Piket',
            ]
        );
    }
}