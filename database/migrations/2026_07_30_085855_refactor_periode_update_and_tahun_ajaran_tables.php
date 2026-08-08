<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropForeign(['periode_update_id']);
        });

        Schema::table('laporan', function (Blueprint $table) {
            $table->dropForeign(['periode_update_id']);
        });

        Schema::table('periode_update', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('periode_update', function (Blueprint $table) {
            $table->string('tahun_ajaran', 255)->change();
            $table->primary('tahun_ajaran');
        });

        Schema::table('siswa', function (Blueprint $table) {
            $table->dropColumn('periode_update_id');
        });

        Schema::table('siswa', function (Blueprint $table) {
            $table->string('tahun_ajaran', 255)->nullable()->change();
            $table->foreign('tahun_ajaran')
                ->references('tahun_ajaran')->on('periode_update')
                ->nullOnDelete();
        });

        Schema::table('laporan', function (Blueprint $table) {
            $table->renameColumn('periode_update_id', 'tahun_ajaran');
        });

        Schema::table('laporan', function (Blueprint $table) {
            $table->string('tahun_ajaran', 255)->nullable()->change();
            $table->foreign('tahun_ajaran')
                ->references('tahun_ajaran')->on('periode_update')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
    }
};