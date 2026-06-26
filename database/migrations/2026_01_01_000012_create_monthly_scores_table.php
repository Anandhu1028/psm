<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('executive_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->year('year');
            $table->tinyInteger('month'); // 1-12
            $table->integer('positive_points')->default(0);
            $table->integer('negative_points')->default(0);
            $table->integer('recovery_points')->default(0);
            $table->integer('net_score')->default(0);
            $table->integer('audit_count')->default(0);
            $table->timestamps();

            $table->unique(['executive_id', 'year', 'month']);
            $table->index(['company_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_scores');
    }
};
