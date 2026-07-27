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
        Schema::create('laporan', function (Blueprint $table) {
            $table->id('laporan_id');

            $table->string('nis');
            $table->foreign('nis')
                ->references('nis')->on('siswa')
                ->cascadeOnDelete();

            $table->string('nip')->nullable();
            $table->foreign('nip')
                ->references('nip')->on('guru_bk')
                ->nullOnDelete();

            $table->string('judul_laporan');

            $table->enum('kategori', [
                'akademik',
                'sosial',
                'perilaku',
                'emosional',
                'lain-lain',
            ])->nullable();

            $table->string('jenis_masalah')->nullable();

            $table->text('deskripsi');

            $table->string('bukti')->nullable();

            $table->enum('status', [
                'baru',
                'pemanggilan',
                'monitoring',
                'selesai',
                'dirujuk',
            ])->default('baru');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};