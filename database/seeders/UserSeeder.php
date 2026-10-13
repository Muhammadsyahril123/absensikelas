<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $akun = [
            [
                'username' => '1234567890',
                'name' => 'Andi',
                'email' => '1234567890@absensi.local',
                'role' => 'siswa',
            ],
            [
                'username' => '1987654321',
                'name' => 'Budi Santoso',
                'email' => '1987654321@absensi.local',
                'role' => 'guru',
            ],
            [
                'username' => '1999999999',
                'name' => 'Guru Piket',
                'email' => '1999999999@absensi.local',
                'role' => 'guru_piket',
            ],
        ];

        foreach ($akun as $data) {
            DB::table('users')->updateOrInsert(
                ['username' => $data['username']],
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make('123456'),
                    'role' => $data['role'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}