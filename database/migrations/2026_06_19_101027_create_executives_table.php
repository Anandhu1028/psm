<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('executives', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id', 50)->unique();
            $table->string('name', 150);
            $table->string('phone', 20);
            $table->string('email', 150)->unique();
            $table->foreignId('zone_id')->constrained('zones')->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->date('date_joined');
            $table->date('probation_end_date');
            $table->foreignId('reporting_manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['active', 'inactive', 'probation'])->default('probation');
            $table->integer('current_score')->default(0);
            $table->enum('current_tier', ['bronze', 'silver', 'gold', 'platinum', 'review_zone'])->default('bronze');
            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index('current_tier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('executives');
    }
};
