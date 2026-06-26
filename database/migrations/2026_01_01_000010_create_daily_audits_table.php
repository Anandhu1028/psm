<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('executive_id')->constrained()->cascadeOnDelete();
            $table->date('audit_date');
            $table->string('audit_type', 20); // tims | focuz
            $table->enum('status', ['draft', 'pending', 'verified'])->default('pending');

            // ── Common Fields ────────────────────────────────
            $table->integer('connected_calls')->default(0);
            $table->integer('confirmed_meetings')->default(0);
            $table->integer('meetings_attended')->default(0);
            $table->boolean('crm_followup')->default(false);
            $table->boolean('crm_disposition_correct')->default(false);
            $table->boolean('first_contact_within_45min')->default(false);
            $table->boolean('all_leads_followed_up')->default(false);
            $table->boolean('warm_lead_converted')->default(false);
            $table->boolean('cold_lead_reactivated')->default(false);

            // ── FOCUZ-Specific Fields ────────────────────────
            $table->integer('rolling_day')->nullable();           // day number in rolling window
            $table->integer('rolling_window_days')->nullable();   // total days in rolling window
            $table->integer('rolling_meeting_count')->nullable(); // cumulative meetings in window
            $table->string('checkpoint_result', 20)->nullable();  // passed | failed | na

            // ── Scoring Results ──────────────────────────────
            $table->integer('positive_points')->default(0);
            $table->integer('negative_points')->default(0);
            $table->integer('recovery_points')->default(0);
            $table->integer('final_score')->default(0);
            $table->string('kpi_status', 20)->default('pending'); // passed | failed | pending
            $table->string('violation_status', 20)->default('none'); // none | active

            // ── Tier at time of audit ────────────────────────
            $table->string('tier_at_audit', 30)->nullable();

            // ── Evidence & Remarks ───────────────────────────
            $table->string('evidence_path')->nullable();
            $table->text('remarks')->nullable();

            // ── Audit Trail ──────────────────────────────────
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            // NO soft deletes — audits are permanent records

            $table->unique(['executive_id', 'audit_date']); // one audit per executive per day
            $table->index(['company_id', 'audit_date']);
            $table->index(['executive_id', 'audit_date']);
            $table->index('kpi_status');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_audits');
    }
};
