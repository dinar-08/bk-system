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
        Schema::create('pemanggilan', function (Blueprint $table) {
            $table->id('pemanggilan_id');

            $table->unsignedBigInteger('laporan_id');
            $table->foreign('laporan_id')
                ->references('laporan_id')
                ->on('laporan')
                ->cascadeOnDelete();

            $table->string('nip')->nullable();
            $table->foreign('nip')
                ->references('nip')
                ->on('guru_bk')
                ->nullOnDelete();

            $table->date('tanggal_pemanggilan');

            $table->time('waktu_pemanggilan');

            $table->enum('pihak_dipanggil', [
                'siswa',
                'orang_tua',
                'siswa_orang_tua',
            ]);

            $table->text('tujuan');

            $table->enum('status_kehadiran', [
                'belum',
                'hadir',
                'tidak_hadir',
            ])->default('belum');

            $table->enum('tindak_lanjut', [
                'belum',
                'monitoring',
                'selesai',
            ])->default('belum');

            $table->date('tanggal_monitoring')->nullable();

            $table->text('catatan')->nullable();

            $table->timestamps();
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemanggilan');
    }
};