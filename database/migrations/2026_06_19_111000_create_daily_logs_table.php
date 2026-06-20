<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_logs', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('executive_id')->constrained('executives')->onDelete('cascade');
            $table->integer('connected_calls')->default(0);
            $table->integer('meetings_arranged')->default(0);
            $table->integer('meetings_attended')->default(0);
            $table->boolean('first_contact_within_45_min')->default(false);
            $table->boolean('all_leads_followed_up')->default(false);
            $table->boolean('crm_disposition_correct')->default(false);
            $table->boolean('warm_lead_converted')->default(false);
            $table->boolean('conduct_violation')->default(false);
            $table->text('cro_remarks')->nullable();
            $table->integer('calculated_score')->default(0);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['date', 'executive_id']);
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_logs');
    }
};
