<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration ini digabung ke create_users_table.
 * Dibiarkan kosong agar tidak error jika sudah di-run sebelumnya.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Kolom username, role, dan wajib_ganti_password
        // sudah didefinisikan di migration create_users_table.
    }

    public function down(): void
    {
        //
    }
};
