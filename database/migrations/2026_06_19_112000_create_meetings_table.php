<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('executive_id')->constrained('executives')->onDelete('cascade');
            $table->string('lead_name', 150);
            $table->date('meeting_date');
            $table->time('meeting_time');
            $table->enum('meeting_type', ['zoom', 'phone', 'in_person'])->default('zoom');
            $table->enum('status', ['scheduled', 'attended', 'missed', 'cancelled'])->default('scheduled');
            $table->string('crm_reference', 100)->nullable();
            $table->date('arranged_date')->nullable(); // To calculate 2-day / 3-day checkpoints
            $table->timestamps();

            $table->index('meeting_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
