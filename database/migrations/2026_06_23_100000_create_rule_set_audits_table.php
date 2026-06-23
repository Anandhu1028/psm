<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('rule_set_audits')) {
            return;
        }

        Schema::create('rule_set_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_set_id')->constrained('rule_sets')->cascadeOnDelete();
            $table->foreignId('university_id')->constrained('universities')->cascadeOnDelete();
            // Use nullable unsignedBigInteger for published_by to avoid FK issues across environments
            $table->unsignedBigInteger('published_by')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->json('snapshot')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rule_set_audits');
    }
};
