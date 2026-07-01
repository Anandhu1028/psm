<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Use direct SQL to avoid requiring doctrine/dbal for column modification
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            \DB::statement('ALTER TABLE `point_transactions` MODIFY `daily_audit_id` BIGINT UNSIGNED NULL');
        } else {
            Schema::table('point_transactions', function (Blueprint $table) {
                $table->foreignId('daily_audit_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            \DB::statement('ALTER TABLE `point_transactions` MODIFY `daily_audit_id` BIGINT UNSIGNED NOT NULL');
        } else {
            Schema::table('point_transactions', function (Blueprint $table) {
                $table->foreignId('daily_audit_id')->nullable(false)->change();
            });
        }
    }
};
