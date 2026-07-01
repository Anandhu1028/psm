<?php

namespace App\Providers;

use App\Events\AuditSubmitted;
use App\Listeners\RecordAuditLogListener;
use App\Models\DailyAudit;
use App\Policies\DailyAuditPolicy;
use App\Repositories\Contracts\DailyAuditRepositoryInterface;
use App\Repositories\Contracts\ExecutiveRepositoryInterface;
use App\Repositories\Contracts\RuleRepositoryInterface;
use App\Repositories\EloquentDailyAuditRepository;
use App\Repositories\EloquentExecutiveRepository;
use App\Repositories\EloquentRuleRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(ExecutiveRepositoryInterface::class,  EloquentExecutiveRepository::class);
        $this->app->bind(DailyAuditRepositoryInterface::class, EloquentDailyAuditRepository::class);
        $this->app->bind(RuleRepositoryInterface::class,       EloquentRuleRepository::class);

        // Recovery engine services — singletons so history queries are not duplicated
        $this->app->singleton(\App\Services\Recovery\RecoveryHistoryService::class);
        $this->app->singleton(\App\Services\Recovery\RecoveryEligibilityService::class);
        $this->app->singleton(\App\Services\Recovery\RecoveryCalculationService::class);
        $this->app->singleton(\App\Services\Recovery\RecoveryTransactionService::class);
    }

    public function boot(): void
    {
        // Policy registrations
        Gate::policy(DailyAudit::class, DailyAuditPolicy::class);

        // Event → Listener mappings
        Event::listen(AuditSubmitted::class, RecordAuditLogListener::class);

        // Enable Bootstrap 5 pagination
        \Illuminate\Pagination\Paginator::useBootstrapFive();
    }
}
