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
            $table->id();

            $table->foreignId('laporan_id')
                ->unique()
                ->constrained('laporan')
                ->cascadeOnDelete();

            $table->foreignId('guru_bk_id')
                ->constrained('guru_bk')
                ->cascadeOnDelete();

            $table->date('tanggal_evaluasi');

            $table->text('hasil_evaluasi');

            $table->text('rekomendasi');

            $table->enum('status_akhir', [
                'selesai',
                'monitoring_lanjutan',
                'saran_rujukan'
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
