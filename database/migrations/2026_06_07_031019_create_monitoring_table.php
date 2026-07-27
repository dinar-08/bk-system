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
        Schema::create('monitoring', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('laporan_id');

            $table->foreign('laporan_id')
                ->references('laporan_id')
                ->on('laporan')
                ->cascadeOnDelete();

            $table->string('nip')->nullable();
            $table->foreign('nip')
                ->references('nip')->on('guru_bk')
                ->nullOnDelete();

            $table->date('tanggal_monitoring');
            $table->time('waktu_monitoring')->nullable();

            $table->date('tanggal_monitoring_berikutnya')->nullable();
            $table->time('waktu_monitoring_berikutnya')->nullable();

            $table->integer('monitoring_ke');

            $table->enum('status_monitoring', [
                'terjadwal',
                'selesai',
            ])->default('selesai');

            $table->enum('status_perkembangan', [
                'membaik',
                'stabil',
                'menurun',
            ]);

            $table->text('catatan_perkembangan');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring');
    }
};