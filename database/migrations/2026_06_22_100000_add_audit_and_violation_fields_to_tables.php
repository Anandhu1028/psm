<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_logs', function (Blueprint $table) {
            $table->foreignId('approved_by')->nullable()->after('created_by')->constrained('users')->onDelete('set null');
            $table->integer('positive_points')->default(0)->after('calculated_score');
            $table->integer('negative_points')->default(0)->after('positive_points');
            $table->integer('recovery_points')->default(0)->after('negative_points');
        });

        Schema::table('violations', function (Blueprint $table) {
            $table->string('description', 255)->nullable()->after('points_deducted');
            $table->string('violation_subtype', 100)->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('violations', function (Blueprint $table) {
            $table->dropColumn(['description', 'violation_subtype']);
        });

        Schema::table('daily_logs', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['approved_by', 'positive_points', 'negative_points', 'recovery_points']);
        });
    }
};
