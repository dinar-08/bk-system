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
            if (!Schema::hasColumn('pemanggilan', 'guru_bk_id')) {
                $table->foreignId('guru_bk_id')->nullable()->after('laporan_id')->constrained('guru_bk')->nullOnDelete();
            }
        });
    }
    public function down(): void
    {
        Schema::table('pemanggilan', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\GuruBK::class);
            $table->dropColumn('guru_bk_id');
        });
    }

};
