<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CRO\DailyLogController;
use App\Http\Controllers\CRO\ExecutiveController;
use App\Http\Controllers\ExecutiveMeetingController;
use App\Http\Controllers\CRO\AuditController;
use App\Http\Controllers\PipController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\ScoreRuleController;
use App\Http\Controllers\Admin\UniversityController;
use App\Http\Controllers\Admin\UniversityRuleController;

// Auth Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    
    // Main Dashboard route (redirects based on role)
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('crm-dashboard', [DashboardController::class, 'crmDashboard'])->name('crm.dashboard');

    // Executives (Roster & Scorecard)
    Route::get('executives', [ExecutiveController::class, 'index'])->name('executives.index');
    Route::get('executives/create', [ExecutiveController::class, 'create'])->name('executives.create');
    Route::post('executives', [ExecutiveController::class, 'store'])->name('executives.store');
    Route::get('executives/{executive}/scorecard', [ExecutiveController::class, 'scorecard'])->name('executives.scorecard');
    Route::delete('executives/{executive}', [ExecutiveController::class, 'destroy'])->name('executives.destroy');
    Route::put('executives/{executive}', [ExecutiveController::class, 'update'])->name('executives.update');

    // Daily Logs (Performance Recording)
    Route::get('daily-logs/crm-metrics', [DailyLogController::class, 'fetchCrmMetrics'])->name('daily_logs.crm_metrics');
    Route::get('daily-logs/executive-dashboard', [DailyLogController::class, 'getExecutiveDashboardData'])->name('daily_logs.executive_dashboard');
    Route::post('daily-logs/preview-score', [DailyLogController::class, 'previewScore'])->name('daily_logs.preview_score');
    Route::get('daily-logs', [DailyLogController::class, 'index'])->name('daily_logs.index');
    Route::get('daily-logs/create', [DailyLogController::class, 'create'])->name('daily_logs.create');
    Route::post('daily-logs', [DailyLogController::class, 'store'])->name('daily_logs.store');
    Route::delete('daily-logs/{dailyLog}', [DailyLogController::class, 'destroy'])->name('daily_logs.destroy');

    // Meetings Tracker
    Route::get('meetings', [ExecutiveMeetingController::class, 'index'])->name('meetings.index');
    Route::get('meetings/create', [ExecutiveMeetingController::class, 'create'])->name('meetings.create');
    Route::post('meetings', [ExecutiveMeetingController::class, 'store'])->name('meetings.store');

    // Audits Module
    Route::get('audits', [AuditController::class, 'index'])->name('audits.index');
    Route::get('audits/create', [AuditController::class, 'create'])->name('audits.create');
    Route::post('audits', [AuditController::class, 'store'])->name('audits.store');

    // PIP Module
    Route::get('pips', [PipController::class, 'index'])->name('pips.index');
    Route::get('pips/create', [PipController::class, 'create'])->name('pips.create');
    Route::post('pips', [PipController::class, 'store'])->name('pips.store');

    // Reports Module
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Admin Configurations (Score Rules)
    Route::get('admin/rules', [ScoreRuleController::class, 'index'])->name('admin.rules.index');
    Route::post('admin/rules', [ScoreRuleController::class, 'update'])->name('admin.rules.update');

    // Universities Module
    Route::resource('admin/universities', UniversityController::class)->names('admin.universities');
    Route::post('admin/universities/{university}/rules', [UniversityController::class, 'updateRules'])->name('admin.universities.rules.update');
    Route::get('admin/universities/{university}/dashboard', [DashboardController::class, 'universityDashboard'])->name('admin.universities.dashboard');
    Route::post('admin/universities/{university}/logo', [UniversityController::class, 'replaceLogo'])->name('admin.universities.logo.replace');
    Route::delete('admin/universities/{university}/logo', [UniversityController::class, 'removeLogo'])->name('admin.universities.logo.remove');
    Route::post('active-university/switch', [DashboardController::class, 'switchActiveUniversity'])->name('active_university.switch');

    // ── University Rule Engine (Dynamic Scoring Rules) ────────────────────────
    Route::prefix('admin/university-rules')->name('admin.university_rules.')->group(function () {
        Route::get('/', [UniversityRuleController::class, 'index'])->name('index');

        // Rule Set actions
        Route::post('{university}/rule-sets/{ruleSet}/activate',
            [UniversityRuleController::class, 'activateRuleSet'])->name('rule_sets.activate');
        Route::post('{university}/rule-sets/{ruleSet}/publish',
            [UniversityRuleController::class, 'publishRuleSet'])->name('rule_sets.publish');
        Route::post('{university}/rule-sets/clone',
            [UniversityRuleController::class, 'cloneToDraft'])->name('rule_sets.clone');

        // Rule CRUD
        Route::post('{university}/rule-sets/{ruleSet}/rules',
            [UniversityRuleController::class, 'store'])->name('rules.store');
        Route::get('rules/{rule}/edit',
            [UniversityRuleController::class, 'edit'])->name('rules.edit');
        Route::put('rules/{rule}',
            [UniversityRuleController::class, 'update'])->name('rules.update');
        Route::delete('rules/{rule}',
            [UniversityRuleController::class, 'destroy'])->name('rules.destroy');
        Route::post('rules/{rule}/toggle',
            [UniversityRuleController::class, 'toggleRule'])->name('rules.toggle');
    });
});
