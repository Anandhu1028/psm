<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tier_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('executive_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('daily_audit_id')->nullable()->constrained('daily_audits')->nullOnDelete();
            $table->string('old_tier', 30);
            $table->string('new_tier', 30);
            $table->text('change_reason')->nullable();
            $table->integer('score_at_change')->default(0);
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->index(['executive_id', 'changed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tier_histories');
    }
};
