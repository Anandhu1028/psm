<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rule_evaluation_results', function (Blueprint $table) {
            if (!Schema::hasColumn('rule_evaluation_results', 'executive_id')) {
                $table->unsignedBigInteger('executive_id')->nullable()->after('daily_log_id');
            }

            if (!Schema::hasColumn('rule_evaluation_results', 'university_id')) {
                $table->unsignedBigInteger('university_id')->nullable()->after('executive_id');
            }

            if (!Schema::hasColumn('rule_evaluation_results', 'rule_set_id')) {
                $table->unsignedBigInteger('rule_set_id')->nullable()->after('university_id');
            }

            if (!Schema::hasColumn('rule_evaluation_results', 'category')) {
                $table->string('category', 50)->nullable()->after('rule_set_id');
            }

            if (!Schema::hasColumn('rule_evaluation_results', 'status')) {
                $table->string('status', 50)->nullable()->after('category');
            }

            if (!Schema::hasColumn('rule_evaluation_results', 'message')) {
                $table->text('message')->nullable()->after('points');
            }

            if (!Schema::hasColumn('rule_evaluation_results', 'context_snapshot')) {
                $table->json('context_snapshot')->nullable()->after('message');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rule_evaluation_results', function (Blueprint $table) {
            $columns = [
                'executive_id', 'university_id', 'rule_set_id', 'category', 'status', 'message', 'context_snapshot'
            ];

            foreach ($columns as $col) {
                if (Schema::hasColumn('rule_evaluation_results', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
