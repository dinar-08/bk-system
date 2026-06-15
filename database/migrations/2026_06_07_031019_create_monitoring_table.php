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

            $table->foreignId('laporan_id')
                ->constrained('laporan')
                ->cascadeOnDelete();

            $table->foreignId('guru_bk_id')
                ->constrained('guru_bk')
                ->cascadeOnDelete();

            $table->date('tanggal_monitoring');

            $table->integer('monitoring_ke');

            $table->enum('status_perkembangan', [
                'membaik',
                'stabil',
                'menurun'
            ]);

            $table->text('catatan_perkembangan');

            $table->text('tindak_lanjut');

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
