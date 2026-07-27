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
        Schema::table('users', function (Blueprint $table) {
            // Menghitung percobaan login gagal berturut-turut.
            // Direset ke 0 setiap kali login berhasil.
            $table->unsignedTinyInteger('failed_login_attempts')->default(0)->after('must_change_password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('failed_login_attempts');
        });
    }
};
