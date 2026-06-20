<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('executive_id')->constrained('executives')->onDelete('cascade');
            $table->foreignId('daily_log_id')->nullable()->constrained('daily_logs')->onDelete('set null');
            $table->enum('violation_type', ['call', 'meeting', 'lead', 'conduct']);
            $table->integer('points_deducted')->default(0);
            $table->enum('status', ['active', 'waived', 'disputed'])->default('active');
            $table->text('resolution_remarks')->nullable();
            $table->date('date_committed');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('date_committed');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('violations');
    }
};
