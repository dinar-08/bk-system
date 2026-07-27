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
        Schema::create('siswa', function (Blueprint $table) {
            $table->string('nis')->primary();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('nama_siswa');
            $table->string('kelas');
            $table->string('tahun_ajaran')->nullable();

            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();

            $table->date('tanggal_lahir')->nullable();

            $table->text('alamat')->nullable();

            $table->string('nama_ortu')->nullable();

            $table->string('no_whatsapp')->nullable();

            $table->string('foto')->nullable();

            $table->timestamp('last_data_updated_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
