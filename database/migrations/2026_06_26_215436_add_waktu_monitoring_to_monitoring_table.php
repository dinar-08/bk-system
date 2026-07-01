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
        Schema::table('monitoring', function (Blueprint $table) {
            $table->time('waktu_monitoring')->nullable()->after('tanggal_monitoring');
            $table->time('waktu_monitoring_berikutnya')->nullable()->after('tanggal_monitoring_berikutnya');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring', function (Blueprint $table) {
            $table->dropColumn([
                'waktu_monitoring',
                'waktu_monitoring_berikutnya',
            ]);
        });
    }
};
