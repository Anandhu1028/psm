<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('executive_id')->constrained('executives')->onDelete('cascade');
            $table->string('lead_identifier', 100);
            $table->boolean('crm_entry_verified')->default(false);
            $table->enum('call_verification_status', ['verified', 'discrepancy', 'fake_lead'])->default('verified');
            $table->string('violation_type', 100)->nullable();
            $table->enum('audit_result', ['pass', 'fail'])->default('pass');
            $table->date('audit_date');
            $table->foreignId('audited_by')->constrained('users')->onDelete('cascade');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('audit_date');
            $table->index('audit_result');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
