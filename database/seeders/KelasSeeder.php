<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        foreach (range(1, 9) as $nomor) {
            DB::table('kelas')->updateOrInsert(
                ['nama_kelas' => "XII RPL {$nomor}"],
                ['jurusan' => 'RPL']
            );
        }

        foreach (range(1, 3) as $nomor) {
            DB::table('kelas')->updateOrInsert(
                ['nama_kelas' => "XII DKV {$nomor}"],
                ['jurusan' => 'DKV']
            );
        }
    }
}