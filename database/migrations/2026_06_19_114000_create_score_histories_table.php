<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('score_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('executive_id')->constrained('executives')->onDelete('cascade');
            $table->string('period', 7); // Format: YYYY-MM
            $table->integer('daily_points_sum')->default(0);
            $table->integer('monthly_score')->default(0);
            $table->integer('rolling_6_month_score')->default(0);
            $table->timestamps();

            $table->unique(['executive_id', 'period']);
            $table->index('period');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('score_histories');
    }
};
