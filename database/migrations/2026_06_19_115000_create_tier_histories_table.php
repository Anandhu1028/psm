<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tier_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('executive_id')->constrained('executives')->onDelete('cascade');
            $table->string('old_tier', 50);
            $table->string('new_tier', 50);
            $table->string('change_reason', 255)->nullable();
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->index('executive_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tier_histories');
    }
};
