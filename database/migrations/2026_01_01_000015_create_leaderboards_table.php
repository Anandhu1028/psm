<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leaderboards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('executive_id')->constrained()->cascadeOnDelete();
            $table->year('year');
            $table->tinyInteger('month'); // 1-12
            $table->integer('rank')->default(0);
            $table->integer('current_score')->default(0);
            $table->integer('monthly_score')->default(0);
            $table->string('tier', 30)->default('bronze');
            $table->string('trend', 10)->default('stable'); // up | down | stable
            $table->integer('previous_rank')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'executive_id', 'year', 'month']);
            $table->index(['company_id', 'year', 'month', 'rank']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaderboards');
    }
};
