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
        Schema::table('pemanggilan', function (Blueprint $table) {
            $table->enum('tindak_lanjut', [
                'belum',
                'monitoring',
                'selesai'
            ])->default('belum')->after('status_kehadiran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemanggilan', function (Blueprint $table) {
            $table->dropColumn('tindak_lanjut');
        });
    }
};
