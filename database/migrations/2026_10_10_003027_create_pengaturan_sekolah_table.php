<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sekolah', 150);
            $table->decimal('latitude_sekolah', 10, 7);
            $table->decimal('longitude_sekolah', 10, 7);
            $table->unsignedInteger('radius_meter')->default(100);
            $table->time('jam_masuk')->default('06:30:00');
            $table->time('batas_toleransi')->default('06:40:00');
            $table->time('batas_wajib_alasan')->default('08:00:00');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_sekolah');
    }
};