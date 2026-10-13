<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Migration aman: JANGAN migrate:fresh.
     * Menyesuaikan tabel catatan_absensi untuk F-11 & F-12.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        // Perluas enum / tipe kolom jenis
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE catatan_absensi MODIFY COLUMN jenis ENUM(
                'dispensasi',
                'sakit_mendadak',
                'pulang_awal',
                'ketidakhadiran_pelajaran'
            ) NOT NULL DEFAULT 'ketidakhadiran_pelajaran'");
        } else {
            // SQLite: ubah jadi string biasa (tidak enforce enum)
            // Abaikan jika gagal — data tetap bisa pakai value baru
            try {
                Schema::table('catatan_absensi', function (Blueprint $table) {
                    $table->string('jenis', 40)->default('ketidakhadiran_pelajaran')->change();
                });
            } catch (\Throwable $e) {
                // doctrine/dbal mungkin belum terpasang; biarkan saja
            }
        }

        Schema::table('catatan_absensi', function (Blueprint $table) {
            if (!Schema::hasColumn('catatan_absensi', 'status_tindak_lanjut')) {
                $table->string('status_tindak_lanjut', 30)
                    ->default('belum')
                    ->after('keterangan');
            }

            if (!Schema::hasColumn('catatan_absensi', 'catatan_tindak_lanjut')) {
                $table->text('catatan_tindak_lanjut')->nullable()->after('status_tindak_lanjut');
            }

            if (!Schema::hasColumn('catatan_absensi', 'ditindaklanjuti_oleh')) {
                $table->unsignedBigInteger('ditindaklanjuti_oleh')->nullable()->after('catatan_tindak_lanjut');
            }

            if (!Schema::hasColumn('catatan_absensi', 'ditindaklanjuti_at')) {
                $table->timestamp('ditindaklanjuti_at')->nullable()->after('ditindaklanjuti_oleh');
            }
        });

        // FK terpisah agar aman di SQLite
        if ($driver === 'mysql' && Schema::hasColumn('catatan_absensi', 'ditindaklanjuti_oleh')) {
            try {
                Schema::table('catatan_absensi', function (Blueprint $table) {
                    $table->foreign('ditindaklanjuti_oleh')
                        ->references('id')
                        ->on('users')
                        ->nullOnDelete();
                });
            } catch (\Throwable $e) {
                // FK mungkin sudah ada
            }
        }

        // Normalisasi status absensi kapital → lowercase
        DB::table('absensi')->where('status', 'Hadir')->update(['status' => 'hadir']);
        DB::table('absensi')->where('status', 'Terlambat')->update(['status' => 'terlambat']);
    }

    public function down(): void
    {
        Schema::table('catatan_absensi', function (Blueprint $table) {
            if (Schema::hasColumn('catatan_absensi', 'ditindaklanjuti_oleh')) {
                try {
                    $table->dropForeign(['ditindaklanjuti_oleh']);
                } catch (\Throwable $e) {
                }
                $table->dropColumn('ditindaklanjuti_oleh');
            }
            if (Schema::hasColumn('catatan_absensi', 'ditindaklanjuti_at')) {
                $table->dropColumn('ditindaklanjuti_at');
            }
            if (Schema::hasColumn('catatan_absensi', 'catatan_tindak_lanjut')) {
                $table->dropColumn('catatan_tindak_lanjut');
            }
            if (Schema::hasColumn('catatan_absensi', 'status_tindak_lanjut')) {
                $table->dropColumn('status_tindak_lanjut');
            }
        });
    }
};
