<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add university_id (nullable initially so we can migrate existing data)
        Schema::table('executives', function (Blueprint $table) {
            $table->foreignId('university_id')->after('id')->nullable()->constrained('universities')->onDelete('cascade');
        });

        Schema::table('score_rules', function (Blueprint $table) {
            $table->foreignId('university_id')->after('id')->nullable()->constrained('universities')->onDelete('cascade');
            // Drop unique index
            $table->dropUnique('score_rules_rule_key_unique');
        });

        Schema::table('daily_logs', function (Blueprint $table) {
            $table->foreignId('university_id')->after('id')->nullable()->constrained('universities')->onDelete('cascade');
        });

        Schema::table('meetings', function (Blueprint $table) {
            $table->foreignId('university_id')->after('id')->nullable()->constrained('universities')->onDelete('cascade');
        });

        Schema::table('violations', function (Blueprint $table) {
            $table->foreignId('university_id')->after('id')->nullable()->constrained('universities')->onDelete('cascade');
        });

        // 2. Insert Default TIMS University
        $timsId = DB::table('universities')->insertGetId([
            'name' => 'TIMS University',
            'code' => 'TIMS',
            'status' => 'active',
            'theme_color' => '#8b5cf6',
            'tier_colors' => json_encode([
                'platinum' => '#c084fc',
                'gold' => '#f59e0b',
                'silver' => '#9ca3af',
                'bronze' => '#b45309',
                'review_zone' => '#ef4444',
            ]),
            'description' => 'Pre-configured TIMS University.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Update existing records with the default TIMS University ID
        DB::table('executives')->update(['university_id' => $timsId]);
        DB::table('score_rules')->update(['university_id' => $timsId]);
        DB::table('daily_logs')->update(['university_id' => $timsId]);
        DB::table('meetings')->update(['university_id' => $timsId]);
        DB::table('violations')->update(['university_id' => $timsId]);

        // 4. Create composite unique index on score_rules
        Schema::table('score_rules', function (Blueprint $table) {
            $table->unique(['university_id', 'rule_key']);
        });
    }

    public function down(): void
    {
        Schema::table('score_rules', function (Blueprint $table) {
            $table->dropUnique(['university_id', 'rule_key']);
            $table->unique('rule_key');
        });

        Schema::table('violations', function (Blueprint $table) {
            $table->dropForeign(['university_id']);
            $table->dropColumn('university_id');
        });

        Schema::table('meetings', function (Blueprint $table) {
            $table->dropForeign(['university_id']);
            $table->dropColumn('university_id');
        });

        Schema::table('daily_logs', function (Blueprint $table) {
            $table->dropForeign(['university_id']);
            $table->dropColumn('university_id');
        });

        Schema::table('score_rules', function (Blueprint $table) {
            $table->dropForeign(['university_id']);
            $table->dropColumn('university_id');
        });

        Schema::table('executives', function (Blueprint $table) {
            $table->dropForeign(['university_id']);
            $table->dropColumn('university_id');
        });
    }
};
