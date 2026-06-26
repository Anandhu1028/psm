<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            // category: kpi | positive | negative | recovery | tier
            $table->string('category', 30);
            // calculation_type: range | boolean | streak | per_unit | selected_violation | tier_range | recovery_cap
            $table->string('calculation_type', 50);
            $table->string('code')->index(); // unique per company
            $table->string('name');
            $table->decimal('points', 8, 2)->default(0);
            // For range-based rules
            $table->decimal('threshold_min', 10, 2)->nullable();
            $table->decimal('threshold_max', 10, 2)->nullable();
            // For single threshold rules
            $table->string('operator', 10)->nullable(); // >=, <=, >, <, between, =
            $table->decimal('threshold_value', 10, 2)->nullable();
            // What metric from the audit context to use
            $table->string('input_metric', 80)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('condition_json')->nullable();
            $table->json('action_json')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'category', 'is_active']);
            $table->unique(['company_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rules');
    }
};
