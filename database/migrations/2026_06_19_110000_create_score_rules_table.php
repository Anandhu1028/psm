<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('score_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_group', 50); // calls, meetings, lead_mgmt, conversion, recovery, violation
            $table->string('rule_key', 50)->unique(); // e.g., calls_40_49
            $table->string('rule_name', 150);
            $table->enum('value_type', ['points', 'multiplier'])->default('points');
            $table->decimal('rule_value', 8, 2);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('rule_group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('score_rules');
    }
};
