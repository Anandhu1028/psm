<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DailyAuditController;
use App\Http\Controllers\PointHistoryController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Admin\ExecutiveController;
use App\Http\Controllers\Admin\UserController;

// ── Authentication ─────────────────────────────────────────────────────────────
Route::get('login',  [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout',[LoginController::class, 'logout'])->name('logout');

// ── Authenticated Routes ───────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/',         [DashboardController::class, 'index'])->name('home');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Daily Audit ────────────────────────────────────────────────────────────
    Route::get('daily-audit',                     [DailyAuditController::class, 'index'])->name('daily_audit.index');
    Route::get('daily-audit/create',              [DailyAuditController::class, 'create'])->name('daily_audit.create');
    Route::post('daily-audit',                    [DailyAuditController::class, 'store'])->name('daily_audit.store');
    Route::get('daily-audit/{dailyAudit}',        [DailyAuditController::class, 'show'])->name('daily_audit.show');
    Route::delete('daily-audit/{dailyAudit}',     [DailyAuditController::class, 'destroy'])->name('daily_audit.destroy');

    // AJAX endpoints
    Route::post('api/daily-audit/preview',        [DailyAuditController::class, 'previewScore'])->name('api.audit.preview');
    Route::get('api/executives/{executive}/data', [DailyAuditController::class, 'executiveData'])->name('api.executive.data');

    // ── Point History ──────────────────────────────────────────────────────────
    Route::get('point-history', [PointHistoryController::class, 'index'])->name('point_history.index');

    // ── Leaderboards ───────────────────────────────────────────────────────────
    Route::get('leaderboards',         [LeaderboardController::class, 'index'])->name('leaderboards.index');
    Route::post('leaderboards/refresh',[LeaderboardController::class, 'refresh'])->name('leaderboards.refresh');

    // ── Reports ────────────────────────────────────────────────────────────────
    Route::get('reports',        [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');

    // ── Executives ─────────────────────────────────────────────────────────────
    Route::get('executives',                    [ExecutiveController::class, 'index'])->name('executives.index');
    Route::get('executives/create',             [ExecutiveController::class, 'create'])->name('executives.create');
    Route::post('executives',                   [ExecutiveController::class, 'store'])->name('executives.store');
    Route::get('executives/{executive}',        [ExecutiveController::class, 'show'])->name('executives.show');
    Route::get('executives/{executive}/edit',   [ExecutiveController::class, 'edit'])->name('executives.edit');
    Route::put('executives/{executive}',        [ExecutiveController::class, 'update'])->name('executives.update');
    Route::delete('executives/{executive}',     [ExecutiveController::class, 'destroy'])->name('executives.destroy');

    // ── Companies ──────────────────────────────────────────────────────────────
    Route::get('companies',                 [CompanyController::class, 'index'])->name('companies.index');
    Route::get('companies/{company}',       [CompanyController::class, 'show'])->name('companies.show');
    Route::post('companies',                [CompanyController::class, 'store'])->name('companies.store');
    Route::put('companies/{company}',       [CompanyController::class, 'update'])->name('companies.update');
    Route::delete('companies/{company}',    [CompanyController::class, 'destroy'])->name('companies.destroy');

    // AJAX: zones for a company
    Route::get('api/companies/{company}/zones', [ZoneController::class, 'byCompany'])->name('api.zones.by_company');

    // ── Zones ──────────────────────────────────────────────────────────────────
    Route::get('zones',            [ZoneController::class, 'index'])->name('zones.index');
    Route::post('zones',           [ZoneController::class, 'store'])->name('zones.store');
    Route::put('zones/{zone}',     [ZoneController::class, 'update'])->name('zones.update');
    Route::delete('zones/{zone}',  [ZoneController::class, 'destroy'])->name('zones.destroy');

    // ── Users ──────────────────────────────────────────────────────────────────
    Route::middleware('can:manage_users')->group(function () {
        Route::get('users',                      [UserController::class, 'index'])->name('users.index');
        Route::get('users/create',               [UserController::class, 'create'])->name('users.create');
        Route::post('users',                     [UserController::class, 'store'])->name('users.store');
        Route::put('users/{user}',               [UserController::class, 'update'])->name('users.update');
        Route::post('users/{user}/reset-password',[UserController::class, 'resetPassword'])->name('users.reset_password');
        Route::delete('users/{user}',            [UserController::class, 'destroy'])->name('users.destroy');
    });
});
