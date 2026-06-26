<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('executive_id')->constrained()->cascadeOnDelete();
            $table->foreignId('daily_audit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rule_id')->nullable()->constrained('rules')->nullOnDelete();
            $table->date('audit_date');
            $table->string('category', 30); // positive | negative | recovery | kpi
            $table->string('rule_code', 100)->nullable();
            $table->string('rule_name', 200)->nullable();
            $table->integer('points'); // positive or negative value
            $table->string('type', 10); // credit | debit
            $table->integer('running_total')->default(0);
            $table->string('evidence')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            // NO soft deletes — transactions are permanent

            $table->index(['executive_id', 'audit_date']);
            $table->index(['company_id', 'audit_date']);
            $table->index(['daily_audit_id']);
            $table->index('category');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
};
