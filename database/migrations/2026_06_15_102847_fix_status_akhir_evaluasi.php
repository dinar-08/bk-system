<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */

    public function up(): void
    {
        DB::statement("ALTER TABLE evaluasi MODIFY COLUMN status_akhir ENUM('selesai', 'dirujuk', 'monitoring_lanjutan', 'saran_rujukan') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE evaluasi MODIFY COLUMN status_akhir ENUM('selesai', 'monitoring_lanjutan', 'saran_rujukan') NOT NULL");
    }
};

