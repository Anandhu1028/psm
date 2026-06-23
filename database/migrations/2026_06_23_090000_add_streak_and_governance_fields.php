<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Streak columns on executives ──────────────────────────────────────
        Schema::table('executives', function (Blueprint $table) {
            $table->unsignedSmallInteger('call_streak_count')->default(0)->after('current_tier');
            $table->unsignedSmallInteger('meeting_streak_count')->default(0)->after('call_streak_count');
            $table->unsignedSmallInteger('best_call_streak')->default(0)->after('meeting_streak_count');
            $table->unsignedSmallInteger('best_meeting_streak')->default(0)->after('best_call_streak');
            $table->date('streak_last_updated')->nullable()->after('best_meeting_streak');
        });

        // ── Governance + new metric columns on daily_logs ─────────────────────
        Schema::table('daily_logs', function (Blueprint $table) {
            $table->boolean('cold_lead_reactivated')->default(false)->after('warm_lead_converted');
            $table->boolean('is_random_audit')->default(false)->after('cold_lead_reactivated');
        });

        // ── Dispute + revocation columns on violations ────────────────────────
        Schema::table('violations', function (Blueprint $table) {
            $table->dateTime('dispute_deadline')->nullable()->after('status');
            $table->text('revocation_reason')->nullable()->after('dispute_deadline');
            $table->dateTime('revoked_at')->nullable()->after('revocation_reason');
            $table->foreignId('revoked_by')
                ->nullable()
                ->after('revoked_at')
                ->constrained('users')
                ->nullOnDelete();
        });

        // ── Meeting confirmation / quality columns ────────────────────────────
        Schema::table('meetings', function (Blueprint $table) {
            $table->boolean('is_confirmed')->default(false)->after('status');
            $table->unsignedInteger('call_duration_seconds')->nullable()->after('is_confirmed');
            $table->boolean('calendar_invite_shared')->default(false)->after('call_duration_seconds');
            $table->boolean('zm_shared')->default(false)->after('calendar_invite_shared');
        });
    }

    public function down(): void
    {
        Schema::table('executives', function (Blueprint $table) {
            $table->dropColumn([
                'call_streak_count', 'meeting_streak_count',
                'best_call_streak', 'best_meeting_streak', 'streak_last_updated',
            ]);
        });

        Schema::table('daily_logs', function (Blueprint $table) {
            $table->dropColumn(['cold_lead_reactivated', 'is_random_audit']);
        });

        Schema::table('violations', function (Blueprint $table) {
            $table->dropForeign(['revoked_by']);
            $table->dropColumn(['dispute_deadline', 'revocation_reason', 'revoked_at', 'revoked_by']);
        });

        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn(['is_confirmed', 'call_duration_seconds', 'calendar_invite_shared', 'zm_shared']);
        });
    }
};
