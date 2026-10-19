<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catatan_absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->enum('jenis', [
                'dispensasi',
                'sakit_mendadak',
                'pulang_awal',
                'ketidakhadiran_pelajaran',
            ]);
            $table->date('tanggal');
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->string('kegiatan', 150)->nullable();
            $table->text('keterangan')->nullable();

            // Tindak lanjut
            $table->enum('status_tindak_lanjut', [
                'belum',
                'sudah_dihubungi',
                'perlu_tindak_lanjut',
                'selesai',
            ])->default('belum');
            $table->text('catatan_tindak_lanjut')->nullable();
            $table->foreignId('ditindaklanjuti_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('ditindaklanjuti_at')->nullable();

            // Siapa yang membuat / melapor
            $table->foreignId('dibuat_oleh')->constrained('users')->restrictOnDelete();
            $table->foreignId('guru_pelapor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_absensi');
    }
};
