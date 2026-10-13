<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispensasis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('siswa_id')
                ->nullable()
                ->constrained('siswa')
                ->nullOnDelete();

            $table->foreignId('kelas_id')
                ->nullable()
                ->constrained('kelas')
                ->nullOnDelete();

            $table->foreignId('guru_piket_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('judul', 150);
            $table->text('keterangan');
            $table->date('tanggal');
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispensasis');
    }
};