<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            if (!Schema::hasColumn('universities', 'settings')) {
                $table->json('settings')->nullable()->after('status');
            }
        });

        if (!Schema::hasTable('rule_sets')) {
            Schema::create('rule_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained('universities')->onDelete('cascade');
            $table->string('name', 150);
            $table->unsignedInteger('version')->default(1);
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->foreignId('cloned_from_rule_set_id')->nullable()->constrained('rule_sets')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['university_id', 'version']);
            $table->index(['university_id', 'status']);
            });
        }

        if (!Schema::hasTable('rules')) {
            Schema::create('rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_set_id')->constrained('rule_sets')->onDelete('cascade');
            $table->foreignId('university_id')->constrained('universities')->onDelete('cascade');
            $table->string('category', 50);
            $table->string('code', 100);
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->string('input_metric', 100)->nullable();
            $table->string('operator', 30)->nullable();
            $table->decimal('threshold_value', 10, 2)->nullable();
            $table->decimal('threshold_to', 10, 2)->nullable();
            $table->decimal('points', 10, 2)->nullable();
            $table->string('calculation_type', 50)->default('fixed');
            $table->json('condition_json')->nullable();
            $table->json('action_json')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['rule_set_id', 'code']);
            $table->index(['university_id', 'category']);
            $table->index(['rule_set_id', 'category']);
            });
        }

        if (!Schema::hasTable('rule_evaluation_results')) {
            Schema::create('rule_evaluation_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_log_id')->nullable()->constrained('daily_logs')->onDelete('cascade');
            $table->foreignId('executive_id')->constrained('executives')->onDelete('cascade');
            $table->foreignId('university_id')->constrained('universities')->onDelete('cascade');
            $table->foreignId('rule_set_id')->constrained('rule_sets')->onDelete('cascade');
            $table->foreignId('rule_id')->nullable()->constrained('rules')->onDelete('set null');
            $table->string('rule_code', 100);
            $table->string('category', 50);
            $table->enum('status', ['passed', 'failed', 'skipped', 'applied'])->default('skipped');
            $table->decimal('points', 10, 2)->default(0);
            $table->string('message', 255)->nullable();
            $table->json('context_snapshot')->nullable();
            $table->timestamps();

            $table->index(['daily_log_id', 'category']);
            $table->index(['executive_id', 'created_at']);
            });
        }

        Schema::table('daily_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('daily_logs', 'rule_set_id')) {
                $table->foreignId('rule_set_id')->nullable()->after('university_id')->constrained('rule_sets')->onDelete('set null');
            }
            if (!Schema::hasColumn('daily_logs', 'kpi_status')) {
                $table->string('kpi_status', 30)->default('not_evaluated')->after('recovery_points');
            }
            if (!Schema::hasColumn('daily_logs', 'violation_status')) {
                $table->string('violation_status', 30)->default('none')->after('kpi_status');
            }
            if (!Schema::hasColumn('daily_logs', 'meeting_window_status')) {
                $table->json('meeting_window_status')->nullable()->after('violation_status');
            }
        });

        Schema::table('score_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('score_transactions', 'rule_set_id')) {
                $table->foreignId('rule_set_id')->nullable()->after('rule_id')->constrained('rule_sets')->onDelete('set null');
            }
            if (!Schema::hasColumn('score_transactions', 'rule_evaluation_result_id')) {
                $table->foreignId('rule_evaluation_result_id')->nullable()->after('rule_set_id')->constrained('rule_evaluation_results')->onDelete('set null');
            }
            if (!Schema::hasColumn('score_transactions', 'component')) {
                $table->string('component', 30)->default('adjustment')->after('type');
            }
        });

        Schema::table('executives', function (Blueprint $table) {
            if (!Schema::hasColumn('executives', 'photo')) {
                $table->string('photo', 255)->nullable()->after('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('executives', function (Blueprint $table) {
            if (Schema::hasColumn('executives', 'photo')) {
                $table->dropColumn('photo');
            }
        });

        Schema::table('score_transactions', function (Blueprint $table) {
            foreach (['rule_evaluation_result_id', 'rule_set_id'] as $column) {
                if (Schema::hasColumn('score_transactions', $column)) {
                    $table->dropConstrainedForeignId($column);
                }
            }
            if (Schema::hasColumn('score_transactions', 'component')) {
                $table->dropColumn('component');
            }
        });

        Schema::table('daily_logs', function (Blueprint $table) {
            if (Schema::hasColumn('daily_logs', 'rule_set_id')) {
                $table->dropConstrainedForeignId('rule_set_id');
            }
            foreach (['kpi_status', 'violation_status', 'meeting_window_status'] as $column) {
                if (Schema::hasColumn('daily_logs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::dropIfExists('rule_evaluation_results');
        Schema::dropIfExists('rules');
        Schema::dropIfExists('rule_sets');

        Schema::table('universities', function (Blueprint $table) {
            if (Schema::hasColumn('universities', 'settings')) {
                $table->dropColumn('settings');
            }
        });
    }
};
