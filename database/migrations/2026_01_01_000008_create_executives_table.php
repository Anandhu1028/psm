<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('executives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('zone_id')->constrained()->cascadeOnDelete();
            $table->string('employee_id')->unique();
            $table->string('name');
            $table->string('mobile')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('photo')->nullable();
            $table->date('date_joined')->nullable();
            $table->date('probation_end_date')->nullable();
            $table->enum('status', ['probation', 'active', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->integer('current_score')->default(0);
            $table->integer('monthly_score')->default(0);
            $table->string('current_tier', 30)->default('bronze');
            // Streak tracking
            $table->integer('call_streak_count')->default(0);
            $table->integer('meeting_streak_count')->default(0);
            $table->integer('best_call_streak')->default(0);
            $table->integer('best_meeting_streak')->default(0);
            $table->date('streak_last_updated')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
            $table->index(['zone_id', 'status']);
            $table->index('current_score');
            $table->index('current_tier');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('executives');
    }
};
