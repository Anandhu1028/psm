<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('score_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('executive_id')->constrained('executives')->onDelete('cascade');
            $table->foreignId('daily_log_id')->nullable()->constrained('daily_logs')->onDelete('set null');
            $table->foreignId('rule_id')->nullable()->constrained('score_rules')->onDelete('set null');
            $table->enum('type', ['credit', 'debit']);
            $table->integer('points');
            $table->integer('running_total'); // Points balance for executive at this time
            $table->string('description', 255);
            $table->date('transaction_date');
            $table->timestamps();

            $table->index('transaction_date');
            $table->index(['executive_id', 'transaction_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('score_transactions');
    }
};
