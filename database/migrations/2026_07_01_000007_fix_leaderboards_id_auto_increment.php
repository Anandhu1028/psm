<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE `leaderboards` MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `leaderboards` MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL');
        try { DB::statement('ALTER TABLE `leaderboards` DROP PRIMARY KEY'); } catch (\Throwable $e) {}
    }
};
