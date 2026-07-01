<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure id is an AUTO_INCREMENT column. Do not attempt to re-create the primary key
        // if it already exists to keep this migration safe to run on databases already fixed.
        DB::statement('ALTER TABLE `daily_audits` MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
    }

    public function down(): void
    {
        // Revert to non-auto-increment bigint id (if you truly need to rollback, be cautious).
        DB::statement('ALTER TABLE `daily_audits` MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL');
    }
};
