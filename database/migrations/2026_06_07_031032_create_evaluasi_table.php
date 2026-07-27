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
        Schema::create('evaluasi', function (Blueprint $table) {
            $table->id('evaluasi_id');

            $table->unsignedBigInteger('laporan_id')->unique();
            $table->foreign('laporan_id')
                ->references('laporan_id')
                ->on('laporan')
                ->cascadeOnDelete();

            $table->string('nip')->nullable();
            $table->foreign('nip')
                ->references('nip')
                ->on('guru_bk')
                ->nullOnDelete();

            $table->date('tanggal_evaluasi');

            $table->text('hasil_evaluasi');

            $table->enum('status_akhir', [
                'selesai',
                'dirujuk',
            ]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluasi');
    }
};