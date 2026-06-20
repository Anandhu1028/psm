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

    // Daily Logs (Performance Recording)
    Route::get('daily-logs', [DailyLogController::class, 'index'])->name('daily_logs.index');
    Route::get('daily-logs/create', [DailyLogController::class, 'create'])->name('daily_logs.create');
    Route::post('daily-logs', [DailyLogController::class, 'store'])->name('daily_logs.store');

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
});

