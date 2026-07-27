<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{

    public function up(): void
    {
     
        DB::statement('ALTER TABLE `monitoring` CHANGE `id` `monitoring_id` BIGINT UNSIGNED AUTO_INCREMENT');
    }

    /**
     * Rollback migration.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE `monitoring` CHANGE `monitoring_id` `id` BIGINT UNSIGNED AUTO_INCREMENT');
    }
};