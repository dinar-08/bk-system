<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Jalankan migration.
     *
     * Menambahkan 2 kolom snapshot ke tabel laporan:
     * - periode_update_id : mencatat tahun ajaran (periode) saat laporan dibuat
     * - kelas              : mencatat kelas siswa saat laporan dibuat
     *
     * Kedua kolom ini WAJIB diisi otomatis di LaporanController@store,
     * agar histori laporan (riwayat.show) tetap akurat walau siswa
     * sudah naik kelas atau tahun ajaran sudah berganti.
     */
    public function up(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            $table->unsignedBigInteger('periode_update_id')
                ->nullable()
                ->after('nip');

            $table->string('kelas', 50)
                ->nullable()
                ->after('periode_update_id');

            $table->foreign('periode_update_id')
                ->references('id')
                ->on('periode_update')
                ->nullOnDelete();
        });
    }

    /**
     * Batalkan migration.
     */
    public function down(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            $table->dropForeign(['periode_update_id']);
            $table->dropColumn(['periode_update_id', 'kelas']);
        });
    }
};