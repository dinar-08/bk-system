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
        Schema::table('siswa', function (Blueprint $table) {
            $table->foreignId('periode_update_id')
                ->nullable()
                ->after('tahun_ajaran')
                ->constrained('periode_update')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropConstrainedForeignId('periode_update_id');
        });
    }
};
